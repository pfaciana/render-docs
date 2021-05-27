<?php

function options_section ($options = [], $header = 'Details')
{
	/*
		$options = [
			'title' => 'Context Summary',
			'keys'  => ['Attribute', 'Option #1', 'Option #2', 'Option #3'],
			'data'  => [
				[
					'Name'     => 'Allows Reading',
					'Option 1' => 'Yes',
					'Option 2' => 'Yes',
					'Option 3' => 'Yes',
				],
				[
					'Name'     => 'Allows Writing',
					'Option 1' => TRUE,
					'Option 2' => TRUE,
					'Option 3' => FALSE,
				],
				// No array keys are needed for the non-first item in the array, or all items if `keys` is defined
				['Allows Appending', TRUE, FALSE, FALSE,],
				['Allows Simultaneous Reading and Writing', 'No', 'No', 'No',],
				['Allows Simultaneous Reading, Writing and Appending', 'N/A', '', NULL,],
			],
		];
	*/

	if (empty($options) || empty($options['data'])) {
		return '';
	}

	ob_start();

	$headers = !empty($options['keys']) ? $options['keys'] : array_keys($options['data'][0]);

	?>
	<div class="refsect1 options" id="refsect1-options">
		<h3 class="title"><?= $header ?></h3>
		<table class="doctable table">
			<?php if (!empty($options['title'])) : ?>
				<caption><strong><?= $options['title'] ?></strong></caption><?php endif; ?>
			<thead>
			<tr>
				<?php foreach ($headers as $header) : ?>
					<th><?= $header ?></th>
				<?php endforeach; ?>
			</tr>
			</thead>
			<tbody class="tbody">
			<?php foreach ($options['data'] as $item) : ?>
				<tr>
					<?php foreach ($item as $value) :
						if (is_bool($value)) {
							$value = $value ? 'Yes' : 'No';
						}
						?>
						<td><?= $value ?></td>
					<?php endforeach; ?>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
	</div>
	<?php

	return ob_get_clean();
}