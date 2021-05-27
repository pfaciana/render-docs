<?php

function name_section ($data = [])
{
	/*
		$data = [
			'name'    => 'array_column',
			'summary' => 'Return the values from a single column in the input array',
		];
	*/

	if (empty($data)) {
		return '';
	}

	if (is_string($data)) {
		$data = ['name' => $data];
	}

	ob_start();

	?>
	<div class="refnamediv">
		<h1 class="refname"><?= $data['name'] ?></h1>
		<?php if (!empty($data['summary'])) : ?><p class="refpurpose"><span class="dc-title"><?= $data['summary'] ?></span></p><?php endif; ?>
	</div>
	<?php

	return ob_get_clean();
}