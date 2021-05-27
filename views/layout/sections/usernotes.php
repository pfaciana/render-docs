<?php

function usernotes_section ($notes = [], $header = 'User Contributed Notes', $types = 'notes')
{
	if (empty($notes)) {
		return '';
	}

	ob_start();

	?>
	<section id="usernotes">
		<div class="head">
			<h3 class="title"><?= $header ?> <span class="count"><?= count($notes) ?> <?= $types ?></span></h3>
		</div>
		<div id="allnotes">
			<?php foreach ($notes as $note) : echo usernote_section($note); endforeach; ?>
		</div>
	</section>
	<?php

	return ob_get_clean();
}

function usernote_section ($note)
{
	/*
		$note = [
			'title'   => 'Note title',
			'date'    => '2018-05-16 02:54:00',
			'content' => 'This function does not preserve the original keys of the array (when not providing an index_key).',
		];
	 */

	$time = strtotime($note['date'] = $note['date'] ?? '2000-01-01 00:00:00');

	ob_start();

	?>
	<div class="note" id="<?= $time ?>">
		<a href="#<?= $time ?>" class="name"><strong class="user"><em><?= $note['title'] ?></em></strong></a>
		<div class="date" title="<?= $note['date'] ?>"><strong><?= date('F j, Y', $time) ?></strong></div>
		<div class="text" id="Hcom<?= $time ?>">
			<div class="phpcode"><code><span class="html"><?= $note['content'] ?></code></div>
		</div>
	</div>
	<?php
	return ob_get_clean();
}