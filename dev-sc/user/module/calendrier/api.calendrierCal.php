<?php

class calendrierCal extends calendrier {

	private $html       = '';
	private $html_      = '';
	private $format     = array();
	private $params     = array();
	private $semaine	= array('Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim');
	private $dayNum		= array(1, 2, 3, 4, 5, 6, 0);
	private $feries     = array();

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public  function __construct(){
		define('EOL',    PHP_EOL);
		define('TAB',   "\t");
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public function __toString(){
		$html = sprintf($this->html_, $this->html);
		$this->reset();
		return $html;
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public  function display(){
		echo $this;
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public function reset(){
		$this->html     = '';
		$this->html_    = '';
		$this->params   = array();
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public function feries($f=NULL){
		if($f == NULL) return $this->feries;
		$this->feries = $f;
		return $this;
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public  function format($key=NULL, $value=NULL){

		if(is_string($key) && $value != ''){
			$key = array($key => $value);
			unset($value);
		}

		if(is_array($key) && $value == NULL){
			foreach($key as $k => $v){
				$this->format[$k] = $v;
			}
		}

		return $this;
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public  function set($key=NULL, $value=NULL){

		if(is_string($key) && $value != ''){
			$key = array($key => $value);
			unset($value);
		}

		if(is_array($key) && $value == NULL){
			foreach($key as $k => $v){
				$this->params[$k] = $v;
			}
		}

		return $this;
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public  function build($date){

		list($iYear, $iMonth) = explode('-', $date);
		if(strlen($iMonth) == 1) $iMonth = '0'.$iMonth;

		$this->monthFullDate 		= mktime(0,0,0, $iMonth, 1, $iYear);
		$this->monthName 			= $this->monthName[$iMonth-1];
		$this->monthStartDay		= date("w", mktime (0,0,0, $iMonth, 1, $iYear));
		$this->monthNextFullDate 	= mktime (0,0,0, $iMonth+1, 1, $iYear);

		$this->diffInMs 			= $this->monthFullDate - $this->monthNextFullDate;
		$this->daysMonth 			= ceil(abs($this->diffInMs / 86400));
		$this->dayCurrent			= 1;

		switch($this->monthStartDay){
			case 1: $this->daysBeforeStart = -1; break;
			case 2: $this->daysBeforeStart =  0; break;
			case 3: $this->daysBeforeStart =  1; break;
			case 4: $this->daysBeforeStart =  2; break;
			case 5: $this->daysBeforeStart =  3; break;
			case 6: $this->daysBeforeStart =  4; break;
			case 0: $this->daysBeforeStart =  5; break;
		}

		if($this->daysMonth > 31)	$this->daysMonth = 31;

	#	$nextYear		= date("Y-m-d", mktime(0, 0, 0, $iMonth, 		1, $iYear+1));
	#	$prevYear		= date("Y-m-d", mktime(0, 0, 0, $iMonth, 		1, $iYear-1));
	#	$nextMonth		= date("Y-m-d", mktime(0, 0, 0, $iMonth+1, 	1, $iYear));
	#	$prevMonth		= date("Y-m-d", mktime(0, 0, 0, $iMonth-1, 	1, $iYear));
	#	$today			= date("Y-m-d");

		$this->table();

		$cel 		= 0;
		$line		= 0;
		$started	= false;
		$dayBefore	= 0;

		$this->html = TAB.TAB.'<tr class="days">'.EOL;


		for($i=0; $i<= ($this->daysMonth + $this->daysBeforeStart); $i++){

			$day = $i - $this->daysBeforeStart;

			if($line == 0){
				if($this->monthStartDay == $this->dayNum[$i]) $started = true;

				if($started){
					if($dayBefore > 0){
						$this->html .= $this->fillStart($iMonth, $iYear, $dayBefore);
						$dayBefore = 0;
					}

					$class  = ($cel == 5) ? 'sat' : ''; // classer les samedis/dimanches
					$class .= ($cel == 6) ? 'sun' : '';

					$this->html .= $this->cel($iYear, $iMonth, $day, $class);

				}else{
					$dayBefore ++;
				}

			}else{
				$class = ($cel == 0) ? 'firstCell' : NULL;
				$class .= ($cel == 5) ? 'sat' : ''; // classer les samedis/dimanches
				$class .= ($cel == 6) ? 'sun' : '';

				$this->html .= $this->cel($iYear, $iMonth, $day, $class);
			}

			if($cel == 6){
				$this->html .= TAB.TAB.'</tr>'.EOL.TAB.TAB.'<tr class="days">'.EOL;
				$cel = 0;
				$line++;

				## s_newLine => on a rempli la dernière row et on a compté une ligne
				# de plus. On doit forcer sa création
				$s_newLine = true;
			}else{
				$cel++;
				$s_newLine = false;
			}
		}

		# Créer dernière ligne pour harmoniser ?
		$newLine = ($line > 4) ? false : true;
		if ($s_newLine) $newLine = true;

		if($cel > 0 || $newLine) $this->html .= $this->fillEnd($iMonth, $iYear, $day, $cel, $newLine);

		return $this;
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
// Remplir le début du calendar
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	private function fillStart($month, $year, $dayBefore) {

		$output     = '';
		$previous   = ($month - 1);

		if ($previous < 1) {
			$previous = 12;
			$year = $year - 1;
		}

		$lastDay  = date('t',strtotime($previous.'/1/'.$year)); # trouve le nombre de jours dans  le mois
		$firstDay = $lastDay - $dayBefore;                      # jour où le calendrier commence vraiment

		for ($i = 0; $i < $dayBefore; $i++) {
			if ($i == 0) {
				$cls = ' first';
			}else
			if($i == 7){
				$cls = ' last';
			}else{
				$cls = '';
			}

			$d = ($firstDay + $i) + 1;

			$output .= TAB.TAB.TAB.
				'<td data-date="'.$this->dataDate($year, $month, $d).'" class="previous'.$cls.'">'.
					'<div class="number">'.$d.'</div>'.
				'</td>'.EOL;
		}

		return $output;
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
// Remplir la fin du calendar
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	private function fillEnd($month, $year, $day, $cel, $newLine) {

		$currentDay = 0;
		$output = '';

		# remplir le reste des cellules
		if ($cel > 0) $cel = (7 - $cel);

		for ($i = 0; $i < $cel; $i++) {

			if ($i == 0) {
				$cls = ' first';
			} else
			if($i == 6){
				$cls = ' last';
			}else{
				$cls = '';
			}

			$output .= TAB.TAB.TAB.
				'<td data-date="'.$this->dataDate($year, $month+1, $i+1).'" class="next'.$cls.'">'.
					'<div class="number">'.($i + 1).'</div>'.
				'</td>'.EOL;

			$currentDay++;
		}

		# ajouter une nouvelle ligne
		if ($newLine) {
			$output .= TAB.TAB.'<tr class="days">'.EOL;

			for ($i=0; $i<7; $i++) {
				if ($i == 0) {
					$cls = ' first';
				} else
				if($i == 6){
					$cls = ' last';
				}else{
					$cls = '';
				}

				$output .= TAB.TAB.TAB.
					'<td data-date="'.$this->dataDate($year, $month+1, $currentDay+1).'" class="next'.$cls.'">'.
						'<div class="number next">'.($currentDay+1).'</div>'.
					'</td>'.EOL;

				$currentDay++;
			}

			$output .= TAB.TAB.'</tr>'.EOL;
		}

		return $output;

	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	private function cel($year, $month, $day, $class=NULL){

		/*if($this->format[$monthForKey][$dayForKey]['value'] != NULL){
			$class .= ' hasEvent';
			$r = "<div class=\"dayPage\">".$this->format[$monthForKey][$dayForKey]['value']."</div>";
		}*/

		$date  = $year.'-'.$month.'-'.$day;
		$class = '';

		if(in_array($date, $this->feries())) $class = 'ferie';
		if(array_key_exists($date, $this->params['dates'])) $class .= ' hasData';

		return TAB.TAB.TAB.
			'<td data-date="'.$this->dataDate($year, $month, $day).'" class="'.$class.'">'.
				'<div class="number">'.$day.'</div>'.
			#	$r.
			'</td>'.EOL;
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	private function dataDate($y, $m, $d){
		if(strlen($d) == 1) $d = '0'.$d;
		if(strlen($m) == 1) $m = '0'.$m;
		return $y.'-'.$m.'-'.$d;
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	private function table(){

	#	$this->pre($this->format);
	#	$this->pre($this->params);

	#	if($this->format['header']['style'] != NULL) 	$styleHeader 	= " style=\"".$this->format['header']['style']."\"";
	#	if($this->format['header']['class'] != NULL) 	$styleClass 	= " class=\"".$this->format['header']['class']."\"";
	#	if($this->format['header']['semaine'] != NULL) 	$this->semaine	= $this->format['header']['semaine'];

	#	if($this->format['dayName']['style'] != NULL)	$styleDayName	= " style=\"".$this->format['dayName']['style']."\"";
	#	if($this->format['dayName']['class'] != NULL)	$classDayName	= " class=\"".$this->format['dayName']['class']."\"";

	#	if($this->format['dayCell']['style'] != NULL)	$styleDayCell	= " style=\"".$this->format['dayCell']['style']."\"";
	#	if($this->format['dayCell']['class'] != NULL)	$classDayCell	= " class=\"".$this->format['dayCell']['class']."\"";


		$padding	= $this->format['padding'];
		$spacing	= $this->format['spacing'];
		$border		= $this->format['border'];
		$style      = $this->format['style'];
		$class      = $this->format['class'];

		$this->html_ =
		'<table cellpadding="'.$padding.'" cellspacing="'.$spacing.'" border="'.$border.'" class="'.$class.'" style="'.$style.'">'.EOL.
			TAB.'<thead>'.EOL.
				TAB.TAB.'<tr>'.EOL.
					TAB.TAB.TAB.'<th colspan="7">'.$this->params['header'].'</th>'.EOL.
				TAB.TAB.'</tr>'.EOL.
			TAB.'</thead>'.EOL.
			TAB.'<tbody>'.EOL.
				TAB.TAB.'<tr class="daysName">'.EOL.
					TAB.TAB.TAB.'<td>'.$this->semaine[0].'</td>'.EOL.
					TAB.TAB.TAB.'<td>'.$this->semaine[1].'</td>'.EOL.
					TAB.TAB.TAB.'<td>'.$this->semaine[2].'</td>'.EOL.
					TAB.TAB.TAB.'<td>'.$this->semaine[3].'</td>'.EOL.
					TAB.TAB.TAB.'<td>'.$this->semaine[4].'</td>'.EOL.
					TAB.TAB.TAB.'<td class="sat">'.$this->semaine[5].'</td>'.EOL.
					TAB.TAB.TAB.'<td class="sun">'.$this->semaine[6].'</td>'.EOL.
				TAB.TAB.'</tr>'.EOL.
				'%s'.
			TAB.'</tbody>'.EOL.
		'</table>';

	}
}