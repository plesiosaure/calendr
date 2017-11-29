<div class="block block-bordered right-search">
	<form method="get" action="/manifestation/search">

		<div class="title medium">Recherche de manifestations</div>

		<div class="clearfix">
			<div class="left label small">Type</div>
			<div class="right">
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
		</div>

		<div class="clearfix">
			<div class="left label small">Région</div>
			<div class="right">
				<select name="region" class="right search-clean" data-dep="#dep-search" data-sel="<?php echo $block['region']; ?>"></select>
			</div>
		</div>

		<div class="clearfix">
			<div class="left label small">Départ.</div>
			<div class="right">
				<select name="dep" id="dep-search" class="right search-clean" data-sel="<?php echo $block['dep']; ?>"></select>
			</div>
		</div>

		<div class="clearfix">
			<div class="left label small">Code postal</div>
			<div class="right zip">
				<input type="text" class="span1 search-clean" name="zip" value="<?php echo $block['zip'] ?>" placeholder="75000" />
			</div>
		</div>

		<div class="clearfix">
			<div class="left label small">Date</div>
			<div class="right">
				<div class="input-append input-prepend date" id="searchDatePicker" data-date="<?php echo $block['date'] ?>" data-date-format="dd.mm.yyyy">
					<span class="add-on empty"><i class="icon-remove"></i></span>
					<input name="date" type="text" value="<?php echo $block['date'] ?>" class="search-clean" placeholder="<?php echo date("d.m.Y") ?>" readonly>
					<span class="add-on"><i class="icon-calendar"></i></span>
				</div>
			</div>
		</div>

		<div class="clearfix">
			<div class="left label small">Recherche</div>
			<div class="right">
				<input type="text" name="q" class="search search-clean" placeholder="Mot clés" value="<?php echo $block['q'] ?>" />
			</div>
		</div>

		<div class="clearfix">
			<div class="right">
				<input type="submit" class="btn btn-small btn-supercal" value="Lancer la recherche" />
				<input type="button" class="btn btn-small" id="cleanSearch" value="Vider la recherche" />
			</div>
		</div>


	</form>
</div>

<?php unset ($block);
