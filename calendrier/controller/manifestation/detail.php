<?php

	$api = $this->apiLoad('calendrierManifestation');

	// FIX GPS DATA/////////////////////////////////////////////////////////////////////////////////////////////////////
	if(isset($_GET['geofix'])){
		$api->fixGeo(array(
			'_id' => $this->kodeine['get']['id_manifestation']
		));

		$this->go($this->kodeine['get']['id_manifestation']);
	}


	// DATA ////////////////////////////////////////////////////////////////////////////////////////////////////////////

	$myManifestation = $api->get(array(
		'debug'  => false,
		'_id'    => $this->kodeine['get']['id_manifestation'],
		'format' => array('orderDate')
	));

	if(empty($myManifestation)) $this->go('/?manifestationNotFound='.$this->kodeine['get']['id_manifestation']);


	// FORMAT //////////////////////////////////////////////////////////////////////////////////////////////////////////

	$myNearestDate = $this->apiLoad('calendrierManifestationDate')->nearest($myManifestation['date']);

	$myType = $this->apiLoad('calendrierManifestationType')->nameFromId($myManifestation['mvs']['type']);

	$myCategory = $this->apiLoad('calendrierManifestationType')->typeFromId($myManifestation['mvs']['category']);

	$myRegion = $this->apiLoad('calendrierDepartement')->regionGet(array(
		'dep' => $myManifestation['city']['dep']
	));

	$myDepartement = $this->apiLoad('calendrierDepartement')->departementGet(array(
		'code' => $myManifestation['city']['dep']
	));


