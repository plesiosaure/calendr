<?php

class motoRegister extends social {

function __clone(){}

/* + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - 
+ - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - */
public function fbPublishWall($message, $url){

	require USER.'/plugin/facebook/src/facebook.php';
	
	// Create our Application instance (replace this with your appId and secret).
	$facebook = new Facebook(array(
		'appId'  => FB_APPID,
		'secret' => FB_SECRET
	));

	// Get User ID
	$user = $facebook->getUser();

	if ($user) {
		try {
			// Proceed knowing you have a logged in user who's authenticated.
			$user_profile = $facebook->api('/me');

			// Do the wall post.
			$arg = array(
				'message'	=> $message,
				'link'		=> $url,
			#	'picture'	=> "YOUR_PICTURE_URL",
			#	'name'		=> "YOUR_LINK_NAME",
			#	'caption'	=> "YOUR_CAPTION"
			);

			$feed = @$facebook->api("/me/feed", "post", $arg);

		} catch (FacebookApiException $e) {
		#	error_log($e);
		#	$this->pre($e);
		#	$user = null;
		}
	}

}

/* + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + -
+ - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - */
public function albumPubish($id_album){

	$pu = $this->dbOne("SELECT 1 FROM `@albumpublic` WHERE id_content=".$id_album);
	$isPublic = ($pu[1] == 1);

	if (!$isPublic) {

		$myAlbum = $this->apiLoad('content')->contentGet(array(
			'id_content'		=> $id_album,
			'assoSocialForum'	=> true,
			'useGroup'			=> false,
			'debug'				=> 0
		));

		## Check appartenance
		if ($myAlbum['id_user'] != $this->user['id_user']) return false;

		$myItems = $this->apiLoad('content')->contentGet(array(
			'id_type'		=> TYPE_ALBUM,
			'is_item'		=> true,
			'id_album'		=> $myAlbum['id_content'],
			'order'			=> 'contentItemPos',
			'direction'		=> 'DESC',

			'useGroup'		=> false,
			'useChapter'	=> false,
			'debug'			=> 0
		));

		if(sizeof($myAlbum['id_socialforum']) > 0){
			$forum = $myAlbum['id_socialforum'];
		}else{
			$forum = array(MASTER_ID_FORUM);
		}

		$postMedia = array();
		for ($i = 0; $i < 3; $i++) {

			$postMedia[] = array(
				'exists' => true,
				'url' => $myItems[$i]['contentItemUrl'],
				'path' => KROOT.$myItems[$i]['contentItemUrl'],
				'type' => 'image'
			);
		}

		$this->dbQuery("INSERT IGNORE INTO `@albumpublic` (id_content, id_user) VALUES (".$myAlbum['id_content'].", ".$this->user['id_user'].")");

		$job = $this->apiLoad('socialPost')->socialPostSet(array(
			'debug'	=> false,
			'forum'	=> $forum,
			'core'	=> array(
				'id_content'			=> array('value' => $myAlbum['id_content']),
				'is_activity'			=> array('value' => 1),
				'id_user'				=> array('value' => $this->user['id_user']),
				'socialPostData'		=> array('value' => "Je viens d'ajouter ".count($myItems)." photo(s) &agrave; mon album: {link}"),
				'socialPostDataParam'	=> array('value' => json_encode(array(
					'linkType'	=> 'album',
					'linkId'	=> $myAlbum['id_content'],
					'linkName'	=> htmlentities($myAlbum['contentName'], ENT_COMPAT | ENT_HTML401, 'UTF-8')
				))),
				'socialPostMedia' => array('value' => json_encode($postMedia))
			)
		));

	}


}

/* + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - 
+ - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - */
public function cookieSet($opt = NULL) {
	/*$opt = array( 'activity' => array('social' => '0',
				 					'groupe' => '0',
				 					'garage' => '0'));*/
				 					
	$cookie = unserialize($_COOKIE['act']);
	
	if (is_array($opt)) {
		$cookie['act'][$opt['name']] = $opt['value'];
	}
		
	setcookie("act", serialize($cookie), (time()+60*60*24*30), '/');
} 

/* + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - 
+ - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - */
public function cookieGet($opt = NULL) {
	
	if(!empty($opt['name'])) $cname = $opt['name'];
	
	if(!empty($cname)){
		$cookie = unserialize($_COOKIE['act']);
		return $cookie['act'][$cname];
	}else{
		return $_COOKIE['motoregister'];
	}
}

/* + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - 
+ - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - */
public function mailSend($opt){

	require_once(APP.'/plugin/phpmailer/class.phpmailer.php');
	$mail = new PHPMailer();

	$mail->SetFrom('ne-pas-repondre@'.DOMAINNAME);
	$mail->ClearReplyTos();
	$mail->AddReplyTo('contact@'.DOMAINNAME);

	// Destinataire
	$mail->AddAddress($opt['to']);

	// Copie
	if(is_array($opt['cc']) && sizeof($opt['cc']) > 0){
		foreach($opt['cc'] as $e){
			$mail->AddCC($e);
		}
	}

	// Copie cachee
	#$mail->AddBCC('bm@kappuccino.org');
	if(is_array($opt['bcc']) && sizeof($opt['bcc']) > 0){
		foreach($opt['bcc'] as $e){
			$mail->AddBCC($e);
		}
	}

	// Title
	$mail->Subject = $opt['title'];

	// Data
	$template = USER.'/mail/'.$opt['template'];
	if(is_array($opt['body']) && file_exists($template) && is_file($template)){
		$body = $this->helperReplace(file_get_contents($template), $opt['body']);
	}else{
		$body = $opt['body'];
	}

	$mail->AltBody = strip_tags($body);
	$mail->MsgHTML(preg_replace("/\\\\/", '', $body));

	if($mail->Send()){
		return true;
	}else{
		return false;
	}
}

/* + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - 
+ - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - */
public function postToAlbum($opt){

	/* Récupérer le post */
	$post = $this->apiLoad('socialPost')->socialPostGet(array(
		'id_socialpost' => $opt['id_post']
	));
	$postMedias = json_decode($post['socialPostMedia'], true);
	
	/* Récupérer l'album du wall */
	$albums = $this->apiLoad('content')->contentGet(array(
		'id_type'		=> TYPE_ALBUM,
		'is_album'		=> true,
		'id_user'		=> $this->user['id_user'],
		'noLimit'		=> true,
		
		'order'			=> 'k_content.id_content',
		'direction'		=> 'DESC',
	
		'useGroup'		=> false,
		'debug'			=> 0,
		'search'		=> array((MODE == 'MOTO')
				? array('searchField' => '_moto', 'searchMode' => 'EG',		'searchValue' => '1')
				: array('searchField' => '_car',  'searchMode' => 'EG',		'searchValue' => '1'))
	));
	
	$walled = false;
	foreach ($albums as $a) {
		
		# Si pas d'album WALL, le créer
		#
		if (!empty($a['field']['albumWall'])) {
			$walled = true; // has wall album		
			$wallAlbumID = $a['id_content'];
		}
	}	
	
	if (!$walled) {
		
		$fields['albumWall'] = "1";
		if (MODE == "MOTO") $fields['_moto'] = "1";
		if (MODE == "CAR") $fields['_car'] = "1";
		
		## CREER UN ALBUM POUR LE WALL 
		#
		$this->apiLoad('content')->contentSet(array(
			'debug'			=> false,
			'id_type'		=> TYPE_ALBUM,
			'language'		=> 'fr',
			'id_content'	=> NULL,
			'id_chapter'	=> array($this->kodeine['id_chapter']),

			'def'			=> array('k_content' => array(
				'id_user'				=> array('value' => $post['id_user']),
				'is_album'				=> array('value' => 1),
				'contentSee'			=> array('value' => 1),
				'contentDateCreation'	=> array('value' => date("Y-m-d H:i:s")),
				'contentDateUpdate'		=> array('value' => date("Y-m-d H:i:s"))
			)),

			'data'			=> array('k_contentdata' => array(
				'contentUrl'			=> array('value' => $this->helperUrlEncode("Photos du mur")),
				'contentName' 			=> array('value' => "Photos du mur")
			)),

			'field'			=> $fields, 

			'album'			=> array('k_contentalbum' => array(
				'id_album'				=> array('value' => ALBUM_MEMBER) // c'est l'id_content de l'album parent "album des membres"	
			))
		));
		
		$wallAlbumID = $this->apiLoad('content')->id_content;	
		$this->dbQuery("INSERT IGNORE INTO `@albumpublic` (id_content, id_user) VALUES (".$wallAlbumID.", ".$post['id_user'].")");		
	}

	## SI LE POST CONTIENT PLUS DE DEUX IMAGES
	#
	# Itérer chaque photo, la déplacer et l'ajouter a l'album
	$json = array();
	$count = 0;
	if (is_array($postMedias) && !empty($postMedias)) {
		foreach($postMedias as $media) {
				
			$source = KROOT.$media['url'];
			$ext	= pathinfo($source, PATHINFO_EXTENSION);
			$count++;

			#$final	= KROOT.'/media/upload/content/'.uniqid('up_').'.'.$ext;


			$optAlbumAdd = array(
				'id_type'	=> TYPE_ALBUM,
				'language'	=> 'fr',
				'debug'		=> false,
				'def'		=> array('k_content' => array(
					'is_item'		=> array('value' => 1),
					'id_user'		=> array('value' => $post['id_user']),
					'contentSee'	=> array('value' => 1)
				)),
				'data'		=> array('k_contentdata' => array(
					'contentName'	=> array('value' => time().'-'.$count),
				)),
				'item'		=> array('k_contentitem' => array(
					'id_album'			=> array('value' => $wallAlbumID)
				)
			));
			
			list($type, $mime) = explode('/', $this->mediaMimeType($source));
	
			$optAlbumAdd['item']['k_contentitem']['contentItemType']	= array('value' => $type);
			$optAlbumAdd['item']['k_contentitem']['contentItemMime']	= array('value' => $mime);
			$optAlbumAdd['item']['k_contentitem']['contentItemWeight']	= array('value' => filesize($source));
		
			if($type == 'image'){
				$size = getimagesize($source);
				$optAlbumAdd['item']['k_contentitem']['contentItemHeight']	= array('value' => $size[1]);
				$optAlbumAdd['item']['k_contentitem']['contentItemWidth']	= array('value' => $size[0]);
			}
			
			$last = $this->dbOne("SELECT MAX(contentItemPos) AS la FROM k_contentitem WHERE id_album=".$wallAlbumID);
			$last = ($last['la'] + 1);
			$optAlbumAdd['item']['k_contentitem']['contentItemPos']	= array('value' => $last);
		
			# Ajouter l'img dans l'album
			$this->apiLoad('content')->contentSet($optAlbumAdd);		
			
			$imgID	= $this->apiLoad('content')->id_content;
			$tool	= $this->apiLoad('alphaNum');
			$alpha	= strtolower($tool->alphaID($imgID, false, 8));
			$tmp	= implode('', array_reverse(str_split($alpha, 1)));
			//$tmp	= implode('', str_split($alpha, 1));
			$final_	= MEDIA.'/upload/album/'.implode('/', str_split($tmp, 1)).'/'.md5(uniqid('f')).'.'.$ext;
			
			$json[] = array("type" => $media['type'], 'url' => str_replace(KROOT, '', $final_));
			umask(0);
			if(!file_exists(dirname($final_))) mkdir(dirname($final_), 0755, true);
			rename($source, $final_);
			umask(0);
		 	chmod($final_, 0755);
	
			$poster = str_replace('.'.pathinfo($final_, PATHINFO_EXTENSION), '.jpg', $final_);
			
			$job = $this->apiLoad('content')->contentSet(array(
				'id_content'	=> $imgID,
				'is_item'		=> true,
				'debug'			=> false,
				'item'			=> array('k_contentitem' => array(
					'contentItemUrl'	=> array('value' => str_replace(KROOT, '', $final_)),
				))
			));
			
			## Si on avait pas d'album du mur, ajouter un poster sur le nouvel album
			if(!$walled){
				$this->dbQuery("UPDATE k_contentalbum SET id_poster=".$imgID." WHERE id_content=".$wallAlbumID);
			}
			## Si on a plus d'une image dans le post
		}			
	}

	if (count($postMedias) > 1) {
		
		$this->apiLoad('socialPost')->socialPostSet(array(
			'id_socialpost'		=> $post['id_socialpost'],
			'debug'				=> false,
			'core'				=> array(
				'id_content' => array('value' => $wallAlbumID),
				'socialPostMedia' => array('value' => json_encode($json))
			),
			'circle'			=> $opt['circle'],
			'forum'				=> $opt['forum'],
			'event'				=> $opt['event']
		));
	} else if (count($postMedias) == 1) {
		$this->apiLoad('socialPost')->socialPostSet(array(
			'id_socialpost'		=> $post['id_socialpost'],
			'debug'				=> false,
			'core'				=> array(
				'id_content' => array('value' => $imgID),
				'socialPostMedia' => array('value' => '')
			),
			'circle'			=> $opt['circle'],
			'forum'				=> $opt['forum'],
			'event'				=> $opt['event']
		));
		
						
	}

}

}