<?php

	if(!isset($app)) include(__DIR__ . '/bootstrap.php');

	$api = $app->apiLoad('calendrierLog');

	// Trouver les LOGAPI qui ont échoué.
	$jobs   = $api->getRetryTask();
	$result = $api->replayAll($jobs);


	var_dump($result);