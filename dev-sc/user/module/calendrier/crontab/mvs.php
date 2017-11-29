#!/usr/bin/php
<?php

	$mode = isset($_SERVER['argv'][1]) ? $_SERVER['argv'][1]: 'dev';
	echo "MODE = ".$mode.PHP_EOL;

	$files = array(
		__DIR__.'/1_ville.php '.$mode,
		__DIR__.'/2_organisateur.php '.$mode,
		__DIR__.'/3_manif.php '.$mode.' 1',
		__DIR__.'/3_manif.php '.$mode.' 2',
		__DIR__.'/3_manif.php '.$mode.' 3',
	);

	echo date("Y-m-d H:i:s")."\n";

	foreach($files as $e){
		echo $e."\n";
		echo shell_exec($e);
	}
