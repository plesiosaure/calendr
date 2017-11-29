<?php

class pub extends content {

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	public function pubCalendrier($id_adzone){

		$opt = array(
			'id_type'    => 8,
			'useForum'   => false,
			'useChapter' => true,
			'useGroup'   => false,
			'adZone'     => $id_adzone,
			'debug'      => false //isset($_GET['k'])
		);

		$pubs = $this->contentGet($opt);
		if(count($pubs) == 0) return;


		$pub = $pubs[array_rand($pubs, 1)];

		if(is_array($pub)){

			/*$this->adStat(array(
				'id_content' => $pub['id_content'],
				'language'   => 'fr',
				'field'      => 'view',
				'debug'      => false
			));*/

			$img = $pub['contentMedia']['image'][0];

			if(substr(strtolower(trim($pub['contentAdCode'])), 0, 4) == '<obj'){
				$html = $pub['contentAdCode'];
			}else
			if(substr(strtolower(trim($pub['contentAdCode'])), 0, 4) == '<img'){
				$html = '<a href="/ad'.$pub['id_content'].'" target="_blank">'.$pub['contentAdCode'].'</a>';
			}else
			if($img['exists']){
				$html = '<a href="/ad'.$pub['id_content'].'" target="_blank"><img src="'.$img['url'].'" /></a>';
			}

			return $html;
		}

		return false;
	}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	public function pubFromForum($ids, $all){

		$all[] = MASTER_ID_FORUM;

		foreach($all as $n => $e){
			$all[$n] = is_array($e) ? intval($e['id_socialforum']) : intval($e);
		}

		$opt = array(
			'id_type'    => 8,
			'useForum'   => true,
			'useGroup'   => false,
			'useChapter' => false,
			'debug'      => 0 //isset($_GET['k'])
		);

		if(is_array($ids)){
			$opt['id_socialforum'] = $ids;
		}else{
			$opt['id_socialforum'] = $all;
		}

		$pubs = $this->contentGet($opt);

		if(count($pubs) == 0) return;

		$pub  = $pubs[array_rand($pubs, 1)];

		if(is_array($pub)){

			$this->adStat(array(
				'id_content' => $pub['id_content'],
				'language'   => 'fr',
				'field'      => 'view',
				'debug'      => false
			));

			if(substr(strtolower(trim($pub['contentAdCode'])), 0, 4) == '<obj'){
				$html = $pub['contentAdCode'];
			}else
			if(substr(strtolower(trim($pub['contentAdCode'])), 0, 4) == '<img'){
				$html = "<a href=\"/ad".$pub['id_content']."\" target=\"_blank\">".$pub['contentAdCode']."</a>";
			}
		}

		return $html;
	}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	public function pubFromList($str){

		// Direct l'ID de la pub
		if(strpos(',', $str) !== false){
			$id  = intval($str);
		}else{
			$ids = explode(',', $str);
			$id  = $ids[array_rand($ids, 1)];
		}

		$pub = $this->contentGet(array(
			'id_content' => $id,
			'useGroup'   => false,
			'useChapter' => false,
			'debug'      => false
		));

		if(is_array($pub)){

			$this->adStat(array(
				'id_content'    => $id,
				'language'      => 'fr',
				'field'         => 'view',
				'debug'         => false
			));

			if(substr(strtolower(trim($pub['contentAdCode'])), 0, 4) == '<obj'){
				$html = $pub['contentAdCode'];
			}else
			if(substr(strtolower(trim($pub['contentAdCode'])), 0, 4) == '<img'){
				$html = "<a href=\"/ad".$pub['id_content']."\" target=\"_blank\">".$pub['contentAdCode']."</a>";
			}
		}

		return $html;
	}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	public function adStat($opt){

		if($opt['field'] != 'view' && $opt['field'] != 'click') return false;

		$this->dbQuery(
			"INSERT INTO k_contentadstats\n".
					"(id_content, language, year, month, day, ".$opt['field'].")\n".
					"VALUES\n".
					"(".$opt['id_content'].", '".$opt['language']."', '".date("Y")."', '".date("m")."', '".date("d")."', 1)\n".
					"ON DUPLICATE KEY UPDATE ".$opt['field']."=".$opt['field']."+1"
		);

		if($opt['debug']) $this->pre($this->db_query, $this->db_error);

		$field = 'contentAdCache'.ucfirst($opt['field']);
		$this->dbQuery("UPDATE k_contentad SET ".$field."=".$field."+1 WHERE id_content=".$opt['id_content']." AND language='".$opt['language']."'");
		if($opt['debug']) $this->pre($this->db_query, $this->db_error);
	}

}