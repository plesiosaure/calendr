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
	<a href="edit">Editer</a>

	<form method="post" action="date">
		<input type="hidden" name="action" value="action">

		<?php foreach($myDates as $i => $e){

			$start = date("Y-m-d", $e['start']);
			$end   = date("Y-m-d", $e['end']);
			$name  = 'date['.$e['start'].'][%s]';

		?>
		<pre>
			<?php echo '#'.$i."\n" ?>
			Début <input value="<?php echo $start ?>" readonly>
			Durée <input name="<?php printf($name, 'days') ?>" value="<?php echo $e['days'] ?>" type="number">
			<textarea name="<?php printf($name, 'comment') ?>"><?php echo $e['comment'] ?></textarea>
			Annulé      <input type="checkbox" name="<?php printf($name, 'canceled') ?>" value="YES" <?php if($e['canceled']) echo 'checked'; ?> >
			Reporté     <input type="checkbox" name="<?php printf($name, 'postponed') ?>" value="YES" <?php if($e['postponed']) echo 'checked'; ?> >
			Incertain   <input type="checkbox" name="<?php printf($name, 'unsure') ?>" value="YES" <?php if($e['unsure']) echo 'checked'; ?> >

			<?php #$this->pre($e); ?>
		</pre>
		<?php } ?>

		<pre>
			Nouvelle date
			<input value="" name="new[start]">
		</pre>

		<input type="submit">
	</form>




<?php
	include(MYTHEME.'/ui/footer.php');
	include(MYTHEME.'/ui/html-end.php');
?>
</body></html>