<?php

	header('Content-type: text/plain; charset=utf-8');

	$api = $this->apiLoad('calendrierManifestation');

// DATA ////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	$opt = array(
		'debug'   => 0,
		'format'  => array(),
		'noLimit' => true,
		'noSort'  => true
	);

	if(!empty($_GET['dep'])){
		$opt['city.dep'] = $_GET['dep'];
	}

	if(!empty($_GET['category'])){
		$opt['category'] = explode(',', trim($_GET['category']));
	}

	if(isset($_GET['date'])){
		list($y, $m, $d) = explode('/', $_GET['date']);
		$time = mktime(0, 0, 0, $m, $d, $y);


		$monday = (date("N", $time) != 1) ? strtotime('last monday', $time) : $time;
		$next   = strtotime("next sunday", $monday);

		$opt['range'] = array(
			'future' => true,
			'start'  => date("d/m/Y", $monday),
			'end'    => date("d/m/Y", $next)
		);
	}

	/*if(!empty($_GET['start']) && !empty($_GET['end'])){

		if(strpos($_GET['start'], '-') === false){
			$start = date("d/m/Y", $_GET['start']);
		}else{
			$start = explode('-', $_GET['start']);
			$start = $start[2].'/'.$start[1].'/'.$start[0];
		}

		if(strpos($_GET['end'], '-') === false){
			$end   = date("d/m/Y", $_GET['end']);
		}else{
			$end   = explode('-', $_GET['end']);
			$end   = $end[2].'/'.$end[1].'/'.$end[0];
		}

		$opt['range'] = array(
			'start' => $start,
			'end'   => $end
		);

	}else{
		$opt['range'] = array('start' => date('d/m/Y'));
	}*/


	if(isset($_GET['near'])){
		$me = $api->get(array(
			'_id'    => $_GET['near'],
			'format' => array()
		));

		$data = $api->near(array(
			'debug'  => false,
			'format' => array(),
			'pos'    => $me['geo']['gps'],
			'limit'  => 75
		));

	}else{
		$data = $api->get($opt);
	}


	$type = array(
		'1' => 'collection',
		'2' => 'auto',
		'3' => 'moto'
	);

// FORMAT //////////////////////////////////////////////////////////////////////////////////////////////////////////////

	$json = array();
	foreach($data as $e){
		$gps = $e['geo']['gps'];
		if(!is_array($gps)) $gps = false;

		$do = (isset($_GET['near']) && $e['_id'] == $me['_id']) ? false : true;

		if($do){
			$json[] = array(
				'_id'      => $e['_id'],
				'zip'      => $e['city']['zip'],
				'dep'      => substr($e['city']['zip'], 0, 2),
				'name'     => strip_tags($api->nameFormat($e)),
				'gps'      => $gps,
				'type'     => $type[$e['mvs']['type']],
				'category' => intval($e['mvs']['category']),
				'link'     => $api->manifestationPermalink($e)
			);
		}
	}

	if(isset($_GET['near'])){
		$json = array(
			'manif' => array(
				'_id'      => $me['_id'],
				'zip'      => $me['city']['zip'],
				'dep'      => substr($me['city']['zip'], 0, 2),
				'name'     => strip_tags($api->nameFormat($me)),
				'gps'      => $me['geo']['gps'],
				'type'     => $type[$me['mvs']['type']],
				'category' => intval($me['mvs']['category'])
			),
			'near'  => $json
		);
	}


// JSON ////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	$json = $this->helperJsonEncode($json);

#	if(ONAIR){
#		echo $json;
#	}else{
		echo $this->helperJsonBeautifier($json);
#	}



