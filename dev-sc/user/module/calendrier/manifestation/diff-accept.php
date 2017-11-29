<?php

	if(!defined('COREINC')) die('Direct access not allowed');
	$api = $app->apiLoad('calendrierManifestation');

	// ACTION //////////////////////////////////////////////////////////////////////////////////////////////////////////
	if(isset($_GET['confirm'])){
		$api->moderationAccept($_GET['_id']);
		$app->go('./');
	}

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

	}else{
		$app->go('./?noDataFound='.$_REQUEST['_id']);
	}

?><!DOCTYPE html>
<html lang="fr">
<head>
	<base href="../" />
	<?php include(COREINC . '/head.php'); ?>
	<link rel="stylesheet" type="text/css" href="../content/ui/css/data.css" />
	<link rel="stylesheet" type="text/css" href="ui/css/main.css" />
	<link rel="stylesheet" type="text/css" href="ui/css/all.css" />
</head>
<body>

<header><?php
	include(COREINC.'/top.php');
	include(dirname(__DIR__) . '/ui/menu.php')
?></header>

<div class="inject-subnav-right hide">
</div>

<div id="app">
	<div class="wrapper">

		<h1 style="float: none;">Accepter les modifications</h1>

		<?php if($mvsExists){ ?>
		<p>Cette manifestation existe sur MVS, elle sera mis à jour</p>
		<?php }else{ ?>
		<p>Cette manifestation n'existe pas sur MVS, elle sera ajouter</p>
		<?php } ?>

		<a href="manifestation/diff-accept?_id=<?php echo $data['_id'] ?>&confirm" class="btn btn-success" style="color: #FFF;">Accepter cette manifestation</a>

	</div>
</div>

<?php include(COREINC.'/end.php'); ?>

</body>
</html>