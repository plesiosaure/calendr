<?php

	$api  = $this->apiLoad('calendrierManifestation');
	$date = $this->apiLoad('calendrierManifestationDate');

	// SEARCH //////////////////////////////////////////////////////////////////////////////////////////////////////////

	$myLimit	= 10;
	$myOffset	= isset($_GET['p']) ? ($_GET['p'] * $myLimit) : 0;
	$myPattern  = sprintf(
		"search?cat=%s&region=%s&dep=%s&date=%s&q=%s",
		$_GET['cat'], $_GET['region'], $_GET['dep'], $_GET['date'], $_GET['q']
	)."&p=%s";

	$opt = array(
		'debug'  => 0,
		'offset' => $myOffset,
		'limit'  => $myLimit,
		'sort'   => 'date.start',
		'dir'    => 1
	);

	//-- Category ------------------------------------------------------------------------
	if(!empty($_GET['cat'])){
		if(false !== strpos($_GET['cat'], 't')){
			$opt['mvs.type'] = intval(substr($_GET['cat'], 1));
		}else
		if(false !== strpos($_GET['cat'], 'c')){
			$opt['mvs.category'] = intval(substr($_GET['cat'], 1));
		}
	}

	if(!empty($_GET['region'])) $opt['region']   = $_GET['region'];
	if(!empty($_GET['dep']))    $opt['dpt']      = array($_GET['dep']);

	if(!empty($_GET['zip'])){
		$zip = trim(str_replace(' ', '', $_GET['zip']));

		if($zip > 0){
			if(strlen($zip) == 1) $zip = '0'.$zip;
			$zip = str_pad($zip, 5, '0', STR_PAD_RIGHT);
			$opt['city.zip'] = $zip;
		}else{
			$zip = '';
		}

		$_GET['zip'] = $zip;
	}

	//-- Une REGION, pas de DEPARTEMENT -------------------------------------------------
	if(!empty($opt['region']) && empty($opt['dpt'])){
		$region  = $this->apiLoad('calendrierDepartement')->regionGet(array(
			'code' => $opt['region']
		));
		$opt['dpt'] = $region['dep'];
	}

	// Une date choisie dd.mm.yyyy ou yyyy.mm.dd (mobile
	if(!empty($_GET['date'])){
		if(strpos($_GET['date'], '-') !== false){
			list($y, $m, $d) = explode('-', $_GET['date']);
		}else{
			list($d, $m, $y) = explode('.', $_GET['date']);
		}

		$opt['thisDay'] = true;
		$opt['range'] = array(
			'start' => $d.'/'.$m.'/'.$y
		);
	}else{
		$opt['incoming'] = new MongoDate();
	}

	//-- Recherche libre ----------------------------------------------------------------
	if(!empty($_GET['q'])){
		$_GET['q'] =  implode(', ', $api->searchToWords($_GET['q']));
		$opt['search'] = $_GET['q'];
	}

	$myData  = $api->get($opt);
	$myTotal = $api->total;

	// Mémoriser la recherche dans la session
	// TODO: le array_filter ne semble pas bien marché du coup tout le GET est mémorisé (trop de data)
	$_SESSION['search'] = array_filter($_GET, function($v){
		$allowed = array('cat', 'region', 'dep', 'zip', 'date', 'q');
		return !in_array($v, $allowed);
	});

	unset($iso, $opt);


