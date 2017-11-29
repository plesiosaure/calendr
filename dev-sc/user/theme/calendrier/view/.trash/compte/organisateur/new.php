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

	<h1>Créer un nouveau compte organisateur</h1>

	<form method="post" action="new" class="check" >
		<input type="hidden" name="action" value="action">

		<pre>
			Nom de l'organisateur
			<input name="name" value="<?php echo $myOrganisation['name'] ?>" required >

			Prénom
			<input name="firstname" value="<?php echo $myOrganisation['firstname'] ?>" required >

			Nom
			<input name="lastname" value="<?php echo $myOrganisation['lastname'] ?>" required >

			email
			<input name="email" value="<?php echo $myOrganisation['email'] ?>">

			Téléphone
			<input name="phone" value="<?php echo $myOrganisation['phone'] ?>">

			Mobile
			<input name="mobile" value="<?php echo $myOrganisation['mobile'] ?>">

			Fax
			<input name="fax" value="<?php echo $myOrganisation['fax'] ?>">

			Web
			<input name="web" value="<?php echo $myOrganisation['web'] ?>" >

			<input type="submit" class="submit">
		</pre>

	</form>

</div>

<?php
	include(MYTHEME . '/ui/footer.php');
	include(MYTHEME . '/ui/html-end.php');
?>

<script type="text/javascript" src="/media/ui/vendor/Parsley.js-2.0.0-rc2/dist/parsley.js"></script>

</body></html>