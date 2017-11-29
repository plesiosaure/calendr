<?php

	$api = $app->apiLoad('calendrierEvent');

	$data = $api->get(array(
		'debug' => false,
		'_id' => '51483e3f8f7b14d17f000000'
	));



	$api->debug(true)->del();

#	$app->pre($data);

