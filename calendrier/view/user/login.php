<!DOCTYPE html>
<html lang="<?php echo LOC ?>">
<head>
	<title><?php echo HTML_TITLE ?></title>
	<meta name="description" content="<?php echo HTML_DESCRIPTION ?>" />
	<meta name="keywords" content="<?php echo HTML_KEYWORDS ?>" />
	<?php include(MYTHEME.'/ui/html-head.php'); ?>
</head>

<body>

<?php include(MYTHEME.'/ui/header.php'); ?>

<div id="main">
	
	<div id="left" class="span10">
		<h1>Identifiez-vous</h1>

		<?php
			if(isset($_GET['missMatch'])){
				$alert = array('view' => true, 'text' => 'L\'identifiant ou le mot de passe que vous avez saisi est incorrect.');
				include(MYTHEME.'/ui/alert.php');
			}
		?>

		<form action="/user/login" method="post">
			<input type="hidden" name="log" value="login"  />

			<table cellpadding="4">
				<tr>
					<td width="100">Identifiant</td>
					<td><input type="text" name="login" /></td>
				</tr>
				<tr>
					<td>Mot de passe</td>
					<td><input type="password" name="password" autocomplete="off" /></td>
				</tr>
				<tr>
					<td></td>
					<td>
						<input type="submit" class="btn" value="Valider" />
						<a href="/user/lost" class="btn">J'ai oubli&eacute; mon mot de passe</a>
						<a href="/user/new" class="btn btn-success">Inscription</a>
					</td>
				</tr>
				<tr>
					<td></td>
					<td>
						<div id="fb-auth"></div>
						<div id="user-info"></div>
					</td>
				</tr>
			</table>
	
			<p></p>
		</form>


	</div>

	<div id="right" class="span4"><?php
		include(MYTHEME.'/ui/right/pub.php');
		include(MYTHEME.'/ui/right/actu-cible.php');
	?></div>


</div>

<?php
	include(MYTHEME.'/ui/footer.php');
	include(MYTHEME.'/ui/html-end.php');
	if($this->user['id_user'] == NULL){
?>
<script type="text/javascript" src="/media/calendrier/ui/_fbconnect/fbconnect.js"></script>
<div id="fb-root"></div>
<?php } ?>
</body></html>