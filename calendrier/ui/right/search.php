<div class="block block-bordered right-search">
	<form method="get" action="/manifestation/search">

		<div class="title medium">Recherche de manifestation</div>

		<div class="clearfix">
			<div class="left label small">Type</div>
			<div class="right">
				<select name="cat" class="right"><option></option><?php

					$orders = array('auto', 'moto', 'collection');
					$api    = $this->apiLoad('calendrierManifestationType');

					foreach($orders as $order){
						$type = $api->name($order);
						$sel  = ($_GET['cat'] == 't'.$type['id']) ? ' selected' : NULL;
						echo '<option value="t'.$type['id'].'"'.$sel.'>'.$type['name'].'</option>';

						foreach($api->get($order) as $e){
							$name = $e['short'] ?: $e['name'];
							$sel  = ($_GET['cat'] == 'c'.$e['id']) ? ' selected' : NULL;
							echo '<option value="c'.$e['id'].'"'.$sel.'>-- '.$name.'</option>';
						}
					}

					unset($api, $orders, $order, $e, $type, $name);

				?></select>
			</div>
		</div>

		<div class="clearfix">
			<div class="left label small">Région</div>
			<div class="right">
				<select name="region" class="right" data-dep="#dep-search" data-sel="<?php echo $_GET['region']; ?>"></select>
			</div>
		</div>

		<div class="clearfix">
			<div class="left label small">Départ.</div>
			<div class="right">
				<select name="dep" id="dep-search" class="right" data-sel="<?php echo $_GET['dep']; ?>"></select>
			</div>
		</div>

		<div class="clearfix">
			<div class="left label small">Code postal</div>
			<div class="right zip">
				<input type="text" class="span1" name="zip" placeholder="01000" value="<?php echo $_GET['zip'] ?>" />
			</div>
		</div>

		<div class="clearfix">
			<div class="left label small">Date</div>
			<div class="right">
				<div class="input-append">
					<input class="icon bsdp datepicker" name="date" type="text" value="<?php echo $_GET['date'] ?>">
					<span class="add-on"><i class="icon-calendar"></i></span>
				</div>
			</div>
		</div>

		<div class="clearfix">
			<div class="left label small">Recherche</div>
			<div class="right">
				<input type="text" name="q" class="search" placeholder="Recherche libre" value="<?php echo $_GET['q'] ?>" />
			</div>
		</div>

		<div class="acenter">
			<input type="submit" class="btn btn-large" value="Lancer la recherche" />
		</div>


	</form>
</div>