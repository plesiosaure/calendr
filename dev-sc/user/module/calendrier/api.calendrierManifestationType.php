<?php

class calendrierManifestationType extends calendrierManifestation {

	protected $type = array();
	protected $name = array();

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public function __construct(){

		$this->name = array(
			'auto'       => array('name' => 'Auto',         'id' => 2),
			'moto'       => array('name' => 'Moto',         'id' => 3),
			'collection' => array('name' => 'Collection',   'id' => 1)
		);

		$this->type = array(
			'auto'  => array(
				'balade'        => array('id' => 200, 'name' => 'Balade, Randonnée, Sortie, Rallye, Ronde, Promenade', 'short' => 'Balade'),
				'bourse'        => array('id' => 210, 'name' => 'Bourse, Brocante, Puces, Vide-greniers, Foire', 'short' => 'Bourse'),
				'course'        => array('id' => 220, 'name' => 'Course (circuit), Grand Prix, V.H.C., Raid', 'short' => 'Course'),
				'exposition'    => array('id' => 230, 'name' => 'Exposition'),
				'rassemblement' => array('id' => 240, 'name' => 'Rassemblement, Concentration, Démonstration, Parade', 'short' => 'Rassemblement'),
				'salon'         => array('id' => 250, 'name' => 'Salon'),
				'enchere'       => array('id' => 260, 'name' => 'Vente (aux enchères)'),
				'assemblee'     => array('id' => 270, 'name' => 'Assemblée générale Club'),
			),
			'moto' => array(
				'balade'        => array('id' => 400, 'name' => 'Balade, Randonnée, Sortie'),
				'bourse'        => array('id' => 410, 'name' => 'Bourse'),
				'course'        => array('id' => 420, 'name' => 'Course (circuit), Grand Prix, Rallye', 'short' => 'Course'),
				'exposition'    => array('id' => 430, 'name' => 'Exposition'),
				'rassemblement' => array('id' => 440, 'name' => 'Rassemblement, concentration'),
				'salon'         => array('id' => 450, 'name' => 'Salon'),
				'enchere'       => array('id' => 460, 'name' => 'Vente (aux enchères)')
			),
			'collection'  => array(
				'brocante'      => array('id' => 10,  'name' => 'Antiquités-brocante'),
				'braderie'      => array('id' => 20,  'name' => 'Vide-greniers - braderie'),
				'videgrenier'   => array('id' => 30,  'name' => 'Brocante et vide-greniers'),
				'multi'         => array('id' => 40,  'name' => 'Salon ou bourse multi collection'),
				'special'       => array('id' => 50,  'name' => 'Salon ou bourse spécialisé (collection)', 'short' => 'Salon ou bourse spécialisé'),
				'jouet'         => array('id' => 60,  'name' => 'Salon ou bourse de jouets'),
				'exposition'    => array('id' => 70,  'name' => 'Exposition'),
				'enchere'       => array('id' => 80,  'name' => 'Ventes aux enchères'),
				'collections'   => array('id' => 90,  'name' => 'Brocante et collections')
			)
		);

	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public function build(){
		$file = __DIR__.'/ui/js/mvs.js';

		$all['t'] = $this->name;
		$all['c'] = $this->type;

		$json = $this->helperJsonEncode($all);
		$json = 'var mvs = '.$json;

		echo $this->helperJsonBeautifier($json);

		umask(0);
		if(file_exists($file)) unlink($file);
		return file_put_contents($file, $json, 0755);
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public function name($type=NULL){
		if($type == NULL) return $this->name;
		return $this->name[$type];
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public function nameFromId($id){
		$id = intval($id);

		foreach($this->name as $k => $e){
			if($e['id'] == $id) return array_merge($e, array('key' => $k));
		}
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public function nameFromSubId($id){
		foreach($this->type as $type => $es){
			foreach($es as $sub => $e){
				if($e['id'] == $id) return array_merge($e, array('key' => $type, 'sub' => $sub));
			}
		}
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public function typeFromId($id){
		foreach($this->type as $es){
			foreach($es as $k => $e){
				if($e['id'] == $id) return array_merge($e, array('key' => $k));
			}
		}
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public function get($k=NULL){

		if($k == NULL){
			return $this->type;
		}else
		if(strpos('.', $k) !== false){
			list($k, $v) = explode('.', $k);
			return $this->type[$k][$v];
		}else{
			return $this->type[$k];
		}
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public function categoryFromId($category){
		foreach($this->type as $type => $es){
			foreach($es as $sub => $e){
				if($e['id'] == $category) return $e;
			}
		}
	}
}