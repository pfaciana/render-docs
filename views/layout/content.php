<?php

function content ($instance)
{
	ob_start();

	head(['title' => $instance->getTitle()]);

	?>

	<?= breadcrumbs($instance->getBreadcrumbs()) ?>

	<div id="layout" class="clearfix">

		<section id="layout-content">

			<div class="refentry">

				<?= toggle_section(TRUE) ?>

				<?= name_section($instance->getName()) ?>

				<?= description_section($instance->getDescription()) ?>

				<?= synopsis_section($instance->getSynopsis()) ?>

				<?= tableofcontents_section($instance->getTableOfContents()) ?>

				<?= parameters_section($instance->getParameters()) ?>

				<?= returnvalues_section($instance->getReturnValues()) ?>

				<?= globals_section($instance->getGlobals()) ?>

				<?= constants_section($instance->getConstants()) ?>

				<?= errors_section($instance->getErrors()) ?>

				<?= exceptions_section($instance->getExceptions()) ?>

				<?= examples_section($instance->getExamples()) ?>

				<?= usage_section($instance->getUsage()) ?>

				<?= seealso_section($instance->getSeeAlso()) ?>

				<?= changelog_section($instance->getChangelog()) ?>

				<?= options_section($instance->getOptions(), 'Details') ?>

				<?= notes_section($instance->getNotes()) ?>

				<?= usernotes_section($instance->getUserNotes()) ?>

				<?= $instance->footer() ?>

			</div>

		</section>

		<?= sidebar_section($instance->getSidebar()) ?>

	</div>

	<?php

	footer();

	return ob_get_clean();
}