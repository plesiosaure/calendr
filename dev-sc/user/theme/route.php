<?php
/*
	switch($_SERVER['HTTP_HOST']){
		case 'supercalendrier.com':
		case 'www.supercalendrier.com':
		case 'dev.supercalendrier.com':    $site = 'calendrier'; break;
		default:                           $site = 'register';
	}

	#	$data = this->kodeine dans le context du hook

	if($site == 'calendrier'){
*/
		$data['id_chapter']  = 2;
		$data['id_theme']    = $data['chaptersIds'][2]['id_theme'];
		$data['themeName']   = 'Calendrier';
		$data['themeFolder'] = 'calendrier';
/*
	}

	unset($site);
*/



