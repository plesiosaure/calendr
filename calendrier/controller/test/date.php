<?php


	$d = $this->apiLoad('calendrierManifestation')->get(array(
		'debug'   => true,
		'range'   => array('start' => '18/09/2013'),
		'noLimit' => true
	));

	$this->pre($d);


