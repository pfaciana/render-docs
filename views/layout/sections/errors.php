<?php

function errors_section ($errors = '', $header = 'Throws')
{
	/*
		$errors = [
			'Error messages details 1',
			'NOT_FOUND'     => '1 Lorem ipsum dolor sit amet, consectetur adipisicing elit.',
			'Error messages details 2',
			'STILL_MISSING' => '2 Lorem ipsum dolor sit amet, consectetur adipisicing elit.',
			'WHATS_UP'      => [
				'blocks' => ['3 Lorem ipsum dolor sit amet, consectetur adipisicing elit.', '4 Lorem ipsum dolor sit amet, consectetur adipisicing elit.'],
				'html'   => '<div class="methodsynopsis dc-description">
							        <span class="methodname"><span class="replaceable">callback</span></span> ( <span class="methodparam"><span class="type"><a href="language.types.declarations.php#language.types.declarations.mixed" class="type mixed">mixed</a></span> <code class="parameter">$value</code></span> ) : <span class="type"><a href="language.types.declarations.php#language.types.declarations.mixed" class="type mixed">mixed</a></span>
							        </div>',
			],
		];
	*/

	if (empty($errors)) {
		return '';
	}

	ob_start();

	$dlOpen = FALSE;
	?>
	<div class="refsect1 errors" id="refsect1-errors">
		<h3 class="title"><?= $header ?></h3>
		<?php
		foreach ((array) $errors as $key => $error) :
			if (is_int($key)) {
				$dlOpen && (print '</dl>') && ($dlOpen = FALSE);
				echo '<p>' . $error . '</p>';
			}
			else {
				!$dlOpen && (print '<dl>') && ($dlOpen = TRUE);
				echo global_section(['name' => $key, 'description' => $error]);
			}
		endforeach;
		$dlOpen && (print '</dl>') && ($dlOpen = FALSE);
		?>
	</div>
	<?php

	return ob_get_clean();
}