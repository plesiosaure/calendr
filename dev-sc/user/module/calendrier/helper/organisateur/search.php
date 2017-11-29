<?php

	$q = urldecode($_GET['q']);

	$organisateur = $app->apiLoad('calendrierOrganisateur')->get(array(
		'debug'    => false,
		'search'   => $q,
		'rubrique' => $_GET['rubrique'],
		'format'   => array()
	));

	$organisateur = array_values($organisateur);

	echo $app->helperJsonBeautifier($app->helperJsonEncode($organisateur));
