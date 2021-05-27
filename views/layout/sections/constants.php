<?php

function constants_section ($constants = [], $header = 'Constants')
{
	/*
		$constants = [
			'4.5.6' => 'Lorem ipusm...',
			'1.2.3' => 'Lorem ipusm...',
		];
	*/

	if (empty($constants)) {
		return '';
	}

	ob_start();

	?>
	<div class="refsect1 constants" id="refsect1-constants">
		<h3 class="title"><?= $header ?></h3>
		<table class="doctable table">
			<thead>
			<tr>
				<th>Value</th>
				<th>Constant</th>
			</tr>
			</thead>
			<tbody class="tbody">
			<?php foreach ($constants as $version => $description) : ?>
				<tr>
					<td><?= $version ?></td>
					<td><strong><code><?= $description ?></code></strong></td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
	</div>
	<?php

	return ob_get_clean();
}