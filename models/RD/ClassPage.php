<?php

namespace RD;

class ClassPage extends PageLayout
{

	public function __construct ($id, $namespace, $ref, $name)
	{
		parent::__construct($id);
		$this->ns   = str_replace('-', "\\", $namespace);
		$this->ref  = $ref;
		$this->name = $name;

		$this->details = new Ref($this->ref, $this->name, $this->ns);

		$this->origData = $this->details->getData();
		$this->data     = $this->sortData($this->origData);
	}

	public function getBreadcrumbs ()
	{
		$ns   = $this->ns;
		$ref  = $this->ref;
		$name = $this->name;

		$links = array_merge($this->getPrevNextLinks($ns, $ref, $name), [
			'links' => [
				'All'                => \Urls::getDocsUrl(),
				$this->projectName              => \Urls::getDocUrl(),
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

	public function getSynopsis ()
	{
		if (empty($this->name)) {
			return '';
		}

		$data = ['type' => $this->ref, 'signature' => $this->buildSynopsis($this->data)];

		if (!empty($this->data['comment']['description'])) {
			$data['description'] = $this->data['comment']['description'];
		}

		return $data;
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

	protected function buildSynopsis ($data)
	{
		ob_start();
		?>
		<div class="classsynopsis">
			<div class="classsynopsisinfo">
				<?php if (!empty($data['isFinal'])) : ?>
					<span class="modifier">final</span>
				<?php endif ?>
				<?php if (!empty($data['isAbstract'])) : ?>
					<span class="modifier">abstract</span>
				<?php endif ?>
				<?= $this->getRefType() ?> <span class="ooclass"><strong class="classname"><?= $this->name ?></strong></span>
				<?php if (!empty($data['extends'])) :
					$url = (new Ref('classes', $data['extends']))->getUrl();
					$extends = $url ? '<a href="' . $url . '"><b>' . $data['extends'] . '</b></a>' : $data['extends'];
					?>
					<span class="ooclass"><span class="modifier">extends </span><?= $extends ?></span>
				<?php endif; ?>
				<?php if (!empty($data['implements'])) : ?>
					<span class="oointerface">implements <?= implode(', ', array_map(function ($implements) {
							$url        = (new Ref('interfaces', $implements))->getUrl();
							$implements = $url ? '<a href="' . $url . '"><b>' . $implements . '</b></a>' : $implements;

							return '<span class="interfacename">' . $implements . '</span>';
						}, $data['implements'])); ?></span>
				<?php endif; ?>
				{
			</div>

			<?php if (!empty($data['traits']['use'])) :
				echo '<div class="classsynopsisinfo classsynopsisinfo_comment">/* Traits */</div>';
				$traits = array_keys($data['traits']['use']);
				foreach ($traits as &$trait) :
					$url   = (new Ref('traits', $trait))->getUrl();
					$trait = $url ? '<a href="' . $url . '"><b>' . $trait . '</b></a>' : $trait;
				endforeach;
				echo '<div class="methodsynopsis">use ', implode(', ', $traits);
				if (!empty($data['traits']['adaptations'])):
					echo ' {';
					foreach ($data['traits']['adaptations'] as $name => $details) :
						echo '<div class="methodsynopsis">', $name, ' from ', $details['use'];
						(!empty($details['method'])) && print '::' . $details['method'];
						echo ';</div>';
					endforeach;
					echo '}';
				else:
					echo ';';
				endif;
				echo '</div>';
			endif; ?>

			<?php if (!empty($data['constants'])) : ?>
				<div class="classsynopsisinfo classsynopsisinfo_comment">/* Constants */</div>
				<?php
				foreach ($data['constants'] as $name => $args) {
					$this->buildProperty($args, $types, $value);
					$url = (new Ref([$this->ref, 'constants'], [$this->name, $name], $this->ns))->getUrl();
					echo '<div class="fieldsynopsis">' . $this->buildPropertyField($name, $types, $value, $args['modifiers'], $url, TRUE) . '</div>';
				}
				?>
			<?php endif; ?>

			<div class="classsynopsisinfo classsynopsisinfo_comment">/* Properties */</div>
			<?php
			if (!empty($data['properties'])) :
				foreach ($data['properties'] as $name => $args) {
					$this->buildProperty($args, $types, $value);
					$url = (new Ref([$this->ref, 'properties'], [$this->name, $name], $this->ns))->getUrl();
					echo '<div class="fieldsynopsis">' . $this->buildPropertyField($name, $types, $value, $args['modifiers'], $url) . '</div>';
				}
			else :
				echo '<div class="fieldsynopsis">None.</div>';
			endif;
			?>

			<div class="classsynopsisinfo classsynopsisinfo_comment">/* Methods */</div>
			<?php
			if (!empty($data['methods'])) :
				foreach ($data['methods'] as $name => $args) {
					$this->buildParams($args, $params, $returns);
					$url = (new Ref([$this->ref, 'methods'], [$this->name, $name], $this->ns))->getUrl();
					echo '<div class="methodsynopsis dc-description">' . $this->buildSignature($name, $params, $returns, $args['modifiers'], $url) . '</div>';
				}
			else :
				echo '<div class="methodsynopsis dc-description">None.</div>';
			endif;
			?>
			}
		</div>
		<?php
		return ob_get_clean();
	}

}