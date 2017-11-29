<?php

	$mode = isset($_SERVER['argv'][1]) ? $_SERVER['argv'][1]: 'dev';
	echo "MODE = ".$mode.PHP_EOL;

	if($mode != 'dev' && $mode != 'prod'){
		echo "Erreur de mode, l'appel doit être ./mvs.php dev ou ./mvs.php prod\n";
		die();
	}

	$GLOBALS['dev_mode'] = false;
	if($mode == 'dev') $GLOBALS['dev_mode'] = true;

	require(dirname(dirname(dirname(dirname(__DIR__)))).'/app/module/core/helper/app.php');
	$app = new coreApp();

	if(file_exists(CONFIG.'/app.php')) include(CONFIG.'/app.php');


	$mail = '';
	function logIt($str){
		global $mail;
		$mail .= $str;
		echo $str;
	}
