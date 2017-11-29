<?php

	$api     = $this->apiLoad('calendrierManifestation');
	$apiDate = $this->apiLoad('calendrierManifestationDate');
	$apiType = $this->apiLoad('calendrierManifestationType');

	// SEARCH //////////////////////////////////////////////////////////////////////////////////////////////////////////

	$myLimit	= 25;
	$myOffset	= isset($_GET['p']) ? ($_GET['p'] * $myLimit) : 0;
	$myPattern  = sprintf(
		"search?cat=%s&date=%s&q=%s&email=%s&dpt=%s&order=%s&sort=%s",
		$_GET['cat'], $_GET['date'], $_GET['q'], $_GET['email'], $_GET['dpt'], $_GET['order'], $_GET['sort']
	)."&p=%s";

	$opt = array(
		'debug'  => 0,
		'offset' => $myOffset,
		'limit'  => $myLimit,
		'sort'   => 'date.start',
		'noOff'  => true,
		'dir'    => 1
	);

	//-- Category -----------------------------------------------------------------------
	if(!empty($_GET['cat'])){
		$opt['mvs.type'] = intval($_GET['cat']);
	}

	//-- Une date choisie dd.mm.yyyy ----------------------------------------------------
	if(!empty($_GET['date'])){
		list($d, $m, $y) = explode('.', $_GET['date']);

		$opt['thisDay'] = true;
		$opt['range'] = array(
			'start' => $d.'/'.$m.'/'.$y
		);
	#}else{
	#	$opt['incoming'] = new MongoDate();
	}

	//-- Recherche libre ----------------------------------------------------------------
	if(!empty($_GET['q'])){
		$_GET['q'] =  implode(', ', $api->searchToWords($_GET['q']));
		$opt['search'] = $_GET['q'];
	}

	//-- Email de la manifestation ------------------------------------------------------
	if(!empty($_GET['email'])){
		$opt['organisateur.email'] = urldecode($_GET['email']);
	}

	//-- Département --------------------------------------------------------------------
	if(!empty($_GET['dpt'])){
		$opt['dpt'] = array($_GET['dpt']);
	}


	//-- Order & Direction --------------------------------------------------------------
	if(!empty($_GET['order'])){
		$opt['sort'] = urldecode($_GET['order']);
		$opt['dir']  = intval($_GET['sort']);
	}

	$myData  = $api->get($opt);
	$myTotal = $api->total;

	unset($opt);



