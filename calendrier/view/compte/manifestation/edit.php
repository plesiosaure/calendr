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

	<h1>Manifestation #<?php echo $myManifestation['_id'] ?></h1>
	<a href="date">Dates</a>

	<form method="post" action="edit">
		<input type="hidden" name="action" value="action">

		<pre>
			name
			<input name="name" value="<?php echo $myManifestation['name'] ?>">

			genre
			<select name="type" data-sel="<?php echo $myManifestation['mvs']['type'] ?>"></select>

			type
			<select name="category" data-sel="<?php echo $myManifestation['mvs']['category'] ?>"></select>

			région
			<select name="region" data-sel="<?php echo $myManifestation['geo']['region'] ?>"></select>

			département
			<select name="dep" data-sel="<?php echo $myManifestation['geo']['dept'] ?>"></select>



			<input type="submit">
		</pre>

	</form>




<?php
	include(MYTHEME.'/ui/footer.php');
	include(MYTHEME.'/ui/html-end.php');
?>
</body></html>