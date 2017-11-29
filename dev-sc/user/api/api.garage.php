<?php

class garage extends coreApp {

	# content types
	private $t_garage;
	private $t_marque;
	private $t_modele;
	# champs
	private $c_marque;
	private $c_modele;
	private $c_description;
	# fields (cl?s)
	private $f_modele;
	private $f_marque;
	private $f_cylindree;
	private $f_description;
	
	private $file_cylindree;
	
public function __construct() {
	
	$this->t_garage = 6;
	$this->t_marque = 3;
	$this->t_modele = 4;
	
	$this->c_marque 	 = 'field10';
	$this->c_modele 	 = 'field11';
	$this->c_description = 'field2';
	$this->c_annee	 	 = 'field26';
	
	$this->f_marque 	 = 10;
	$this->f_modele 	 = 11;
	$this->f_cylindree   = 14;
	$this->f_description = 2;
	$this->f_annee	 	 = 26;
	
	$this->file_cylindree = KROOT.'/media/app/cylindree/cylindree.config';

	parent::__construct();
}

function __clone(){}

/* + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - 
+ - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - */
public function garageGet($opt = NULL){

	$build = array();
	
	$build['debug']				= $opt['debug'];
	$build['id_content'] 	  	= $opt['id_content'];
	$build['id_type']		  	= $this->t_garage;
	$build['useChapter'] 	  	= false;
	$build['useGroup'] 	 	  	= false;
	$build['assoSocialForum'] 	= true;
	$build['assoUser']		 	= ($opt['assoUser'] == true);

	if($opt['contentSee'])  $build['contentSee'] = 'ALL';

	if($opt['limit'] != NULL) {
		$build['limit'] = $opt['limit'];
		$build['offset']= $opt['offset'];
	} else {
		$build['noLimit'] = true;
	}
	
	if (isset($opt['order']) && isset($opt['direction'])) {
		$build['order'] = $opt['order'];
		$build['direction'] = $opt['direction'];
	}
		
	## pas le choix ici
	$build['searchLink'] = 'OR';

	if(is_array($opt['search'])){
		$build['search'] 		= $opt['search'];
		$build['searchLink']	= $opt['searchLink'];
	}

	if ($opt['id_user'] != NULL) $build['id_user'] = $opt['id_user'];

	if ($opt['id_socialforum'] != NULL) $build['id_socialforum'] = $opt['id_socialforum'];
	
	
	## trouver les garages par marque et/ou modele
	if (isset($opt['id_marque']) && is_array($opt['id_marque'])) {
		foreach ($opt['id_marque'] as $marque)	{
			$build['search'][] = array('searchField' => $this->c_marque, 'searchValue' => $marque, 'searchMode' => 'EG');	
		}	
	}
	if (isset($opt['id_modele']) && is_array($opt['id_modele'])) {
		foreach ($opt['id_modele'] as $modele)	{
			$build['search'][] = array('searchField' => $this->c_modele, 'searchValue' => $modele, 'searchMode' => 'EG');	
		}	
	}
	
	## trouver les garages en attente de mod?ration (contentSee = 0)
	if ($opt['moderate']) {
		$build['search'][] = array('searchField' => 'contentSee', 'searchValue' => '0', 'searchMode' => 'EG');
		$build['contentSee'] = 'ALL'; # on a besoin de tout voir
	}	

	$result = $this->apiLoad('content')->contentGet($build);
	$this->total = $this->apiLoad('content')->total;
	
	return $result;
		
}

/* + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - 
+ - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - */
public function garageSet($opt) {
	
	$build = array();
	## opt defaults
	##
	## Garage, par d?faut [noVerify] = true; je laisse l'opt au cas o? mais 
	## elle n'a pas vraiment d'interet ici
	$_noVerify 		= $opt['noVerify'] != NULL ? $opt['noVerify'] : true;
	$_user			= $opt['user'] != NULL ? $opt['user'] : $this->user['id_user'];
	$_contentsee	= $opt['contentSee'] != NULL ? $opt['contentSee'] : 1; # par d?faut 1 sauf v?rifs apres
	if($opt['debug']) $build['debug'] = true; 
	
	## contentSet defaults
	$build['id_type'] 	= $this->t_garage;
	$build['language']	= 'fr';
	$build['id_group']	= array(-1);
	$build['id_chapter']= array(1);
	if ($opt['id_content']) { # update
		$build['id_content'] = $opt['id_content'];	
		$_noVerify = true;
	}
	
	
	## Si trouv?, return
	if ($_noVerify == false) {
		
		$optchk['contentName'] = array($opt['contentName']); 
		$doublon = $this->garageGet($optchk);

		if (count($doublon) > 0) {
			return false;
		}
	}
	
	# annee de la moto
	if ($opt['annee'] != NULL) {
			
		//$this->pre($opt['annee']);
		$build['field'][$this->f_annee] = $opt['annee'];
	}
	
	if ($opt['id_content'] != NULL) {
		$retrieve = $this->garageGet(array(
			'id_content' => $opt['id_content'],
			'contentSee' => 'ALL'
		));
		$build['field'][$this->f_marque] = array($retrieve['field']['_lienMarque'][0]['id_content']);
		$build['field'][$this->f_modele] = array($retrieve['field']['_lienModele'][0]['id_content']);	
						
	} 
	if ($opt['id_marque'] != NULL && $opt['id_modele'] != NULL) {
	## on a besoin d'une marque et d'un modele pour le garage 	
		$build['field'][$this->f_marque] = array($opt['id_marque']);
		$build['field'][$this->f_modele] = array($opt['id_modele']);
		
	## si on met a jour un garage on a pas besoin de tout resp?cifier
	} else { 
		return false;
	}
	
	if ($opt['contentName'] != NULL) {
		$_url = $this->helperUrlEncode($opt['contentName'], 'fr');
		$build['data'] = array('k_contentdata' => array(
			'contentName' => array('value' => $opt['contentName']),
			'contentUrl'  => array('value' => $_url)
		));
	}	
	
	## TODO !!! Attention, cette condition empeche de valider un garage sans les marques/modeles, a voir si ca se garder
	## Check si la marque/modeles sont valid?s, sinon contentsee = 0
	$modele = $this->apiLoad('garage')->modeleGet(array('id_content' => $build['field'][$this->f_modele][0], 'contentSee' => 'ALL'));
	$marque = $this->apiLoad('garage')->marqueGet(array('id_content' => $build['field'][$this->f_marque][0], 'contentSee' => 'ALL'));
	if (!empty($modele) && !empty($marque)) {
		if ($modele['contentSee'] == 0 || $marque['contentSee'] == 0) {
			$_contentsee = 0;			
		}
	} else {
		return false;
	}
	
	if ($opt['id_socialforum'] != NULL && is_array($opt['id_socialforum'])) {
		$build['id_socialforum'] = $opt['id_socialforum'];
	}
	
	## Description de la moto
	if ($opt['description'] != NULL) {
		$build['field'][$this->f_description] = $opt['description'];
	}	
	
	## Cylindr?e, check avec les cyl dispos sur le modele, si pas dans le json
	## => garage en attente de mod?ration
	if ($opt['cylindree'] != NULL) {
		
		if (!empty($modele['field']['_cylindreeModele'])) {
			$cylindrees = json_decode($modele['field']['_cylindreeModele'], true);	
		} else {
			$cylindrees = array();
		}
		## si la cylindr?e existe => ok, sinon contentsee = 0
		if (in_array($opt['cylindree'], $cylindrees) && is_numeric($opt['cylindree'])) {
			$build['field'][$this->f_cylindree] = $opt['cylindree'];
		} else {
			$build['field'][$this->f_cylindree] = $opt['cylindree'];
			$_contentsee = 0;
		}
	}

	if($opt['_moto'] == '1'){
		$build['field']['_moto'] = '1';
	}else
	if($opt['_car']  == '1'){
		$build['field']['_car'] = '1';
	}
	
	## def
	$build['def'] = array('k_content' => array(
			'id_user'				=> array('value' => $_user),		
			'contentSee'			=> array('value' => $_contentsee),
			'contentDateCreation'	=> array('value' => date("Y-m-d G:i"))
		));
	## content set, media set, renvoie l'id

	$result = $this->apiLoad('content')->contentSet($build);
	
	if ($result && $opt['nostats'] != true) {
		
		$this->garageStatsSet();
		$id = $this->apiLoad('content')->id_content;
		
		if ($opt['media'] != NULL) {
			$this->apiLoad('content')->contentMediaLink(array(
				'id_content'	=>$id,
				'url'			=>$opt['media'],
				'debug'			=>$opt['debug']
			));
		}
		
		# POST UNIVERS 
		
		if ($opt['id_socialforum'] != null && is_array($opt['id_socialforum'])) {
			$postForum = $opt['id_socialforum'];
		} else {
			$postForum = array(MASTER_ID_FORUM);
		}
		
		if($_contentsee){
			$vehiType = (MODE == "MOTO") ? "moto" : "voiture";
			$this->apiLoad('socialPost')->socialPostSet(array(
				'debug'				=> false,
				'id_socialpost'		=> NULL,
				'forum'				=> $postForum,
				'core'				=> array(
					'id_content'			=> array('value' => $id),
					'is_activity'			=> array('value' => 1),
					'id_user'				=> array('value' => $_user),
					'socialPostData'		=> array('value' => "Je viens d'ajouter une ".$vehiType." &agrave; mon garage, {link}"),
					'socialPostDataParam'	=> array('value' => json_encode(array(
						'linkType'	=> 'garage',
						'linkId'	=> $id,
						'linkName'	=> htmlentities($opt['contentName'])
					)))
				),
				'field' => array(
					'_moto' => (MODE == "MOTO") ? '1' : 0,
					'_car' 	=> (MODE == "CAR") ? '1' : 0,
				)
			));
		}

		return $id;

	}else{
		return false;
	}

}

/* + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - 
+ - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - */
public function marqueGet($opt = NULL){
	
	$build = array();

	$build['id_content'] 	= $opt['id_content'];
	$build['id_type']		= $this->t_marque;
	if($opt['contentSee'])  $build['contentSee'] = 'ALL';
	if($opt['debug']) 		$build['debug'] = true; 
	
	## defaults no group/chapter, no limit
	$build['useChapter'] = false;
	$build['useGroup'] 	 = false;
	if ($opt['limit'] != NULL) {
		$build['limit'] = $opt['limit'];
	} else {
		$build['noLimit'] = true;
	}
	

	if (isset($opt['order']) && isset($opt['direction'])) {
		$build['order'] = $opt['order'];
		$build['direction'] = $opt['direction'];
	} else {
		$build['order'] = 'contentName';
		$build['direction'] = 'ASC';
	}
	
	## trouver par nom de marque 
	if ($opt['contentName'] != NULL && is_array($opt['contentName'])) {
		## si sp?cifi? le mode de recherche
		## LIKE% -> rajouter un LIKE tout seul dans core.db->dbMatch()
		$searchMode = $opt['searchMode'] != NULL ? $opt['searchMode'] : 'EG';
		foreach ($opt['contentName'] as $name) {
			$build['search'][] = array('searchField' => 'contentName', 'searchValue' => $name, 'searchMode' => $searchMode);
		}
	}
	
	## trouver les marques en attente de mod?ration (contentSee = 0)
	if ($opt['moderate']) {
		$build['search'][] = array('searchField' => 'contentSee', 'searchValue' => '0', 'searchMode' => 'EG');
		$build['contentSee'] = 'ALL'; # on a besoin de tout voir
	}

	if (is_array($opt['search'])){
		$build['search'] = $opt['search'];
	}

	$result = $this->apiLoad('content')->contentGet($build);
	return $result;
	
}

/* + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - 
+ - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - */
public function marqueSet($opt) {

	$build = array();
	
	## opt defaults
	$_noVerify 		= $opt['noVerify'] != NULL ? $opt['noVerify'] : false;
	$_user			= $opt['user'] != NULL ? $opt['user'] : $this->user['id_user'];
	$_contentsee	= $opt['contentSee'] != NULL ? $opt['contentSee'] : 0;
	if($opt['debug']) $build['debug'] = true; 
	
	## contentSet defaults
	$build['id_type'] 	= $this->t_marque;
	$build['language']	= 'fr';
	if ($opt['id_content']) { # update
		$build['id_content'] = $opt['id_content'];	
		$_noVerify = true;
	}
	
	## Si trouv?, return
	if ($_noVerify == false) {
		$optchk['contentName'] = array($opt['contentName']); 
		$doublon = $this->marqueGet($optchk);

		if (count($doublon) > 0) {
			return false;
		}
	}
	
	if ($opt['contentName'] != NULL) {
			
		$_url = $this->helperUrlEncode($opt['contentName'], 'fr');
		$build['data'] = array('k_contentdata' => array(
			'contentName' => array('value' => $opt['contentName']),
			'contentUrl'  => array('value' => $_url)
		));
	}	
	
	if (intval($opt['_moto']) > 0) $build['field']['_moto'] = '1';
	if (intval($opt['_car']) > 0) $build['field']['_car'] = '1';
		
	## def
	$build['def'] = array('k_content' => array(
			'id_user'				=> array('value' => $_user),		
			'contentSee'			=> array('value' => $_contentsee),
			'contentDateCreation'	=> array('value' => date("Y-m-d G:i"))
		));

	## content set, media set, renvoie l'id
	$result = $this->apiLoad('content')->contentSet($build);
	if ($result) {
		
		$id = $this->apiLoad('content')->id_content;
		
		if ($opt['media'] != NULL) {
			$this->apiLoad('content')->contentMediaLink(array(
				'id_content'	=>$id,
				'url'			=>$opt['media'],
				'debug'			=>$opt['debug']
			));
		}
		
		return $id;
	} else {
		return false;
	}
	

}


/* + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - 
+ - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - */
public function modeleGet($opt = NULL){
	
	$build = array();

	$build['id_content'] = $opt['id_content'];
	$build['id_type']	 = $this->t_modele;
	if($opt['contentSee']) $build['contentSee'] = 'ALL';
	if($opt['debug']) 	   $build['debug'] = true; 

	## default opt
	$build['searchLink'] = isset($opt['searchLink']) ? $opt['searchLink'] : 'OR';
	
	
	## defaults no group/chapter, noLimit
	$build['useChapter'] = false;
	$build['useGroup'] 	 = false;
	if ($opt['limit'] != NULL) {
		$build['limit'] = $opt['limit'];
	} else {
		$build['noLimit'] = true;
	}
	
	## ordre d'arriv?e
	if (isset($opt['order']) && isset($opt['direction'])) {
		$build['order'] = $opt['order'];
		$build['direction'] = $opt['direction'];
	} else {
		$build['order'] = 'contentName';
		$build['direction'] = 'ASC';
	}
	
	## trouver les v?hicules de la marque
	if ( isset($opt['id_marque']) && is_array($opt['id_marque']) ) {
			
		foreach ($opt['id_marque'] as $marque) {
			$build['search'][] = array('searchField' => $this->c_marque, 'searchValue' => $marque, 'searchMode' => 'EG');
		}
	}
	
	## trouver par nom de modele 
	if ($opt['contentName'] != NULL && is_array($opt['contentName'])) {
		## si sp?cifi? le mode de recherche
		## LIKE% -> rajouter un LIKE tout seul dans core.db->dbMatch()
		$searchMode = $opt['searchMode'] != NULL ? $opt['searchMode'] : 'EG';
		foreach ($opt['contentName'] as $name) {
			$build['search'][] = array('searchField' => 'contentName', 'searchValue' => $name, 'searchMode' => $searchMode);
		}
	}
	
	## trouver les modeles en attente de mod?ration (contentSee = 0)
	if ($opt['moderate']) {
		$build['search'][] = array('searchField' => 'contentSee', 'searchValue' => '0', 'searchMode' => 'EG');
		$build['contentSee'] = 'ALL'; # on a besoin de tout voir
	}


	if (is_array($opt['search'])){
		$build['search'] = $opt['search'];
	}

	$result = $this->apiLoad('content')->contentGet($build);
	return $result;
}

/* + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - 
+ - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - */
public function modeleSet($opt){
	$build = array();

	## opt defaults
	$_noVerify 		= $opt['noVerify'] != NULL ? $opt['noVerify'] : false;
	$_user			= $opt['user'] != NULL ? $opt['user'] : $this->user['id_user'];
	$_contentsee	= $opt['contentSee'] != NULL ? $opt['contentSee'] : 0;
	if($opt['debug']) $build['debug'] = true; 

	## contentSet defaults
	$build['id_type'] 	= $this->t_modele;
	$build['language']	= 'fr';
	if ($opt['id_content']) { # update
		$build['id_content'] = $opt['id_content'];	
		$_noVerify = true;
	}
	
	
	## Si trouvé, return
	if ($_noVerify == false) {
		
		$optchk['contentName'] = array($opt['contentName']);
		$optchk['contentSee'] = 'ALL';

		$doublon = $this->modeleGet($optchk);
		
		## vérification des doublons, si pas de doute renvoie l'id
		## -> sinon vautrage
		if (count($doublon) == 1) {
			return $doublon[0]['id_content'];
		}
		else if (count($doublon) > 0) {
			//$this->pre("DOUBLON  !!", $doublon);
			//return $doublon[0]['id_content']
			## Trouver si on a un résultat qui correspond a notre marque dans les modeles
			foreach($doublon as $d) {
				if ($d['field']['_lienMarque'] == $opt['id_marque']) {
					return $d;
				}
			}

		}
	}
	
	if (intval($opt['_moto']) > 0) $build['field']['_moto'] = '1';
	if (intval($opt['_car']) > 0) $build['field']['_car'] = '1';
		
	## on a besoin d'une marque pour le modele 	
	if ($opt['id_marque'] != NULL) {
		$build['field'][10] = array($opt['id_marque']);
	}
	
	if ($opt['contentName'] != NULL) {
			
		$_url = $this->helperUrlEncode($opt['contentName'], 'fr');
		$build['data'] = array('k_contentdata' => array(
			'contentName' => array('value' => $opt['contentName']),
			'contentUrl'  => array('value' => $_url)
		));
	}	
	## Modifier les cylindr?es du modele
	if ( is_array($opt['cylindree']) && isset($build['id_content'])) {
		# [add] ajouter une/des cylindr?es
		# [remove] supprimer une/des cylindr?es
		# [set] remplacer le json
		$tmp  = $this->modeleGet(array('id_content' => $build['id_content'], 'contentSee' => 'ALL'));
		$json = json_decode($tmp['field']['_cylindreeModele'], true);

		if ( is_array($opt['cylindree']['add']) ) {
			foreach ($opt['cylindree']['add'] as $add) {
				if (is_numeric($add)) $json[] = $add;	
				$jsonbuild = true;
			}
		}
		if ( is_array($opt['cylindree']['remove']) ) {
			foreach ($opt['cylindree']['remove'] as $remove) {
				if (is_numeric($remove)) {
					foreach($json as $k=>$j) {
						if ($j == $remove) unset($json[$k]);
						$jsonbuild = true;
					}
				}	
			}
		}
		if ( is_array($opt['cylindree']['set']) ) {
			# faut-il vraiment tester ici ?	
			foreach ($opt['cylindree']['set'] as $set) {
				if (is_numeric($set))	{
					$json[] = $set;
					$jsonbuild = true;
				}			
			}			
		}
		
		if ($jsonbuild) {
			$build['field']['13'] = json_encode($json);
		}	
	}
	
	## def
	$build['def'] = array('k_content' => array(
			'id_user'				=> array('value' => $_user),		
			'contentSee'			=> array('value' => $_contentsee),
			'contentDateCreation'	=> array('value' => date("Y-m-d G:i"))
		));
		
	## content set, media set, renvoie l'id
	$result = $this->apiLoad('content')->contentSet($build);
	if ($result) {
		
		$id = $this->apiLoad('content')->id_content;
		
		if ($opt['media'] != NULL) {
			$this->apiLoad('content')->contentMediaLink(array(
				'id_content'	=>$id,
				'url'			=>$opt['media'],
				'debug'			=>$opt['debug']
			));
		}

		return $id;
	}else{
		return false;
	}
}

public function garageRemove($opt) {
	if ($opt['id_content'] != NULL ) {

		$this->apiLoad('hook')->garageHide(array(
			'kill'       => $opt['kill'],
			'id_content' => $opt['id_content']
		));

		if ($opt['nostats'] != true) $this->garageStatsSet();
	}
}
public function modeleRemove($opt) {
	if ($opt['id_content'] != NULL ) {
		$job = $this->apiLoad('content')->contentRemove(4, $opt['id_content'], 'fr');		
	}
}

public function marqueRemove($opt) {
	if ($opt['id_content'] != NULL ) {
		$modeles = $this->modeleGet(array(
			'id_marque' => array($opt['id_content']),
			'contentSee'=> 'ALL'
		));
		## supprimer tous les modeles li?s a la marques
		foreach ($modeles as $modele) {
			$this->modeleRemove(array(
				'id_content' => $modele['id_content']
			));
		}
		$job = $this->apiLoad('content')->contentRemove(6, $opt['id_content'], 'fr');		
	}
}

public function garageStatsSet() {

	if(MODE == 'MOTO'){
		$field = 54;
		$name  = 'motoGarageCount';
	}else
	if(MODE == 'CAR'){
		$field = 55;
		$name  = 'carGarageCount';
	}

	$stats = $this->dbOne("
		SELECT COUNT(k_content.id_content) AS ccc FROM k_content
		INNER JOIN k_content".TYPE_GARAGE." ON k_content.id_content = k_content".TYPE_GARAGE.".id_content
		WHERE contentSee=1 AND field".$field."=1
	");

	$this->dbQuery("UPDATE `@stats` SET statsValue = ".$stats['ccc']." WHERE statsName = '".$name."'");
	$this->cache->sqlcacheDelete('GLOBAL:STATS');
}

/*public function garageStatsGet($opt=null) {
	$result = $this->dbOne("SELECT * FROM `@stats` WHERE statsName = 'motoGarageCount'");
	return $result['statsValue'];
}*/

/* + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - 
+ - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - */
##	MODERATION :
##	- Si pas d'opt, renvoie tous les non-valid?s. 
## 	- Si opt['id_content'], renvoie le d?tail de ce qu'il reste a valider
## 		ex: pour un garage renvoie le modele & cylindr?e non valid?s 
public function garageModerateGet($opt = NULL) {
	if ($opt == NULL) {
		$moderate = $this->apiLoad('garage')->garageGet(array(
			'moderate' 	=> true,
			'order'		=> 'contentDateCreation',
			'direction'	=> 'DESC'
		));	
		return $moderate;	
	}	
	if ($opt['id_content']) {
		## pour la sortie :
		##  -> valid? == 0; a mod?rer == 1
		$toModerate['cylindree'] = 0;
		$toModerate['modele'] 	 = 0;
		$toModerate['marque'] 	 = 0;
		
		## aller chercher tout ce dont on a besoin		
		$garage 	= $this->garageGet(array('id_content' => $opt['id_content'], 'contentSee' => 'ALL'));
		$marque 	= $this->marqueGet(array('id_content' => $garage['field']['_lienMarque'][0]['id_content'], 'contentSee' => 'ALL'));
		$modele 	= $this->marqueGet(array('id_content' => $garage['field']['_lienModele'][0]['id_content'], 'contentSee' => 'ALL'));
		if (!empty($modele['field']['_cylindreeModele'])) {
			$cylindree = json_decode($modele['field']['_cylindreeModele'], true);	
		} else {
			$cylindree = array();
		}		
		
		## v?rifier si la cylindr?e est en attente de mod?ration
		if (!empty($garage['field']['cylindreeGarage'])) {
			foreach($cylindree as $c) {
				if ($c != $garage['field']['cylindreeGarage']) $toModerate['cylindree'] = 1;
			}
		}
		## verifier si le modele/marque sont valid?s
		if ($marque['contentSee'] == 0) $toModerate['marque'] = 1;
		if ($modele['contentSee'] == 0) $toModerate['modele'] = 1;

		## renvoie ce qu'il y a a mod?rer & le d?tail du garage
		return array($toModerate, $garage);		
	}
	
}

public function modeleModerateGet($opt = NULL) {
	$moderate = $this->apiLoad('garage')->modeleGet(array(
		'moderate' => true
	));	
	return $moderate;
}

public function marqueModerateGet($opt = NULL) {
	$moderate = $this->apiLoad('garage')->marqueGet(array(
		'moderate' => true
	));	
		
	return $moderate;
}

/* + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - */

public function marqueModerateSet($opt) {
	if ($opt['id_content'] == NULL) return false;
	
	if ($opt['valide'] != NULL) {
		$build['id_content'] = $opt['id_content'];
		$build['contentSee'] = '1';
	
		$valide = $this->marqueSet($build);
		return $valide;
	}
}

public function modeleModerateSet($opt) {
	if ($opt['id_content'] == NULL) return false;
	
	## TODO ? permettre de trouver toutes les cylindrees en attente ?	
	if ($opt['cylindree'] != NULL && is_array($opt['cylindree'])) {
		$build['cylindree'] = $opt['cylindree'];			
	}
	
	if ($opt['valide'] != NULL) {
		$build['id_content'] = $opt['id_content'];
		$build['contentSee'] = '1';
		
		$valide = $this->modeleSet($build);
		return $valide;
	}
}

public function garageModerateSet($opt) {
	if ($opt['id_content'] == NULL) return false;
	
	$garage = $this->garageModerateGet(array('id_content' => $opt['id_content']));

	$check  = $garage[0];
	$user	= $garage[1]['id_user'];
	$garage = $garage[1];
	
	## si tout le monde est visible, validable
	if ($check['modele'] == 0 && $check['marque'] == 0) {
		$build['contentSee'] = 1; # valider le garage	
	}

	if ($opt['valide'] != NULL) {
		
		$validate['nostats']	= true; # On valide depuis l'admin, pas de scope vers les globales de stats
		$validate['id_content'] = $opt['id_content'];
		$validate['contentSee'] = '1';
		$validate['id_marque']  = $garage['field']['_lienMarque']['id_content'];
		$validate['id_modele']  = $garage['field']['_lienModele']['id_content'];
		$validate['user']		= $user;
		
		## envoyer
		$this->garageSet($validate);
	}
	
	if ($opt['validateThrough'] != NULL) { # valider tout le garage
		$marque 	= $this->marqueGet(array('id_content' => $garage['field']['_lienMarque'][0]['id_content'], 'contentSee' => 'ALL'));
		$modele 	= $this->modeleGet(array('id_content' => $garage['field']['_lienModele'][0]['id_content'], 'contentSee' => 'ALL'));
		
		## checker la cylindr?e
		if (!empty($garage['field']['cylindreeGarage'])) {
			$tmp  = $garage['field']['cylindreeGarage'];	
			$json = json_decode($modele['field']['_cylindreeModele'], true);
			if (empty($json)) $json = array(); # le modele n'a pas encore de cylindrees
			
			## si la cylindree n'existe pas
			if (!in_array($tmp, $json))	{
				$_json['cylindree']['add'] = array($tmp);
			}
			$_json['id_content'] = $modele['id_content'];
			$this->modeleSet($_json); # mettre a jour le json
		}
		## hop hop, tout valider
		$b_modele['id_content'] = $modele['id_content'];
		$b_modele['valide'] 	= true;

		$b_marque['id_content']	= $marque['id_content'];
		$b_marque['valide']		= true;
		
		$b_garage['id_content'] = $garage['id_content'];
		$b_garage['valide']		= true;

		$b_modele = $this->modeleModerateSet($b_modele);
		$b_marque = $this->marqueModerateSet($b_marque);
		$b_garage = $this->garageModerateSet($b_garage);		
		
		return true;
	}		
}

## renvoie les cylindrees generales (! pas par modele)
public function cylindreeGet() {
	
	$sql 		= "SELECT * FROM `@cylindree`";
	$cylindrees = $this->dbMulti($sql);
	
	foreach ($cylindrees as $cyl) {
		$final[] = $cyl['cylindree'];		
	}
	
	return $final;
}

## ajoute une cylindr?e aux g?n?rales (! pas par mod?le)
/*
 * public function cylindreeSet($opt) {
	$cylindrees = $this->cylindreeGet();
	
	if ( is_array($opt['add']) ) {
		foreach ( $opt['add'] as $add ) {
			$cylindrees[] = $add;		
		} 
	}
	
	file_put_contents($this->file_cylindree, 
					  json_encode($cylindrees));
} */

## supprime une cylindr?e des g?n?rales (! pas par mod?le)
public function cylindreeRemove() {
	// TODO
}

}

/* = + = + = + = + MODELE SET + = + = + = + = + = +
 *
  	$optset	= array(
		'contentName' 	=> 'Garage Paul 4',
		'id_marque'	  	=> 70,
		'id_modele'		=> 72,
 		'cylindree'		=> 950, # si pas dispo, contentSee = 0
		'media'			=> '/media/ui/img/garage.gif',
		'debug'		  	=> true
	);

	$insert = $this->apiLoad('garage')->garageSet($optset);
 
  = + = + = + = + = + = + = + = + = + = + = + = + = +*/
	
		
	
/* = + = + = + = + MODELE SET + = + = + = + = + = +
 *
  	$optset	= array(
		'contentName' 	=> 'Diavel',
 		'cylindree'		=> array(
 				'add'		=> array(), #ajouter une cylindr?e
				'set' 		=> array(), #reconstruire ttes les cylindr?es
 				'remove' 	=> array()	#supprimer une cyl
					)
		'id_marque'	  	=> 60,
		'id_content'	=> 68,
		'media'			=> '/media/ui/img/diavel.jpg',
		'noVerify'		=> true, # v?rifier doublons
		'debug'		  	=> true
	);

	$insert = $this->apiLoad('garage')->modeleSet($optset);
 
  = + = + = + = + = + = + = + = + = + = + = + = + = +*/
	
/* = + = + = + = + MARQUE SET + = + = + = + = + = +
 *
	$optset	= array(
		'contentName' 	=> 'Suzuki',
		'id_content'	=> 70,
		'noVerify'		=> false, # v?rifier doublons
		'debug'		  	=> false
	);

	$insert = $this->apiLoad('garage')->marqueSet($optset);
 
  = + = + = + = + = + = + = + = + = + = + = + = + = +*/	
	
	
?>