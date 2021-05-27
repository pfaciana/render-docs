<?php

function seealso_section ($links = [], $header = 'See Also')
{
	/*
		$links = [
			'Link #1' => '#',
			'Link #2' => ['url' => '#', 'summary' => 'summary 2'],
			'Link #3' => ['url' => '#', 'summary' => ''],
		];
	*/

	if (empty($links)) {
		return '';
	}

	ob_start();

	?>
	<div class="refsect1 seealso" id="refsect1-seealso">
		<h3 class="title"><?= $header ?></h3>
		<ul class="simplelist">
			<?php foreach ($links as $text => $link) :
				$link = !is_array($link) ? ['url' => $link] : $link; ?>
				<li><a href="<?= $link['url'] ?>"><?= $text ?></a><?= !empty($link['summary']) ? " — {$link['summary']}" : '' ?></li>
			<?php endforeach; ?>
		</ul>
	</div>
	<?php

	return ob_get_clean();
}