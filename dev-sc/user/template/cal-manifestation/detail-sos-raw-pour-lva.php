<!DOCTYPE html>
<html lang="<?php echo LOC ?>">
<head>
	<title><?php echo $myManifestation['name'] ?></title>
	<?php include(MYTHEME.'/ui/html-head.php'); ?>
</head>
<body>

<?php include(MYTHEME.'/ui/header.php'); ?>

<div id="main" class="clearfix">

	<?php #include(MYTHEME.'/ui/carou.php'); ?>

	<div class="left clearfix manif-detail">

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
					<?php } if($me['id_group'] == -2){ ?>
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
				echo ' à ';
				echo $myManifestation['city']['name'];
			?></h4>

			<div class="resume"><?php

				echo $api->manifestationResumeFull($myManifestation);

			?>

<?php /*if($_SERVER['HTTP_HOST'] == 'dev.supercalendrier.com'){ ?>
<pre style="text-align: left;">
email:      <?php echo $myManifestation['email'] ?: 'NC' ?>

téléphone:  <?php echo $myManifestation['phone'] ?: 'NC' ?>

fax:        <?php echo $myManifestation['fax'] ?: 'NC' ?>

mobile:     <?php echo $myManifestation['mobile'] ?: 'NC' ?>

web:        <?php echo $myManifestation['web'] ?: 'NC' ?>

gratuit:    <?php echo $myManifestation['free'] ?: 'NC' ?>

payant:     <?php echo $myManifestation['paying'] ?: 'NC' ?>

prix:       <?php echo $myManifestation['price'] ?: 'NC' ?>

jouet:      <?php echo $myManifestation['jouet'] ?: 'NC' ?>

adresse:    <?php echo $myManifestation['geo']['address'] ?: 'NC' ?>

adresse c.  <?php echo $myManifestation['geo']['comment'] ?: 'NC' ?>

particulier:<?php echo $myManifestation['individual'] ?: 'NC' ?>

pro:        <?php echo $myManifestation['pro'] ?: 'NC' ?>

expo habit  <?php echo $myManifestation['resident'] ?: 'NC' ?>

intérieur:  <?php echo $myManifestation['indoor'] ?: 'NC' ?>

extérieur:  <?php echo $myManifestation['outdoor'] ?: 'NC' ?>

nombre exp: <?php echo $myManifestation['number'] ?: 'NC' ?>

ouverture:  <?php echo $myManifestation['opening'] ?: 'NC' ?>

organ.      <?php echo $myManifestation['organisateur']['name'] ?: 'NC' ?>

horaire:    <?php echo $myManifestation['schedule'] ?: 'NC' ?>
</pre>
<?php }*/ ?>

				<?php #$this->pre($myManifestation); ?>


				<p>Informations fournies à titre indicatif et sous toutes réserves d'erreurs, d'annulations ou
				d'omissions. Téléphonez avant de vous déplacer.</p>


				<?php if(!empty($myImages)){ ?>
				<h5>Galierie</h5>
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
				<ul><?php



					foreach($myManifestation['date'] as $e){
						echo '<li>';
						echo $api->datePeriod($e);
						#echo '('.date("Y-m-d H:i:s", $e['start']->sec).' // '.date("Y-m-d H:i:s", $e['end']->sec).')';
						#$this->pre($e);
						echo '</li>';
					}
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

	<div class="right"><?php
		include(MYTHEME.'/ui/right/search.php');
		include(MYTHEME.'/ui/right/ad.php');
		include(MYTHEME.'/ui/right/actu.php');
	?></div>

</div>

<?php
	include(MYTHEME.'/ui/footer.php');
	include(MYTHEME.'/ui/html-end.php');

	if($_SERVER['HTTP_HOST'] == 'dev.supercalendrier.com'){
	#	$this->pre($myManifestation);
	}
	echo $this->apiLoad('calendrier')->googleMapJS();
?>

<script src="/media/ui/js/manifestation-detail.js"></script>
<script src="/media/ui/vendor/fancybox/source/jquery.fancybox.pack.js"></script>

</body></html>