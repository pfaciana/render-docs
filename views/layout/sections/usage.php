<?php

// TODO: Add summary option like SeeAlso
function usage_section ($usages = [], $header = 'Usage')
{
	/*
		$usages = [
			'header' => [
				'glob://',
				'http://example.com',
				'https://example.com'               => 'https://example.com',
				'https://user:password@example.com' => 'https://user:password@example.com',
			]
		];
	*/

	if (empty($usages)) {
		return '';
	}

	ob_start();

	?>
	<div class="refsect1 usage" id="refsect1-usage">
		<h3 class="title"><?= $header ?></h3>
		<?php foreach ($usages as $title => $items) : ?>
			<?php if (!empty(trim($title) && !empty($items))) : ?>
				<strong><?= $title ?></strong>
			<?php endif; ?>
			<?php if (!empty($items)) : ?>
				<ul class="itemizedlist">
					<?php foreach ($items as $key => $var) :
						$var = !is_array($var) ? ['url' => $var] : $var; ?>
						<li class="listitem">
					<span class="simpara">
						<var><?= is_int($key) ? $var['url'] : '<a href="' . $var['url'] . '">' . $key . '</a>' ?></var><?= !empty($var['summary']) ? " — {$var['summary']}" : '' ?>
					</span>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		<?php endforeach; ?>
	</div>
	<?php

	return ob_get_clean();
}