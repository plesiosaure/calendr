<?php

	if(!defined('COREINC')) die('Direct access not allowed');
	$api  = $app->apiLoad('calendrierManifestation');

	if(isset($_GET['k'])){
		$api->purge();
		$app->go('purge');
	}

	$count = $api->purgeCount();

?><!DOCTYPE html>
<html lang="fr">
<head>
	<base href="../" />
	<?php include(COREINC . '/head.php'); ?>
	<link rel="stylesheet" type="text/css" href="ui/css/main.css" />
	<link rel="stylesheet" type="text/css" href="ui/vendor/bootstrap-datepicker/css/datepicker.css" />
</head>
<body>

<header><?php
	include(COREINC . '/top.php');
	include(dirname(__DIR__). '/ui/menu.php')
?></header>


<div id="app"><div style="text-align: center">

	Il y a <?php echo $count ?> manifestation supprimé, <button onclick="ask()">voulez-vouez purger</button> ?

</div></div>


<?php include(COREINC.'/end.php'); ?>
<script>
	function ask(){
		if(confirm("Vraiment ?")){
			document.location.href = 'manifestation/purge?k';
		}
	}
</script>

</body>
</html>