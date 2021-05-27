<?php

namespace RD;

class ReferencePage extends PageLayout
{
	protected $ns;
	protected $ref;

	public function __construct ($id, $namespace, $ref)
	{
		parent::__construct($id);
		$this->ns  = str_replace('-', "\\", $namespace);
		$this->ref = $ref;
	}

	public function getBreadcrumbs ()
	{
		$ns  = $this->ns;
		$ref = $this->ref;

		$links = array_merge($this->getPrevNextLinks($ns, $ref), [
			'links' => [
				'All'                => \Urls::getDocsUrl(),
				$this->projectName              => \Urls::getDocUrl(),
				$this->getNsHtml($ns) => \Urls::getDocUrl($ns),
			],
		]);

		return $links;
	}

	public function getName ()
	{
		return ['name' => ucfirst($this->ref)];
	}

	public function getDescription () { }

	public function getTableOfContents ()
	{
		$ns  = $this->ns;
		$ref = $this->ref;
		$rd  = $this->rd;
		$toc = $rd->getToc();

		$links = [];

		foreach ($toc[$ns][$ref] as $name => $summary) {
			$summary      = htmlentities(trim($summary, " -/\\\t\n\r\0\x0B")) ?: NULL;
			$links[$name] = ['url' => \Urls::getDocUrl($ns, $ref, $name), 'summary' => $summary];
		}

		return ['links' => $links];
	}

	public function getParameters () { }

	public function getReturnValues () { }

	public function getErrors () { }

	public function getExceptions () { }

	public function getChangelog () { }

	public function getConstants () { }

	public function getOptions () { }

	public function getUsage () { }

	public function getExamples () { }

	public function getNotes () { }

	public function getSeeAlso () { }

	public function getUserNotes () { }

	public function getSidebar ()
	{
		$ns  = $this->ns;
		$ref = $this->ref;
		$rd  = $this->rd;
		$toc = $rd->getToc();

		$links = [];

		foreach ($toc[$ns] as $key => $values) {
			$links[$key] = ['url' => \Urls::getDocUrl($ns, $ref), 'current' => $key === $ref];
		}

		$data = [
			'header'    => ucfirst($ref),
			'group_url' => \Urls::getDocUrl($ns, $ref),
			'links'     => $links,
		];

		return $data;
	}
}