<?php

	$api = $app->apiLoad('calendrierEvent');

	$data = $api->get(array(
		'debug' => true,
		'type'  => array(
			'auto'  => array('course')
		)
	));


	$app->pre($data);
