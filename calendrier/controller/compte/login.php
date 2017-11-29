<?php

	include(__DIR__.'/_check.php');

	// SEND email (opt-in) ////////////////////////////////////////////////////////////////////////////////////////////:

	if($_POST['update'] == 'YES'){

		$_POST['new'] = trim($_POST['new']);
		$_POST['con'] = trim($_POST['con']);

		if(filter_var($_POST['new'], FILTER_VALIDATE_EMAIL) !== false){

			$this->dbQuery("
				INSERT INTO `@emailupdate`
				(id_user, updateTTL, emailOld, emailNew)
				VALUES
				('".$me['id_user']."', '".(time() + (24*60*60))."', '".$userFull['userMail']."', '".$_POST['new']."')
			");

			$this->apiLoad('sendMail')->send(array(
				'to'       => $_POST['new'],
				'title'    => $_SERVER['HTTP_HOST'] . ' changement d\'identifiant',
				'template' => 'change-email-calendrier.html',
				'body'     => array(
					'current'	=> $me['userMail'],
					'new'		=> $_POST['new'],
					'lien'		=> 'http://'.$_SERVER['HTTP_HOST'].'/compte/login?auth='.$this->db_insert_id
				)
			));

			$this->go('login?sent');
		}else{
			$this->go('login?format');
		}

	}else

	// CHANGE email from email previously sent /////////////////////////////////////////////////////////////////////////

	if(intval($_GET['auth']) > 0){
		$auth = $this->dbOne("SELECT * FROM `@emailupdate` WHERE id_emailupdate=".intval($_GET['auth']));

		if(intval($auth['id_emailupdate']) > 0){
			if(intval($auth['updateTTL']) > time()){
				$this->dbQuery("UPDATE k_user SET userMail='".$auth['emailNew']."' WHERE id_user=".$auth['id_user']);
				$this->dbQuery("DELETE FROM `@emailupdate` WHERE id_emailupdate=".intval($_GET['auth']));

				$this->go('login?done');
			}else{
				$this->go('login?outdated');
			}
		}else{
			$this->go('login?outdated');
		}
	}

