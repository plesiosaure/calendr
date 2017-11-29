<!DOCTYPE html>
<html lang="<?php echo LOC ?>">
<head>
	<?php include(MYTHEME.'/ui/html-head.php'); ?>
</head>
<body>
<?php include(MYTHEME.'/ui/header.php'); ?>

<div id="main" class="clearfix show-grid home">

	<div class="left clearfix">

		<h1 class="gradient">Annulation</h1>

		<?php if($confirmed){

			$page = $this->apiLoad('content')->contentGet(array(
				'id_content' => 60117,
			));

			echo $page['field']['_description'];
			editBloc($page);

		}else{ ?>
			<p>Cette manifestation a déjà été modéré.</p>
		<?php } ?>

	</div>

	<div class="right">
		<?php include __DIR__.'/../../ui/right/aide.php'; ?>
	</div>

</div>

<?php
	include(MYTHEME.'/ui/footer.php');
	include(MYTHEME.'/ui/html-end.php');
?>

</body></html>