<?php

	$api = $this->apiLoad('calendrierOrganisateur');

	// DATA ////////////////////////////////////////////////////////////////////////////////////////////////////////////
	if($_POST['action']){

		$data = array(
			'created'   => new MongoDate(),
			'updated'   => new MongoDate(),
			'name'      => trim($_POST['name']),
			'firstname' => trim($_POST['firstname']),
			'lastname'  => trim($_POST['lastname']),
			'email'     => trim($_POST['email']),
			'phone'     => trim($_POST['phone']),
			'mobile'    => trim($_POST['mobile']),
			'fax'       => trim($_POST['fax']),
			'web'       => trim($_POST['web']),
			'rubrique'  => 1, // ! Forcer à 1 pour voir
			'member'    => array(
				intval($me['id_user'])
			)
		);

		$api->set($data);

		if($api->valid()){
			$api->fake(false)->debug(false)->save();

			$api->apiLoad('calendrierMvsSync')->organisateurCreation(array(
				'_id' => $api->_id()
			));

			$this->go('../organisateur/');
		}

	}
