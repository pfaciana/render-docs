<?php

namespace RD;

class AttrPage extends PageLayout
{
	public function __construct ($id, $namespace, $parentRef, $class, $ref, $name)
	{
		parent::__construct($id);
		$this->ns        = str_replace('-', "\\", $namespace);
		$this->parentRef = $parentRef;
		$this->ref       = $ref;
		$this->class     = $class;
		$this->name      = $name;

		$this->details = new Ref($this->ref, [$this->class, $this->name], $this->ns);

		$this->parentData      = $this->details->getData();
		$this->data            = $this->rd->getDataAttr($this->parentData[$ref], $name);
		$this->data['comment'] = !empty($this->data['comments']) ? $this->data['comments'][count($this->data['comments']) - 1] : NULL;
		$this->data['tags']    = !empty($this->data['comment']) && !empty($this->data['comment']['tags']) ? $this->data['comment']['tags'] : [];
		$this->buildParams($this->data, $this->params, $this->returns);
	}

	public function getBreadcrumbs ()
	{
		$ns        = $this->ns;
		$parentRef = $this->parentRef;
		$ref       = $this->ref;
		$class     = $this->class;
		$name      = $this->name;

		$links = [];

		[$prev, $next] = $this->getPrevNextItemsFromGroup($this->parentData[$ref]['public'] ?? [], $name);

		!empty($prev) && ($links['prev'] = [\Urls::getDocUrl(str_replace("\\", '-', $ns), $parentRef, $class, $ref, $prev), $prev]);
		!empty($next) && ($links['next'] = [\Urls::getDocUrl(str_replace("\\", '-', $ns), $parentRef, $class, $ref, $next), $next]);

		$links['links'] = [
			'All'                => \Urls::getDocsUrl(),
			$this->projectName              => \Urls::getDocUrl(),
			$this->getNsHtml($ns) => \Urls::getDocUrl($ns),
			$parentRef            => \Urls::getDocUrl($ns, $parentRef),
			$class                => \Urls::getDocUrl($ns, $parentRef, $class),
		];

		return $links;
	}

	public function getName ()
	{
		$name = $this->class . '::' . ($this->ref === 'properties' ? '$' : '') . $this->name;

		return ['name' => $name, 'summary' => $this->data['comment']['summary'] ?? NULL];
	}

	public function getSidebar ()
	{
		$ns        = $this->ns;
		$parentRef = $this->parentRef;
		$ref       = $this->ref;
		$class     = $this->class;
		$name      = $this->name;

		$links = [];
		if (empty($keys = array_keys($this->parentData[$ref]['public'] ?? []))) {
			return '';
		}

		sort($keys);

		foreach ($keys as $key) {
			$links[$key] = ['url' => \Urls::getDocUrl(str_replace("\\", '-', $ns), $parentRef, $class, $ref, $key), 'current' => $key === $name];
		}

		$data = [
			'header' => ucfirst($ref),
			'links'  => $links,
		];

		return $data;
	}
}