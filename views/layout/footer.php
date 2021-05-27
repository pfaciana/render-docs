<?php

function footer ($links = [])
{
	?>
	<footer>
		<div class="container footer-content">
			<div class="row-fluid">
				<ul class="footmenu">
					<?php foreach ($links as $text => $link) : ?>
						<li><a href="<?= $link ?>"><?= $text ?></a></li>
					<?php endforeach; ?>
				</ul>
			</div>
		</div>
	</footer>
	</body>
	</html>
	<?php
}