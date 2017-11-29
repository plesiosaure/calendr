<?php

	if(intval($this->user['id_user']) > 0){
		header("Location: /compte/");
		exit();
	}


# - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + 


	# ALLOW
	#
	if(filter_var($_GET['u'], FILTER_VALIDATE_EMAIL) !== false){
		$success	= false;
		$user		= $this->dbOne("SELECT * FROM k_user WHERE userMail='".$_GET['u']."'");
		
		if(intval($user['id_user']) > 0){
			$success = true;

			$job = $this->apiLoad('user')->userSet(array(
				'id_user'	=> $user['id_user'],
				'debug'		=> false,
				'def'		=> array('k_user' => array(
					'is_active'		=> array('value' => '1'),
					'is_deleted'	=> array('value' => '0'),
					'is_trusted'	=> array('value' => '1'),
				))
			));
			
			$this->apiLoad('socialTemplate')->userStatsSet();
		}
	}

	if($success && isset($_GET['token'])){

		if($user['userToken'] === $_GET['token']){
			$_SESSION['id_user'] = $user['id_user'];
			$this->userLogin();
			header("Location: /compte/complete?fromNew");
		}else{
			header("Location: /");
		}

		exit();
	}

?>