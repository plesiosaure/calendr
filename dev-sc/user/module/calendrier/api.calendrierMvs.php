<?php


class calendrierMvs extends calendrier {

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	private function fail($err){
		return array('ok' => false, 'err' => $err);
	}

	function mysqlSource(){

		$config = array();
		require(USER.'/config/config.php');
		$key = $config['supercal']['source'];

		return "`".$key."`";
	}

// VILLE ///////////////////////////////////////////////////////////////////////////////////////////////////////////////

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public function villeGet(array $opt){

		$cond = array(
			'format' => array()
		);

		if($opt['method'] == 'id'){
			$cond['id'] = intval($opt['id']);
		}else
		if($opt['method'] == '_id'){
			$cond['_id'] = new MongoId($opt['id']);
		}

		$data = $this->apiLoad('calendrierCity')->get($cond);

		return empty($data)
			? $this->fail('No data found with this id')
			: array('ok' => true, 'data' => $data);
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public function villeCreation(array $opt){

		if(empty($opt['name']))     return $this->fail('parameter name is missing');
		if(empty($opt['zip']))      return $this->fail('parameter zip is missing');
		if(empty($opt['id']))       return $this->fail('parameter id is missing');
		if(empty($opt['id_dep']))   return $this->fail('parameter id_dep is missing');

		// Check Existing
		$api = $this->apiLoad('calendrierCity');
		$tmp = $api->get(array(
			'id' => intval($opt['id'])
		));

		if(!empty($tmp)) return $this->fail('Data already exists with id:'.$opt['id']);

		$data = array(
			'id'     => intval($opt['id']),
			'name'   => $opt['name'],
			'zip'    => $opt['zip'],
			'id_dep' => intval($opt['id_dep'])
		);

		$db = $api->set($data)->debug(false)->save()->retrieve(array(
			'format' => array()
		));

		return array('ok' => true, 'data' => $db);
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public function villeDelete(array $opt){

		$cond = array();

		if($opt['method'] == 'id'){
			$cond['id'] = intval($opt['id']);
		}else
		if($opt['method'] == '_id'){
			$cond['_id'] = new MongoId($opt['id']);
		}

		$job  = $this->apiLoad('calendrierCity')->del($cond);

		return array('ok' => $job);
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public function villeUpdate(array $opt){

		if($opt['method'] == 'id'){
			$cond['id'] = intval($opt['id']);
		}else
		if($opt['method'] == '_id'){
			$cond['_id'] = new MongoId($opt['id']);
		}else{
			return $this->fail('No method');
		}

		$api  = $this->apiLoad('calendrierCity');
		$test = $api->get($cond);
		if(empty($test)) return $this->fail('No data found');

		$data = array();
		if(!empty($opt['zip']))    $data['zip']    = $opt['zip'];
		if(!empty($opt['name']))   $data['name']   = $opt['name'];
		if(!empty($opt['id_dep'])) $data['id_dep'] = intval($opt['id_dep']);

		if(count($data) > 0){
			$upd = array_merge($cond, array('data' => $data));
			$job = $api->update($upd);
		}else{
			return $this->fail('No data to update');
		}

		if($job){
			$data = $api->get(array_merge($cond, array(
				'format' => array()
			)));

			return array('ok' => $job, 'data' => $data);
		}else{
			return $this->fail('Update failed');
		}

	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public function villeImport(){

		$bdd = $this->mysqlSource();

		echo 'q';
		$raw = $this->dbMulti("
			SELECT * FROM ".$bdd.".lva_ville

			LEFT JOIN ".$bdd.".lva_departement ON ".$bdd.".lva_ville.id_departement_ville        = ".$bdd.".lva_departement.id_departement
			LEFT JOIN ".$bdd.".lva_region      ON ".$bdd.".lva_departement.id_region_departement = ".$bdd.".lva_region.id_region
			LEFT JOIN ".$bdd.".lva_pays        ON ".$bdd.".lva_region.id_pays_region             = ".$bdd.".lva_pays.id_pays

			WHERE id_ville > 0 AND ville_4d IS NOT NULL
		");
		echo ' ..end'.PHP_EOL;

		$mon  = $this->mongoAuth();
		$db   = $mon->selectDB($this->db);
		$col  = $mon->selectCollection($db, 'city');

		$dep  = $this->apiLoad('calendrierDepartement');

		#$col->remove(array());

		$total = count($raw);
		$done = 0;
		foreach($raw as $e){

		#	print_r($e);

			$data = array(
				'id'      => intval($e['id_ville']),
				'id_dep'  => intval($e['id_departement_ville']),
				'zip'     => $e['code_postal_ville'],
				'name'    => trim($e['nom_ville']),
				'country' => $dep->countryFromLVA($e['code_pays'])
			);

		#	print_r($data);
		#	die();

		#	print_r($data);

			$col->update(
				array('id' => intval($e['id_ville'])),
				array('$set' => $data),
				array('upsert' => true)
			);
		#	die();

			$done++;
			echo (100 * ($done / $total)).PHP_EOL;
		}

		echo PHP_EOL;

		return array('ok' => true, 'total' => count($raw));

	}




// ORGANISATEUR ////////////////////////////////////////////////////////////////////////////////////////////////////////

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public function organisateurGet(array $opt){

		$cond = array(
			'format' => array('city')
		);

		if($opt['method'] == 'id'){
			$cond['id'] = intval($opt['id']);
		}else
		if($opt['method'] == '_id'){
			$cond['_id'] = new MongoId($opt['id']);
		}

		$data = $this->apiLoad('calendrierOrganisateur')->get($cond);

		return empty($data)
			? $this->fail('No data found with this id')
			: array('ok' => true, 'data' => $data);
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public function organisateurCreation(array $opt){

		if(empty($opt['name']))     return $this->fail('parameter name is missing');
		if(empty($opt['id']))       return $this->fail('parameter id is missing');

		// Check Existing
		$api = $this->apiLoad('calendrierOrganisateur');
		$tmp = $api->get(array(
			'id' => intval($opt['id'])
		));

		if(!empty($tmp)) return $this->fail('Data already exists with id:'.$opt['id']);

		// Strict minimum
		$data = array(
			'id'      => intval($opt['id']),
			'name'    => $opt['name'],
			'created' => new MongoDate(),
			'updated' => new MongoDate()
		);

		$fields = array('title', 'firstname', 'lastname', 'fonction', 'address', 'city', 'phone', 'fax',
			'mobile', 'email', 'web', 'commentaire');

		foreach($fields as $e){
			if(!empty($opt[$e])){
				if($e == 'city'){
					$city = $this->apiLoad('calendrierCity')->get(array(
						'id'     => intval($opt['city']['id']),
						'format' => array()
					));

					if(!empty($city)) $data['city'] = array('_id' => $city['_id']);

				}else{
					$data[$e] = $opt[$e];
				}
			}
		}

		$db = $api->set($data)->debug(false)->save()->retrieve(array(
			'format' => array('city')
		));

		return array('ok' => true, 'data' => $db);
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public function organisateurDelete(array $opt){

		$cond = array();

		if($opt['method'] == 'id'){
			$cond['id'] = intval($opt['id']);
		}else
		if($opt['method'] == '_id'){
			$cond['_id'] = new MongoId($opt['id']);
		}

		$job  = $this->apiLoad('calendrierOrganisateur')->del($cond);

		return array('ok' => $job);
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public function organisateurUpdate(array $opt){

		if($opt['method'] == 'id'){
			$cond['id'] = intval($opt['id']);
		}else
		if($opt['method'] == '_id'){
			$cond['_id'] = new MongoId($opt['id']);
		}else{
			return $this->fail('No method');
		}

		$api  = $this->apiLoad('calendrierOrganisateur');
		$test = $api->get($cond);
		if(empty($test)) return $this->fail('No data found');

		$data   = array();
		$fields = array('name', 'title', 'firstname', 'lastname', 'fonction', 'address', 'city', 'phone', 'fax',
				  'mobile', 'email', 'web', 'commentaire');

		foreach($fields as $e){
			if(!empty($opt[$e])){
				if($e == 'city'){
					$city = $this->apiLoad('calendrierCity')->get(array(
						'id'     => intval($opt['city']['id']),
						'format' => array()
					));

					if(!empty($city)) $data['city'] = array('_id'  => $city['_id']);
				}else{
					$data[$e] = $opt[$e];
				}
			}
		}

		if(count($data) > 0){
			$upd = array_merge($cond, array('data' => $data));
			$job = $api->update($upd);
		}else{
			return $this->fail('No data to update');
		}

		if($job){
			$data = $api->get(array_merge($cond, array(
				'format' => array('city')
			)));

			return array('ok' => $job, 'data' => $data);
		}else{
			return $this->fail('Update failed');
		}
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public function organisateurImport(array $opt){

		$bdd  = $this->mysqlSource();
		$raw  = $this->dbMulti("SELECT * FROM ".$bdd.".lva_cal_organisateur");

		$mon  = $this->mongoAuth();
		$db   = $mon->selectDB($this->db);
		$col  = $mon->selectCollection($db, 'organisateur');
		$city = $mon->selectCollection($db, 'city');

		foreach($city->find() as $e){
			$c[intval($e['id'])] = (string) $e['_id'];
		}

		$total = count($raw);
		$done = 0;
		foreach($raw as $e){
			$this->organisateurImportItem($col, array(
				'col'  => $col,
				'item' => $e,
				'city' => $c[$e['id_ville_organisateur']]
			));

			$done++;
			echo (100 * ($done / $total)).PHP_EOL;
		}

		return array('ok' => true, 'total' => count($raw));
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public function organisateurImportItem($col, array $opt){

		$col  = $opt['col'];
		$e    = $opt['item'];
		$city = $opt['city'];

		$data = array(
		#	'created'     => new MongoDate(strtotime($e['date_creation_organisateur'])),
			'updated'     => new MongoDate(strtotime($e['date_modification_organisateur'])),
			'id'          => intval($e['id_organisateur']),
			'address'     => $e['adresse_organisateur'],
			'city'        => array('_id' => $city),
			'commentaire' => $e['commentaire_organisateur'],
			'email'       => $e['email_organisateur'],
			'fax'         => $e['fax_organisateur'],
			'firstname'   => $e['nom_organisateur'],
			'lastname'    => $e['prenom_organisateur'],
			'fonction'    => $e['fonction_organisateur'],
			'mobile'      => $e['mobile_organisateur'],
			'name'        => $e['raison_sociale_organisateur'],
			'phone'       => $e['telephone_organisateur'],
			'title'       => $e['civilite_organisateur'],
			'web'         => $e['siteweb_organisateur'],
			'npai'        => ($e['npai_organisateur'] == 1),
			'rubrique'    => intval($e['rubrique_organisateur'])
		);

		$col->update(
			array('id' => $data['id'], 'rubrique' => $data['rubrique']),
			array('$set' => $data),
			array('upsert' => true)
		);

	}




// MANIFESTATION ///////////////////////////////////////////////////////////////////////////////////////////////////////

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public function manifestationGet(array $opt){

		$cond = array(
			'format' => array('date', 'city', 'organisateur')
		);

		if($opt['method'] == 'id'){
			$cond['id']       = intval($opt['id']);
			$cond['mvs.type'] = intval($opt['type']);
		}else
		if($opt['method'] == '_id'){
			$cond['_id'] = new MongoId($opt['id']);
		}

		$data = $this->apiLoad('calendrierManifestation')->get($cond);

		return empty($data)
			? $this->fail('No data found with this id')
			: array('ok' => true, 'data' => $data);
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public function manifestationCreation(array $opt){

		if(empty($opt['name']))  return $this->fail('parameter name is missing');
		if(empty($opt['id']))    return $this->fail('parameter id is missing');
		if(empty($opt['city']))  return $this->fail('parameter city is missing');

		// Check Existing
		$api  = $this->apiLoad('calendrierManifestation');
		$date = $this->apiLoad('calendrierManifestationDate');
		$tmp  = $api->get(array(
			'id'     => $opt['id'],
			'format' => array('date')
		));

		if(!empty($tmp)) return $this->fail('Data already exists with id:'.$opt['id']);

		// Strict minimum
		//list($type, $id) = explode('_', $opt['id_manifestation']);
		$data = array(
			'id'      => $opt['id'],
			'name'    => $opt['name'],
			'created' => new MongoDate(),
			'updated' => new MongoDate()
		);

		$fields = array(
			'name', 'email', 'phone', 'fax', 'web', 'mobile', 'paying', 'free', 'price', 'indoor', 'outdoor', 'pro',
			'individual', 'resident', 'game', 'number', 'opening', 'periodicity', 'schedule', 'presentation', 'resume',
			'city', 'organisateur', 'date', 'mvs'
		);

		foreach($fields as $e){
			if(!empty($opt[$e])){

				if($e == 'date' && is_array($opt['date']) && count($opt['date']) > 0){

					foreach($opt['date'] as $d){

						$days = (intval($d['days']) > 0) ? intval($d['days']) : 0;

						$tmp = array(
							'start' => new MongoDate(intval($d['start'])),
							'end'   => $date->day($d['start'], $days),
							'days'  => $days
						);

						if(!empty($d['comment']))   $tmp['comment']   = trim($d['comment']);
						if(!empty($d['canceled']))  $tmp['canceled']  = ($d['canceled'] == 1);
						if(!empty($d['postponed'])) $tmp['postponed'] = ($d['postponed'] == 1);
						if(!empty($d['unsure']))    $tmp['unsure']    = ($d['unsure'] == 1);

						$data['date'][] = $tmp;
					}

				}else
				if($e == 'organisateur'){
					$organisateur = $this->apiLoad('calendrierOrganisateur')->get(array(
						'id'     => intval($opt['organisateur']['id']),
						'format' => array()
					));

					if(!empty($organisateur)) $data['organisateur'] = array('_id' => $organisateur['_id']);

				}else
				if($e == 'city'){
					$city = $this->apiLoad('calendrierCity')->get(array(
						'id'     => intval($opt['city']['id']),
						'format' => array()
					));

					if(!empty($city)) $data['city'] = array('_id' => $city['_id']);

				}else
				if($e == 'mvs'){
					$data['mvs'] = array(
						'type'      => intval($opt['mvs']['type']),
						'category'  => intval($opt['mvs']['category'])
					);
				}else{
					$data[$e] = $opt[$e];
				}
			}
		}

		$db = $api->set($data)->debug(false)->save()->retrieve(array(
			'format' => array('date', 'city', 'organisateur')
		));

		return array('ok' => true, 'data' => $db);
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public function manifestationDelete(array $opt){

		$cond = array();

		if($opt['method'] == 'id'){
			$cond['id']       = intval($opt['id']);
			$cond['mvs.type'] = intval($opt['type']);
		}else
		if($opt['method'] == '_id'){
			$cond['_id'] = new MongoId($opt['id']);
		}

		$job  = $this->apiLoad('calendrierManifestation')->del($cond);

		return array('ok' => $job);
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
// NE GERE PAS ENCORE LES DATES
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public function manifestationUpdate(array $opt){

		if($opt['method'] == 'id'){
			$cond['id']       = intval($opt['id']);
			$cond['mvs.type'] = intval($opt['type']);
		}else
		if($opt['method'] == '_id'){
			$cond['_id'] = new MongoId($opt['id']);
		}else{
			return $this->fail('No method');
		}

		$api  = $this->apiLoad('calendrierManifestation');
		$test = $api->get($cond);
		if(empty($test)) return $this->fail('No data found');

		$data   = array();
		$fields = array(
			'name', 'email', 'phone', 'fax', 'web', 'mobile', 'paying', 'free', 'price', 'indoor', 'outdoor', 'pro',
			'individual', 'resident', 'game', 'number', 'opening', 'periodicity', 'schedule', 'presentation', 'resume',
			'city', 'organisateur', 'mvs'
		);

		foreach($fields as $e){
			if(!empty($opt[$e])){

				if($e == 'organisateur'){
					$organisateur = $this->apiLoad('calendrierOrganisateur')->get(array(
						'id'     => intval($opt['organisateur']),
						'format' => array()
					));

					if(!empty($organisateur)) $data['organisateur'] = array('_id' => $organisateur['_id']);

				}else
				if($e == 'city'){
					$city = $this->apiLoad('calendrierCity' )->get(array(
						'id'     => intval($opt['city']),
						'format' => array()
					));

					if(!empty($city)) $data['city'] = array('_id' => $city['_id']);

				}else
				if($e == 'mvs'){
					$data['mvs'] = array(
						'type'     => intval($opt['mvs']['type']),
						'category' => intval($opt['mvs']['category'])
					);
				}else{
					$data[$e] = $opt[$e];
				}

			}
		}

		if(count($data) > 0){

			# Si la manif possede un BACKUP, il est mit mis à jour
			if(array_key_exists('backup', $test)){
				$d = array('backup' => $data);
				$d = calendrier::flatten($d, $base = '', $divider_char = ".");
				$d['mvs.warning'] = true;
			}else{
				$d = $data;
			}

			// Method un peu particulière pour l'update depuis l'API REST
			$job = $api->updateFromRest((string) $test['_id'], $d);
		}else{
			return $this->fail('No data to update');
		}

		if($job){
			$data = $api->get(array_merge($cond, array(
				'format' => array('date', 'city', 'organisateur')
			)));

			return array('ok' => $job, 'data' => $data);
		}else{
			return $this->fail('Update failed');
		}
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public function manifestationImportDate(){

		$mon  = $this->mongoAuth();
		$db   = $mon->selectDB($this->db);
		$date = $mon->selectCollection($db, 'date');

		$date->remove(array());
		$bdd  = $this->mysqlSource();
		$raw  = $this->dbMulti("SELECT * FROM ".$bdd.".lva_cal_date");

		foreach($raw as $e){
			$date->insert(array(
				'_id'   => intval($e['id_date']),
				'date'  => $e['date'],
				'ferie' => ($e['ferie'] == '1')
			));
		}

		return array('ok' => true, 'total' => count($raw));

	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
// Ajoute a la collection "geocache" toute les addresse des manifestation
// Si l'adresse existe déjà elle n'est pas remplacer (même _ID) => les geocache sans GPS sont à traité ensuite
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public function manifestationImportGeoCache(){

		/*$last = $this->apiLoad('calendrierManifestation')->get(array(
			'noOff'    => true,
			'debug'    => false,
			'incoming' => date('d/m/Y'),
			'noLimit'  => true
		));*/

		$mon  = $this->mongoAuth();
		$db   = $mon->selectDB($this->db);
		$col  = $mon->selectCollection($db, 'manifestation');
		$geo  = $mon->selectCollection($db, 'geocache');


		$last = $col->find();
		$last = iterator_to_array($last);

		$address = array();
		foreach($last as $e){

			// COLLECTION = COMPLET
			if($e['mvs']['type'] == 1){
				$tmp = trim($e['geo']['address'].' '.$e['city']['zip'].' '.$e['city']['name']);

				$address[$tmp] = array(
					'address' => $e['geo']['address'],
					'zip'     => $e['city']['zip'],
					'city'    => $e['city']['name']
				);
			}else{
				$tmp = trim($e['city']['zip'].' '.$e['city']['name']);

				$address[$tmp] = array(
					'zip'  => $e['city']['zip'],
					'city' => $e['city']['name']
				);
			}
		}


		foreach($address as $add => $data){
			$geo->update(
				array('_id' => $add),
				array(
					'$set' => array(
						'_id'  => $add,
						'address' => $data['address'],
						'zip'     => $data['zip'],
						'city'    => $data['city']
					)
				),
				array('upsert' => true)
			);
		}


		return array('ok' => true, 'total' => count($address));
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
// Pour la collection geocache, trouver la position de toutes les adresses qui n'ont pas de GPS (not exists)
// Limiter à 10 adresses par appel
// Pause de 1 seconde entre chaque appel
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public function manifestationImportGeoCode(array $opt){

		$goo  = $this->apiLoad('calendrierGeocode');

		$mon  = $this->mongoAuth();
		$db   = $mon->selectDB($this->db);
		$col  = $mon->selectCollection($db, 'geocache');

		$add  = $col->find(array(
			'gps'   => array('$exists' => false),
			'_raw'  => array('$exists' => false)
		))->limit(10);

		$add  = iterator_to_array($add);
		$done = 0;

		if($opt['print']) echo "\n";

		foreach($add as $e){

		#	print_r($e);

			$gps = $goo->addresseToGPS(array(
				'print'   => $opt['print'],
				'address' => $e['_id']
			));

			// GOOGLE a bien renvoyé des LAT-LNG => on sauve
			if($gps['ok']){
				$col->update(
					array('_id' => $e['_id']),
					array('$set' => array('gps' => $gps['gps'], 'country' => $gps['country']))
				);
			}else{
			// Mémoriser pour analyse l
				$col->update(
					array('_id' => $e['_id']),
					array('$set' => array('_raw' => $gps['raw']))
				);
			}

			$done++;
			sleep(1);
		}

		return array('ok' => true, 'total' => $done);
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
// Remettre les geocode dans les manifestation d'après la cache
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public function manifestationImportGeoSet(){

		$mon  = $this->mongoAuth();
		$db   = $mon->selectDB($this->db);
		$geo  = $mon->selectCollection($db, 'geocache');
		$col  = $mon->selectCollection($db, 'manifestation');

		$done = 0;

		$manifs = $col->find(array(
			'geo.gps'   => array('$exists' => false),
			'city.zip'  => array('$exists' => true),
			'city.name' => array('$exists' => true),
		));

		$manifs  = iterator_to_array($manifs);

		foreach($manifs as $manif){

			$_id = $manif['city']['zip'].' '.$manif['city']['name'];
			if($manif['mvs']['type'] == 1) $_id = $manif['geo']['address'].' '.$_id;

			$cache = $geo->findOne(array(
				'_id' => $_id,
				'gps' => array('$exists' => true)
			));

			if($cache){
				$cond = array('_id' => $manif['_id']);
				$set  = array('$set' => array(
					'geo.gps'     => $cache['gps'],
					'geo.country' => $cache['country']
				));

				$col->update($cond, $set);
				$done++;
			}
		}

		return array('ok' => true, 'total' => $done);
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public function manifestationImport(array $opt){

		$bdd  = $this->mysqlSource();
		$done = 0;
		$mon  = $this->mongoAuth();
		$db   = $mon->selectDB($this->db);

		$col  = $mon->selectCollection($db, 'manifestation');
		$city = $mon->selectCollection($db, 'city');
		$orga = $mon->selectCollection($db, 'organisateur');
		$date = $mon->selectCollection($db, 'date');

		foreach($city->find() as $e){
			$c[intval($e['id'])] = array('_id' => (string) $e['_id'], 'id' => $e['id'], 'name' => $e['name'], 'zip' => $e['zip'], 'country' => $e['country']);
		}

		foreach($orga->find() as $e){
			$o[intval($e['id'])] = array('_id' => (string) $e['_id'], 'id' => $e['id'], 'name' => $e['name'], 'email' => $e['email']);
		}

		foreach($date->find() as $e){
			$d[intval($e['_id'])] = array('date' => $e['date']);
		}

		// Tagguer les manif existante dans la base
		/*$col->update(
			array(),
			array('$set' => array('inSuperCal' => true)),
			array('multiple' => true)
		);*/

		$distrib = array(
			null, /// Pour avoir les index équivalent au type !!!!!!
			array('type' => 1, 'name' => 'Collection',  'table' => ''),
			array('type' => 2, 'name' => 'Auto',        'table' => '2'),
			array('type' => 3, 'name' => 'Moto',        'table' => '3'),
		);

		$di = $distrib[$opt['type']];
	#	print_r($di);
	#	return;

		#foreach($distrib as $di){
			$raw  = $this->dbMulti("
				SELECT *
				FROM ".$bdd.".lva_cal_manifestation".$di['table']."
				WHERE id_ville_manifestation > 0 AND id_organisateur_manifestation > 0
			");

			$total = count($raw);
			$percent = 0;

			foreach($raw as $e){

				$tmp   = array();
				$qd    = "SELECT * FROM ".$bdd.".lva_cal_date_manifestation".$di['table']." WHERE id_manifestation=".$e['id_manifestation'];
				$dates = $this->dbMulti($qd);

				foreach($dates as $f){
					$tmp[] = array(
						'debut'        => $d[$f['id_date_debut']]['date'],
						'fin'          => $d[$f['id_date_fin']]['date'],
						'annule'       => $f['annule'],
						'reporte'      => $f['reporte'],
						'sous_reserve' => $f['sous_reserve']
					);
				}

				$do = $this->manifestationImportItem($col, array(
					'dates' => $tmp,
					'type'  => $di['type'],
					'col'   => $col,
					'item'  => $e,
					'city'  => $c[$e['id_ville_manifestation']],
					'city_' => $e['id_ville_manifestation'],
					'orga'  => $o[$e['id_organisateur_manifestation']],
					'orga_' => $e['id_organisateur_manifestation']
				));

				if($do){
					$done++;
					$p = round(100 * ($done / $total));
					if($percent != $p){
						echo PHP_EOL.$p.'%';
						$percent = $p;
					}else{
						echo '.';
					}
				}
			}
		#}

		// Y a t'il encore des manif qui sont tagués 'inSuperCal' => les supprimer
		//$col->remove(array('inSuperCal' => true));

		return array('ok' => true, 'total' => $done);
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public function manifestationImportItem($col, array $opt){

#		var_dump($opt['type']);
#		die();

		$col   = $opt['col'];
		$e     = $opt['item'];
		$city  = $opt['city'];
		$orga  = $opt['orga'];
		$dates = $opt['dates'];

		if($city['_id'] == NULL){
			return false;
		#	print_r($opt);
		#	die('fuck, ville not found ! — devrait se retrouver dans un fichier log');
		}

		$data = array(
		#	'_id'         => new MongoId(md5($opt['type'].'_'.$e['id_manifestation'])),
			'created'     => new MongoDate(strtotime($e['date_creation_manifestation'])),
			'updated'     => new MongoDate(strtotime($e['date_modification_manifestation'])),
			'id'          => intval($e['id_manifestation']),
			'city'        => array(
				'_id'  => $city['_id'],
				'id'   => $city['id'],
				'name' => $city['name'],
				'zip'  => $city['zip'],
				'dep'  => substr($city['zip'], 0, 2)
			),
			'organisateur' => array(
				'_id'   => $orga['_id'],
				'id'    => $orga['id'],
				'name'  => $orga['name'],
				'email' => $orga['email'],
			),
			'name'        => $e['intitule_manifestation'],
			'periodicity' => intval($e['periodicite_manifestation']),

			'free'        => ($e['manifestation_gratuite'] == '1'),
			'paying'      => ($e['manifestation_payante'] == '1'),
			'game'        => ($e['jouet_manifestation'] == '1'),

			'web'         => $e['site_web_manifestation'],
			'fax'         => $e['fax_manifestation'],
			'mobile'      => $e['mobile_manifestation'],
			'phone'       => $e['telephone_manifestation'],
			'email'       => $e['mail_manifestation'],

			'number'      => $e['nb_exposant_manifestation'],
			'geo'         => array(
				'address' => $e['adresse_manifestation'],
				'comment' => $e['situation_geo_manifestation'],
				'country' => $city['country']
			),

			'presentation' => $e['communique_manifestation'],

			'indoor'      => ($e['lieu_interieur_manifestation'] == '1'),
			'outdoor'     => ($e['lieu_exterieur_manifestation'] == '1'),

			'pro'         => ($e['type_exposant_pro_manifestation'] == '1'),
			'individual'  => ($e['type_exposant_particulier_manifestation'] == '1'),
			'resident'    => ($e['type_exposant_habitant_manifestation'] == '1'),

			'schedule'    => $e['horaires_manifestation'],
			'opening'     => $e['ouverture_manifestation'],

			'resume_date' => $e['resume_date_manifestation'],

			'price'       => $e['tarif_manifestation'],

			'mvs'         => array(
				'type'    => intval($opt['type']),
				'category'=> intval($e['id_categorie_manifestation'])
			)
		);

		$threshold = time() - (2 * 31536000); // 2 ans en arrière
		foreach($dates as $e){
			if(strtotime($e['debut']) >= $threshold){

				$datetime1 = new DateTime($e['debut']);
				$datetime2 = new DateTime($e['fin']);
				$interval  = $datetime1->diff($datetime2);
				$days      = $interval->days + 1;

				$start = new MongoDate(strtotime($e['debut'].' 00:00:00'));
				$end   = $this->apiLoad('calendrierManifestationDate')->day(strtotime($e['debut'].' 00:00:00'), $days);

				$myDates[] = array(
					'start'           => $start,
					'end'             => $end,
					'days'            => $days,
					'canceled'        => ($e['annule'] == '1'),
					'postponed'       => ($e['reporte'] == '1'),
					'unsure'          => ($e['sous_reserve'] == '1')
				);

			}
		}

		if(isset($myDates)){
			$data['date'] = $myDates;

			usort($data['date'], array($this, 'manifestationImportItemOrderDate'));

			$cond  = array('id' => $data['id'], 'mvs.type' => $data['mvs']['type']);
			$manif = $col->findOne($cond);

			if(array_key_exists('backup', $manif)){
				$upd    = array('$unset' => array('inSuperCal' => true));
				$opt    = array();
				$return = false;
			}else{
				$upd    = array('$set' => $data, '$unset' => array('inSuperCal' => true));
				$opt    = array('upsert' => true);
				$return = true;
			}

			$col->update($cond, $upd, $opt);

			return $return;
		}

		return false;
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	private function manifestationImportItemOrderDate($a, $b) {
		return $a['start']->sec - $b['start']->sec;
	}


//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public function manifestationImportPostScript(){

		/*$mon  = $this->mongoAuth();
		$db   = $mon->selectDB($this->db);
		$col  = $mon->selectCollection($db, 'manifestation');

		// Toute les manifs sont 0
		$col->update(
			array(),
			array('$set' => array('off' => true)),
			array('multiple' => true)
		);

		sleep(1); // ?

		// Non en fait, celle qui ont une date > maintenant
		$col->update(
			array('date.start' => array('$gte' => new MongoDate())),
			array('$unset' => array('off' => '')),
			array('multiple' => true)
		);*/

		return true;
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public function manifestationFails($_id){

		$mon  = $this->mongoAuth();
		$db   = $mon->selectDB($this->db);
		$col  = $mon->selectCollection($db, 'manifestation');

		$col->update(
			array('_id' => new MongoId($_id)),
			array('$set' => array('geo.failed' => true))
		);

		return true;
	}


// MANIFESTATION DATE //////////////////////////////////////////////////////////////////////////////////////////////////

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public function manifestationDateCreation(array $opt){

		if(empty($opt['start']))        return $this->fail('parameter start is missing');
		if(empty($opt['days']))         return $this->fail('parameter days is missing');
		if(empty($opt['comment']))      return $this->fail('parameter comment is missing');

		// Check Existing MANIFESTATION
		$api  = $this->apiLoad('calendrierManifestationDate');
		$data = $api->get(array(
			'_id'    => $opt['_id'],
			'format' => array('date')
		));

		if(empty($data)) $this->fail('No data found with this _ID');

		$dates = $data['date'] ?: array();

		if(in_array($opt['start'], $dates)){
			$this->fail('Date already exists.');
		}else{

			$job = $api->push(array(
				'_id'       => $opt['_id'],
				'start'     => $opt['start'],
				'day'       => intval($opt['days']),
				'comment'   => $opt['comment'],
				'canceled'  => $opt['canceled'],
				'postponed' => $opt['postponed'],
				'unsure'    => $opt['unsure']
			));

			if($job){
				$data = $api->get(array(
					'_id'    => $opt['_id'],
					'format' => array('date')
				));

				return array('ok' => $job, 'data' => $data);
			}else{
				return $this->fail('Creation failed');
			}
		}

	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public function manifestationDateDelete(array $opt){

		if(empty($opt['timestamp']))     return $this->fail('parameter timestamp is missing');

		$cond = array(
			'debug' => false,
			'_id'   => $opt['_id'],
			'start' => $opt['timestamp']
		);

		$job = $this->apiLoad('calendrierManifestationDate')->pop($cond);

		return array('ok' => $job);
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public function manifestationDateUpdate(array $opt){

		if(empty($opt['timestamp']))  return $this->fail('parameter timestamp is missing');
		if(empty($opt['days']))       return $this->fail('parameter days is missing');
		if(empty($opt['comment']))    return $this->fail('parameter comment is missing');

		$cond = array(
			'debug'   => false,
			'_id'     => $opt['_id'],
			'start'   => $opt['timestamp'],
			'days'    => $opt['days'],
			'comment' => $opt['comment']
		);

		$api  = $this->apiLoad('calendrierManifestationDate');

		$data = array(
			'_id'       => $opt['_id'],
			'start'     => $opt['timestamp'],
			'days'      => $opt['days'],
			'comment'   => $opt['comment'],
			'canceled'  => $opt['canceled'],
			'postponed' => $opt['postponed'],
			'unsure'    => $opt['unsure']
		);

		$job = $api->set($data);

		if($job){
			$data = $api->get(array_merge(array(
				'_id'    => $opt['_id'],
				'format' => array('date')
			)));

			return array('ok' => $job, 'data' => $data);
		}else{
			return $this->fail('Update failed');
		}
	}

}
