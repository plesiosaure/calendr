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

	<h1>Nouvelle manifestation</h1>

	<form method="post" action="new">
		<input type="hidden" name="action" value="action">

	<pre>
		name
		<input name="name">

		Organisateur
		<select name="id_organisateur"><?php
			foreach($myOrganisations as $e){
				echo '<option value="'.$e['_id'].'">'.$e['name'].'</option>';
			}
		?></select>

		<input type="submit">
	</pre>

	</form>




<?php
	include(MYTHEME.'/ui/footer.php');
	include(MYTHEME.'/ui/html-end.php');
?>
</body></html>