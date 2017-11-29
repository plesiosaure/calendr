<?php

	$api  = $app->apiLoad('calendrierManifestationPeriode');

	if($_GET['from'] != '') $api->start($_GET['from'].'-01');
	if($_GET['end']  != '') $api->end($_GET['end'].'-31');

	$data = $api->labelToDates($_GET['label']);


	echo $app->helperJsonBeautifier($app->helperJsonEncode($data));



