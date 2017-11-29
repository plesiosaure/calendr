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
			<h1><?php echo $content['contentName'] ?></h1>
		</div>

		<div class"resume"><?php
			echo $content['field']['actuBody'];
		?></div>

		<div class="img-body"><?php

			if (!empty($content['contentMedia'])) {
				foreach ($content['contentMedia']['image'] as $media) {

					if ($media['exists'] == 1) {
						$url = $this->mediaUrlData(array(
							'url' 	=> $media['url'],
							'mode'	=> 'width',
							'value'	=> 600
						));
						echo '<div><img src="'.$url['img'].'" style="margin-left:100px;" /></div>';
					}

				}
			}

		?></div>

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
