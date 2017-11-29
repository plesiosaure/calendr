<?php

class calendrierMvsImport extends calendrierMvs {

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public function __construct(){
		parent::__construct();
	}

	private function sql(){
		$con = $this->dbExtConnect('localhost', 'motoregister', 'aaXHTtfFURepJ2s5', 'calendrier');
		$con->set_charset("utf8");

		return $con;
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public function prepare(){

		// Coté SC
		$mon = $this->mongoAuth();
		$db  = $mon->selectDB($this->db);
		$org = $mon->selectCollection($db, 'organisateur_');
		$man = $mon->selectCollection($db, 'manifestation_');

		$org->remove(array());
		$man->remove(array());

		// Coté MVS
		$db = $this->sql();
		$this->dbQuery("UPDATE lva_cal_organisateur   SET sc_import=0", $db);
		$this->dbQuery("UPDATE lva_cal_manifestation  SET sc_import=0", $db);
		$this->dbQuery("UPDATE lva_cal_manifestation2 SET sc_import=0", $db);
		$this->dbQuery("UPDATE lva_cal_manifestation3 SET sc_import=0", $db);

		return true;
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public function organisateurImport(){

		$mon  = $this->mongoAuth();
		$db   = $mon->selectDB($this->db);
		$col  = $mon->selectCollection($db, 'organisateur_');
		$city = $this->apiLoad('calendrierCity');

		$db   = $this->sql();
		$data = $this->dbMulti("SELECT * FROM lva_cal_organisateur WHERE sc_import=0 LIMIT 100", $db);
		$done = 0;

		foreach($data as $e){

			$this->organisateurImportItem($col, array(
				'col'  => $col,
				'city' => $city,
				'item' => $e
			));

			$done++;
			$this->dbQuery("UPDATE lva_cal_organisateur SET sc_import=1 WHERE id_organisateur=".$e['id_organisateur'], $db);
		}

		return $done;
	}
}

