<?php

	$bloc = $this->apiLoad('content')->contentGet(array(
		'id_content' => 60112,
	));

?>
<div class="block block-bordered">
	<div class="title medium"><?php echo $bloc['field']['titreAlt'] ?: $bloc['contentName'] ?></div>
	<?php echo $bloc['field']['_description']; editBloc($bloc); ?>
</div>

<?php unset($bloc); ?>
