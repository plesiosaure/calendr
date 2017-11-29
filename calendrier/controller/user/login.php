<?php

	if($me['id_user'] != NULL){
		header("Location: /social/");
		exit();
	}

	if($_POST['login'] != '' && $_POST['password']){
		if(!$this->userLoginSuccess){
			header('Location: /user/login?missMatch');
			exit(0);
		}
	}
?>