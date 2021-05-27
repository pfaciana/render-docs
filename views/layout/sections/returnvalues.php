<?php

function returnvalues_section ($returnvalue = '', $header = 'Return Values')
{
	/*
		$returnvalue = 'Returns an array of values representing a single column from the input array.';
	*/

	if (empty($returnvalue)) {
		return '';
	}

	ob_start();

	?>
	<div class="refsect1 returnvalues" id="refsect1-returnvalues">
		<h3 class="title"><?= $header ?></h3>
		<?php
		if (is_array($returnvalue)) :
			echo '<p>' . implode('</p><p>', $returnvalue) . '</p>';
		else :
			echo $returnvalue;
		endif;
		?>
	</div>
	<?php

	return ob_get_clean();
}
