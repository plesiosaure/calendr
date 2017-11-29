<?php

	$id_orga = $data['organisateur']['_id'];

if($id_orga){

	$orga = $app->apiLoad('calendrierOrganisateur')->get(array(
		'_id'    => $id_orga
	));

	$city = $app->apiLoad('calendrierCity')->get(array(
		'_id'    => $orga['city']['_id'],
		'format' => array()
	));

	#$app->pre($city, $orga);
?>

<table id="editor">
	<tr>
		<td colspan="2" class="head">Coordonnées de l'organisateur</td>
	</tr>
	<tr>
		<td width="100">Nom</td>
		<td><?php echo $orga['firstname'].' '.$orga['lastname'] ?></td>
	</tr>
	<tr>
		<td>Organisation</td>
		<td><?php echo $orga['name'] ?></td>
	</tr>
	<tr>
		<td>Email</td>
		<td><a href="mailto:<?php echo $orga['email'] ?>"><?php echo $orga['email'] ?></a></td>
	</tr>
	<tr>
		<td>Téléphone</td>
		<td><?php echo $orga['phone'] ?></td>
	</tr>
	<tr>
		<td>Adresse</td>
		<td><?php echo $orga['address'] ?></td>
	</tr>
	<tr>
		<td>Code postal</td>
		<td><?php echo $city['zip'] ?></td>
	</tr>
	<tr>
		<td>Ville</td>
		<td><?php echo $city['name']; ?></td>
	</tr>
</table>

<?php }