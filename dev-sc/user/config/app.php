<?php

// ERRORS //////////////////////////////////////////////////////////////////////////////////////////////////////////////

	ini_set('display_errors',	'On');
	ini_set('html_errors', 		'On');
	ini_set('error_reporting',	E_ALL ^ E_NOTICE ^ E_DEPRECATED ^ E_WARNING);

// HOOKS ///////////////////////////////////////////////////////////////////////////////////////////////////////////////

	$app->hookRegister('kodeineInit',       USER.'/theme/route.php', 'filter');


// MEDIA ///////////////////////////////////////////////////////////////////////////////////////////////////////////////

	$mediaWaterMark = array(
		'domain' => MEDIA . '/ui/img/watermark/domain.png',
		'car'    => MEDIA . '/ui/img/watermark/car.png'
	);

	$mediaCDN = (strpos('kap', $_SERVER['HTTP_HOST']) === false)
		? 'static.moto-register.com'
		: 'kap.moto-register.com';


