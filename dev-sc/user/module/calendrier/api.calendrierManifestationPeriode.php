<?php

class calendrierManifestationPeriode extends calendrier{

	public  $start;             // Objet datetime du début de la période
	private $current;           // Objet datetime courant servant a itérer
	public  $end;               // Objet datetime de la fin de la période
	private $dates  = array();  // Liste des dates pour la période (yyy-mm-dd)
	private $feries = array();  // Liste des jours feries, une classe est ajouté sur la cellule

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public  function __construct(){
		$this->labels = array(
			'WEEKEND_ALL'    => 'Tous les week-end',
			'WEEKEND_ALTER1' => 'Tous les week-end (une fois sur deux)',
			'WEEKEND_ALTER2' => 'Tous les week-end (une fois sur trois)',
			'WEEKEND_ALTER3' => 'Tous les week-end (une fois sur quatre)',
			'WEEKEND_FIRST'  => 'Tous les premiers week-end',
			'WEEKEND_LAST'   => 'Tous les derniers week-end',
		);

		$this->feries = array(
			'2013-01-01', '2014-01-01', '2015-01-01', // Jour de l'an
			'2013-04-01', '2014-04-21', '2015-04-06', // Lundi de Pâques
			'2013-05-01', '2014-05-01', '2015-05-01', // Fête du Travail
			'2013-05-08', '2014-05-08', '2015-05-08', // 8 Mai 1945
			'2013-05-09', '2014-05-29', '2015-05-14', // Jeudi de l'Ascension
			'2013-05-20', '2014-06-09', '2015-05-25', // Lundi de Pentecôte
			'2013-07-14', '2014-07-14', '2015-07-14', // Fête Nationale
			'2013-08-15', '2014-08-15', '2015-08-15', // Assomption
			'2013-11-01', '2014-11-01', '2015-11-01', // La Toussaint
			'2013-11-11', '2014-11-11', '2015-11-11', // Armistice
			'2013-12-25', '2014-12-25', '2015-12-25'  // Noël
		);
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public  function feries($f=NULL){
		if($f == NULL) return $this->feries;
		$this->feries = $f;
		return $this;
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public  function labels(){
		return $this->labels;
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public  function start($start){
		$this->start    = new DateTime($start);
		$this->current($start);
		return $this;
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public  function end($end){
		$this->end = new DateTime($end);
		return $this;
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
// Retourne un array de dates depuis un label
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public  function labelToDates($label){

		if($this->start == NULL) $this->start(date("Y-m-d"));
		if($this->end   == NULL) $this->end(  date("Y-m-d", strtotime("2 years", $this->start->getTimestamp())));

		switch($label){
			case 'WEEKEND_ALL':     $this->weekEndAll(0); break;
			case 'WEEKEND_ALTER1':  $this->weekEndAll(1); break;
			case 'WEEKEND_ALTER2':  $this->weekEndAll(2); break;
			case 'WEEKEND_ALTER3':  $this->weekEndAll(3); break;
			case 'WEEKEND_ALTER4':  $this->weekEndAll(4); break;
			case 'WEEKEND_FIRST':   $this->weekEndFirst(); break;
			case 'WEEKEND_LAST':    $this->weekEndLast(); break;
		}

		$dates = $this->dates;
		$this->dates = array();

		return is_array($dates) ? $dates : array();
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
// Ajouter une date à la liste des dates qui nous intéresse
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	private function push($date){
		array_push($this->dates, $date);
		$this->current($date);
	#	$this->pre("PUSH: ".$date.' CURRENT: '.$this->current->format('Y-m-d'));
		return $this;
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
// On est a cette date
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	private function current($date){
		$this->current = new DateTime($date);
		return $this;
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
// A t'on atteint la fin de la période ?
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	private function reached(){
		$diff = $this->end->diff($this->current);
		return ($diff->days > 0 && $diff->invert == 1) ? false : true;
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public  function datesToMongoDays($dates){

		$length = count($dates);
		if($length == 0) return array();
		sort($dates);

		$final = array($dates[0] => 0);
		$last  = $dates[0];

		for($i=1; $i<$length; $i++){

			$previous   = new DateTime($dates[$i-1]);
			$current    = new DateTime($dates[$i]);
			$diff       = $current->diff($previous);

			// La date actuelle est le lendemain de la precedente
			if($diff->days == 1 && $diff->invert == 1){
				$final[$last]++;
			}
			// La date actuelle n'est pas collée a la precedente
			else{
				$last = $dates[$i];
				$final[$last] = 0;
			}
		}

		$mongo = array();
		foreach($final as $date => $days){
			$d = new DateTime($date);
			$e = strtotime($days.' days', $d->getTimestamp());

			$mongo[] = array(
				'start' => new MongoDate(strtotime($date)),
				'end'   => new MongoDate(strtotime(date("Y-m-d", $e))),
				'days'  => intval($days)+1,
			);
		}

		return $mongo;
	}

//----------------------------------------------------------------------------------------------------------------------
//----------------------------------------------------------------------------------------------------------------------
	public  function mongoDaysToDate($dates){

		if(count($dates) == 0) return array();

		$out = array();
		foreach($dates as $e){

			$start  = new DateTime(date("Y-m-d", $e['start']->sec));
			$days   = $e['days'] - 1;

			$out[$start->format('Y-m-d')] = NULL;

			if($days > 0){
				for($i=0; $i<$days; $i++){
					$start->add('P1D');
					$out[$start->format('Y-m-d')] = NULL;
				}
			}

		}

		return $out;
	}

//----------------------------------------------------------------------------------------------------------------------
//----------------------------------------------------------------------------------------------------------------------
	private function weekEndAll($alter=0){
		$do = 0;
		while(!$this->reached() AND $do < 365){
			$s = strtotime('next saturday', $this->current->getTimestamp());
			$d = strtotime('tomorrow', $s);

		#	$this->pre($s, $d, date("m",$s), date("m", $d));

			if(intval($s) > 0 && intval($d) > 0 && date('m', $s) == date('m',$d)){
				$this->push(date("Y-m-d", $s));
				$this->push(date("Y-m-d", $d));
			}else{
				$this->current(date("Y-m-d", $d));
			}

			if($alter > 0){
				$this->current(date("Y-m-d", strtotime(($alter*7).' days', $this->current->getTimestamp())));
			}

			$do++;
		}
	}

//----------------------------------------------------------------------------------------------------------------------
//----------------------------------------------------------------------------------------------------------------------
	private function weekEndFirst(){
		$do = 0;
		while(!$this->reached() AND $do < 365){
			$s = strtotime('next saturday', $this->current->getTimestamp());
			if(intval($s) > 0) $this->push(date("Y-m-d", $s));

			$d = strtotime('tomorrow', $s);
			if(intval($d) > 0) $this->push(date("Y-m-d", $d));

			// Passer au mois prochain
			$last = date("Y-m-t", $this->current->getTimestamp());
			$this->current($last);

			$do++;
		}
	}

//----------------------------------------------------------------------------------------------------------------------
//----------------------------------------------------------------------------------------------------------------------
	private function weekEndLast(){
		$do = 0;
		while(!$this->reached() AND $do < 365){
			// se placer a la fin du mois;
			$this->current(date("Y-m-t", $this->current->getTimestamp()));
			$l = $this->current->getTimestamp();

			$s = strtotime('last saturday', $this->current->getTimestamp());
			$d = strtotime('tomorrow', $s);

			if(intval($s) > 0 && intval($d) > 0){
				$this->push(date("Y-m-d", $s));
				$this->push(date("Y-m-d", $d));
			}

			// Passer au premier jour du mois suivant
			$this->current(date("Y-m-d", strtotime('+ 2 days', $l)));

			$do++;
		}
	}

}