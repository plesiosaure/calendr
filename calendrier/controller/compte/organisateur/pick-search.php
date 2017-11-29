<?php

	header("Content-Type: application/json");

	$out = array();

	$api = $this->apiLoad('calendrierOrganisateur');
	$raw = $api->get(array(
		'search' => urldecode($_GET['q']),
		'format' => array(),
		'limit'  => 100
	));

	if(!empty($raw)){
		foreach($raw as $e){
			$out[] = array(
				'_id'  => $e['_id'],
				'name' => $e['name'],
			);
		}
	}


	echo $this->helperJsonBeautifier($this->helperJsonEncode($out));

