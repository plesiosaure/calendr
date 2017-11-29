<?php

	if(isset($this->kodeine['id_content'])){
		$content = $this->apiLoad('content')->contentGet(array(
			'id_content'	=> $this->kodeine['id_content']
		));
	}

	// => ???
	if($content['id_type'] != NULL){

		$type = $this->apiLoad('type')->typeGet(array(
			'id_type'	=> $content['id_type']
		));

		if($content['contentTemplate'] != NULL){
			$template = $content['contentTemplate'];
		}else{
			$template = 'cal-default';
		}

		$template = TEMPLATE.'/'.$template.'/detail.php';

		if(file_exists($template)){
			include($template);
		}else{
			$SHOW_404 = true;
		}

	}else{
		$SHOW_404 = true;
	}


