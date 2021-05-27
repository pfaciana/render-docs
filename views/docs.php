<?php

function docsPage ()
{
	$tocLinks = [];

	if (!empty($projectDirs = glob(RenderDocs2PhpNet::getRootDir() . '*', GLOB_ONLYDIR))) {
		foreach ($projectDirs as $projectDir) {
			$project = new RenderDocs2PhpNet(str_replace(RenderDocs2PhpNet::getRootDir(), '', $projectDir));

			$tocLinks[$project->getMeta('name')] = [
				'url'     => $project->getUrl(),
				'summary' => $project->getMeta('description') ?? NULL,
			];
		}
	}

	ksort($tocLinks);

	ob_start();

	head(['title' => 'All Documentation - RenderDocs']);

	?>

	<?= breadcrumbs(['links' => ['All Documentation' => \Urls::getDocsUrl()]]) ?>

	<div id="layout" class="clearfix">

		<section id="layout-content">

			<div class="refentry">

				<?= name_section(['name' => 'All Projects']) ?>

				<?= tableofcontents_section(['links' => $tocLinks]) ?>

			</div>

		</section>

	</div>

	<?php

	footer();

	return ob_get_clean();
}