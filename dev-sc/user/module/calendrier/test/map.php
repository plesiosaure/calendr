<?php

	require USER . '/theme/calendrier/helper/constant.php';

	$api = $app->apiLoad('calendrierMap');

	$api->param(array(
	#	'center'    => 'New York,NY',
		'center'    => '44.883607,-0.594635',
		'zoom'      => 13,
		'size'      => '600x400',
	#	'scale'     => '2',
		'markers'   => 'color:blue|label:S|44.883607,-0.594635',
		'sensor'    => 'false',
		'key'       => GMAPSTATIC
	))->image();

	$url = $api->url();

	echo $url;
#	die();
	echo '<br />';
	echo '<img src="'.$url.'"  />';



