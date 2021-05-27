<?php

namespace RD;

class OverviewPage extends PageLayout
{
	public function __construct ($id = NULL)
	{
		parent::__construct($id);
	}

	public function getBreadcrumbs ()
	{
		$links = [
			'links' => [
				'All'              => \Urls::getDocsUrl(),
				$this->projectName => \Urls::getDocUrl(),
			],
		];

		return $links;
	}

	public function getName ()
	{
		return ['name' => "{$this->projectName} Namespaces"];
	}

	public function getDescription () { }

	public function getTableOfContents ()
	{
		$rd  = $this->rd;
		$toc = $rd->getToc();

		$links = [];

		foreach ($toc as $namespace => $ref) {
			$links[$namespace] = \Urls::getDocUrl($namespace);
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

	public function getSidebar () { }

}