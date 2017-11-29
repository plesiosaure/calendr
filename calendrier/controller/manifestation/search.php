<?php

	$api  = $this->apiLoad('calendrierManifestation');
	$date = $this->apiLoad('calendrierManifestationDate');

	// SEARCH //////////////////////////////////////////////////////////////////////////////////////////////////////////

	$myLimit	= 20;
	$myOffset	= isset($_GET['p']) ? ($_GET['p'] * $myLimit) : 0;
	$myPattern  = sprintf(
		"search?type=%s&region=%s&dep=%s&date=%s&q=%s",
		$_GET['type'], $_GET['region'], $_GET['dep'], $_GET['date'], $_GET['q']
	)."&p=%s";

	$opt = array(
		'debug'  => 0,
		'offset' => $myOffset,
		'limit'  => $myLimit,
		'sort'   => 'date.start',
		'dir'    => 1
	);

	// Category
	if(!empty($_GET['cat'])){
		if(strpos($_GET['cat'], 't')){
			$opt['mvs.type'] = intval(substr($_GET['cat'], 1));
		}else
		if(strpos($_GET['cat'], 'c')){
			$opt['mvs.category'] = intval(substr($_GET['cat'], 1));
		}
	}

	if(!empty($_GET['region'])) $opt['region']   = $_GET['region'];
	if(!empty($_GET['dep']))    $opt['dpt']      = array($_GET['dep']);
	if(!empty($_GET['zip']))    $opt['city.zip'] = $_GET['zip'];

	// Une REGION, pas de DEPARTEMENT
	if(!empty($opt['region']) && empty($opt['dpt'])){
		$region  = $this->apiLoad('calendrierDepartement')->regionGet(array(
			'code' => $opt['region']
		));
		$opt['dpt'] = $region['dep'];
	}

	// Une date choisie dd.mm.yyyy
	if(!empty($_GET['date'])){
		list($d, $m, $y) = explode('.', $_GET['date']);

		$opt['range'] = array(
			'start' => $d.'/'.$m.'/'.$y,
			'end'   => $d.'/'.$m.'/'.$y
		);
	}else{
		$opt['range'] = array(
			'start' => date("d/m/Y")
		);
	}

	// Recherche libre
	if(!empty($_GET['q'])) $opt['search'] = $_GET['q'];

	$myData  = $api->get($opt);
	$myTotal = $api->total;

	unset($iso, $opt);


