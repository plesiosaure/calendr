<?php

	$out = array();
	$api = $app->apiLoad('calendrierMvsImport');
	$db  = $app->dbExtConnect('localhost', 'motoregister', 'aaXHTtfFURepJ2s5', 'calendrier');

	if($_GET['action'] == 'prepare'){
		$out['ok'] = $api->prepare();
	}else

	if($_GET['action'] == 'import-organisateur'){
		$done = $api->organisateurImport();
		$out['done'] = $done;
	}else

	if($_GET['action'] == 'count-organisateur'){
		$tmp = $app->dbOne("SELECT COUNT(*) as ccc FROM lva_cal_organisateur", $db);
		$out = array('ok' => true, 'total' => intval($tmp['ccc']));
	}else

	if($_GET['action'] == 'count-manifestation'){
	}

	echo json_encode($out);