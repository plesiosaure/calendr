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

		$this->apiLoad('sendMail')->send(array(
			'to'       => $myUser['userMail'],
			'title'    => $_SERVER['HTTP_HOST'] . ' T\'est dans la place !',
			'template' => 'organisateur-accepted.html',
			'body'     => array(
				'orgName' => $myOrganisation['name']
			)
		));

		$api->go($myUser['id_user'].'?done');
	}





