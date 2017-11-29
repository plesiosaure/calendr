<?php

	# get DATA
	#
	$content = $this->apiLoad('content')->contentGet(array(
		'debug'			=> false,
		'cache'			=> true,
		'contentUrl'	=> $this->kodeine['get']['urlFile']
	));

	if($content['id_type'] > 0){
		$fields	= $this->apiLoad('content')->contentField;
		$type	= $this->apiLoad('type')->typeGet(array('id_type' => $content['id_type']));

		// Incremente le compteur de VIEW si la page n'a pas deja ete affichee.
		if($content['id_content'] != NULL && !@in_array($content['id_content'], $_SESSION['contentViewed'])){
			$this->apiLoad('content')->contentView($content['id_content']);
			$_SESSION['contentViewed'][] = $content['id_content'];
		}

		$myOffset	= 0;
		$myLimit	= POST_LIMIT;
		$myArgs		= array(
			'debug'			=> false,
			'withUser'		=> true,
			'id_content'	=> $content['id_content'],
			'limit'			=> $myLimit,
			'offset'		=> $myOffset
		);

		$myPost		= $this->apiLoad('socialPost')->socialPostGet($myArgs);
		$myTotal	= $this->apiLoad('socialPost')->total;
		$myRated	= $this->apiLoad('socialPost')->socialPostRateForUser($this->user['id_user']);
	}

