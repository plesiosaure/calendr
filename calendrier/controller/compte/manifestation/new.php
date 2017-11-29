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
			'name'              => trim($_POST['name']),
			'created'           => new MongoDate(),
			'updated'           => new MongoDate(),
			'organisateur._id'  => ($_POST['id_organisateur'] ?: $myOrganisations[0]['_id'])
		);

		$api->set($data);

		if($api->valid()){
			$api->fake(false)->debug(false)->save();
		#   $this->apiLoad('calendrierMvsSync')->manifestationCreation(array('_id' => $api->_id()));
			$this->go($api->_id().'/edit');

		}else{
			$this->pre('pb de validation');
		}

	}
