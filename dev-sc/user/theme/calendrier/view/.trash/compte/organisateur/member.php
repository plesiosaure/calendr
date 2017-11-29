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

<?php include dirname(__DIR__) . '/ui/menu.php'; ?>

	<h1>Organisateur #<?php echo $myOrganisation['_id'] ?></h1>

	<table border="1" width="100%">
		<?php foreach($myMembers as $e){ ?>
		<tr>
			<td><?php echo $e['field']['userPrenom'].' '.$e['field']['userNom'] ?></td>
			<td width="200"><?php
				if($e['id_user'] == $me['id_user']){

				}else
				if($e['isPending']){
					echo '<a class="btn btn-danger">Ne pas accepter</a>';
				}else{
					echo '<a class="btn btn-danger">Rejeter</a>';
				}
			?></td>
		</tr>
		<?php } ?>
	</table>


	<br /><br /><br /><br />
	<?php
	#	$this->pre($myOrganisation, $myMembers);
	?>




<?php
	include(MYTHEME . '/ui/footer.php');
	include(MYTHEME . '/ui/html-end.php');
?>
</body></html>