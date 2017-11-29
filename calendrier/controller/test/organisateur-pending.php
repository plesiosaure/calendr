<?php


	$api = $this->apiLoad('calendrierOrganisateur');
	$_id = '5243e7a08f7b14842100f5fd';

	$this->pre(
		"AVANT",
		$api->get(array('_id' => $_id))
	);

	$d = $api->pending(array(
		'_id'     => $_id,
		'id_user' => '1'
	));

	$this->pre(
		"APRES",
		$api->get(array('_id' => $_id))
	);



