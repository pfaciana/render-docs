<?php

function globals_section ($globals = [], $header = 'Globals')
{
	/*
		$globals = [
			[
				'name'        => 'input',
				'description' => ['line 1', 'line 2'],
			],
			[
				'name'        => 'count',
				'description' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit.',
			],
			[
				'name'        => 'callback',
				'description' => [
					'html' => '<div class="methodsynopsis dc-description">
						            <span class="methodname"><span class="replaceable">callback</span></span> ( <span class="methodparam"><span class="type"><a href="language.types.declarations.php#language.types.declarations.mixed" class="type mixed">mixed</a></span> <code class="parameter">$value</code></span> ) : <span class="type"><a href="language.types.declarations.php#language.types.declarations.mixed" class="type mixed">mixed</a></span>
						        </div>
						        <p>A callable to apply to each value in the deque.</p>
						        <p>The callback should return what the value should be replaced by.</p>',
				],
			],
			[
				'name'        => 'callback2',
				'description' => [
					'blocks' => ['A callable to apply to each value in the deque', 'The callback should return what the value should be replaced by.'],
					'html'   => '<div class="methodsynopsis dc-description">
						        <span class="methodname"><span class="replaceable">callback</span></span> ( <span class="methodparam"><span class="type"><a href="language.types.declarations.php#language.types.declarations.mixed" class="type mixed">mixed</a></span> <code class="parameter">$value</code></span> ) : <span class="type"><a href="language.types.declarations.php#language.types.declarations.mixed" class="type mixed">mixed</a></span>
						        </div>',
				],
			],
		];
	*/

	if (empty($globals)) {
		return '';
	}

	ob_start();

	?>
	<div class="refsect1 parameters" id="refsect1-parameters">
		<h3 class="title"><?= $header ?></h3>
		<dl>
			<?php foreach ($globals as $global) : echo global_section($global); endforeach; ?>
		</dl>
	</div>
	<?php

	return ob_get_clean();
}

function global_section ($global)
{
	if (empty($global)) {
		return '';
	}

	if (is_string($global['description'])) {
		$global['description'] = ['blocks' => [$global['description']]];
	}

	if (array_keys($global['description']) === range(0, count($global['description']) - 1)) {
		$global['description'] = ['blocks' => $global['description']];
	}

	ob_start();

	?>
	<dt>
		<code class="parameter"><?= $global['name'] ?></code>
	</dt>
	<dd>
		<?= !empty($global['description']['blocks']) ? '<p>' . implode('</p><p>', (array) $global['description']['blocks']) . '</p>' : '' ?>
		<?= !empty($global['description']['html']) ? $global['description']['html'] : '' ?>
	</dd>
	<?php

	return ob_get_clean();
}
