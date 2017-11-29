<?php

	$api = $this->apiLoad('calendrierManifestation');

	// DATA ////////////////////////////////////////////////////////////////////////////////////////////////////////////

	$args = array(
		'_id'    => $_REQUEST['id'],
		'debug'  => false,
		'format' => array('orderDate')
	);

	$myManifestation = $api->get($args);

	if(empty($myManifestation)) $this->go('/?manifestationNotFound='.$this->kodeine['get']['id_manifestation']);


	// UPDATE //////////////////////////////////////////////////////////////////////////////////////////////////////////
	if(!empty($_POST['id'])){

		$editor = array(
			'name'         => $_POST['org_name'],
			'lastname'     => $_POST['org_lastname'],
			'organisation' => $_POST['org_organisation'],
			'email'        => $_POST['org_email'],
			'phone'        => $_POST['org_phone']
		);

		// Sauver la manifestation
		$saved = $this->apiLoad('calendrierManifestation')->manifestationTemp(array(
			'_id'        => $_POST['id'],
			'editor'     => $editor,
			'moderation' => 'remove'
		));

		if($saved){

			$accept = 'http://'.$_SERVER['HTTP_HOST'].'/organisateur/accept?_id='.$_POST['id'];
			$reject = 'http://'.$_SERVER['HTTP_HOST'].'/organisateur/reject?_id='.$_POST['id'];

			// Mail envoyé à celui qui a fait la modification
			$m = array(
				'template' => 'organisateur-remove-confirmation-editor',
				'message'  => array(
					'track_opens' => true,
					'tags'        => array('supercalendrier', 'organisateur', 'remove'),
					'to'          => array(
						array('type' => 'to',  'email' => $_POST['org_email']),
						array('type' => 'bcc', 'email' => EMAIL_BCC),
					),
					'global_merge_vars' => array(
						array('name' => 'manifestation', 'content' => $myManifestation['name']),
						array('name' => 'link_accept',   'content' => $accept),
						array('name' => 'link_reject',   'content' => $reject)
					)
				)
			);

			$this->apiLoad('sendMail')->mandrill($m);

			// Mail envoyé à l'email inscrit dans la manif si différent
			if($_POST['org_email'] != $myManifestation['email']){
				$m = array(
					'template' => 'organisateur-remove-confirmation-owner',
					'message'  => array(
						'track_opens' => true,
						'tags'        => array('supercalendrier', 'owner', 'remove'),
						'to'          => array(
							array('type' => 'to',  'email' => $_POST['org_email']),
							array('type' => 'bcc', 'email' => EMAIL_BCC),
						),
						'global_merge_vars' => array(
							array('name' => 'editor',        'content' => $_POST['org_email']),
							array('name' => 'manifestation', 'content' => $myManifestation['name']),
							array('name' => 'link_accept',   'content' => $accept),
							array('name' => 'link_reject',   'content' => $reject)
						)
					)
				);

				$this->apiLoad('sendMail')->mandrill($m);
			}

			$this->go('remove-after?_id='.$_POST['id']);

		}else{
			die('saved failed, cas a traiter');
		}

	}

