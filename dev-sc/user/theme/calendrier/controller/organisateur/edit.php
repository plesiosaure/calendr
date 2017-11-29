<?php

	$api = $this->apiLoad('calendrierManifestation');

	// DATA ////////////////////////////////////////////////////////////////////////////////////////////////////////////

	$args = array(
		'_id'    => $_REQUEST['id'],
		'debug'  => false,
		'format' => array('orderDate')
	);

	$myManifestation = $api->get($args);

	if (empty($myManifestation)) $this->go('/?manifestationNotFound=' . $this->kodeine['get']['id_manifestation']);


	// FORMAT //////////////////////////////////////////////////////////////////////////////////////////////////////////

	$myNearestDate = $this->apiLoad('calendrierManifestationDate')->nearest($myManifestation['date']);

	$myType = $this->apiLoad('calendrierManifestationType')->nameFromId($myManifestation['mvs']['type']);

	$myCategory = $this->apiLoad('calendrierManifestationType')->typeFromId($myManifestation['mvs']['category']);

	$myRegion = $this->apiLoad('calendrierDepartement')->regionGet(array(
		'dep' => $myManifestation['city']['dep']
	));

	$myDepartement = $this->apiLoad('calendrierDepartement')->departementGet(array(
		'code' => $myManifestation['city']['dep']
	));

	if ($myManifestation['mvs']['type'] == 1) {
		$myTypeImage = 'collection';
	} else
	if ($myManifestation['mvs']['type'] == 2) {
		$myTypeImage = 'auto';
	} else
	if ($myManifestation['mvs']['type'] == 3) {
		$myTypeImage = 'moto';
	}

	if(!empty($myManifestation['image'])){
		$myPoster = '';
	}

	$_POST['free']   = ($_POST['fee'] == 'free')   ? 1 : 0;
	$_POST['paying'] = ($_POST['fee'] == 'paying') ? 1 : 0;

	// UPDATE //////////////////////////////////////////////////////////////////////////////////////////////////////////

	if (!empty($_POST['id'])) {

		$phones = $_POST['phones'];
		unset($phones['n']);

		$manif = array(
			'name'              => $_POST['name'],
			'dates'              => $_POST['dates'],
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

			'presentation'     => $_POST['presentation'],
			'presentation_web' => $_POST['presentation_web'],
		#	'resume_date'      => $_POST['resume_date'],

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
		if (!empty($_POST['images'])) {
			$manif['images'] = array_map('urldecode', $_POST['images']);
		}

		$editor = array(
			'name'         => $_POST['org_name'],
			'lastname'     => $_POST['org_lastname'],
			'organisation' => $_POST['org_organisation'],
			'email'        => $_POST['org_email'],
			'phone'        => $_POST['org_phone'],
			'address'      => $_POST['org_address'],
			'zip'          => $_POST['org_zip'],
			'city'         => $_POST['org_city'],
			'country'      => $_POST['org_country'],

		);

		// Sauver la manifestation
		$saved = $this->apiLoad('calendrierManifestation')->manifestationTemp(array(
			'_id'           => $_POST['id'],
			'poster'        => $_POST['poster-url'],
			'manifestation' => $manif,
			'editor'        => $editor,
			'moderation'    => 'update'
		));

		if ($saved) {

			$accept = 'http://' . $_SERVER['HTTP_HOST'] . '/organisateur/accept?_id=' . $_POST['id'];
			$reject = 'http://' . $_SERVER['HTTP_HOST'] . '/organisateur/reject?_id=' . $_POST['id'];

			// Mail envoyé à celui qui a fait la modification
			$m = array(
				'template' => 'organisateur-edit-confirmation-editor',
				'message'  => array(
					'track_opens'       => true,
					'tags'              => array('supercalendrier', 'organisateur', 'edit'),
					'to'                => array(
						array('type' => 'to',  'email' => $_POST['org_email']),
						array('type' => 'bcc', 'email' => EMAIL_BCC),
					),
					'global_merge_vars' => array(
						array('name' => 'manifestation', 'content' => $myManifestation['name']),
						array('name' => 'link_accept', 'content' => $accept),
						array('name' => 'link_reject', 'content' => $reject)
					)
				)
			);

			$this->apiLoad('sendMail')->mandrill($m);

			$this->go('edit-after?_id=' . $_POST['id']);

		} else {
			die('saved failed, cas a traiter');
		}

	}

