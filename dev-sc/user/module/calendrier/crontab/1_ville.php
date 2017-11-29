#!/usr/bin/php
<?php

	if(!isset($app)) include(__DIR__.'/bootstrap.php');

	echo __FILE__.PHP_EOL;

// IMPORT MYSQL ////////////////////////////////////////////////////////////////////////////////////////////////////////

	$now = microtime(true);
	$script  = '/home/motoregister/sql/' . (($mode == 'dev') ? 'import_dev.sh' : 'import.sh');
	echo $script.PHP_EOL;
	logIt("Import MySQL... ");
	$app->helperPipeExec($script);
	logIt((microtime(true) - $now)." seconds\n");

// VILLE ///////////////////////////////////////////////////////////////////////////////////////////////////////////////
	$now = microtime(true);
	logIt("Ville... ");
	$job = $app->apiLoad('calendrierMvs')->villeImport(); // Reimport
	logIt($job['total'].' items ');
	logIt((microtime(true) - $now)." seconds \n");

