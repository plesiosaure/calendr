<?php

	$api = $this->apiLoad('calendrierManifestation');

	// MANIFESTATION ///////////////////////////////////////////////////////////////////////////////////////////////////
	$myManifestation = $this->apiLoad('calendrierManifestation')->get(array(
		'debug'  => false,
		'format' => array(),
		'_id'    => $this->kodeine['get']['id_manifestation']
	));

	if(empty($myManifestation)) $this->go('/?manifestationNotFound');

	// ORGANISATEUR ////////////////////////////////////////////////////////////////////////////////////////////////////
	$myOrganisation = $this->apiLoad('calendrierOrganisateur')->get(array(
		'debug'  => false,
		'format' => array(),
		'_id'    => $myManifestation['organisateur']['_id']
	));


	// DATA ////////////////////////////////////////////////////////////////////////////////////////////////////////////
	if($_POST['action']){

		$data = array(
			'name'              => trim($_POST['name'])
		);

		$api->_id($myManifestation['_id'])->set($data);

		if($api->valid()){
			$api->fake(false)->debug(false)->save();
		#   $this->apiLoad('calendrierMvsSync')->manifestationUpdate(array('_id' => $api->_id()));
			$this->go('edit');

		}else{
			$this->pre('pb de validation');
		}

	}
