<?php

	$api  = $this->apiLoad('calendrierManifestation');
	$date = $this->apiLoad('calendrierManifestationDate');

	// SEARCH //////////////////////////////////////////////////////////////////////////////////////////////////////////

	$myDepartement = $this->apiLoad('calendrierDepartement')->departementGet(array(
		'code' => $this->kodeine['get']['departement']
	));

	$myLimit	= 50;
	$myOffset	= isset($this->kodeine['get']['page']) ? ($this->kodeine['get']['page'] * $myLimit) : 0;
	$myPattern  = '/manifestation/departement/'.$myDepartement['code'].'/page/%u';

	$opt = array(
		'debug'  => 0,
		'offset' => $myOffset,
		'limit'  => $myLimit,
		'sort'   => 'date.start',
		'dir'    => 1,
		'dpt'    => array($myDepartement['code'])
	);

	$myData  = $api->get($opt);
	$myTotal = $api->total;

	unset($iso, $opt);


