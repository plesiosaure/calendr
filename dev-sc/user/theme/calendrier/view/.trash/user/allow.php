<!DOCTYPE html> 
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">
<head>
	<title><?php echo HTML_TITLE ?></title>
	<meta name="description" content="" />
	<meta name="keywords" content="" />
	<?php include(MYTHEME . '/ui/html-head.php') ?>
</head>
<body class="body">
<?php include(MYTHEME.'/ui/start.php'); ?>

<div class="container center">
	
	<div id="left" class="span10">

		<h1>Verification de votre compte</h1>
		
		<?php
		
			if($success){
				echo "Validation OK. <a href=\"/user/login\">Continuer</a>";
			}else{
				echo "Validation en echec";
			}

		?>

	</div>

	<div id="right" class="span4"><?php
		include(MYTHEME.'/ui/right/pub.php');
		include(MYTHEME.'/ui/right/actu-cible.php');
	?></div>

</div>

<div id="fb-root"></div>

<?php
	include(MYTHEME.'/ui/footer.php');
	include(MYTHEME.'/ui/html-end.php');
?>
<script type="text/javascript" src="/media/ui/_fbconnect/fbconnect.js"></script>
</body></html>