<?php

	$api = $app->apiLoad('calendrierGeocode');
	$data = $api->resumeNoGPS();

?><!DOCTYPE html>
<html lang="fr" xmlns="http://www.w3.org/1999/html">
<head>
	<base href="../" />
	<?php include(COREINC . '/head.php'); ?>
	<link rel="stylesheet" type="text/css" href="ui/css/all.css" />
</head>
<body>

<header><?php
	include(COREINC . '/top.php');
	include(dirname(__DIR__). '/ui/menu.php')
?></header>

<div id="app"><div class="row-fluid">

	<h2>GéoCache <?php echo $data['total'] ?></h2>
	<table cellpadding="0" cellspacing="0" width="100%" class="listing">
		<thead>
			<tr>
				<th>Addresse</th>
				<th width="20%">Raison</th>
				<th width="35%">Manifestation</th>
				<th width="10%">Désactivé</th>
			</tr>
		</thead>
		<tbody>
		<?php foreach($data['nogps'] as $e){ ?>
			<tr class="line">
				<td><?php echo $e['_id'] ?></td>
				<td><?php echo $e['_raw']['status'] ?: '-' ?></td>
				<td><?php
					$manifs = $e['manifs'];
					if(!empty($manifs)){
						foreach($manifs as $m){
							echo "- ".$m['name'].' (ID '.$m['id'].')<br>';
						}
					}
				?></td>
				<td><a class="btn btn-small remove-me" data-id="<?php echo $e['_id'] ?>">Supprimer de la cache</a></td>
			</tr>
		<?php } ?>
		</tbody>
	</table>

	<?php
	#	$app->pre($data);
	?>


</div></div>

<?php include(COREINC.'/end.php'); ?>

<script>

	$('.remove-me').click(function(){

		var _id = $(this).attr('data-id')
		, tr = $(this).parents('tr');

		$.ajax({
			url: 'gps/helper/remove',
			data: {
				_id: _id
			},
			type: 'post'
		}).done(function(){
			tr.remove();
		})
	});


</script>
</body>
</html>