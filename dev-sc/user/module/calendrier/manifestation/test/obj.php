<?php


	$a = $app->apiLoad('calendrierEvent');
	$a->kodeine['z'] = 'z';
	$a->set('user', 'A');

	$app->pre("A", $a->data(), $a->kodeine['z']);









	$b = $app->apiLoad('calendrierEvent');
	$b->set('user', 'B');

	$app->pre("B", $a->data(), $a->kodeine['z']);


