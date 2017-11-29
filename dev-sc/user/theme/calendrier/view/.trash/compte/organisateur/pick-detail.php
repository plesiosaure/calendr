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

	<?php if(isset($_GET['done'])){ ?>

		<p>Votre demande est en cours de modération, vous recevrez un mail </p>

	<?php }else{ ?>
		<p>Demande de rattachement à cet organisateur</p>

		<form method="post" action="<?php echo $myOrganisation['_id'] ?>">
			<input type="hidden" name="confirmed" value="YES" />

			<a href="../../pick" class="btn btn-danger">Annuler</a>
			<input type="submit" class="btn btn-success" value="Confirmer" />
		</form>

	<?php } ?>



	<?php
	#	$this->pre($myOrganisation);
	?>

<?php
	include(MYTHEME . '/ui/footer.php');
	include(MYTHEME . '/ui/html-end.php');
?>
</body></html>