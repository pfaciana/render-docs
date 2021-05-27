<?php

function homePage ()
{
	ob_start();

	head(['title' => 'Home - RenderDocs']);

	?>

	<?= breadcrumbs(['links' => ['All Documentation' => \Urls::getDocsUrl()]]) ?>

	<div id="layout" class="clearfix">

		<section id="layout-content">

			<div class="refentry">

				<?= name_section(['name' => 'Welcome']) ?>

				<p><strong><a href="<?= \Urls::getDocsUrl() ?>">View Documentation</a></strong></p>

			</div>

		</section>

	</div>

	<?php

	footer();

	return ob_get_clean();
}