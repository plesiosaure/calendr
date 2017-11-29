<?php

	define('LOC', 				$this->kodeine['language']);
	define('MYTHEME', 			USER.'/theme/'.$this->kodeine['themeFolder']);
	define('GMAPSTATIC',        'AIzaSyBzs4uc-wH5gPlP65p1h7iNaUf6iT5_PS4');
	define('WATERMARK',			'domain');
	define('ANALYTICS',         'UA-XXXXXXXX-X');

	define('TYPE_ACTU',			7);

	define('CHAPTER_ID',        2);
	define('CHAPTER_NAME',      'calendrier');

// DEV vs PROD /////////////////////////////////////////////////////////////////////////////////////////////////////////

	$onAir = false;
	define('ONAIR',				$onAir);
	define('ISDEMO',			($_SERVER['HTTP_HOST'] != 'caldev.kappuccino.org'));

	define('USECACHE',			$onAir);
	define('DOMSTATIC',			((ONAIR) ? 'calendrier.kappuccino.org' : 'caldev.kappuccino.org'));


// ME //////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	$me = array();
	if(intval($this->user['id_user']) > 0){

		$me = (USECACHE === true) ? $this->cache->sqlcacheGet('CAL-USER:'.$this->user['id_user']) : false;

		if($me === false){
			$me = $this->apiLoad('socialUser')->socialUserGet(array(
				'id_user' => $this->user['id_user']
			));

			$me['field']['userPolicy'] = json_decode($me['field']['userPolicy'], true);

			if(USECACHE) $this->cache->sqlcacheSet('CAL-USER:'.$this->user['id_user'], $me, 60*60);
		}

		if(isset($_GET['me']) && $me['id_group'] == -2) $this->pre($me);
	}

// TOOLS ///////////////////////////////////////////////////////////////////////////////////////////////////////////////

	function menuSelected($r, $x=false, $d='#'){
		return ($x)
			? preg_match($d.$r.$d, $_SERVER['REQUEST_URI']) ? 'active' : ''
			: ((strpos($_SERVER['REQUEST_URI'], $r) === false) ? '' : 'active');
	}


