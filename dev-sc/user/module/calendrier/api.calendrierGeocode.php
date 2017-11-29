<?php

class calendrierGeocode extends calendrier {

// CORE ////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	public function __construct(){
		parent::__construct();

	#	$this->collection('geocode');
		$this->collection('geocache');
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
		// -- -- -- -- -- -- -- -- -- -- -- -- -- -- --

		$data = $col->$find($cond);
		$flip = false;

		if($find == 'find'){
			$this->total    = $data->count();
			$this->limit    = ($opt['limit'] != '')   ? intval($opt['limit'])  : 10;
			$this->offset   = (($opt['offset'] != '') ? intval($opt['offset']) : 0) * $this->limit;
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
				'data' => $data
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
			$data[$n]['_id'] = (string)$e['_id'];
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

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
// Extrait des informations simplistes sur les adresses n'ayant pas de coordonnées GPS
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public function resumeNoGPS(){

		$man  = $this->apiLoad('calendrierManifestation');

		$mon  = $this->mongoAuth();
		$db   = $mon->selectDB($this->db);
		$col  = $mon->selectCollection($db, $this->collection);

		$total = $col->find(array())->count();

		$nogps = $col->find(array(
			'gps' => array('$exists' => false)
		));

		$nogps = iterator_to_array($nogps);

		// Trouver les manifestation qui utilisent cette adresse
		if(count($nogps) > 0){
			foreach($nogps as $n => $e){
				$manifs = $man->get(array(
					'geo.address' => $e['address'],
					'city.zip'    => $e['zip'],
					'city.name'   => $e['city']
				));

				$nogps[$n]['manifs'] = $manifs;
			}
		}

		return array(
			'total' => $total,
			'nogps' => $nogps
		);
	}

	public function removeThisCache($_id){

		$mon  = $this->mongoAuth();
		$db   = $mon->selectDB($this->db);
		$col  = $mon->selectCollection($db, $this->collection);

		$cond = array('_id' => $_id);

		$this->pre($cond);

		$col->remove($cond);
	}

// CRONTAB//////////////////////////////////////////////////////////////////////////////////////////////////////////////

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
// Demander des addresse necessitant d'être traduite
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public function crontabGetGps(){
	}




// GOOGLE //////////////////////////////////////////////////////////////////////////////////////////////////////////////

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
// Convertit une addresse postal en coordonné GPS et met en cache le résultat
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public function addresseToGPS(array $opt){

		$add = $opt['address'];
		$url = 'https://maps.googleapis.com/maps/api/geocode/json?address='.urlencode($add).'&sensor=false';
		$raw = @file_get_contents($url);
		$goo = json_decode($raw, true);

#		$mon  = $this->mongoAuth();
#		$db   = $mon->selectDB($this->db);
#		$col  = $mon->selectCollection($db, $this->collection);

	#	echo $url."\n";
	#	print_r($goo);

		if(is_array($goo) && count($goo['results']) > 0){
			$first = $goo['results'][0];
			$gps   = $first['geometry']['location'];

			foreach($first['address_components'] as $ac){
				if(in_array('country', $ac['types'])) $pays = $ac['short_name'];
			}

			if($opt['print']) echo $add." >>>>>> (".$pays.") ".$gps['lat'].'/'.$gps['lng']."\n";

			return array(
				'ok'      => true,
				'gps'     => array(floatval($gps['lat']), floatval($gps['lng'])),
				'country' => $pays
			);

#			$col->insert(array(
#				'address' => $add,
#				'gps'     => array(floatval($gps['lat']), floatval($gps['lng']))
#			));
		}

#		$col->insert(array(
#			'address'  => $add,
#			'notfound' => true,
#			'google'   => $goo
#		));

		return array(
			'ok'  => false,
			'raw' => $goo
		);;
	}

}