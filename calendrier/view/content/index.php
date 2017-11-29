<!DOCTYPE html> 
<html lang="<?php echo LOC ?>">
<head>
	<title></title>
	<meta name="description" content="<?php echo HTML_DESCRIPTION ?>" />
	<meta name="keywords" content="<?php echo HTML_KEYWORDS ?>" />
	<?php include(MYTHEME.'/ui/html-head.php'); ?>
</head>
<body>
<?php include(dirname(dirname(__DIR__)).'/ui/header.php'); ?>

<div id="main" class="clearfix show-grid home">

	<div class="left clearfix">

		<div class="title-gradient heading">
            <h1>La carte des manifestations</h1>
			<div id="slider"></div>
		</div>

		<div class="left">
			<div class="block block-bordered" id="categories">
				<div class="title medium">Dans votre département</div>

				<select class="full-width" id="dep-select" name="dep" data-empty="yes"></select>

				<?php
					$orders = array('auto', 'moto', 'collection');
					$api    = $this->apiLoad('calendrierManifestationType');

					foreach($orders as $order){
						$type = $api->name($order);


						echo '<div class="is-'.$order.'">';
						echo '<div class="categorytitle">Manifestations ';
						echo $type['name'];
						echo '</div>';

						foreach($api->get($order) as $e){
							$name = $e['short'] ?: $e['name'];
							echo '<label class="checkbox">';
							echo '<input type="checkbox" data-cat="'.$e['id'].'">';
							echo $name;
							echo '</label>';
						}
						echo '</div>';
					}

					unset($api, $orders, $order, $e, $type, $name);
				?>

			</div>
		</div>
        <div class="right">
	        <div id="map-legend">
		        <a id="btnAllAuto">
			        <img src="/media/calendrier/ui/img/map/marker-blue.png" height="20" />
			        Auto
		        </a>
		        <a id="btnAllMoto">
			        <img src="/media/calendrier/ui/img/map/marker-red.png" height="20" />
			        moto
		        </a>
		        <a id="btnAllCollection">
			        <img src="/media/calendrier/ui/img/map/marker-yellow.png" height="20" />
			        collection
		        </a>
		        <a id="btnHome">
			        <img src="/media/calendrier/ui/img/map/marker-home.png" height="20" />
			        votre domicile
		        </a>
		        <a id="homeload" style="display:none;">
			        <img src="/media/calendrier/ui/img/icons/mapload.gif" height="32" width="32" />
		        </a>
	        </div>
	        <div id="map" style="width:599px; height:730px"></div>
        </div>
	</div>
	
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

<script type="text/javascript" src="/media/calendrier/ui/js/map.js"></script>

</body></html>