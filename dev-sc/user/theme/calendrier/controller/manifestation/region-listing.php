<?php

	$api  = $this->apiLoad('calendrierManifestation');
	$date = $this->apiLoad('calendrierManifestationDate');

	// SEARCH //////////////////////////////////////////////////////////////////////////////////////////////////////////

	$myRegion = $this->apiLoad('calendrierDepartement')->regionGet(array(
		'code' => $this->kodeine['get']['region']
	));

	$myLimit	= 50;
	$myOffset	= isset($this->kodeine['get']['page']) ? ($this->kodeine['get']['page'] * $myLimit) : 0;
	$myPattern  = '/manifestation/region/'.$myRegion['code'].'/page/%u';

	$opt = array(
		'debug'  => 0,
		'offset' => $myOffset,
		'limit'  => $myLimit,
		'sort'   => 'date.start',
		'dir'    => 1,
		'dpt'    => $myRegion['dep']
	);

	$myData  = $api->get($opt);
	$myTotal = $api->total;

	unset($iso, $opt);


