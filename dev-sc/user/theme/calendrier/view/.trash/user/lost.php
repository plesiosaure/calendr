<!DOCTYPE html>
<html lang="<?php echo LOC ?>">
<head>
	<title></title>
	<meta name="description" content="<?php echo HTML_DESCRIPTION ?>" />
	<meta name="keywords" content="<?php echo HTML_KEYWORDS ?>" />
	<?php include(MYTHEME . '/ui/html-head.php'); ?>
</head>
<body>

<?php include(MYTHEME . '/ui/header.php'); ?>

<div id="main" class="clearfix">

	<?php #include(MYTHEME.'/ui/carou.php'); ?>

	<div class="left clearfix">
		<h1><?php echo LOST_TITLE ?></h1>

		<?php

		if($NEED_VALIDE_EMAIL) 		$m = LOST_VALID;
		if($USER_NOT_EXIST)			$m = LOST_NOT_EXISTS;
		if($PLEASE_CHECK_INBOX)		$m = LOST_INBOX;
		if($PASSWORD_RESET)			$m = sprintf(LOST_RESET, $password);


		if($m != NULL){
			$alert = array('view'=>true, 'text'=>$m);
			include(MYTHEME . '/ui/alert.php');
		} ?>

		<?php if(!isset($PLEASE_CHECK_INBOX) OR !isset($PASSWORD_RESET)){ ?>

			<p>Afin de reg&eacute;n&eacute;rer votre mot de passe, merci de saisir votre adresse email</p>
			<p>Nous vous enverrons un courrier &eacute;lectronique contenant votre nouveau mot de passe.</p>

			<form action="lost" method="post">
				<input type="hidden" name="mailTitle" value="Regeneration de votre mot de passe" />
				email <input type="text" name="email" />
				<input type="submit" class="btn" value="Valider" />
			</form>

		<?php } ?>
	</div>

	<div class="right"><?php
		include(MYTHEME . '/ui/right/search.php');
		include(MYTHEME . '/ui/right/ad.php');
		include(MYTHEME . '/ui/right/actu.php');
	?></div>
</div>

<?php
	include(MYTHEME . '/ui/footer.php');
	include(MYTHEME . '/ui/html-end.php');
?>
</body></html>