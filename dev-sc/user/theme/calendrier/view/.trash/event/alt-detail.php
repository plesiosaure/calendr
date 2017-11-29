<!DOCTYPE html> 
<html lang="<?php echo LOC ?>">
<head>
	<title></title>
	<meta name="description" content="<?php echo HTML_DESCRIPTION ?>" />
	<meta name="keywords" content="<?php echo HTML_KEYWORDS ?>" />

	<?php include(MYTHEME . '/ui/html-head.php'); ?>
</head>
<body>
<?php include(MYTHEME.'/ui/top/header.php'); ?>

<div id="main" class="clearfix">
	
	<?php include(MYTHEME.'/ui/top/carou.php'); ?>
	
	<div id="left">
		
		<div class="row">
			
			<div class="span9 barre">
				<div class="span3 nomargin">
					<img class="left vignette" src="http://placehold.it/240x180&text=VIGNETTE 4/3" />
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
				<div class="span6">
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
						
						<div class="left clear">
							Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?
						</div>
						
						<div class="left">
							<a href="#" class="btn btn-mini">{contact}</a>
							<a href="#" class="btn btn-mini">{voir google}</a>
							<a href="#" class="btn btn-mini">{voir site}</a>
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
			
			<div class="span9">
				<img src="http://placehold.it/900x430&text=GOOGLEMAPS">
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
	include(MYTHEME . '/ui/footer.php');
	include(MYTHEME . '/ui/html-end.php');
?>

</body></html>