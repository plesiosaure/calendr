<?php

	if(!defined('COREINC')) die('Direct access not allowed');
	$api  = $app->apiLoad('calendrierLog');

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
	<pre><?php $api->replayGUI($_GET['_id']); ?></pre>
</div>

<?php include(COREINC.'/end.php'); ?>

</body>
</html>