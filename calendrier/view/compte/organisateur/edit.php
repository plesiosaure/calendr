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

	<h1>Organisateur #<?php echo $myOrganisation['_id'] ?></h1>

	<form method="post" action="edit" class="check" novalidate="novalidate">
		<input type="hidden" name="action" value="action">

		<pre>
			Nom de l'organisateur
			<input name="name" value="<?php echo $myOrganisation['name'] ?>" data-required="true">

			Prénom
			<input name="firstname" value="<?php echo $myOrganisation['firstname'] ?>" data-required="true">

			Nom
			<input name="lastname" value="<?php echo $myOrganisation['lastname'] ?>" data-required="true">

			email
			<input name="email" value="<?php echo $myOrganisation['email'] ?>" data-type="email">

			Téléphone
			<input name="phone" value="<?php echo $myOrganisation['phone'] ?>">

			Mobile
			<input name="mobile" value="<?php echo $myOrganisation['mobile'] ?>">

			Fax
			<input name="fax" value="<?php echo $myOrganisation['fax'] ?>">

			Web
			<input name="web" value="<?php echo $myOrganisation['web'] ?>" data-type="urlstrict">


			<input type="submit" class="submit">
		</pre>

	</form>


	<?php
		$this->pre($myOrganisation);
	?>


<?php
	include(MYTHEME.'/ui/footer.php');
	include(MYTHEME.'/ui/html-end.php');
?>
</body></html>