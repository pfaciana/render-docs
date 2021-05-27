<?php

function changelog_section ($changes = [], $header = 'Changelog')
{
	/*
		$changes = [
			'4.5.6' => 'Lorem ipusm...',
			'1.2.3' => 'Lorem ipusm...',
		];
	*/

	if (empty($changes)) {
		return '';
	}

	ob_start();

	?>
	<div class="refsect1 changelog" id="refsect1-changelog">
		<h3 class="title"><?= $header ?></h3>
		<table class="doctable informaltable">
			<thead>
			<tr>
				<th>Version</th>
				<th>Description</th>
			</tr>
			</thead>
			<tbody class="tbody">
			<?php foreach ($changes as $version => $description) : ?>
				<tr>
					<td><?= $version ?></td>
					<td><?= $description ?></td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
	</div>
	<?php

	return ob_get_clean();
}