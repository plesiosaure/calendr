<?php

class datamongoModel extends datamongo {

	protected   $model    = array();
	protected   $valid    = true;
	protected   $notValid = array();
	protected   $fake     = false;
	protected   $_id      = NULL;
	protected   $data     = array();
	public      $debug    = false;
	public      $total    = 0;
	public      $limit    = 100;
	public      $offset   = 0;
	public      $sort     = '';
	public      $dir      = -1;

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public  function __construct(){
		parent::__construct();
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public  function debug($d=NULL){
		if($d === NULL) return $this->debug;
		$this->debug = $d;
		return $this;
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public  function model($data=NULL){
		if($data == NULL) return $this->model;
		$this->model = $data;
		return $this;
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public  function modelKeys(){
		return array_keys($this->model);
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public  function modelKeyExists($key){
		return array_key_exists($key, $this->model());
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public  function create(){
		return $this->reset();
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public  function reset(){

		foreach($this->model as $k => $def){
			unset($this->model[$k]['valid']);
		}

		$this->valid(true);
		$this->fake(false);
		$this->data(array());
		$this->_id = NULL; // On ne peut pas utiliser l'appel this->_id(NULL)

		return $this;
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public  function adopt($data){

		$this->reset();

		if(is_string($data)){
			$this->data(array());
			$this->_id($data);
		}else{
			$this->data($data);
			$this->_id($data['_id']);
		}

		return $this;
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public  function sortByList($data, $list, $field='_id'){

		$tmp = $sort = array();
		foreach($data as $e){
			$tmp[$e[$field]] = $e;
		}

		foreach($list as $key){
			if(!empty($tmp[$key])) $sort[$key] = $tmp[$key];
		}

		return $sort;
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public  function fake($f=NULL){
		if($f === NULL) return $this->fake;
		$this->fake = $f;
		return $this;
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public  function valid($key=NULL, $value=NULL){

		if(is_bool($key) && $value == NULL){
			$this->valid = $key;
			return $this;
		}

		if($key == NULL && $value == NULL) return $this->valid;

		$valid = true;
		if($key != '_id'){
			$model = $this->dotGet($this->model, str_replace('.', '.child.', $key));

			if($model === NULL) throw new Exception('Key "'.$key.'" is not defined in MODEL via VALID()');

			if($model['check'] != NULL){
				$valid = preg_match('#'.$model['check'].'#', $value) == true;
			}else
			if($model['email'] == true){
				$valid = (filter_var($value, FILTER_VALIDATE_EMAIL) === FALSE) ? false : true;
			}else
			if($model['date'] == true){
				if(is_a($value, 'MongoDate')){
					$valid = true;
				}else
				if(!preg_match("#^[0-9]{4}-[0-9]{2}-[0-9]{2}$#", $value)){
					$valid = false;
				}
			}

			$this->model[$key]['valid'] = $valid;
			if(!$valid) $this->notValid($key);
		}

		return $this->valid($valid);
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
// Retourne la liste des KEYS pas valide, ou bien ajoute KEY a la liste non valide
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public  function notValid($key=NULL){
		if($key == NULL) return $this->notValid;
		if(!in_array($key, $this->notValid)) $this->notValid[] = $key;
		return $this;
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public  function _id($_id=NULL){
		if(count(func_get_args()) == 0) return $this->_id;
		$this->_id = is_string($_id) ? new MongoId($_id) : $_id;
		return $this;
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public  function data($data=NULL){
		if($data === NULL) return $this->data;
		$this->data = $data;
		return $this;
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public  function cond($opt, $find){

		$query   = array();
		$optKeys = array_keys($opt);
		$modKeys = array_keys($this->model);

		if($find == 'findOne'){
			$query['_id'] = is_string($opt['_id']) ? new MongoId($opt['_id']) : $opt['_id'];
		}

		foreach($optKeys as $key){

			$param = $opt[$key];

			// la KEY est _ID
			if($key == '_id'){

				// string to mongoId
				if(is_string($param)){
					$param = new MongoId($param);
				}else
				if(is_array($param)){
					foreach($param as $n => $v){
						if(!is_a($v, 'MongoId')) $param[$n] = new MongoId($v);
					}
					$param = array('$in' => $param);
				}else
				if(!is_a($param, 'MongoId')){
					throw new Exception('Key _id must be a string/array/MongoId');
				}

				$query[$key] = $param;
			}else

			// La KEY fait partie du MODEL
			if(in_array($key, $modKeys) && $this->model[$key]['get'] === true){

				if(is_bool($param) === true){
					$query[$key] = $param;
				}else
				if(is_string($param) OR is_integer($param) OR is_float($param)){
					$query[$key] = $param;
				}else
				if(is_array($param) && count($param) > 0){
					$tmpKeys = array_keys($param);

					if(is_integer($tmpKeys[0])){
						$query[$key] = (count($param) > 1) ? array('$in' => $param) : $param[0];
					}else
					if(substr($tmpKeys[0], 0, 1) == '$'){
						$query[$key] = $param;
					}else
					if(array_key_exists('raw', $param) AND is_array($param['raw'])){
						$query[$key] = $param['raw'];
					}
				}
			}else

			// La KEY commence par $, ou contient un "." : instruction Mongo
			if(strpos($key, '.') !== false OR substr($key, 0, 1) == '$'){
				$query[$key] = $param;
			}

		}

		return $query;
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public  function retrieve(){

		$args = func_get_args();

	#	var_dump($this->_id());
	#	var_dump($this->_id());
	#	var_dump(is_a($this->_id(), 'MongoID'));

	#	echo "\n\n\n\n\n\n";

		if(!is_a($this->_id(), 'MongoID')){
			throw new Exception('You can\'t retrieve an object without _id');
		}

		if(is_array($args[0])) $args[0] = array_merge($args[0], array('_id' => $this->_id()));

		return call_user_func_array(array($this, 'get'), $args);
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//  Définit un ensemble de clé/valeur pour le DATA courant
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public  function set($key, $value=NULL){

		if(is_string($key) && $value != ''){
			$key = array($key => $value);
			unset($value);
		}

		if(is_array($key) && $value == NULL){
			foreach($key as $k => $v){
				$this->set_($k, $v);
			}
		}

		return $this;
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	private function set_($k, $v){

		if($k != '_id'){

			$model = $this->dotGet($this->model, str_replace('.', '.child.', $k));

			/*echo "----\n";
			echo $k."\n";
			var_dump($model);
			echo "----\n\n\n\n";*/

			if($model === NULL) throw new Exception('Key '.$k.' is not defined in MODEL via SET');

			// TYPAGE ///////////////////////////////////////////////////

			if($model['boolean'] === true){
				$v = empty($v) ? false : true;
			}else
			if($model['integer'] === true){
				$v = intval($v);
			}else
			if($model['float'] === true){
				$v = floatval($v);
			}else
			if($model['gps'] === true){
				$lat = floatval($v[0]);
				$lng = floatval($v[1]);

				$v = array($lat, $lng);

				if($model['notIfEmpty'] && $v == array(0, 0)) $v = array();
			}

			/////////////////////////////////////////////////////////////

			if($model['set'] != NULL){
				if(is_callable($model['set'])) $v = call_user_func($model['set'], $v);
			}

			if($model['notIfEmpty'] && empty($v)) unset($v);

			if(isset($v)) $this->valid($k, $v);
		}

		$this->dotSet($this->data, $k, $v);
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	function dotGet(array &$arr, $path, $checkEmpty = true, $emptyResponse = false) {

		$pathElements = explode('.', $path);
		$path =& $arr;

		// Go through path elements
		foreach ($pathElements as $e) {
			// Check set
			#if (!isset($path[$e])) return $emptyResponse;

			// Check empty
			#if ($checkEmpty and empty($path[$e])) return $emptyResponse;

			// Update path
			$path =& $path[$e];
		}

		return $path;
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	function dotSet(array &$arr, $path, $value) {

		$pathElements = explode('.', $path);
		$path =& $arr;

		// Go through path elements
		foreach ($pathElements as $e) {
			// Check set
			#if (!isset($path[$e])) return $emptyResponse;

			// Check empty
			#if ($checkEmpty and empty($path[$e])) return $emptyResponse;

			// Update path
			$path =& $path[$e];
		}

		$path = $value;

	#	return
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
// Supprime l'enregistrement courant, _id doit etre définit
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public  function remove(){

		if($this->_id == NULL) return false;

		$mon  = $this->mongoAuth();
		$db   = $mon->selectDB($this->db());
		$col  = $mon->selectCollection($db, $this->collection());

		// -- -- -- -- -- -- -- -- -- -- -- -- -- -- --

		$cond   = array('_id' => $this->_id());
		$option = array('justOne' => true);
		$job    = $col->remove($cond, $option);

		if($this->debug()) $this->pre("REMOVE", "COND", $cond, "OPTION", $option, "JOB", $job);

		$this->reset();

		return $this;
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
// Affecte a DATA les champs par défaut
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	private function defaultValue(){

		foreach($this->model as $key => $def){
			$default = $this->model[$key]['default'];

			if(!array_key_exists($key, $this->data) && $default != NULL){
				$this->data[$key] = $default;
			}

		}

	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
// Déclenche un INSERT ou UPDATE avec le jeu de DATA
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public  function save(){

		if(count($this->data) == 0) return false;
		if(!$this->valid()) return false;

		$new = !array_key_exists('_id', $this->data());
		if(method_exists($this, 'beforeSave')) $this->beforeSave($new);

		$mon  = $this->mongoAuth();
		$db   = $mon->selectDB($this->db());
		$col  = $mon->selectCollection($db, $this->collection());

		// -- -- -- -- -- -- -- -- -- -- -- -- -- -- --

		if($this->_id() != NULL) $this->set('_id', $this->_id());

		if(!array_key_exists('_id', $this->data())){
			$this->defaultValue();

			$job = $this->fake() ? 'FAKE' : $col->insert($this->data, array());
			if($this->debug()) $this->pre("INSERT", "ADD", $this->data, "JOB", $job);

	#		var_dump($job);

			if($job == true && !$this->fake()){
				$this->_id($this->data['_id']);

			#	echo '@@@ save @@@'.PHP_EOL;
			#	$this->pre($this->data(), $this->_id());
			#	echo '@@@ save @@@'.PHP_EOL;
			}

		}else{
			$id = $this->data['_id'];
			if(is_string($id)) $id = new MongoId($id);
			unset($this->data['_id']);

			$cond   = array('_id' => $id);
			$set    = array('$set' => $this->data);
			$option = array('multiple' => false);

			$job    = $this->fake() ? 'FAKE' : $col->update($cond, $set, $option);


			if($this->debug()) $this->pre("UPDATE", "COND", $cond, "SET", $set, "OPTION", $option, "JOB", $job);
		}

		// -- -- -- -- -- -- -- -- -- -- -- -- -- -- --

		if(method_exists($this, 'afterSave')) $this->afterSave($new);

		return $this;
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
// Retourne le DATA courant sous forme d'array
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public  function toArray(){
		return $this->data;
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
// Retourn le DATA courant en JSON
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public  function toJson(){
		return $this->helperJsonEncode($this->data);
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
// Si le model est valide (tout le model) retourne la valeur de la BDD ($db), si non celle du champs ($fd)
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public  function formValue($db, $fd){
		if(is_string($db)) $db 	= stripslashes($db);
		if(is_string($fd)) $fd 	= stripslashes($fd);

		return $this->valid() ? $db : $fd;
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
// Si le model ne valid pas le champs retourne le message
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public  function formError($key, $message){
		return (isset($this->model[$key]['valid']) && $this->model[$key]['valid'] == false) ? $message : NULL;
	}
}