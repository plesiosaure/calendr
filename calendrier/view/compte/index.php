<!DOCTYPE html> 
<html lang="<?php echo LOC ?>">
<head>
	<title></title>
	<meta name="description" content="<?php echo HTML_DESCRIPTION ?>" />
	<meta name="keywords" content="<?php echo HTML_KEYWORDS ?>" />
	<?php include(MYTHEME.'/ui/html-head.php'); ?>
</head>
<body>
<?php include(MYTHEME.'/ui/header.php'); ?>

<div id="main" class="clearfix">

	<?php include __DIR__.'/ui/menu.php'; ?>

	<h1>Mon compte</h1>

	<h2>Résumé du compte</h2>
	<p>...</p>

	<h2>Mes manifs</h2>
	<p><a href="manifestation/">aller voir ici</a></p>

	<h2>Membre des organisations</h2>
	<ul><?php
		foreach($myOrganisations as $e){
			echo '<li><a href="/compte/organisateur/'.$e['_id'].'">'.$e['name'].'</a></li>';
		}
	?></ul>

	<a href="organisateur/pick">Nouvelle</a>


<?php

	include(MYTHEME.'/ui/footer.php');
	include(MYTHEME.'/ui/html-end.php');

?></body></html>