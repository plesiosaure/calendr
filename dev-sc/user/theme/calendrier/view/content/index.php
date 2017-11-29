<!DOCTYPE html> 
<html lang="<?php echo LOC ?>">
<head>
	<?php include(MYTHEME.'/ui/html-head.php'); ?>
</head>
<body>
<?php include(MYTHEME.'/ui/header.php'); ?>

<div id="main" class="clearfix show-grid home">

	<div class="left clearfix no-mobile">
		<div id="map-dates" class="title-gradient">
            <h1>La carte des manifestations</h1>

			<div class="weeks btn-toolbar">
				<div class="btn-group" id="dates-buttons" data-delta="<?php echo $myDelta ?>">
					<button class="btn active" id="previousWeek">&laquo;</button>
					<?php
						foreach($myDates as $d){
							$class = (intval($d['week']) == intval($myCurrent)) ? ' current' : '';
							echo '<button class="btn week-num '.$class.'" data-date="'.$d['js'].'">';
							echo ucwords($d['short']);
							echo '</button>';
						}
					?>
					<button class="btn active" id="nextWeek">&raquo;</button>
				</div>
			</div>
		</div>

		<div class="left">
			<div class="block block-bordered" id="categories">
				<div class="title medium">Filtres de la carte</div>

				<div class="title light">Département</div>
				<select class="full-width" id="dep-select" name="dep" data-empty="yes"></select>

				<div class="title light">Filtre par semaine</div>
				<select id="weekPicker"><?php
					$start = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
					$week  = 7 * 86400;

					for($i=$myCurrent; $i<=52; $i++){
						$sel = ($i+1 == intval($myCurrent)) ? ' selected' : '';
						$d = ucwords($this->helperDate(date("Y-m-d", $start), '%e %b'));
						$v = date("Y/m/d", $start);
						echo '<option value="'.$v.'"'.$sel.' data-week="'.$i.'">Semaine '.($i+1).' ('.$d.')</option>';
						$start += $week;
					}

				?></select>

				<?php
					$orders = array('auto', 'moto', 'collection');
					$api    = $this->apiLoad('calendrierManifestationType');

					foreach($orders as $order){
						$type = $api->name($order);

						echo '<div class="homecat is-'.$order.'">';
						echo '<div class="categorytitle clearfix all'.ucfirst($type['name']).'"><i></i> Manifestations ';
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
		        <span>Filtrer les manifestations:</span>

		        <a class="allAuto">
		            <img src="/media/ui/img/map/marker-red.png" class="pin">
			        <span>Auto</span>
		        </a>
		        <a class="allMoto">
				    <img src="/media/ui/img/map/marker-blue.png" class="pin">
			        <span>Moto</span>
		        </a>
		        <a class="allCollection">
			        <img src="/media/ui/img/map/marker-yellow.png" class="pin">
			        <span>Collection</span>
		        </a>
		        <a id="btnHome">
			        <img src="/media/ui/img/map/marker-home.png">
			        <span>Géolocaliser mon domicile</span>
		        </a>
		        <a id="homeload">
			        <img src="/media/ui/img/icons/mapload.gif">
		        </a>
	        </div>
	        <div id="map" style="width:599px; height:600px; background: #e1e1e1;"></div>
        </div>
	</div>

	<div id="mobile-finder" class="mobile">

		<nav id="geo-action">
			<div class="action">
				<button id="find-me" class="btn visible">Rechercher autour de moi</button>
				<button id="show-map-options" class="btn">Options carte</button>
				<button id="show-search-form" class="btn">Masquer la carte</button>
			</div>
		</nav>

		<form method="get" action="/manifestation/search" class="visible">
			<div class="form-group">
				<label>Date</label>
				<input type="date" name="date" value="<?php
				$date = $_SESSION['search']['date'];
				if($date){
					list($d, $m, $y) = explode('.', $data);
					echo $y.'-'.$m.'-'.$d;
				}
			?>">
			</div>

			<div class="form-group">
				<label>Type</label>
				<select name="cat" class="right search-clean"><option></option><?php

					$block  = $_SESSION['search'] ?: $_GET;
					$orders = array('auto', 'moto', 'collection');
					$api    = $this->apiLoad('calendrierManifestationType');

					foreach($orders as $order){
						$type = $api->name($order);
						$sel  = ($block['cat'] == 't'.$type['id']) ? ' selected' : NULL;
						echo '<option value="t'.$type['id'].'"'.$sel.'>'.$type['name'].'</option>';

						foreach($api->get($order) as $e){
							$name = $e['short'] ?: $e['name'];
							$sel  = ($block['cat'] == 'c'.$e['id']) ? ' selected' : NULL;
							echo '<option value="c'.$e['id'].'"'.$sel.'>-- '.$name.'</option>';
						}
					}

					unset($api, $orders, $order, $e, $type, $name);

				?></select>

			</div>

			<div class="form-group">
				<label>Région</label>
				<select name="region" data-sel="<?php echo $_SESSION['search']['region']; ?>" data-dep="#dep-search-mobile" ></select>
			</div>

			<div class="form-group">
				<label>Département</label>
				<select name="dep" data-sel="<?php echo $_SESSION['search']['dep']; ?>" id="dep-search-mobile"></select>
			</div>

			<input type="submit" class="btn btn-submit" value="Valider">

		</form>
	</div>

	<div id="map-wrapper" class="mobile"></div>

	<div id="map-options" class="mobile">
		<div class="data">
			map-options
		</div>
	</div>

	<div class="right no-mobile"><?php
		include(MYTHEME.'/ui/right/organisateur.php');
		include(MYTHEME.'/ui/right/search.php');
		include(MYTHEME.'/ui/right/ad.php');
		include(MYTHEME.'/ui/right/actu.php');
	?></div>

</div>

<?php
	include(MYTHEME.'/ui/footer.php');
	include(MYTHEME.'/ui/html-end.php');
	echo $this->apiLoad('calendrier')->googleMapJS();
?>

<script src="/media/ui/js/home.min.js"></script>

</body></html>