<?php

	if(empty($_GET['_id'])) $this->go('/?notFound&from='.$_SERVER['REQUEST_URI']);

	$api = $this->apiLoad('calendrierManifestation');

	$ids = explode(',', $_GET['_id']);

	foreach($ids as $id){
		$myManifestation = $api->get(array(
			'_id'   => $id,
			'debug' => false
		));

		if(!empty($myManifestation)){
			$confirmed = false;

			if($myManifestation['mode'] == 'wait_email'){
				$this->apiLoad('calendrierManifestation')->manifestationEmailConfirmed($id);
				$confirmed = true;

				// Mail envoyé à LVA pour leur demander de modérer
				if($myManifestation['moderation'] == 'remove'){
					$v = 'http://'.$_SERVER['HTTP_HOST'].'/admin/calendrier/moderation/contact?_id='.$id.'&reject=1';
				}else{
					$v = 'http://'.$_SERVER['HTTP_HOST'].'/admin/calendrier/moderation/data?_id='.$id;
				}

				$m = array(
					'template' => 'lva-nouvelle-moderation',
					'message'  => array(
						'track_opens' => true,
						'tags'        => array('supercalendrier', 'organisateur'),
						'to'          => array(
							array('type' => 'to', 'email' => EMAIL_LVA)
						),
						'global_merge_vars' => array(
							array('name' => 'link', 'content' => $v)
						)
					)
				);

				$z = $this->apiLoad('sendMail')->mandrill($m);
				#var_dump($z);
			}
		}
	}

#	if(!$found) $this->go('/?notFound&from='.$_SERVER['REQUEST_URI']);


