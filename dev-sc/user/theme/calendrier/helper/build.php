<?php

	define('LOC', 				$this->kodeine['language']);
	define('MYTHEME', 			dirname(__DIR__));
	define('GMAPSTATIC',        'AIzaSyBzs4uc-wH5gPlP65p1h7iNaUf6iT5_PS4');
	define('WATERMARK',			'domain');
	define('ANALYTICS',         'UA-3345628-24');

	define('TYPE_ACTU',			7);

	define('CHAPTER_ID',        2);
	define('CHAPTER_NAME',      'calendrier');

	define('EMAIL_TO',          'contact@supercalendrier.com');
	define('EMAIL_BCC',         'contact@supercalendrier.com');
	define('EMAIL_LVA',         'contact@supercalendrier.com');

	if(!defined('HTML_TITLE')) define('HTML_TITLE', 'Supercalendrier.com');

// DEV vs PROD /////////////////////////////////////////////////////////////////////////////////////////////////////////

	define('ONAIR',             false);
	define('ONAIR',             file_exists(KROOT.'/user/config/.onair'));
	define('ISDEMO',			$_SERVER['HTTP_HOST'] != 'dev.supercalendrier.com');

	$cdn = ($_SERVER['HTTP_HOST'] == 'dev.supercalendrier.com')
		? 'cdn-dev.supercalendrier.com'
		: 'cdn.supercalendrier.com';

	define('USECACHE',			ONAIR);
	define('DOMSTATIC',			(ONAIR ? $cdn : $_SERVER['HTTP_HOST']));


	define('PKG_VERSION',       include __DIR__.'/../ui/version.php');

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
	}

// TOOLS ///////////////////////////////////////////////////////////////////////////////////////////////////////////////

	function menuSelected($r, $x=false, $d='#'){
		return ($x)
			? preg_match($d.$r.$d, $_SERVER['REQUEST_URI']) ? 'active' : ''
			: ((strpos($_SERVER['REQUEST_URI'], $r) === false) ? '' : 'active');
	}


