<?php

	if(!defined('COREINC')) die('Direct access not allowed');
	$api = $app->apiLoad('calendrierManifestation');

	// ACTION //////////////////////////////////////////////////////////////////////////////////////////////////////////
	if(count($_POST) > 0){
		$api->moderationReject($_GET['_id'], array(
			'message' => $_POST['message']
		));
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


#  $this->apiLoad('calendrierMvsSync')->manifestationCreation(array('_id' => $api->_id()));

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

		<h1 style="float: none;">Rejeter les modifications</h1>

		<form method="post" action="manifestation/diff-reject?_id=<?php echo $data['_id'] ?>">


			<?php if($mvsExists){ ?>
			<p>Cette manifestation existe sur MVS</p>
			<?php }else{ ?>
			<p>Cette manifestation n'existe pas sur MVS, elle sera supprimé définitivement</p>
			<?php } ?>

			<p>Message supplémentaire</p>
			<textarea name="message" cols="60" rows="10"></textarea>

			<br />
			<button type="submit" class="btn btn-danger" style="color: #FFF;">Confirmer le rejet</button>
		</form>

	</div>
</div>

<?php include(COREINC.'/end.php'); ?>

</body>
</html>