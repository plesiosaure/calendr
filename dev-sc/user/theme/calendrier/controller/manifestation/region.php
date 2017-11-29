<?php

	$api = $this->apiLoad('calendrierManifestation');

	#$this->apiLoad('calendrierManifestation')->manigestationMapReduceDept();

	// DATA ////////////////////////////////////////////////////////////////////////////////////////////////////////////

	$myData = $this->apiLoad('calendrierManifestation')->manifestationByRegion();

