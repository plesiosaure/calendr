<?php


	$d = $this->apiLoad('sendMail')->mandrill(array(
		'template' => 'moderation-manifestation-rejected',
		'message'  => array(
			'from_email'  => 'no-reply@supercalendrier.com',
			'from_name'   => 'Super Calendrier',
			'track_opens' => true,
			'tags'        => array('supercalendrier', 'moderation', 'rejet'),
			'to'          => array(
				array(
					'type'  => 'to',
					'email' => 'bm@kappuccino.org'
				)
			),
			'global_merge_vars' => array(
				array('name' => 'name',     'content' => 'NOM'),
				array('name' => 'message',  'content' => "message\nsur\n3 lignes")
			)
		)
	));

	var_dump($d);
