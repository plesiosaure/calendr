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

	<?php include __DIR__ . '/ui/menu.php'; ?>

	<h1>Changer son identifiant de connection</h1>
	<a href="profil">Retour au compte</a>

	<?php if(isset($_GET['outdated'])){ ?>
		<p>Demande de changement de mot de passe trop ancienne.</p>
	<?php }else if(isset($_GET['sent'])){ ?>
		<p>Un email a été envoyé à votre nouvelle adresse.</p>
	<?php }else if(isset($_GET['done'])){ ?>
		<p>Mise à jour réussie</p>
	<?php }else{ ?>

		<form method="post" action="login" class="checkjs" novalidate="novalidate" id="formLogin">
			<input type="hidden" name="update" value="YES">
			<pre>
				Actuelle:
				<span id="now"><?php echo $me['userMail'] ?></span>

				Nouvelle
				<input name="new" type="email" data-required="true" data-notblank="true" id="a" data-same="true">

				Confirmation
				<input name="con" type="email" data-required="true" data-notblank="true" data-equalTo="#a">

				<input type="submit" class="submit">
			</pre>
		</form>

	<?php } ?>

</div>


<?php
	include(MYTHEME . '/ui/footer.php');
	include(MYTHEME . '/ui/html-end.php');
?>
<script>

	$(function(){
		$('#formLogin').parsley({
			inputs: 'input',
			excluded: 'input[type=hidden]' ,
			trigger: false,
			focus: 'first',
			successClass: 'parsley-success',
			errorClass: 'parsley-error',
			validators: {
				same: function(val){
					return val != $('#now').html();
				}
			},
			showErrors: true,
			messages: {},

			//some quite advanced configuration here..
			validateIfUnchanged: false,

			errors: {
				errorsWrapper: false, //'<ul></ul>',
				errorElem: false, //'<li></li>'
			}
		});
	});

</script>

</body></html>