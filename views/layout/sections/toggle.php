<?php

function toggle_section ($show = FALSE)
{
	if (empty($show)) {
		return '';
	}

	ob_start();

	?>
	<div class="show-hide-labels" style="display: none">
		<span class="controls">
			<a>[?]</a>
		</span>
		<ul style="display: none">
			<li><a data-key="modifier">modifiers</a>
			<li class="has-classsynopsis" style="display: none">[<a data-key="modifier" data-value="public">public</a></li>
			<li class="has-classsynopsis" style="display: none"><a data-key="modifier" data-value="protected">protected</a></li>
			<li class="has-classsynopsis" style="display: none"><a data-key="modifier" data-value="private">private</a>]</li>
			<li><a data-key="type">types</a></li>
			<li><a data-key="initializer">initializers</a></li>
		</ul>
	</div>
	<?php

	return ob_get_clean();
}