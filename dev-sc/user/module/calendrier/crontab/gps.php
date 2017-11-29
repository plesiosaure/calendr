#!/usr/bin/php
<?php

	if(!isset($app)) include(__DIR__.'/bootstrap.php');

	logIt(date("Y-m-d H:i:s")."\n");

	// Extraire les addresse et les mettre en cache
	$now = microtime(true);
	logIt("Manifestation GEOCODE 1/3 ... ");
	$job = $app->apiLoad('calendrierMvs')->manifestationImportGeoCache();
	logIt($job['total'].' items ');
	logIt((microtime(true) - $now)." seconds\n");

	// Trouver la position GPS des adresse en cache qui n'en ont pas => google api
	$now = microtime(true);
	echo "Manifestation GEOCODE 2/3 ... ";
	$job = $app->apiLoad('calendrierMvs')->manifestationImportGeoCode(array('print' => false));
	echo $job['total'].' items ';
	echo (microtime(true) - $now)." seconds\n";

	// Remettre les GPS dans les manifestation d'après le cache
	$now = microtime(true);
	echo "Manifestation GEOCODE 3/3 ... ";
	$job = $app->apiLoad('calendrierMvs')->manifestationImportGeoSet();
	echo $job['total'].' items ';
	echo (microtime(true) - $now)." seconds\n";

