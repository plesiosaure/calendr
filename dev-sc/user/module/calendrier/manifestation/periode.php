<?php

	if(!defined('COREINC')) die('Direct access not allowed');
	$api = $app->apiLoad('calendrierManifestation');
	$per = $app->apiLoad('calendrierManifestationPeriode');
	$cal = $app->apiLoad('calendrierCal');

	$cal->feries($per->feries());

// GET /////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	if($_REQUEST['_id'] != NULL){
		$data = $api->get(array(
			'debug' => 0,
			'_id'   => $_GET['_id']
		));

		$dates  = $per->mongoDaysToDate($data['date']);

		foreach($dates as $k => $v){
			$dates[$k] = array();
		}
	}

	if($data['_id'] == '') $app->go('./');

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

?><!DOCTYPE html>
<html lang="fr">
<head>
	<base href="../" />
	<?php include(COREINC . '/head.php'); ?>
	<link rel="stylesheet" type="text/css" href="ui/css/main.css" />
	<link rel="stylesheet" type="text/css" href="manifestation/ui/css/period.css" />
	<link rel="stylesheet" type="text/css" href="ui/vendor/bootstrap-datepicker/css/datepicker.css" />
</head>
<body>

<header><?php
	include(COREINC . '/top.php');
	include(dirname(__DIR__) . '/ui/menu.php')
?></header>

<div class="inject-subnav-right hide">
	<li>
		<div class="btn-group">
			<a href="manifestation/date?_id=<?php echo $data['_id'] ?>" class="btn btn-small">Revenir aux dates</a>
			<a onclick="pushToEvent('<?php echo $data['_id'] ?>')" class="btn btn-small btn-success">Enregistrer</a>
		</div>
	</li>
</div>

<div id="app"><div id="period">

		<div class="picker">
			A partir de <input type="text" id="start" />

			<select id="labels" id="labels"><option></option><?php
				foreach($per->labels() as $k => $v){
					echo '<option value="'.$k.'">'.$v.'</option>';
				}
			?></select>

			arrêter le <input type="text" id="finish" />

			<button onclick="display()">Afficher</button>
		</div>

		<div class="clearfix"><?php

			$date = new DateTime(date("Y-m-d", strtotime('-2 months')));

			for($i=0; $i<=24; $i++){

				$cal->format(array(
					'class'     => 'myCal',
					'padding'   => '0',
					'spacing'   => '0',
					'border'    => '0'
				))

				->set(array(
					'header'    => $date->format('Y-m'),
					'dates'     => $dates
				))

				-> build($date->format('Y-m'))

				-> display();

				$date->add(new DateInterval('P1M'));
			}


	?></div>
</div></div>


<?php include(COREINC.'/end.php'); ?>
<script type="text/javascript" src="ui/vendor/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
<script type="text/javascript" src="manifestation/ui/js/periode.js"></script>

</body>
</html>