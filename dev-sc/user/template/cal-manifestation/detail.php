<!DOCTYPE html>
<html lang="<?php echo LOC ?>">
<head>
	<title><?php echo $myManifestation['name'] ?></title>
	<?php include(MYTHEME.'/ui/html-head.php'); ?>
</head>
<body>

<?php include(MYTHEME.'/ui/header.php'); ?>

<div id="main" class="clearfix">

	<div class="left clearfix manif-detail">

		<nav id="geo-action" class="pusher mobile">
			<div class="action">
				<a href="/" class="btn visible">Modifier la recherche</a>
			</div>
		</nav>

		<div class="mobile">
			<h1><?php echo $myManifestation['name'] ?></h1>
			<h4><?php
				if($myNearestDate) echo $api->datePeriod($myNearestDate);
				echo ' à '.$myManifestation['city']['name'];
			?></h4>
		</div>

		<div class="left tiny">
			<div class="block block-bordered toy"><?php
				if(!empty($myPoster)){

					$poster = $this->mediaUrlData(array(
						'url'   => $myPoster,
						'mode'  => 'width',
						'value' => 200
					));

					if(!empty($poster)) echo '<img '.$poster['html'].' >';

				}else{ ?>
					<img src="/media/ui/img/icons/category-large-<?php echo $myTypeImage; ?>.png">
				<?php } ?>
			</div>

			<div class="block">
				<div class="title light">Détail de la manifestation</div>

				<table border="0" cellspacing="0" cellpadding="0" width="100%" class="property alter">
					<tr>
						<td width="40%" class="label">Organisateur</td>
						<td class="value"><?php echo $myManifestation['organisateur']['name']; ?></td>
					</tr>
					<tr>
						<td width="40%" class="label">Genre</td>
						<td class="value"><?php echo $myType['name']; ?></td>
					</tr>
					<tr>
						<td class="label">Type</td>
						<td class="value"><?php echo $myCategory['name'] ?></td>
					</tr>
					<?php if($myManifestation['geo']['country'] == 'FR'){ ?>
					<tr>
						<td class="label">Région</td>
						<td class="value"><?php echo $myRegion['name']; ?></td>
					</tr>
					<tr>
						<td class="label">Département</td>
						<td class="value"><?php echo $myDepartement['name']; ?></td>
					</tr>
					<?php } ?>
					<tr>
						<td class="label">Code Postal</td>
						<td class="value"><?php echo $myManifestation['city']['zip']; ?></td>
					</tr>
					<tr>
						<td class="label">Ville</td>
						<td class="value"><?php echo $myManifestation['city']['name']; ?></td>
					</tr>
					<tr>
						<td class="label">Pays</td>
						<td class="value"><?php
							$country = $this->apiLoad('calendrierDepartement')->countryGet(array(
								'code' => ($myManifestation['geo']['country'] ?: 'FR')
							));

							echo $country['name'];
						?></td>
					</tr>
					<?php if(!empty($myManifestation['price'])){ ?>
					<tr>
						<td class="label">Tarif</td>
						<td class="value"><?php echo $myManifestation['price'] ?> €</td>
					</tr>
					<?php } if($me['id_group'] == '_____-2____'){ ?>
					<tr>
						<td class="label"></td>
						<td class="value"><a href="<?php echo $myManifestation['_id'] ?>?geofix">Fix GPS</a></td>
					</tr>
					<?php } ?>
				</table>
			</div>
		</div>

		<div class="right large">
			<h1><?php echo $myManifestation['name'] ?></h1>
			<h4><?php
				if($myNearestDate) echo $api->datePeriod($myNearestDate);
				echo ' à '.$myManifestation['city']['name'];
			?></h4>

			<div class="resume">
				<?php echo $api->manifestationResumeFull($myManifestation); ?>

				<p>Informations fournies à titre indicatif et sous toutes réserves d'erreurs, d'annulations ou
				d'omissions. Téléphonez avant de vous déplacer.</p>

				<?php if(!empty($myImages)){ ?>
				<h5>Galerie de photos</h5>
				<div class="gallery"><?php

					foreach($myImages as $e){
						$img = $this->mediaUrlData(array(
							'url'  => $e['url'],
							'mode' => 'width',
							'value' => 150,
						));

						$mode = ($e['height'] > $e['width']) ? 'H' : 'W';
						$zoom = $this->mediaUrlData(array(
							'url'   => $e['url'],
							'mode'  => (($mode == 'W') ? 'width' : 'height'),
							'value' => (($mode == 'W') ? 800 : 500),
						));

						echo '<div class="item">';
						echo '<a href="'.$zoom['img'].'" class="fancybox" rel="g"><img '.$img['html'].' ></a>';
						echo '</div>';
					}

				?></div>
				<?php } ?>

				<h5>Dates</h5>
				<?php
					if($myManifestation['periodicity'] == 2){
						echo '<p>Manifestation hebdomadaire</p>';
					}else
					if($myManifestation['periodicity'] == 3){
						echo '<p>Manifestation mensuelle</p>';
					#}else{
					#	echo '<p>Manifestation ponctuelle</p>';
					}
				?>
				<ul><?php
					foreach($myManifestation['date'] as $n => $e){
						echo '<li>';
						echo $api->datePeriod($e);
						#echo '('.date("Y-m-d H:i:s", $e['start']->sec).' // '.date("Y-m-d H:i:s", $e['end']->sec).')';
						#$this->pre($e);
						if($e['canceled'])  echo ' (annulée)';
						if($e['postponed']) echo ' (reportée)';
						if($e['unsure'])    echo ' (incertaine)';
						echo '</li>';
					}

				#	$this->pre($myManifestation['date']);

				?></ul>

			</div>
		</div>

		<?php if(!empty($myManifestation['geo']['gps'])){ ?>
		<div class="block clear">
			<h3 class="title medium">Autres manifestations à proximité</h3>
			<p>La manifestation courante est affiché en gris.</p>
			<div class="block mapped">
				<div id="map-detail" data-id="<?php echo $myManifestation['_id'] ?>"></div>
			</div>
		</div>
		<?php } ?>

	</div>

	<div class="right no-mobile">

		<div class="block block-bordered right-search">
			<div class="title medium">Vous êtes l'organisateur ?</div>
			<p>Si vous êtes l'organisateur de cette manifestation, vous pouvez
			<a href="/organisateur/edit?id=<?php echo $myManifestation['_id'] ?>">modifier</a>
			les informations ou demander une
			<a href="/organisateur/remove?id=<?php echo $myManifestation['_id'] ?>">suppression</a>.</p>

			<p>Rendez-vous dans votre espace organisateur pour ajouter de
			<a href="/organisateur/create">nouvelles manifestations</a></p>
		</div>

		<?php

		include(MYTHEME.'/ui/right/search.php');
		include(MYTHEME.'/ui/right/ad.php');
		include(MYTHEME.'/ui/right/actu.php');

	?></div>

</div>

<?php
	include(MYTHEME.'/ui/footer.php');
	include(MYTHEME.'/ui/html-end.php');

	if($_SERVER['HTTP_HOST'] == 'dev.supercalendrier.com'){
		#$this->pre($myManifestation);
	}
	echo $this->apiLoad('calendrier')->googleMapJS();
?>

<script src="/media/ui/js/manifestation-detail.js"></script>
<script src="/media/ui/vendor/fancybox/source/jquery.fancybox.pack.js"></script>

</body></html>