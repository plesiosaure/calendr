<?php

	$api = $this->apiLoad('calendrierOrganisateur');

	// DATA ////////////////////////////////////////////////////////////////////////////////////////////////////////////

	$myOrganisation = $api->get(array(
		'debug'  => false,
		'format' => array(),
		'_id'    => $this->kodeine['get']['id_organisateur']
	));

	if(empty($myOrganisation)) $this->go('/?organisateurNotFound');

	$myUser = $this->apiLoad('user')->userGet(array(
		'id_user' => $this->kodeine['get']['id_user']
	));

	if(empty($myOrganisation)) $this->go('/?userToManageNotFound');

	// ACTION //////////////////////////////////////////////////////////////////////////////////////////////////////////

	if($_POST['confirmed'] == 'YES'){

		$api->member(array(
			'_id'     => $myOrganisation['_id'],
			'id_user' => $myUser['id_user']
		));

		$this->apiLoad('sendMail')->mandrill(array(
			'template' => 'organisateur-accepted',
			'message'  => array(
				'track_opens' => true,
				'tags'        => array('supercalendrier', 'organisateur', 'rejected'),
				'to'          => array(
					array('type' => 'to', 'email' => $myUser['userMail'])
				),
				'global_merge_vars' => array(
					array('name' => 'org_name', 'content' => $myOrganisation['name'])
				)
			)
		));

		$api->go($myUser['id_user'].'?done');
	}





