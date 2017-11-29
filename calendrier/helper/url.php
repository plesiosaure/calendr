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

		// DETAIL
		if(preg_match("#[a-z0-9]{24}#", $rest)){
			$v['moduleFile'] = 'detail';
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

