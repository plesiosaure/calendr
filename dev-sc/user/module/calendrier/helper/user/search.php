<?php

	$q = urldecode($_GET['q']);

	$users = $app->apiLoad('user')->userGet(array(
		'debug'      => false,
		'searchLink' => 'OR',
		'search'     => array(
			array('searchField' => 'userNom',     'searchValue' => $q, 'searchMode' => 'CT'),
			array('searchField' => 'userPrenom',  'searchValue' => $q, 'searchMode' => 'CT'),
			array('searchField' => 'userPseudo',  'searchValue' => $q, 'searchMode' => 'CT')
		)
	));

	foreach($users as $e){
		$out[] = array(
			'id_user'   => $e['id_user'],
			'name'      => $e['field']['userPrenom'].' '.$e['field']['userNom'],
			'pseudo'    => $e['field']['userPseudo']
		);
	}

	echo $app->helperJsonBeautifier($app->helperJsonEncode($out));
