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

	<h1>Nouvelle manifestation</h1>

	<form method="post" action="new" class="check">
		<input type="hidden" name="action" value="action">

		<pre>
			name
			<input name="name" value="" required >

			Organisateur
			<select name="id_organisateur" required ><?php
				foreach($myOrganisations as $e){
					echo '<option value="'.$e['_id'].'">'.$e['name'].'</option>';
				}
			?></select>

			Genre
			<select name="type" data-sel="<?php echo $myManifestation['mvs']['type'] ?>" ></select>

			Type
			<select name="category" data-sel="<?php echo $myManifestation['mvs']['category'] ?>" ></select>

			<div class="invalid-form-error-message"></div>

			<input type="submit">
		</pre>

	</form>

</div>

<?php
	include(MYTHEME . '/ui/footer.php');
	include(MYTHEME . '/ui/html-end.php');
?>

<script src="/media/ui/vendor/Parsley.js-2.0.0-rc2/dist/parsley.js"></script>
<script src="/media/ui/js/compte/manifestation/form.js"></script>

</body></html>