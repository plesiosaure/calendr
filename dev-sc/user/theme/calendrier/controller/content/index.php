<?php

	// Date courante pour repere temporel
	$myCurrent = new DateTime();

	// Nombre de semaine a afficher avant et après la semaine courante
#	$myDeltaBefore  = 2;
	$myDelta   = 6;

	// Liste des dates finales
	$myDates = array();

	// Liste des dates avant la semaine courante
	$myDates_before = array();

	// ITERATION ///////////////////////////////////////////////////////////////////////////////////////////////////////

/*	for($i=0; $i<$myDeltaBefore; $i++){
		$myCurrent->sub(new DateInterval('P1W'));
		$myDates_before[] = $myCurrent->format("Y-m-d");
	}

	$myDates   = array_reverse($myDates_before);
*/

	$myDates = array();

	$myCurrent = new DateTime();
	$myDates[] = $myCurrent->format("Y-m-d");

	for($i=0; $i<$myDelta-1; $i++){
		$myCurrent->add(new DateInterval('P1W'));
		$myDates[] = $myCurrent->format("Y-m-d");
	}

	// $myDates est au format array(dateJS, numeroSemaine)
	foreach($myDates as $n => $d){
		list($y, $m, $d) = explode('-', $d);
		$timestamp = mktime(0, 0, 0, $m, $d, $y);
		$myDates[$n] = array(
			'js'    => date("Y/m/d", $timestamp),
			'week'  => date("W", $timestamp),
			'short' => $this->helperDate(date("Y-m-d", $timestamp), "%e %b")
			
		);
	}

	$myCurrent = date("W");
