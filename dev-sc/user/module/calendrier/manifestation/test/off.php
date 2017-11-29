<?php

	$api = $app->apiLoad('calendrierEvent');

	$data = $api->get(array(
		'debug' => false,
		'_id' => '51062084140ba0e536000000'
	));

	$api->debug(true)->off(array('undo' => true));

#	$app->pre($data);

