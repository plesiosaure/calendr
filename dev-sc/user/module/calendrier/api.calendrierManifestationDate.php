<?php

class calendrierManifestationDate extends calendrierManifestation {

	public function __construct(){
		parent::__construct();

		/*$this->collection('manifestation');
		$this->model(array(
			'id'              => array('get' => true, 'integer' => true),
		));*/
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
// Ajout une date dans manifestation.date
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public function push($opt){

		if(!isset($opt['_id']) OR (!isset($opt['raw']) && !isset($opt['start']))) return false;

		/////////////

		if(isset($opt['raw'])){
			$set = $opt['raw'];
		}else{
			$days = $opt['days'] ? intval($opt['days']) : 1;
			$set  = array(
				'start' => new MongoDate($opt['start']),
				'end'   => $this->day($opt['start'], $days),
				'days'  => $days
			);

			if(!empty($opt['comment'])) $set['comment'] = $opt['comment'];
		}

		/////////////

		$me = $this->get(array('_id' => $opt['_id']));
		$do = true;

		if(count($me['date']) > 0){
			foreach($me['date'] as $d){
				if($set['start'] == $d['start']) $do = false;
			}
		}

		if(!$do){
			if($opt['debug']) $this->pre("EXISTS");
			return false;
		}

		/////////////

		$mon = $this->mongoAuth();
		$db  = $mon->selectDB($this->db);
		$col = $mon->selectCollection($db, 'manifestation');

		$cond   = array('_id' => new MongoId($opt['_id']));
		$data   = array('$push' => array('date' => $set));
		$job    = $col->update($cond, $data);

		if($opt['debug']){
			$this->pre("COND", $cond, "SET", $data, "OPTION", $option, "JOB", var_export($job, true));
		}

		return true;
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
// Ajoute/Met à jour une date pour la manifestation. OPT: _id + date obligatoire
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public function set($opt){

		if(!isset($opt['_id']) OR !isset($opt['start'])) return false;

		/////////////

		$option = array();

		$data = $this->get(array(
			'_id' => $opt['_id']
		));

		$cond = array('_id' => $data['_id']);

		if(!is_array($data['date'])) $data['date'] = array();

		foreach($data['date'] as $n => $e){
			if($e['start']->sec == $opt['start']) $key = $n;
		}

		$set = array(
			'start'     => new MongoDate($opt['start']),
			'end'       => $this->day($opt['start'], $opt['days']),
			'days'      => intval($opt['days']),
			'comment'   => $opt['comment'],
			'canceled'  => ($opt['canceled'] == 1),
			'postponed' => ($opt['postponed'] == 1),
			'unsure'    => ($opt['unsure'] == 1)

		);

		if(isset($key)){
			$data   = array('$set'  => array('date.'.$key => $set));
		}else{
			$data   = array('$push' => array('date' => $set));
		}

		/////////////

		$mon = $this->mongoAuth();
		$db  = $mon->selectDB($this->db);
		$col = $mon->selectCollection($db, 'manifestation');
		$job = $col->update($cond, $data, $option);

		if($opt['debug']){
			$this->pre("COND", $cond, "SET", $data, "OPTION", $option, "JOB", var_export($job, true));
		}

		return $job;
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
// Supprime une date, pourrait être mieux écrit que ça,
// vérifier si mongo permet un unset d'un array avec/sans index
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public function pop($opt){

		if(!isset($opt['_id']) OR !isset($opt['start'])) return false;

		/////////////

		$data = $this->get(array(
			'_id' => $opt['_id']
		));

		if(count($data['date']) == 0) return true;

		foreach($data['date'] as $n => $e){
			if($e['start']->sec == $opt['start']) $key = 'date.'.$n;
		}

		if(!isset($key)) return false;

		$cond   = array('_id' => new MongoId($opt['_id']));
		$clr    = array('$unset' => array($key   => true));
		$fix    = array('$pull'  => array('date' => null));

		/////////////

		$mon  = $this->mongoAuth();
		$db   = $mon->selectDB($this->db);
		$col  = $mon->selectCollection($db, 'manifestation');

		$col->update($cond, $clr);
		$col->update($cond, $fix);

		if($opt['debug']) $this->pre("COND", $cond, "UNSET", $clr, "PULL", $fix);

		return true;
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
// Determine une date de fin depuis une date de debut et une durée
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public function day($start, $day, $mongo=true){

		$day = intval($day) -1;
		$ts  = strtotime(date("Y-m-d", $start).' 23:59:59') + ($day * 86400);

		if($day <= 1) return ($mongo) ? new MongoDate($ts) : $start;

		return ($mongo) ? new MongoDate($ts) : $ts;
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
// Change la valeur de la date pour l'array manifestation.date (met à jour la date de fin, d'après la durée)
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public function change($opt){

		if(!isset($opt['_id']) OR !isset($opt['old']) OR !isset($opt['new'])) return false;

		/////////////

		$from = $this->get(array(
			'_id' => $opt['_id']
		));

		foreach($from['date'] as $n => $e){
			if($e['start']->sec == $opt['old']) $key = $n;
		}

		if(isset($key)){
			$set = array(
				'start' => new MongoDate(strtotime($opt['new'].' 00:00:00')),
				'end'   => $this->day(strtotime($opt['new'].' 00:00:00'), $opt['days']),
				'days'  => intval($opt['days'])
			);

			if(!empty($opt['comment']))   $set['comment']   = $opt['comment'];
			if(!empty($opt['canceled']))  $set['canceled']  = $opt['canceled'];
			if(!empty($opt['postponed'])) $set['postponed'] = $opt['postponed'];
			if(!empty($opt['unsure']))    $set['unsure']    = $opt['unsure'];

			$data = array('$set'  => array('date.'.$key => $set));
		}else{
			return false;
		}

		/////////////

		$mon = $this->mongoAuth();
		$db  = $mon->selectDB($this->db);
		$col = $mon->selectCollection($db, 'manifestation');

		$cond = array('_id' => $from['_id']);
		$job  = $col->update($cond, $data);

		if($opt['debug']){
			$this->pre("COND", $cond, "SET", $data, "JOB", var_export($job, true));
		}
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
// Retour la date la plus ancienne
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public function oldest($dates){

		if(!is_array($dates) OR count($dates) == 0) return false;

		$oldest = 9999999999;
		foreach($dates as $date){
			if($date['start']->sec < $oldest) $oldest = $date['start']->sec;
		}

		return $oldest;
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
// Retour la date la plus récente
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public function lastest($dates){

		if(!is_array($dates) OR count($dates) == 0) return false;

		$lastest = 0;
		foreach($dates as $date){
			if($date['end']->sec > $lastest) $lastest = $date['end']->sec;
		}

		return $lastest;

	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
// Retour la date la plus proche (de maintenant, ou d'une date spécifique)
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public function nearest($dates, $date=NULL){


		// Pas ce qu'il faut
		if(!is_array($dates) OR count($dates) == 0) return false;

		// Juste une date
		if(count($dates) == 1) return $dates[0];

		$d    = array();
		$time = empty($date) ? time() : strtotime($date);

		// On ne garde que les dates qui sont après notre seuil (NOW ou un timstamp)
		foreach($dates as $date){
			if($date['end']->sec > $time) $d[$date['start']->sec] = $date;
		}

		// toute les dates sont dans le passé ou avant notre seuile : récupérer la dernières
		if(empty($d)){

			usort($dates, function($a, $b){
				return $a['start']->sec	> $b['start']->sec;
			});

		#	$this->pre($dates);
			$d = $dates[count($dates)-1];
			return $d;
		}

		ksort($d);
		$d = array_values($d);

		return $d[0] ?: false;

	}

}