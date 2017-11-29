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

		$this->apiLoad('sendMail')->mandrill(array(
			'template' => 'user-check',
			'message'  => array(
				'track_opens' => true,
				'tags'        => array('supercalendrier', 'user', 'password'),
				'to'          => array(
					array('type' => 'to', 'email' => $_POST['email'])
				),
				'global_merge_vars' => array(
					array('name' => 'user_mail', 'content' => $sent),
					array('name' => 'lien',      'content' => $lien)
				)
			)
		));

		$message = $this->helperReplace(
			file_get_contents(USER.'/mail/user.check.html'),
			array(
				'userMail'	=> $sent,
				'lien'		=> $lien
			)
		);

		if(!isset($alert)) $reload = 'new?created';
	}


	# RELOAD
	#
	if(isset($reload)){
		
		$location = is_string($reload) ? $reload : '/user/new';
		header("Location: ".$location);
		exit(0);
	}
?>