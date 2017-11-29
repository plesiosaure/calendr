<?php

class calendrierCity extends calendrier {

// CORE ////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	public  function __construct(){
		parent::__construct();

		$this->collection('city');
		$this->model(array(
			'name'   => array('get' => true),
			'zip'    => array('get' => true),
			'id'     => array('get' => true, 'integer' => true),
			'id_dep' => array('get' => true, 'integer' => true)
		));
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public  function get(array $opt){

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

		if(array_key_exists('search', $opt) && is_string($opt['search']) && $opt['search'] != ''){
			$regex = new MongoRegex('/'.$opt['search'].'/i');
			$cond['$or'][] = array('name' => $regex);
		}

		// -- -- -- -- -- -- -- -- -- -- -- -- -- -- --

		$data = $col->$find($cond);
		$flip = false;

		if($find == 'find'){
			$this->total    = $data->count();
			$this->limit    = ($opt['limit'] != '')   ? intval($opt['limit'])  : 10;
			$this->offset   = (($opt['offset'] != '') ? intval($opt['offset']) : 0) * $this->limit;
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
				'data' => $data
			));
		}

		if($flip) $data = $data[0];

		///////////////////////

		return $data;
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public  function del(array $opt){

		if(!empty($opt['_id'])){
			$cond = array('_id' => new MongoId($opt['_id']));
		}else
		if(!empty($opt['id'])){
			$cond = array('id' => intval($opt['id']));
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
	public  function update(array $opt){

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

		$job  = $col->update($cond, array('$set' => $opt['data']));

		return $job;
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public  function format(array $opt){

		if(count($opt['data']) == 0) return $opt['data'];

		$data = $opt['data'];

		foreach($data as $n => $e){
			$data[$n]['_id'] = (string) $e['_id'];
		}

		return $data;
	}


}