<form method="get" action="search">
	<table border="0" class="orga-manif-search" cellpadding="2">
		<tr>
			<td>Email</td>
			<td><input name="email" value="<?php echo addslashes($_GET['email']); ?>"></td>
		</tr>
		<tr>
			<td>Type</td>
			<td>
				<select name="cat" style="width: 93%">
					<option></option><?php

					$orders = array('auto', 'moto', 'collection');
					$api    = $this->apiLoad('calendrierManifestationType');

					foreach($orders as $order){
						$type = $api->name($order);
						$sel  = ($_GET['cat'] == $type['id']) ? ' selected' : NULL;
						echo '<option value="'.$type['id'].'"'.$sel.'>'.$type['name'].'</option>';
					}

					?></select>
			</td>
		</tr>
		<tr>
			<td>Dépt</td>
			<td><input name="dpt" value="<?php echo $_GET['dpt']; ?>"></td>
		</tr>
		<tr>
			<td>Date</td>
			<td><input name="date" value="<?php echo $_GET['date']; ?>" id="organisateurDatePicker"></td>
		</tr>
		<tr>
			<td>Mots-clés</td>
			<td><input name="q" value="<?php echo addslashes($_GET['q']); ?>"></td>
		</tr>
		<tr>
			<td></td>
			<td><input type="submit"class="btn" value="Lancer la recherche"></td>
		</tr>
	</table>

</form>

