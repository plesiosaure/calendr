<?php

	if(!defined('COREINC')) die('Direct access not allowed');
	$api = $app->apiLoad('calendrierManifestation');

	// GET /////////////////////////////////////////////////////////////////////////////////////////////////////////////
	if($_REQUEST['_id'] != NULL){

		$data = $api->get(array(
			'debug'  => false,
			'_id'    => $_REQUEST['_id'],
			'format' => array('city', 'organisateur')
		));

		$id_organisateur = $data['organisateur']['_id'];
		$organisateur    = $data['organisateur']['name'];
		$id_city         = $data['city']['_id'];
		$city            = $data['city']['name'];

		if(!empty($data)){
			/*// Vérifier la DATA chez VMS (synchro ?)
			$mvs = $app->apiLoad('calendrierMvsSync')->manifestationGet(array(
				'_id' => $data['_id']
			));

			$mvs = (is_array($mvs) && $mvs['ok']) ? $mvs['data'] : array();*/

			// Ajouter un backup si besoin
			$api->createBackup($data['_id']);

			// Vérifier si l'on a des appel à l'API MVS en attente
			$errorCalls = $app->apiLoad('calendrierLog')->get(array(
				'debug'    => false,
				'error'    => true,
				'id_manif' => (string)$data['_id']
			));
		}

	}



	// SAVE ////////////////////////////////////////////////////////////////////////////////////////////////////////////
	if($_POST['action'] == 'save'){

		// Si on a pas ajouter manuellement le numero via le JS, récuper l'étourderie ici
		if(!empty($_POST['phones']['n']['number'])) $_POST['phones'][] = $_POST['phones']['n'];
		unset($_POST['phones']['n']);

		$data = array(
			'updated'          => new MongoDate(),
			'name'             => $_POST['name'],
			'type'             => $_POST['type'],

			'pro'              => $_POST['pro'],
			'individual'       => $_POST['individual'],
			'resident'         => $_POST['resident'],
			'indoor'           => $_POST['indoor'],
			'outdoor'          => $_POST['outdoor'],
			'game'             => $_POST['game'],
			'number'           => $_POST['number'],

			'free'             => $_POST['free'],
			'paying'           => $_POST['paying'],
			'price'            => $_POST['price'],

			'resume'           => $_POST['resume'],
			'presentation'     => $_POST['presentation'],
			'schedule'         => $_POST['schedule'],
			'opening'          => $_POST['opening'],
			'periodicity'      => $_POST['periodicity'],

			'email'            => $_POST['email'],
			'phone'            => $_POST['phone'],
			'phones'           => (!empty($_POST['phones']) ? $_POST['phones'] : array()),
			'mobile'           => $_POST['mobile'],
			'fax'              => $_POST['fax'],
			'web'              => $_POST['web'],

			'mvs.type'         => intval($_POST['mvs_type']),
			'mvs.category'     => intval($_POST['mvs_category']),

			'city._id'         => $_POST['id_city'],
			'organisateur._id' => $_POST['id_organisateur'],

			'geo.region'       => $_POST['region'],
			'geo.dept'         => $_POST['dept'],
			'geo.address'      => $_POST['address'],
			'geo.comment'      => $_POST['comment'],
			'geo.gps'          => array($_POST['lat'], $_POST['lng']),
			'geo.zoom'         => $_POST['zoom']
		);

		if($_REQUEST['_id']){
			$api->_id($_REQUEST['_id']);
		}else{
			$data['created'] = new MongoDate();
		}

		$api->set($data);

		if($api->valid()){
			$api->fake(false)->debug(false)->save();

			$api->postUpsert($api->_id());

			// Mise à jour de la base MVS
			#$fct = empty($_POST['_id']) ? 'manifestationCreation' : 'manifestationUpdate';
			#$app->apiLoad('calendrierMvsSync')->$fct(array('_id' => $api->_id()));

			// Rediriger vers la liste
			$app->go('./?_id='.$api->_id());
		}
	}

?><!DOCTYPE html>
<html lang="fr">
<head>
	<base href="../" />
	<?php include(COREINC . '/head.php'); ?>
	<link rel="stylesheet" type="text/css" href="../content/ui/css/data.css" />
	<link rel="stylesheet" type="text/css" href="ui/css/main.css" />
	<link rel="stylesheet" type="text/css" href="ui/css/all.css" />
	<link rel="stylesheet" type="text/css" href="manifestation/ui/css/data.css" />
	<link rel="stylesheet" type="text/css" href="ui/vendor/bootstrap-datepicker/css/datepicker.css" />
</head>
<body>

<header><?php
	include(COREINC.'/top.php');
	include(dirname(__DIR__) . '/ui/menu.php')
?></header>

<form action="manifestation/data" method="post" id="data" class="check">

<div class="inject-subnav-right hide">
	<!--
	<li style="margin-top: -5px;">
		<div class="toogle-button">
			<div class="onoffswitch">
				<input type="checkbox" name="mvs_sync" class="onoffswitch-checkbox" value="yes" id="mvs_sync" <?php
				$v = $api->formValue($data['mvs']['sync'], $_POST['mvs_sync']);
				if(($v !== false && empty($v)) || $v === true) echo 'checked' ?> />
				<label class="onoffswitch-label" for="mvs_sync">
					<div class="onoffswitch-inner"></div>
					<div class="onoffswitch-switch"></div>
				</label>
			</div>
		</div>
	</li>
	-->
	<li>
		<div class="btn-group">
			<a onclick="$('#sub').trigger('click');" class="btn btn-small btn-success">Enregistrer</a>
			<?php if($data['_id'] != ''){ ?>
			<a href="manifestation/?cancel=<?php echo $data['_id'] ?>" class="btn btn-small btn-warning" style="color: #FFF;">Annuler</a>
			<a class="btn btn-small btn-danger" onclick="r('<?php echo $data['_id'] ?>')">Supprimer</a>
			<a href="manifestation/date?_id=<?php echo $data['_id'] ?>" class="btn btn-small">Dates</a>
			<?php } ?>
			<a href="manifestation/data" class="btn btn-small">Nouveau</a>
		</div>
	</li>
</div>

<div id="app">

		<?php if(count($errorCalls) > 0){ ?>
		<div class="message messageWarning">
			<b>Attention </b> il y a <?php echo count($errorCalls); ?> appel(s) en erreur pour cette manifestation:
			<a href="apilog/?cf&error=yes&id_manif=<?php echo $data['_id'] ?>">Afficher</a>
		</div>
		<?php } ?>

		<?php if(array_key_exists('backup', $data) > 0){ ?>
		<div class="message messageWarning">
			<?php if($data['mvs']['warning']){ ?><b>Cette manifestation a été mise à jour depuis MVS</b><?php } ?>
			Cette manifestation possède une archive:
			<a href="manifestation/diff?_id=<?php echo $data['_id'] ?>">Afficher les différences</a>
		</div>
		<?php } ?>

		<input type="hidden" name="action" value="save" />
		<input type="hidden" name="_id" id="_id" value="<?php echo $data['_id'] ?>" />

		<div class="tabset">
			<div class="view">

				<div class="row-fluid no-gutter_">

					<div class="span8">
						<ul class="field-list">
							<li class="clearfix form-item <?php echo $api->formError('name', 'needToBeFilled') ?>">
								<label>Intitulé</label>
								<div class="form">
									<input type="text" name="name" value="<?php echo $api->formValue($data['name'], $_POST['name']); ?>" style="width:99%;" class="field" required />
								</div>
							</li>


							<li class="clearfix form-item">
								<label>MVS</label>
								<div class="form">
									Type <select name="mvs_type" data-sel="<?php echo $api->formValue($data['mvs']['type'], $_POST['mvs_type']); ?>"></select>
									Catégorie <select name="mvs_category" data-sel="<?php echo $api->formValue($data['mvs']['category'], $_POST['mvs_category']); ?>"></select>
								</div>
							</li>
							<li class="clearfix form-item">
								<div class="form" style="width:95%">
									<table width="100%">
										<tr>
											<td width="45%">
												<input type="checkbox" class="cb-toggle" data-toggle="cb-auto" id="all-auto" />
												<label for="all-auto"><b>Auto</b></label>
											</td>
											<td width="30%">
												<input type="checkbox" class="cb-toggle" data-toggle="cb-moto" id="all-moto" />
												<label for="all-moto"><b>Moto</b></label>
											</td>
											<td width="30%">
												<input type="checkbox" class="cb-toggle" data-toggle="cb-collection" id="all-collection" />
												<label for="all-collection"><b>Collection</b></label>
											</td>
											<td></td>
										</tr>
										<tr valign="top"><?php
											$apiType = $app->apiLoad('calendrierManifestationType');
											$myTypes = $api->formValue($data['type'], $_POST['type']);

											foreach($apiType->name() as $k => $name){
												$types = $apiType->get($k);

												echo '<td>';
												foreach($types as $kk => $v){
													$chk = @in_array($kk, $myTypes[$k]) ? 'checked' : '';
													echo '<input type="checkbox" name="type['.$k.'][]" value="'.$kk.'" class="cb-'.$k.'" id="'.$k.'.'.$kk.'" '.$chk.' />';
													echo '<label for="'.$k.'.'.$kk.'">'.$v['name'].'</label><br />';
												}
												echo '</td>';
											}

											?><td></td>
										</tr>
									</table>
								</div>
							</li>
							<li class="clearfix form-item">
								<label>Exposant</label>
								<div class="form">
									<table width="100%">
										<tr>
											<td width="25%">
												<input type="checkbox" name="pro" value="1" id="is-pro" <?php if($data['pro']) echo ' checked'; ?> />
												<label for="is-pro">Pro</label>
											</td>
											<td width="25%">
												<input type="checkbox" name="individual" value="1" id="is-individual" <?php if($data['individual']) echo ' checked'; ?>  />
												<label for="is-individual">Particulier</label>
											</td>
											<td width="25%">
												<input type="checkbox" name="resident" value="1" id="is-resident" <?php if($data['resident']) echo ' checked'; ?> />
												<label for="is-resident">Habitant</label>
											</td>
											<td>
												<input type="text" size="10" name="number" value="<?php echo $api->formValue($data['number'], $_POST['number']); ?>" class="field" />
												<label>Nombre</label>
											</td>
										</tr>
										<tr>
											<td>
												<input type="checkbox" name="outdoor" value="1" id="is-outdoor" <?php if($data['outdoor']) echo ' checked'; ?> />
												<label for="is-outdoor">Extérieur</label>
											</td>
											<td>
												<input type="checkbox" name="indoor" value="1" id="is-indoor" <?php if($data['indoor']) echo ' checked'; ?> />
												<label for="is-indoor">Intérieur</label>
											</td>
											<td>
												<input type="checkbox" name="game" value="1" id="is-game" <?php if($data['game']) echo ' checked'; ?> />
												<label for="is-game">Jouet</label>
											</td>
											<td></td>
										</tr>
									</table>
								</div>
							</li>
							<li class="clearfix form-item">
								<label>Tarif</label>
								<div class="form">
									<table width="100%">
										<tr>
											<td width="25%">
												<input type="checkbox" name="free" value="1" id="is-free" <?php if($data['free']) echo ' checked'; ?> />
												<label for="is-free">Gratuit</label>
											</td>
											<td width="25%">
												<input type="checkbox" name="paying" value="1" id="is-paying" <?php if($data['paying']) echo ' checked'; ?> />
												<label for="is-paying">Payant</label>
											</td>
											<td></td>
											<td>
												<input type="text" size="10" name="price" value="<?php echo $api->formValue($data['price'], $_POST['price']); ?>" class="field" />
												<label>Tarif</label>
											</td>
										</tr>
									</table>
								</div>
							</li>
							<li class="clearfix form-item">
								<label>Résumé</label>
								<div class="form">
									<textarea style="width: 99%" rows="4" name="resume" class="field"><?php echo $api->formValue($data['resume'], $_POST['resume']); ?></textarea>
								</div>
							</li>
							<li class="clearfix form-item">
								<label>Présentation</label>
								<div class="form">
									<textarea style="width: 99%" rows="8" name="presentation" class="field"><?php echo $api->formValue($data['presentation'], $_POST['presentation']); ?></textarea>
								</div>
							</li>
							<li class="clearfix form-item">
								<label>Horaire</label>
								<div class="form">
									<input type="text" name="schedule" value="<?php echo $api->formValue($data['schedule'], $_POST['schedule']); ?>" style="width:99%;" class="field" />
								</div>
							</li>
							<li class="clearfix form-item">
								<label>Ouveture</label>
								<div class="form">
									<input type="text" name="opening" value="<?php echo $api->formValue($data['opening'], $_POST['opening']); ?>" style="width:99%;" class="field" />
								</div>
							</li>
							<li class="clearfix form-item">
								<label>Périodicité</label>
								<div class="form">
									<select name="periodicity"><?php
										$v = $api->formValue($data['periodicity'], $_POST['periodicity']);
										$t = array('1' => 'Ponctuel', '2' => 'Hebdomadaire', '3' => 'Mensuel');
										foreach($t as $k => $v){
											echo '<option value="'.$k.'"'.(($v != $k) ?: ' selected').'>'.$v.'</option>';
										}
									?></select>
								</div>
							</li>
							<li class="clearfix form-item <?php echo $api->formError('user', 'needToBeFilled') ?>">
								<label>Compte</label>
								<div class="form">
									<input type="hidden" name="id_organisateur" value="<?php echo $id_organisateur ?>" />
									<input type="text"  name="tmp_organisateur" value="<?php echo $api->formValue($organisateur, $_POST['tmp_organisateur']); ?>" autocomplete="off" style="width:99%; outline: none;" class="field" />
								</div>
							</li>

							<li class="clearfix form-item">
								<label>Contact</label>
								<div class="form">
									<table width="100%">
										<tr>
											<td>eMail</td>
											<td>Téléphone</td>
											<td>Mobile</td>
											<td>Fax</td>
										</tr>
										<tr>
											<td width="40%"><input type="text" name="email" value="<?php echo $api->formValue($data['email'], $_POST['email']); ?>" class="field" style="width:75%;" /> <a id="openContact" class="btn btn-mini">Mail</a></td>
											<td width="20%"><input type="text" name="phone" value="<?php echo $api->formValue($data['phone'], $_POST['phone']); ?>" class="field" style="width:75%;" /></td>
											<td width="20%"><input type="text" name="mobile" value="<?php echo $api->formValue($data['mobile'], $_POST['mobile']); ?>" class="field" style="width:75%;" /></td>
											<td width="20%"><input type="text" name="fax" value="<?php echo $api->formValue($data['fax'], $_POST['fax']); ?>" class="field" style="width:75%;" /></td>
										</tr>
									</table>

									<p>Numéros formatés</p>
									<table width="100%" id="phones-table">
										<tr class="n">
											<td width="20%">
												<select name="phones[n][indicatif]" class="menu-indicatif"></select>
											</td>
											<td width="20%">
												<input name="phones[n][number]" type="text" class="field is-phone" placeholder="Numéro" data-parsley-type="number">
											</td>
											<td width="20%">
												<select name="phones[n][type]" class="menu-type"></select>
											</td>
											<td width="40%">
												<input name="phones[n][comment]" type="text" class="field" style="width: 90%" placeholder="Commentaire">
											</td>
											<td>
												<a class="icon-plus insert-phone-line"></a>
												<a class="icon-remove"></a>
											</td>
										</tr>
										<?php $phones = $data['phones'] ?: array(); foreach($phones as $n => $e){ ?>
											<tr class="phones-line">
												<td>
													<select name="phones[<?php echo $n ?>][indicatif]" data-val="<?php echo $e['indicatif'] ?>" class="menu-indicatif"></select>
												</td>
												<td>
													<input name="phones[<?php echo $n ?>][number]" value="<?php echo $e['number'] ?>" type="text" class="field is-phone" data-parsley-type="number" data-parsley-trigger="change" required>
												</td>
												<td>
													<select name="phones[<?php echo $n ?>][type]" data-val="<?php echo $e['type'] ?>" class="menu-type"></select>
												</td>
												<td>
													<input name="phones[<?php echo $n ?>][comment]" value="<?php echo $e['comment'] ?>" type="text" class="field" style="width: 90%">
												</td>
												<td>
													<a class="icon-remove"></a>
												</td>
											</tr>
										<?php } ?>
									</table>
								</div>
							</li>

							<li class="clearfix form-item">
								<label>Site web</label>
								<div class="form">
									<input type="text" name="web" value="<?php echo $api->formValue($data['web'], $_POST['web']); ?>" class="field" style="width:75%;" />
									<a id="openWeb" class="btn btn-mini">Ouvrir</a>
								</div>
							</li>
						</ul>
					</div>
					<div class="span4">
						<ul class="field-list">
							<li class="clearfix form-item">
								<label>Région</label>
								<div class="form">
									<select name="region" data-sel="<?php echo $api->formValue($data['geo']['region'], $_POST['region']); ?>"></select>
								</div>
							</li>
							<li class="clearfix form-item">
								<label>Départ.</label>
								<div class="form">
									<select name="dept" data-sel="<?php echo $api->formValue($data['geo']['dept'], $_POST['dept']); ?>"></select>
								</div>
							</li>
							<li class="clearfix form-item">
								<label>Ville</label>
								<div class="form">
									<input type="hidden" name="id_city" value="<?php echo $id_city ?>" />
									<input type="text"  name="tmp_city" value="<?php echo $api->formValue($city, $_POST['tmp_city']); ?>" autocomplete="off" style="width:99%; outline: none;" class="field" />
								</div>
							</li>
							<li class="clearfix form-item">
								<label>Indication</label>
								<div class="form">
									<textarea style="width: 98%" rows="2" name="comment" class="field"><?php echo $api->formValue($data['geo']['comment'], $_POST['comment']); ?></textarea>
								</div>
							</li>
							<li class="clearfix form-item">
								<label>
									Adresse
									<i class="icon-map-marker" id="set-map-adress" style="cursor: pointer"></i>
								</label>
								<div class="form">
									<textarea style="width: 98%" rows="4" name="address" class="field"><?php echo $api->formValue($data['geo']['address'], $_POST['address']); ?></textarea>
									<input type="hidden" name="lat"  value="<?php $lat = $api->formValue($data['geo']['gps'][0], $_POST['lat']);  echo number_format($lat, 14, '.', ''); ?>" />
									<input type="hidden" name="lng"  value="<?php $lng = $api->formValue($data['geo']['gps'][1], $_POST['lng']);  echo number_format($lng, 14, '.', ''); ?>" />
									<input type="hidden" name="zoom" value="<?php echo $api->formValue($data['geo']['zoom'], $_POST['zoom']); ?>" />
								</div>
								<div id="map" style="float: left; width:100%; margin-top: 40px; height: 500px;"></div>
							</li>
						</ul>
					</div>
				</div>

			</div>
		</div>


	<input type="submit" id="sub" style="visibility: hidden" />

	</form>
</div>

<?php include(COREINC.'/end.php'); ?>
<script type="text/javascript" src="ui/js/dep.js"></script>
<script type="text/javascript" src="ui/js/mvs.js"></script>
<script type="text/javascript" src="/media/ui/js/organisateur/indicatif.js"></script>
<script type="text/javascript" src="ui/vendor/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
<script type="text/javascript" src="/media/ui/vendor/Parsley.js-2.0.0-rc2/dist/parsley.min.js"></script>
<script type="text/javascript" src="../core/vendor/tinymce/jscripts/tiny_mce/jquery.tinymce.js"></script>
<script type="text/javascript" src="../core/vendor/tinymce/jscripts/tiny_mce/tiny_mce.js"></script>


<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=true&libraries=drawing,geometry"></script>
<script type="text/javascript" src="manifestation/ui/js/map.js"></script>
<script type="text/javascript" src="manifestation/ui/js/data.js"></script>


<script>
	function r(id){
		if(confirm("Voulez-vous supprimer cet organisateur .")){
			document.location = 'manifestation/?remove='+id;
		}
	}
</script>
<?php

	$app->pre($data, $phones);

?>
</body>
</html>