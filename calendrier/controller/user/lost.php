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

			$message = $this->helperReplace(
				file_get_contents(USER.'/mail/user.lost.html'),
				array(
					'email'	=> $_POST['email'],
					'link'	=> 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']."?e=".base64_encode($_POST['email'])
				)
			);

			require_once(KROOT.'/app/plugin/phpmailer/class.phpmailer.php');
			$mail = new PHPMailer();

			$mail->SetFrom("noreply@".$_SERVER['HTTP_HOST']);
			$mail->AddAddress($_POST['email']);

			$mail->Subject	= "Nouveau mot de passe";
			$mail->AltBody	= "Vous devez utiliser un client compatible html pour lire ce message";
			//$body = eregi_replace("[\]",'',$message);
			$body = str_replace('\\', '', $message);
			$mail->MsgHTML($body);

			if(!$mail->Send()) $FATAL_ERROR = $mail->ErrorInfo;

			$PLEASE_CHECK_INBOX = true;	
		}else{
			$USER_NOT_EXIST = true;	
		}
	}
?>