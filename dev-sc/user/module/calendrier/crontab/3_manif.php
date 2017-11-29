#!/usr/bin/php
<?php

	$type = $argv[2] ?: 1;

	if(!isset($app)) include(__DIR__.'/bootstrap.php');


	// Date => Mongo
	if($type == '1'){
		$now = microtime(true);
		logIt("Manifestation DATES ... ");
		$job = $app->apiLoad('calendrierMvs')->manifestationImportDate(); // Empty + Import
		logIt($job['total'].' items ');
		logIt((microtime(true) - $now)." seconds\n");
	}

	// Manifestation
	$now = microtime(true);
	logIt("Manifestation DATA ... ");
	$job = $app->apiLoad('calendrierMvs')->manifestationImport(array('type' => $type)); // Import update mvs.id + mvs.type
	logIt($job['total'].' items ');
	logIt((microtime(true) - $now)." seconds\n");


	// Fix des manifestations qui ne sont passé
	/*$now = microtime(true);
	logIt("Manifestation Post script ... ");
	$job = $app->apiLoad('calendrierMvs')->manifestationImportPostScript();
	logIt((microtime(true) - $now)." seconds\n");*/

