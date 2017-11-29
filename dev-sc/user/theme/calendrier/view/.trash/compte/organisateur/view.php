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

	<h1>Organisateur #<?php echo $myOrganisation['_id'] ?></h1>
	<a href="<?php echo $myOrganisation['_id'] ?>/edit">Editer</a>
	<a href="<?php echo $myOrganisation['_id'] ?>/member">Membres</a>

	<?php
	#	$this->pre($myOrganisation);
	?>

<?php
	include(MYTHEME . '/ui/footer.php');
	include(MYTHEME . '/ui/html-end.php');
?>
</body></html>