<!DOCTYPE html>
<html lang="<?php echo LOC ?>">
<head>
	<title></title>
	<?php include(MYTHEME.'/ui/html-head.php'); ?>
</head>
<body>

<?php include(MYTHEME.'/ui/header.php'); ?>

<div id="main" class="clearfix">

	<div class="left clearfix manif-detail">
		<div class="title-gradient">
			<h1>Manifesation Auto, Moto, Collection: <?php
				echo $myRegion['name'];
			?></h1>
		</div>

		<p>Evénement(s): <?php echo $myTotal ?></p>

		<p>Affichier les <a href="/manifestation/region">autres région</a></p>

		<table border="1">
			<tbody>
			<?php foreach($myData as $e){
				$nearest = $date->nearest($e['date']);
			?>
				<tr>
					<td><?php
						echo $api->manifestationPermalinkMarkup($e).' ';
						if($nearest) echo $api->datePeriod($nearest);
						echo ' à ';
						echo $e['city']['name'];
					?></td>
				</tr>
			<?php } ?>
			</tbody>
		</table>



		<?php
			if($myTotal > $myLimit){
				echo $this->apiLoad('calendrierHelper')->pagination(array(
					'total'   => $myTotal,
					'limit'   => $myLimit,
					'offset'  => $myOffset,
					'pattern' => $myPattern,
					'size'    => 6
				));
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
