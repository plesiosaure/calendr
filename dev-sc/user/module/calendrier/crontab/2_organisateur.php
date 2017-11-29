#!/usr/bin/php
<?php

	if(!isset($app)) include(__DIR__.'/bootstrap.php');

	echo __FILE__.PHP_EOL;

// ORGANISATEUR ////////////////////////////////////////////////////////////////////////////////////////////////////////

	$now = microtime(true);
	logIt("Organisateur  DATA ... ");
	$job = $app->apiLoad('calendrierMvs')->organisateurImport(array()); // Import upsert
	logIt($job['total'].' items ');
	logIt((microtime(true) - $now)." seconds \n");
