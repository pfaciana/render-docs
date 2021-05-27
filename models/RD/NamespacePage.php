<?php

namespace RD;

class NamespacePage extends PageLayout
{
	protected $ns;

	public function __construct ($id, $namespace)
	{
		parent::__construct($id);
		$this->ns = str_replace('-', "\\", $namespace);
	}

	public function getBreadcrumbs ()
	{
		$links = array_merge($this->getPrevNextLinks($this->ns), [
			'links' => [
				'All'   => \Urls::getDocsUrl(),
				$this->projectName => \Urls::getDocUrl(),
			],
		]);

		return $links;
	}

	public function getName ()
	{
		return ['name' => $this->getNsHtml($this->ns)];
	}

	public function getDescription () { }

	public function getTableOfContents ()
	{
		$ns  = $this->ns;
		$rd  = $this->rd;
		$toc = $rd->getToc();

		$links = [];

		foreach ($toc[$ns] as $ref => $values) {
			$ref_links = [];
			foreach ($values as $name => $summary) {
				$ref_links[] = '<a href="' . \Urls::getDocUrl($ns, $ref, $name) . '">' . $name . '</a>';
			}
			$links[ucfirst($ref)] = [
				'url'   => \Urls::getDocUrl($ns, $ref),
				'links' => [implode(', ', $ref_links) => NULL,],
			];
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
		$rd  = $this->rd;
		$toc = $rd->getToc();

		$links = [];

		foreach ($toc[$ns] as $ref => $vales) {
			$links[ucfirst($ref)] = \Urls::getDocUrl($ns, $ref);
		}

		$data = [
			'header'    => $this->getNsHtml($ns) . ' NS',
			'group_url' => \Urls::getDocUrl($ns),
			'links'     => $links,
		];

		return $data;
	}
}