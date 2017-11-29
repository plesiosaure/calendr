<?php

	if(!defined('COREINC')) die('Direct access not allowed');
	$api  = $app->apiLoad('calendrierLog');

	$data = $api->get(array(
		'_id' => $_GET['_id']
	));

?><!DOCTYPE html>
<html lang="fr">
<head>
	<base href="../" />
	<?php include(COREINC . '/head.php'); ?>
	<link rel="stylesheet" type="text/css" href="ui/css/main.css" />
	<link rel="stylesheet" type="text/css" href="ui/css/all.css" />
</head>
<body>

<header><?php
	include(COREINC . '/top.php');
	include(dirname(__DIR__). '/ui/menu.php')
?></header>

<div id="app">

	<div style="text-align: center">
		Appel depuis <code><?php echo $data['from_domain'] ?></code>
		vers <code><?php echo $data['host'] ?></code
	</div>


	<?php $app->pre($data); ?>
</div>

<?php include(COREINC.'/end.php'); ?>

</body>
</html>