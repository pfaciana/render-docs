<?php

function examples_section ($examples = [], $header = 'Examples')
{
	/*
		$examples = [
			'Just content.',
			['title' => 'No Number Title', 'content' => 'No Number Content'],
			['id' => 3000, 'content' => 'No Title Content'],
			['id' => 'Q', 'content' => 'Lettered Number'],
		];
	*/

	if (empty($examples)) {
		return '';
	}

	ob_start();

	?>
	<div class="refsect1 examples" id="refsect1-examples">
		<h3 class="title"><?= $header ?></h3>
		<?php foreach ($examples as $index => $example) : echo example_section($example, $index); endforeach; ?>
	</div>
	<?php

	return ob_get_clean();
}

function example_section ($example = [], $index = 0)
{
	if (is_string($example)) {
		$example = ['content' => $example];
	}

	if (empty($example['id'])) {
		$example['id'] = $index + 1;
	}

	if (is_numeric($example['id'])) {
		$example['id'] = '#' . $example['id'];
	}

	ob_start();

	?>
	<div class="example" id="example-<?= $example['id'] ?>">
		<p><strong>Example <?= $example['id'] ?> <?= $example['title'] ?? '' ?></strong></p>
		<div class="example-contents">
			<div class="phpcode"><code><?= $example['content'] ?></code></div>
		</div>
	</div>
	<?php

	return ob_get_clean();
}