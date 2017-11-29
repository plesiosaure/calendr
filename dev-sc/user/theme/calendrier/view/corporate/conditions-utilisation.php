<?php

	$content = $this->apiLoad('content')->contentGet(array(
		'id_content' => 50222
	));

	include USER . '/template/cal-default/detail.php';


