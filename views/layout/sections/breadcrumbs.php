<?php

function breadcrumbs ($data = [])
{
	/*
		$data = [
			'prev'  => ['#', 'array_combine'],
			'next'  => 'array_chunk',
			'links' => [
				'PHP Manual'          => '#',
				'Function Reference'  => '#',
				'Variable Extensions' => '#',
				'Arrays'              => '#',
				'Array Functions'     => '#',
				'/some/page',
			],
		];
	*/

	if (empty($data)) {
		return '';
	}

	ob_start();

	?>
	<div id="breadcrumbs" class="clearfix">
		<div id="breadcrumbs-inner">
			<?php if (!empty($data['next']) && ($data['next'] = (array) $data['next'])) : ?>
				<div class="next">
					<a href="<?= $data['next'][0] ?>"> <?= $data['next'][1] ?? $data['next'][0] ?> » </a>
				</div>
			<?php endif; ?>
			<?php if (!empty($data['prev']) && ($data['prev'] = (array) $data['prev'])) : ?>
				<div class="prev">
					<a href="<?= $data['prev'][0] ?>"> « <?= $data['prev'][1] ?? $data['prev'][0] ?> </a>
				</div>
			<?php endif; ?>
			<?php if (!empty($data['links'])) : ?>
				<ul>
					<?php foreach ($data['links'] as $text => $url) :
						if (is_int($text)) {
							$text = $url;
						} ?>
						<li><a href="<?= $url ?>"><?= $text ?></a></li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>
	</div>
	<?php

	return ob_get_clean();
}