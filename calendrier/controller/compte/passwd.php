<?php

	include(__DIR__.'/_check.php');


	// PASSWORD UPDATE /////////////////////////////////////////////////////////////////////////////////////////////////

	if($_POST['update'] == 'YES'){

		if(trim($_POST['new']) != '' && $_POST['new'] == $_POST['con']){

			$def['k_user'] = array(
				'userDateUpdate' => array('function' => 'NOW()'),
				'userPasswd'	 => array('value' => $_POST['new'], 'function' => "MD5('".$_POST['new']."')", 'check' => '([A-Za-z0-9]){4,16}'),
			);

			if($this->formValidation($def)){

				$job = $this->apiLoad('user')->userSet(array(
					'debug'   => false,
					'id_user' => $me['id_user'],
					'def'     => $def,
				));

				$this->go('passwd?done');
			}
		}

		$this->go('passwd?failed');
	}


