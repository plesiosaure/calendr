<!DOCTYPE html> 
<html lang="<?php echo LOC ?>">
<head>
	<title></title>
	<meta name="description" content="<?php echo HTML_DESCRIPTION ?>" />
	<meta name="keywords" content="<?php echo HTML_KEYWORDS ?>" />

	<?php include(MYTHEME.'/ui/html-head.php'); ?>
</head>
<body>
<?php include(MYTHEME.'/ui/header.php'); ?>

<div id="main" class="clearfix">
	
	<?php include(MYTHEME.'/ui/top/carou.php'); ?>
	
	<div id="left">
		
		<div class="row">
			
			<div class="span9 barre">
				<img class="left vignette" src="http://placehold.it/160x135&text=VIGNETTE">
				<div class="span7">
					<div class="navbar">
						<div class="navbar-inner">
							<a class="brand" href="#">Victory Road Trip</a>
							<div class="right">
								<span class="label label-info">Moto</span>
							</div>
						</div>

						<div class="left clear">
							{TITLE}
						</div>
						<div class="left clear">
							{sous titre}
						</div>
					</div>
				</div>			
			</div>
			
			<div class="span9">
				<div class="navbar">
					<div class="navbar-inner">
						<a class="brand" href="#">Autres manifestations aux alentours</a>
					</div>
				</div>
			</div>
			
			<div class="span9 barre">
				<img class="left vignette" src="http://placehold.it/170x135&text=VIGNETTE">
				<img class="right vignette" src="http://placehold.it/680x135&text=IMGARTICLE">
					<br style="clear:both" />

				<div class="span7 right">
					<div class="left">
						<a href="#" class="btn btn-mini">Precedent</a>
						<a href="#" class="btn btn-mini">Suivant</a>
					</div>
					<div class="right"><a href="#" class="btn btn-mini">Lire l'article</a></div>			
				</div>
			</div>
			
			
			<div class="span9 bloc-manif">
				<div class="manif left">
					<table class="table table-condensed">
						<tr>
							<td>{title}</td>
							<td>{detail}</td>
						</tr>
						<tr>
							<td>{title}</td>
							<td>{detail}</td>
						</tr>
						<tr>
							<td>{title}</td>
							<td>{detail}</td>
						</tr>
						<tr>
							<td>{title}</td>
							<td>{detail}</td>
						</tr>
						<tr>
							<td>{title}</td>
							<td>{detail}</td>
						</tr>
						<tr>
							<td>{title}</td>
							<td>{detail}</td>
						</tr>
						<tr>
							<td>{title}</td>
							<td>{detail}</td>
						</tr>
						<tr>
							<td>{title}</td>
							<td>{detail}</td>
						</tr>
						<tr>
							<td>{title}</td>
							<td><a href="#" class="btn btn-mini">{detail}</a></td>
						</tr>
						<tr>
							<td>{title}</td>
							<td><a href="#" class="btn btn-mini">{detail}</a></td>
						</tr>
					</table>
				</div>
				<div class="manif right">
					<div class="inner">
						<h2>{titre}</h2>
						<h3>{sous-titre}</h3>
						<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
					
						<a href="#" class="btn btn-mini">Contacter l'organisateur</a>
						<a href="#" class="btn btn-mini">Voir le site</a>
					</div>
				</div>
			</div>

			<div class="span9">
				<img src="http://placehold.it/900x430&text=GOOGLEMAPS">
			</div>

		</div>
		
	</div>
	
	<div id="right">
		<?php 
			include(MYTHEME.'/ui/right/search.php');
			include(MYTHEME.'/ui/right/ad.php');
			include(MYTHEME.'/ui/right/actu.php'); ?>
	</div>
	
</div>

<?php
	include(MYTHEME.'/ui/footer.php');
	include(MYTHEME.'/ui/html-end.php');
?>

</body></html>