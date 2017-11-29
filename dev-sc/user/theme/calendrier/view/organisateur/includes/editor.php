<div class="pane is-left">
	<div class="form-group">
		<label for="">Votre organisation *</label>
		<input value="" type="text" name="org_organisation" required data-parsley-group="orga" data-parsley-required-message="Ce champ est requis">
	</div>

	<div class="form-group">
		<label for="">Votre prénom *</label>
		<input value="" type="text" name="org_lastname" required data-parsley-group="orga" data-parsley-required-message="Ce champ est requis">
	</div>

	<div class="form-group">
		<label for="">Votre nom *</label>
		<input value="" type="text" name="org_name" required data-parsley-group="orga" data-parsley-required-message="Ce champ est requis">
	</div>

	<div class="form-group">
		<label for="">Votre email *</label>
		<input value="" type="email" name="org_email" required data-parsley-group="orga" data-parsley-required-message="Ce champ est requis">
	</div>
</div>

<div class="pane is-right">

	<div class="form-group">
		<label for="">Numéro de téléphone (a saisir sans espace) *</label>
		<input value="" type="text" name="org_phone" required data-parsley-group="orga" data-parsley-required-message="Ce champ est requis">
	</div>

	<div class="form-group">
		<label for="">Adresse *</label>
		<input value="" type="text" name="org_address" required data-parsley-group="orga" data-parsley-required-message="Ce champ est requis">
	</div>

	<div class="form-group">
		<label for="">Code postal *</label>
		<input value="" type="text" name="org_zip" required data-parsley-group="orga" data-parsley-required-message="Ce champ est requis">
	</div>

	<div class="form-group">
		<label for="">Ville *</label>
		<input value="" type="text" name="org_city" autocomplete="off" required data-parsley-group="orga" data-parsley-required-message="Ce champ est requis">
		<input type="hidden" name="org_id_ville" value="" _required _data-parsley-group="orga" data-parsley-errors-messages-disabled>
		<div id="orga-city-result"></div>
	</div>

	<div class="form-group">
		<label for="">Pays *</label>
		<select name="org_country" class="field" required data-parsley-group="step-1" data-parsley-required-message="Ce champ est requis">
			<option></option><?php
			$pays = $this->apiLoad('calendrierDepartement')->country();
			foreach($pays as $e){
				$sel = (($_POST['country'] ?: 'FR') == $e['code']) ? ' selected' : '';
				echo '<option value="'.$e['code'].'"'.$sel.'>'.$e['name'].'</option>';
			}
		?></select>
	</div>


</div>



