<?php

function notes_section ($notes = [], $header = 'Notes')
{
	/*
		$notes = [
			'1 Lorem ipsum dolor sit amet, consectetur adipisicing elit.',
			['2 Lorem ipsum dolor sit amet, consectetur adipisicing elit.', '3 Lorem ipsum dolor sit amet, consectetur adipisicing elit.'],
			[
				'inline' => '4 Lorem ipsum dolor sit amet, consectetur adipisicing elit.',
				'blocks' => '5 Lorem ipsum dolor sit amet, consectetur adipisicing elit.',
			],
			[
				'inline' => '6 Lorem ipsum dolor sit amet, consectetur adipisicing elit.',
				'blocks' => ['7 Lorem ipsum dolor sit amet, consectetur adipisicing elit.', '8 Lorem ipsum dolor sit amet, consectetur adipisicing elit.'],
			],
			['html' => '<div>Div not a P, user generated html</div>'],
		];
	*/

	if (empty($notes)) {
		return '';
	}

	if (is_string($notes)) {
		$notes = [$notes];
	}

	ob_start();

	?>
	<div class="refsect1 notes" id="refsect1-notes">
		<h3 class="title"><?= $header ?></h3>
		<?php foreach ($notes as $note) : echo note_section($note); endforeach; ?>
	</div>
	<?php

	return ob_get_clean();
}

function note_section ($note)
{
	if (empty($note)) {
		return '';
	}
	elseif (is_string($note)) {
		$note = ['inline' => $note, 'blocks' => ''];
	}
	elseif (array_keys($note) === range(0, count($note) - 1)) {
		$note = ['inline' => '', 'blocks' => $note];
	}

	ob_start();

	?>
	<blockquote class="note">
		<p><strong class="note">Note</strong>: <?= !empty($note['inline']) ? $note['inline'] : '' ?></p>
		<?= !empty($note['blocks']) ? '<p>' . implode('</p><p>', (array) $note['blocks']) . '</p>' : '' ?>
		<?= !empty($note['html']) ? $note['html'] : '' ?>
	</blockquote>
	<?php

	return ob_get_clean();
}