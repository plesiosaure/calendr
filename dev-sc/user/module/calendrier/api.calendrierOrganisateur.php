<?php

class calendrierOrganisateur extends calendrier {

	protected $title;
	protected $rubrique;

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	public function __construct(){
		parent::__construct();

		$this->title    = array('M.', 'M. MM', 'ME', 'MELLE', 'MLLE', 'MM', 'MM.', 'MME', 'PERE');
		$this->rubrique = array(1 => 'Collection', 2 => 'Auto', 3 => 'Moto');

		$this->collection('organisateur');
		$this->model(array(
			'id'          => array('get' => true, 'integer' => true),
			'created'     => array('date' => true),
			'updated'     => array('date' => true),
			'name'        => array(),
			'title'       => array(), // Civilité: Mr, Mme, Mlle etc...
			'firstname'   => array(),
			'lastname'    => array(),
			'fonction'    => array(),
			'address'     => array(),
			'city'        => array('array' => true, 'child' => array(
				'_id'           => array('get' => true),
				'id'            => array('integer' => true),
				'name'          => array()
			)),
			'phone'       => array(),
			'fax'         => array(),
			'mobile'      => array(),
			'email'       => array('email' => true),
			'web'         => array(),
			'commentaire' => array(),
			'zip'         => array(),
			'rubrique'    => array('integer' => true),
			'member'      => array('array' => true)
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

		if(!empty($opt['id'])){
			$find = 'findOne';
		}

		if(!empty($opt['rubrique'])){
			$cond['rubrique'] = intval($opt['rubrique']);
		}

		if(array_key_exists('member', $opt) && !empty($opt['member'])){
			$cond['member'] = intval($opt['member']);
		}

		if(array_key_exists('search', $opt) && is_string($opt['search']) && $opt['search'] != ''){
			$regex = new MongoRegex('/'.$opt['search'].'/i');
			$cond['$or'][] = array('name' => $regex);
		}

		if(array_key_exists('notMember', $opt) && !empty($opt['notMember'])){
			$cond['member'] = array('$nin' => $opt['notMember']);
		}

		// -- -- -- -- -- -- -- -- -- -- -- -- -- -- --

		$data = $col->$find($cond);
		$flip = false;

		if($find == 'find'){
			$this->total    = $data->count();
			$this->limit    = ($opt['limit'] != '')   ? intval($opt['limit'])  : 50;
			$this->offset   = ($opt['offset'] != '')  ? intval($opt['offset']) : 0;
			$this->dir      = ($opt['dir'] != '')     ? intval($opt['dir'])    : -1;
			$this->sort     = ($opt['sort'] != '')    ? $opt['sort']           : 'name';

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

		if(count($data) == 0) return array();

		///////////////////////

		if($flip) $data = array($data);

		if(is_array($opt['format'])){
			$data = $this->format(array(
				'data' => $data,
				'city' => $opt['format']['city'] ? : in_array('city', $opt['format']),

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
			$cond = array('id' => intval($opt['id']));
		}else{
			return false;
		}

		$data = $this->get(array_merge($cond, array('format' => array())));

		if(empty($data)) return true;

		$this->_id($data['_id'])->debug(false)->remove();

		return true;
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public function update(array $opt){

		if(!empty($opt['_id'])){
			$cond = array('_id' => new MongoId($opt['_id']));
		}else
		if(!empty($opt['id'])){
			$cond = array('id' => intval($opt['id']));
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
	public function format(array $opt){

		if(count($opt['data']) == 0) return $opt['data'];

		$data   = $opt['data'];
		$city   = is_array($opt['city']) ? true : ($opt['city'] === true);
		$cities = array();

		foreach($data as $n => $e){
			$data[$n]['_id']     = (string)$e['_id'];

			if(!empty($e['created'])) $data[$n]['created'] = $e['created']->sec;
			if(!empty($e['updated'])) $data[$n]['updated'] = $e['updated']->sec;

			if($city) $cities[] = $e['city']['_id'];
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

		return $data;
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public function setId(array $opt){

		$mon  = $this->mongoAuth();
		$db   = $mon->selectDB($this->db);
		$col  = $mon->selectCollection($db, $this->collection);

		$cond = array('_id'  => new MongoId($opt['_id']));
		$set  = array('$set' => array('id' => intval($opt['id'])));

		$job  = $col->update($cond, $set);

		return $job;
	}


//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
// Un USER est ajouté en PENDING sur cet ORGANISATEUR
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public function pending($opt){

		if(empty($opt['_id']) OR intval($opt['id_user']) == 0) return false;

		$org = $this->get(array(
			'_id' => $opt['_id']
		));

		if(empty($org)) return false;

		$mon = $this->mongoAuth();
		$db  = $mon->selectDB($this->db);
		$col = $mon->selectCollection($db, $this->collection);

		$col->update(
			array('_id' => $org['_id']),
			array(
				'$addToSet' => array(
					'pending' => intval($opt['id_user'])
				)
			)
		);

		return true;
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
// Supprime un USER de la liste des PENDING pour une ORGANISATEUT (on veut pas de toi !)
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public function pendingRemove($opt){

		if(empty($opt['_id']) OR intval($opt['id_user']) == 0) return false;

		$org = $this->get(array(
			'_id' => $opt['_id']
		));

		if(empty($org)) return false;

		$mon = $this->mongoAuth();
		$db  = $mon->selectDB($this->db);
		$col = $mon->selectCollection($db, $this->collection);

		$col->update(
			array('_id' => $org['_id']),
			array(
				'$pull' => array(
					'pending' => intval($opt['id_user'])
				)
			)
		);

		return true;
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
// Un USER est ajouté en MEMBER sur cet ORGANISATEUR (remove PENDING)
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public function member($opt){

		if(empty($opt['_id']) OR intval($opt['id_user']) == 0) return false;

		$org = $this->get(array(
			'_id' => $opt['_id']
		));

		if(empty($org)) return false;

		$mon = $this->mongoAuth();
		$db  = $mon->selectDB($this->db);
		$col = $mon->selectCollection($db, $this->collection);

		$col->update(
			array('_id' => $org['_id']),
			array(
				'$addToSet' => array(
					'member' => intval($opt['id_user'])
				),
				'$pull' => array(
					'pending' => intval($opt['id_user'])
				)
			)
		);

		return true;

	}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	public function title(){
		return $this->title;
	}

	public function rubrique(){
		return $this->rubrique;
	}

}