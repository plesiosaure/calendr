<?php

	$api = $app->apiLoad('calendrierEvent');

	$data = $api->get(array(
		'debug' => false,
	#	'_id' => '51062016140ba0ec34000000'
	));

	foreach($data as $e){
		$d = $api->adopt($e);

		$app->pre($d->_id());
	}


die();

#	$api->pre($api->data(), $api->_id());

	die();

	$api    -> reset()
			-> debug(false)
		    -> set('name', 'Mon Event '.time())
			-> save()
			-> debug(true)
			-> set('user', '007')
			-> save();

	$arr = $api->toArray();
	$js  = $api->toJson();

	$api->pre($arr, $js);

