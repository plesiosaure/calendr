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
		$ok = 'http://'.$_SERVER['HTTP_HOST'].'/compte/organisateur/'.$myOrganisation['_id'].'/accept/'.$me['id_user'];
		$ko = 'http://'.$_SERVER['HTTP_HOST'].'/compte/organisateur/'.$myOrganisation['_id'].'/reject/'.$me['id_user'];

		// Failover - ou pas de membre a prevenir
		if(empty($to)){
			$to = 'bm@kappuccino.org';
		}

		$data = array(
			'orgName'    => $myOrganisation['name'],
			'pickerName' => $me['field']['userPrenom'] . ' ' . $me['field']['userNom'],
			'ok'         => $ok,
			'ko'         => $ko
		);

		$m = $this->apiLoad('sendMail')->send(array(
			'to'       => $to,
			'title'    => $_SERVER['HTTP_HOST'] . ' Demande de rattachement',
			'template' => $template,
			'body'     => $data
		));

		$this->go($myOrganisation['_id'].'?done');
	}

