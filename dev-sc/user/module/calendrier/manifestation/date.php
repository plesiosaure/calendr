<?php

	if(!defined('COREINC')) die('Direct access not allowed');
	$api = $app->apiLoad('calendrierManifestationDate');

// GET /////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	if($_REQUEST['_id'] != NULL){
		$data = $api->get(array(
			'debug' => false,
			'_id'   => $_REQUEST['_id']
		));
	}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	if($data['_id'] == NULL) $app->go('./');

?><!DOCTYPE html>
<html lang="fr">
<head>
	<base href="../" />
	<?php include(COREINC . '/head.php'); ?>
	<link rel="stylesheet" type="text/css" href="../content/ui/css/data.css" />
	<link rel="stylesheet" type="text/css" href="ui/css/main.css" />
	<link rel="stylesheet" type="text/css" href="ui/css/all.css" />
	<link rel="stylesheet" type="text/css" href="manifestation/ui/css/date.css" />
	<link rel="stylesheet" type="text/css" href="ui/vendor/bootstrap-datepicker/css/datepicker.css" />
</head>
<body data-_id="<?php echo $data['_id'] ?>">

<header><?php
	include(COREINC . '/top.php');
	include(dirname(__DIR__) . '/ui/menu.php')
?></header>

<div class="inject-subnav-right hide">
	<li><a id="saveBtn" class="btn btn-small btn-success">Enregistrer</a></li>
</div>

<div id="app"><div id="configurator">

	<div id="svg"></div>

	<div id="dates">

		<div class="new-date">
			<input size="8" type="text" placeholder="Nouvelle date" readonly="" id="new-date" />
			<a href="manifestation/periode?_id=<?php echo $data['_id'] ?>" class="btn btn-mini">Période</a>
		</div>


		<ul></ul>
	</div>

	<div id="form"><div class="data">

		<div class="overlay">
			<div class="mess">
				Sélectionnez une ou plusieurs dates pour modifier les paramètres
			</div>
		</div>

		<div class="tabset">

			<div class="view">

				<div class="view-label">
					<span>Paramètres pour les dates sélectionnées</span>
				</div>

				<ul class="field-list">
					<li class="clearfix form-item">
						<label>Date</label>
						<div class="form">
							<input type="text" name="start" data-date="" />
						</div>
					</li>
					<li class="clearfix form-item">
						<label>Durée</label>
						<div class="form">
							<input type="number" class="days" name="days" max="30" min="1" step="1" />
						</div>
					</li>
					<li class="clearfix form-item">
						<label>Param</label>
						<div class="form">
							<input type="text" name="comment" style="width:99%;" />
						</div>
					</li>
				</ul>

			</div>
		</div>

	</div></div>

</div></div>

<script type="text/template" id="date-item">
	<i class="remove icon-remove"></i>
	<span class="date"><%- display %></span>
	<i class="circle"></i>
</script>

<?php include(COREINC.'/end.php'); ?>
<script type="text/javascript" src="../core/vendor/underscore/underscore-min.js"></script>
<script type="text/javascript" src="../core/vendor/backbone/backbone-min.js"></script>

<script type="text/javascript" src="ui/vendor/raphael/raphael-min.js"></script>
<script type="text/javascript" src="ui/vendor/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>

<script type="text/javascript" src="manifestation/ui/js/date.js"></script>

</body>
</html>