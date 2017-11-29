<!DOCTYPE html>
<html lang="<?php echo LOC ?>">
<head>
	<?php include(MYTHEME.'/ui/html-head.php'); ?>
</head>
<body>
<?php include(MYTHEME.'/ui/header.php'); ?>

<div id="main" class="clearfix show-grid">

	<div class="left clearfix" id="wizard">

		<h1 class="gradient">Création d'une nouvelle manifestation</h1>
		<?php include __DIR__.'/includes/status.php'; ?>

		<div id="message"></div>
		<p>Les champs signalés par une étoile (*) sont obligatoires</p>

		<form method="post" action="create" id="form-edit">
			<input type="hidden" name="poster-url" id="poster-url" value="" >
			<input type="hidden" name="lat" value="">
			<input type="hidden" name="lng" value="">
			<input type="hidden" name="zoom" value="5">

			<div class="step">

				<div class="pane is-left">

					<div class="form-group">
						<label for="">Intitulé de la manifestation *</label>
						<p>Ex : Bourse de jouets anciens ou 3e Rallye des givrés</p>
						<input type="text" name="name" value="" class="field" required data-parsley-group="step-1" data-parsley-required-message="Ce champ est requis">
					</div>

					<div class="form-group list-select">
						<label for="">Type de manifestation *</label>

						<span>Auto</span>
						<select name="cat-auto">
							<option></option><?php
							foreach($apiType->get('auto') as $e){
								$sel  = ($block['cat'] == 'c'.$e['id']) ? ' selected' : NULL;
								echo '<option value="'.$e['id'].'"'.$sel.'>'.($e['short'] ?: $e['name']).'</option>';
							}
						?></select>

						<span>Moto</span>
						<select name="cat-moto">
							<option></option><?php
							foreach($apiType->get('moto') as $e){
								$sel  = ($block['cat'] == 'c'.$e['id']) ? ' selected' : NULL;
								echo '<option value="'.$e['id'].'"'.$sel.'>'.($e['short'] ?: $e['name']).'</option>';
							}
						?></select>

						<span>Collection</span>
						<select name="cat-collection">
							<option></option><?php
							foreach($apiType->get('collection') as $e){
								$sel  = ($block['cat'] == 'c'.$e['id']) ? ' selected' : NULL;
								echo '<option value="'.$e['id'].'"'.$sel.'>'.($e['short'] ?: $e['name']).'</option>';
							}
						?></select>
					</div>

					<div class="form-group">
						<label for="">Situation géographique</label>
						<p>Ex : à 2 Km de Paris ou entre  Melun et Savigny</p>
						<textarea name="situation" class="field" data-parsley-group="step-1"></textarea>
					</div>

				</div>

				<div class="pane is-right">

					<div class="form-group">
						<label for="">Ville *</label>
						<input type="hidden" name="id_ville" value="" _required _data-parsley-group="step-1" data-parsley-errors-messages-disabled>

						<input type="text" id="city-search" autocomplete="off" value="" class="field" required data-parsley-group="step-1" data-parsley-required-message="Ce champ est requis">

						<div id="city-result"></div>
					</div>

					<div class="form-group">
						<label for="">Code postal *</label>
						<input type="text" name="zip" value="" required data-parsley-group="step-1" data-parsley-required-message="Ce champ est requis">
					</div>

					<div class="form-group">
						<label for="">Pays *</label>
						<select name="country" class="field" required data-parsley-group="step-1" data-parsley-required-message="Ce champ est requis">
							<option></option><?php
							$pays = $this->apiLoad('calendrierDepartement')->country();
							foreach($pays as $e){
								$sel = (($_POST['country'] ?: 'FR') == $e['code']) ? ' selected' : '';
								echo '<option value="'.$e['code'].'"'.$sel.'>'.$e['name'].'</option>';
							}
						?></select>
					</div>


				</div>

				<div class="pane is-both is-map">

					<p>
						<button class="btn btn-small" id="set-map-adress" type="button">Placer le point d'après l'adresse</button>
						Cliquez sur le pictogramme
						<img src="/media/ui/img/map/placement.gif" height="16" width="12" align="middle" >
						puis cliquer sur la carte pour positionner la manifestation
					</p>

					<input type="hidden" name="lat">
					<input type="hidden" name="lng">
					<input type="hidden" name="zoom">

					<div id="map"></div>
				</div>
			</div>

			<div class="step">
				<div class="pane">
					<div class="date-selector">
						Début   <input name="dates[0][start]" class="is-date date-from" data-parsley-trigger="change focusin focusout" data-parsley-group="step-2" data-parsley-no-focus required>
						Fin     <input name="dates[0][end]"   class="is-date date-to"   data-parsley-trigger="change focusin focusout" data-parsley-group="step-2" data-parsley-no-focus required>
						<a class="removeDateSelector" class="btn btn-small">Supprimer cette date</a>
					</div>

					<a id="newDateSelector" class="btn btn-small">Ajouter une nouvelle date</a>

					<div class="pane is-both map">
						<div class="form-group">
							<label for="">Texte complémentaire pour les manifestations hebdomadaires et mensuelles</label>
							<p>Décrivez succintement la manière dont les dates se repétent — Par exemple:<br>
							Ex : Tous les 2e dimanches du mois sauf juillet-aout – Tous les premiers week-ends du mois</p>
							<textarea name="opening" class="field" style="height: 30px; margin-bottom: 10px;" data-parsley-group="step-2" data-parsley-no-focus></textarea>
						</div>
					</div>

				</div>
			</div>

			<div class="step">
				<div class="pane is-left">
					<div class="form-group">
						<label for="">Horaires de la manifestation</label>
						<input type="text" name="schedule" class="field" />
					</div>
					<div class="form-group">
						<label>La manifestation aura lieu:</label>
						<div class="checkbox">
							<label>
								<input type="checkbox" name="indoor" value="1">
								Aura lieu à l'intérieur
							</label>
							<label>
								<input type="checkbox" name="outdoor" value="1">
								Aura lieu à l'éxterieur
							</label>
						</div>
					</div>
					<div class="form-group">
						<label for="">Nombre d'exposants</label>
						<input type="text" name="number" style="width: 60px;">
					</div>
					<div class="form-group">
						<label for="">Types d’exposants</label>
						<div class="checkbox">
							<label>
								<input type="checkbox" name="pro" value="1">
								Professionnels
							</label>
							<label>
								<input type="checkbox" name="individual" value="1">
								Particuliers
							</label>
							<label>
								<input type="checkbox" name="resident" value="1">
								Habitants
							</label>
						</div>
					</div>
					<div class="form-group">
						<label for="">Entrée visiteur</label>
						<div class="radio">
							<label>
								<input type="radio" name="fee" value="free">
								Gratuit
							</label>

							<label>
								<input type="radio" name="fee" value="paying">
								Payant
							</label>
						</div>
					</div>
					<div class="form-group">
						<label for="">Tarif</label>
						<input type="text" name="price" style="width: 30px; display: inline;">
					</div>
				</div>
				<div class="pane is-right">
					<div class="form-group">
						<label for="">Email contact de la manifestation</label>
						<input type="text" name="email" value="" class="field">
					</div>

					<div class="form-group">
						<label for="">Site Web</label>
						<input type="text" name="web" value="" class="field">
					</div>

					<div class="form-group">
						<label>Présentation magazine (nombre de caractères limité)</label>
						<p>Ex : Circuit de 70km à travers la campagne genevoise et française pour voitures, motos, véhicules lourds d’avant 1978. RV dès 7h30 place de la République. Départ à 9h</p>
						<textarea name="presentation" class="field" style="height: 75px;" data-parsley-maxlength="280"></textarea>
						<div id="paperCount"></div>
					</div>

					<div class="form-group">
						<label>Complément d’information en ligne (vous pouvez développer ici la présentation de votre manifestation)</label>
						<textarea name="presentation_web" class="field" style="height: 150px;" data-parsley-group="step-3"></textarea>
					</div>
				</div>

				<div style="clear: both; height: 40px; background: #FFFFFF;"></div>

				<div class="pane">
					<div class="form-group">
						<label for="">Adresse à publier *</label>
						<p>Pour une manifestation de type collection, indiquez l’adresse de la manifestation (Ex. Place du marché)<br>
						   Pour une manifestation de type auto-moto, indiquez l’adresse postale complète du contact de la manifestation (Ex. M. Dupont Martin, 70 avenue de Valvins, 77210 Avon)</p>
						<textarea name="address" class="field" required data-parsley-group="step-3" data-parsley-required-message="Ce champ est requis"></textarea>
					</div>
				</div>

				<div style="clear: both; height: 40px; background: #FFFFFF;"></div>

				<div class="pane is-both">
					<div class="form-group">
						<label>Contacts téléphoniques</label>
						<table width="100%" id="phones-table">
							<tr class="name">
								<td width="15%">Pays</td>
								<td width="15%">Numéro</td>
								<td width="15%">Type</td>
								<td width="30%">Remarques</td>
								<td></td>
							</tr>
							<tr class="ex">
								<td>Ex: France</td>
								<td>01 60 39 69 69</td>
								<td>tél</td>
								<td>le soir à partir de 18h</td>
								<td></td>
							</tr>
							<tr class="n">
								<td><select name="phones[n][indicatif]" class="menu-indicatif"></select></td>
								<td><input name="phones[n][number]" type="text" class="field is-phone" data-parsley-group="step-3"></td>
								<td><select name="phones[n][type]" class="menu-type"></select></td>
								<td><input name="phones[n][comment]" type="text" class="field" style="width: 90%"></td>
								<td class="ico">
									<a class="insert-phone-line btn btn-small">Valider</a>
									<a class="remove-me btn btn-small">Supprimer cette ligne</a>
								</td>
							</tr>
						</table>
					</div>
				</div>

				<div style="clear: both; height: 40px; background: #FFFFFF;"></div>



				<div class="pane is-both">

					<div class="form-group">
						<label>Photo ou logo</label>
					</div>

					<input id="fileupload" type="file" name="files[]" multiple >

					<div id="progress" class="progress">
						<div class="bar" style="width: 5%;"></div>
					</div>

					<div id="files" class="files image-group">
						<p>Sélectionner l’image qui représentera votre manifestation sur le site internet.<br>En survolant la photo, cliquez sur l’étoile pour valider ou sur la croix pour supprimer </p>
					</div>
				</div>

			</div>


			<div class="step">
				<?php include __DIR__.'/includes/editor.php'; ?>
			</div>


			<div class="last">
				<?php

				$bloc = $this->apiLoad('content')->contentGet(array(
					'id_content' => 60111,
				));

				echo $bloc['field']['_description'];
				editBloc($bloc);

				?>

				<button type="submit" class="btn">Envoyer</button>
			</div>

			<div class="action">
				<button id="go-next" type="button" class="btn">Continuer »</button>
			</div>
		</form>

	</div>

	<div class="right">
		<?php include __DIR__.'/../../ui/right/aide-create-edit.php'; ?>
	</div>

</div>

<?php
	include(MYTHEME.'/ui/footer.php');
	include(MYTHEME.'/ui/html-end.php');
?>

<script src="/media/ui/vendor/jquery-file-upload/js/vendor/jquery.ui.widget.js"></script>
<script src="/media/ui/vendor/jquery-file-upload/js/jquery.iframe-transport.js"></script>
<script src="/media/ui/vendor/jquery-file-upload/js/jquery.fileupload.js"></script>

<script src="/media/ui/vendor/Parsley.js-2.0.0-rc2/dist/parsley.js"></script>

<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=true&libraries=drawing,geometry"></script>

<script src="/media/ui/js/organisateur/indicatif.min.js"></script>
<script src="/media/ui/js/organisateur/edit.min.js"></script>
<script src="/media/ui/js/organisateur/edit-map.min.js"></script>


</body></html>