<?php

	$dep  = $_GET['dep'];
	$file = '/media/calendrier/ui/map/kml/'.$dep.'.kml';
	$json = '/media/calendrier/ui/map/kml/json/'.$dep.'.txt';

	if(file_exists(KROOT.$json)){
		$this->go($json);
	}else
	if(file_exists(KROOT.$file)){
		$kml = file_get_contents(KROOT.$file);

		preg_match('#<coordinates>(.*)</coordinates>#msU', $kml, $match);

		$points = trim($match[1]);
		$points = explode(' ', $points);

		foreach($points as $n => $e){
			list($lat, $lng) = explode(',', $e);
		#	$points[$n] = '['.$lat.','.$lng.']';
			$points[$n] = '['.$lng.','.$lat.']';
		}

		$final = '['.implode(",", $points).']';

		umask(0);
		file_put_contents(KROOT.$json, $final, 0755);
		$this->go($json);

	}else{
		echo '[]';
	}

?>