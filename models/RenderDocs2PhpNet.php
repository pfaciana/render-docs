<?php


class RenderDocs2PhpNet
{
	protected $config = [];

	protected $rootDir;
	protected $projectRoot;
	protected $projectData;
	protected $sectionId;

	protected $toc;
	protected $search;
	protected $meta = [];

	public $route;
	public $parentRef;
	public $ref;
	public $ns;
	public $name;
	public $attr;

	public function __construct ($sectionId, $config = [])
	{
		$this->config      = $config;
		$this->rootDir     = rtrim(DOC_ROOT, " \/\\\t\n\r\0\x0B") . '/';
		$this->sectionId   = rtrim($sectionId);
		$this->projectRoot = $this->rootDir . $this->sectionId . '/';
		$this->projectData = $this->projectRoot . 'data/';

		if (is_file($metaFile = $this->projectRoot . 'meta.json')) {
			$this->meta = json_decode(file_get_contents($this->projectRoot . '/meta.json'), TRUE);
		}

		if (empty($this->meta['name'])) {
			$this->meta['name'] = ucwords(str_replace(['-'], [' '], $this->sectionId));
		}
	}

	public function getToc ()
	{
		if (!$this->toc) {
			$toc = json_decode(file_get_contents($this->projectRoot . '/toc.json'), TRUE);

			ksort($toc, SORT_STRING | SORT_FLAG_CASE);
			foreach ($toc as $id => &$ref) {
				ksort($ref, SORT_STRING | SORT_FLAG_CASE);
				foreach ($ref as $type => &$items) {
					ksort($items, SORT_STRING | SORT_FLAG_CASE);
				}
			}

			$this->toc = ['global' => $toc['global']] + $toc;
		}

		return $this->toc;
	}

	public function getSearch ()
	{
		if (!$this->search) {
			$this->search = json_decode(file_get_contents($this->projectRoot . '/search.json'), TRUE);
		}

		return $this->search;
	}

	public function getUrl ()
	{
		return \Urls::getPrefix() . '/' . \Urls::getSlug() . '/' . $this->sectionId;
	}

	public function getMeta ($key = NULL, $default = '')
	{
		if (empty($key)) {
			return $this->meta;
		}

		if (!array_key_exists($key, $this->meta)) {
			return $default;
		}

		return $this->meta[$key];
	}

	public function getSectionID ()
	{
		return $this->sectionId;
	}

	static public function getRootDir ()
	{
		return rtrim(DOC_ROOT, " \/\\\t\n\r\0\x0B") . '/';
	}

	public function getProjectDir ()
	{
		return $this->projectRoot;
	}

	public function getDataDir ()
	{
		return $this->projectData;
	}

	public function getDataAttr ($data, $attr)
	{
		foreach ($data as $visibility => $names) {
			if (in_array($attr, array_keys($names))) {
				$names[$attr]['visibility'] = $visibility;

				return $names[$attr];
			}
		}

		return NULL;
	}

	public function filterFileLocation ($file)
	{
		if (!empty($this->config['locationFilter']) && is_callable($this->config['locationFilter'])) {
			$file = $this->config['locationFilter']($file);
		}

		return $file;
	}
}