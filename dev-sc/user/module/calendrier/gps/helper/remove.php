<?php

	$app->apiLoad('calendrierGeocode')->removeThisCache($_POST['_id']);

	echo json_encode(array('ok' => true));