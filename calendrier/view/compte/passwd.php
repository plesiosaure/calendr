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

	<?php include __DIR__ . '/ui/menu.php'; ?>

	<h1>Changer son mot de passe</h1>
	<a href="profil">Retour au compte</a>

	<?php if(isset($_GET['done'])){ ?>
		<p>Mise à jour réussie</p>
	<?php }else{ ?>

		<form method="post" action="passwd" class="check" novalidate="novalidate">
			<input type="hidden" name="update" value="YES">

			Le nouveau mot de passe doit faire au minimum 6 caractères, ne peut être vide

			<pre>
				Nouveau mot de passe
				<input name="new" type="password" data-required="true" data-notblank="true" data-minlength="6" id="a">

				Confirmation
				<input name="con" type="password" data-required="true" data-equalTo="#a">

				<input type="submit" class="submit">

			</pre>

		</form>

	<?php } ?>

</div>


<?php
	include(MYTHEME.'/ui/footer.php');
	include(MYTHEME.'/ui/html-end.php');
?>
</body></html>