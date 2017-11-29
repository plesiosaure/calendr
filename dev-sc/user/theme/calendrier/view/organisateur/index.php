<!DOCTYPE html>
<html lang="<?php echo LOC ?>">
<head>
	<?php include(MYTHEME.'/ui/html-head.php'); ?>
</head>
<body>
<?php include(MYTHEME.'/ui/header.php'); ?>

<div id="main" class="clearfix show-grid home">

	<div class="left clearfix">

		<?php

		$bloc = $this->apiLoad('content')->contentGet(array(
			'id_content' => 60110,
		));

		?>

		<h1 class="gradient"><?php echo $bloc['field']['titreAlt'] ?: $bloc['contentName'] ?></h1>

		<div class="main-text">
			<?php echo $bloc['field']['_description']; editBloc($bloc); ?>
		</div>

		<?php include __DIR__.'/includes/form-search.php' ?>

	</div>

	<div class="right">
		<?php include __DIR__.'/../../ui/right/aide.php'; ?>
	</div>

</div>

<?php
	include(MYTHEME.'/ui/footer.php');
	include(MYTHEME.'/ui/html-end.php');
?>

<script>

	$(function(){

		$('.tip').tooltip();

		var search = $('#organisateurDatePicker').datepicker({
			format: 'dd.mm.yyyy',
			weekStart: 1
		}).on('changeDate', function(e) {
			if(e.viewMode == 'days'){
				$('.datepicker').css('display', 'none'); // Todo: fixer plus efficacement
			}
		});

	});

</script>

</body></html>