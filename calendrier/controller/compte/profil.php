<?php

	include(__DIR__.'/_check.php');

	// CHANGE AVATAR ///////////////////////////////////////////////////////////////////////////////////////////////////

	if($_FILES['Filedata']['tmp_name'] != ''){

		ini_set('upload_max_filesize',	'10M');
		ini_set('post_max_size',		'10M');
		ini_set('max_execution_time',	'100');
		ini_set('max_input_time',		'100');

		$ext	= pathinfo($_FILES['Filedata']['name'], PATHINFO_EXTENSION);
		$final	= MEDIA.'/upload/user/'.uniqid('up_').'.'.$ext;
		move_uploaded_file($_FILES['Filedata']['tmp_name'], $final);

		$tool	= $this->apiLoad('alphaNum');
		$alpha	= strtolower($tool->alphaID($me['id_user'], false, 8));
		$tmp	= implode('', array_reverse(str_split($alpha, 1)));
		$final_	= dirname($final).'/'.implode('/', str_split($tmp, 1)).'/'.md5(uniqid('f')).'.'.$ext;

		if(!file_exists(dirname($final_))) mkdir(dirname($final_), 0755, true);
		rename($final, $final_);

		$this->apiLoad('user')->userMediaLink(array(
			'debug'   => false,
			'id_user' => $this->user['id_user'],
			'onlyMe'  => true,
			'type'    => '',
			'url'     => str_replace(KROOT, '', $final_)
		));

		$reload = true;
	}

	// MISE A JOUR DU COMPTE ///////////////////////////////////////////////////////////////////////////////////////////

	if($_POST['update'] == 'YES'){

		$job = $this->apiLoad('user')->userSet(array(
			'debug'		=> false,
			'id_user'	=> $me['id_user'],
			'field'		=> $_POST['field'],
			'def'		=> array('k_user' => array(
				'userDateUpdate' 	=> array('function' => 'NOW()')
			))
		));

		$this->apiLoad('social')->socialUserCacheClean($me['id_user']);

		if($job){
			$this->go('profil?done');
		}else{
			$alert	= array('view' => true, 'class' => 'alert-error', 'text' => COMPTE_EDIT_ERROR);
		}
	}


