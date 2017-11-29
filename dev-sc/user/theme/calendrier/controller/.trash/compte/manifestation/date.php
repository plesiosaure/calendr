<?php

	$api  = $this->apiLoad('calendrierManifestation');
	$date = $this->apiLoad('calendrierManifestationDate');

	// MANIFESTATION ///////////////////////////////////////////////////////////////////////////////////////////////////
	$myManifestation = $this->apiLoad('calendrierManifestation')->get(array(
		'debug'  => false,
		'_id'    => $this->kodeine['get']['id_manifestation'],
		'format' => array('date')
	));

	if(empty($myManifestation)) $this->go('/?manifestationNotFound');

	$myDates = $myManifestation['date'] ?: array();

	// UPDATE //////////////////////////////////////////////////////////////////////////////////////////////////////////
	if($_POST['action']){

		if(count($_POST['date']) > 0){
			foreach($_POST['date'] as $start => $e){

				if($e['delete'] == 'YES'){
					$date->pop(array(
						'_id'       => $myManifestation['_id'],
						'start'     => $start
					));
				}else{
					$date->change(array(
						'_id'       => $myManifestation['_id'],
						'old'       => $start,
						'new'       => date("Y-m-d", $start),
						'days'      => intval($e['days']),
						'comment'   => trim($e['comment']),
						'canceled'  => ($e['canceled'] == 'YES'),
						'postponed' => ($e['postponed'] == 'YES'),
						'unsure'    => ($e['unsure'] == 'YES')
					));
				}
			}
		}

		if(!empty($_POST['new']['start'])){

			$newdate = NULL;

			if(preg_match('#([0-9]{2})/([0-9]{2})/([0-9]{4})#', $_POST['new']['start'], $d)){
				$newdate = $d[3].'-'.$d[1].'-'.$d[2];
			}

			if(!empty($newdate)){
				$date->push(array(
					'_id'   => $myManifestation['_id'],
					'start' => strtotime($newdate . ' 00:00:00'),
					'days'  => 1
				));
			}
		}

		$this->go('date');
	}



