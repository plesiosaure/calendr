<?php

	$api = $app->apiLoad('calendrierEvent');


	$start  = new MongoDate(strtotime("2012-01-15 00:00:00"));
	$end    = new MongoDate(strtotime("2012-02-15 00:00:00"));


	$app->pre(

		$api->get(array(
			'range'     => array('start' => $start, 'end' => $end),
			'search'    => 'ann'
		))
	);
