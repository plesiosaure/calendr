<?php

	$api = $this->apiLoad('calendrierCity');

	$data = $api->get(array(
		'search' => urldecode($_GET['q']),
		'limit'  => 100,
		'format' => array()
	));

	$tmp = array();
	foreach($data as $e){
		$tmp[] = $e;
	}

	header('Content-Type: plain/text');
	echo $this->helperJsonBeautifier($this->helperJsonEncode($tmp));
