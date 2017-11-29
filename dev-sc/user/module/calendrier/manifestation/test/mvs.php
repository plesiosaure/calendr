<?php

die();


	$api = $app->apiLoad('calendrierManifestation');

	class test extends coreApp {

		public function __construct(){
			$this->rest = new coreRest('', '', '217.167.201.245');
			$this->rest->setMode('classic');
		}
	}

	$rest = new test();

	$d = $rest->rest->request(array(
		'debug' => true,
		'uri' => '/test_supercal/supercal/ville/id/50506',
		'verb' => 'GET',
		'data' => array(
			'id_ville_supercal'    => '123',
			'id_departement_ville' => '97',
			'code_postal_ville'    => '01000',
			'nom_ville'            => 'Benjamin City'
		)
	));

	$d['body'] = json_decode($d['body'], true);


	print_r($d);

