<?php

	if(!defined('COREINC')) die('Direct access not allowed');

	$manif = $app->apiLoad('calendrierManifestation')->get(array(
		'_id'   => $_REQUEST['_id'],
		'debug' => false
	));

	if(!empty($manif['organisateur']['_id'])){
		$orga = $app->apiLoad('calendrierOrganisateur')->get(array(
			'_id'   => $manif['organisateur']['_id'],
			'debug' => false
		));
	}

	// ACTION //////////////////////////////////////////////////////////////////////////////////////////////////////////

	if(!empty($_POST['message'])){

		if($_POST['sendmail'] == 'YES'){

			$mandrill = $app->apiLoad('sendMail')->mandrillMail(array(
				'message'  => array(
					'subject'     => $_POST['title'],
					'from_email'  => 'contact@supercalendrier.com',
					'from_name'   => 'Supercalendrier',
					'text'        => $_POST['message'],
					'html'        => nl2br($_POST['message']),
					'track_opens' => true,
					'tags'        => array('supercalendrier', 'organisateur', 'contact'),
					'to'          => array(
						array('type' => 'to',  'email' => $_POST['recipient']),
					)
				)
			));
		}

		if($_POST['todo'] == 'reject'){
			echo 'reject';
		#$app->apiLoad('calendrierManifestation')->manifestationModerateRejected($_POST['_id']);
			$app->go('./');
		}else
		if($_POST['todo'] == 'remove'){
			$app->apiLoad('calendrierManifestation')->manifestationModerateRemove($_POST['_id']);
			$app->go('./');
		}

		die();
	}

	$recipient = $_POST['recipient']        ?: ($manif['editor']['email'] ?: $orga['email']);
	$todo      = $_REQUEST['reject'] == 1   ? 'reject' : 'contact';
	$template  = $todo == 'reject'          ? 'reject.txt' : 'contact.txt';

	$message   = $_POST['message']          ?: file_get_contents(__DIR__ . '/template/'.$template);
	$title     = $_POST['title']            ?: ($todo == 'reject' ? 'Refus' : 'Contact');

// REPLACE /////////////////////////////////////////////////////////////////////////////////////////////////////////
	$message = $app->helperReplace($message, array(
		'nom'           => $manif['editor']['name'],
		'manifestation' => $manif['name']
	));

?><!DOCTYPE html>
<html lang="fr" xmlns="http://www.w3.org/1999/html">
<head>
	<base href="../" />
	<?php include(COREINC . '/head.php'); ?>
	<link rel="stylesheet" type="text/css" href="moderation/ui/css/contact.css" />
	<link rel="stylesheet" type="text/css" href="moderation/ui/css/data.css" />
</head>
<body>

<header><?php
	include(COREINC . '/top.php');
	include(dirname(__DIR__). '/ui/menu.php')
?></header>

<div id="app"><div id="contact">

	<?php if($mandrill === true){ ?>
	<div class="message messageValid">
		Votre message a bien été envoyé
	</div>
	<?php }else if(isset($mandrill)){ ?>
	<div class="message messageError">
		Une erreur est survenur : <?php $app->pre($mandrill); ?>
	</div>
	<?php } ?>

	<div class="message messageWarning"><?php

		if($manif['moderation'] == 'remove'){
			echo 'Demande de suppression.';
		}else
		if($data['moderation'] == 'update'){
			echo 'Demande de mise à jour.';
		}else{
			echo 'Nouvelle manifestation.';
		}

	?></div>

	<?php include (__DIR__.'/includes/editor.php'); ?>


	<form method="post" action="moderation/contact">
		<input type="hidden" name="_id" value="<?php echo $_REQUEST['_id'] ?>">

		Destinatire du message
		<input type="text" name="recipient" value="<?php echo $recipient ?>">

		<br>

		Titre de l'email
		<input type="text" name="title" value="<?php echo $title ?>">

		<textarea name="message"><?php echo $message ?></textarea>

		<br>
		<br>

		<input type="radio" name="sendmail" value="YES" id="mail-yes" <?php if($_POST['sendmail'] != 'YES') echo 'checked' ?> >
		<label for="mail-yes">Envoyer le mail</label>

		<br>

		<input type="radio" name="sendmail" value="NO" id="mail-no" <?php if($_POST['sendmail'] == 'YES') echo 'checked' ?> >
		<label for="mail-no">Ne pas envoyer l'email</label>

		<br><br>

		<?php if($manif['moderation'] == 'remove'){ ?>

		<input type="radio" name="todo" value="" id="mail-nada" <?php if($todo != 'reject' && $todo != 'accept') echo 'checked' ?> >
		<label for="mail-nada">Ne rien faire d'autre</label>

		<br>

		<input type="radio" name="todo" value="reject" id="mail-ko" <?php if($todo == 'reject') echo 'checked' ?> >
		<label for="mail-ko">Rejeter la demande de suppression</label>

		<br>

		<input type="radio" name="todo" value="remove" id="mail-kill" <?php if($todo == 'accept') echo 'checked' ?> >
		<label for="mail-kill">Accepter la demande de suppression</label>

		<br><br>

		<?php } ?>

		<input type="submit" value="Envoyer">
	</form>

</div></div>


<?php include(COREINC.'/end.php'); $app->pre($orga, $manif) ?>


</body>
</html>