<!DOCTYPE html>
<html lang="<?php echo LOC ?>">
<head>
	<title><?php echo $myManifestation['name'] ?></title>
	<meta name="description" content="<?php echo HTML_DESCRIPTION ?>" />
	<meta name="keywords" content="<?php echo HTML_KEYWORDS ?>" />
	<?php include(MYTHEME.'/ui/html-head.php'); ?>
</head>
<body>
<?php include(MYTHEME.'/ui/header.php'); ?>

<div id="main" class="clearfix">

	<div class="left clearfix">
		<div class="title-gradient">
			<h1>Activité du site</h1>
		</div>

		<?php

		$template = 'cal-default';

		$actu = $this->apiLoad('content')->contentGet(array(
			'debug'      => false,
			'id_type'    => TYPE_ACTU,
			'id_chapter' => CHAPTER_ID,
			'order'      => 'contentDateCreation',
			'direction'  => 'DESC',
			'noLimit'    => true
		));

		foreach($actu as $e){
			$tpl = ($e['contentTemplate'] != '') ? $e['contentTemplate'] : $template;

			$content = &$e;

			include(TEMPLATE.'/'.$tpl.'/index.php');
		}

		?>


	</div>

	<div class="right"><?php
		include(MYTHEME.'/ui/right/search.php');
		include(MYTHEME.'/ui/right/ad.php');
		include(MYTHEME.'/ui/right/actu.php');
	?></div>

</div>

<?php
	include(MYTHEME.'/ui/footer.php');
	include(MYTHEME.'/ui/html-end.php');
?>

</body></html>
