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

<div id="main" class="clearfix show-grid">

	<?php #include(MYTHEME.'/ui/carou.php'); ?>

	<div class="left clearfix">

		<div class="title-gradient">
            <h1>La carte des manifestations</h1>

			<div class="pagination right">
	            <ul>
	                <li><a href="#"><<</a></li>
	                <li><a href="#">1</a></li>
	                <li><a href="#">2</a></li>
	                <li><a href="#">3</a></li>
	                <li><a href="#">4</a></li>
	                <li><a href="#">>></a></li>
	            </ul>
			</div>

            <select class="right">
                <option>Semaine X</option>
            </select>
		</div>

		<div class="left">
			<div class="block block-bordered">
				<div class="title">Dans votre département</div>

				<select class="full-width">
					<option>64 - Pyrénées Atlantiques</option>
				</select>

				<div class="categorytitle">Manifestations Auto</div>

				<label class="checkbox"><input type="checkbox">Rassemblement</label>
				<label class="checkbox"><input type="checkbox">Salon</label>
				<label class="checkbox"><input type="checkbox">Bourse de l'occasion</label>
				<label class="checkbox"><input type="checkbox">Road Trip</label>

				<div class="categorytitle">Manifestations Moto</div>

				<label class="checkbox"><input type="checkbox">Rassemblement</label>
				<label class="checkbox"><input type="checkbox">Salon</label>
				<label class="checkbox"><input type="checkbox">Bourse de l'occasion</label>
				<label class="checkbox"><input type="checkbox">Road Trip</label>

				<div class="categorytitle">Manifestations Brocante</div>

				<label class="checkbox"><input type="checkbox">Vide Greniers</label>
				<label class="checkbox"><input type="checkbox">Brocante</label>
				<label class="checkbox"><input type="checkbox">Bourse de collections</label>
				<label class="checkbox"><input type="checkbox">Vente aux enchères</label>
			</div>
		</div>
        <div class="right">
	        <div id="map" style="width:599px; height:692px"></div>
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
<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=true&libraries=drawing,geometry"></script>
<script src="/media/calendrier/ui/vendor/kapmaps/kapmaps.js"></script>
<script>
	$(function() {
		$('#map').kapmap({
			'polyStyle' : {
				fillColor: 'red',
				fillOpacity: 0.5,
				strokeWeight: 1,
				strokeColor : '#444',
				clickable: false,
				editable: false,
				zIndex: 1
			},
			'onInit' : function(maps) {
				$.ajax({
					'url' : 'http://calendrier.kappuccino.org/map/departement?dep=81',
					'dataType' : 'json'
				}).done(function(data) {
					maps.loadPolys(data);
				});

				$.ajax({
					'url' : 'http://calendrier.kappuccino.org/map/departement?dep=31',
					'dataType' : 'json'
				}).done(function(data) {
					maps.opt.polyStyle.fillColor = '#f90f90';
					maps.loadPolys(data);

				});
			}
		});
	});
</script>
</body></html>