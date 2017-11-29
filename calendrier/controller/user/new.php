<?php

	if(intval($me['id_user']) > 0){
		header("Location: /compte/");
		exit();
	}

# - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + 

	# UPDATED
	#
	if(isset($_GET['created'])){
		$NEW_CONFIRMATION = true;
	}


	# CREATION
	#
	if($_POST['todo'] == 'create'){
		
		$do = true;
		if($_POST['cgu'] == '1'){

			$def['k_user'] = array(
				'is_active'		=> array('value'	=> '0'),
				'id_group'		=> array('value' 	=> '1'),
				'userPasswd'	=> array('value'	=> $_POST['userPasswd'], 	'function'	=> "MD5('".$_POST['userPasswd']."')", 'check' => '([A-Za-z0-9]){4,16}'),
				'userMail'		=> array('value' 	=> $_POST['userMail'], 		'email'		=> true)
			);
			
			if(!$this->formValidation($def)){
				$do = false;
				$NEW_ERROR_FILLED = true;
			}
	
			$check = $this->apiLoad('user')->userGet(array(
				'debug'		=> false,
				'search'	=> array(
					array('searchField' => 'userMail', 'searchMode' => 'EG', 'searchValue' => $_POST['userMail'])
				)
			));

			$update = false;
			$exists = false;

			if(sizeof($check) == 1) $exists = true;	

			if($do && $exists){	
				$do = false;
				$NEW_ERROR_EXISTS = true;
			}
	
			if($do){
				$job = $this->apiLoad('user')->userSet(array(
					'id_user'	=> NULL,
					'debug'		=> false,
					'def'		=> $def
				));

				if($job){
					$tmp = $this->apiLoad('user')->userGet(array(
						'id_user'	=> $this->apiLoad('user')->id_user
					));
	
					$sent   = $_POST['userMail'];
					$token	= $tmp['userToken'];
				}else{
					$NEW_ERROR_INSERT = true;
				}

			}else{
				$NEW_ERROR_FILLED = true;
			}
		}else{
			$NEW_ERROR_FILLED = true;
		}
	}


	# MAIL DE VERIFICATION
	#
	if(filter_var($_GET['again'], FILTER_VALIDATE_EMAIL) !== false) $sent = $_GET['again'];
	if(filter_var($sent, FILTER_VALIDATE_EMAIL) !== false){
		
		$lien = 'http://'.$_SERVER['HTTP_HOST'].'/user/allow?u='.$sent;
		if(isset($token)) $lien .= '&token='.$token;
		
		$message = $this->helperReplace(
			file_get_contents(USER.'/mail/user.check.html'),
			array(
				'userMail'	=> $sent,
				'lien'		=> $lien
			)
		);

		require_once(PLUGIN.'/phpmailer/class.phpmailer.php');
		$mail = new PHPMailer();

		$mail->SetFrom('noreply@'.$_SERVER['HTTP_HOST']);
		$mail->AddAddress($sent);

		if(filter_var($this->kodeine['configMailCc'], FILTER_VALIDATE_EMAIL) !== FALSE){
			$mail->AddCC($this->kodeine['configMailCc']);
		}

		if(filter_var($this->kodeine['configMailBcc'], FILTER_VALIDATE_EMAIL) !== FALSE){
			$mail->AddBCC($this->kodeine['configMailBcc']);
		}

		$mail->Subject = "[".$_SERVER['HTTP_HOST']."] Verification de votre compte";
		$mail->AltBody = "To view the message, please use an HTML compatible email viewer!";
		#$mail->MsgHTML(eregi_replace("[\]",'', $message)); deprecated
		$mail->MsgHTML(str_replace('\\', '', $message));

		if(!$mail->Send()) die("Mailer Error: " .$mail->ErrorInfo);
		
		if(!isset($alert)){
			$reload = 'new?created';
		}
	}


	# RELOAD
	#
	if(isset($reload)){
		
		$location = is_string($reload) ? $reload : '/user/new';
		header("Location: ".$location);
		exit(0);
	}
?>