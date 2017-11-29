<?php

	$api  = $app->apiLoad('calendrierManifestationPeriode');
	$date = $app->apiLoad('calendrierManifestationDate');

	if(is_array($_POST['dates'])){
		sort($_POST['dates']);

		$period = $api->datesToMongoDays($_POST['dates']);

		foreach($period as $data){
			$date->push(array(
				'debug' => false,
				'_id'   => $_POST['_id'],
				'raw'   => $data
			));
		}
	}

	echo json_encode(array('ok' => true));
