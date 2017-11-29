<?php

	$api = $this->apiLoad('calendrierOrganisateur');

	// DATA ////////////////////////////////////////////////////////////////////////////////////////////////////////////

	$myOrganisation = $this->apiLoad('calendrierOrganisateur')->get(array(
		'debug'  => false,
		'format' => array(),
		'_id'    => $this->kodeine['get']['id_organisateur']
	));

	if(empty($myOrganisation)) $this->go('/?organisateurNotFound');

	if(!is_array($myOrganisation['pending'])) $myOrganisation['pending'] = array();
	if(!is_array($myOrganisation['member']))  $myOrganisation['member'] = array();

	$ids = $myMembers = array();

	$ids = array_merge($myOrganisation['pending'], $myOrganisation['member']);

	if(!empty($ids)){

		$myMembers = $this->apiLoad('user')->userGet(array(
			'id_user' => $ids
		));

		foreach($myMembers as $n => $e){
			if(in_array(intval($e['id_user']), $myOrganisation['pending'])) $myMembers[$n]['isPending'] = true;
		}
	}



	// ACTION //////////////////////////////////////////////////////////////////////////////////////////////////////////



