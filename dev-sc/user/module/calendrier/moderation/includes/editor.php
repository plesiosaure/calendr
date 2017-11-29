<?php

	$editor = $data['editor'] ?: $manif['editor'];


#	$app->pre($data);

	if($editor){
?>

<table id="editor">
	<tr>
		<td colspan="2" class="head">Coordonnées de la personne ayant fait la modification</td>
	</tr>
	<tr>
		<td width="100">Nom</td>
		<td><?php echo $editor['name'].' '.$editor['lastname'] ?></td>
	</tr>
	<tr>
		<td>Organisation</td>
		<td><?php echo $editor['organisation'] ?></td>
	</tr>
	<tr>
		<td>Email</td>
		<td><a href="mailto:<?php echo $editor['email'] ?>"><?php echo $editor['email'] ?></a></td>
	</tr>
	<tr>
		<td>Téléphone</td>
		<td><?php echo $editor['phone'] ?></td>
	</tr>
	<tr>
		<td>Adresse</td>
		<td><?php echo $editor['address'] ?></td>
	</tr>
	<tr>
		<td>Code postal</td>
		<td><?php echo $editor['zip'] ?></td>
	</tr>
	<tr>
		<td>Ville</td>
		<td><?php echo $editor['city'] ?></td>
	</tr>
</table>

<?php } ?>