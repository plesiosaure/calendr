<!DOCTYPE html>
<html lang="<?php echo LOC ?>">
<head>
	<?php include(MYTHEME.'/ui/html-head.php'); ?>
</head>
<body>
<?php include(MYTHEME.'/ui/header.php'); ?>

<div id="main" class="clearfix show-grid home">

	<div class="left clearfix" id="wizard">

		<h1>Suppression de la manifestation</h1>

		<form method="post" action="remove" id="form-edit">
			<input type="hidden" name="id" value="<?php echo $myManifestation['_id'] ?>">

			<?php

			$bloc = $this->apiLoad('content')->contentGet(array(
				'id_content' => 60113,
			));

			echo $bloc['field']['_description'];
			editBloc($bloc);

			?>

			<div class="step">
				<?php include __DIR__.'/includes/editor.php'; ?>
			</div>

			<div class="last is-visible">
				<?php

				$bloc = $this->apiLoad('content')->contentGet(array(
					'id_content' => 60111,
				));

				echo $bloc['field']['_description'];
				editBloc($bloc);

				?>

				<button type="submit" class="btn">Confirmer la suppression</button>
			</div>

		</form>

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