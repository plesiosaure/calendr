<?php

	if(!defined('COREINC')) die('Direct access not allowed');
	$api = $app->apiLoad('calendrierOrganisateur');

	// GET /////////////////////////////////////////////////////////////////////////////////////////////////////////////
	if($_REQUEST['_id'] != NULL){

		$data = $api->get(array(
			'debug'  => false,
			'_id'    => $_REQUEST['_id'],
			'format' => array('city')
		));

		$id_city         = $data['city']['_id'];
		$city            = $data['city']['name'];
	}

	// SAVE ////////////////////////////////////////////////////////////////////////////////////////////////////////////
	if($_POST['action'] == 'save'){

		if($_REQUEST['_id']) $api->_id($_REQUEST['_id']);

		$data = array(
			'name'             => $_POST['name'],

			'title'            => $_POST['title'],
			'firstname'            => $_POST['firstname'],
			'lastname'            => $_POST['lastname'],

			'email'            => $_POST['email'],
			'phone'            => $_POST['phone'],
			'mobile'           => $_POST['mobile'],
			'fax'              => $_POST['fax'],
			'web'              => $_POST['web'],

			'city._id'         => $_POST['id_city'],
			'address'          => $_POST['address'],
			'rubrique'         => $_POST['rubrique'],
		);

		$api->set($data);


		if($api->valid()){
			$api->fake(false)->debug(false)->save();

			// Mise à jour de la base MVS
			$fct = empty($_POST['_id']) ? 'organisateurCreation' : 'organisateurUpdate';
			$app->apiLoad('calendrierMvsSync')->$fct(array('_id' => $api->_id()));

			$app->go('data?_id='.$api->_id());
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
</head>
<body>

<header><?php
	include(COREINC.'/top.php');
	include(dirname(__DIR__) . '/ui/menu.php')
?></header>

<div class="inject-subnav-right hide">
	<li>
		<div class="btn-group">
			<a onclick="$('#data').submit();" class="btn btn-small btn-success">Enregistrer</a>
			<?php if($data['_id'] != ''){ ?>
				<a class="btn btn-small btn-danger" onclick="r('<?php echo $data['_id'] ?>')">Supprimer</a>
			<?php } ?>
			<a href="organisateur/data" class="btn btn-small">Nouvel organisateur</a>
		</div>
	</li>
</div>

<div id="app">
	<form action="organisateur/data" method="post" id="data">

		<input type="hidden" name="action" value="save" />
		<input type="hidden" name="_id" id="_id" value="<?php echo $data['_id'] ?>" />

		<div class="tabset">
			<div class="view">

				<div class="row-fluid no-gutter_">
					<div class="span6">

						<ul class="field-list">
							<li class="clearfix form-item <?php echo $api->formError('name', 'needToBeFilled') ?>">
								<label>Organisateur</label>
								<div class="form">
									<input type="text" name="name" value="<?php echo $api->formValue($data['name'], $_POST['name']); ?>" style="width:99%;" class="field" />
								</div>
							</li>
							<li class="clearfix form-item">
								<label>Représentant</label>
								<div class="form">
									<table width="100%">
										<tr>
											<td width="5%"></td>
											<td width="40%">Nom</td>
											<td></td>
											<td width="40%">Prénom</td>
										</tr>
										<tr>
											<td><select name="title"><option></option><?php
												foreach($api->title() as $k){
													$sel = ($api->formValue($data['title'], $_POST['title']) == $k) ? 'selected' : NULL;
													echo '<option value="'.$k.'"'.$sel.'>'.$k.'</option>';
												}
											?></select></td>
											<td><input type="text" name="firstname" value="<?php echo $api->formValue($data['firstname'], $_POST['firstname']); ?>" class="field" style="width:100%;" /></td>
											<td></td>
											<td><input type="text" name="lastname"  value="<?php echo $api->formValue($data['lastname'], $_POST['lastname']); ?>" class="field" style="width:100%;" /></td>
										</tr>
									</table>

								</div>
							</li>
							<li class="clearfix form-item">
								<label>Contact</label>
								<div class="form">
									<table width="100%">
										<tr>
											<td width="40%">eMail</td>
											<td width="20%">Téléphone</td>
											<td width="20%">Mobile</td>
											<td width="20%">Fax</td>
										</tr>
										<tr>
											<td><input type="text" name="email" value="<?php echo $api->formValue($data['email'], $_POST['email']); ?>" class="field" style="width:75%;" /></td>
											<td><input type="text" name="phone" value="<?php echo $api->formValue($data['phone'], $_POST['phone']); ?>" class="field" style="width:95%;" /></td>
											<td><input type="text" name="mobile" value="<?php echo $api->formValue($data['mobile'], $_POST['mobile']); ?>" class="field" style="width:95%;" /></td>
											<td><input type="text" name="fax" value="<?php echo $api->formValue($data['fax'], $_POST['fax']); ?>" class="field" style="width:100%;" /></td>
										</tr>
									</table>

								</div>
							</li>
							<li class="clearfix form-item">
								<label>Site web</label>
								<div class="form">
									<input type="text" name="web" value="<?php echo $api->formValue($data['web'], $_POST['web']); ?>" class="field" style="width:100%;" />
								</div>
							</li>
							<li class="clearfix form-item">
								<label>Rubrique</label>
								<div class="form">
									<select name="rubrique"><?php
										foreach($api->rubrique() as $k => $n){
											$sel = ($api->formValue($data['rubrique'], $_POST['rubrique']) == $k) ? 'selected' : NULL;
											echo '<option value="'.$k.'"'.$sel.'>'.$n.'</option>';
										}
									?></select>
								</div>
							</li>
						</ul>
					</div>

					<div class="span6">
						<ul class="field-list">
							<li class="clearfix form-item">
								<label>Adresse</label>
								<div class="form">
									<textarea style="width: 98%" rows="4" name="address" class="field"><?php echo $api->formValue($data['address'], $_POST['address']); ?></textarea>
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
								<label>Commentaire</label>
								<div class="form">
									<textarea style="width: 98%" rows="6" name="commentaire" class="field"><?php echo $api->formValue($data['commentaire'], $_POST['commentaire']); ?></textarea>
								</div>
							</li>
						</ul>
					</div>

				</div>

			</div>
		</div>


	<input type="submit" style="visibility: hidden;" />
	</form>
</div>

<?php include(COREINC.'/end.php'); ?>
<script type="text/javascript" src="ui/js/mvs.js"></script>
<script type="text/javascript" src="manifestation/ui/js/data.js"></script>

<script>
	function r(id){
		if(confirm("Voulez-vous supprimer cet organisateur .")){
			document.location = 'organisateur/?remove='+id;
		}
	}
</script>

<?php

	$app->pre($data);

?>
</body>
</html>