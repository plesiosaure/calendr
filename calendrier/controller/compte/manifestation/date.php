<?php

	$api  = $this->apiLoad('calendrierManifestation');
	$date = $this->apiLoad('calendrierManifestationDate');

	// MANIFESTATION ///////////////////////////////////////////////////////////////////////////////////////////////////
	$myManifestation = $this->apiLoad('calendrierManifestation')->get(array(
		'debug'  => false,
		'format' => array('date'),
		'_id'    => $this->kodeine['get']['id_manifestation']
	));

	if(empty($myManifestation)) $this->go('/?manifestationNotFound');

	$myDates = $myManifestation['date'] ?: array();

	// UPDATE //////////////////////////////////////////////////////////////////////////////////////////////////////////
	if($_POST['action']){

		if(count($_POST['date']) > 0){
			foreach($_POST['date'] as $start => $e){

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

		if(!empty($_POST['new']['start'])){
			$date->push(array(
				'_id'   => $myManifestation['_id'],
				'start' => strtotime($_POST['new']['start'] . ' 00:00:00'),
				'days'  => 1
			));
		}

		unset($myDates, $start, $e);

		$this->go('date');
	}



