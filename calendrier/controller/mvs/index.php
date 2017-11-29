<?php

	$time = microtime(true);

	ini_set('upload_max_filesize',	'100M');
	ini_set('post_max_size',		'100M');

	ini_set('max_execution_time',	'1000');
	ini_set('max_input_time',		'1000');


	require KROOT.'/user/plugin/klein.php/klein.php';
	require __DIR__.'/route.php';

	// ACTIONS /////////////////////////////////////////////////////////////////////////////////////////////////////////
	if(is_array($route)){
		$controller = $route['controller'];
		$action     = $route['action'];
		$args       = is_array($route['args']) ? $route['args'] : array();

	#	$out = $this->apiLoad($controller)->$action($args);
		$out = array('ok' => false, 'err' => 'Fake');

		$out['controller'] = $route['controller'];
		$out['action']     = $route['action'];
	#	$out['args']       = $args;
		$out['time']       = microtime(true) - $time;

	}else{
		$out = array(
			'ok'  => false,
			'err' => 'No route'
		);
	}

	// OUTPUT //////////////////////////////////////////////////////////////////////////////////////////////////////////
#	if(!is_array($out)) exit();

	$json = $this->helperJsonEncode($out);

	if(!headers_sent()) header("Content-Type: text/plain");

	#if(isset($_GET['beauty'])){
		echo $this->helperJsonBeautifier($json);
	#}else{
	#	echo $json;
	#}


	// LOG /////////////////////////////////////////////////////////////////////////////////////////////////////////////
	$args = $route['args'];
	unset($args['rewrite'], $args['urlLanguage'], $args['urlRequest']); // Kodeine stuff

	$this->apiLoad('calendrierLog')->dev(array(
		'from'   => 'mvs',
		'raw'    => $out,
		'method' => $_SERVER['REQUEST_METHOD'],
		'api'    => array(
			'controller' => $route['controller'],
			'action'     => $route['action'],
			'args'       => $args
		)
	));


