<?php

	$api = $this->apiLoad('calendrierOrganisateur');

	// ORGANISATEUR ////////////////////////////////////////////////////////////////////////////////////////////////////
	$myOrganisation = $this->apiLoad('calendrierOrganisateur')->get(array(
		'debug'  => false,
		'format' => array(),
		'_id'    => $this->kodeine['get']['id_organisateur']
	));

	if(empty($myOrganisation)) $this->go('/?organisateurNotFound');


	// DATA ////////////////////////////////////////////////////////////////////////////////////////////////////////////
	if($_POST['action']){

		$data = array(
			'name'      => trim($_POST['name']),
			'firstname' => trim($_POST['firstname']),
			'lastname'  => trim($_POST['lastname']),
			'email'     => trim($_POST['email']),
			'phone'     => trim($_POST['phone']),
			'mobile'    => trim($_POST['mobile']),
			'fax'       => trim($_POST['fax']),
			'web'       => trim($_POST['web'])
		);

		$api->_id($myOrganisation['_id'])->set($data);

		if($api->valid()){
			$api->fake(false)->debug(false)->save();

			$this->apiLoad('calendrierMvsSync')->organisateurUpdate(array(
				'_id' => $api->_id()
			));

			$this->go('edit');
		}else{
			$this->go('edit?validatationFailed');
		}

	}
