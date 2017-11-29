<?php

	include(__DIR__.'/helper/build.php');

	# Url definition
	#
	$SHOW_404	= false;
	$controller	= '/{T}/controller/{m}/{F}';
	$view		= '/{T}/view/{m}/{F}';

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	# Rewriter les URL si on n'est pas en mode restrictif
	#
	$restriction = false;
	if(file_exists($this->kTalk('{R}'.$controller))){
		$restriction = true;
	}else
	if(file_exists($this->kTalk('{R}'.$view))){
		$restriction = true;
	}

	if(!$restriction){ //&& $this->kodeine['get']['urlFile'] != ''){
		include(dirname(__FILE__).'/helper/url.php');
	}


	# configGet (bootExt) - decommenter si vous avez besoin des bootExt
	#
	#$this->kodeine['bootExt'] = $this->configGet('bootExt');



	# Je demande le controller du theme (CONTROLLER)
	#
	if(file_exists($this->kTalk('{R}'.$controller))){
		include($this->kTalk('{R}'.$controller));

		// Je demande la vue si elle existe
		if(file_exists($this->kTalk('{R}'.$view))) include($this->kTalk('{R}'.$view));
	}else

	# Je demande la vue si elle existe
	#
	if(file_exists($this->kTalk('{R}'.$view))){
		include($this->kTalk('{R}'.$view));
	}



	# Si non c'est du 404
	#
	else{
		include(dirname(__FILE__).'/helper/404.php');
	}


	# Si depuis une fichier je demande la 404
	#
	if($SHOW_404){
		include(dirname(__FILE__).'/helper/404.php');
	}

?>