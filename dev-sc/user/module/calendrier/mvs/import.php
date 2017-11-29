<?php

	if(!defined('COREINC')) die('Direct access not allowed');
	$api  = $app->apiLoad('calendrierLog');

?><!DOCTYPE html>
<html lang="fr">
<head>
	<base href="../" />
	<?php include(COREINC . '/head.php'); ?>
	<link rel="stylesheet" type="text/css" href="mvs/ui/css/import.css" />
</head>
<body>

<header><?php
	include(COREINC . '/top.php');
	include(dirname(__DIR__). '/ui/menu.php')
?></header>

<div id="app"><div class="row-fluid">

	<div id="mvs-import">

		<div style="text-align:center; padding: 10px; margin-bottom: 50px;">
			<a id="start" class="btn btn-success" style="color:white;">Lancer l'import depuis MVS</a>
			<p>Ne pas fermer cette fenêtre le temps de l'opération</p>
		</div>

		<div class="item">
			<b class="name">Organisateurs</b>
			<div class="progress" id="organisateur">
				<div class="bar" style="width: 5%"></div>
			</div>
			<div id="organisateur-log" class="log">En attente</div>
		</div>


		<div class="item">
			<b class="name">Manifestation</b>
			<div class="progress" id="manifestation">
				<div class="bar" style="width: 5%"></div>
			</div>
			<div id="manifestation-log" class="log">En attente</div>
		</div>

	</div>

</div></div>

<?php include(COREINC.'/end.php'); ?>
<script type="text/javascript" src="mvs/ui/js/import.js"></script>

</body>
</html>