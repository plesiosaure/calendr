<div class="block block-bordered">

	<div class="title medium"><a href="/actu">Actu des manifestations</a></div>

	<?php

	$actu = $this->apiLoad('content')->contentGet(array(
		'debug'      => false,
		'id_type'    => TYPE_ACTU,
		'id_chapter' => CHAPTER_ID,
		'order'      => 'contentDateCreation',
		'direction'  => 'DESC',
		'limit'      => 2
	));

	foreach($actu as $e){
		echo '<div class="item clearfix">';
		echo '<div class="title"><a href="/'.$e['contentUrl'].'">'.$e['contentName'].'</a></div>';
		echo '<div class="resume justify">'.$e['field']['actuResume'].'</div>';
	#	echo '<a href="/'.$e['contentUrl'].'" class="btn btn-mini readmore right">Lire l\'article</a>';
		echo '</div>';
	}

#	echo '<br /><br />';
#	echo '<div><a href="/actu" class="btn btm-medium">Toute l\'actualité</a></div>';

	unset($actu, $e);

?></div>