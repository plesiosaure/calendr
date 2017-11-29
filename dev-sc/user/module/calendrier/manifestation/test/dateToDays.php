<?php

	$api = $app->apiLoad('calendrierPeriode');

	$dates = $api->start('2013-01-01')->end('2013-03-01')->labelToDates('WEEKEND_ALL');

	echo $api->datesToMongoDays($dates);

