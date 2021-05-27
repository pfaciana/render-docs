<?php

class RenderDocs
{
	protected $config = [];

	public static function get_instance ($config = [])
	{
		static $instance = FALSE;

		if (!$instance) {
			$instance = new static($config);
		}

		return $instance;
	}

	protected function __construct ($config = [])
	{
		$this->config = $config;
		$this->set_constants();
		$this->register_autoload();
		Flight::set('flight.handle_errors', FALSE);
		$this->load_routes(Urls::getSlug());
		Flight::start();
	}

	protected function set_constants ()
	{
		!defined('DOC_ROOT') && define('DOC_ROOT', $this->config['docRoot'] ?? __DIR__ . '/../docs/');
		!defined('EDITOR_PREFIX') && define('EDITOR_PREFIX', $this->config['editorUrl'] ?? FALSE);

		if (!is_dir(DOC_ROOT)) {
			echo('Documentation Directory [' . DOC_ROOT . '] is missing.');
			exit();
		}
	}

	protected function register_autoload ()
	{
		spl_autoload_register(function ($class) {
			include sprintf("%s/%s.php", __DIR__, $class);
		});

		$this->include_dir(__DIR__ . '/../views');
	}

	protected function include_dir ($dir)
	{
		foreach (scandir($dir) as $key => $path) {
			if (!in_array($path, ['.', '..'])) {
				$location = $dir . DIRECTORY_SEPARATOR . $path;
				is_dir($location) ? $this->include_dir($location) : (pathinfo($location, PATHINFO_EXTENSION) === 'php' && require_once $location);
			}
		}
	}

	protected function load_routes ($slug)
	{
		Flight::route("/ajax", function ($route) {
			echo json_encode(['results' => (new Search())->getSearchResults(), 'pagination' => ['more' => FALSE]]);
			exit();
		}, TRUE);

		Flight::route("/search", function ($route) {
			echo searchPage();
		}, TRUE);

		Flight::route("/search/@id", function ($id, $route) {
			global $rd;
			$rd = new RenderDocs2PhpNet($id, $this->config);
			echo searchPage();
		}, TRUE);

		Flight::route("/", function ($route) {
			echo homePage();
		}, TRUE);

		Flight::route("/{$slug}", function ($route) {
			echo docsPage();
		}, TRUE);

		Flight::route("/{$slug}/@id", function ($id, $route) {
			global $rd;
			$rd = new RenderDocs2PhpNet($id, $this->config);
			echo content(new RD\OverviewPage(...func_get_args()));
		}, TRUE);

		Flight::route("/{$slug}/@id/@namespace(/@ref(/@name(/@type(/@attr))))", function ($id, $namespace, $ref, $name, $type, $attr, $route) {
			global $rd;
			$rd        = new RenderDocs2PhpNet($id, $this->config);
			$rd->route = $route;
			$rd->ns    = explode('-', $namespace);
			$rd->name  = $name;
			$rd->attr  = $attr;

			if (!empty($attr)) {
				$rd->parentRef = $ref;
				$rd->ref       = $type;
			}
			else {
				$rd->parentRef = NULL;
				$rd->ref       = $ref;
			}

			return TRUE;
		}, TRUE);

		Flight::route("/{$slug}/@id/@namespace", function ($id, $namespace, $route) {
			echo content(new RD\NamespacePage(...func_get_args()));
		}, TRUE);

		Flight::route("/{$slug}/@id/@namespace/@ref", function ($id, $namespace, $ref, $route) {
			echo content(new RD\ReferencePage(...func_get_args()));
		}, TRUE);

		Flight::route("/{$slug}/@id/@namespace/@ref/@name", function ($id, $namespace, $ref, $name, $route) {
			if ($ref === 'functions') {
				echo content(new RD\FunctionPage(...func_get_args()));
			}
			else {
				echo content(new RD\ClassPage(...func_get_args()));
			}
		}, TRUE);

		Flight::route("/{$slug}/@id/@namespace/@ref/@name/@type/@attr", function ($id, $namespace, $ref, $name, $type, $attr, $route) {
			echo content(new RD\AttrPage(...func_get_args()));
		}, TRUE);

		Flight::route('*', function () {
			echo '';
		});
	}
}

function RenderDocs ($config = [])
{
	return RenderDocs::get_instance($config);
}