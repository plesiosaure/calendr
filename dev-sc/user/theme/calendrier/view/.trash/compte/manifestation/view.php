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

<?php include dirname(__DIR__) . '/ui/menu.php'; ?>

	<h1>Manifestation #<?php echo $myManifestation['_id'] ?></h1>
	<a href="<?php echo $myManifestation['_id'] ?>/edit">Edit</a>
	<a href="?off=<?php echo $myManifestation['_id'] ?>">Annuler</a>

	<?php
		$this->pre($myManifestation);
	?>

	<h2>Organisateur #<?php echo $myOrganisation['_id'] ?></h2>

	<?php
		$this->pre($myOrganisation);
	?>


<?php
	include(MYTHEME . '/ui/footer.php');
	include(MYTHEME . '/ui/html-end.php');
?>
</body></html>