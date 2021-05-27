<?php

function description_section ($data = [], $header = 'Description')
{
	/*
		$data = [
			'signature'    => 'signature',
			'description'  => 'Returns a message',
			'description2' => ['Returns a message', 'Accepts multiple args'],
			'description3' => [
				'blocks' => ['Returns a message', 'Accepts multiple args'],
				'html'   => '<div class="methodsynopsis dc-description">
							        <span class="methodname"><span class="replaceable">callback</span></span> ( <span class="methodparam"><span class="type"><a href="language.types.declarations.php#language.types.declarations.mixed" class="type mixed">mixed</a></span> <code class="parameter">$value</code></span> ) : <span class="type"><a href="language.types.declarations.php#language.types.declarations.mixed" class="type mixed">mixed</a></span>
							        </div>',
				'notes'  => [
					['2 Lorem ipsum dolor sit amet, consectetur adipisicing elit.', '3 Lorem ipsum dolor sit amet, consectetur adipisicing elit.'],
					[
						'inline' => '4 Lorem ipsum dolor sit amet, consectetur adipisicing elit.',
						'blocks' => '5 Lorem ipsum dolor sit amet, consectetur adipisicing elit.',
					],
					[
						'inline' => '6 Lorem ipsum dolor sit amet, consectetur adipisicing elit.',
						'blocks' => ['7 Lorem ipsum dolor sit amet, consectetur adipisicing elit.', '8 Lorem ipsum dolor sit amet, consectetur adipisicing elit.'],
					],
					['html' => '<div>Div not a P, user generated html</div>'],
				],
			],
		];
	*/

	if (empty($data)) {
		return '';
	}

	ob_start();

	if (is_string($data['description'] ?? NULL)) {
		$data['description'] = ['blocks' => [$data['description']]];
	}

	if (is_array($data['description'] ?? NULL) && array_keys($data['description']) === range(0, count($data['description']) - 1)) {
		$data['description'] = ['blocks' => $data['description']];
	}

	?>
	<div class="refsect1 description" id="refsect1-description">
		<h3 class="title"><?= $header ?></h3>
		<?php if (!empty($data['signature']) && (empty($data['type']) || !in_array($data['type'], ['classes', 'traits', 'interfaces']))) : ?>
			<div class="methodsynopsis dc-description"><?= $data['signature'] ?></div>
		<?php endif; ?>
		<?= !empty($data['description']['blocks']) ? '<p>' . implode('</p><p>', (array) $data['description']['blocks']) . '</p>' : '' ?>
		<?= !empty($data['description']['html']) ? $data['description']['html'] : '' ?>
		<?php foreach ((array) ($data['description']['notes'] ?? NULL) as $note) : echo note_section($note); endforeach; ?>
		<?php if (!empty($data['signature']) && !empty($data['type']) && in_array($data['type'], ['classes', 'traits', 'interfaces'])) : ?>
			<div class="methodsynopsis dc-description"><?= $data['signature'] ?></div>
		<?php endif; ?>
	</div>
	<?php

	return ob_get_clean();
}
