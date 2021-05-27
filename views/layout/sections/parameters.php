<?php

function parameters_section ($parameters = [], $header = 'Parameters')
{
	/*
		$parameters = [
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

	if (empty($parameters)) {
		return '';
	}

	ob_start();

	?>
	<div class="refsect1 parameters" id="refsect1-parameters">
		<h3 class="title"><?= $header ?></h3>
		<dl>
			<?php foreach ($parameters as $parameter) : echo global_section($parameter); endforeach; ?>
		</dl>
	</div>
	<?php

	return ob_get_clean();
}

function parameter_section ($parameter)
{
	if (empty($parameter)) {
		return '';
	}

	if (is_string($parameter['description'])) {
		$parameter['description'] = ['blocks' => [$parameter['description']]];
	}

	if (array_keys($parameter['description']) === range(0, count($parameter['description']) - 1)) {
		$parameter['description'] = ['blocks' => $parameter['description']];
	}

	ob_start();

	?>
	<dt>
		<code class="parameter"><?= $parameter['name'] ?></code>
	</dt>
	<dd>
		<?= !empty($parameter['description']['blocks']) ? '<p>' . implode('</p><p>', (array) $parameter['description']['blocks']) . '</p>' : '' ?>
		<?= !empty($parameter['description']['html']) ? $parameter['description']['html'] : '' ?>
	</dd>
	<?php

	return ob_get_clean();
}
