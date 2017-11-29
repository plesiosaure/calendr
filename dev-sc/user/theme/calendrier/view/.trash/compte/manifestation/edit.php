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

	<h1>Manifestation #<?php echo $myManifestation['_id'] ?></h1>
	<a href="date">Dates</a>

	<form method="post" action="edit" class="check">
		<input type="hidden" name="action" value="action">

		<pre>
			name
			<input name="name" value="<?php echo $myManifestation['name'] ?>" required>

			<input type="submit">
		</pre>

	</form>

</div>


<?php
	include(MYTHEME . '/ui/footer.php');
	include(MYTHEME . '/ui/html-end.php');
?>

<script type="text/javascript" src="/media/ui/vendor/Parsley.js-2.0.0-rc2/dist/parsley.js"></script>

</body></html>