<?php

class calendrierLog extends calendrier {

// CORE ////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	public function __construct(){
		parent::__construct();

		$this->collection('apilog');

		$this->model(array(
			'date'     => array(),
			'url'      => array(),
			'raw'      => array(),
			'id_manif' => array('string' => true, 'get' => true)
		));
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public function get(array $opt){

		if(!empty($opt['debug'])) $this->pre($opt);

		$mon  = $this->mongoAuth();
		$db   = $mon->selectDB($this->db);
		$col  = $mon->selectCollection($db, $this->collection);

		$find = (array_key_exists('_id', $opt) && !is_array($opt['_id'])) ? 'findOne' : 'find';
		$cond = $this->cond($opt, $find);

		// -- -- -- -- -- -- -- -- -- -- -- -- -- -- --

		if(array_key_exists('range', $opt)){

			$start = $opt['range']['start'];
			if(is_string($start) && $start != ''){
				list($y, $m, $d) = explode('-', $start);
				$start = new MongoDate(strtotime($y.'-'.$m.'-'.$d.' 00:00:00'));
				$same  = new MongoDate(strtotime($y.'-'.$m.'-'.$d.' 23:59:59'));
			}else
			if(!is_a($start, 'MongoDate')){
				unset($start);
			}

			$end = $opt['range']['end'];
			if(is_string($end) && $end != ''){
				list($y, $m, $d) = explode('-', $end);
				$end = new MongoDate(strtotime($y.'-'.$m.'-'.$d.' 00:00:00'));
			}else
			if(!is_a($end, 'MongoDate')){
				unset($end);
			}

			if(!isset($end)) $end = $same;

			if(isset($start)){
				$cond['date'] = array('$gte' => $start, '$lte' => $end);
			}
		}

		if(array_key_exists('search', $opt) && is_string($opt['search']) && $opt['search'] != ''){
			$regex = new MongoRegex('/'.$opt['search'].'/i');
			$cond['$or'][] = array('url' => $regex);
		}

		if(array_key_exists('error', $opt) && is_bool($opt['error'])){
			$cond['success'] = ($opt['error']) ? false : true;
		}

		// -- -- -- -- -- -- -- -- -- -- -- -- -- -- --

		$data = $col->$find($cond);
		$flip = false;

		if($find == 'find'){
			$this->total    = $data->count();
			$this->limit    = ($opt['limit'] != '')   ? intval($opt['limit'])  : 100;
			$this->offset   =(($opt['offset'] != '')  ? intval($opt['offset']) : 0);        # * $this->limit;
			$this->dir      = ($opt['dir'] != '')     ? intval($opt['dir'])    : -1;
			$this->sort     = ($opt['sort'] != '')    ? $opt['sort']           : 'date';

			$data->skip($this->offset);
			$data->limit($this->limit);
			$data->sort(array($this->sort => $this->dir));

			$explain = var_export($data->explain(), true);
			$data    = iterator_to_array($data);
		}else
		if(is_array($data) && array_key_exists('_id', $data)){
			$this->total = 1;
			$flip = true;
		}else{
			$this->total = 0;
			$this->reset();
			$data = array();
		}

		if(!empty($opt['debug'])){
			$this->pre("COND", $cond, "TOTAL", $this->total, "DATA", $data, "EXPLAIN", $explain);
		}

		///////////////////////

		if($flip) $data = array($data);

		if(is_array($opt['format'])){
			$data = $this->format(array(
				'data' => $data,
				'user' => $opt['format']['user'] ?: in_array('user', $opt['format']),
			));
		}

		if($flip) $data = $data[0];

		///////////////////////

		return $data;
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public function del(array $opt){
		$this->_id($opt['_id'])->debug(false)->remove();
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public function format(array $opt){

		if(count($opt['data']) == 0) return $opt['data'];

		$data = $opt['data'];

		foreach($data as $n => $e){
			$data[$n]['_id']  = (string)$e['_id'];
			$data[$n]['date'] = intval($e['date']->sec);
		}

		return $data;
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public function clear(){

		$mon  = $this->mongoAuth();
		$db   = $mon->selectDB($this->db);
		$col  = $mon->selectCollection($db, $this->collection);

		$col->remove(array());
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public function dev(array $data){

		$mon  = $this->mongoAuth();
		$db   = $mon->selectDB($this->db);
		$col  = $mon->selectCollection($db, $this->collection);

		$more =  array(
			'date'   => new MongoDate(),
			'ip'     => $_SERVER['REMOTE_ADDR'],
			'domain' => $_SERVER['HTTP_HOST'],
			'url'    => $_SERVER['REQUEST_URI']
		);

		$data = array_merge($more, $data);

		$col->insert($data);

		return true;
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public function replay(array $opt){

		$src = $this->get(array(
			'_id'    => $opt['_id'],
			'format' => array()
		));

		$print = $opt['shell'] == false;

		if(empty($src)){
			if($print) echo "Source not found: _id = ".$opt['_id']."\n";
			return false;
		}else
		if($src['locked']){
			if($print) echo "Locked item: _id = ".$opt['_id']."\n";
			return false;
		}

		$url = 'http://'.$src['host'].$src['url'];
		$rest = new coreRest('', '', $src['host']);
		$rest->setMode('classic');

		if($print){
			echo 'URL '.$url."\n";
			echo 'SRC '; print_r($src);
			echo "\n\nREPLAY ...\n";
		}

		$raw = $rest->request(array(
			'uri'   => $src['url'],
			'verb'  => $src['method'],
			'data'  => $src['api']['args']
		));

		if($print){
			print_r($raw);
			return $raw;
		}

		return $raw['body']['ok'] === true;
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
// Relancer les task une à une, augment le compteur de tentative si ça échoue, vérouille la tache si  > 5
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public function replayAll(array $tasks){

		if(!is_array($tasks) OR count($tasks) == 0) return false;

		$mon  = $this->mongoAuth();
		$db   = $mon->selectDB($this->db);
		$col  = $mon->selectCollection($db, $this->collection);

		foreach($tasks as $e){

			$success = $this->replay(array(
				'_id'   => $e['_id'],
				'shell' => true
			));

			$this->replaySave($col, $e, $success);
		}

		return true;

	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
// Relancer une tache manuellement depuis le back office, ne fait rien en cas d'erreur
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public function replayGUI($_id){

		$mon  = $this->mongoAuth();
		$db   = $mon->selectDB($this->db);
		$col  = $mon->selectCollection($db, $this->collection);

		$log = $this->get(array(
			'_id' => $_id
		));

		$raw = $this->replay(array(
			'_id' => $_id
		));

		$success = $raw['body']['ok'];

		$this->replaySave($col, $log, $success);

		return $success;
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
// Mise à jour de la BDD
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	private function replaySave($col, $data, $success){

		if($success === true){
			$set = array(
				'success'     => true,
				'dateSuccess' => new MongoDate()
			);
		}

		// En erreur (encore)
		else{
			$inc = array('attempt' => 1);
			$set = array(
				'dateLastError' => new MongoDate(),
			);

			// Trop de tentative raté => vérouillé
			if($data['attempt'] +1 > 5) $set['locked'] = true;
		}

		$update = array('$set' => $set);
		if(isset($inc)) $update['$inc'] = $inc;

		$col->update(
			array('_id' => $data['_id']),
			$update
		);

	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public function errorCount(){

		$mon  = $this->mongoAuth();
		$db   = $mon->selectDB($this->db);
		$col  = $mon->selectCollection($db, $this->collection);

		$data = $col->find(array(
			'from'    => 'rest',
			'success' => array('$ne' => true)
		));

		return $data->count();
	}

// REST ////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	public function _sos(array $opt){

		$this->dev(array(
			'from' => 'rest',
			'dump' => $opt['dump'],
			'url'  => $opt['url'],
			'sos'  => true,
		));

		return array('ok' => true, 'data' => 'Call 911');
	}

// CRONTAB /////////////////////////////////////////////////////////////////////////////////////////////////////////////

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
// Recupère des appels qui ont échoué
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public function getRetryTask($opt=array()){

		if(!empty($opt['debug'])) $this->pre($opt);

		$mon  = $this->mongoAuth();
		$db   = $mon->selectDB($this->db);
		$col  = $mon->selectCollection($db, $this->collection);

		$find = (array_key_exists('_id', $opt) && !is_array($opt['_id'])) ? 'findOne' : 'find';
		$cond = array(
			'success' => false,
			'locked'  => array('$exists' => false),
			'$or' => array(
				array('attempt' => array('$lte' => 5)),
				array('attempt' => array('$exists' => false))
			)
		);

		$data = $col->find($cond)->skip(0)->limit(5);
		$data = iterator_to_array($data);

		return $data;
	}

}