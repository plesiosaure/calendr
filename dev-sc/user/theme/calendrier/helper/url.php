<?php

	$v = array();
	$full = '/'.$this->kodeine['get']['urlRequest'];

// MVS /////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	if(substr($full, 0, 5) == '/mvs/'){

		$rest = substr($full, 5);

		if(strpos($rest, '.html') !== false){
			include(dirname(__DIR__).'/controller/mvs/'.$rest); exit();
		}else{
			$v = array('moduleFolder' => 'mvs', 'moduleFile' => 'index');
		}
	}else


// COMPTE //////////////////////////////////////////////////////////////////////////////////////////////////////////////

	if(substr($full, 0, 8) == '/compte/'){
		$v['moduleFolder'] = 'compte';
		$rest = substr($full, 8);

		// ORGANISATEUR
		if(substr($rest, 0, 13) == 'organisateur/'){
			$v['moduleFolder'] = 'compte/organisateur';
			$rest = substr($rest, 13);

			// DETAIL D'UN ORGANISATEUR
			if(preg_match("#^([a-z0-9]{24})(/(.*))?#", $rest, $match)){
				$v['moduleFile']      = 'view';
				$v['id_organisateur'] = $match[1];

				if(in_array($match[3], array('edit', 'member'))){
					$v['moduleFile'] = $match[3];
				}else
				if(preg_match("#(accept|reject)/([0-9]*)#", $match[3], $match)){
					$v['moduleFile'] = $match[1];
					$v['id_user']    = $match[2];
				}
			}else

			if(preg_match("#^(pick-search|pick)(/([a-z0-9]{24}))?#", $rest, $match)){
				$v['moduleFile']     = $match[1];


				if(!empty($match[3])){
					$v['moduleFile']      = 'pick-detail';
					$v['id_organisateur'] = $match[3];
				}
			}
		}

		// MANIFESTATION
		if(substr($rest, 0, 14) == 'manifestation/'){
			$v['moduleFolder'] = 'compte/manifestation';
			$rest = substr($rest, 14);

			// DETAIL D'UN MANIFESTATION
			if(preg_match("#^([a-z0-9]{24})(/(.*))?#", $rest, $match)){
				$v['moduleFile']       = 'view';
				$v['id_manifestation'] = $match[1];

				if(in_array($match[3], array('edit', 'date'))) $v['moduleFile'] = $match[3];
			}else

			// NEW
			if($rest == 'new'){
				$v['moduleFile'] = 'new';
			}

		}

	}else

// MANIFESTATION ///////////////////////////////////////////////////////////////////////////////////////////////////////

	if(substr($full, 0, 15) == '/manifestation/'){
		$v['moduleFolder'] = 'manifestation';

		$rest = substr($full, 15);

		// Liste par région
		if(preg_match("#region/([AB0-9]*)/(page/([0-9]*))?#", $rest, $a)){
			$v['moduleFile'] = 'region-listing';
			$v['region']     = $a[1];

			if(isset($a['3'])) $v['page'] = $a[3];
		}else
		// Liste pas département
		if(preg_match("#departement/([AB0-9]*)/(page/([0-9]*))?#", $rest, $a)){
			$v['moduleFile']  = 'departement-listing';
			$v['departement'] = $a[1];

			if(isset($a['3'])) $v['page'] = $a[3];
		}else

		// DETAIL (depuis type-cat-mvsid)
		if(preg_match("#.*-([0-9]+)#", $rest, $a)){
			$v['moduleFile']       = 'detail';
			$v['id_type']          = 'mvs';
			$v['id_manifestation'] = intval($a[1]);
		}else

		// DETAIL (depuis mongoID);
		if(preg_match("#[a-z0-9]{24}#", $rest)){
			$v['moduleFile']       = 'detail';
			$v['id_type']          = 'mongo';
			$v['id_manifestation'] = $rest;
		}

	}else

// +++ INE /////////////////////////////////////////////////////////////////////////////////////////////////////////////

	if($this->kodeine['moduleFolder'] == 'content' && $this->kodeine['get']['urlFile'] != ''){
		$v = array('moduleFolder' =>'content', 'moduleFile' => 'detail');
	}

// >>> KODEINE /////////////////////////////////////////////////////////////////////////////////////////////////////////

	if(!empty($v)){
		foreach($v as $ke => $va){
			$this->kodeine['get'][$ke] = $this->kodeine[$ke] = $va;
		}
	}

// CLEAN ///////////////////////////////////////////////////////////////////////////////////////////////////////////////

	$rewrited = in_array('moduleFolder', $v);
	unset($full, $rest, $mod, $match, $v, $ke, $va);

