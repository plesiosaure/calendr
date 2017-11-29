<?php

	$q = urldecode($_GET['q']);

	$city = $app->apiLoad('calendrierCity')->get(array(
		'debug'  => false,
		'search' => $q,
		'format' => array()
	));

	$city = array_values($city);

	echo $app->helperJsonBeautifier($app->helperJsonEncode($city));
