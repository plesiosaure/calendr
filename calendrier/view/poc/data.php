<?php

$mon = new Mongo("mongodb://mongo01.kappuccino.net");
$db  = $mon->calendrier;
$col = $db->event2;

$out = array(
	'success' => false,
	'data'    => array()
);

#$_POST['near'] = true;
#$_POST['lat'] = 44.276671273775186;
#$_POST['lng'] = 1.40625;

$lat = floatval(str_replace(',', '.', $_POST['lat']));
$lng = floatval(str_replace(',', '.', $_POST['lng']));

#$col->ensureIndex(array('pos' => '2d', 'name' => 1));
#$col->ensureIndex(array('pos' => 'geoHaystack'), array('bucketSize' => 5));

if(!empty($_POST['remove'])){

	$job = $col->remove(
		array('_id' => new MongoId($_POST['remove']))
	);

	$out['success'] = $job;
	$out['id']      = $_POST['remove'];

}else
if(isset($_POST['near'])){

	function sinProject($x, $y){ //assumes x and y are degrees from -180 to 180
		$yRad = $y * pi() / 180; //convert to radians
		$xSin = $x * cos($yRad);

		return array($xSin, $yRad);
	}

	$radius = 100 / 6371;
	$radius  = 1; #0.005 * 1.609344;

	$data = $db->command(array(
		'geoNear'            => "event2",
		'near'               => array($lat, $lng),
	#	'num'                => 1000,
		'spherical'          => true,
	#	'maxDistance'        => 2,
		'maxDistance'        => 0.025051308596118127,
	#	'maxDistance'        => 1.5

	#	'distanceMultiplier' => 6378,
	#	,'distanceMultiplier' => (1/112),
	));
	$out['data']    = $data['results'];

	/*
	$center = array($lat, $lng);
	$radius = 1.2;

#	$r = sqrt(pow($lat,2) + pow($lng,2) );
#	$o = 2 * atan($lat/ ($lgn + $r));

#	$center = array($r, $o);

/*

	$del = 1; //111.195 / 79.986;
	$box = array(array(48,-1), array(46,2));

	$fac = 1;
	$box = array(array($lat-$fac, $lng-($fac*$del)), array($lat+$fac, $lng+($fac*$del)));

	$out['box'] = $box;

	$data = $col->find(array(
		'pos' => array(
			'$within' => array(
				'$box'      => $box
			#	'$center'   => array(array($lat, $lng), 1)
			#	'$polygon'  => array(array(47, -3), array(47, 5), array(43, 1))
			)
		#	'$near' => $center,
			,'$maxDistance' => 0.2
		)
	));

	foreach($data as $e){
	#	$this->pre($e);
		$e['obj'] = $e;
		$tmp[] = $e;
	}

	$data = $tmp;
	$out['data']    = $data;

#	$data = iterator_to_array($data);
	#print_r($data);
*/
	$out['radius']  = $radius;
	$out['success'] = true;

}else
if(!empty($_POST['lng']) && !empty($_POST['lat'])){

	$doc = array(
		'name'  => date("Y-m-d H:i:s"),
		'pos'   => array($lat, $lng)
	);

	$out['data']    = $doc;
	$out['success'] = $col->insert($doc);


}else{

	$data = $col->find();
	$data = iterator_to_array($data);

	foreach($data as $n => $e){
		$data[$n]['_id'] = (string) $e['_id'];
	}

	$out['success'] = true;
	$out['data']    = $data;
}

if(isset($_GET['pre'])){
	$this->pre($out);
	exit();
}

echo json_encode($out);
