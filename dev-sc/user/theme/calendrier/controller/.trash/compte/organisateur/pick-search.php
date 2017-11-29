<?php

	header("Content-Type: application/json");

	$out = array();

	$api = $this->apiLoad('calendrierOrganisateur');
	$raw = $api->get(array(
		'debug'     => false,
		'search'    => urldecode($_GET['q']),
		'notMember' => array(1),
		'format'    => array(),
		'limit'     => 100
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

