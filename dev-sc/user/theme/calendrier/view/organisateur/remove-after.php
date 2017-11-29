<!DOCTYPE html>
<html lang="<?php echo LOC ?>">
<head>
	<?php include(MYTHEME.'/ui/html-head.php'); ?>
</head>
<body>
<?php include(MYTHEME.'/ui/header.php'); ?>

<div id="main" class="clearfix show-grid home">

	<div class="left clearfix">

		<h1 class="gradient">Suppression de la manifestation</h1>

		<?php

		$page = $this->apiLoad('content')->contentGet(array(
			'id_content' => 60114,
		));

	#	echo '<h1>'.($page['field']['titreAlt'] ?: $page['contentName']).'</h1>';
		echo $page['field']['_description'];
		editBloc($page);

		?>
	</div>

	<div class="right">
		<?php include __DIR__.'/../../ui/right/aide.php'; ?>
	</div>

</div>

<?php
	include(MYTHEME.'/ui/footer.php');
	include(MYTHEME.'/ui/html-end.php');
?>

<script src="/media/ui/vendor/Parsley.js-2.0.0-rc2/dist/parsley.js"></script>
<script src="/media/ui/js/organisateur/edit.min.js"></script>

</body></html>