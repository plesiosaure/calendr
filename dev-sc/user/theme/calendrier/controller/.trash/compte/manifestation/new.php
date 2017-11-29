<?php

	$api = $this->apiLoad('calendrierManifestation');

	// ORGANISATEUR ///////////////////////////////////////////////////////////////////////////////////////////////////
	$myOrganisations = $this->apiLoad('calendrierOrganisateur')->get(array(
		'debug'  => false,
		'format' => array(),
		'member' => $me['id_user']
	));

	$myOrganisations = array_values($myOrganisations);

	// DATA ////////////////////////////////////////////////////////////////////////////////////////////////////////////
	if($_POST['action']){

		$data = array(
			'name'             => trim($_POST['name']),
			'created'          => new MongoDate(),
			'updated'          => new MongoDate(),
			'mvs.type'         => $_POST['type'],
			'mvs.category'     => $_POST['category'],
			'organisateur._id' => ($_POST['id_organisateur'] ? : $myOrganisations[0]['_id']),
			'periodicity'      => 1,
			'backup'           => array() // Déclenche la modération
		);

		$api->set($data);

		if($api->valid()){
			$api->fake(false)->debug(false)->save();

			$api->postUpsert($api->_id());

			$this->go($api->_id().'/edit');
		}

	}
