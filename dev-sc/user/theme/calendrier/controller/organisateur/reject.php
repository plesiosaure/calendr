<?php

	if(empty($_GET['_id'])) $this->go('/?notFound&from='.$_SERVER['REQUEST_URI']);

	$api = $this->apiLoad('calendrierManifestation');

	// DATA ////////////////////////////////////////////////////////////////////////////////////////////////////////////
	$myManifestation = $api->get(array(
		'_id'   => $_GET['_id'],
		'debug' => false
	));

	if(empty($myManifestation)) $this->go('/?notFound&from='.$_SERVER['REQUEST_URI']);

	// ACTION //////////////////////////////////////////////////////////////////////////////////////////////////////////
	$confirmed = false;

	if(!empty($myManifestation['temp'])){
		$this->apiLoad('calendrierManifestation')->manifestationEmailRejected($_GET['_id']);
		$confirmed = true;
	}

