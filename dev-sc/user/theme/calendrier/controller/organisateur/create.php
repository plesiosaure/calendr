<?php

	$api     = $this->apiLoad('calendrierManifestation');
	$apiType = $this->apiLoad('calendrierManifestationType');


	// INSERT //////////////////////////////////////////////////////////////////////////////////////////////////////////
	if(!empty($_POST)){

		$_POST['free']   = ($_POST['fee'] == 'free')   ? 1 : 0;
		$_POST['paying'] = ($_POST['fee'] == 'paying') ? 1 : 0;

		$phones = $_POST['phones'];
		unset($phones['n']);

		$manif = array(
			'name'              => $_POST['name'],
			'dates'             => $_POST['dates'],
		#	'resume_date'       => $_POST['resume_date'],
			'city'              => $_POST['id_ville'],

			'schedule'          => $_POST['schedule'],
			'opening'           => $_POST['opening'],

			'number'            => $_POST['number'],
			'pro'               => $_POST['indoor'],
			'individual'        => $_POST['individual'],
			'resident'          => $_POST['resident'],

			'indoor'            => $_POST['indoor'],
			'outdoor'           => $_POST['outdoor'],

			'free'              => $_POST['free'],
			'paying'            => $_POST['paying'],
			'price'             => $_POST['price'],

		#	'phone'             => $_POST['phone'],
			'phones'            => $phones,
		#	'fax'               => $_POST['fax'],
			'email'             => $_POST['email'],
			'web'               => $_POST['web'],

			'presentation'      => $_POST['presentation'],
			'presentation_web'  => $_POST['presentation_web'],

			'geo'               => array(
				'address' => $_POST['address'],
				'comment' => $_POST['situation'],
				'country' => $_POST['country']
			)
		);


		// Si on a une map
		if(!empty($_POST['lat']) && !empty($_POST['lng']) && !empty($_POST['zoom'])){
			$manif['geo']['gps']  = array(floatval($_POST['lat']), floatval($_POST['lng']));
			$manif['geo']['zoom'] = intval($_POST['zoom']);
		}

		// Si on a ajouté des images
		if(!empty($_POST['images'])){
			$manif['images'] = array_map('urldecode', $_POST['images']);
		//if(empty($_POST['poster-url'])) $_POST['poster-url'] = $manif['images'][0];
		}

		// Types de manif (auto / moto / colleciton)
		$types = array();
		if(!empty($_POST['cat-auto']))       $types['auto']       = $_POST['cat-auto'];
		if(!empty($_POST['cat-moto']))       $types['moto']       = $_POST['cat-moto'];
		if(!empty($_POST['cat-collection'])) $types['collection'] = $_POST['cat-collection'];

		$editor = array(
			'name'         => $_POST['org_name'],
			'lastname'     => $_POST['org_lastname'],
			'organisation' => $_POST['org_organisation'],
			'email'        => $_POST['org_email'],
			'phone'        => $_POST['org_phone'],
			'address'      => $_POST['org_address'],
			'city'         => $_POST['org_id_ville']
		);

		// Sauver la manifestation
		$saved = $this->apiLoad('calendrierManifestation')->manifestationDispatch(array(
			'manifestation' => $manif,
			'type'          => $types,
			'editor'        => $editor,
			'poster'        => ($_POST['poster-url'] ?: '')
		));

		if($saved){

			$ids    = implode(',', $saved);
			$accept = 'http://'.$_SERVER['HTTP_HOST'].'/organisateur/accept?_id='.$ids;
			$reject = 'http://'.$_SERVER['HTTP_HOST'].'/organisateur/reject?_id='.$ids;

			// Mail envoyé à celui qui a fait la modification
			$m = array(
				'template' => 'organisateur-creation-confirmation-editor',
				'message'  => array(
					'track_opens' => true,
					'tags'        => array('supercalendrier', 'organisateur', 'creation'),
					'to'          => array(
						array('type' => 'to',  'email' => $_POST['org_email']),
						array('type' => 'bcc', 'email' => EMAIL_BCC),
					),
					'global_merge_vars' => array(
						array('name' => 'manifestation', 'content' => $_POST['name']),
						array('name' => 'link_accept',   'content' => $accept),
						array('name' => 'link_reject',   'content' => $reject)
					)
				)
			);

			$this->apiLoad('sendMail')->mandrill($m);

			$this->go('create-after?_id='.$ids);

		}else{
			die('saved failed, cas a traiter');
		}
	}

