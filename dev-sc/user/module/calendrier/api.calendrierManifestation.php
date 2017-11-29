<?php

class calendrierManifestation extends calendrier {

	public function __construct(){
		parent::__construct();

		$this->collection('manifestation');
		$this->model(array(
			'id'              => array('get' => true, 'findOne' => true),
			'created'         => array('date' => true),
			'updated'         => array('date' => true),

			'name'            => array('check' => '.*'),
			'type'            => array(),

			'pro'             => array('boolean' => true),
			'individual'      => array('boolean' => true),
			'resident'        => array('boolean' => true),
			'indoor'          => array('boolean' => true),
			'outdoor'         => array('boolean' => true),
			'game'            => array('boolean' => true),
			'number'          => array('integer' => true, 'notIfEmpty' => true),

			'free'            => array('boolean' => true),
			'paying'          => array('boolean' => true),
			'price'           => array('notIfEmpty' => true, 'set' => function($v){
				return floatval(str_replace(',', '.', $v));
			}),

			'resume'          => array(),
		#	'resume_date'     => array(),
			'presentation'    => array(),
			'presentation_web'=> array(),
			'schedule'        => array(),
			'opening'         => array(),
			'periodicity'     => array('integer' => true),

			'email'           => array('get' => true),
			'phone'           => array(),
			'phones'          => array(),
			'mobile'          => array(),
			'fax'             => array(),
			'web'             => array(),

			'editing'         => array('boolean' => true, 'get' => true),
			'mvs'             => array('array' => true, 'child' => array(
				'type'            => array('integer' => true),
				'category'        => array('integer' => true)
			)),

			'geo'             => array('array' => true, 'child' => array(
				'region'          => array('integer' => true, 'notIfEmpty' => true),
				'dept'            => array('integer' => true, 'notIfEmpty' => true),
				'address'         => array(),
				'country'         => array(),
				'comment'         => array(),
				'gps'             => array('gps' => true, 'notIfEmpty' => true),
				'zoom'            => array('integer' => true, 'notIfEmpty' => true)
			)),

			'city'            => array('array' => true, 'child' => array(
				'_id'             => array('get' => true),
				'id'              => array('integer' => true),
				'name'            => array()
			)),

			'organisateur'    => array('array' => true, 'child' => array(
				'_id'             => array('get' => true),
				'id'              => array('integer' => true),
				'name'            => array()
			)),

			'date'            => array('array' => true, 'child' => array(
				'start'           => array('date' => true),
				'end'             => array('date' => true),
				'days'            => array('integer' => true),
				'comment'         => array(),
				'canceled'        => array('boolean' => true),
				'postponed'       => array('boolean' => true),
				'unsure'          => array('boolean' => true)
			)),

			'mode'            => array('get' => true, 'string' => true),
			'moderation'      => array('get' => true, 'string' => true),
			'editor'          => array('array' => true),
			'temp'            => array('array' => true)
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

		//////////////////////////////////////////////////////////////////////////////

		// ID MVS (type_id)
		if(!empty($opt['id'])) $find = 'findOne';

		$cond['$or'] = array();

		if(array_key_exists('type', $opt)){

			// Permet de passer 'type.subs' plutot que type:[sub]
			if(is_string($opt['type']) && $opt['type'] != ''){
				list($m, $s) = explode('.', $opt['type']);
				$opt['type'] = array($m => ($s != '') ? array($s) : NULL);
			}

			#	$this->pre($opt['type']);

			if(is_array($opt['type']) && count($opt['type']) > 0){
				foreach($opt['type'] as $k => $v){
					$cond['$or'][] = array(
						'type.'.$k => is_array($v)
							? array('$in' => $v)
							: array('$exists' => true)
					);
				}
			}

			unset($cond['type']);
		}

		// Event a venir
		if(array_key_exists('incoming', $opt) && !empty($opt['incoming'])){

			$start = $opt['incoming'];

			if(is_string($start)){
				list($d, $m, $y) = explode('/', $start);
				$start = new MongoDate(strtotime($y.'-'.$m.'-'.$d.' 00:00:00'));
			}else
			if(!is_a($start, 'MongoDate')){
				unset($start);
			}

			if(isset($start)) $cond['date.start'] = array('$gte' => $start);
		}else

		// Event pour une date particulière
		if(array_key_exists('range', $opt) && !empty($opt['range'])){

			$start  = $opt['range']['start'];
			$end    = $opt['range']['end'];
			$future = $opt['range']['future'] ? : false;

			if(is_string($start) && !empty($start)){
				list($d, $m, $y) = explode('/', $start);
				$start = new MongoDate(strtotime($y.'-'.$m.'-'.$d.' 00:00:00'));
				if(!empty($opt['thisDay'])) $end  = new MongoDate(strtotime($y.'-'.$m.'-'.$d.' 23:59:59'));
			}else
			if(!is_a($start, 'MongoDate')){
				unset($start);
			}

			if(is_string($end) && !empty($end)){
				list($d, $m, $y) = explode('/', $end);
				$end = new MongoDate(strtotime($y.'-'.$m.'-'.$d.' 23:59:59'));
			}else
			if(!is_a($end, 'MongoDate')){
				unset($end);
			}

		#	var_dump($end);
		#	echo 'S='.date("Y-m-d H:i:s", $start->sec).' &nbsp; &nbsp; &nbsp; E='.date("Y-m-d H:i:s", $end->sec);

		#	if(!isset($end)) $end = $same;

			$st = '$lte';
			$en = '$gte';

			// Cas particulier, utiliser par la MAP de la home: (les evenements n'ont peut être pas encore commencé)
			if($future){ $st = '$gte'; $en = '$lte'; }

			if(isset($start)) $cond['date']['$elemMatch']['start'] = array($st => $start);
			if(isset($end))   $cond['date']['$elemMatch']['end']   = array($en => $end);
		}

		if(array_key_exists('search', $opt) && is_string($opt['search']) && $opt['search'] != ''){
			$words = $this->searchToWords($opt['search']);
			if(count($words) > 0){
				$regex = new MongoRegex('/'.implode('|', $words).'/iu');
				$cond['$or'][] = array('name' => $regex);
			}
		}

		if(array_key_exists('category', $opt) && is_array($opt['category']) && count($opt['category']) > 0){
			$category = array_map('intval', $opt['category']);
			$cond['mvs.category'] = (count($category) == 1) ? $category[0] : array('$in' => $category);
			unset($category);
		}

		if(array_key_exists('dpt', $opt) && is_array($opt['dpt']) && count($opt['dpt']) > 0){
			$dep = array_map('trim', $opt['dpt']);
			$cond['city.dep'] = (count($dep) == 1) ? $dep[0] : array('$in' => $dep);
			unset($dep);
		}

		/*if(array_key_exists('notSynced', $opt) && is_bool($opt['notSynced'])){
			if($opt['notSynced'] === true) $cond['mvs.sync'] = false;
		}*/

		if(array_key_exists('hasBackup', $opt) && is_bool($opt['hasBackup'])){
			if($opt['hasBackup'] === true) $cond['backup'] = array('$exists' => true);
		}

		if($find == 'find' && empty($opt['noOff'])) $cond['off'] = array('$exists' => false);

		$orCount = count($cond['$or']);

		if($orCount == 0){
			unset($cond['$or']);
		}else
		if($orCount == 1){
			$first = array_keys($cond['$or'][0]);
			$cond[$first[0]] = $cond['$or'][0][$first[0]];
			unset($cond['$or']);
		}

#		$this->pre($cond);

		//////////////////////////////////////////////////////////////////////////////

		#$this->pre("start", date("Y-m-d H:i:s", $cond['date.start']["\$gte"]->sec));
		#$this->pre("end  ", date("Y-m-d H:i:s", $cond['date.end']["\$lte"]->sec));


	#	$this->pre($opt, $cond);

	#	$now = microtime(true);
	#	print_r($cond);
		$data = $col->$find($cond);

		$flip = false;

		if($find == 'find'){
			$this->total    = $data->count();
			$this->limit    = ($opt['limit'] != '')   ? intval($opt['limit'])  : 10;
			$this->offset   = ($opt['offset'] != '')  ? intval($opt['offset']) : 0;
			$this->dir      = ($opt['dir'] != '')     ? intval($opt['dir'])    : -1;
			$this->sort     = ($opt['sort'] != '')    ? $opt['sort']           : 'date.start';

			if(!$opt['noLimit']){
				$data->skip($this->offset);
				$data->limit($this->limit);
			}

			if(!$opt['noSort']){
				$data->sort(array($this->sort => $this->dir));
			}

			$explain = var_export($data->explain(), true);
		#	print_r($data->explain());
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
			$this->pre(
				"COND",     $cond,
				"SORT",     array($this->sort, $this->dir),
				"TOTAL",    $this->total,
				"EXPLAIN",  $explain
			);
		}

	#	echo (microtime(true) - $now);

		if(count($data) == 0) return array();

		///////////////////////

		if($flip) $data = array($data);

		if(is_array($opt['format'])){
			$data = $this->format(array(
				'data'         => $data,
			#	'orderDate'    => $opt['format']['orderDate']    ? : in_array('orderDate', $opt['format']),
				'date'         => $opt['format']['date']         ? : in_array('date', $opt['format']),
				'city'         => $opt['format']['city']         ? : in_array('city', $opt['format']),
				'organisateur' => $opt['format']['organisateur'] ? : in_array('organisateur', $opt['format']),
			));
		}

		if($flip) $data = $data[0];

		///////////////////////

		return $data;
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public function del(array $opt){

		if(!empty($opt['_id'])){
			$cond = array('_id' => new MongoId($opt['_id']));
		}else
		if(!empty($opt['id'])){
			$cond = array('id' => $opt['id']);
		}else{
			return false;
		}

		$city = $this->get($cond);

		if(empty($city)) return true;

		$this->_id($city['_id'])->debug(false)->remove();

		return true;
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public function near(array $opt){

		if(!empty($opt['debug'])) $this->pre($opt);

		$mon  = $this->mongoAuth();
		$db   = $mon->selectDB($this->db);

		$find = (array_key_exists('_id', $opt) && !is_array($opt['_id'])) ? 'findOne' : 'find';
		$cond = $this->cond($opt, $find);

		$near = array(
			$this->helperFloat(str_replace(',', '.', $opt['pos'][0])),
			$this->helperFloat(str_replace(',', '.', $opt['pos'][1]))
		);

		$cmd = array(
			'geoNear' => $this->collection,
			'near'    => $near,
			'num'     => $opt['limit'] ?: 50
		);

		//////////////////////////////////////////////////////////////////////////////
		//////////////////////////////////////////////////////////////////////////////

		$data = $db->command($cmd);
		$data = $data['results'];

		if(count($data) > 0){

			foreach($data as $n => $d){
				$obj         = $d['obj'];
				$obj['_dis'] = $d['dis'];
				$obj['_km']  = $d['dis'] * 111.325;
				$data[$n]    = $obj;
			}

			$this->total = count($data);
		}else{
			$this->total = 0;
			$this->reset();
			$data = array();
		}

		if(!empty($opt['debug'])) $this->pre("COND", $cond, "TOTAL", $this->total, "DATA", $data);

		if(count($data) == 0) return array();

		///////////////////////

		if(is_array($opt['format'])){
			$data = $this->format(array(
				'data'         => $data,
				'orderDate'    => $opt['format']['orderDate']    ? : in_array('orderDate', $opt['format']),
				'date'         => $opt['format']['date']         ? : in_array('date', $opt['format']),
				'city'         => $opt['format']['city']         ? : in_array('city', $opt['format']),
				'organisateur' => $opt['format']['organisateur'] ? : in_array('organisateur', $opt['format']),
			));
		}

		///////////////////////

		return $data;

	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public function update(array $opt){

		if(!empty($opt['_id'])){
			$cond = array('_id' => new MongoId($opt['_id']));
		}else
		if(!empty($opt['id'])){
			$cond = array('id' => $opt['id']);
		}else{
			return false;
		}

		$mon  = $this->mongoAuth();
		$db   = $mon->selectDB($this->db);
		$col  = $mon->selectCollection($db, $this->collection);

		$data = $opt['data'];
		$data['updated'] = new MongoDate();


		$job  = $col->update($cond, array('$set' => $data));

		return $job;
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public function updateFromRest($_id, $set){

		$mon  = $this->mongoAuth();
		$db   = $mon->selectDB($this->db);
		$col  = $mon->selectCollection($db, $this->collection);

	#	print_r(func_get_args());
	#	die('-');

		$job  = $col->update(
			array('_id' => new MongoId($_id)),
			array('$set' => $set)
		);

		return $job;
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
// Formattage de l'array manifestation retourné
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public function format(array $opt){

		if(count($opt['data']) == 0) return $opt['data'];

		$data         = $opt['data'];
	#	$orderDate    = is_array($opt['orderDate']) ? true : ($opt['orderDate'] === true);
		$date         = is_array($opt['date']) ? true : ($opt['date'] === true);
		$city         = is_array($opt['city']) ? true : ($opt['city'] === true);
		$organisateur = is_array($opt['organisateur']) ? true : ($opt['organisateur'] === true);

		$cities = $organisateurs = array();

		foreach($data as $n => $e){
			$data[$n]['_id']     = (string) $e['_id'];

			if(!empty($e['created'])) $data[$n]['created'] = $e['created']->sec;
			if(!empty($e['updated'])) $data[$n]['updated'] = $e['updated']->sec;

			if($date && is_array($e['date']) && count($e['date']) > 0){
				foreach($e['date'] as $m => $d){
					$data[$n]['date'][$m]['start'] = $d['start']->sec;
					$data[$n]['date'][$m]['end']   = $d['end']->sec;
				}

			}

			if($city)           $cities[]        = $e['city']['_id'];
			if($organisateur)   $organisateurs[] = $e['organisateur']['_id'];
		}


		if($city && count($cities) > 0){
			$cities = $this->apiLoad('calendrierCity')->get(array(
				'_id'    => $cities,
				'format' => is_array($opt['city']) ? $opt['city'] : array()
			));

			foreach($data as $n => $e){
				$data[$n]['city'] = $cities[$e['city']['_id']];
			}
		}

		if($organisateur && count($organisateurs) > 0){
			$organisateurs = $this->apiLoad('calendrierOrganisateur')->get(array(
				'_id'    => $organisateurs,
				'format' => is_array($opt['organisateur']) ? $opt['organisateur'] : array()
			));

			foreach($data as $n => $e){
				$data[$n]['organisateur'] = $organisateurs[$e['organisateur']['_id']];
			}
		}

		return $data;
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
// A VERIFIER: Affecte un ID MVS pour la manifestion
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public function setId(array $opt){

		$mon  = $this->mongoAuth();
		$db   = $mon->selectDB($this->db);
		$col  = $mon->selectCollection($db, $this->collection);

		$cond = array('_id'  => new MongoId($opt['_id']));
		$set  = array('$set' => array('id' => $opt['id']));

		$job  = $col->update($cond, $set);

		return $job;
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
// Script de post traitement suite à la création ou la mise à jour d'une manifestation
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public function postUpsert($_id){

		// Création d'un brouillon s'il n'existe pas déjà
		$copy = $this->createBackup($_id);

		// Difference entre l'original et la version de base
		if($copy){
			$this->cleanBackup($_id);
		}

	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
// Determine les différences entre la version de BASE (celle qui est affiché partout) et le BACKYP (copie de sécurité)
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public function compareToBackup($_id){

		$manif = $this->get(array(
			'_id' => $_id
		));

		$base   = $manif;
		$backup = $base['backup'];
		$data   = array(
			'base'   => array(),
			'backup' => array(),
			'dates'  => array()
		);

		// DATES
		$dates       = array();
		$date_base   = is_array($base['date'])   ? $base['date']   : array();
		$date_backup = is_array($backup['date']) ? $backup['date'] : array();

		// Réunir les dates et voir les différences
		foreach($date_base as $n => $date){
			$dates[$date['start']->sec.'-'.$date['end']->sec]['base'] = $date;
		}

		foreach($date_backup as $n => $date){
			$dates[$date['start']->sec.'-'.$date['end']->sec]['backup'] = $date;
		}

		foreach($dates as $key => $both){
			if(array_key_exists('base', $both) && array_key_exists('backup', $both)){
				$left  = calendrier::flatten($both['base']);
				$right = calendrier::flatten($both['backup']);

				if(count(array_diff($left, $right)) > 0 OR count(array_diff($right, $left)) > 0){
					$dates[$key]['diff'] = true;
				}
			}
		}

		$data['dates'] = $dates;

		unset($base['backup'], $base['_id'], $base['updated'], $base['date']);
		unset($backup['updated'], $backup['date']);

		$base   = calendrier::flatten($base);
		$backup = calendrier::flatten($backup);
		$diff   = array_diff($base, $backup);

		if(count($diff) > 0){
			$keys = array_keys($diff);
			foreach($keys as $key){
				$data['base'][$key]   = $base[$key];
				$data['backup'][$key] = $backup[$key];
			}
		}

		return $data;
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
// Nettois le BACKUP s'il est identique à la version de BASE
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public function cleanBackup($_id){

		$has   = false;
		$diff  = $this->compareToBackup($_id);
		$dates = $diff['dates'] ? : array();

		foreach($dates as $both){
			if($both['diff']) $has = true;
		}

		if($diff['base'] != $diff['backup']) $has = true;

		// J'ai pas de différence => plus besoin du backup
		if(!$has) $this->removeBackup($_id);
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
// BACKUP + MVS.WARNING sont killé
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public function moderationAccept($_id){

		$data = $this->get(array(
			'_id' => $_id
		));

		if(empty($data)) return false;

		$mon  = $this->mongoAuth();
		$db   = $mon->selectDB($this->db);
		$col  = $mon->selectCollection($db, $this->collection);

		$col->update(
			array('_id' => new MongoId($_id)),
			array('$unset' => array(
				'backup'      => true,
				'mvs.warning' => true
			))
		);

		//-- Mise à jour chez MVS -------------------------------------------------------
		if(empty($data['id'])){
			$this->apiLoad('calendrierMvsSync')->manifestationCreation(array('_id' => $_id));
		}else{
			$this->apiLoad('calendrierMvsSync')->manifestationUpdate(array('_id' => $_id));
		}

		die('--');

		//-- Envois de l'email à l'organisateur -----------------------------------------
		$orga = $this->apiLoad('calendrierOrganisateur')->get(array(
			'_id' => $data['organisateur']['_id']
		));

		if(!empty($orga['email']) && filter_var($orga['email'], FILTER_VALIDATE_EMAIL) !== false){
			$this->apiLoad('sendMail')->mandrill(array(
				'template' => 'moderation-manifestation-accepted',
				'message'  => array(
					'track_opens' => true,
					'tags'        => array('supercalendrier', 'moderation', 'accepted'),
					'to'          => array(
						array('type' => 'to', 'email' => $orga['email'])
					),
					'global_merge_vars' => array(
						array('name' => 'name', 'content' => $data['name'])
					)
				)
			));
		}

		return true;
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
// Les data de BACKUP sont réinjecté à la racine, BACKUP+MVS.WARNING sont killés
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public function moderationReject($_id, $opt){

		$me = $this->get(array(
			'_id' => $_id
		));

		if(empty($me) OR empty($me['backup'])) return false;

		$mon  = $this->mongoAuth();
		$db   = $mon->selectDB($this->db);
		$col  = $mon->selectCollection($db, $this->collection);

		// TODO: vérifier si les 2 requêtes peuvent être réunines
		// il semble que les $set+$unset ne peuven être fait en même temps !

		$col->update(
			array('_id' => new MongoId($_id)),
			array('$set'   => $me['backup'])
		);

		$col->update(
			array('_id' => new MongoId($_id)),
			array('$unset' => array(
				'backup'      => true,
				'mvs.warning' => true
			))
		);

		//-- Mise à jour chez MVS -------------------------------------------------------
		if(empty($me['id'])){
			$this->apiLoad('calendrierMvsSync')->manifestationCreation(array('_id' => $_id));
		}else{
			$this->apiLoad('calendrierMvsSync')->manifestationUpdate(array('_id' => $_id));
		}

		//-- Envois de l'email à l'organisateur -----------------------------------------
		$orga = $this->apiLoad('calendrierOrganisateur')->get(array(
			'_id' => $me['organisateur']['_id']
		));

		if(!empty($orga['email']) && filter_var($orga['email'], FILTER_VALIDATE_EMAIL) !== false){
			$this->apiLoad('sendMail')->mandrill(array(
				'template' => 'moderation-manifestation-rejected',
				'message'  => array(
					'track_opens' => true,
					'tags'        => array('supercalendrier', 'moderation', 'rejected'),
					'to'          => array(
						array('type' => 'to', 'email' => $orga['email'])
					),
					'global_merge_vars' => array(
						array('name' => 'name',    'content' => $me['name']),
						array('name' => 'message', 'content' => nl2br(trim($opt['message'])))
					)
				)
			));
		}

		return true;
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
// Création d'une version de secours de la manif .backup
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public function createBackup($_id){

		$me = $this->get(array(
			'_id' => $_id
		));

		// On ne copie par l'original si il existe déjà
		if(array_key_exists('backup', $me)) return true;

		$copy = $me;
		unset($copy['_id']);

		$mon  = $this->mongoAuth();
		$db   = $mon->selectDB($this->db);
		$col  = $mon->selectCollection($db, $this->collection);

		$col->update(
			array('_id' => new MongoId($_id)),
			array('$set' => array(
				'backup' => $copy
			))
		);

		return true;
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
// Supprime la version de backup de la manifestation
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public function removeBackup($_id){

		$mon  = $this->mongoAuth();
		$db   = $mon->selectDB($this->db);
		$col  = $mon->selectCollection($db, $this->collection);

		$col->update(
			array('_id' => new MongoId($_id)),
			array('$unset' => array('backup' => true))
		);

		return true;
	}


////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
// Allege la base de donnée en supprimant les manifestation OFF=TRUE (qui ne sont pas affiché sur le site)
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public function purge(){

		$mon   = $this->mongoAuth();
		$db    = $mon->selectDB($this->db);
		$col   = $mon->selectCollection($db, $this->collection);

		$col->remove(array('off' => true));

		return true;
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
// Compte les manifs qui sont OFF=TRUE
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public function purgeCount(){

		$mon   = $this->mongoAuth();
		$db    = $mon->selectDB($this->db);
		$col   = $mon->selectCollection($db, $this->collection);
		$count = $col->find(array('off' => true))->count();

		return intval($count);
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
// Retrouve le geocode pour l'adresse
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public function fixGeo(array $opt){

		$manif = $this->get(array(
			'_id'    => $opt['_id'],
			'format' => array()
		));

		if(empty($manif)) return false;

		// GEO
		$add = $manif['geo']['address'].' '.$manif['city']['zip'].' '.$manif['city']['name'];
		$gps = $this->apiLoad('calendrierGeocode')->addresseToGPS(array(
			'address' => $add
		));



		$this->pre($gps, $manif);

		die();

	}



////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


	/**
	 * Retourne un array de FLOAT si l'input est une STRING
	 * @param $coo
	 * @return array
	 * @throws Exception
	 */
	public function gps($coo){
		if(is_array($coo)){
			return array(floatval($coo[0]), floatval($coo[1]));
		}else
		if(is_string($coo)){
			list($a, $b) = explode(',', $coo);
			$coo = array(floatval($a), floatval($b));
			return $coo;
		}else{
			throw new Exception('Input is neither a SRTING nor an ARRAY');
		}
	}

	/**
	 * Définir comme off=true OU kill le champs 'off'
	 * @param array $opt
	 * @return bool
	 */
	public function off(array $opt){

		if(empty($opt['_id'])) return false;

		$mon  = $this->mongoAuth();
		$db   = $mon->selectDB($this->db);
		$col  = $mon->selectCollection($db, $this->collection);

		$action = ($opt['undo']) ? '$unset' : '$set';

		$cond   = array('_id' => new MongoId($opt['_id']));
		$set    = array($action => array('off' => true));
		$option = array('multiple' => true);
		$job    = $col->update($cond, $set, $option);

		return $job;
	}

	/**
	 * Effectue un MAP/REDUCE pour trouver le nombre de manifestation par département
	 */
	public function manigestationMapReduceDept(){

		$mon   = $this->mongoAuth();
		$db    = $mon->selectDB($this->db);

		$map = new MongoCode("function() {
			emit(this.city.dep, 1);
		}");

		$reduce = new MongoCode("function(key, values) {
			return Array.sum(values);
		}");

		$args = array(
			'mapreduce' => $this->collection(),
			'out'       => $this->collection() . '_dept',
			'map'       => $map,
			'reduce'    => $reduce,
			'query'     => array(
				'city.dep' => array('$exists' => true),
				'off'      => array('$exists' => false),
				'date'     => array(
					'$elemMatch' => array(
						'start' => array('$gte' => new MongoDate(strtotime(date('Y-m-d').' 00:00:00'))),
					//	'end'   => array('$lte' => new MongoDate(strtotime(date('Y-m-d').' 23:59:59')))
					)
				)
			)
		);

		$cmd = $db->command($args);

		$this->pre($map);
		$this->pre($reduce);
		$this->pre($args);
		$this->pre($cmd);
	}


	/**
	 * Retourne la liste des départements avec les données issues du mapReduce "manigestationMapReduceDept"
	 * Retourne la même liste que calendrierDepartement->departement()
	 * @return mixed
	 */
	public function manifestationByDept(){

		// RAW
		$mon = $this->mongoAuth();
		$db  = $mon->selectDB($this->db);
		$col = $mon->selectCollection($db, $this->collection . '_dept');

		$raw = $col->find(array());
		$raw = iterator_to_array($raw);

		$count = array();
		foreach($raw as $e){
			if(intval($e['_id']) > 0) $count[$e['_id']] = $e['value'];
		}

		// DEPTS
		$depts = $this->apiLoad('calendrierDepartement')->departement();
		foreach($depts as $n => $e){
			$depts[$n]['count'] = $count[$e['code']];
		}

		return $depts;
	}

	/**
	 * Retourne la liste des régions avec les données issues du mapReduce "manigestationMapReduceDept"
	 * recoupé par Dept
	 * Retourne la même liste que calendrierDepartement->regionGet()
	 * @return mixed
	 */
	public function manifestationByRegion(){

		// RAW
		$mon = $this->mongoAuth();
		$db  = $mon->selectDB($this->db);
		$col = $mon->selectCollection($db, $this->collection . '_dept');

		$raw = $col->find(array());
		$raw = iterator_to_array($raw);

		$count = array();
		foreach($raw as $e){
			if(intval($e['_id']) > 0) $count[$e['_id']] = $e['value'];
		}

		// Region & Depts
		$depts   = $this->apiLoad('calendrierDepartement')->departement();
		foreach($depts as $d){
			$depts_[$d['code']] = $d['name'];
		}
		$depts = $depts_;

		$regions = $this->apiLoad('calendrierDepartement')->region();
		foreach($regions as $n => $e){
			foreach($e['dep'] as $dep){
				$regions[$n]['count'] += $count[$dep];
				$regions[$n]['depts'][] = array(
					'name'  => $depts[$dep],
					'code'  => $dep,
					'count' => $count[$dep]
				);
			}
		}

		return $regions;
	}









	/**
	 * Modifie une manifestation et la met en mode:wait_email
	 *
	 * @param $opt
	 * opt.moderation (indique le type de modération: rien=add remove=suppression update=mise à jour
	 * opt.manifestation (array indiquant les infos à mettre à jour — dans le cas d'une mise à jour)
	 *
	 * @return bool
	 */
	public function manifestationTemp($opt){

		$_id    = $opt['_id'];
		$editor = $opt['editor'];

		$mon  = $this->mongoAuth();
		$db   = $mon->selectDB($this->db);
		$col  = $mon->selectCollection($db, $this->collection);

		$cond = array('_id' => new MongoId($_id));
		$data = array(
			'mode'       => 'wait_email',
			'moderation' => $opt['moderation'],
			'editor'     => $editor
		);

		// Si j'ai des modifications à apporter, on les stock dans .temp
		if(!empty($opt['manifestation'])){

			$manif = $opt['manifestation'];

			// Si j'ai des images, les déplacer au bon endroit
			if(!empty($manif['images'])){
				$manif['images'] = $this->manifestationImages($manif['images'], $opt['poster']);
			}

			$this->manifestationUpdateManif($_id, $manif); // Retourn un _id pas un booleen
		}

		$job = $col->update($cond, array('$set' => $data));

		return $job;
	}

	/**
	 * Passer la manifestation en mode:wait_moderation, c'est à LVA de modérer manuellement
	 *
	 * @param $_id
	 *
	 * @return bool
	 */
	public function manifestationEmailConfirmed($_id){

		$mon  = $this->mongoAuth();
		$db   = $mon->selectDB($this->db);
		$col  = $mon->selectCollection($db, $this->collection);

		$cond = array('_id' => new MongoId($_id));
		$data = array(
			'$set' => array(
				'mode' => 'wait_moderation'
			)
		);

		$job = $col->update($cond, $data);

		return $job;
	}

	/**
	 * Supprime les data en cours de modération (editor / temp / mode)
	 *
	 * @param $_id
	 *
	 * @return bool
	 */
	public function manifestationEmailRejected($_id){

		$mon  = $this->mongoAuth();
		$db   = $mon->selectDB($this->db);
		$col  = $mon->selectCollection($db, $this->collection);

		$cond = array('_id' => new MongoId($_id));
		$data = array(
			'$unset' => array(
				'mode'   => '',
				'temp'   => '',
				'editor' => ''
			)
		);

		$job = $col->update($cond, $data);

		return $job;
	}


// MODERATION DEPUIS LE BO

	/**
	 * Met à jour les infos à modérer, remet la manif dans le bon état et se synchronise avec MVS
	 *
	 * @param $opt
	 * @return bool
	 */
	public function manifestationModerateConfirmed($opt){

		$manif = $opt['manifestation'];
		$id    = $manif['_id'];
		$vdb   = $this->get(array('_id' => $id));

		// Mettre à jour La ville + Region + Département
		$manif['city']          = $this->manifestationCity($manif['city']);
		$manif['geo']['region'] = $manif['city']['region'];
		$manif['geo']['dept']   = $manif['city']['dep'];

		// Mise à jour des dates
		if(!empty($manif['date'])){
			$date = array();

			foreach($manif['date'] as $e){

				list($d, $m, $y) = explode('/', $e['start']);
				$e['start'] = $y.'-'.$m.'-'.$d;

				list($d, $m, $y) = explode('/', $e['end']);
				$e['end'] = $y.'-'.$m.'-'.$d;

				$datetime1 = new DateTime($e['start']);
				$datetime2 = new DateTime($e['end']);
				$interval  = $datetime1->diff($datetime2);
				$days      = $interval->days + 1;

				$start = new MongoDate(strtotime($e['start'].' 00:00:00'));
				$end   = $this->apiLoad('calendrierManifestationDate')->day(strtotime($e['start'].' 00:00:00'), $days);

				$date[] = array(
					'start'     => $start,
					'end'       => $end,
					'days'      => $days,
					'canceled'  => ($e['annule'] == '1'),
					'postponed' => ($e['reporte'] == '1'),
					'unsure'    => ($e['sous_reserve'] == '1')
				);
			}

			$manif['date'] = $date;
		}

		// Category ?
		if($manif['mvs']['category']){
			$new_cat = $manif['mvs']['category'];
			$manif['mvs'] = $vdb['mvs'];
			$manif['mvs']['category'] = $new_cat;
		}

		// Mise à jour en local
		$mon  = $this->mongoAuth();
		$db   = $mon->selectDB($this->db);
		$col  = $mon->selectCollection($db, $this->collection);

		$cond = array('_id' => new MongoId($id));

		unset($manif['_id']);

	#	print_r($manif);
	#	die();

		$col->update($cond, array('$set'   => $manif));
		$col->update($cond, array('$unset' => array('temp' => '', 'mode' => '', 'editor' => '')));


		// Ajouter Distant
		if(empty($vdb['id'])){
			$this->apiLoad('calendrierMvsSync')->manifestationCreation(array(
				'_id' => $id
			));
		}else{
			$this->apiLoad('calendrierMvsSync')->manifestationUpdate(array(
				'_id' => $id
			));
		}

		// Envois des emails
		$display = 'http://'.$_SERVER['HTTP_HOST'].$this->manifestationPermalink($vdb);
		$mail = array(
			'template' => 'organisateur-moderation-confirmed',
			'message'  => array(
				'track_opens' => true,
				'tags'        => array('supercalendrier', 'moderation', 'confirmed'),
				'to'          => array(
					array('type' => 'to',  'email' => $vdb['editor']['email']),

					// Todo: manque l'email de l'annonce
					//array('type' => 'bcc', 'email' => EMAIL_BCC),
				),
				'global_merge_vars' => array(
					array('name' => 'manifestation', 'content' => $manif['name']),
					array('name' => 'link',          'content' => $display),
				)
			)
		);


		$this->apiLoad('sendMail')->mandrill($mail);

		return true;
	}

	/**
	 * Rejette les demandes de mise à jour
	 *
	 * @param $id
	 * @return bool
	 */
	public function manifestationModerateRejected($id){

		$manif = $this->get(array('_id' => $id));

		// Mise à jour en local
		$mon  = $this->mongoAuth();
		$db   = $mon->selectDB($this->db);
		$col  = $mon->selectCollection($db, $this->collection);

		$cond = array('_id' => new MongoId($id));
		$data = array(
			'$unset' => array('temp' => '', 'mode' => '', 'editor' => '')
		);

		$col->update($cond, $data);

		// Envois des emails
		/*$mail = array(
			'template' => 'organisateur-moderation-rejected',
			'message'  => array(
				'track_opens' => true,
				'tags'        => array('supercalendrier', 'moderation', 'rejected'),
				'to'          => array(
					array('type' => 'to',  'email' => $manif['editor']['email']),
					// Todo: manque l'email de l'annonce
					//array('type' => 'bcc', 'email' => EMAIL_BCC),
				),
				'global_merge_vars' => array(
					array('name' => 'manifestation', 'content' => $manif['name']),
				)
			)
		);


		$this->apiLoad('sendMail')->mandrill($mail);*/

		return true;
	}

	/**
	 * Supprime la manifestation en local et en distant
	 *
	 * @param $_id
	 * @return bool
	 */
	public function manifestationModerateRemove($_id){

		// Suppression en local
		$mon  = $this->mongoAuth();
		$db   = $mon->selectDB($this->db);
		$col  = $mon->selectCollection($db, $this->collection);

	    $col->remove(array('_id' => new MongoId($_id)));

		// Suppresion distante
		$this->apiLoad('calendrierMvsSync')->manifestationDelete(array(
			'_id' => $_id
		));

		return true;
	}




	/**
	 * Range les images depuis /supercalendrier/temp/ vers le bon dossier classé par date et retourne un array d'URL
	 *
	 * @param array $images
	 * @param string $poster Determine si l'image est le poster via son URL
	 * @return array
	 */
	private function manifestationImages(Array $images, $poster=''){

		if(empty($images)) return $images;

		foreach($images as $n => $e){
			$isPoster = false;

			if(strpos($e, '/temp/') !== false){

				$source      = KROOT . $e;
				$ext         = pathinfo($source, PATHINFO_EXTENSION);
				$folder      = KROOT . '/media/upload/supercalendrier/'.date("Y/m/d");
				$destination = $folder . '/' . md5($e) . '.' . $ext;

				#$this->pre($source, $folder, $destination);

				umask(0);
				if(!is_dir($folder)) mkdir($folder, 0755, true);

				if(file_exists($source) && !file_exists($destination)) rename($source, $destination);

				if(KROOT.$poster == $source) $isPoster = true;
			}else{
				$destination = $e;
			}

			$destination = str_replace(KROOT, '', $destination);

			if(!is_file(KROOT.$destination) && !file_exists(KROOT.$destination)){
				unset($images[$n]);
				continue;
			}

			// On mémorise le nouvel endroit pour mettre à jour l'annonce
			$images[$n] = array(
				'url' => $destination
			);

			// Est-ce que cette image est le poster ?
			if($isPoster) $images[$n]['poster'] = true;
		}

		return $images;
	}

	/**
	 * Création d'un ou de plusieurs manifestation depuis des data communes (type différents)
	 *
	 * @param array $opt
	 * @return array $ids Liste des ID des manif qu'on vient de créer
	 */
	public function manifestationDispatch(Array $opt){

		$type = $opt['type'];
		$ids = array();

		// On s'occupe des images si besoin
		if(!empty($opt['manifestation']['images'])){
			$opt['manifestation']['images'] = $this->manifestationImages($opt['manifestation']['images'], $opt['poster']);
		}

		// Pour tous les types (auto/moto/collection) t=type c=category
		foreach($type as $t => $c){
			$t = $this->apiLoad('calendrierManifestationType')->name($t);

			// 1 Création d'un organisateur depuis EDITOR
			$org_id = $this->manifestationCreationOrganisateur($t['id'], $opt['editor']);

			// 2 Création d'une manifestation
			$manif_id = $this->manifestationCreationManif($t['id'], $c, $opt['manifestation'], $org_id, $opt['editor']);

			$ids[] = $manif_id;
		}

		return $ids;
	}

	/**
	 * Ajoute un organisateur et l'injecte dans MVS
	 *
	 * @param $type
	 * @param $editor
	 * @return MongoID
	 */
	private function manifestationCreationOrganisateur($type, $editor){

		$data = array(
			'created'   => new MongoDate(),
			'updated'   => new MongoDate(),
			'name'      => $editor['organisation'],
			'firstname' => $editor['name'],
			'lastname'  => $editor['lastname'],
			'email'     => $editor['email'],
			'phone'     => $editor['phone'],
			'address'   => $editor['address'],
		#	'zip'       => $editor['zip'],
			'city'      => $this->manifestationCity($editor['city']),
			'rubrique'  => $type
		);

		$orga = new calendrierOrganisateur();
		$orga->set($data)->debug(false)->save();
		$_id = $orga->_id();

		// MVS
		$this->apiLoad('calendrierMvsSync')->organisateurCreation(array(
			'_id' => $_id
		));

		return $_id;
	}

	/**
	 * Ajout une manifestation et retourne son ID
	 *
	 * @param $type
	 * @param $cat
	 * @param $manif    Array   Les infos de la manif
	 * @param $id_org   string  L'ID que l'on vient de créer
	 * @param $editor   Array   Les infos de l'editeur
	 *
	 * @return string
	 */
	private function manifestationCreationManif($type, $cat, $manif, $id_org, $editor){

	#	$this->pre(func_get_args());
	#	die();

		$orga = $this->apiLoad('calendrierOrganisateur')->get(array(
			'_id'    => $id_org,
			'format' => array()
		));

		// gérer la ville
		$city   = $this->apiLoad('calendrierCity')->get(array(
			'_id'    => $manif['city'],
			'format' => array()
		));
		$zip    = substr($city['zip'], 0, 2);
		$dept   = $this->apiLoad('calendrierDepartement')->departementGet(array('code' => $zip));
		$region = $this->apiLoad('calendrierDepartement')->regionGet(array('dep' => $dept['code']));

		$data = array(
			'created'      => new MongoDate(),
			'updated'      => new MongoDate(),
			'mode'         => 'wait_email',
			'organisateur' => array(
				'_id'   => $orga['_id'],
				'id'    => $orga['id'],
				'name'  => $orga['name'],
				'email' => $orga['email'],
			),
			'mvs'          => array(
				'type'     => intval($type),
				'category' => intval($cat)
			),
			'editor' => $editor,
			'temp'         => array(
				'name'         => $manif['name'],
				'images'       => $manif['images'],
				'city'         => $this->manifestationCity($manif['city']),

				'schedule'     => $manif['schedule'],
				'opening'      => $manif['opening'],

				'number'       => $manif['number'],
				'pro'          => ($manif['indoor'] == 1),
				'individual'   => ($manif['individual'] == 1),
				'resident'     => ($manif['resident'] == 1),

				'indoor'       => ($manif['indoor'] == 1),
				'outdoor'      => ($manif['outdoor'] == 1),

				'free'         => ($manif['free'] == 1),
				'paying'       => ($manif['paying'] == 1),
				'price'        => floatval(str_replace(',', '.', trim($manif['price']))),

			#	'phone'        => $manif['phone'],
				'phones'       => $manif['phones'],
			#	'fax'          => $manif['fax'],
				'email'        => $manif['email'],
				'web'          => $manif['web'],


				'presentation' => $manif['presentation'],
				'presentation_web' => $manif['presentation_web'],
			#	'resume_date'  => $manif['resume_date'],

				'geo'    =>  array(
					'address' => $manif['geo']['address'],
					'comment' => $manif['geo']['comment'],
					'country' => $manif['geo']['country'],
				),
			)
		);


		// GPS + ZOOM
		if(!empty($manif['geo']['gps']))  $data['temp']['geo']['gps']  = $manif['geo']['gps'];
		if(!empty($manif['geo']['zoom'])) $data['temp']['geo']['zoom'] = $manif['geo']['zoom'];

		$data['temp']['geo']['region'] = $data['temp']['city']['region'];
		$data['temp']['geo']['dept']   = $data['temp']['city']['dep'];

#$this->pre($data);
#die();

		foreach($manif['dates'] as $e){
			list($d, $m, $y) = explode('/', $e['start']);
			$e['start'] = $y.'-'.$m.'-'.$d;

			list($d, $m, $y) = explode('/', $e['end']);
			$e['end'] = $y.'-'.$m.'-'.$d;

			$datetime1 = new DateTime($e['start']);
			$datetime2 = new DateTime($e['end']);
			$interval  = $datetime1->diff($datetime2);
			$days      = $interval->days + 1;

			$start = new MongoDate(strtotime($e['start'].' 00:00:00'));
			$end   = $this->apiLoad('calendrierManifestationDate')->day(strtotime($e['start'].' 00:00:00'), $days);

			$myDates[] = array(
				'start' => $start,
				'end'   => $end,
				'days'  => $days
			);
		}

		// Classer par ordre croissant les dates
		$data['temp']['date'] = $myDates;
		usort($data['temp']['date'], function($a, $b){
			return $a['start']->sec - $b['start']->sec;
		});

		// Sauver la manif
		$manif = new calendrierManifestation();
		$manif->set($data)->debug(false)->save();
		$_id = $manif->_id();

		return $_id;
	}

	/**
	 * Met à jour une manifestation dans (tmp) Depuis le front, quand on la met en tmp email
	 *
	 * @param $_id
	 * @param $manif Array des infos de la manif
	 *
	 * @return string
	 */
	private function manifestationUpdateManif($_id, $manif){

		// gérer la ville
		$city   = $this->apiLoad('calendrierCity')->get(array(
			'_id'    => $manif['city'],
			'format' => array()
		));
		$zip    = substr($city['zip'], 0, 2);
		$dept   = $this->apiLoad('calendrierDepartement')->departementGet(array('code' => $zip));
		$region = $this->apiLoad('calendrierDepartement')->regionGet(array('dep' => $dept['code']));
		$orig   = $this->get(array('_id' => $_id));

		$data = array(
			'updated'      => new MongoDate(),
			'temp'         => array(
				'name'         => $manif['name'],
				'images'       => $manif['images'],
				'city'         => $this->manifestationCity($manif['city']),

				'schedule'     => $manif['schedule'],
				'opening'      => $manif['opening'],

				'number'       => $manif['number'],
				'pro'          => ($manif['indoor'] == 1),
				'individual'   => ($manif['individual'] == 1),
				'resident'     => ($manif['resident'] == 1),

				'indoor'       => ($manif['indoor'] == 1),
				'outdoor'      => ($manif['outdoor'] == 1),

				'free'         => ($manif['free'] == 1),
				'paying'       => ($manif['paying'] == 1),
				'price'        => $manif['price'],

			#	'phone'        => $manif['phone'],
				'phones'       => $manif['phones'],
			#	'fax'          => $manif['fax'],
				'email'        => $manif['email'],
				'web'          => $manif['web'],


				'presentation'     => $manif['presentation'],
				'presentation_web' => $manif['presentation_web'],
			#	'resume_date'      => $manif['resume_date'],

				'geo'    =>  array(
					'address' => $manif['geo']['address'],
					'comment' => $manif['geo']['comment'],
					'country' => $manif['geo']['country'],
				)
			)
		);

		// GPS + ZOOM
		if(!empty($manif['geo']['gps']))  $data['temp']['geo']['gps']  = $manif['geo']['gps'];
		if(!empty($manif['geo']['zoom'])) $data['temp']['geo']['zoom'] = $manif['geo']['zoom'];

		$data['temp']['geo']['region'] = $data['temp']['city']['region'];
		$data['temp']['geo']['dept']   = $data['temp']['city']['dep'];


		// Ne pas perdre les infos relarive aux date déjà en place
		$orig_date = $orig['date'];
		if(!is_array($orig_date)) $orig_date = array();

		foreach($orig_date as $n => $e){
			$orig_date[$e['start']->sec.'-'.$e['end']->sec] = $e;
		}

		foreach($manif['dates'] as $e){
			list($d, $m, $y) = explode('/', $e['start']);
			$e['start'] = $y.'-'.$m.'-'.$d;

			list($d, $m, $y) = explode('/', $e['end']);
			$e['end'] = $y.'-'.$m.'-'.$d;

			$datetime1 = new DateTime($e['start']);
			$datetime2 = new DateTime($e['end']);
			$interval  = $datetime1->diff($datetime2);
			$days      = $interval->days + 1;

			$start = new MongoDate(strtotime($e['start'].' 00:00:00'));
			$end   = $this->apiLoad('calendrierManifestationDate')->day(strtotime($e['start'].' 00:00:00'), $days);

			$canceled  = false;
			$postponed = false;
			$unsure    = false;

			$orig_key = $start->sec.'-'.$end->sec;
			if(array_key_exists($orig_key, $orig_date)){
				$canceled  = $orig_date[$orig_key]['canceled'];
				$postponed = $orig_date[$orig_key]['postponed'];
				$unsure    = $orig_date[$orig_key]['unsure'];
			}

			$myDates[] = array(
				'start'     => $start,
				'end'       => $end,
				'days'      => $days,
				'canceled'  => $canceled,
				'postponed' => $postponed,
				'unsure'    => $unsure
			);
		}

		// Classer par ordre croissant les dates
		$data['temp']['date'] = $myDates;
		usort($data['temp']['date'], function($a, $b){
			return $a['start']->sec - $b['start']->sec;
		});

		// Sauver la manif
		$manif = new calendrierManifestation();
		$manif->_id($_id)->set($data)->debug(false)->save();

		return $_id;
	}






	private function manifestationCity($id_city){

		$city   = $this->apiLoad('calendrierCity')->get(array(
			'_id'    => $id_city,
			'format' => array()
		));

		$zip    = substr($city['zip'], 0, 2);

		$dept   = $this->apiLoad('calendrierDepartement')->departementGet(array('code' => $zip));
		$region = $this->apiLoad('calendrierDepartement')->regionGet(array('dep' => $dept['code']));

		return array(
			'_id'    => $city['_id'],
			'id'     => $city['id'],
			'name'   => $city['name'],
			'zip'    => $city['zip'],
			'dep'    => $dept['code'],
			'region' => $region['code'],
			'country' => $city['country']
		);

	}









// Fonctions utilisés sur le site pour manipuler des Manifestation /////////////////////////////////////////////////////

	/**
	 * Determine un array de mot clés pour une recherche libre
	 * @param $search
	 * @return array
	 */
	protected function searchToWords($search){

		$search = str_replace(array(',', '.', '!', '?'), ' ', $search);
		$search = trim($search);

		$words = explode(' ', $search);
		$words = array_merge($words, array_map(array($this, 'helperNoAccent'), $words));
		$words = array_filter($words, function($v){
			return !empty($v);
		});

		return array_unique($words);
	}

	/**
	 * Transforme les paramètres de recherche en une phrase pour le SEO
	 * (X evement en aquitaine le week-end du 17 janvier...
	 * @param $total
	 * @param $get
	 * @return string
	 */
	public function manifestationSearchSEO($total, $get){

		if($total == 0){
			$format = 'Aucun événement';
		}else
		if($total == 1){
			$format = 'Un seul événement';
		}else{
			$format = $total. ' événements';
		}

		if(!empty($get['cat'])){
			$api = $this->apiLoad('calendrierManifestationType');

			if(false !== strpos($_GET['cat'], 't')){
				$type = $api->nameFromId(substr($get['cat'], 1));
				$format .= ' de type '.$type['name'];
			}else
			if(false !== strpos($_GET['cat'], 'c')){
				$cat  = $api->typeFromId(substr($get['cat'], 1));
				$type = $api->nameFromSubId(substr($get['cat'], 1));
				$type = $api->name($type['key']);

				$format .= ' de type '.$type['name'].' > '.$cat['name'];
			}
		}

		if(!empty($get['dep'])){

			$dep = $this->apiLoad('calendrierDepartement')->departementGet(array(
				'code' => $get['dep']
			));

			$region = $this->apiLoad('calendrierDepartement')->regionGet(array(
				'dep' => $get['dep']
			));

			$format .= ' dans le '.$dep['code'].' ('.$dep['name'].', '.$region['name'].')';


	#		$this->pre($dep, $region);
		}
		#	387 événements « vide-greniers » dans le 01 avec le mot-clé « vieux »)

		if(!empty($get['date'])){
			list($d, $m, $y) = explode('.', $get['date']);
			$format .= ' le '.$this->helperDate($y.'-'.$m.'-'.$d, '%A %e %B %Y');
		}

		if(!empty($get['q'])){

			$tags = $this->searchToWords($get['q']);

			if(count($tags) == 1){
				$format .= ' avec le mot-clé « '.$tags[0].' »';
			}else{
				$format .= ' avec les mots-clés « '.implode(', ', $tags).' »';
			}

		}

		return $format;
	}

	/**
	 *  Retourne l'URL d'une manifestaion. $manif doit être un array complet pour la manif
	 * @param $manif
	 * @return string
	 */
	public function manifestationPermalink($manif){

		$type = $this->apiLoad('calendrierManifestationType')->nameFromId($manif['mvs']['type']);
		$cat  = $this->apiLoad('calendrierManifestationType')->typeFromId($manif['mvs']['category']);

		#$this->pre($manif['mvs'], $type, $cat);

		$url  = '/manifestation/'.$type['key'].'-'.$cat['key'].'-'.$manif['id'];

	#	$this->pre($manif);

		return $url;
	}

	/**
	 * Retourne un lien HTML bien formaté pour aller à la page de la manifestation
	 * @param      $manif
	 * @param null $name
	 * @return string
	 */
	public function manifestationPermalinkMarkup($manif, $name=NULL){
		$name = $name ?: $manif['name'];
		return '<a href="'.$this->manifestationPermalink($manif).'" alt="" title="">'.$name.'</a>';
	}

	/**
	 * Retourne un réseuém de la manifestation (affichage court, pour le moteur de recherche par exemple)
	 * @param array $e
	 * @param array $opt
	 * @return string
	 */
	public function manifestationResume(array $e, $opt=array()){

		$apiDate = $this->apiLoad('calendrierManifestationDate');
		$html = '';

		// Date
		$myNearestDate = $apiDate->nearest($e['date'], $opt['searchDate']);
		if($myNearestDate) $html = '<b>'.$apiDate->datePeriod($myNearestDate).'</b><br>';

		$html .= $this->manifestationAddresse($e).'<br>';

		if(!empty($e['schedule'])){
			$html .= 'Horaires: '.$e['schedule'].'<br>';
		}

		if(!empty($e['presentation'])){
			$html .= $e['presentation'].'<br>';
		}

		/*if(!empty($e['web'])){
			$link = $e['web'];
			if(strpos($link, 'http') === false) $link = 'http://'.$link;
			$html .= '<a href="'.$link.'" target="_blank" rel="nofollow">Site web</a>';
		}*/

		return $html;
	}

	/**
	 * Retourne le resumé complet de la manif (pour la page de détail)
	 * @param array $e
	 * @return string
	 */
	public function manifestationResumeFull(array $e){

		// Numéros
		$phones = $e['phones'] ? : array();
		$myFax  = $e['fax'];
		$myTel  = $e['phone'];

		foreach($phones as $p){
			if($p['type'] == 'fax' OR $p['type'] == 'tfax') $myFax = $this->manifestationFormatPhone($p);
			if($p['type'] == 'tel')                         $myTel = $this->manifestationFormatPhone($p);
		}

	#	$this->pre($myFax, $myTel);


		$html = '<p>'.$this->manifestationAddresse($e).'</p>';

		if(!empty($e['presentation_web'])){
			$html .= '<p>'.$e['presentation_web'].'</p>';
		}else
		if(!empty($e['presentation'])){
			$html .= '<p>'.$e['presentation'].'</p>';
		}

		if(!empty($e['schedule'])){
			$html .= '<p>Horaires: '.$e['schedule'].'</p>';
		}

		if(!empty($myTel)){
			$html .= '<p>Téléphone: '.$myTel.'</p>';
		}

		if(!empty($myFax)){
			$html .= '<p>Fax: '.$myFax.'</p>';
		}

		if(!empty($e['email'])){
			$html .= '<p>Contact email: '.$e['email'].'</p>';
		}

		if(!empty($e['web'])){
			$link = $e['web'];
			if(strpos($link, 'http') === false) $link = 'http://'.$link;

			$html .= '<p><a href="'.$link.'" target="_blank" rel="nofollow">Site web</a></p>';
		}

		return $html;
	}

	/**
	 * Format l'adresse en fonction du type de manifestation
	 * @param array $e
	 * @return string
	 */
	public function manifestationAddresse(array $e){

		$addresse = $e['geo']['address'].' ';

		if($e['mvs']['type'] == 1){
			$addresse .= $e['city']['zip'].' '.$e['city']['name'].' ';
		}

		if(!empty($e['geo']['comment'])){
			$addresse .= '('.$e['geo']['comment'].')';
		}

		return $addresse;
	}

	/**
	 * Format une string de numéro de téléphone en fonction de l'indicatif et du commentaire
	 * @param $e
	 * @return string
	 */
	public function manifestationFormatPhone($e){

		$number = $e['number'];

		if(!empty($e['indicatif'])) $number = '+'.$e['indicatif'].' '.$number;
		if(!empty($e['comment']))   $number .= ' ('.$e['comment'].')';

		return $number;
	}
}
