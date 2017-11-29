<?php

	if(intval($me['id_user']) > 0){
		header("Location: /compte/");
		exit();
	}


# - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + 


	if($_GET['e'] != NULL){
		$email = base64_decode($_GET['e']);

		if(ereg('@', $email)){
		
			$str = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
			for($i=0;$i<=8; $i++){
				$password .= $str{rand(0, strlen($str))};
			}

			$user 	= $this->apiLoad('user');
			$usr 	= $this->dbOne("SELECT id_user FROM k_user WHERE userMail='".$email."'");

			if($usr['id_user'] == NULL) die("Compte inexistant : ".$email);

			$this->dbQuery("UPDATE k_user SET userPasswd=MD5('".$password."') WHERE id_user='".$usr['id_user']."'");		
		
			if($this->db_error == NULL){

				$this->userLogout();
				$this->userLogin($email, $password);
	
				$PASSWORD_RESET = true;
			}else{
				die("Echec de la mise a jour du mot de passe");
			}

		}else{
			die('email invalide : '.$email);
		}
	
	}else
	if($_POST['email'] != NULL){
		
		$exists = $this->dbOne("SELECT 1 FROM k_user WHERE userMail='".addslashes($_POST['email'])."'");

		if(filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) === FALSE){
			$NEED_VALIDE_EMAIL = true;
		}else
		if($exists[1]){

			$this->apiLoad('sendMail')->mandrill(array(
				'template' => 'user-lost-password',
				'message'  => array(
					'track_opens' => true,
					'tags'        => array('supercalendrier', 'user', 'password'),
					'to'          => array(
						array('type' => 'to', 'email' => $_POST['email'])
					),
					'global_merge_vars' => array(
						array('name' => 'lien', 'content' => 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']."?e=".base64_encode($_POST['email']))
					)
				)
			));

			$PLEASE_CHECK_INBOX = true;	
		}else{
			$USER_NOT_EXIST = true;	
		}
	}
?>