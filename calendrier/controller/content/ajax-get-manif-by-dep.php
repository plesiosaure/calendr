<?php

$api        = $this->apiLoad('calendrierAnnonce');
$polygons   = array();

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -

# Renvoyer le kml si dep précisé
#
if ($_GET['dep'] > 0) {

	$dep  = $_GET['dep'];
	$file = '/media/calendrier/ui/map/kml/'.$dep.'.kml';
	$json = '/media/calendrier/ui/map/kml/json/'.$dep.'.txt';

	if(file_exists(KROOT.$json)){
		#$this->go($json);
		$polygons = json_decode(file_get_contents(KROOT.$json), true);
	}else
	if(file_exists(KROOT.$file)){
		$kml = file_get_contents(KROOT.$file);

		preg_match('#<coordinates>(.*)</coordinates>#msU', $kml, $match);

		$points = trim($match[1]);

		$points = explode(' ', $points);

		foreach($points as $n => $e){
			list($lat, $lng) = explode(',', $e);
			$points[$n] = '['.$lat.','.$lng.']';
		}

		$final = '['.implode(",", $points).']';

		umask(0);
		file_put_contents(KROOT.$json, $final, 0755);
		#$this->go($json);

		$polygons = json_decode($final, true);

	}else{
		echo '[]';
	}
}

# get DATA
#
$opt        = array(
	'debug'		=> false,
	'offset'    => $myOffset,
	'limit'     => $myLimit,

	'type'      => $_GET['type'],
#	'region'    => $_GET['reg'],
	'dpt'       => $_GET['dep'],
	'range'     => array(
		'start' => $_GET['date'],
		'end'   => $api->dateDay($_GET['date'], 1)
	)
);
$myData     = $api->get($opt);
$myTotal    = $api->total;

$out = array();
foreach ($myData as $k=>$data) {
	$out[] = array(
		"id"        => $k,
		"latlng"    => $data['gps'],
		'title'     => $data['name'],
		'region'    => $data['region'],
		'customdata'      => array(
			'infobox' => '<img src="http://placekitten.com/g/100/100" />'
		)
	);
}

echo json_encode(array(
	'data'      => $out,
	'polygons'  => $polygons
));


//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -

