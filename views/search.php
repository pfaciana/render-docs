<?php

function searchPage ()
{
	global $rd;

	if (isset($rd)) {
		$name      = $rd->getMeta('name');
		$desc      = $rd->getMeta('description');
		$sectionId = $rd->getSectionID();
	}
	else {
		$name      = 'All';
		$desc      = '';
		$sectionId = NULL;
	}

	$query   = filter_input_array(INPUT_GET);
	$results = (new Search($query['q'] ?? '', $sectionId ?? NULL))->getSearchResults();

	$title = $sectionId ? "Searched \"{$query['q']}\" in \"{$name}\"" : "Searched for \"{$query['q']}\" in projects";

	ob_start();

	head(['title' => 'Search "' . $query['q'] . '"- RenderDocs']);

	?>

	<?= breadcrumbs(['links' => ['All Documentation' => \Urls::getDocsUrl()]]) ?>

	<div id="layout" class="clearfix">

		<section id="layout-content">

			<div class="refentry">

				<?= name_section(['name' => $title]) ?>

				<div class="classsynopsis">

					<div class="refsect1 parameters" id="refsect1-parameters">
						<dl>
							<?php foreach ($results as $result) : ?>
								<dt>
									<a href="<?= Search::getUrl($result['id'], $result['refs'] ?? []) ?>"><code class="parameter"><?= $result['text'] ?></code></a>
								</dt>
								<dd>
									<?= $result['desc'] ?>
								</dd>
							<?php endforeach; ?>
						</dl>
					</div>

				</div>

			</div>

		</section>

	</div>

	<?php

	footer();

	return ob_get_clean();
}