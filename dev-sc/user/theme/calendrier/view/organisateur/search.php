<!DOCTYPE html>
<html lang="<?php echo LOC ?>">
<head>
	<?php include(MYTHEME.'/ui/html-head.php'); ?>
</head>
<body>
<?php include(MYTHEME.'/ui/header.php'); ?>

<div id="main" class="clearfix show-grid home">

	<div class="left clearfix">

		<h1 class="gradient">Recherche de manifestations</h1>

		<?php include __DIR__.'/includes/form-search.php'; ?>

		<div class="orga-search-resume">
			<div class="total">
				Nombre de manifestations trouvées: <?php echo $myTotal ?>
			</div>
			<div class="suggest">
			</div>
		</div>

		<?php if(isset($myData)){

			$dir = ($_GET['sort'] == 1) ? -1 : 1;

			function filter($myPattern, $f, $d){
				echo sprintf($myPattern, 0);
				echo '&order='.$f.'&sort='.$d;
			}

		?>

		<table width="100%" border="0" class="orga-search-result">
			<thead>
				<tr>
					<th><a href="<?php filter($myPattern, 'name', $dir) ?>">Titre</a></th>
					<th><a href="<?php filter($myPattern, 'mvs.type', $dir) ?>">Type</a></th>
					<th><a href="<?php filter($myPattern, 'city.name', $dir) ?>">Ville</a></th>
					<th><a href="<?php filter($myPattern, 'city.dep', $dir) ?>">Dépt.</a></th>
					<th width="10%"><a href="<?php filter($myPattern, 'date', $dir) ?>">Date</a></th>
					<th width="7%">Action</th>
				</tr>
			</thead>
			<tbody>
			<?php foreach($myData as $e){

				$type = $apiType->nameFromId($e['mvs']['type']);
				$nearest = $apiDate->nearest($e['date']);

				?>
				<tr class="<?php $o = ($o == 'odd' ? '' : 'odd'); echo $o; ?>">
					<td><?php echo $e['name']; ?></td>
					<td><?php echo $type['name']; ?></td>
					<td><?php echo $e['city']['name']; ?></td>
					<td><?php echo $e['city']['dep']; ?></td>
					<td><?php echo date('d/m/Y', $nearest['start']->sec); ?></td>
					<td>
						<a data-toggle="tooltip" class="tip" title="Afficher la manifestation" href="<?php echo $api->manifestationPermalink($e); ?>" target="_blank" ><i class="icon-eye-open"></i></a>
						<a data-toggle="tooltip" class="tip" title="Modifier les infos" href="/organisateur/edit?id=<?php echo $e['_id'] ?>"><i class="icon-pencil"></i></a>
						<a data-toggle="tooltip" class="tip" title="Demander une suppression" href="/organisateur/remove?id=<?php echo $e['_id'] ?>"><i class="icon-trash"></i></a>
					</td>
				</tr>
			<?php } ?>
			</tbody>
		</table>

		<?php
			if($myTotal > $myLimit){
				echo $this->apiLoad('calendrierHelper')->pagination(array(
					'total'   => $myTotal,
					'limit'   => $myLimit,
					'offset'  => $myOffset,
					'pattern' => $myPattern,
					'size'    => 6
				));
			}
		?>
		<?php } ?>

	</div>

	<div class="right">
		<?php include __DIR__.'/../../ui/right/aide.php'; ?>
		<div class="block block-bordered">
			<div class="title medium">Votre manifestation n'est pas dans la liste ?</div>
			<p><a href="create" class="btn btn-small">créer une nouvelle manifestation</a></p>
		</div>
	</div>

</div>

<?php
	include(MYTHEME.'/ui/footer.php');
	include(MYTHEME.'/ui/html-end.php');
?>

<script>

$(function(){

	$('.tip').tooltip()

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