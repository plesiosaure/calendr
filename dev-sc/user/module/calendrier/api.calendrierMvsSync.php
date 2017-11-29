<?php

// Dcumentation
// http://217.167.201.245/test_supercal/supercal/doc

class calendrierMvsSync extends calendrier {

	public $domain  = '';
	public $url     = '';

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public function __construct(){
		parent::__construct();

		$config = array();
		require(USER.'/config/config.php');

		$this->url = $config['supercal']['url'];
		$this->domain = $config['supercal']['domain'];

		$this->rest = new coreRest('', '', $this->domain);
		$this->rest->setMode('curl');
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	private function __log(array $data){

		$ok = ($data['raw']['ok'] === true);

		$data = array_merge(array(
			'from'        => 'supercal',
			'from_domain' => $_SERVER['HTTP_HOST'],
			'method'      => $_SERVER['REQUEST_METHOD'],
			'host'        => $this->domain,
			'success'     => $ok
		), $data);

		$this->apiLoad('calendrierLog')->dev($data);
	}

// MANIFESTATION ///////////////////////////////////////////////////////////////////////////////////////////////////////

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public function manifestationGet(array $opt){

		$me = $this->apiLoad('calendrierManifestation')->get(array(
			'_id' => $opt['_id']
		));

		if(empty($me)) return false;

		$mvs  = $this->manifestationBuild($me);
		$uri  = $this->url . '/manifestation/id/' . $me['id'] . '/type/' . $me['mvs']['type'];
		$time = microtime(true);

		$d = $this->rest->request(array(
			'debug' => true,
			'uri'   => $uri,
			'verb'  => 'GET',
			'data'  => $mvs
		));

		$time = microtime(true) - $time;

		if($d['body']['ok']){
			return $d['body'];
		}

		return array();

	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
// UTILISE DEPUIS calendrierManifestation::manifestationCreationManif();
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public function manifestationCreation(array $opt){

		$me = $this->apiLoad('calendrierManifestation')->get(array(
			'_id'    => $opt['_id'],
			'format' => array('date', 'city', 'organisateur')
		));

	#	$this->pre($me);
	#	die('@@');

		if(empty($me)) return false;

		$mvs  = $this->manifestationBuild($me);
		$uri  = $this->url . '/manifestation';
		$time = microtime(true);

		$d = $this->rest->request(array(
			'debug' => true,
			'uri'   => $uri,
			'verb'  => 'PUT',
			'data'  => $mvs
		));

	#	$this->pre(date("Y-m-d H:i:s"), $mvs, 'manifestationCreation', $d);

		$idMVS = $d['body']['data']['id_manifestation'];
		$time  = microtime(true) - $time;

		if($d['body']['ok']){
			$this->apiLoad('calendrierManifestation')->setId(array(
				'_id' => $me['_id'],
				'id'  => $idMVS     // $d['body']['data']['type_manifestation']'_'.$d['body']['data']['id_manifestation']
			));
		}

		$this->__log(array(
			'time'     => $time,
			'url'      => $uri,
			'raw'      => $d['body'],
			'id_manif' => (string) $opt['_id'],
			'api'      => array(
				'args' => $mvs
			)
		));

		return $idMVS;
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public function manifestationDelete(array $opt){

		$me = $this->apiLoad('calendrierManifestation')->get(array(
			'_id'    => $opt['_id'],
			'format' => array()
		));

		if(empty($me)) return false;

		$uri  = $this->url . '/manifestation/id/'.$me['id'].'/type/'.$me['mvs']['type'];
		$time = microtime(true);

		$d = $this->rest->request(array(
			'debug' => true,
			'uri'   => $uri,
			'verb'  => 'DELETE',
			'data'  => array(),
		));

		$time = microtime(true) - $time;

		$this->__log(array(
			'time' => $time,
			'url'  => $uri,
			'raw'  => $d['body'],
			'api'  => array(
				'args' => array()
			)
		));

		return $d['body']['ok'];
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public function manifestationUpdate(array $opt){

		$me = $this->apiLoad('calendrierManifestation')->get(array(
			'_id'    => $opt['_id'],
			'format' => array('date', 'city', 'organisateur')
		));

		if(empty($me)) return false;

		$mvs = $this->manifestationBuild($me);

		$uri = $this->url . '/manifestation';

		$time = microtime(true);

		$d = $this->rest->request(array(
			'debug' => true,
			'uri'   => $uri,
			'verb'  => 'POST',
			'data'  => $mvs
		));

	#	$this->pre('manifestationUpdate', $d);

		$time = microtime(true) - $time;

		$this->__log(array(
			'time'     => $time,
			'url'      => $uri,
			'raw'      => $d['body'],
			'id_manif' => (string) $opt['_id'],
			'api'      => array(
				'args' => $mvs
			)
		));
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	private function manifestationBuild($me){

		$dates = array();
		foreach(($me['date'] ?: array()) as $e){
			$start      = date("Y-m-d", $e['start']);
			$end        = date("Y-m-d", $e['end']);
			$canceled   = $e['canceled']  ? '1' : '0';
			$postponed  = $e['postponed'] ? '1' : '0';
			$unsure     = $e['unsure']    ? '1' : '0';

			$dates[]    = $start.'|'.$end.'|'.$canceled.'|'.$postponed.'|'.$unsure;
		}
		$dates = implode('||', $dates);

		$mvs = array(
			'intitule_manifestation'                    =>  $me['name'],
			'horaires_manifestation'                    =>  $me['schedule'],
			'ouverture_manifestation'                   =>  $me['opening'],
			'adresse_manifestation'                     =>  $me['geo']['address'],
			'situation_geo_manifestation'               =>  $me['geo']['comment'],
			'nb_exposant_manifestation'                 =>  $me['number'],
			'telephone_manifestation'                   =>  $me['phone'],
			'fax_manifestation'                         =>  $me['fax'],
			'mail_manifestation'                        =>  $me['email'],
			'site_web_manifestation'                    =>  $me['web'],
		    'tarif_manifestation'                       =>  $me['price'],
			'resume_date_manifestation'             	=>  $me['resume_date'],
			'communique_manifestation'                  =>  $me['presentation'],
			'type_manifestation'                        =>  $me['mvs']['type'],
			'id_manifestation_supercal'                 =>  $me['_id'],
			'id_categorie_manifestation'                =>  $me['mvs']['category'],
			'id_organisateur_manifestation'             =>  $me['organisateur']['id'],
			'id_ville_manifestation'                    =>  $me['city']['id'],
			'periodicite_manifestation'                 => ($me['periodicity'] ?: 1),
			'type_exposant_pro_manifestation'           => ($me['pro']          ? 1 : 0),
			'type_exposant_particulier_manifestation'   => ($me['individual']   ? 1 : 0),
			'type_exposant_habitant_manifestation'      => ($me['resident']     ? 1 : 0),
			'lieu_interieur_manifestation'              => ($me['indoor']       ? 1 : 0),
			'lieu_exterieur_manifestation'              => ($me['outdoor']      ? 1 : 0),
			'jouet_manifestation'                       => ($me['game']         ? 1 : 0),
			'manifestation_payante'                     => ($me['paying']       ? 1 : 0),
			'manifestation_gratuite'                    => ($me['free']         ? 1 : 0),
			'date_manifestation'                        =>  $dates
		);

		if(!empty($me['id'])) $mvs['id_manifestation'] = $me['id'];

		return $mvs;
	}






// ORGANISATEUR / ///////////////////////////////////////////////////////////////////////////////////////////////////////

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
// UTILISE DEPUIS calendrierManifestation::manifestationCreationOrganisateur();
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public function organisateurCreation(array $opt){

		$me = $this->apiLoad('calendrierOrganisateur')->get(array(
			'_id'    => $opt['_id'],
			'format' => array('organisateur')
		));

		if(empty($me)) return false;

		$uri  = $this->url . '/organisateur';
		$mvs  = $this->organisateurBuild($me);
		$time = microtime(true);

		$d = $this->rest->request(array(
			'uri'  => $uri,
			'verb' => 'PUT',
			'data' => $mvs
		));

	#	print_r($d);
	#	die();

		if(!$d['body']['ok']){
			print_r($mvs);

			echo PHP_EOL.PHP_EOL.PHP_EOL.PHP_EOL.PHP_EOL;

			# Data truncated for column 'id_organisateur_supercal' at ro
			print_r($d['body']);
			die();
		}

		$idMVS = intval($d['body']['data']['id_organisateur']);
		$time  = microtime(true) - $time;

		if($d['body']['ok']){
			$this->apiLoad('calendrierOrganisateur')->setId(array(
				'_id' => $me['_id'],
				'id'  => $idMVS
			));
		}

		$this->__log(array(
			'time' => $time,
			'url'  => $uri,
			'raw'  => $d['body'],
			'api'  => array(
				'args' => $mvs
			)
		));

		return $idMVS;
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public function organisateurDelete(array $opt){

		$uri  = $this->url . '/organisateur/_id/'.$opt['_id'];
		$time = microtime(true);

		$d = $this->rest->request(array(
			'uri'  => $uri,
			'verb' => 'DELETE',
			'data' => array()
		));

		$time = microtime(true) - $time;

		$this->__log(array(
			'time' => $time,
			'url'  => $uri,
			'raw'  => $d['body'],
			'api'  => array(
				'args' => array()
			)
		));

		return $d['body']['ok'];
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public function organisateurUpdate(array $opt){

		$me = $this->apiLoad('calendrierOrganisateur')->get(array(
			'_id'    => $opt['_id'],
			'format' => array('city')
		));

		if(empty($me)) return false;

		$uri = $this->url . '/organisateur'; #/id/'.$me['id'];
		$mvs = $this->organisateurBuild($me);

		$time = microtime(true);

		$d = $this->rest->request(array(
			'debug' => true,
			'uri'   => $uri,
			'verb'  => 'POST',
			'data'  => $mvs
		));

		$time = microtime(true) - $time;

		$this->__log(array(
			'time' => $time,
			'url'  => $uri,
			'raw'  => $d['body'],
			'api'  => array(
				'args' => $mvs
			)
		));

	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	private function organisateurBuild($me){

		$mvs = array(
			'id_organisateur'                => $me['id'],
			'raison_sociale_organisateur'    => $me['name'],
			'civilite_organisateur'          => $me['title'],
			'nom_organisateur'               => $me['lastname'],
			'prenom_organisateur'            => $me['firstname'],
			'adresse_organisateur'           => $me['address'],
			'id_ville_organisateur'          => $me['city']['id'],
			'telephone_organisateur'         => $me['phone'],
			'fax_organisateur'               => $me['fax'],
			'mobile_organisateur'            => $me['mobile'],
			'fonction_organisateur'          => $me['fonction'],
			'email_organisateur'             => $me['email'],
			'siteweb_organisateur'           => $me['siteweb'],
			'commentaire_organisateur'       => $me['commentaire'],
		//	'date_creation_organisateur'     => $me[''],
		//	'date_modification_organisateur' => $me[''],
			'rubrique_organisateur'          => $me['rubrique'],
			'npai_organisateur'              => ($me['npai'] ? 1 : 0),
			'id_organisateur_supercal'       => $me['_id']
		//	'com_supercal'                   => $me[''],
		);

		return $mvs;
	}

}

