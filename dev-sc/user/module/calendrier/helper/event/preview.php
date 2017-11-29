<?php

	require USER . '/theme/calendrier/helper/constant.php';

	if(!defined('COREINC')) die('Direct access not allowed');
	$api = $app->apiLoad('calendrierEvent');

	$api->get(array(
		'_id' => $_GET['_id']
	));

	$myData = $api->data();

?><!DOCTYPE html>
<html lang="fr">
<head>
	<base href="../../" target="_blank" />
	<?php include(COREINC . '/head.php'); ?>
	<link rel="stylesheet" type="text/css" href="ui/css/main.css" />
</head>
<body>


<div id="preview">
	<div class="data">

		<div class="name"><?php echo $myData['name'] ?></div>

		<div class="map"><?php

			$map = $app->apiLoad('calendrierMap');
			$coo = $map->coordinates($myData['gps']);

			$map->param(array(
				'center'    => $coo,
				'zoom'      => 5,
				'size'      => '200x200',
				'scale'     => '2',
				'markers'   => 'color:blue|label:S|'.$coo,
				'sensor'    => 'false',
				'key'       => GMAPSTATIC
			))->image();

			echo '<img '.$map->html().' alt="" />';


			?></div>

	</div>
</div>



</body>
</html>