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
	
	<?php include(MYTHEME.'/ui/top/carou.php'); ?>
	
	<div id="left">
		
		<div class="navbar" style="height: 40px; margin-bottom: 20px;">
			<div class="left">La liste des manifestations</div>				
			
			<div class="right clearfix">
				<select class="select-small left"><option>Brocantes</option></select>
				<select class="select-small left"><option>Alsace</option></select>
				<select class="select-small left"><option>01 - Ain</option></select>
				<select class="select-small left"><option>01250</option></select>
				<select class="select-small left"><option>Cyborgville</option></select>
			</div>
				
			<br style="clear:both;" />
		
			<div class="left">Classez les manifestations par </div>

			<div class="right">
				<select class="select-big left"><option>Semaine 39 (du 4 au 10 octobre 2011)</option></select>
			</div>
			
		</div>
				
		<div class="row">
			
			<div class="span9 barre">
				<img class="left" src="http://placehold.it/170x135&text=VIGNETTE">
				<img class="right" src="http://placehold.it/680x135&text=IMGARTICLE">
					<br style="clear:both" />
				<div class="right"><a href="#" class="btn btn-mini">Lire l'article</a></div>			
			</div>
			
			<div class="span9 barre">
				<img class="left" src="http://placehold.it/170x135&text=VIGNETTE">
				<img class="right" src="http://placehold.it/680x135&text=IMGARTICLE">
					<br style="clear:both" />
				<div class="right"><a href="#" class="btn btn-mini">Lire l'article</a></div>			
			</div>
			
			<div class="span9 barre">
				<img class="left" src="http://placehold.it/170x135&text=VIGNETTE">
				<img class="right" src="http://placehold.it/680x135&text=IMGARTICLE">
					<br style="clear:both" />
				<div class="right"><a href="#" class="btn btn-mini">Lire l'article</a></div>			
			</div>
			
			<div class="span9 barre">
				<img class="left" src="http://placehold.it/170x135&text=VIGNETTE">
				<img class="right" src="http://placehold.it/680x135&text=IMGARTICLE">
					<br style="clear:both" />
				<div class="right"><a href="#" class="btn btn-mini">Lire l'article</a></div>			
			</div>
			
		</div>
		
	</div>
	
	<div id="right"><?php
		include(MYTHEME . '/ui/right/search.php');
		include(MYTHEME . '/ui/right/ad.php');
		include(MYTHEME . '/ui/right/actu.php');
	?></div>
	
</div>

<?php
	include(MYTHEME.'/ui/bottom/footer.php');
	include(MYTHEME . '/ui/html-end.php');
?>

</body></html>