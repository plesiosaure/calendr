<?php

class socialTemplate extends social {

## CONFIG ##
private $modeField; 		// ID_FIELD MOTO | CAR
private $siteMemberCount; 	// CLEF DE LA STAT MEMBRES
private $siteCircleCount; 	// CLEF DE LA STAT CIRCLES
private $siteEventCount;	// CLEF DE LA STAT EVENTS

## STRINGS ##
private $avatar_path; 		// CHEMIN AVATAR DEFAULT
private $vehicule_path; 	// CHEMIN MOTO DEFAULT
private $name_string; 		// NOM DU SITE
private $concern_string; 	// INTERET (moto/car)
private $headline_string; 	// ACITIVITY
private $site_url; 			// SITE URL STRING


/* + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - 
+ - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - */
public function __construct() {

	# CONFIG
	#
	$this->modeField 		= (MODE == 'MOTO')	? 54 				: 55;
	$this->siteMemberCount 	= (MODE == 'MOTO')	? 'motoMemberCount' : 'carMemberCount';
	$this->siteCircleCount 	= (MODE == 'MOTO')	? 'motoGroupCount' 	: 'carGroupCount';
	$this->siteEventCount 	= (MODE == "MOTO")	? 'motoEventCount' 	: 'carEventCount';
	
	# STRINGS
	#
	$this->avatar_path = (MODE == 'MOTO')
		? '/media/ui/img/commons/avatar-defaut.png'
		: '/media/ui/img/commons/avatar-defaut-cr.png';
										  
	$this->vehicule_path = (MODE == 'MOTO')
		? '/media/ui/img/commons/default-moto.png'
		: '/media/ui/img/commons/default-car.png';
										  
	$this->name_string = (MODE == 'MOTO')
		? 'moto-register.com'
		: 'car-register.com';
										  
	$this->concern_string = (MODE == 'MOTO')
		? 'motos' 
		: 'voitures';

	$this->site_url = (MODE == 'MOTO')
		? 'http://moto-register.com/'
		: 'http://car-register.com/';					 
			
	$this->headline_string = 'Je viens de rejoindre '.$this->name_string.', le site des passionnés de '.$this->concern_string;

	parent::__construct();
}

/* + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + -
+ - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - */
public function avatar($opt){

	$user = $opt['user'];
	$size = $opt['size'];
	$lazy = $opt['lazy'];
	
	$src   = $lazy ? 'data-original' : 'src';
	$class = $lazy ? 'class="lazy" ' : '';
	
	// Avatar
	if($user['userMedia']['image'][0]['exists']){
		$tmp = $this->mediaUrlData(array(
			'url'		=> $user['userMedia']['image'][0]['url'],
			'mode'		=> 'square',
			'value'		=> $size,
			'cdn'		=> true,
		));

		$avatar = '<img '.$src.'="'.$tmp['img'].'" height="'.$tmp['height'].'" width="'.$tmp['height'].'" '.$class.'/>';
	}else{
		$avatar = '<img '.$src.'="'.$this->avatar_path.'" height="'.$size.'" width="'.$size.'" '.$class.'/>';
	}

	return '<a href="/social/membre/'.$user['id_user'].'">'.$avatar.'</a>';
}

/* + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - 
+ - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - */
public function userNameVisible($u, $link=false){

	$format = ($link) ? '<a href="/social/membre/'.$u['id_user'].'">%s</a>' : '%s';

	if(count($u) == 0){
		return 'Membre d&eacute;sinscrit';
	}else
	if($u['field']['userPseudo'] != ''){
		return sprintf($format, $u['field']['userPseudo']);
	}else{
		return sprintf($format, 'Membre '.$u['id_user']);
	}
}

/* + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - 
+ - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - */
public function userCreation(){

	// Cache
	$this->cache->sqlcacheDelete('USER:'.$this->user['id_user']);

	$tmp = $this->dbOne("SELECT * FROM k_socialpost WHERE id_user=".$this->user['id_user']." AND socialPostData LIKE '%Je viens de rejoindre ".$this->name_string."%'");
	if($tmp['id_socialpost'] > 0) return false;

	// Push sur facebook ?
	if(intval($this->user['fb_userid']) > 0){
		$this->apiLoad('motoRegister')->fbPublishWall(
			$this->headline_string,
			$this->site_url);
	}

	// Premier POST ?
	$this->apiLoad('socialPost')->socialPostSet(array(
		'debug'				=> false,
		'id_socialpost'		=> NULL,
		'forum'				=> array(MASTER_ID_FORUM),
		'core'				=> array(
			'is_activity'		=> array('value' => 1),
			'id_user'			=> array('value' => $this->user['id_user']),
			'socialPostData'	=> array('value' => $this->headline_string),
		),
	));

	// Stats
	$this->userStatsSet();
}

/* + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - 
+ - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - */
public function userStatsSet() {

	$stats = $this->dbOne("
		SELECT COUNT(k_user.id_user) AS ccc FROM k_user
		INNER JOIN k_userdata ON k_user.id_user = k_userdata.id_user
		WHERE is_active=1 && is_deleted=0 AND field".$this->modeField."=1
	");

	$this->dbQuery("UPDATE `@stats` SET statsValue = ".$stats['ccc']." WHERE statsName = '".$this->siteMemberCount."'");
	$this->cache->sqlcacheDelete('GLOBAL:STATS:'.MODE);
}

/* + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - 
+ - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - */
public function circleStatsSet() {

	$stats = $this->dbOne("SELECT COUNT(id_socialcircle) AS ccc FROM k_socialcircle WHERE field".$this->modeField."=1");
	$this->dbQuery("UPDATE `@stats` SET statsValue = ".$stats['ccc']." WHERE statsName = '".$this->siteCircleCount."'");
	$this->cache->sqlcacheDelete('GLOBAL:STATS:'.MODE);
}

/* + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - 
+ - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - */
public function eventStatsSet(){

	$stats = $this->dbOne("SELECT COUNT(id_socialevent) AS ccc FROM k_socialevent WHERE field".$this->modeField."=1");
	$this->dbQuery("UPDATE `@stats` SET statsValue = ".$stats['ccc']." WHERE statsName = '".$this->siteEventCount."'");
	$this->cache->sqlcacheDelete('GLOBAL:STATS:'.MODE);
}

/* + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - 
+ - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - */
public function post($e, $opt=array()){

	$first	= ($opt['first']) ? true : false;
	$square	= ($opt['first']) ? 60   : 40;
	$param	= $e['socialPostDataParam'];

	// Avatar
	$avatar = '';
	if($e['user']['userMedia']['image'][0]['exists']){
		$tmp = $this->mediaUrlData(array(
			'url'		=> $e['user']['userMedia']['image'][0]['url'],
			'mode'		=> 'square',
			'value'		=> $square,
			'cdn'		=> true,
		));

		$avatar .= '<img data-original="'.$tmp['img'].'" height="'.$tmp['height'].'" width="'.$tmp['height'].'" class="lazy" />';
	}else{
		$avatar = '<img data-original="'.$this->avatar_path.'" height="'.$square.'" width="'.$square.'" class="lazy" />';
	}

	$avatar = '<a href="/social/membre/'.$e['user']['id_user'].'">'.$avatar.'</a>';

	// About
	unset($about, $about_, $topData);
	#$this->pre($e);
	
	if(!$opt['args']['hideAbout']){
		if($e['id_content'] != ''){

			$about = $this->apiLoad('content')->contentGet(array(
				'debug'			=> false,
				'id_content'	=> $e['id_content'],
				'useChapter'	=> false,
				'useGroup'		=> false,
				'contentSee'	=> 1
			));

			if(intval($about['id_content']) > 0){

				if($about['id_type'] == TYPE_ACTU){
					$about = '<div class="about"><span>A propos de l\'actu </span><a class="ellip" href="/'.$about['contentUrl'].'">'.$about['contentName'].'</a></div>';
				}else
				if($about['id_type'] == TYPE_ALBUM && $about['is_album']){
					$about = '<div class="about"><span>Dans l\'album </span><a class="ellip" href="/social/membre/'.$about['id_user'].'/album/'.$about['id_content'].'">'.$about['contentName'].'</a></div>';
				}else	
				if($about['id_type'] == TYPE_ALBUM && $about['is_item'] && file_exists(KROOT.$about['contentItemUrl'])){
	
					$tmp = $this->mediaUrlData(array(
						'url'	=> $about['contentItemUrl'],
						'mode'	=> 'width',
						'value'	=> 150,
						'cdn'	=> true,
					));

					$topData  = '<div><a href="/social/membre/'.$about['id_user'].'/album/'.$about['id_album'].'/'.$about['id_content'].'">';
					$topData .= '<img data-original="'.$tmp['img'].'" height="'.$tmp['height'].'" width="'.$tmp['width'].'" class="lazy" />';
					$topData .= '</a></div>';
				}else
				if($about['id_type'] == TYPE_GARAGE){
	
					if($about['contentMedia']['image'][0]['exists']){
						$tmp = $this->mediaUrlData(array(
							'url'	=> $about['contentMedia']['image'][0]['url'],
							'mode'	=> 'width',
							'value'	=> 150,
							'cdn'	=> true,
							'watermark'	=> array(
								'source'   => WATERMARK,
								'position' => 'rb'
							)
						));
						
						$img = '<img data-original="'.$tmp['img'].'" height="'.$tmp['height'].'" width="'.$tmp['width'].'" class="lazy" />';
					}else{
						$img = '<img data-original="'.$this->vehicule_path.'" height="100" width="150" class="lazy" />';
					}
					
					$topData  = '<div><a href="/social/membre/'.$about['id_user'].'/garage/'.$about['id_content'].'">';
					$topData .= 'A propos de la '.$this->concern_string.' '.$about['contentName'].'<br />'.$img;
					$topData .= '</a></div>';
				}
	
				if(isset($about_)) $about = "<div class=\"about\">".$about_."</div>";
			}
			unset($about_);
			if(is_array($about)) unset($about);

		}
		if(sizeof($e['socialPostEvent']) > 0){
			
			$about = $this->apiLoad('socialEvent')->socialEventGet(array(
				'debug'				=> false,
				'id_socialevent'	=> $e['socialPostEvent'][0]
			));
			
			if(sizeof($about) > 0){
				$about = "<div class=\"about\"><span>Au sujet de l'&eacute;venement: </span><a class=\"ellip\" href=\"/social/event/".$about['id_socialevent']."\">".$about['socialEventName']."</a></div>";
			}
		}else
		if(sizeof($e['socialPostCircle'][0]) > 0){

			$postCircle = $this->apiLoad('socialCircle')->socialCircleGet(array(
				'id_socialcircle' => $e['socialPostCircle']
			));
			$about = '<div class="about"><span>Dans le groupe </span><a class="ellip" href="/social/groupe/'.$e['socialPostCircle'][0].'">'.$postCircle[0]['socialCircleName'].'</a></div>';
		}else
		if(sizeof($e['socialPostForum']) > 0){

			foreach($e['socialPostForum'] as $f){
				if($f != MASTER_ID_FORUM){
					$about_[] = '<a href="/social/univers/'.$f.'">'.$GLOBALS['forumID'][$f].'</a>';
				}
			}
	
			//if(isset($about_)) $about = "<div class=\"about\"><span>Dans les univers </span>" .implode(', ', $about_)."</div>";
			if(isset($about_)){
				$univers = (count($about_) > 1) ? "Dans les univers" : "Dans l'univers";
				$about   = "<div class=\"about\"><span>".$univers." </span>" .implode(', ', $about_)."</div>";
			}
		}
	}
	
	unset($about_);
	
	# Tagger le post si activity
	$is_activity = ($e['is_activity'] == 1) ? 'activity' : '';
	
	$html  = "<div class=\"post-item ".((!$first) ? 'small' : '')." clearfix ".$is_activity."\" id=\"post-".$e['id_socialpost']."\" data-id=\"".$e['id_socialpost']."\">";

	$html .= "<div class=\"avatar\">".$avatar."</div>";

	$head  = "<div class=\"head head-float clearfix\">";
	$head .= "<a href=\"/social/membre/".$e['id_user']."\" class=\"membre\">";
	$head .= "<i></i><span class=\"name\">".$this->userNameVisible($e['user'])."</span>";
	$head .= "</a>".$about;
	$head .= "</div>";

	
	# HEAD
	#
	$html .= "<div class=\"foobar clearfix\">";
		if($first) $html .= $head;
	//	$html .= $about;
	#$html .= "</div>";

	$dataView = $e['socialPostDataView'];

	# LINKS
	#
	if(sizeof($param) > 0){
		$link = '';

		if($param['linkId'] != '' && $param['linkName'] != ''){

			// EVENT
			if($param['linkType'] == 'event'){
			#	$link = '<a href="/social/event/'.$param['linkId'].'">'.utf8_decode($param['linkName']).'</a>';
				$link = '<a href="/social/event/'.$param['linkId'].'">'.$param['linkName'].'</a>';
			}else

			// GROUPE
			if($param['linkType'] == 'groupe'){
			#	$link = '<a href="/social/groupe/'.$param['linkId'].'">'.utf8_decode($param['linkName']).'</a>';
				$link = '<a href="/social/groupe/'.$param['linkId'].'">'.$param['linkName'].'</a>';
			}else

			// ALBUM
			if($param['linkType'] == 'album'){
				$link = '<a href="/social/membre/'.$e['id_user'].'/album/'.$param['linkId'].'">'.$param['linkName'].'</a>';
			}else

			// ALBUM ITEM
			if($param['linkType'] == 'album-item'){
				$link = '<a href="/social/">'.$param['linkName'].'</a>';
			}else

			// GARAGE
			if($param['linkType'] == 'garage'){
				$link = '<a href="/social/garage/'.$param['linkId'].'">'.$param['linkName'].'</a>';
			}
		}
		//$this->pre($e);
		$e['socialPostDataView'] = $this->helperReplace($dataView, array('link' => $link));
	}

	# DATA
	#
	$html .= "<div class=\"data data-float clearfix\">";

	if(!$first) $html .= $head;

	if($topData != ''){
		$html .= "<div class=\"top-wrapp\">".$topData."</div>";
	}

	$html .= "<div class=\"txt-wrapp\">". $this->postDataView($e['socialPostDataView']) ."</div>";
		# MEDIA
		#
		if(sizeof($e['socialPostMedia']) > 0){


			//$this->pre($e);


			$html .= "<div class=\"media-wrapp clearfix\">";
			# Au post
			#
			if(is_array($e['socialPostMedia']) && is_array($e['socialPostMedia']['image']) && sizeof($e['socialPostMedia']['image']) > 0){
				foreach($e['socialPostMedia']['image'] as $img){

					if ($img['exists']) {
						$tmpmedia = $img;					
						$img = $this->mediaUrlData(array(
							'url'	=> $tmpmedia['url'],
							'mode'	=> 'height',
							'value'	=> 175,
							'cdn'	=> true,
							'watermark'	=> array(
								'source'   => WATERMARK,
								'position' => 'rb'
							)

						));
						
						$full = $this->mediaUrlData(array(
							'url'	=> $tmpmedia['url'],
							'mode'	=> 'width',
							'value'	=> 600,
							'cdn'	=> true,
							'watermark'	=> array(
								'source'   => WATERMARK,
								'position' => 'rb'
							)


						));
					}

					if ($param['linkType'] == 'garage' && $img['url']) {

					}

					$html .= "<div class=\"e\">".
							 '<img data-postmedia="true" data-source="'.$full['img'].'" data-original="'.$img['img'].'" height="'.$img['height'].'" width="'.$img['width'].'" class="lazy" />'.
							 '</div>';
				}
			}

			if(!is_array($e['socialPostMedia']) && $this->isJson($e['socialPostMedia'] == 1)) {
				$smedia = json_decode($e['socialPostMedia'], true);

				if (is_array($smedia)) {

					foreach($smedia as $med){
						if ($med['url'] != '' ) {
							$img = $this->mediaUrlData(array(
								'url'	=> $med['url'],
								'mode'	=> 'height',
								'value'	=> 175,
								'cdn'	=> true,
								'watermark'	=> array(
									'source'   => WATERMARK,
									'position' => 'rb'
								)
							));


							$html .= "<div class=\"e\">".
									 '<img data-postmedia="true" data-source="/w:600'.$med['url'].'" data-original="'.$img['img'].'" height="'.$img['height'].'" width="'.$img['width'].'" class="lazy" />'.
									 '</div>';

						}
					}
				}
			}
			
			$html .= "</div>";
		}
		# OPEN GRAPH DATA ?
		#
		$og = $e['socialPostOpenGraph'];
		if(sizeof($og) > 0){
			$html .= "<div class=\"og-wrapp\">";
			foreach($og as $oge){
				$html .= "<div class=\"oge\">".$this->openGraphItemView($oge, (($first) ? 600 : 550))."</div>"; 
			}
			$html .= "</div>";
		}

		# INFOS
		#
		if($first) { 

			$html .= "<div class=\"info clearfix\">";
			$html .= $this->likeWrapp(array(
				'first'		=> true,
				'post'		=> $e,
				'rated'		=> $opt['rated'],
			));
			$html .= "</div>";
		}else{
			$html .= "<div class=\"info clearfix injected\">";
			$html .= $this->likeWrapp(array(
				'first'		=> false,
				'post'		=> $e,
				'rated'		=> $opt['rated'],
			));
			$html .= "</div>";
		}

	$html .= "</div>";
	$html .= "</div>";

	if($first){
		$html .= "<div class=\"inject\" id=\"inject-small-".$e['id_socialpost']."\">";

		if(sizeof($e['socialPostFlat']) > $opt['view']){
			$html .= "<div class=\"action\">";
			$html .= "<a onclick=\"postLoad(this, ".$e['id_socialpost'].", 'inject-small-".$e['id_socialpost']."', ".(sizeof($e['socialPostFlat']) - $opt['view']).")\" class=\"ico ico-chat\"><i></i>Afficher les ".sizeof($e['socialPostFlat'])." commentaires</a>";
			$html .= "</div>";
		}

		#$html .= "<div class=\"t\"></div>";

		$html .= "{more}";

		$html .= "<div class=\"form off\" id=\"replyto-".$e['id_socialpost']."\">";
			$html .= "<div class=\"post-here\"></div>";
			$html .= "<div class=\"area-container\"><textarea id=\"".$e['id_socialpost']."\" placeholder=\"Ma reponse ...\"></textarea></div>";

			$html .= '<div class="toggle">';
	
				$html .= '<div class="camera-upload">';
				$html .= '<input type="file" id="post-'.$e['id_socialpost'].'-temp-upload" class="tempUpload" />';
				$html .= '</div>';

				$html .= '<div class="drop-smiley">';
				$html .= '<img src="/media/ui/img/emoticons/Cool.png" height="18" width="18" class="open-smiley">';
				$html .= '</div>';

				$html .= '<a onclick="postReply('.$e['id_socialpost'].');" class="btn btn-mini">Ajouter mon commentaire</a>';
				$html .= '<span class="post-alert"></span>';

			$html .= '</div>';

		$html .= "</div>";

		$html .= "</div>";
	}

	$html .= "</div>";
	
	# AJOUTER LE SEPARATEUR DES INJECTS
	#if (!$first) $html .= '<div class="inject-sep"></div>';

	unset($about, $about_);

	return $html;
}

private function postDataView($data){

	$format = '<img src="/media/ui/img/emoticons/%s.png" class="smile_preview">';
	$replace = array(
		':)'  => 'Content',
		';)'  => 'Wink',
		':('  => 'Embarrassed',
		'>:o' => 'Angry',
		':D'  => 'Grin',
		":'(" => 'Cry',
		':o'  => 'Gasp',
		':p'  => 'Yuck',
		'B)'  => 'Cool',
		':x'  => 'Sealed',
		':s'  => 'Confused',
		':*'  => 'Mini-Frown',
		':/'  => 'Slant',
		'<3'  => 'Heart',
		':>'  => 'Mini-Smile',
		'=D'  => 'Laughing'
	);

	foreach($replace as $smile => $img){
		$data = str_replace(' '.$smile,  sprintf($format, $img), $data);
	}

	$data = nl2br($data);

	return $data;
}

/* + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - 
+ - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - */
public function openGraphItemView($og, $width) {

	# VIDEO
	#
	if($og['og:video:type'] != ''){
		$w = $og['og:video:width'];
		$h = $og['og:video:height'];

		if($w > $width){
			$r = $width / $w;
			$w = round($r * $w);
			$h = round($r * $h);
		}
		
		$w_ = 128;
		$h_ = ($w_ / $w) * $h;

		$html  = '<a onclick="ogVideo(this,'.$w.','.$h.',\''.addslashes($og['og:video']).'\')">';
		$html .= '<img data-original="'.$og['og:image'].'" height="'.$h_.'" width="'.$w_.'" border="0" class="lazy" />';
		$html .= '</a>';
	}else
	
	# IMAGE
	#
	if($og['og:image'] != ''){

		$w = $og['og:image:width'];
		$h = $og['og:image:height'];

		if($w > $width){
			$r = $width / $w;
			$w = round($r * $w);
			$h = round($r * $h);
		}
		
		if($w != '' && $h != '') $size = "height=\"".$h."\" width=\"".$w."\"";

		$html  = '<a href="'.$og['og:url'].'" target="_blank">';
		$html .= '<img data-original="'.$og['og:image'].'" '.$size.' border="0" class="lazy oge-img" style="display:none;" />';
		$html .= '</a>';
	}

	return $html;
}

/* + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - 
+ - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - */
public function wall($opt=array()){
	
	// Dans opt on donne VIEW qui n'est pas present dans OPT[ARGS] => on le rajoute
	$opt['args']['view'] = $opt['view'];

	$html  = '<div id="socialpost" data-offset="'.$opt['offset'].'" data-limit="'.$opt['limit'].'" data-total="'.$opt['total'].'">';
	$html .= $this->wallItems($opt);
	$html .= '</div>';

	if($opt['total'] > ($opt['limit'] + $opt['offset'])){
		unset($opt['post']);

		$html .= '<div id="getmore">';
		$html .= '<textarea style="display:none;">'.json_encode($opt['args']).'</textarea>';
		$html .= '<a onclick="postGetMore(this, '.$opt['total'].', '.$opt['limit'].')" class="getmore">Afficher plus de posts</a>';
		$html .= '</div>';
	}

	return $html;
}

/* + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - 
+ - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - */
public function wallItems($opt=array()){

	$html = '';

	foreach($opt['post'] as $e){
		$html .= $this->wallPost($opt, $e);
	}

	return $html;
}

/* + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - 
+ - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - */
public function wallPost($opt, $e){

	if (!$opt['keepNotifications'] && intval($this->user['id_user']) > 0) {
		$this->apiLoad('socialNotification')->socialNotificationView(array(
			'debug'					=> false,
			'id_user'				=> $this->user['id_user'],
			'socialActivityId' 		=> array_merge(array($e['id_socialpost']), $e['socialPostFlat'])
		));
	}

	$item	= $this->post($e, array_merge($opt, array('first' => true)));
	$scheme = $e['socialPostThread'];
	$all	= $e['socialPostFlat'];
	$last	= array_slice($all, sizeof($all) - $opt['view']);

	if(sizeof($all) > 0){

		$flat = $this->apiLoad('socialPost')->socialPostGet(array(
			'debug'			=> false,
			'id_socialpost'	=> $last,
			'withUser'		=> true,
			'withMedia'		=> true,
			'human'			=> true,
			'order'			=> 'socialPostDate',
			'direction'		=> 'ASC'
		));

		$more = '';
		foreach($flat as $f){
			$more .= $this->post($f, $opt);
		}

		$item = $this->helperReplace($item, array(
			'more' => $more
		));

		unset($more);
	}else{
		$item = $this->helperReplace($item, array('more' => ''));
	}

	return $item;
}

/* + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - 
+ - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - */
public function wallActivity($opt, $e){

	echo "<table border=\"1\" width=\"100%\" style=\"margin:10px 0 10px 0;\">";	
		echo "<tr><td>";
		$this->activityItem($e, $opt);
		echo "</td></tr>";
	echo "</table>";

}

/* + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - 
+ - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - */
public function rate($opt=array()){

	$post	= $opt['post'];
	$pUsers	= $post['socialPostRatePlusUser'];
	$mUsers	= $post['socialPostRateMinusUser'];
	$first	= ($opt['first']) ? 'true' : 'false';


	if(in_array($this->user['id_user'], $pUsers)){

		$nblike = (sizeof($pUsers) -1);
		$aime = ((sizeof($pUsers) - 1) > 1) ? $nblike." personnes aimez " : $nblike." personne aimez ";
		if(sizeof($pUsers) > 1) $more = "<div class=\"likeit\" data-like=\"".$nblike."\"><a href=\"#\">Vous et ".$aime."</a></div>";

		$ret = 	$more.
				"<div><a onclick=\"unlike(".$post['id_socialpost'].",this,".$first.")\">Je n'aime plus</a></div>";
				if($opt['first']) $ret .= "<div><a onclick=\"showComments($(this))\">Commenter</a></div>";

	}else{

		$aime = (sizeof($pUsers) > 1) ? " personnes aiment " : " personne aime ";
		if(sizeof($pUsers) > 0) $more = "<div class=\"likeit\" data-like=\"".sizeof($pUsers)."\">".sizeof($pUsers)." ".$aime."</div>";

		$ret =	$more.
				"<div><a onclick=\"like(".$post['id_socialpost'].",this,".$first.")\">J'aime</a></div>";
				if($opt['first']) $ret .= "<div><a onclick=\"showComments($(this))\">Commenter</a></div>";
	}
	
	return $ret;
}

/* + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - 
+ - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - */
public function activity($opt){
	
	$id_user 	= $opt['id_user'];
	$activity	= $opt['activity'];
	
	$opt_ = $opt;
	unset($opt_['activity']);

	if(sizeof($activity) == 0) return false;

	echo "<table border=\"1\" width=\"100%\">";	
	foreach($activity as $e){
		echo "<tr><td>";
		$this->activityItem($e, $opt_);
		echo "</td></tr>";
	}
	echo "</table>";

}

/* + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - 
+ - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - */
public function activityItem($e, $opt){

	echo $e['socialActivityDate']."<br />";

	# User
	#	
	$user = $this->apiLoad('user')->userGet(array('id_user' => $e['id_user']));
	$user = "<a href=\"post-by?id_user=".$user['id_user']."\">".$this->userNameVisible($user)."</a>";

	# POST
	#
	if(intval($e['id_socialpost']) > 0){
	
		$post = $this->apiLoad('socialPost')->socialPostGet(array('id_socialpost' => $e['id_socialpost']));
		$post = "<a href=\"post?id_socialpost=".$post['id_socialpostthread']."\">&laquo; ".$post['socialPostData']."...&raquo;</a>";

		if($e['socialActivityFlag'] == 'PLUS'){
			echo "<b>".$user."</b> aime <b>".$post."</b>";
		}else
		if($e['socialActivityFlag'] == 'MINUS'){
			echo "<b>".$user."</b> aime pas ".$post."</b>";
		}else
		if($e['socialActivityFlag'] == 'REPLY'){
			echo "<b>".$user."</b> a ajout&eacute; un commentaire ".$post."</b>";
		}else{
			echo "<b>PAS RECONNU : ".$e['socialActivityFlag']."</b>";
		}
	}

}

/* + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - 
+ - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - */
public function followLink($opt){

	if(in_array($opt['id_followed'], $opt['follow'])){
		echo "<span class=\"follow\"><i class=\"plus\"></i><a onclick=\"_follow(this,".$opt['id_followed'].",1);\">Ne plus suivre</a></span>";
	}else{
		echo "<span class=\"follow\"><i class=\"plus\"></i><a onclick=\"_follow(this,".$opt['id_followed'].",0);\">Suivre</a></span>";
	}
	
#	return $html;
}

/* + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - 
+ - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - */
public function circleLink($opt){

	$myCircles = is_array($opt['myCircles']) ? $opt['myCircles'] : array();
	$myPending = is_array($opt['myPending']) ? $opt['myPending'] : array();

	if(in_array($opt['id_socialcircle'], $myPending)){
		echo "<span><a href=\"#\" onclick=\"_circle(this,".$opt['id_socialcircle'].",'out');\" class=\"btn btn-mini\">Demande en cours (annuler)</a></span>";
	}else
	if(in_array($opt['id_socialcircle'], $myCircles)){
		echo "<span><a onclick=\"_circle(this,".$opt['id_socialcircle'].",'out');\" class=\"btn btn-mini\">Quitter</a></span>";
	}else{
		echo "<span><a onclick=\"_circle(this,".$opt['id_socialcircle'].",'in');\" class=\"btn btn-mini\">Rejoindre</a></span>";
	}

	#return $html;
}

/* + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - 
+ - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - */
public function eventLink($opt){

	$myEvents  = is_array($opt['myEvents'])  ? $opt['myEvents']  : array();
	$myPending = is_array($opt['myPending']) ? $opt['myPending'] : array();

	if(in_array($opt['id_socialevent'], $myPending)){
		echo "<a onclick=\"eventAction(this,".$opt['id_socialevent'].",'out');\" class=\"btn btn-mini\">Demande en cours (annuler)</a>";
	}else
	if(in_array($opt['id_socialevent'], $myEvents)){
		echo "<a class=\"btn btn-mini\" onclick=\"eventAction(this,".$opt['id_socialevent'].",'out');\">Ne plus participer</a>";
	}else{
		echo "<a class=\"btn btn-mini\" onclick=\"eventAction(this,".$opt['id_socialevent'].",'in');\">Participer</a>";
	}
	
#	return $html;
}

/* + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - 
+ - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - */
public function likeWrapp($opt){

	# J'aime
	#
#	$html  = "<div class=\"likeWrapp\">";
	$html  = $this->rate(array(
		'post'	=> $opt['post'],
		'rated'	=> $opt['rated'],
		'first'	=> $opt['first']
	));
#	$html .= "</div>";
	
	# Picto
	#
	if($opt['first']){
		$nb_likes 		= $opt['post']['socialPostRatePlus'];
		$nb_comments	= sizeof($opt['post']['socialPostFlat']);

		if($nb_likes 	> 0) $class_likes		= "liked";
		if($nb_comments	> 0) $class_comments	= "commented";	

		$html .= '<div class="pictolikes" data-like="'.$nb_likes.'"><i class="'.$class_likes.'"></i>'.$nb_likes.'</div>';
		$html .= '<div class="pictocomments"><i class="'.$class_comments.'"></i>'.$nb_comments.'</div>';
	}

	# Date + Heure
	#
	$html .= "<div class=\"time\"><a href=\"/social/post/".$opt['post']['id_socialpostthread']."\" class=\"time\">";
	$html .= "<abbr title=\"".$opt['post']['socialPostDate']."\" class=\"timeago\">".$opt['post']['socialPostDate']."</abbr>";
	$html .= "</a></div>";

	# Supprimer
	#
	if($opt['post']['id_user'] == $this->user['id_user']){
		$html .= "<div class=\"remove\"><a onclick=\"postRemove(".$opt['post']['id_socialpost'].")\">Supprimer ce post</a></div>";
	}else
	if($this->user['is_admin'] == '1'){
		$html .= "<div class=\"remove\"><a onclick=\"postRemove(".$opt['post']['id_socialpost'].")\">Supprimer ce post (Droit Super Admin)</a></div>";
	}

	return $html;
}

/* + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - 
+ - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - */
public function message($e, $opt=array()){
	
	$first	= ($opt['first']) ? true : false;
	$square	= ($opt['first']) ? 48   : 30;
	
	// Avatar
	if(sizeof($e['author']['userMedia']['image']) > 0){
		$tmp	= $this->mediaUrlData(array(
			'url'		=> $e['author']['userMedia']['image'][0]['url'],
			'mode'		=> 'square',
			'value'		=> $square,
			'cdn'		=> true,
		));
		$avatar = '<img data-original="'.$tmp['img'].'" height="'.$tmp['height'].'" width="'.$tmp['height'].'" class="lazy" />';
		unset($tmp);
	}else{
		
		$avatar = '<img data-original="'.$this->avatar_path.'" height="'.$square.'" width="'.$square.'" class="lazy" />';
	}

	$html = "<div class=\"message-item ".((!$first) ? 'small' : '')." clearfix\" id=\"message-".$e['id_socialmessage']."\">";
		$html .= "<div class=\"avatar\">".$avatar."</div>";

		# OWNER
		#
		$html .= "<div class=\"head head-float\">";
		$html .= $e['author']['userMail'];
		$html .= "</div>";

		# DATA
		#
		$html .= "<div class=\"data data-float clearfix\">";
	#	$html .= "<div class=\"txt-wrapp\">".nl2br(htmlentities(trim($e['socialMessageData'])))."</div>";
		$html .= "<div class=\"txt-wrapp\">".nl2br(trim($e['socialMessageDataView']))."</div>";

		# OPEN GRAPH DATA ?
		#
		$og = $e['socialMessageOpenGraph'];
		if(sizeof($og) > 0){
			$html .= "<div class=\"og-wrapp\">";
			foreach($og as $oge){
				$html .= "<div class=\"oge\">".$this->openGraphItemView($oge, (($first) ? 600 : 550))."</div>"; 
			}
			$html .= "</div>";
		}

		# INFOS
		#
	#	$date  = utf8_decode($this->helperDate($e['socialMessageDate'], '%A %d %B %Y &agrave; %Hh%M'));
		$date  = $this->helperDate($e['socialMessageDate'], '%A %d %B %Y &agrave; %Hh%M');

		$html .= "<div class=\"info\">";
		if($first){
			$html .= "<a href=\"message-detail?id_socialmessage=".$e['id_socialmessage']."\">".$date."</a>";
		}else{
			$html .= $date;
		}
		if($e['id_user'] == $this->user['id_user']){
			$html .= " &#8212; <a onclick=\"messageRemove(".$e['id_socialmessage'].")\">Supprimer ce message</a>";
		}
		$html .= "</div>";


		if($first){
			$html .= "<div class=\"inject\" id=\"inject-small-".$e['id_socialmessage']."\">";

			$html .= "<div class=\"t\"></div>";

			$html .= "{more}";

			$html .= "<div class=\"form off\" id=\"replyto-".$e['id_socialmessage']."\">";
				$html .= "<div class=\"message-here\"></div>";
				$html .= "<textarea id=\"".$e['id_socialmessage']."\" placeholder=\"Ma reponse ...\"></textarea>";
				
				$html .= "<div class=\"toggle\">";
				$html .= "<a onclick=\"messageSubmit(".$e['id_socialmessage'].");\" class=\"btn btn-mini\">Ajouter mon message</a>";
				$html .= "</div>";

			$html .= "</div>";

			$html .= "</div>";
		}


	$html .= "</div>";
	$html .= "</div>";

	return $html;
}

/* + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - 
+ - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - */
public function messageWall($opt){

	$items	= $opt['message'];
	$users	= array();
	$html	= "<div id=\"conversation\">";

	foreach($items as $e){

		$org  = $e;
		$last = $e['socialMessageFlat'][sizeof($e['socialMessageFlat'])-1];

		// Inverser le LAST et le CURRENT pour afficher le dernier message
		if($last != ''){
			$e = $this->apiLoad('socialMessage')->socialMessageGet(array(
				'id_socialmessage'	=> $last,
				'withRecipient'		=> true,
				'withAuthor'		=> true
			));
		}

		// Author
		if(array_key_exists($e['id_user'], $users)){
			$author = $users[$e['id_user']];
		}else{
			$author = $this->apiLoad('user')->userGet(array(
				'id_user'	=> $e['id_user'],
				'useMedia'	=> true
			));

			$users[$e['id_user']] = $author;
		}

		if(sizeof($author['userMedia']['image']) > 0){
			$tmp	= $this->mediaUrlData(array(
				'url'		=> $author['userMedia']['image'][0]['url'],
				'mode'		=> 'square',
				'value'		=> 60,
				'cdn'		=> true,
			));
			$avatar = '<img data-original="'.$tmp['img'].'" height="'.$tmp['height'].'" width="'.$tmp['height'].'" class="lazy" />';
		}else{
			$avatar = '<img data-original="'.$this->avatar_path.'" height="60" width="60" class="lazy" />';
		}


		# RECIPIENT
		#
		unset($rcpt);
		if(sizeof($org['socialMessageRecipient']) > 0){
			foreach($org['socialMessageRecipient'] as $r){
				$rcpt[] = "<a href=\"/social/membre/".$r['id_user']."\">".$this->apiLoad('socialTemplate')->userNameVisible($r)."</a>";
			}
		}

		$rcpt_ = 'Message de <a href="/social/membre/'.$e['id_user'].'">'.$this->apiLoad('socialTemplate')->userNameVisible($org['author'])."</a>";
		if(sizeof($rcpt) > 0) $rcpt_ .= " avec ".implode(', ', $rcpt);

		$html .= $this->messageThreadItem(array(
			'message'	=> $e,
			'intro'		=> true,
			'rcpt'		=> $rcpt_
		));

		/*$html .= "<div class=\"message-item clearfix\">";
			$html .= "<div class=\"avatar\">".$avatar."</div>";
		
			$html .= "<div class=\"user\">".$rcpt_."</div>";
			$html .= "<div class=\"time\"><a href=\"/profil/message/".$org['id_socialmessage']."\">".$this->helperDate($e['socialMessageDate'], '%e %b')."</a></div>";

			$html .= "<div class=\"text\">";
				$html .= $e['socialMessageDataView'];
				$html .= "<br /><a href=\"/profil/message/".$org['id_socialmessage']."\" class=\"btn btn-mini\">Afficher la conversation compl&egrave;te</a>";
			$html .= "</div>";
	
		$html .= "</div>";*/

	}

	$html .= "</div>";

	return $html;
}

/* + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - 
+ - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - */
public function messageThread($opt){

	# Premier message
	#
	$message = $this->apiLoad('socialMessage')->socialMessageGet(array(
		'debug'				=> false,
		'id_socialmessage'	=> $opt['id_socialmessage'],
		'withRecipient'		=> true
	));

	$v[] = $message;

	# Toutes les reponses par ordre de date
	# 
	if(is_array($message['socialMessageFlat']) && sizeof($message['socialMessageFlat']) > 0){
		$messages = $this->apiLoad('socialMessage')->socialMessageGet(array(
			'debug'				=> false,
			'id_socialmessage'	=> $message['socialMessageFlat'],
			'order'				=> "FIND_IN_SET(k_socialmessage.id_socialmessage, '".implode(',', $message['socialMessageFlat'])."')",
			'direction'			=> 'ASC'

		));

		foreach($messages as $e){
			$v[] = $e;
		}
	}
	
	# Pour tous les message => On afficher une ligne
	#
	$html = '';
	foreach($v as $e){
		$html .= $this->messageThreadItem(array(
			'message' => $e
		));
	}

	# Possibilite de supprimer la convesation
	#
	if($message['id_user'] == $this->user['id_user']){
		$del = "<a onclick=\"messageConversationRemove(".$message['id_socialmessage'].")\" class=\"btn btn-mini\">Supprimer cette conversation</a>";
	}

	echo
	"<div id=\"conversation\">".
		"<div class=\"action\">". $del ."</div>".
		$html.
	"</div>";
}

/* + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - 
+ - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - */
public function messageThreadItem($opt){

	$e = $opt['message'];

	$author = $this->apiLoad('user')->userGet(array(
		'debug'		=> false,
		'id_user'	=> $e['id_user'],
		'useMedia'	=> true,
		'useField'	=> true,
	));

	if(sizeof($author['userMedia']['image']) > 0){
		$tmp = $this->mediaUrlData(array(
			'url'		=> $author['userMedia']['image'][0]['url'],
			'mode'		=> 'square',
			'value'		=> 60,
			'cdn'		=> true,
		));
		$avatar = '<img data-original="'.$tmp['img'].'" height="'.$tmp['height'].'" width="'.$tmp['height'].'" class="lazy" />';
	}else{
		$avatar = '<img data-original="'.$this->avatar_path.'" height="60" width="60" class="lazy" />';
	}

	$rcpt = ($opt['rcpt'] == '')
		? "<a href=\"/social/membre/".$e['id_user']."\">".$this->apiLoad('socialTemplate')->userNameVisible($author)."</a>"
		: $opt['rcpt'];

#	$html = $e['id_socialmessage'];
	$html = "<div class=\"message-item clearfix\">";

		$html .= "<div class=\"avatar\">";
		$html .= "<a href=\"/social/membre/".$e['id_user']."\">".$avatar."</a>";
		$html .= "</div>";

		$html .= "<div class=\"user\">".$rcpt."</div>";
#		$html .= "<div class=\"time\">".$this->helperDate($e['socialMessageDate'], '%e %b')."</div>";

		$html .= "<div class=\"time\"><abbr title=\"".$e['socialMessageDate']."\" class=\"timeago\">".$e['socialPostDate']."</abbr></div>";

		$html .= "<div class=\"text\">".nl2br($e['socialMessageDataView']);
		if($opt['intro']){
			$html .= "<br /><a href=\"/profil/message/".$e['id_socialmessagethread']."\" class=\"btn btn-mini\">Afficher la conversation compl&egrave;te</a>";
		}
		$html .= "</div>";

	$html .= "</div>";	

	// Noter la ligne commu lu
	$this->apiLoad('socialMessage')->socialMessageMarkRead(array(
		'id_socialmessage'	=> $e['id_socialmessage'],
		'id_user'			=> $this->user['id_user'],
		'is_read'			=> 1
	));

	return $html;
}

/* + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - 
+ - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - */
public function notificationWall($opt){

	if(sizeof($opt['notification']) == 0) return NULL;
	
	foreach($opt['notification'] as $d){
		$tmp[substr($d['socialActivityDate'], 0, 10)][] = $d;
	}
	
	

	echo '<div class="notification-wall">';

	foreach($tmp as $date => $data){
	
		$d = $this->helperDate($date, '%A %e %B');

		if(date("Y-m-d", $this->helperDate($date, TIMESTAMP)) == date("Y-m-d"))							$d = 'aujourd\'hui';
		if(date("Y-m-d", $this->helperDate($date, TIMESTAMP)) == date("Y-m-d", strtotime("-1 day")))	$d = 'hier';
	
		echo '<div class="day">';
		echo '<div class="name">'.ucfirst($d).'</div>';
	
		echo '<ul>';
		foreach($data as $e){
			$html = $this->notificationItem($e);
		#	if(!empty($html)) echo utf8_encode('<li class="view-'.$e['socialNotificationView'].'">'.$html.'</li>');
			if(!empty($html)) echo '<li class="view-'.$e['socialNotificationView'].'">'.$html.'</li>';
		#	if($e['socialActivityId'] == 34250) $this->pre($e['socialPost']);
		}
		echo "</ul>";

		echo "</div>";
	}

	echo "</div>";
	
}

//---  --- ---  --- ---  --- ---  --- ---  --- ---  --- ---  --- ---  --- ---  --- ---  --- ---  --- ---  --- ---  --- -
//---  --- ---  --- ---  --- ---  --- ---  --- ---  --- ---  --- ---  --- ---  --- ---  --- ---  --- ---  --- ---  --- -
public function notificationItem($e){

	// USER qui SUIT ///////////////////////////////////////////////////////////////////////////////////////////////////
	if($e['socialActivityKey'] == 'follow'){# && $this->user['id_user'] == $e['socialActivityId']){
		$html = '<i class="icon-flag"></i> {user} vous suit d&eacute;sormais {time}';
	}else

	 // GROUP //////////////////////////////////////////////////////////////////////////////////////////////////////////
	if($e['socialActivityKey'] == 'id_socialcircle'){

		// MISE A JOUR PAR L'ADMIN
		if($e['socialActivityFlag'] == 'UPDATE'){
			$html = '<i class="icon-user"></i> {user} a mis à jour le groupe {circle} {time}';
		}else

		// JE SUIS LE PROPRIO DU GROUPE
		if($this->user['id_user'] == $e['socialCircle']['id_user']){
			if($e['socialActivityFlag'] == 'PENDING'){
				$html = '<i class="icon-user"></i>{user} a rejoint votre groupe {circle} - en attente de confirmation {time}';
			}else
			if($e['socialActivityFlag'] == 'ACCEPTED'){
				$html = '<i class="icon-user"></i> {user} a rejoint votre groupe {circle} {time}';
			}
		}else

		// UN COMPTE SUR UN GROUPE
		if($this->user['id_user'] == $e['socialCircle']['id_user']){
			if($e['socialActivityFlag'] == 'PENDING'){
				$html = '<i class="icon-user"></i> {user} a demandé l\'inscription au groupe {circle} est en attente {time}';
			}else
			if($e['socialActivityFlag'] == 'ACCEPTED'){
				$html = '<i class="icon-user"></i> {user} fait désormais parti du groupe {circle} {time}';
			}
		}
	}else

	// EVENT ///////////////////////////////////////////////////////////////////////////////////////////////////////////
	if($e['socialActivityKey'] == 'id_socialevent'){

		// L'ADMIN d'un EVENT à mise à jour l'EVENT
		if($e['socialActivityFlag'] == 'UPDATE'){
			$html = '<i class="icon-calendar"></i> {user} a mis à jour l\'évement {event} {time}';
		}
	}else

	// POST ////////////////////////////////////////////////////////////////////////////////////////////////////////////
	if($e['socialActivityKey'] == 'id_socialpost'){

		// MES DATA
		if($this->user['id_user'] == $e['socialPost']['user']['id_user']){
			if($e['socialActivityFlag'] == 'PLUS'){
				$html = '<i class="icon-thumbs-up"></i> {user} a aimé mon {post} {time}';
			}else
			if($e['socialActivityFlag'] == 'POST' OR $e['socialActivityFlag'] == 'REPLY'){
				$html = '<i class="icon-comment"></i> {user} a commenté mon {post} {time}';
			}
		}else

		// SES DATA
		if($e['id_user'] == $e['socialPost']['user']['id_user']){
			if($e['socialActivityFlag'] == 'PLUS'){
				$html = '<i class="icon-thumbs-up"></i> {user} a aim&eacute; son propre {post} {time}';
			}else
			if($e['socialActivityFlag'] == 'POST' OR $e['socialActivityFlag'] == 'REPLY'){
				$html = '<i class="icon-comment"></i> {user} a commenté son propre {post} {time}';
			}
		}else{
			if($e['socialActivityFlag'] == 'PLUS'){
				$html = '<i class="icon-thumbs-up"></i> {user} aimé le {post} {owner} {time}';
			}else
			if($e['socialActivityFlag'] == 'POST' OR $e['socialActivityFlag'] == 'REPLY'){
				$html = '<i class="icon-comment"></i> a comment&eacute; le {post} {owner} {time}';
			}
		}
	}




#	if(!isset($html)) return var_export($e, true);

	$idn = '?activity='.$e['id_socialactivity'];

	$ts = new DateTime($e['socialActivityDate']);

	$owner = (count($e['socialPost']['user']) > 0)
		? 'de <a href="/social/membre/'.$e['socialPost']['id_user'].'?'.$idn.'">'.$this->userNameVisible($e['socialPost']['user']).'</a>'
		: 'd\'un membre d&eacute;sinscrit';

	$html = $this->helperReplace($html, array(
		'time'		=> '<span class="date">&mdash; '.$ts->format('H:i').'</span>',
		'post'		=> '<a href="/social/post/'.$e['socialActivityThread'].$idn.'">message</a>',
		'user'		=> '<a href="/social/membre/'.$e['id_user'].$idn.'">'.$this->userNameVisible($e['user']).'</a>',
		'owner'		=> $owner,
		'circle'	=> '<a href="/social/groupe/'.$e['socialCircle']['id_socialcircle'].$idn.'">'.$e['socialCircle']['socialCircleName'].'</a>',
		'event'	    => '<a href="/social/event/'.$e['socialActivityId'].$idn.'">'.$e['socialEvent']['socialEventName'].'</a>'
	));

	return $html;		
}

/* + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - 
+ - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - */
public function notificationPop($opt){

	if(sizeof($opt['notification']) == 0) return NULL;
	
	echo '<div class="notification-pop">';
	echo '<ul>';

	foreach($opt['notification'] as $e){
		echo '<li>['.$e['socialNotificationView'].'] '.$this->notificationItem($e).'</li>';
	}

	echo "</ul>";
	echo "</div>";
	
}

public function isJson($string) {
	@json_decode($string);
	return (json_last_error() == JSON_ERROR_NONE);
}

}