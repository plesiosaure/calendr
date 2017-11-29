<?php

	$diff = $app->apiLoad('calendrierManifestation')->compareToOriginal('5313c60b8f7b143654010812');

	$app->pre($diff);

	var_dump($diff);