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

	<h1>Rejet du membre <?php echo $myUser['field']['userPrenom'].' '.$myUser['field']['userNom'] ?></h1>

	<?php if(isset($_GET['done'])){ ?>

		<p>Cet utilisateur ne fait pas parti de la liste des membre de cet organisateur</p>
		<p>Il pourra de nouveau en fait la demande</p>
		<p><a href="../<?php echo $myOrganisation['_id'] ?>/member">Afficher la liste des membre</a></p>

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