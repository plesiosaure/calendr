<!DOCTYPE html>
<html lang="<?php echo LOC ?>">
<head>
	<?php include(MYTHEME.'/ui/html-head.php'); ?>
</head>
<body>
<?php include(MYTHEME.'/ui/header.php'); ?>

<div id="main" class="clearfix show-grid home">

<div class="left clearfix" id="wizard">

		<h1 class="gradient">Modification de la manifestation</h1>
		<?php include __DIR__.'/includes/status.php'; ?>

		<div id="message"></div>
		<p>Les champs signalés par une étoile (*) sont obligatoires</p>

		<form method="post" action="edit" id="form-edit">
			<input type="hidden" name="id" value="<?php echo $myManifestation['_id'] ?>">
			<input type="hidden" name="poster-url" id="poster-url" value="<?php echo $myPoster ?>">

			<?php list($lat, $lng) = $myManifestation['geo']['gps']; ?>
			<input type="hidden" name="lat" value="<?php echo $lat ?>">
			<input type="hidden" name="lng" value="<?php echo $lng ?>">
			<input type="hidden" name="zoom" value="<?php echo $myManifestation['geo']['zoom'] ?>">

			<div class="step">

				<div class="pane is-left">

					<div class="form-group">
						<label for="">Intitulé de la manifestation *</label>
						<p>Ex : Bourse de jouets anciens ou 3e Rallye des givrés</p>
						<input type="text" name="name" value="<?php echo $myManifestation['name'] ?>" class="field" required data-parsley-group="step-1" data-parsley-required-message="Ce champ est requis">
					</div>

					<div class="form-group">
						<label for="">Situation géographique</label>
						<p>Ex : à 2 Km de Paris ou entre Melun et Savigny</p>
						<textarea name="situation" class="field" data-parsley-group="step-1"><?php echo $myManifestation['geo']['comment'] ?></textarea>
					</div>

				</div>

				<div class="pane is-right">

					<div class="form-group">
						<label for="">Ville *</label>
						<input type="hidden" name="id_ville" value="<?php echo $myManifestation['city']['_id'] ?>" required data-parsley-group="step-1">
						<input type="hidden" name="zip" value="<?php echo $myManifestation['city']['zip'] ?>" required data-parsley-group="step-1">

						<input type="text" id="city-search" autocomplete="off" value="<?php echo $myManifestation['city']['name'] ?>" class="field" required data-parsley-group="step-1" data-parsley-required-message="Ce champ est requis">

						<div id="city-result"></div>
					</div>

					<div class="form-group">
						<label for="">Code postal *</label>
						<input type="text" name="zip" value="<?php echo $myManifestation['city']['zip'] ?>" required data-parsley-group="step-1" data-parsley-required-message="Ce champ est requis">
					</div>

					<div class="form-group">
						<label for="">Pays *</label>
						<select name="country" class="field" required data-parsley-group="step-1" data-parsley-required-message="Un pays doit être sélectionné">
							<option></option><?php
							$pays = $this->apiLoad('calendrierDepartement')->country();
							foreach($pays as $e){
								$sel = ($myManifestation['geo']['country'] == $e['code']) ? ' selected' : '';
								echo '<option value="'.$e['code'].'"'.$sel.'>'.$e['name'].'</option>';
							}
						?></select>
					</div>

				</div>

				<div class="pane is-both is-map">

					<p>
						<button class="btn btn-small" id="set-map-adress" type="button">Placer le point d'après l'adresse</button>
						Cliquez sur le pictogramme
						<img src="/media/ui/img/map/placement.png" height="16" width="12" align="middle" >
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

					<?php foreach($myManifestation['date'] as $n => $e){ ?>
						<div class="date-selector">
							Début   <input name="dates[<?php echo $n ?>][start]" class="is-date date-from" value="<?php echo date('d/m/Y', $e['start']->sec) ?>" data-parsley-trigger="change focusin focusout" data-parsley-group="step-2" data-parsley-no-focus required>
							Fin     <input name="dates[<?php echo $n ?>][end]"   class="is-date date-to"   value="<?php echo date('d/m/Y', $e['end']->sec) ?>" data-parsley-trigger="change focusin focusout" data-parsley-group="step-2" data-parsley-no-focus required>
							<a class="removeDateSelector">Supprimer cette date</a>
						</div>
					<?php } ?>

					<a id="newDateSelector" class="btn btn-small">Ajouter une nouvelle date</a>

					<div class="pane is-both">
						<div class="form-group">
							<label for="">Texte complémentaire pour les manifestations hebdomadaires et mensuelles</label>
							<p>Décrivez succintement la manière dont les dates se repétent — Par exemple:<br>
							Ex : Tous les 2e dimanches du mois sauf juillet-aout – Tous les premiers week-ends du mois</p>
							<textarea name="opening" class="field" style="height: 30px; margin-bottom: 10px;" data-parsley-group="step-2" data-parsley-no-focus><?php echo $myManifestation['opening'] ?></textarea>
						</div>
					</div>

				</div>
			</div>

			<div class="step">
				<div class="pane is-left">
					<div class="form-group">
						<label for="">Horaire de la manifestation</label>
						<input type="text" name="schedule" value="<?php echo $myManifestation['schedule'] ?>" class="field">
					</div>
					<div class="form-group">
						<label>La manifestation aura lieu:</label>
						<div class="checkbox">
							<label>
								<input type="checkbox" name="indoor" value="1" <?php if($myManifestation['indoor']) echo 'checked' ?> >
								Aura lieu à l'intérieur
							</label>
							<label>
								<input type="checkbox" name="outdoor" value="1" <?php if($myManifestation['outdoor']) echo 'checked' ?> >
								Aura lieu à l'extérieur
							</label>
						</div>
					</div>
					<div class="form-group">
						<label>Nombre d'exposants</label>
						<input type="text" name="number" value="<?php echo $myManifestation['number'] ?>" style="width: 60px;">
					</div>
					<div class="form-group">
						<label>Types d'exposants</label>
						<div class="checkbox">
							<label>
								<input type="checkbox" name="pro" value="1" <?php if($myManifestation['pro']) echo 'checked' ?> >
								Professionnels
							</label>
							<label>
								<input type="checkbox" name="individual" value="1" <?php if($myManifestation['individual']) echo 'checked' ?> >
								Particuliers
							</label>
							<label>
								<input type="checkbox" name="resident" value="1" <?php if($myManifestation['resident']) echo 'checked' ?> >
								Habitants
							</label>
						</div>
					</div>
					<div class="form-group">
						<label>Entrée visiteur</label>
						<div class="radio">
							<label>
								<input type="radio" name="fee" value="free" <?php if($myManifestation['free']) echo 'checked' ?> >
								Gratuit
							</label>
							<label>
								<input type="radio" name="fee" value="paying" <?php if($myManifestation['paying']) echo 'checked' ?> >
								Payant
							</label>
						</div>
					</div>

					<div class="form-group">
						<label>Tarif</label>
						<input type="text" name="price" value="<?php echo $myManifestation['price'] ?>" style="width: 30px;">
					</div>
				</div>
				<div class="pane is-right">
					<div class="form-group">
						<label for="">Email</label>
						<input type="text" name="email" value="<?php echo $myManifestation['email'] ?>" class="field">
					</div>
					<div class="form-group">
						<label for="">Site Web</label>
						<input type="text" name="web" value="<?php echo $myManifestation['web'] ?>" class="field">
					</div>
					<div class="form-group">
						<label>Présentation magazine (nombre de caractères limité)</label>
						<p>Ex : Circuit de 70km à travers la campagne genevoise et française pour voitures, motos, véhicules lourds d’avant 1978. RV dès 7h30 place de la République. Départ à 9h</p>
						<textarea name="presentation" class="field" style="height: 75px;" data-parsley-maxlength="280"><?php echo $myManifestation['presentation'] ?></textarea>
						<div id="paperCount"></div>
					</div>

					<div class="form-group">
						<label>Complément d’information en ligne (vous pouvez développer ici la présentation de votre manifestation)</label>
						<textarea name="presentation_web" class="field" style="height: 150px;" data-parsley-group="step-3"><?php echo $myManifestation['presentation_web'] ?></textarea>
					</div>
				</div>

				<div style="clear: both; height: 40px; background: #FFFFFF;"></div>

				<div class="pane">
					<div class="form-group">
						<label for="">Adresse à publier *</label>
						<p>Pour une manifestation de type collection, indiquez l’adresse de la manifestation (Ex. Place du marché)<br>
						   Pour une manifestation de type auto-moto, indiquez l’adresse postale complète du contact de la manifestation (Ex. M. Dupont Martin, 70 avenue de Valvins, 77210 Avon)</p>
						<textarea name="address" class="field" required data-parsley-group="step-3" data-parsley-required-message="Ce champ est requis"><?php echo $myManifestation['geo']['address'] ?></textarea>
					</div>
				</div>

				<div style="clear: both; height: 40px; background: #FFFFFF;"></div>

				<div class="pane is-both">
					<div class="form-group">
						<label>Contacts téléphoniques</label>
						<?php
							$mvs = array();

							if(!empty($myManifestation['phone']))   $mvs[] = 'Téléphone: '.$myManifestation['phone'];
							if(!empty($myManifestation['fax']))     $mvs[] = 'Fax: '.$myManifestation['fax'];

							if(!empty($mvs)) echo '<p>Valeur en cours: '.implode(' ', $mvs).'</p>';
						?>
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
							<?php $phones = $myManifestation['phones'] ?: array(); foreach($phones as $n => $e){ ?>
								<tr class="phones-line">
									<td><select name="phones[<?php echo $n ?>][indicatif]" data-val="<?php echo $e['indicatif'] ?>" class="menu-indicatif"></select></td>
									<td><input name="phones[<?php echo $n ?>][number]" value="<?php echo $e['number'] ?>" type="text" class="field is-phone" data-parsley-trigger="change" data-parsley-group="step-3"></td>
									<td><select name="phones[<?php echo $n ?>][type]" data-val="<?php echo $e['type'] ?>" class="menu-type"></select></td>
									<td><input name="phones[<?php echo $n ?>][comment]" value="<?php echo $e['comment'] ?>" type="text" class="field" style="width: 90%"></td>
									<td>
										<a class="remove-me btn-small">Supprimer cette ligne</a>
									</td>
								</tr>
							<?php } ?>
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

						<?php if($myManifestation['images']){ foreach($myManifestation['images'] as $n => $e){

							$img = $this->mediaUrlData(array(
								'url'   => $e['url'],
								'mode'  => 'height',
								'value' => '50'
							));

							?>

							<div class="item <?php if($e['poster']) echo 'poster' ?>">
								<input type="hidden" name="images[]" value="<?php echo $e['url'] ?>">

								<div class="bar">
									<i class="icon-star icon-white ttip poster" rel="tooltip" data-original-title="Mettre en avant"></i>
									<i class="icon-remove icon-white ttip remove" rel="tooltip" data-original-title="Supprimer cette photo"></i>
								</div>
								<div class="data">
									<img <?php echo $img['html'] ?> >
								</div>
							</div>

						<?php }} ?>

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

		<div class="block block-bordered">
			<div class="title medium">Autres infos</div>
			<p><a href="<?php echo $api->manifestationPermalink($myManifestation); ?>" target="_blank">Ouvrir la page de la manifestation</a></p>
		</div>
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