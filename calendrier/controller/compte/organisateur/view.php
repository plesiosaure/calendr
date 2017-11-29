<?php

	// ORGANISATEUR ///////////////////////////////////////////////////////////////////////////////////////////////////

	$myOrganisation = $this->apiLoad('calendrierOrganisateur')->get(array(
		'debug'  => false,
		'format' => array(),
		'_id'    => $this->kodeine['get']['id_organisateur']
	));




