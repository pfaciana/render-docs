<?php

namespace RD;

abstract class PageLayout
{
	/**
	 * @var \RenderDocs2PhpNet
	 */
	public $rd;
	public $projectName = '';
	protected $details;
	protected $sectionIdk;
	protected $ns;
	protected $parentRef;
	protected $ref;
	protected $class;
	protected $name;
	protected $origData;
	protected $parentData;
	protected $data;
	protected $params = [];
	protected $returns;


	public function __construct ($id)
	{
		$this->sectionId = $id;
		if (isset($GLOBALS['rd'])) {
			$this->rd          = $GLOBALS['rd'];
			$this->projectName = $this->rd->getMeta('name');
		}

	}

	public function getBreadcrumbs () { }

	public function getName () { }

	public function getDescription ()
	{
		if ($this->details->isTypeClass()) {
			return '';
		}

		$modifiers = $this->getModifierList($this->data, $this->data['visibility'] ?? NULL);

		if (in_array($this->ref, ['constants', 'properties'])) {
			$this->buildProperty($this->data, $types, $value);
			$signature = $this->buildPropertyField($this->name, $types, $value, $modifiers, NULL, $this->ref === 'constants');
		}
		else {
			$signature = $this->buildSignature($this->name, $this->params, $this->returns, $modifiers);
		}

		$data = ['type' => $this->ref, 'signature' => $signature];

		if (!empty($this->data['comment']['description'])) {
			$data['description'] = $this->data['comment']['description'];
		}

		return $data;
	}

	public function getSynopsis () { }

	public function getTableOfContents ()
	{
		if (!$this->details->isTypeClass()) {
			return '';
		}

		$content = '';

		$sortKeys = ['constants', 'properties', 'methods'];

		foreach ($sortKeys as $key) {
			if (!empty($this->data[$key])) {
				$content .= '<h3>' . ucfirst($key) . '</h3>';
				$links   = [];
				foreach ($this->data[$key] as $name => $details) {
					$links[$name] = ['url' => (new Ref([$this->ref, $key], [$this->name, $name], $this->ns))->getUrl()];
					if (!empty($details['comment']) && !empty($details['comment']['summary'])) {
						$links[$name]['summary'] = $details['comment']['summary'];
					}
				}
				$content .= tableofcontentslist_section($links);
			}
		}

		return ['content' => $content];
	}

	public function getParameters ()
	{
		$parameters = [];

		foreach ($this->params as $param) {
			$parameters[] = [
				'name'        => $this->getParamNamePrefixed($param, NULL),
				'description' => $this->buildDescription($param),
			];
		}

		return $parameters;
	}

	public function getReturnValues ()
	{
		return !empty($param = $this->returns) ? $this->buildDescription($param) : NULL;
	}

	public function getGlobals ()
	{
		if (empty($tags = $this->data['tags'])) {
			return '';
		}

		$globals = [];

		foreach ($tags as $tag) {
			if (in_array($tag['tagName'], ['global'])) {
				$globals[] = [
					'name'        => '$' . $this->getParamNamePrefixed($tag, NULL),
					'description' => $this->buildDescription($tag),
				];
			}
		}

		return $globals;
	}

	public function getErrors ()
	{
		if (empty($tags = $this->data['tags'])) {
			return '';
		}

		$errors = [];

		foreach ($tags as $tag) {
			if (in_array($tag['tagName'], ['throws'])) {
				$errors[$tag['type']] = $tag['desc'];
			}
		}

		return $errors;
	}

	public function getExceptions ()
	{
		if (empty($tags = $this->data['tags'])) {
			return '';
		}

		$exceptions = [];

		foreach ($tags as $tag) {
			if (in_array($tag['tagName'], ['deprecated'])) {
				$exceptions[] = 'Deprecated since version ' . $tag['type'] . ($tag['desc'] ? "</p><p>{$tag['desc']}" : '');
			}
		}

		return $exceptions;
	}

	public function getChangelog ()
	{
		if (empty($tags = $this->data['tags'])) {
			return '';
		}

		$changes = [];

		foreach ($tags as $tag) {
			if (in_array($tag['tagName'], ['since', 'version', 'change'])) {
				$version = ($tag['tagName'] === 'since' ? $tag['type'] : $tag['desc']) ?? NULL;
				$summary = $tag['tagName'] === 'since' && !empty(trim($tag['desc'])) ? $tag['desc'] : '@' . $tag['tagName'];

				$changes[$version] = $summary;
			}
		}

		uksort($changes, function ($a, $b) {
			return -1 * version_compare($a, $b);
		});

		foreach ($tags as $tag) {
			if (in_array($tag['tagName'], ['date'])) {
				$version = ($tag['tagName'] === 'since' ? $tag['type'] : $tag['desc']) ?? NULL;
				$summary = $tag['tagName'] === 'since' && !empty(trim($tag['desc'])) ? $tag['desc'] : '@' . $tag['tagName'];

				$changes[$version] = $summary;
			}
		}

		return $changes;
	}

	public function getConstants () { }

	public function getOptions ()
	{
		if (empty($tags = $this->data['tags'])) {
			return '';
		}

		$options = [
			'keys' => ['Tag', 'Summary'],
			'data' => [],
		];

		foreach ($tags as $tag) {
			if (in_array($tag['tagName'], ['author', 'copyright', 'license', 'category', 'package', 'subpackage',])) {
				$version = ($tag['tagName'] === 'version' ? $tag['type'] : $tag['desc']) ?? NULL;
				$summary = $tag['tagName'] === 'deprecated' ? $tag['desc'] : '';

				$options['data'][] = ['@' . $tag['tagName'], $version, $summary];
			}
		}

		return $options;
	}

	public function getUsage ()
	{
		if (empty($tags = $this->data['tags'])) {
			return '';
		}

		$usages = [];

		foreach ($tags as $tag) {
			if (in_array($tag['tagName'], ['uses'])) {
				$ref     = new Ref($tag['type'], $tag['name']['name'], $tag['name']['namespace']);
				$url     = $ref->getUrl();
				$text    = (string) $ref;
				$summary = $tag['desc'] ?? NULL;

				$usages['Uses'][$text] = ['url' => $url, 'summary' => $summary];
			}
		}

		foreach ($tags as $tag) {
			if (in_array($tag['tagName'], ['used-by'])) {
				$ref     = new Ref($tag['type'], $tag['name']['name'], $tag['name']['namespace']);
				$url     = $ref->getUrl();
				$text    = (string) $ref;
				$summary = $tag['desc'] ?? NULL;

				$usages['Used By'][$text] = ['url' => $url, 'summary' => $summary];
			}
		}

		foreach ($tags as $tag) {
			if (in_array($tag['tagName'], ['tutorial'])) {
				!empty($tag['desc']) && ($usages['Tutorials'][] = $tag['desc']);
			}
		}

		return $usages;
	}

	public function getExamples ()
	{
		if (empty($tags = $this->data['tags'])) {
			return '';
		}

		$examples = [];

		foreach ($tags as $tag) {
			if (in_array($tag['tagName'], ['example']) && !empty(trim($tag['desc']))) {
				$examples[] = str_replace(["\\n", "\n", "\t"], ['<br>', '<br>', '&nbsp;&nbsp;&nbsp;&nbsp;'], $tag['desc']);
			}
		}

		return $examples;
	}

	public function getNotes ()
	{
		if (empty($tags = $this->data['tags'])) {
			return '';
		}

		$notes = [];

		foreach ($tags as $tag) {
			if (in_array($tag['tagName'], ['internal', 'todo'])) {
				$notes[] = ['inline' => '@' . $tag['tagName'], 'blocks' => $tag['desc']];
			}
		}

		return $notes;
	}

	public function getSeeAlso ()
	{
		if (empty($tags = $this->data['tags'])) {
			return '';
		}

		$links = [];

		foreach ($tags as $tag) {
			if (in_array($tag['tagName'], ['link', 'see',])) {
				if (!empty($tag['type']) && !empty($tag['name'])) {
					$ref     = new Ref($tag['type'], $tag['name']['name'], $tag['name']['namespace']);
					$url     = $ref->getUrl();
					$text    = (string) $ref;
					$summary = $tag['desc'] ?? NULL;
				}
				else {
					$url     = !empty($tag['type']) ? $tag['type'] : $tag['name'];
					$text    = $tag['desc'] ?: $url;
					$summary = NULL;
				}

				if (!empty($url)) {
					$links[$text] = ['url' => $url];
					$summary && $links[$text]['summary'] = $summary;
				}
			}
		}

		return $links;
	}

	public function getUserNotes () { }

	public function getSidebar () { }

	public function footer ()
	{
		if (!defined('EDITOR_PREFIX') || !EDITOR_PREFIX) {
			return '';
		}

		ob_start();

		if (!empty($file = $this->getFileLocation())) {
			$url = \Urls::getEditorUrl($file, $line = ($this->data['lines'][0] ?? 0));
			echo "<div style='text-align: center;'><small><a href='{$url}' data-editor-url target='_blank'>{$file}:{$line}</a></small></div>";
		}

		return ob_get_clean();
	}

	protected function getFileLocation ()
	{
		if (empty($file = $this->getDataFile())) {
			return FALSE;
		}

		$file = $this->rd->filterFileLocation($file);

		return file_exists($file) ? $file : FALSE;
	}

	protected function getDataFile ()
	{
		if (isset($this->data['file'])) {
			return $this->data['file'];
		}

		if (isset($this->parentData['file'])) {
			return $this->parentData['file'];
		}

		return FALSE;
	}

	protected function getPrevNextItemsFromGroup ($group, $key)
	{
		if (empty($group)) {
			return [NULL, NULL];
		}

		$keys = array_keys($group);

		sort($keys);

		$index = array_search($key, $keys);
		$prev  = $index !== FALSE && $index ? $index - 1 : count($keys) - 1;
		$next  = $index !== FALSE && $index < count($keys) - 1 ? $index + 1 : 0;

		return [$prev !== $index ? $keys[$prev] : NULL, $next !== $index ? $keys[$next] : NULL, $group];
	}

	protected function getPrevNextItems ()
	{
		$toc = $this->rd->getToc();

		$args = func_get_args();
		foreach ($args as $index => $arg) {
			$group = isset($group) ? $group[$args[$index - 1]] : $toc;
			$key   = $arg;
		}

		return $this->getPrevNextItemsFromGroup($group, $key);
	}

	protected function getPrevNextLinks ()
	{
		$args      = func_get_args();
		$lastIndex = func_num_args() - 1;

		[$prev, $next] = $this->getPrevNextItems(...$args);

		$links = [];

		if (!empty($next)) {
			$args[$lastIndex] = $next;
			$links['next']    = [\Urls::getDocUrl(...$args), $next];
		}

		if (!empty($prev) && $prev !== $next) {
			$args[$lastIndex] = $prev;
			$links['prev']    = [\Urls::getDocUrl(...$args), $prev];
		}

		return $links;
	}

	protected function getNsHtml ($ns)
	{
		return implode("&ZeroWidthSpace;\\", explode("\\", $ns));
	}

	protected function newLineToSpace ($str)
	{
		return is_string($str) ? trim(str_replace(["\n", "\\n"], ' ', $str)) : $str;
	}

	protected function getVarTypes ($param)
	{
		return implode('|', $param['type']);
	}

	protected function var_export ($var, $indent = "")
	{
		switch (gettype($var)) {
			case "string":
				return str_replace("\'", '"', "'" . addcslashes($var, "\\\'\r\n\t\v\f") . "'");
			case "array":
				$indexed = array_keys($var) === range(0, count($var) - 1);
				$r       = [];
				foreach ($var as $key => $value) {
					$r[] = "$indent    " . ($indexed ? "" : $this->var_export($key) . " => ") . $this->var_export($value, "$indent    ");
				}

				return "[\n" . implode(",\n", $r) . (count($r) ? "," : "") . "\n" . $indent . "]";
			case "boolean":
				return $var ? "TRUE" : "FALSE";
			default:
				return var_export($var, TRUE);
		}
	}

	protected function sortData ($origData)
	{
		$data = [];

		$sortKeys = ['constants', 'properties', 'methods'];

		foreach ($sortKeys as $key) {
			if (!empty($origData[$key])) {
				foreach ($origData[$key] as $visibility => $item) {
					foreach ($item as $name => $details) {
						$details['name']       = $name;
						$details['visibility'] = $visibility;
						$details['modifiers']  = $this->getModifierList($details, $visibility);
						$details['comment']    = !empty($details['comments']) ? $details['comments'][count($details['comments']) - 1] : NULL;
						$data[$key][$name]     = $details;
					}
				}
				ksort($data[$key]);
			}
		}

		$data['comment'] = !empty($origData['comments']) ? $origData['comments'][count($origData['comments']) - 1] : NULL;
		$data['tags']    = !empty($data['comment']) && !empty($data['comment']['tags']) ? $data['comment']['tags'] : [];

		return array_merge($origData, $data);
	}

	protected function getModifierList ($data, $visibility = NULL)
	{
		$modifiers = [];

		foreach (['final', 'abstract', 'static'] as $modifier) {
			if (array_key_exists($key = 'is' . ucfirst($modifier), $data) && $data[$key]) {
				$modifiers[] = $modifier;
			}
		}

		$visibility && ($modifiers[] = $visibility);

		return $modifiers;
	}

	protected function getRefType ()
	{
		switch ($this->ref) {
			case 'traits':
				return 'trait';
			case 'interfaces':
				return 'interface';
		}

		return 'class';
	}

	protected function buildProperty ($data, &$types, &$value)
	{
		$types = [];

		$possibleTagNames = ['const', 'opt_param', 'param', 'property', 'property-read', 'property-write', 'staticvar', 'var',];
		if (!empty($data['comment']['tags'])) {
			foreach ($data['comment']['tags'] as $tag) {
				if (in_array($tag['tagName'], $possibleTagNames)) {
					array_walk($tag['type'], function (&$type) {
						$type = preg_replace("/[^A-Za-z0-9-_\[\]]/", '', $type);
					});
					$types = $tag['type'];
					break;
				}
			}
		}

		$value = array_key_exists('value', $data) ? $data['value'] : NULL;
	}

	protected function buildPropertyField ($name, $types = [], $value = NULL, $modifiers = [], $url = FALSE, $isConstant = FALSE)
	{
		$modifiers_str = implode('', array_map(function ($modifier) {
			return '<span class="modifier">' . $modifier . '</span> ';
		}, $modifiers));

		$type = !empty($types) ? '<span class="type">' . $this->getVarTypes(['type' => $types]) . '</span> ' : '';

		$property_str = ($isConstant ? '' : '$') . '<var class="varname">' . $name . '</var>';

		$url && ($property_str = '<a href="' . $url . '"><strong>' . $property_str . '</strong></a>');

		$property_str = '<var class="varname">' . $property_str . '</var>';

		$value_str = isset($value) ? ' <span class="initializer"> = <strong><code>' . $this->newLineToSpace($this->var_export($value)) . '</code></strong></span>' : ' ';

		return "{$modifiers_str}{$type}{$property_str}{$value_str}<span class=\"initializer initializer-ending\">;</span>";
	}

	protected function buildParams ($data, &$params, &$returns)
	{
		$returns   = NULL;
		$paramTags = $params = [];
		$baseParam = ['desc' => '', 'optional' => FALSE, 'type' => []];

		if (!empty($data['comment']['tags'])) {
			foreach ($data['comment']['tags'] as $tag) {
				if ($tag['tagName'] === 'param') {
					array_walk($tag['type'], function (&$type) {
						$type = preg_replace("/[^A-Za-z0-9-_\[\]]/", '', $type);
					});
					$paramTags[] = $tag;
				}
			}
		}

		if (!empty($data['arguments'])) {
			$paramTagName = array_column($paramTags, 'name');
			foreach ($data['arguments'] as $arg) {
				if (($index = array_search($arg['name'], $paramTagName)) !== FALSE) {
					unset($paramTags[$index]['name']);
					$param = array_merge_recursive($arg, $paramTags[$index]);
					if (!empty($param['type'])) {
						$param['type'] = array_unique((array) $param['type']);
					}
					if (isset($param['value'])) {
						$param['optional'] = TRUE;
					}
					$params[] = $param;
					unset($paramTags[$index]);
				}
				else {
					if (isset($arg['value'])) {
						$arg['optional'] = TRUE;
					}
					$params[] = array_merge_recursive($arg, $baseParam);
				}
			}
		}

		if (!empty($paramTags)) {
			foreach ($paramTags as $index => $tag) {
				$params[] = $tag;
				unset($paramTags[$index]);
			}
		}

		if (!empty($data['comment']['tags'])) {
			foreach ($data['comment']['tags'] as $tag) {
				if ($tag['tagName'] === 'return') {
					$returns = $tag;
				}
			}
		}
	}

	protected function getParamNamePrefixed ($param, $addlPrefix = '$')
	{
		return (!empty($param['variadic']) ? '...' : '') . (!empty($param['byref']) ? '&' : '') . ($addlPrefix ?: '') . $param['name'];
	}

	protected function buildSignature ($name, $params = [], $returns = NULL, $modifiers = [], $url = NULL)
	{
		$optional = FALSE;

		$modifiers_str = implode('', array_map(function ($modifier) {
			return '<span class="modifier">' . $modifier . '</span> ';
		}, $modifiers));

		$property_str = "<b>{$name}</b>";

		$url && ($property_str = '<a href="' . $url . '">' . $property_str . '</a>');

		$property_str = "<span class='methodname'>{$property_str}</span>";

		$param_str = implode(', ', array_map(function ($param) use (&$optional) {
			$type        = !empty($param['type']) ? '<span class="type">' . $this->getVarTypes($param) . '</span> ' : '';
			$parameter   = '<code class="parameter">' . $this->getParamNamePrefixed($param) . '</code>';
			$initializer = array_key_exists('value', $param) || $optional ? '<span class="initializer"> = <strong><code>' . $this->newLineToSpace($this->var_export($param['value'] ?? NULL)) . '</code></strong></span>' : '';
			!empty($initializer) && ($optional = TRUE);

			return "<span class='methodparam'>{$type}{$parameter}{$initializer}</span>";
		}, $params));

		$return_types = !empty($returns) ? '<span class="type"> : ' . $this->getVarTypes($returns) . '</span>' : '';

		return "{$modifiers_str}{$property_str} ( {$param_str} ){$return_types}";
	}


	protected function getParamDefault ($param, &$var)
	{
		foreach (['defaultObj', 'defaultValue', 'value'] as $key) {
			if (array_key_exists($key, $param) && (!empty($param['optional']) || $key !== 'value')) {
				if ($param[$key] === 'empty') {
					if (isset($param['value'])) {
						$key = 'value';
					}
					elseif (in_array('array', $param['type'])) {
						$param[$key] = [];
					}
					elseif (in_array('string', $param['type'])) {
						$param[$key] = '';
					}
					elseif (in_array('object', $param['type'])) {
						$param[$key] = (object) [];
					}
				}

				$var = is_string($param[$key]) ? $this->newLineToSpace($param[$key]) : $param[$key];

				return TRUE;
			}
		}

		if (!empty($param['optional'])) {
			if (in_array('array', $param['type'])) {
				$var = [];

				return TRUE;
			}

			if (in_array('string', $param['type'])) {
				$var = '';

				return TRUE;
			}

			if (in_array('object', $param['type'])) {
				$var = (object) [];

				return TRUE;
			}
		}

		return FALSE;
	}

	protected function buildDescription ($param)
	{
		$lines = [];
		$html  = NULL;

		if (empty($param['tagName'])) {
			$param['tagName'] = 'param';
		}

		if (!in_array($param['tagName'], ['return', 'global'])) {
			$lines[] = '<b>' . (!empty($param['optional']) ? 'Optional' : 'Required') . '.</b>';
		}

		if (!empty($param['type'])) {
			$lines[] = '<span class="methodsynopsis"><span class="type"><b>' . $this->getVarTypes($param) . '</b></span></span>';
		}

		if (is_scalar($param['desc'])) {
			if (!empty($desc = $this->newLineToSpace($param['desc']))) {
				$lines[] = $desc;
			}
		}
		elseif (!empty($params = $param['desc']['tags'])) {
			if (!empty($desc = ($param['desc']['summary'] ?? '') . ' ' . ($param['desc']['description'] ?? ''))) {
				$lines[] = $desc;
			}
			$html = $this->buildParamTable($params);
		}

		if ($this->getParamDefault($param, $value)) {
			$lines[] = '<b>Default: <span class="initializer">' . $this->newLineToSpace($this->var_export($value)) . '</span></b>';
		}

		return $param['tagName'] === 'return' ? $html ? '<p>' . implode(' ', $lines) . '</p>' . $html : implode(' ', $lines) : [
			'blocks' => [implode(' ', $lines)],
			'html'   => $html,
		];
	}

	protected function buildParamTable ($params)
	{
		if (empty($params)) {
			return '';
		}

		ob_start();
		?>
		<table class="doctable informaltable">
			<thead>
			<tr>
				<th>Name</th>
				<th>Type</th>
				<th>Description</th>
			</tr>
			</thead>
			<tbody class="tbody">
			<?php foreach ($params as $param) : ?>
				<tr>
					<td><code class="parameter"><?= $this->getParamNamePrefixed($param, NULL) ?></code></td>
					<td><span class="methodsynopsis"><span class="type"><?= $this->getVarTypes($param) ?></span></span></td>
					<td><?php
						if (!empty($param['desc'])) {
							if (is_scalar($param['desc'])) {
								echo $this->newLineToSpace($param['desc']);
							}
							elseif (!empty($param['desc']['tags'])) {
								echo $this->buildParamTable($param['desc']['tags']);
							}
						}
						if (!array_key_exists('optional', $param)) {
							$param['optional'] = TRUE;
						}
						?><?= $this->getParamDefault($param, $value) ? ' <b>Default: <span class="initializer">' . (empty($value) || is_scalar($value) ? $this->var_export($value) : $this->buildParamTable($value)) . '</span></b>' : '' ?></td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
		<?php

		return ob_get_clean();
	}

	public function getRef ()
	{
		return $this->ref ?? NULL;
	}

	public function getTitle ()
	{
		return implode(' - ', array_filter([$this->rd->attr, $this->rd->name, $this->rd->getMeta('name'), 'RenderDocs']));
	}
}