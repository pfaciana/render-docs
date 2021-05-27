<?php

function tableofcontents_section ($data = [], $header = 'Table of Contents')
{
	/*
		$links = [
			'Link #1' => '#',
			'Link #2' => [
				'url'     => '#',
				'summary' => 'summary 2',
				'links'   => [
					'Link #2a' => '#',
					'<b>Link #2b</b>' => [
						'links' => [
							'Link #2a1' => '#',
							'Link #2b2' => '#',
							'Link #2c3' => '#',
						],
					],
					'Link #2c' => '#',
				],
			],
			'Link #3' => ['url' => '#', 'summary' => ''],
		];
	*/

	if (empty($data)) {
		return '';
	}

	ob_start();

	echo '<h2>', $header, '</h2>';
	if (!empty($data['links'])) {
		echo tableofcontentslist_section($data['links'], '');
	}
	if (!empty($data['content'])) {
		echo $data['content'];
	}

	return ob_get_clean();
}

function tableofcontentslist_section ($links = [], $class = 'chunklist_children')
{
	ob_start();

	?>
	<ul class="chunklist chunklist_chapter <?= $class ?>">
		<?php foreach ($links as $text => $link) :
			$link = !is_array($link) ? ['url' => $link] : $link; ?>
			<li>
				<?php if (!empty($link['url'])) : ?><a href="<?= $link['url'] ?>"><?= $text ?></a><?php else : echo $text; endif; ?>
				<?= !empty($link['summary']) ? " — {$link['summary']}" : '' ?>
				<?php if (!empty($link['links'])) :
					echo tableofcontentslist_section($link['links']);
				endif; ?>
			</li>
		<?php endforeach; ?>
	</ul>
	<?php

	return ob_get_clean();
}