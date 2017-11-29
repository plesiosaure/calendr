<!DOCTYPE html>
<html lang="<?php echo LOC ?>">
<head>
	<title></title>
	<?php include(MYTHEME.'/ui/html-head.php'); ?>
</head>
<body>

<?php include(MYTHEME.'/ui/header.php'); ?>

<div id="main" class="clearfix">

	<div class="left clearfix manif-detail">
		<div class="title-gradient">
			<h1>Liste des manifestation par département</h1>
		</div>

		<table border="1">
			<thead>
				<tr>
					<td width="50">#</td>
					<td width="200">Département</td>
					<td>Manifestation</td>
				</tr>
			</thead>
			<tbody>
			<?php foreach($myData as $e){

				$href = 'departement/'.$e['code'].'/page/0';
				?>
				<tr>
					<td><?php echo '<a href="'.$href.'">'.$e['code'].'</a>' ?></td>
					<td><?php echo '<a href="'.$href.'">'.$e['name'].'</a>' ?></td>
					<td><?php echo '<a href="'.$href.'">'.intval($e['count']).'</a>' ?></td>
				</tr>
			<?php } ?>
			</tbody>
		</table>
	</div>

	<div class="right"><?php
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
