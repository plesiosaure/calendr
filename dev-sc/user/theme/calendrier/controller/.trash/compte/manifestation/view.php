<?php

	// MANIFESTATION ///////////////////////////////////////////////////////////////////////////////////////////////////
	$myManifestation = $this->apiLoad('calendrierManifestation')->get(array(
		'debug'  => false,
		'format' => array(),
		'_id'    => $this->kodeine['get']['id_manifestation']
	));

	// ORGANISATEUR ///////////////////////////////////////////////////////////////////////////////////////////////////
	$myOrganisation = $this->apiLoad('calendrierOrganisateur')->get(array(
		'debug'  => false,
		'format' => array(),
		'_id'    => $myManifestation['organisateur']['_id']
	));




