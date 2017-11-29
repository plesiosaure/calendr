<?php

	$do = false;
	if(!empty($_GET['_id'])){
		$do = $this->apiLoad('calendrierMvs')->manifestationFails($_GET['_id']);
	}

	echo json_encode(array('ok' => $do));

