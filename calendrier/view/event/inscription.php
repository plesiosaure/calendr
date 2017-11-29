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
					
					<div class="resume clear">
						<div class="navbar">
							<div class="navbar-inner">
								<a class="brand" href="#">Victory Road Trip</a>
								<div class="right">
									<span class="label label-info">Moto</span>
								</div>
							</div>
						</div>
						{TITRE RESUME}
						<br />
						{corps resume}
					</div>
					
					<div class="register clear">
						<div class="navbar">
							<div class="navbar-inner">
								<a class="brand" href="#">Participer a l'event</a>
								<div class="right">
									<span class="label label-info">Exposant</span>
								</div>
							</div>
						</div>
						
						<form class="form-horizontal">
							
							<div class="control-group">
								<label class="control-label" for="inputEmail">Nom</label>
								<div class="controls">
									<input type="text" class="span4" id="" placeholder="">
								</div>
							</div>
							<div class="control-group">
								<label class="control-label" for="inputPassword">Prenom</label>
								<div class="controls">
									<input type="password" class="span4" id="" placeholder="">
								</div>
							</div>
							<div class="control-group">
								<label class="control-label" for="inputPassword">Adresse</label>
								<div class="controls">
									<textarea class="span4"></textarea>
								</div>
							</div>
							<div class="control-group">
								<label class="control-label" for="inputPassword">Code Postal</label>
								<div class="controls">
									<input type="password" class="span4" id="" placeholder="">
								</div>
							</div>
							<div class="control-group">
								<label class="control-label" for="inputPassword">Ville</label>
								<div class="controls">
									<input type="password" class="span4" id="" placeholder="">
								</div>
							</div>
							
						</form>
						
					</div>
					
					<div class="buy clear">
						<div class="navbar">
							<div class="navbar-inner">
								<a class="brand" href="#">Acheter des emplacements</a>
								<div class="right">
									<span class="label label-info">Exposant</span>
								</div>
							</div>
						</div>
						
						<table class="table table-striped">
							<thead>
								<th>
									<td>Intitul�</td>
									<td>Tarifs</td>
									<td>Conditions</td>
									<td>Qt�</td>
									<td>Total</td>
								</th>
							</thead>
							<tbody>
								<tr>
									<td>Espace 3X3</td>
									<td>50.00&euro;</td>
									<td>Plein air</td>
									<td>2</td>
									<td>100.00</td>
								</tr>
								<tr>
									<td>Espace 3X3</td>
									<td>50.00&euro;</td>
									<td>Plein air</td>
									<td>2</td>
									<td>100.00</td>
								</tr>
								<tr>
									<td>Espace 3X3</td>
									<td>50.00&euro;</td>
									<td>Plein air</td>
									<td>2</td>
									<td>100.00</td>
								</tr>
								<tr><td colspan="6"></td></tr>
								<tr>
									<td colspan="5">Montant Total</td>
									<td>102.00</td>
								</tr>
							</tbody>
						</table>
						<a href="#" class="btn btn-mini right">Valider</a>
						<a href="#" class="btn btn-mini right">Recalculer</a>
					</div>
					
				</div>
			</div>

		</div>
		
	</div>
	
	<div id="right"><?php
		include(MYTHEME.'/ui/right/search.php');
		include(MYTHEME.'/ui/right/ad.php');
		include(MYTHEME.'/ui/right/actu.php');
	?></div>
	
</div>

<?php
	include(MYTHEME.'/ui/footer.php');
	include(MYTHEME.'/ui/html-end.php');
?>

</body></html>