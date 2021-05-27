<?php

function exceptions_section ($exceptions = [], $header = 'Deprecated')
{
	/*
		$exceptions = [
			'Exception #1',
			['Exception #2', 'Exception #3'],
			'Caution'  => 'Caution #1',
			'Caution ' => 'Caution #2',
		];
	*/

	if (empty($exceptions)) {
		return '';
	}

	ob_start();

	?>
	<div class="refsect1 exceptions" id="refsect1-exceptions">
		<h3 class="title"><?= $header ?></h3>
		<?php foreach ($exceptions as $type => $exception) : echo exception_section($exception, trim($type)); endforeach; ?>
	</div>
	<?php

	return ob_get_clean();
}

function exception_section ($exception, $type = 'Warning')
{
	if (is_int($type) || ctype_digit($type)) {
		$type = 'Warning';
	}

	ob_start();

	?>
	<div class="<?= strtolower($type) ?>"><strong class="<?= strtolower($type) ?>"><?= $type ?></strong>
		<p><?= implode('</p><p>', (array) $exception) ?></p>
	</div>
	<?php

	return ob_get_clean();
}