<!DOCTYPE html>
<html lang="<?php echo LOC ?>">
<head>
	<title></title>
	<meta name="description" content="<?php echo HTML_DESCRIPTION ?>" />
	<meta name="keywords" content="<?php echo HTML_KEYWORDS ?>" />
	<?php include(MYTHEME.'/ui/html-head.php'); ?>
</head>
<body>

<?php include(MYTHEME.'/ui/header.php'); ?>

<div id="main" class="clearfix">

	<?php #include(MYTHEME.'/ui/carou.php'); ?>

	<div class="left clearfix">
		<div class="title-gradient">
			<h1>Recherche de manifestations</h1>
		</div>

		<?php

			if(count($myData) > 0){

				foreach($myData as $e){
					include TEMPLATE.'/cal-manifestation/search.php';
				}

				if($myTotal > $myLimit){
					echo $this->apiLoad('calendrierHelper')->pagination(array(
						'total'     => $myTotal,
						'limit'     => $myLimit,
						'offset'    => $myOffset,
						'pattern'   => $myPattern,
						'size'      => 8
					));
				}

			}else{
				echo "No result";
			}

	?></div>

	<div class="right"><?php
		include(dirname(dirname(__DIR__)).'/ui/right/search.php');
		include(dirname(dirname(__DIR__)).'/ui/right/ad.php');
		include(dirname(dirname(__DIR__)).'/ui/right/actu.php');
	?></div>

</div>

<?php
	include(MYTHEME.'/ui/footer.php');
	include(MYTHEME.'/ui/html-end.php');
?>

</body></html>