<?php

	$api = $this->apiLoad('calendrierManifestation');

	// ACTIONS /////////////////////////////////////////////////////////////////////////////////////////////////////////
	if(isset($_GET['off'])){
		$api->off(array(
			'_id' => $_GET['off']
		));
		$this->go('./');
	}



	// ORGANISATEUR ////////////////////////////////////////////////////////////////////////////////////////////////////
	$myOrganisations = $this->apiLoad('calendrierOrganisateur')->get(array(
		'debug'  => false,
		'format' => array(),
		'member' => $me['id_user']
	));

	$myIdOrganisations = array_keys($myOrganisations);


	// MANIFESTATION de mes ORGANISATIONS //////////////////////////////////////////////////////////////////////////////
	$myManifestations = $this->apiLoad('calendrierManifestation')->get(array(
		'debug'            => false,
		'format'           => array(),
		'organisateur._id' => array('$in' => $myIdOrganisations)
	));


