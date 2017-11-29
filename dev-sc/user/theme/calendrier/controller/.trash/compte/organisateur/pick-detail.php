<?php

	$api = $this->apiLoad('calendrierOrganisateur');

	// ORGANISATEUR ////////////////////////////////////////////////////////////////////////////////////////////////////

	$myOrganisation = $api->get(array(
		'debug'  => false,
		'format' => array(),
		'_id'    => $this->kodeine['get']['id_organisateur']
	));



	// PENDING /////////////////////////////////////////////////////////////////////////////////////////////////////////

	if($_POST['confirmed'] == "YES"){

		$this->apiLoad('calendrierOrganisateur')->pending(array(
			'_id'     => $myOrganisation['_id'],
			'id_user' => $me['id_user']
		));

		if(!empty($myOrganisation['member'])){

			$user = $this->apiLoad('user')->userGet(array(
				'id_user' => $myOrganisation['member'][0]
			));

			$to = $user['userMail'];
		}

		$template = 'organisateur-pick.html';
		$name     = $me['field']['userPrenom'] . ' ' . $me['field']['userNom'];

		$ok = 'http://'.$_SERVER['HTTP_HOST'].'/compte/organisateur/'.$myOrganisation['_id'].'/accept/'.$me['id_user'];
		$ko = 'http://'.$_SERVER['HTTP_HOST'].'/compte/organisateur/'.$myOrganisation['_id'].'/reject/'.$me['id_user'];

		// Failover - ou pas de membre a prevenir
		if(empty($to)) $to = 'contact@supercalendrier.com';

		$this->apiLoad('sendMail')->mandrill(array(
			'template' => 'organisateur-pick',
			'message'  => array(
				'track_opens' => true,
				'tags'        => array('supercalendrier', 'organisateur', 'pick'),
				'to'          => array(
					array('type' => 'to', 'email' => $to)
				),
				'global_merge_vars' => array(
					array('name' => 'org_name',     'content' => $myOrganisation['name']),
					array('name' => 'picker_name',  'content' => $name),
					array('name' => 'url_ok',       'content' => $ok),
					array('name' => 'url_ko',       'content' => $ko),
				)
			)
		));

		$this->go($myOrganisation['_id'].'?done');
	}

