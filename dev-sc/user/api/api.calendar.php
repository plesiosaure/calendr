<?php
/* + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - 
	API			kalendar
	Version		1.0.0 
	Date		2007/03/24
 	Update		2012/05/29 (Paul)
	Creator		Benjamin MOSNIER (nemo@kappuccino.org)
	Inheritage	standalone
	Infos		This class allow to create a calendar with HTML table
 
	CHANGELOG : Affiche le calendrier avec un nombre de lignes fixes, remplit les 
 		vides avec les jours des mois précédents/suivants
 
+ - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - */
class calendar{

/* + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - 
+ - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - */
function calendar(){}

/* + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - 
+ - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - */
function calendarBuild($dateArg, $format=NULL){

	$monthName	= array('Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Aout','Septembre','Octobre','Novembre','Décembre');
	$semaine	= array('Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim');
	$dayNum		= array(7, 1, 2, 3, 4, 5, 6);
	$dayNum		= array(1, 2, 3, 4, 5, 6, 0);

	# Découpe la date
	list($this->iYear, $this->iMonth, $this->iDay) = split ('[-]', $dateArg);

	# Week Config
	$this->format				= $format;
	$this->monthFullDate 		= mktime(0,0,0, $this->iMonth, $this->iDay, $this->iYear);	
	$this->monthName 			= $monthName[$this->iMonth-1];
	$this->monthStartDay		= date("w", mktime (0,0,0, $this->iMonth, $this->iDay, $this->iYear));
	$this->monthNextFullDate 	= mktime (0,0,0, $this->iMonth+1, $this->iDay, $this->iYear);
	$this->diffInMs 			= $this->monthFullDate - $this->monthNextFullDate; 
	$this->daysMonth 			= ceil(abs($this->diffInMs / 86400));
	$this->output				= NULL;
	$this->dayCurrent			= 1;

	/*echo "<pre>";
	print_r($this->format);
	echo "</pre>";*/


	switch($this->monthStartDay){
		case 1 : 	$this->daysBeforeStart = -1; break;
		case 2 : 	$this->daysBeforeStart =  0; break;
		case 3 : 	$this->daysBeforeStart =  1; break;
		case 4 : 	$this->daysBeforeStart =  2; break;
		case 5 : 	$this->daysBeforeStart =  3; break;
		case 6 : 	$this->daysBeforeStart =  4; break;	 // Valeur non vérifiée
		case 0 : 	$this->daysBeforeStart =  5; break;
	}

	if($this->daysMonth > 31)	$this->daysMonth = 31;

	$nextYear		= date("Y-m-d", mktime(0, 0, 0, $this->iMonth, 		$this->iDay, $this->iYear+1));
	$prevYear		= date("Y-m-d", mktime(0, 0, 0, $this->iMonth, 		$this->iDay, $this->iYear-1));
	$nextMonth		= date("Y-m-d", mktime(0, 0, 0, $this->iMonth+1, 	$this->iDay, $this->iYear));
	$prevMonth		= date("Y-m-d", mktime(0, 0, 0, $this->iMonth-1, 	$this->iDay, $this->iYear));
	$today			= date("Y-m-d");

	if($this->format['calendar']['header']['style'] != NULL) 	$styleHeader 	= " style=\"".$this->format['calendar']['header']['style']."\"";
	if($this->format['calendar']['header']['class'] != NULL) 	$styleClass 	= " class=\"".$this->format['calendar']['header']['class']."\"";
	if($this->format['calendar']['header']['semaine'] != NULL) 	$semaine		= $this->format['calendar']['header']['semaine'];

	if($this->format['calendar']['dayName']['style'] != NULL)	$styleDayName	= " style=\"".$this->format['calendar']['dayName']['style']."\"";
	if($this->format['calendar']['dayName']['class'] != NULL)	$classDayName	= " class=\"".$this->format['calendar']['dayName']['class']."\"";

	if($this->format['calendar']['dayCell']['style'] != NULL)	$styleDayCell	= " style=\"".$this->format['calendar']['dayCell']['style']."\"";
	if($this->format['calendar']['dayCell']['class'] != NULL)	$classDayCell	= " class=\"".$this->format['calendar']['dayCell']['class']."\"";

	if($this->format['calendar']['table']['style'] != NULL)		$style			= " style=\"".$this->format['calendar']['table']['style']."\"";
	if($this->format['calendar']['table']['class'] != NULL)		$class			= " class=\"".$this->format['calendar']['table']['class']."\"";
	
	$cellpadding	= ($this->format['calendar']['table']['padding']) ? $this->format['calendar']['table']['padding'] : 0;
	$cellspacing	= ($this->format['calendar']['table']['spacing']) ? $this->format['calendar']['table']['spacing'] : 0;
	$border			= ($this->format['calendar']['table']['border'])  ? $this->format['calendar']['table']['border']  : 0;
	
	$head			= $this->monthName." ".$this->iYear;
	$head			= ($this->format['calendar']['header']['href'] != NULL) ? "<a href=\"".$this->format['calendar']['header']['href']."\">".$head."</a>" : $head; 

	$this->output = 
	"<table cellpadding=\"".$cellpadding."\" cellspacing=\"".$cellspacing."\" border=\"".$border."\"".$class.$style.">".
		"<tr>\n".
			"<td colspan=\"7\"".$styleHeader.$styleClass.">".$head."</td>\n".
		"</tr>\n".
		"<tr".$styleDayName.$classDayName.">\n".
			"<td>".$semaine[0]."</td>\n".
			"<td>".$semaine[1]."</td>\n".
			"<td>".$semaine[2]."</td>\n".
			"<td>".$semaine[3]."</td>\n".
			"<td>".$semaine[4]."</td>\n".
			"<td class=\"sat\">".$semaine[5]."</td>\n".
			"<td class=\"sun\">".$semaine[6]."</td>\n".
		"</tr>".
		"<tr".$styleDayCell.$classDayCell.">";
	
		$cel 		= 0;
		$line		= 0;
		$started	= false;
		$dayBefore	= 0;

		for($i=0; $i<= ($this->daysMonth + $this->daysBeforeStart); $i++){
			
			$day = $i - $this->daysBeforeStart;
			
			if($line == 0){
				if($this->monthStartDay == $dayNum[$i]) $started = true;

				if($started){
					if($dayBefore > 0){
						
						$this->output .= $this->fillStart($this->iMonth, $this->iYear, $dayBefore);
					//	$this->output .= "<td colspan=\"".$dayBefore."\" class=\"firstCell\">&nbsp;</td>";
						$dayBefore = 0;
					}

					$class  = ($cel == 5) ? 'sat' : ''; // classer les samedis/dimanches
					$class  .= ($cel == 6) ? 'sun' : ''; 

					$this->output .= $this->kalendarCel($this->iMonth, $day, $class);
					
				}else{
					$dayBefore ++;
				}
			}else{
				$class = ($cel == 0) ? 'firstCell' : NULL;
				$class .= ($cel == 5) ? 'sat' : ''; // classer les samedis/dimanches
				$class .= ($cel == 6) ? 'sun' : ''; 
				
				$this->output .= $this->kalendarCel($this->iMonth, $day, $class);
			}
			
			if($cel == 6){
				$this->output .= "</tr>\n<tr".$styleDayCell.$classDayCell.">\n";
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
		
		if($cel > 0 || $newLine) $this->output .= $this->fillEnd($this->iMonth, $this->iYear, $cel, $newLine);

		//if($cel > 0) $this->output .= "<td colspan=\"".(7 - $cel)."\">".$cel."</td>";

	$this->output .= "</tr>\n";
	$this->output .= "</table>";
		
	return $this->output;
}

## remplir le début du calendar
function fillStart($month, $year, $dayBefore) {
	$previous = ($month - 1);

	if ($previous < 1) {
		$previous = 12;
		$year = $year - 1;
	}
	
	# trouve le nombre de jours dans  le mois
	$lastDay = date('t',strtotime($previous.'/1/'.$year));
	# jour où le calendrier commence vraiment
	$firstDay = $lastDay - $dayBefore;

	for ($i = 0; $i < $dayBefore; $i++) {
		if ($i == 0) {
			$output .= '<td class="firstCell previous"><div class="dayNumber previous">'.($firstDay + 1).'</td>';
		} else {
			$output .= '<td class="previous"><div class="dayNumber previous">'.(($firstDay + $i)+1).'</div></td>';
		}
	}
	
	return $output;
}

## remplir la fin du calendar
function fillEnd($month, $year, $cel, $newLine) {
	$currentDay = 0;
	
	# remplir le reste des cellules
	if ($cel > 0) $cel = (7 - $cel);
	for ($i = 0; $i < $cel; $i++) {
		$output .= '<td class="firstCell next"><div class="dayNumber next">'.($i + 1).'</td>';
		$currentDay++;
	}
	
	# ajouter une nouvelle ligne
	if ($newLine) {
		$output .= '<tr class="dayCell">';
		
		for ($i = 0; $i < 7; $i++) {
			if ($i == 0) {
				$output .= '<td class="firstCell next"><div class="dayNumber next">'.($currentDay + 1).'</td>';
			} else {
				$output .= '<td class="next"><div class="dayNumber next">'.($currentDay + 1).'</div></td>';
			}
			$currentDay++;
		}
		
		$output .= '</tr>';
	}
	
	return $output;
	
}

/* + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - 
+ - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - */
function kalendarCel($month, $day, $class=NULL){
	
	$monthForKey	= (strlen($month) == 1) ? '0'.$month	: $month;
	$dayForKey 		= (strlen($day) == 1)   ? '0'.$day		: $day;

#	print_r($this->format[$monthForKey][$dayForKey]);

	if($this->format[$monthForKey][$dayForKey]['value'] != NULL){
		$class .= ' hasEvent';
		$r = "<div class=\"dayPage\">".$this->format[$monthForKey][$dayForKey]['value']."</div>";
	}else
	if($this->format[$monthForKey][$dayForKey]['href'] != NULL){
	#	$r = "<a href=\"".$this->format[$monthForKey][$dayForKey]['href']."\" class=\"dayNUmber\">".$day."</span></a>";
	}else{
	#	$r = "<div class=\"day\"><div>";
	}


	return "<td class=\"".$class."\" style=\"".$this->format[$monthForKey][$dayForKey]['style']."\"><div class=\"dayNumber\">".$day."</div>".$r."</td>";
}


}
?>