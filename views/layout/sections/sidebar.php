<?php

function sidebar_section ($data = [])
{
	/*
		$data = [
			'header'    => 'Array Functions',
			'group_url' => '#',
			'links'     => [
				'Link #1' => '#',
				'Link #2' => ['url' => '#', 'current' => TRUE],
				'Link #3' => ['url' => '#', 'current' => FALSE],
			],
			'alt_links' => [
				[
					'header' => 'Deprecated',
					'links'  => [
						'Link #4' => '#',
						'Link #5' => ['url' => '#', 'current' => TRUE],
						'Link #6' => ['url' => '#', 'current' => FALSE],
					],
				],
				[
					'header' => 'Other',
					'links'  => [
						'Link #7' => '#',
						'Link #8' => ['url' => '#', 'current' => TRUE],
						'Link #9' => ['url' => '#', 'current' => FALSE],
					],
				],
			],
		];
	*/

	if (empty($data)) {
		return '';
	}

	ob_start();

	?>
	<aside class="layout-menu">
		<ul class="parent-menu-list">
			<li>
				<?php if (!empty($data['group_url'])) : ?>
					<a href="<?= $data['group_url'] ?>"><?= $data['header'] ?></a>
				<?php else : ?>
					<?= $data['header'] ?>
				<?php endif; ?>
				<?php if (!empty($data['links'])) : ?>
					<ul class="child-menu-list">
						<?php foreach ($data['links'] as $text => $link) :
							$link = !is_array($link) ? ['url' => $link] : $link; ?>
							<li class="<?= !empty($link['current']) ? 'current' : '' ?>">
								<a href="<?= $link['url'] ?>" title="<?= htmlspecialchars($link['summary'] ?? $text) ?>"><?= htmlspecialchars($text) ?></a>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</li>
			<?php if (!empty($data['alt_links'])) : ?>
				<?php foreach ($data['alt_links'] as $alt_link) : ?>
					<li>
						<span class="header"><?= $alt_link['header'] ?></span>
						<ul class="child-menu-list">
							<?php foreach ($alt_link['links'] as $text => $link) :
								$link = !is_array($link) ? ['url' => $link] : $link; ?>
								<li class="<?= !empty($link['current']) ? 'current' : '' ?>">
									<a href="<?= $link['url'] ?>" title="<?= htmlspecialchars($link['summary'] ?? $text) ?>"><?= htmlspecialchars($text) ?></a>
								</li>
							<?php endforeach; ?>
						</ul>
					</li>
				<?php endforeach; ?>
			<?php endif; ?>
		</ul>
	</aside>
	<?php

	return ob_get_clean();
}