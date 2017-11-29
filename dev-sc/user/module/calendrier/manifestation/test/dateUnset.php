<?php

	$api = $app->apiLoad('calendrierEvent');

	$data = $api->dateUnset(array(
		'debug' => true,
		'_id'   => '51062084140ba0e536000000',
		'date'  => '2010-01-01'
	));

