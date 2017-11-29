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

<?php include dirname(__DIR__) . '/ui/menu.php'; ?>

	<h1>Accepter le membre: <?php echo $myUser['field']['userPrenom'].' '.$myUser['field']['userNom'] ?></h1>

	<?php if(isset($_GET['done'])){ ?>

		<p>Cet utilisateur fait désormais partie des membres de l'organisateur:
			<a href="../../<?php echo $myOrganisation['_id'] ?>"><?php echo $myOrganisation['name'] ?></a></p>
		<p>Il peut créer des manifestations.</p>

	<?php }else{ ?>

		<form method="post" action="<?php echo $myUser['id'] ?>">
			<input type="hidden" name="confirmed" value="YES" />

			<a href="../../pick" class="btn btn-danger">Annuler</a>
			<input type="submit" class="btn btn-success" value="Confirmer" />
		</form>

	<?php } ?>

	<?php
	#	$this->pre($myOrganisation, $myUser);
	?>

<?php
	include(MYTHEME.'/ui/footer.php');
	include(MYTHEME.'/ui/html-end.php');
?>
</body></html>