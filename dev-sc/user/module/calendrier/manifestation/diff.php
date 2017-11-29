<?php

	if(!defined('COREINC')) die('Direct access not allowed');
	$api = $app->apiLoad('calendrierManifestation');

	// GET /////////////////////////////////////////////////////////////////////////////////////////////////////////////
	if($_REQUEST['_id'] != NULL){

		$data = $api->get(array(
			'debug'  => false,
			'_id'    => $_REQUEST['_id'],
			'format' => array('city', 'organisateur')
		));

		if(!array_key_exists('backup', $data)) $app->go('./?noBackupFoubnd='.$data['_id']);

		$mvsExists = false;
		if(!empty($data['id'])){
			$mvs = $app->apiLoad('calendrierMvsSync')->manifestationGet(array(
				'_id' => $data['_id']
			));

			if(!empty($mvs['data']['id_manifestation'])) $mvsExists = true;
		}

		$diff = $api->compareToBackup($data['_id']);
		$web  = $data;
		$bak  = $data['backup'];

	}else{
		$app->go('./?noDataFound='.$_REQUEST['_id']);
	}

	$fields = array(
		'name' => 'Nom'
	);

#  $this->apiLoad('calendrierMvsSync')->manifestationCreation(array('_id' => $api->_id()));

?><!DOCTYPE html>
<html lang="fr">
<head>
	<base href="../" />
	<?php include(COREINC . '/head.php'); ?>
	<link rel="stylesheet" type="text/css" href="../content/ui/css/data.css" />
	<link rel="stylesheet" type="text/css" href="ui/css/main.css" />
	<link rel="stylesheet" type="text/css" href="ui/css/all.css" />
	<style>
		.is-green .name{ color: #008000; }
		.is-red   .name{ color: red; }
	</style>
</head>
<body>

<header><?php
	include(COREINC.'/top.php');
	include(dirname(__DIR__) . '/ui/menu.php')
?></header>

<div class="inject-subnav-right hide">
</div>

<div id="app">

	<?php if(!$mvsExists){ ?>
	<div class="message messageWarning">
		Cette manifestation n'existe pas sur MVS
	</div>
	<?php } ?>

	<table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-bottom: 20px;">
		<tr>
			<td width="40%" style="text-align: right;"><a href="manifestation/diff-accept?_id=<?php echo $data['_id'] ?>" class="btn btn-success" style="color: #FFF;">Valider la version en ligne</a></td>
			<td width="20%" align="center">
				<a href="manifestation/data?_id=<?php echo $data['_id'] ?>" class="btn">Editer la manifestation</a>
			</td>
			<td width="40%" style="text-align: left;"><a href="manifestation/diff-reject?_id=<?php echo $data['_id'] ?>" class="btn btn-danger" style="color: #FFF;">Rejeter les modifications, revenir à la version sauvegardée</a></td>
		</tr>
	</table>

	<table border="0" cellpadding="0" cellspacing="0" width="100%" class="listing" style="margin-bottom: 20px;">
		<thead>
			<tr>
				<th width="40%" style="text-align: right;">Sur le site web</th>
				<th width="20%" style="text-align: center;">Propriété</th>
				<th width="40%">Version sauvegardée</th>
			</tr>
		</thead>
		<?php foreach($fields as $field => $name){
			$left  = $web[$field];
			$right = $bak[$field];
		?>
		<tr class="is-<?php echo ($left == $right) ? 'green' : 'red'; ?>">
			<td align="right"><?php echo $left; ?></td>
			<td class="name" align="center"><?php echo $name; ?></td>
			<td><?php echo $right; ?></td>
		</tr>
		<?php } ?>
	</table>


	<table border="0" cellpadding="0" cellspacing="0" width="100%" class="listing">
		<thead>
			<tr>
				<th width="40%" style="text-align: right;">Sur le site web</th>
				<th width="20%" style="text-align: center;">Dates</th>
				<th width="40%">Version sauvegardée</th>
			</tr>
		</thead>
		<?php foreach($diff['dates'] as $key => $both){
			$left  = $both['base'];     unset($left['start'], $left['end']);
			$right = $both['backup'];   unset($right['start'], $right['end']);
		?>
		<tr class="is-<?php echo ($both['diff']) ? 'red' : 'green'; ?>">
			<td align="right"><?php $app->pre($left); ?></td>
			<td class="name" align="center"><?php echo $key; ?></td>
			<td><?php echo $app->pre($right); ?></td>
		</tr>
		<?php } ?>
	</table>

	<?php $app->pre($diff); ?>

</div>

<?php include(COREINC.'/end.php'); ?>

</body>
</html>