<?php

	$onAir = true;

	if(isset($GLOBALS['dev_mode']) && $GLOBALS['dev_mode'] == 'dev'){ // depuis les scripts de crontab
		$onAir = false;
	}else
	if(basename(dirname(dirname(dirname(__FILE__)))) == 'dev-sc'){
		$onAir = false;
	}

	$config	 = array(
		'mysql'	=> array(
			'utf8'     => true,
			'log'      => false,
			'lograw'   => false,
			'host'     => 'localhost',
			'login'    => 'motoregister',
			'password' => 'aaXHTtfFURepJ2s5',
			'database' => 'motoregister'
		),

		'mongodb' => array(
			'host'     => 'localhost',
			'login'    => 'calendrier',
			'password' => 'PstHwA0XdxvamOf',
			'database' => 'calendrier-dev',
			'timeout'  => 600000
		),

		'benchmark' => array(
			'allow' => true
		),

		'debug' => array(
			'allow' => true
		),

		'googlemap' => array(
			'apikey' => 'AIzaSyDz0ayI8XOWo9NFyOg9IXjmJcxVkHGKPVs'
		),

		'mandrill' => array(
			'key' => 'XBvyIFUyZBeY-k14FetjCg'
		),

		'supercal' => array(
			'domain' => '217.167.201.245',
			'url'    => '/test_supercal/supercal',
			'source' => 'calendrier-test'
		)

	);

	$onAir = true;
	if($onAir){
		$config['mongodb']['database']  = 'calendrier';
		$config['supercal']['source']   = 'calendrier';
		$config['supercal']['url']      = '/lva_cal_guide_cote/supercal';
	}

	unset($onAir);

