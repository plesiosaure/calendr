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

	<h1>Mon compte utilisateur</h1>
	<a href="login">Changer son login</a>
	<a href="passwd">Changer son mot de passe</a>

	<?php if(isset($_GET['done'])){ ?>
		<p>Mise à jour réussie</p>
	<?php } ?>


	<form method="post" action="profil" class="check" novalidate="novalidate">
		<input type="hidden" name="update" value="YES">

		<pre>
			Nom
			<input name="field[userNom]" value="<?php echo $me['field']['userNom'] ?>" data-required="true">

			Prénom
			<input name="field[userPrenom]" value="<?php echo $me['field']['userPrenom'] ?>" data-required="true">

			<input type="submit" class="submit">

		</pre>

	</form>

</div>


<?php
	include(MYTHEME.'/ui/footer.php');
	include(MYTHEME.'/ui/html-end.php');
?>
</body></html>