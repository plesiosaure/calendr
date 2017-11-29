<div id="left">

	<div id="logo"><?php

		$logo = $this->apiLoad('content')->contentGet(array(
			'id_content'	=> 74,
			'contentSee'	=> 'ALL',
		));

		echo '<img />';
		echo $logo['field']['body'];

	?></div>
	
	<div class="sep"></div>
	
	<ul id="menu"><?php
		$pages = $this->apiLoad('content')->contentGet(array(
			'id_type'	=> 72,
			'order'		=> 'contentDateCreation',
			'direction'	=> 'ASC'
		));
		
		foreach($pages as $e){
			if($e['id_content'] != $content['id_content']){
				list($a, $b) = explode(',', $e['contentName']);
				echo '<li>';
				echo '<a href="/'.$e['contentUrl'].'">'.trim($a).', <i>'.trim($b).'</i></a>';
				echo '</li>';
			}
		}

	?></ul>

	<div class="sep"></div>
	
	<div id="bio"><?php
		
		$bio = $this->apiLoad('content')->contentGet(array(
			'id_content'	=> 68,
			'contentSee'	=> 'ALL',
		));
		
		$bio = $bio['field']['body'];
		$bio = str_replace('@', '\\\\\\', $bio);

		echo $bio;
	
	?></div>
	

</div>