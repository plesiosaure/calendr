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
		<div class="title-gradient no-mobile">
			<h1>Recherche de manifestations</h1>
		</div>

		<div class="result-description no-mobile"><?php
			echo $api->manifestationSearchSEO($myTotal, $_GET);
		?></div>

		<nav id="geo-action" class="mobile">
			<div class="action">
				<a href="/" class="btn visible">Modifier la recherche</a>
			</div>
		</nav>

		<?php if(count($myData) > 0){ ?>

			<div id="manif-result"><?php

			foreach($myData as $e){

				if($e['mvs']['type'] == 1){
					$myType = 'collection';
				}else
				if($e['mvs']['type'] == 2){
					$myType = 'auto';
				}else
				if($e['mvs']['type'] == 3){
					$myType = 'moto';
				}

				include TEMPLATE.'/cal-manifestation/search.php';

			} ?></div>

			<?php
				if($myTotal > $myLimit){
					echo $this->apiLoad('calendrierHelper')->pagination(array(
						'total'   => $myTotal,
						'limit'   => $myLimit,
						'offset'  => $myOffset,
						'pattern' => $myPattern,
						'size'    => 4
					));
				}
			}else{ ?>
				<div class="no-result no-mobile">
					Nous n'avons pas trouvé d'événement avec vos critères de recherche.<br>
					Elargissez votre recherche en modifiant vos critères dans la colonne de droite
				</div>

				<div class="no-result mobile">
					Nous n'avons pas trouvé d'événement avec vos critères de recherche.
				</div>
		<? } ?>

	</div>

	<div class="right no-mobile"><?php
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