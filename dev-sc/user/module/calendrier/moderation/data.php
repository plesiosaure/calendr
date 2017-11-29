<?php

	if(!defined('COREINC')) die('Direct access not allowed');
	$api = $app->apiLoad('calendrierManifestation');

	// GET /////////////////////////////////////////////////////////////////////////////////////////////////////////////
	if($_REQUEST['_id'] != NULL){
		$data = $api->get(array(
			'debug'  => false,
			'_id'    => $_REQUEST['_id']
		#	'format' => array('city', 'organisateur')
		));

		$temp = $data['temp'];
	}

	// CONFIRMED ///////////////////////////////////////////////////////////////////////////////////////////////////////
	if(isset($_POST['confirmed'])){

		$_POST['free']   = ($_POST['fee'] == 'free')   ? true : false;
		$_POST['paying'] = ($_POST['fee'] == 'paying') ? true : false;


		$manif = array(
			'_id'          => $_REQUEST['_id'],
			'name'         => $_POST['name'],
			'date'         => $_POST['date'],
			'city'         => $_POST['id_city'],

			'schedule'     => $_POST['schedule'],
			'opening'      => $_POST['opening'],

			'number'       => intval($_POST['number']),
			'pro'          => !empty($_POST['indoor']),
			'individual'   => !empty($_POST['individual']),
			'resident'     => !empty($_POST['resident']),

			'indoor'       => !empty($_POST['indoor']),
			'outdoor'      => !empty($_POST['outdoor']),

		#	'free'         => !empty($_POST['free']),
		#	'paying'       => !empty($_POST['paying']),

			'free'         => $_POST['free'],
			'paying'       => $_POST['paying'],
			'price'        => floatval(str_replace(',', '.', $_POST['price'])),

			'phone'        => $_POST['phone'],
			'phones'       => $_POST['phones'],
			'fax'          => $_POST['fax'],
			'email'        => $_POST['email'],
			'web'          => $_POST['web'],

			'presentation'     => $_POST['presentation'],
			'presentation_web' => $_POST['presentation_web'],
			'resume_date'      => $_POST['resume_date'],

			'geo'        => array(
				'address' => $_POST['address'],
				'country' => $_POST['country'],
				'comment' => $_POST['situation']
			)
		);

		if(!empty($_POST['lat']) && !empty($_POST['lng'])){
			$manif['geo']['gps'] = array(
				floatval(str_replace(',', '.', $_POST['lat'])),
				floatval(str_replace(',', '.', $_POST['lng']))
			);
		}

		if(!empty($_POST['zoom'])){
			$manif['geo']['zoom'] = $_POST['zoom'];
		}

		if(!empty($_POST['mvs_category'])){
			$manif['mvs']['category'] = intval($_POST['mvs_category']);
		}

		// En cas d'image
		if(!empty($_POST['images'])){
			$keep = array();

			foreach($_POST['images'] as $n => $e){
				if($e['keep']){

					$tmp = array('url' => $e['url']);

					if($_POST['poster'] == $e['url']) $tmp['poster'] = true;

					$keep[] = $tmp;
				}
			}

			$manif['images'] = $keep;
		}

		#$app->pre($manif);
		#die();


		$api->manifestationModerateConfirmed(array(
			'manifestation' => $manif
		));

		$app->go('./');
	}

	// REJECTED ////////////////////////////////////////////////////////////////////////////////////////////////////////
	if(isset($_POST['rejected'])){
		$api->manifestationModerateRejected($_POST['_id']);
		$app->go('./');
	}else
	if(isset($_POST['rejected-email'])){
		$app->go('./contact?_id='.$_POST['_id'].'&reject=1');
	}

?><!DOCTYPE html>
<html lang="fr">
<head>
	<base href="../" />
	<?php include(COREINC . '/head.php'); ?>
	<link rel="stylesheet" type="text/css" href="../content/ui/css/data.css" />
	<link rel="stylesheet" type="text/css" href="moderation/ui/css/data.css" />
</head>
<body>

<header><?php
	include(COREINC.'/top.php');
	include(dirname(__DIR__) . '/ui/menu.php')
?></header>

<div class="inject-subnav-right hide">
	<li>
		<a href="moderation/contact?_id=<?php echo $data['_id'] ?>">Contact</a>
	</li>
</div>

<div id="app"><div id="split">

<div class="message messageWarning"><?php

	if($data['moderation'] == 'remove'){
		echo 'Demande de suppression.';
	}else
	if($data['moderation'] == 'update'){
		echo 'Demande de mise à jour.';
	}else{
		echo 'Nouvelle manifestation.';
	}

?></div>

<div class="message messageValid"><?php

	$types = array(
		'1' => 'Collection',
		'2' => 'Auto',
		'3' => 'Moto'
	);

	echo 'Type de manifestation: '.$types[$data['mvs']['type']];


?></div>

<form action="moderation/data" method="post" id="data">
	<input type="hidden" name="_id" value="<?php echo $data['_id'] ?>" >

	<?php list($lat, $lng) = $temp['geo']['gps'] ?>
	<input type="hidden" name="lat" value="<?php echo $lat ?>" >
	<input type="hidden" name="lng" value="<?php echo $lng ?>" >
	<input type="hidden" name="zoom" value="<?php echo $temp['geo']['zoom'] ?>" >

	<table class="main">
		<tr>
			<td class="title" width="45%">Version originale</td>
			<td class="title"></td>
			<td class="title" width="45%">Version modifiée</td>
		</tr>
		<tr>
			<td><?php include (__DIR__.'/includes/organisateur.php'); ?></td>
			<td></td>
			<td><?php include (__DIR__.'/includes/editor.php'); ?></td>
		</tr>
		<tr>
			<td></td>
			<td class="libelle">Catégorie</td>
			<td><?php
				$type = $app->apiLoad('calendrierManifestationType')->nameFromSubId($data['mvs']['category']);
				$cats = $app->apiLoad('calendrierManifestationType')->get($type['key']);
			#	$app->pre($cats);
				?>

				<select name="mvs_category"><?php
				foreach($cats as $c){
					$sel = ($c['id'] == $data['mvs']['category']) ? ' selected' : '';
					echo '<option value="'.$c['id'].'"'.$sel.'>'.$c['name'].'</option>';
				}
				?>
				</select>
			</td>
		</tr>
		<tr>
			<td><input value="<?php echo $data['name']; ?>" class="field" disabled></td>
			<td class="libelle">Titre</td>
			<td><input name="name" value="<?php echo $temp['name']; ?>" class="field"></td>
		</tr>
		<tr valign="top">
			<td>
				<textarea class="field" disabled><?php echo $data['geo']['address']; ?></textarea>
				<input value="<?php echo $data['city']['name']; ?>" class="field" disabled>
				<select disabled><?php
					foreach($app->apiLoad('calendrierDepartement')->country() as $e){
						$sel = ($data['geo']['country'] == $e['code']) ? ' selected' : '';
						echo '<option value="'.$e['code'].'"'.$sel.'>'.$e['name'].'</option>';
					}
				?></select>
			</td>
			<td class="libelle">Adresse</td>
			<td>
				<textarea name="address" class="field"><?php echo $temp['geo']['address']; ?></textarea>

				<input name="id_city" type="hidden" value="<?php echo $temp['city']['_id']; ?>" class="field">

				<input id="city-search" value="<?php echo $temp['city']['name']; ?>" class="field">
				<div id="city-result">...</div>

				<select name="country" ><?php
					foreach($app->apiLoad('calendrierDepartement')->country() as $e){
						$sel = ($temp['geo']['country'] == $e['code']) ? ' selected' : '';
						echo '<option value="'.$e['code'].'"'.$sel.'>'.$e['name'].'</option>';
					}
				?></select>
			</td>
		</tr>
		<tr valign="top">
			<td><textarea class="field" disabled><?php echo $data['geo']['comment']; ?></textarea></td>
			<td class="libelle">Situation</td>
			<td><textarea name="situation" class="field"><?php echo $temp['geo']['comment']; ?></textarea></td>
		</tr>
		<tr valign="top">
			<td>
				Horaire
				<textarea class="field" disabled><?php echo $data['schedule']; ?></textarea>
				Ouverture
				<textarea class="field" disabled><?php echo $data['opening']; ?></textarea>
			</td>
			<td class="libelle">Ouveture</td>
			<td>
				Horaire
				<textarea name="schedule" class="field"><?php echo $temp['schedule']; ?></textarea>
				Ouverture
				<textarea name="opening" class="field"><?php echo $temp['opening']; ?></textarea>
			</td>
		</tr>
		<tr>
			<td>
				Nombre
				<input value="<?php echo $data['number'] ?>" class="field short" disabled>

				<input type="checkbox" <?php if($data['pro']) echo 'checked' ?> disabled>
				Professionnels

				<input type="checkbox" <?php if($data['individual']) echo 'checked' ?> disabled>
				Particulier

				<input type="checkbox" <?php if($data['resident']) echo 'checked' ?> disabled>
				Habitant
			</td>
			<td class="libelle">Exposant</td>
			<td>
				Nombre
				<input type="number" name="number" value="<?php echo $temp['number'] ?>" class="field short" >

				<input type="checkbox" name="pro" value="1" <?php if($temp['pro']) echo 'checked' ?> />
				Professionnels

				<input type="checkbox" name="individual" value="1" <?php if($temp['individual']) echo 'checked' ?> />
				Particulier

				<input type="checkbox" name="resident" value="1" <?php if($temp['resident']) echo 'checked' ?> />
				Habitant
			</td>
		</tr>
		<tr>
			<td>
				<input type="checkbox" <?php if($data['indoor']) echo 'checked' ?> disabled>
				Intérieur

				<input type="checkbox" <?php if($data['outdoor']) echo 'checked' ?> disabled>
				Extérieur
			</td>
			<td class="libelle">Lieu</td>
			<td>
				<input type="checkbox" name="indoor" value="1" <?php if($temp['indoor']) echo 'checked' ?> />
				Intérieur

				<input type="checkbox" name="outdoor" value="1" <?php if($temp['outdoor']) echo 'checked' ?> />
				Extérieur
			</td>
		</tr>
		<tr>
			<td>
				<input type="radio" <?php if($data['free']) echo 'checked' ?> disabled>
				Gratuit

				<input type="radio" <?php if($data['paying']) echo 'checked' ?> disabled>
				Payant

				<input type="text" value="<?php echo $data['price'] ?>" class="field tiny" disabled>
			</td>
			<td class="libelle">Tarifs</td>
			<td>
				<input type="radio" name="fee" value="free" <?php if($temp['free']) echo 'checked' ?> />
				Gratuit

				<input type="radio" name="fee" value="paying" <?php if($temp['paying']) echo 'checked' ?> />
				Payant

				<input type="text" name="price" value="<?php echo $temp['price'] ?>" class="field tiny">
			</td>
		</tr>
		<tr valign="top">
			<td>
				<table width="100%" class="inside">
					<tr>
						<td width="50%">Téléphone (MVS)</td>
						<td width="50%">Fax (MVS)</td>
					</tr>
					<tr>
						<td><textarea rows="6" class="field" disabled><?php echo $data['phone'] ?></textarea></td>
						<td><textarea rows="6" class="field" disabled><?php echo $data['fax'] ?></textarea></td>
					</tr>
					<tr>
						<td colspan="2">Site Web</td>
					</tr>
					<tr>
						<td colspan="2"><input type="text" value="<?php echo $data['web'] ?>" class="field" disabled></td>
					</tr>
					<tr>
						<td colspan="2">email</td>
					</tr>
					<tr>
						<td colspan="2"><input type="text" value="<?php echo $data['email'] ?>" class="field" disabled></td>
					</tr>
				</table>
			</td>
			<td class="libelle">Contact</td>
			<td>
				<table width="100%" class="inside">
					<tr>
						<td width="50%">Téléphone (MVS)</td>
						<td width="50%">Fax (MVS)</td>
					</tr>
					<tr>
						<td><textarea rows="6" name="phone" class="field"><?php echo $temp['phone'] ?></textarea></td>
						<td><textarea rows="6" name="fax"   class="field"><?php echo $temp['fax'] ?></textarea></td>
					</tr>
					<tr>
						<td colspan="2">Site Web</td>
					</tr>
					<tr>
						<td colspan="2"><input type="text" name="web" value="<?php echo $temp['web'] ?>" class="field"></td>
					</tr>
					<tr>
						<td colspan="2">email</td>
					</tr>
					<tr>
						<td colspan="2"><input type="text" name="email" value="<?php echo $temp['email'] ?>" class="field"></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr valign="top">
			<td><div class="images clearfix">
				<?php if($data['images']){ foreach($data['images'] as $n => $e){

					$img = $app->mediaUrlData(array(
						'url'   => $e['url'],
						'mode'  => 'height',
						'value' => 50
					));

					echo '<div class="item">';
					echo '<img '.$img['html'].' >';
					echo '</div>';

				}} ?>
			</div></td>
			<td class="libelle">Images</td>
			<td>
				<input type="radio" name="poster" value="" id="no-poster">
				<label for="no-poster">Ne pas utiliser de poster</label>
				<div class="images clearfix">

				<?php if($temp['images']){ foreach($temp['images'] as $n => $e){

					$img = $app->mediaUrlData(array(
						'url'   => $e['url'],
						'mode'  => 'height',
						'value' => 50
					));

					echo '<div class="item">';
					echo '<input name="images['.$n.'][url]" type="hidden" value="'.$e['url'].'" >';
					echo '<img '.$img['html'].' >';

					echo '<div class="action">';
					echo '<input name="images['.$n.'][keep]" type="checkbox" value="1" id="img'.$n.'" checked>';
					echo '<label for="img'.$n.'">Garder</label><br>';
					echo '<input type="radio" name="poster" value="'.$e['url'].'" id="poster'.$n.'" '.(($e['poster']) ? 'checked' : '').'>';
					echo '<label for="poster'.$n.'">Poster</label>';
					echo '</div>';

					echo '</div>';

				}} ?>
				</div>
			</td>
		</tr>
		<tr valign="top">
			<td><?php foreach($data['date'] as $n => $e){ ?>
				<div>
					<input value="<?php echo date("d/m/Y", $e['start']->sec) ?>" class="field date" disabled>
					<input value="<?php echo date("d/m/Y", $e['end']->sec) ?>" class="field date" disabled>
				</div>
			<?php } ?></td>
			<td class="libelle">Dates</td>
			<td><?php foreach($temp['date'] as $n => $e){ ?>
				<div>
					<input name="date[<?php echo $n ?>][start]" value="<?php echo date("d/m/Y", $e['start']->sec) ?>" class="field date">
					<input name="date[<?php echo $n ?>][end]"   value="<?php echo date("d/m/Y", $e['end']->sec) ?>" class="field date">
				</div>
			<?php } #$app->pre($temp['date']) ?></td>
		</tr>
		<tr valign="top">
			<td><textarea class="field" disabled><?php echo $data['resume_date']; ?></textarea></td>
			<td class="libelle">Dates (résumé)</td>
			<td><textarea name="resume_date" class="field"><?php echo $temp['resume_date']; ?></textarea></td>
		</tr>
		<tr valign="top">
			<td><textarea class="field" disabled><?php echo $data['presentation']; ?></textarea></td>
			<td class="libelle">Présentation papier</td>
			<td><textarea name="presentation" class="field"><?php echo $temp['presentation']; ?></textarea></td>
		</tr>
		<tr valign="top">
			<td><textarea class="field" disabled><?php echo $data['presentation_web']; ?></textarea></td>
			<td class="libelle">Présentation web</td>
			<td><textarea name="presentation_web" class="field"><?php echo $temp['presentation_web']; ?></textarea></td>
		</tr>
		<tr valign="top">
			<td>
				<table border="1" class="phones-table">
					<?php foreach(($data['phones'] ?: array()) as $n => $e){ ?>
						<tr class="phones-line">
							<td><select data-val="<?php echo $e['indicatif'] ?>" class="menu-indicatif" disabled></select></td>
							<td><input value="<?php echo $e['number'] ?>" type="text" class="field is-phone" disabled></td>
							<td><select data-val="<?php echo $e['type'] ?>" class="menu-type" disabled></select></td>
							<td><input value="<?php echo $e['comment'] ?>" type="text" class="field" style="width: 90%" disabled></td>
						</tr>
					<?php } ?>
				</table>
			</td>
			<td class="libelle">Numéro</td>
			<td>
				<table border="1" class="phones-table">
				<?php foreach(($temp['phones'] ?: array()) as $n => $e){ ?>
					<tr class="phones-line">
						<td><select name="phones[<?php echo $n ?>][indicatif]" data-val="<?php echo $e['indicatif'] ?>" class="menu-indicatif"></select></td>
						<td><input name="phones[<?php echo $n ?>][number]" value="<?php echo $e['number'] ?>" type="text" class="field is-phone" data-parsley-type="number" data-parsley-trigger="change" data-parsley-group="step-3"></td>
						<td><select name="phones[<?php echo $n ?>][type]" data-val="<?php echo $e['type'] ?>" class="menu-type"></select></td>
						<td><input name="phones[<?php echo $n ?>][comment]" value="<?php echo $e['comment'] ?>" type="text" class="field" style="width: 90%"></td>
					</tr>
				<?php } ?>
				</table>
			</td>
		</tr>
	</table>

	<button type="submit" class="btn btn-success" name="confirmed">Valider</button>
	<button type="submit" class="btn btn-danger" name="rejected">Rejeter</button>
	<button type="submit" class="btn btn-danger" name="rejected-email">Rejeter et envoyer un mail</button>

</form>

</div></div>

<?php include(COREINC.'/end.php'); #$app->pre($data) ?>

<script src="/media/ui/js/organisateur/indicatif.min.js"></script>
<script type="text/javascript" src="moderation/ui/js/data.min.js"></script>

</body>
</html>