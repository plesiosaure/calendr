<?php

	header("HTTP/1.0 404 Not Found");

?><!DOCTYPE html>
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

	<div class="left clearfix">
		<h1 class="gradient">Page introuvable !</h1>

		<div class="main-text">
			<p>La page a généré une erreur 404, ce qui signifit qu'elle n'existe plus ou qu'elle n'a jamais exité.</p>
			<p>Vous pouvez, <a href="/">revenir à la page d'accueil</a> ou bien effectuer une recherche depuis le formulaire ci-contre.</p>
		</div>

	</div>

	<div class="right"><?php
		include(MYTHEME.'/ui/right/search.php');
		include(MYTHEME.'/ui/right/ad.php');
		include(MYTHEME.'/ui/right/actu.php');
	?></div>

</div>

<?php
	include(MYTHEME.'/ui/footer.php');
	include(MYTHEME.'/ui/html-end.php');
?>

</body></html>