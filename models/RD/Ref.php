<?php


namespace RD;


class Ref
{
	/**
	 * @var \RenderDocs2PhpNet
	 */
	protected $rd;

	protected $types = [
		'classes'    => 'class',
		'traits'     => 'trait',
		'interfaces' => 'interface',
		'properties' => 'property',
		'constants'  => 'constant',
		'functions'  => 'function',
		'methods'    => 'method',
	];

	protected $origName;
	protected $strName = '';
	protected $ns;
	protected $parentRef;
	protected $ref;
	protected $name;
	protected $attr;
	protected $data;
	protected $file;
	protected $url;

	public function __construct ($ref, $name, $ns = NULL)
	{
		$this->rd        = $GLOBALS['rd'];
		$this->parentRef = $this->getParentRef($ref);
		$this->ref       = $this->getRef($ref);
		$this->origName  = $name;

		if (is_null($ns)) {
			[$name, $ns, $strict] = $this->parseRawRef($name);
		}

		if (is_string($ns)) {
			$ns = explode('-', str_replace("\\", '-', $ns));
		}

		$ns = $this->findNs($this->ref, $name, $ns, $strict ?? FALSE);

		[$name, $attr] = is_array($name) ? array_pad($name, 2, NULL) : [$name, NULL];

		$this->name = $name;
		$this->attr = $attr;

		$this->ns = $ns;
	}

	protected function parseRawRef ($raw)
	{
		if (!is_string($raw)) {
			return [$raw, NULL, FALSE];
		}

		$isGlobal = $raw[0] === "\\";

		$namespaces = explode("\\", trim($raw, " \\\t\n\r\0\x0B"));

		$name = array_pop($namespaces);

		return [$name, $namespaces ?: ($isGlobal ? ['global'] : NULL), $isGlobal];
	}

	static protected function getParentRefs ($type = NULL)
	{
		return in_array($type, ['constants', 'properties', 'methods']) ? ['classes', 'traits', 'interfaces'] : [$type];
	}

	protected function findNs ($ref, $name, $ns = [], $strict = FALSE)
	{
		$toc        = $this->rd->getToc();
		$name       = is_array($name) ? $name[0] : $name;
		$name       = is_array($name) ? (array_key_exists('name', $name) ? $name['name'] : $name[0]) : $name;
		$parentRefs = static::getParentRefs($this->getRef($ref));

		$namespaces = array_filter($strict ? [$ns] : [$ns, ($this->rd->ns ?? []), ['global']]);

		$allNs = array_keys($toc);
		foreach ($namespaces as $namespace) {
			$namespace = implode("\\", $namespace);
			foreach ($parentRefs as $parentRef) {
				if (array_key_exists($name, $toc[$namespace][$parentRef] ?? [])) {
					if (in_array($this->ref, ['constants', 'properties', 'methods'])) {
						$this->parentRef = $parentRef;
					}

					return explode("\\", $namespace);
				}
				elseif (!$strict) {
					$matches = array_filter($allNs, function ($thisNs) use ($namespace) {
						if (strtolower($thisNs) === strtolower($namespace)) {
							return FALSE;
						}

						return strtolower(substr($thisNs, -strlen($namespace))) === strtolower($namespace);
					});
					foreach ($matches as $match) {
						if (array_key_exists($name, $toc[$match][$parentRef] ?? [])) {
							return explode("\\", $match);
						}
					}
				}
			}
		}

		return NULL;
	}

	public function getGenericRef ($type = NULL, $single = FALSE)
	{
		$types = $single ? $this->types : array_flip($this->types);
		$type  = $type ?: ($this->ref ?? NULL);

		return array_key_exists($type, $types) ? $types[$type] : $type;
	}

	public function getParentRef ($type = NULL, $single = FALSE)
	{
		if (!is_array($type) || count($type) < 2) {
			return NULL;
		}

		return $this->getGenericRef($type[0], $single);
	}

	public function getRef ($type = NULL, $single = FALSE)
	{
		if (is_array($type) && !empty($type)) {
			$type = $type[count($type) - 1];
		}

		return $this->getGenericRef($type, $single);
	}

	public function isTypeClass ()
	{
		return in_array($this->ref, ['classes', 'traits', 'interfaces']);
	}

	public function isTypeAttr ()
	{
		return in_array($this->ref, ['properties', 'constants', 'methods']);
	}

	public function isTypeFunction ()
	{
		return in_array($this->ref, ['functions']);
	}

	// This is just a series of way too expensive requests. Tabling for later possibly
	// Not only are we searching for and reading multiple files that consume resources, we also have to keep track of overrides
	// A lot of the time everything is being overwritten and thus we are creating very long loading times for no new information.
	// If this is going to be done, it must be cached somehow.
	public function setInheritedData ()
	{
		if (!isset($this->data['inherits'])) {
			$this->data['inherits'] = [];
		}

		if (!empty($this->data['extends'])) {
			$this->data['inherits'][$this->data['extends']] = ($parent = new Ref('classes', $this->data['extends']));

			$parentData = $parent->getData();
			if (!empty($parentData['inherits'])) {
				$this->data['inherits'] = array_merge($this->data['inherits'], $parentData['inherits']);
			}
		}

		if (!empty($this->data['implements'])) {
			foreach ($this->data['implements'] as $implements) {
				$this->data['inherits'][$this->data['implements']] = ($parent = new Ref('interfaces', $implements));

				$parentData = $parent->getData();
				if (!empty($parentData['inherits'])) {
					$this->data['inherits'] = array_merge($this->data['inherits'], $parentData['inherits']);
				}
			}
		}

		if (!empty($this->data['traits']['use'])) {
			foreach ($this->data['implements'] as $trait) {
				$this->data['inherits'][$trait] = ($parent = new Ref('traits', $trait));

				$parentData = $parent->getData();
				if (!empty($parentData['inherits'])) {
					$this->data['inherits'] = array_merge($this->data['inherits'], $parentData['inherits']);
				}
			}
		}

		return;
	}

	public function getFile ()
	{
		if (!empty($this->file)) {
			return $this->file;
		}

		if (empty($this->ns)) {
			return NULL;
		}

		global $rd;

		$args = array_filter([implode('/', $this->ns), $this->parentRef ?: $this->ref]);

		$args[0] === 'global' && array_shift($args);

		if (!empty($path = implode('/', $args))) {
			$path = '/' . trim($path, "\\/");
		}

		return $this->file = $rd->getDataDir() . $path . '.json';
	}

	public function getData ()
	{
		if (!empty($this->data)) {
			return $this->data;
		}

		if (empty($filename = $this->getFile())) {
			return NULL;
		}

		$this->data = json_decode(file_get_contents($filename), TRUE)[$this->name];

		// This is just a series of way too expensive requests. Tabling for later possibly
		//$this->isTypeClass() && $this->setInheritedData();

		$this->data['tags'] = $this->data['tags'] ?? [];

		return $this->data;
	}

	public function getUrl ()
	{
		if (!empty($this->url)) {
			return $this->url;
		}

		if (empty($this->ns)) {
			return NULL;
		}

		if (!empty($this->parentRef) && !empty($this->attr)) {
			return $this->url = \Urls::getDocUrl(implode('-', $this->ns), $this->parentRef, $this->name, $this->ref, $this->attr);
		}


		return $this->url = \Urls::getDocUrl(implode('-', $this->ns), $this->ref, $this->name);
	}

	protected function getNameToString ($name, $attr = NULL)
	{
		if (is_array($name)) {
			[$name, $attr] = $name;
		}

		return $name . ($attr ? '::' . ($this->ref === 'properties' ? '$' : '') . $attr : '');
	}

	public function __toString ()
	{
		if (!empty($this->strName)) {
			return $this->strName;
		}

		if (is_null($this->ns)) {
			return $this->strName = $this->getNameToString($this->origName);
		}

		($this->ns && $this->ns !== ['global']) && ($this->strName = "\\" . implode("\\", $this->ns) . "\\");
		$this->strName .= $this->getNameToString($this->name, $this->attr);

		return $this->strName;
	}
}