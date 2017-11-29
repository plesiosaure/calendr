<!DOCTYPE html> 
<html lang="<?php echo LOC ?>">
<head>
	<title></title>
	<meta name="description" content="<?php echo HTML_DESCRIPTION ?>" />
	<meta name="keywords" content="<?php echo HTML_KEYWORDS ?>" />
	<?php include(MYTHEME . '/ui/html-head.php'); ?>
</head>
<body>
<?php include(MYTHEME . '/ui/header.php'); ?>

<div id="main" class="clearfix">

	<?php include __DIR__ . '/ui/menu.php'; ?>

	<h2>Résumé du compte</h2>
	<p>...</p>

<?php

	include(MYTHEME . '/ui/footer.php');
	include(MYTHEME . '/ui/html-end.php');

?></body></html>