<?php

class calendrier extends datamongoModel {

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public  function __construct(){
		parent::__construct();
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public function nameFormat($e, $opt=''){

		$name = '<span class="name">'.$e['name'].'</span>';

		if(strpos($name, $e['city']['name']) === false){
			$name .= ' à <span class="city">'.$e['city']['name'].'</span> ';
				#	'<span class="zip">('.$e['city']['zip'].')</span>';
		}

		if(strpos($opt, 'DATE') !== false){
			$date  = $this->apiLoad('calendrierManifestationDate')->nearest($e['date']);
			$start = ucwords(strftime("%A %e %B %G", $date['start']->sec));
			$name  = '<span class="date">'.$start.'</span> '.$name;
		}

		return $name;
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public function datePeriod(array $date){

		$start = ucwords(strftime("%A %e %B %G", $date['start']->sec));
		$end   = ucwords(strftime("%A %e %B %G", $date['end']->sec));

		return ($end != $start) ? 'Du '.$start.' au '.$end : $start;
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public function googleMapJS(){

		$config = array();
		require(USER.'/config/config.php');
		$key = $config['googlemap']['apikey'];

		return '<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=true&libraries=drawing,geometry&key='.$key.'"></script>'.PHP_EOL.PHP_EOL;


	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public function flatten($arr, $base = "", $divider_char = "/"){
		$ret = array();
		if (is_array($arr)) {
			foreach ($arr as $k => $v) {
				if (is_array($v)) {
					$tmp_array = $this->flatten($v, $base . $k . $divider_char, $divider_char);
					$ret       = array_merge($ret, $tmp_array);
				} else
					if (is_a($v, 'MongoDate')) {
						$ret[$base . $k] = $v->sec;
					} else {
						$ret[$base . $k] = $v;
					}
			}
		}

		return $ret;
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public function inflate($arr, $divider_char = "/"){

		if (!is_array($arr)) {
			return false;
		}

		$split = '/' . preg_quote($divider_char, '/') . '/';

		$ret = array();
		foreach ($arr as $key => $val) {
			$parts    = preg_split($split, $key, -1, PREG_SPLIT_NO_EMPTY);
			$leafpart = array_pop($parts);
			$parent   = & $ret;

			foreach ($parts as $part) {
				if (!isset($parent[$part])) {
					$parent[$part] = array();
				} elseif (!is_array($parent[$part])) {
					$parent[$part] = array();
				}
				$parent = & $parent[$part];
			}

			if (empty($parent[$leafpart])) {
				$parent[$leafpart] = $val;
			}
		}

	    return $ret;
	}

}