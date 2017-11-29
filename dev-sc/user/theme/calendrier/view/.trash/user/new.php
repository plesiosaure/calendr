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

	<div class="left">
		<h1>Creer un compte</h1>

		<?php

		if($NEW_ERROR_FILLED)		$m = NEW_ERROR_FILLED;
		if($NEW_ERROR_EXISTS)		$m = NEW_ERROR_EXISTS;
		if($NEW_ERROR_INSERT)		$m = NEW_ERROR_INSERT;
		if($NEW_CONFIRMATION)		$h = NEW_CONFIRMATION;
		if($NEW_UPDATE)				$h = NEW_UPDATE;

		if($h != '') $m = $h;

		if($m != NULL){
			$alert = array('view'=>true, 'text'=>$m);
			include(MYTHEME . '/ui/alert.php');
		}

		if($exists){ ?>
			<div class="alert alert-block alert-warning fade in">
				<p><a href="new?again=<?php echo $_POST['userMail'] ?>"><?php echo NEW_RESEND_MAIL ?></a></p>
			</div>
		<?php }
		
		if(!isset($h)){ ?>
		<form action"new" method="post">
		
			<input type="hidden" name="todo" value="create" />
			<table width="100%" class="debug">
				<tr>
					<td width="150">Email</td>
					<td><input type="text" name="userMail" autocomplete="off" value="<?php echo $_POST['userMail'] ?>" /></td>
				</tr>
				<tr>
					<td>Mot de passe</td>
					<td><input type="password" name="userPasswd" autocomplete="off" value="<?php echo $_POST['userPasswd'] ?>" /> 
						Que des lettres et des chiffres de 4 &agrave; 16 caract&egrave;res
					</td>
				</tr>
				<tr>
					<td height="30"></td>
					<td>
						<input type="checkbox" name="cgu" value="1" <?php if($_POST['cgu']) echo 'checked' ?> />
						J'accepte les
						<a href="/corporate/conditions-utilisation" target="_blank">conditions g&eacute;n&eacute;rales d'utilisations</a>
						du site.
					</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>
						<input type="submit" value="Valider" class="btn" style="float:left;" />
						<div style="float:left; margin-left:10px; margin-top:3px;">
							<div id="fb-auth"></div>
							<div id="user-info"></div>
						</div>
					</td>
				</tr>
			</table>
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