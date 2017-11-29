<?php

class hook extends social {

function __clone(){}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
// Supprimer un GARAGE
// + Avec l'option [kill] le garage est vraiment supprimé et pas jsute masqué
// + Supprimer les MEDIA liés opt[removeFile]
// + Supprimer les POST liés
// + Si un album exist, détaché l'album du GARAGE
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
public  function garageHide($opt){

		$id_content = $opt['id_content'];

		$content = $this->apiLoad('garage')->garageGet(array(
			'debug'      => false,
			'useChapter' => false,
			'useGroup'   => false,
			'contentSee' => 'ALL',
			'id_content' => $id_content
		));

		if($content['id_content'] == NULL) return false;

		if($opt['removeFile']){
			foreach($content['contentMedia'] as $type => $es){
				foreach($es as $e){
					$url = KROOT.$e['url'];
					if(file_exists($url) && is_file($url)) unlink($url);
				}
			}
		}

		/////////////

		$albums = $this->apiLoad('content')->contentGet(array(
			'raw'           => true,
			'id_type'		=> TYPE_ALBUM,
			'is_album'		=> true,
			'noLimit'		=> 1,
			'search'		=> array(
				array('searchField'	=> 'albumGarage', 'searchValue' => $id_content, 'searchMode' => 'EG')
			)
		));

		foreach($albums as $e){
			$this->apiLoad('content')->contentSet(array(
				'debug'     => false,
				'is_album'  => true,
				'id_content'=> $e['id_content'],
				'id_type'   => TYPE_ALBUM,
				'language'  => $e['language'],
				'def'		=> array('k_content' => array(
					'is_album' => array('value' => 1),
				)),
				'field'     => array(
					'albumGarage' => ''
				)
			));
		}

		/////////////

		$post = $this->apiLoad('socialPost')->socialPostGet(array(
			'id_content' => $id_content
		));

		foreach ($post as $p) {
			$this->apiLoad('socialPost')->socialPostHide(array(
				'id_socialpost' => $p['id_socialpost']
			));
		}

	#	$this->apiLoad('content')->contentRemove($content['id_type'], $content['id_content'], $content['language']);


		$this->apiLoad('content')->contentSet(array(
			'debug'      => false,
			'id_content' => $content['id_content'],
			'id_type'    => $content['id_type'],
			'language'   => $content['language'],
			'def'        => array('k_content' => array(
				'contentSee' => array('value' => 2)
			)),
		));

		if($opt['kill'] === true){
			echo '@';
			$this->apiLoad('content')->contentRemove($content['id_type'], $content['id_content'], $content['language']);
		}

		return true;
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
// Supprime un ALBUM
// + tous les POST/ACTIVITY lié
// + tous les ITEMS liés
// + tous les POST/ACTIVITY liés aux ITEMS
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public  function albumHide($opt){

		$album = $this->apiLoad('content')->contentGet(array(
			'id_content'    => $opt['id_content'],
			'raw'           => true
		));

		if($album['id_content'] == NULL) return false;

		// POST sur ALBUM
		$posts = $this->apiLoad('socialPost')->socialPostGet(array(
			'id_content' 	=> $album['id_content'],
			'noLimit'		=> true
		));

		foreach($posts as $p){
			$this->apiLoad('socialPost')->socialPostHide(array(
				'id_socialpost' => $p['id_socialpost']
			));
		}

		// Remove Links
		$this->dbQuery("DELETE FROM `@albumcircle` WHERE id_content=".$album['id_content']);
		$this->dbQuery("DELETE FROM `@albumuser`   WHERE id_content=".$album['id_content']);

		// Masquer l'ALBUM
		$this->apiLoad('content')->contentSet(array(
			'debug'      => false,
			'is_album'   => true,
			'id_content' => $album['id_content'],
			'id_type'    => $album['id_type'],
			'language'   => $album['language'],
			'def'        => array('k_content' => array(
				'is_album'   => array('value' => 1),
				'contentSee' => array('value' => 2)
			)),
		));

		/////////////

		$items = $this->apiLoad('content')->contentGet(array(
			'is_item'  => true,
			'id_type'  => $album['id_type'],
			'id_album' => $album['id_content'],
			'raw'      => true
		));

		// items + post sur items
		foreach($items as $e){
			$this->albumItemHide(array('id_content' => $e['id_content']));
		}

		return true;
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
// Supprimer un ITEM
// + Tous les POST/ACTIVITY de cet ITEM
// + Tous les POST/ACTIVITY lié a l'ALBUM qui font référence à ITEM
// + Supprimer les MEDIA liés opt[removeFile]
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public  function albumItemHide($opt){

		$id_item = $opt['id_content'];

		// Trouver l'item
		$item = $this->apiLoad('content')->contentGet(array(
			'id_content'	=> $id_item,
			'is_item'		=> true,
			'raw'           => true,
			'debug'			=> false
		));

		if($item['id_content'] == NULL) return false;

		// Pour tous les POST/ACTIVITY de l'ALBUM...
		$posts = $this->apiLoad('socialPost')->socialPostGet(array(
			'id_content' 	=> $item['id_album'],
			'is_activity'   => true,
			'noLimit'		=> true,
			'debug'         => false
		));

		foreach($posts as $p){
			$del = false;

			// ... on cherche si on parle de ID_ITEM
			if(is_array($p['socialPostDataVal'])){
				if(in_array($id_item, $p['socialPostDataVal'])) $del = true;
			}else
			if($p['socialPostDataVal'] == $id_item){
				$del = true;
			}

			// Si j'en trouve UN seul, on HIDE le POST qui en parle
			if($del) $this->apiLoad('socialPost')->socialPostHide(array(
				'id_socialpost' => $p['id_socialpost']
			));
		}

		/////////////

		// Tous les POST directement lié à l'ITEM, sont SUPPRIMES
		$posts = $this->apiLoad('socialPost')->socialPostGet(array(
			'id_content' => $id_item,
			'noLimit'	 => true
		));

		foreach($posts as $p){
			$this->apiLoad('socialPost')->socialPostHide(array(
				'id_socialpost' => $p['id_socialpost']
			));
		}

		// Masquer l'ITEM
		$this->apiLoad('content')->contentSet(array(
			'debug'      => false,
			'is_item'    => true,
			'id_content' => $item['id_content'],
			'id_type'    => $item['id_type'],
			'language'   => $item['language'],
			'def'        => array('k_content' => array(
				'is_item'    => array('value' => 1),
				'contentSee' => array('value' => 2)
			)),
		));

		// On Supprime le MEDIA
		if($opt['removeFile']){
			$media = KROOT.$item['contentItemUrl'];
			if(file_exists($media) && is_file($media)) unlink($media);
		}

		return true;
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
// Masque un EVENT
// + Supprimer les MEDIA liés opt[removeFile]
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public  function socialEventHide($opt){

		$id_socialevent = $opt['id_socialevent'];

		$event = $this->apiLoad('socialEvent')->socialEventGet(array(
			'id_socialevent' => $id_socialevent
		));

		if($event['id_socialevent'] == NULL) return false;

		if($opt['removeFile']){
			$media = json_decode($event['socialEventMedia'], true);
			foreach($media as $e){
				$url = KROOT.$e['url'];
				if(file_exists($url) && is_file($url)) unlink($url);
			}
		}

		$this->apiLoad('socialEvent')->socialEventHide(array(
			'id_socialevent' => $id_socialevent
		));

		return true;
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
// Masque un CIRCLE
// + Supprimer les MEDIA liés opt[removeFile]
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public  function socialCircleHide($opt){

		$id_circle = $opt['id_socialcircle'];
		$circle = $this->apiLoad('socialCircle')->socialCircleGet(array(
			'id_socialcircle' => $id_circle
		));

		if($circle['id_socialcircle'] == NULL) return false;

		if($opt['removeFile']){
			$media = json_decode($circle['socialCircleMedia'], true);
			if(is_array($media) && count($media) > 0){
				foreach($media as $e){
					$url = KROOT.$e['url'];
					if(file_exists($url) && is_file($url)) unlink($url);
				}
			}
		}

		$this->apiLoad('socialCircle')->socialCircleHide(array(
			'id_socialcircle' => $_GET['removeCircle']
		));
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public  function socialActivitySet($id){
		$field	= (MODE == 'CAR') ? 'field55' : 'field54';
		$this->dbQuery("UPDATE k_socialactivity SET ".$field."=1 WHERE id_socialactivity=".$id);
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public  function socialCleanUser($opt){

		$id_user = $opt['id_user']; // Verifié a ce stade

		// ALBUM //////////////////////////////////////////////////////////////////
		$albums = $this->apiLoad('content')->contentGet(array(
			'debug'      => false,
			'useGroup'   => false,
			'useChapter' => false,
			'id_type'    => 2,
			'is_album'   => true,
			'id_user'    => $id_user
		));

		if($opt['trace']) echo "ABLUM: ".count($albums)."\n";
		foreach($albums as $e){
			if($opt['trace']) echo "- REMOVE ".$e['id_content']."\n";
			$this->apiLoad('hook')->albumHide(array(
				'id_content' => $e['id_content']
			));
		}
		if($opt['trace']) echo "\n";

		// GARAGE//////////////////////////////////////////////////////////////////
		$garages = $this->apiLoad('content')->contentGet(array(
			'debug'     => false,
			'useGroup'  => false,
			'useChapter'=> false,
			'id_type'   => 6,
			'id_user'   => $id_user
		));

		if($opt['trace']) echo "GARAGE: ".count($garages)."\n";
		foreach($garages as $e){
			if($opt['trace']) echo "- REMOVE ".$e['id_content']."\n";
			$this->apiLoad('hook')->garageHide(array(
				'id_content' => $e['id_content']
			));
		}
		if($opt['trace']) echo "\n";

	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public  function socialSandboxRoot($type, $id){

		# POST
		#
		if($type == 'id_socialpost'){
			$root = $this->apiLoad('socialPost')->socialPostGet(array('id_socialpost' => $id));
			#$this->pre("ROOT", $root);

			// Je suis une reponse, pas le master du thread
			if($root['id_socialpost'] != $root['id_socialpostthread']){
				$root = $this->apiLoad('socialPost')->socialPostGet(array('id_socialpost' => $root['id_socialpostthread']));
				#$this->pre("THREAD", $root);
				$id	  = $root['id_socialpost'];
			}

			// Je suis relie a du contenu
			if($root['id_content'] != NULL){
				$root = $this->apiLoad('content')->contentGet(array('id_content' => $root['id_content'], 'raw' => true, 'useField' => true,));
				#$this->pre("CONTENT", $root);
				$type = 'content:'.$root['id_type'];
				$id	  = $root['id_content'];
			}

			// Trouver l'album
			if($root['is_item']){
				$root = $this->apiLoad('content')->contentGet(array('id_content' => $root['id_album'], 'raw' => true, 'useField' => true, 'is_album' => true));
				#$this->pre("ALBUM 1", $root);
				$type = 'item';
				$id	  = $root['id_content'];
			}

			// Exist'il un garage relie ?
			if($root['is_album'] && $root['field']['albumGarage'] > 0){
				$root = $this->apiLoad('content')->contentGet(array('id_content' => $root['field']['albumGarage'], 'raw' => true));
				#$this->pre("MOTO", $root);
				$type = 'content:'.$root['id_type'];
				$id	  = $root['id_content'];
			}

			# Si je suis la c'est que type=id_socialpost = JE N'AI ENCORE RIEN TROUVE (peut petre circle ou event)
			if($type == 'id_socialpost'){

				// Cercle ?
				$tmp = $this->dbOne("SELECT id_socialcircle FROM k_socialpostcircle WHERE id_socialpost='".$root['id_socialpost']."'");
				if($tmp['id_socialcircle'] > 0){
					$root = $this->apiLoad('socialCircle')->socialCircleGet(array('id_socialcircle' => $tmp['id_socialcircle']));
					#$this->pre("CIRCLE", $root);
					$type = 'id_socialcircle';
					$id   = $root['id_socialcircle'];
				}

				// Event ?
				$tmp = $this->dbOne("SELECT id_socialevent FROM k_socialpostevent WHERE id_socialpost='".$root['id_socialpost']."'");
				if($tmp['id_socialevent'] > 0){
					$root = $this->apiLoad('socialEvent')->socialEventGet(array('id_socialevent' => $tmp['id_socialevent']));
					#$this->pre("EVENT", $root);
					$type = 'id_socialevent';
					$id   = $root['id_socialevent'];
				}
			}
		}

		return array('type' => $type, 'id' => $id);

	}

}