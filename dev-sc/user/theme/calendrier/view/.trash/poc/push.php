<?php

$mon = new Mongo("mongodb://mongo01.kappuccino.net");
$db  = $mon->calendrier;
$col = $db->event2;

$col->drop();

/*
 * All documents must store location data in the same order; however, if you use latitude and
 * longitude as your coordinate system, @@@@@@@ always store longitude first. @@@@@@@@
 *
 * MongoDB’s 2d spherical index operators only recognize [ longitude, latitude ] ordering.
 */


for($lng=40; $lng<52; $lng+=0.4){       // vert

	for($lat=-6; $lat<10; $lat+=0.5){   // horizon

		$doc = array(
			'name'=> time(),
			'pos' => array($lng, $lat)
		);

		var_dump($col->insert($doc));
		echo "\n";

	}
}

function sinProject($x, $y){ //assumes x and y are degrees from -180 to 180
	$yRad = $y * pi() / 180; //convert to radians
	$xSin = $x * cos($yRad);

	return array($xSin, $yRad);
}

$col->ensureIndex(array('pos' => '2d'));


?>
