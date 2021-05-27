<?php

function head ($args = []) {
/*
	$args = [
		'title'        => 'Title',
		'links'        => [
			'Inactive' => '#',
			'Active'   => ['url' => '#', 'class' => 'active'],
		],
		'notification' => '<a href="#">Heads up!</a>',
		'message'      => '',
	];
*/

$sectionId = isset($GLOBALS['rd']) ? $GLOBALS['rd']->getSectionID() : NULL;
$relPath = \Urls::getPrefix();
$docUrl = $sectionId ? \Urls::getDocUrl() : NULL;
$docsUrl = \Urls::getDocsUrl();

?><!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?= $args['title'] ?? '' ?></title>
	<link rel="preconnect" href="https://fonts.gstatic.com">
	<link href="https://fonts.googleapis.com/css2?family=Fira+Mono:wght@400;700&family=Fira+Sans:ital,wght@0,400;0,500;0,700;1,400;1,500&display=swap" rel="stylesheet">
	<style type="text/css">
		<?= file_get_contents(__DIR__ . '/../../assets/css/theme-base.css'); ?>
		<?= file_get_contents(__DIR__ . '/../../assets/css/theme-medium.css'); ?>
	</style>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" integrity="sha512-nMNlpuaDPrqlEls3IX/Q56H36qvBASwb3ipuo3MxeWbsQB1881ox0cRv7UPTgBlriqoynt35KjEwgGUeUXIPnw==" crossorigin="anonymous" referrerpolicy="no-referrer"/>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js" integrity="sha512-2ImtlRlf2VVmiGZsjm9bEyhjGW4dU7B6TNwh/hx/iSByxNENtj3WVE6o/9Lj4TJeVXPi4bnOIMXFIJJAeufa0A==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
	<script type="application/javascript">
		window.sectionId = <?= json_encode($sectionId) ?>;
		window.relPath = "<?= $relPath ?>";
		window.docsUrl = "<?= $docsUrl ?>";
		window.docUrl = "<?= $docUrl ?>";
		<?= file_get_contents(__DIR__ . '/../../assets/js/main.js'); ?>
	</script>
</head>
<body class="docs">
<nav id="head-nav" class="navbar navbar-fixed-top">
	<div class="navbar-inner clearfix">
		<a href="<?= $relPath ?>/" class="brand">Home</a>
		<div id="mainmenu-toggle-overlay">
			<div class="hamburger"></div>
		</div>
		<input type="checkbox" id="mainmenu-toggle">
		<?php if (!empty($args['links'])) : ?>
			<ul class="nav">
				<?php foreach ($args['links'] as $text => $link) :
					$link = !is_array($link) ? ['url' => $link] : $link; ?>
					<li class="<?= $link['class'] ?? '' ?>"><a href="<?= $link['url'] ?>"><?= $text ?></a></li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
		<form class="navbar-search" id="topsearch" action="<?= $relPath ?>/search.php">
			<input type="hidden" name="show" value="quickref">
			<input type="hidden" name="pattern" class="search-query" placeholder="Search" accesskey="s">
			<select class="select2-ajax-data search-query"></select>
		</form>
	</div>
	<?php if (!empty($args['message'])) : ?>
		<div id="flash-message"><?= $args['message'] ?></div>
	<?php endif; ?>
</nav>
<?php if (!empty($args['notification'])) : ?>
<div class="headsup"><?= $args['notification'] ?></div>
<?php
endif;
}