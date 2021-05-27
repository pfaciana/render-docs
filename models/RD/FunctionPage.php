<?php

namespace RD;

class FunctionPage extends PageLayout
{
	public function __construct ($id, $namespace, $ref, $name)
	{
		parent::__construct($id);
		$this->ns   = str_replace('-', "\\", $namespace);
		$this->ref  = $ref;
		$this->name = $name;

		$this->details = new Ref($this->ref, $this->name, $this->ns);

		$this->data = $this->details->getData();

		$this->data['comment'] = !empty($this->data['comments']) ? $this->data['comments'][count($this->data['comments']) - 1] : NULL;
		$this->data['tags']    = !empty($this->data['comment']) && !empty($this->data['comment']['tags']) ? $this->data['comment']['tags'] : [];
		$this->buildParams($this->data, $this->params, $this->returns);
	}

	public function getBreadcrumbs ()
	{
		$ns   = $this->ns;
		$ref  = $this->ref;
		$name = $this->name;

		$links = array_merge($this->getPrevNextLinks($ns, $ref, $name), [
			'links' => [
				'All'                 => \Urls::getDocsUrl(),
				$this->projectName    => \Urls::getDocUrl(),
				$this->getNsHtml($ns) => \Urls::getDocUrl($ns),
				$ref                  => \Urls::getDocUrl($ns, $ref),
			],
		]);

		return $links;
	}

	public function getName ()
	{
		$rd   = $this->rd;
		$ns   = $this->ns;
		$ref  = $this->ref;
		$name = $this->name;

		return ['name' => $this->name, 'summary' => $rd->getToc()[$ns][$ref][$name] ?? NULL];
	}


	public function getSidebar ()
	{
		$ns   = $this->ns;
		$ref  = $this->ref;
		$name = $this->name;
		$rd   = $this->rd;
		$toc  = $rd->getToc();

		$links = [];

		foreach ($toc[$ns][$ref] as $key => $summary) {
			$links[$key] = ['url' => \Urls::getDocUrl($ns, $ref, $key), 'summary' => $summary, 'current' => $key === $name];
		}

		$data = [
			'header'    => ucfirst($ref),
			'group_url' => \Urls::getDocUrl($ns, $ref),
			'links'     => $links,
		];

		return $data;
	}

}