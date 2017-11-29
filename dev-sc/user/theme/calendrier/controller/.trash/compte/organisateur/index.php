<?php

	// ORGANISATEUR ///////////////////////////////////////////////////////////////////////////////////////////////////
	// Liste des organisation ou je suis rattaché
	$myOrganisations = $this->apiLoad('calendrierOrganisateur')->get(array(
		'debug'  => false,
		'format' => array(),
		'member' => $me['id_user']
	));


