<?php

	$api = $this->apiLoad('calendrierManifestation');

	// FIX GPS DATA/////////////////////////////////////////////////////////////////////////////////////////////////////
	if(isset($_GET['geofix'])){
		$api->fixGeo(array(
			'_id' => $this->kodeine['get']['id_manifestation']
		));

		die('erri url ');
		$this->go($this->kodeine['get']['id_manifestation']);
	}


	// DATA ////////////////////////////////////////////////////////////////////////////////////////////////////////////
	$args = array(
		'debug'  => false,
		'format' => array('orderDate')
	);

	if($this->kodeine['get']['id_type'] == 'mvs'){
		$args['id'] = $this->kodeine['get']['id_manifestation'];
	}else
	if($this->kodeine['get']['id_type'] == 'mongo'){
		$args['_id'] = $this->kodeine['get']['id_manifestation'];
	}

	$myManifestation = $api->get($args);

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

	$myPoster = false;
	$myImages = array();

	if(!empty($myManifestation['images'])){
		$myImages =  array_filter($myManifestation['images'], function($e){
			if(!$e['poster']) return $e;
		});

		$myPoster = array_filter($myManifestation['images'], function($e){
			if($e['poster']) return $e;
		});

		if(!empty($myPoster)){
			$myPoster = array_values($myPoster);
			$myPoster = $myPoster[0]['url'];
		}else{
			unset($myPoster);
		}
	}

	if($myManifestation['mvs']['type'] == 1) {
		$myTypeImage = 'collection';
	}else
	if($myManifestation['mvs']['type'] == 2) {
		$myTypeImage = 'auto';
	}else
	if($myManifestation['mvs']['type'] == 3) {
		$myTypeImage = 'moto';
	}



