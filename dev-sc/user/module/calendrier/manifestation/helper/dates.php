<?php

	$api = $app->apiLoad('calendrierManifestationDate');


// GET /////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	if($_SERVER['REQUEST_METHOD'] == 'GET'){

		$data  = $api->get(array(
			'_id'    => $_GET['_id'],
			'format' => array()
		));

		$data = is_array($data['date']) ? $data['date'] : array();

		foreach($data as $n => $e){
			$data[$n]['start']   = $e['start']->sec;
			$data[$n]['display'] = date("Y-m-d", $e['start']->sec);
			$data[$n]['end']     = $e['end']->sec;
		}

		// Si je demande une DATE en particulier
		if(isset($_GET['start'])){
			foreach($data as $e){
				if($e['start'] == $_GET['start']){
					$found = true;
					$data = $e;
					break;
				}
			}

			if(!$found) $data = array();
		}

	}else

// CREATE //////////////////////////////////////////////////////////////////////////////////////////////////////////////

	if($_SERVER['REQUEST_METHOD'] == 'POST'){

		die('--');

		$req = json_decode(file_get_contents('php://input'), true);

		$data = $api->get(array(
			'_id'    => $req['_id'],
			'format' => array('date')
		));

		$dates = is_array($data['date']) ? $data['date'] : array();

		foreach($dates as $n => $e){
			$dates[$n] = $e['start'];

	#		echo date("Y-m-d H:i:s", $e['start'])."\n";
		}

	#	echo date("Y-m-d H:i:s", $req['start'])."\n";

	#	var_dump(in_array($req['start'], $dates));
	#	die();

		if(in_array($req['start'], $dates)){
			header("HTTP/1.1 400");
			$data = array('duplicate' => true);
		}else{

			$api->push(array(
				'_id'   => $req['_id'],
				'start' => $req['start']
			));

			$data = array(
				'start'   => $req['start'],
				'post'    => $req,
				'days'    => 1,
				'display' => date("Y-m-d", $req['start'])
			);
		}

	}else

// UPDATE //////////////////////////////////////////////////////////////////////////////////////////////////////////////

	if($_SERVER['REQUEST_METHOD'] == 'PUT'){

		$in = file_get_contents('php://input');

		// Provient d'une Request Payload
		$req = json_decode($in, true);

		// Provient d'un Form Data
		if(!is_array($req)) parse_str($in, $req);


		// MIMIC POST
		if($req['create']) $req['dates'] = array($req['start']);

		foreach($req['dates'] as $e){
			$api->set(array(
				'debug'   => false,
				'_id'     => $req['_id'],
				'start'   => $e,
				'days'    => intval($req['days']),
				'comment' => $req['comment']
			));
		}

		// Si je change la date et que je demande un update sur un seul element
		/*if(count($req['dates']) == 1 && $req['date'] != ''){
			$api->change(array(
				'_id' => $req['_id'],
				'old' => $req['dates'][0],
				'new' => $req['date'],
			));
		}*/

		$data = array(
			'success' => true,
			'req' => $req
		);

		if($req['create']) $data['display'] = date("Y-m-d", $req['dates'][0]);

	}else

// DELETE //////////////////////////////////////////////////////////////////////////////////////////////////////////////

	if($_SERVER['REQUEST_METHOD'] == 'DELETE'){

		if(strpos($_GET['_id'], '/') !== false){
			list($_id, $start) = explode('/', $_GET['_id']);

			$api->pop(array(
				'debug' => false,
				'_id'   => $_id,
				'start' => $start
			));

			$data = true;
		}else{
			$data = false;
		}

	}else

// THIS IS NOT THE DATA YOU ARE LOOKING FOR, BRO ///////////////////////////////////////////////////////////////////////

	{
		$data = array('err' => 'unknown request method: '.$_SERVER['REQUEST_METHOD']);
	}

// OUTPUT //////////////////////////////////////////////////////////////////////////////////////////////////////////////

	if(is_array($data)){
		echo $app->helperJsonBeautifier($app->helperJsonEncode($data));
	}else{
		echo json_encode($data);
	}

