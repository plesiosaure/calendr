<?php

	$map = $this->apiLoad('calendrierMap');
	$coo = $map->coordinates();

	$map->param(array(
		'center'  => $coo,
		'zoom'    => (($myManifestation['zoom'] > 0) ? $myManifestation['zoom'] : 7),
		'size'    => '600x400',
		'scale'   => '2',
		'markers' => 'color:red|label:S|'.$coo,
		'sensor'  => 'false',
		'key'     => GMAPSTATIC
	))->image();

	echo '<img '.$map->html().' />';

	#	$this->pre($myManifestation);

?>