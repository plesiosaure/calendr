<?php

	if(!defined('COREINC')) die('Direct access not allowed');
	$api  = $app->apiLoad('calendrierManifestation');
	$date = $app->apiLoad('calendrierManifestationDate');

	// FILTER //////////////////////////////////////////////////////////////////////////////////////////////////////////
	if(isset($_GET['reset'])){
		$app->filterReset('calendrier/manifestation');
		$app->go('./');
	}else
	if(isset($_GET['cf'])){
		$app->filterSet('calendrier/manifestation', $_GET);
		$filter = array_merge($app->filterGet('calendrier/manifestation'), $_GET);
	}else
	if(isset($_POST['filter'])){
		$app->filterSet('calendrier/manifestation', $_POST['filter']);
		$filter = array_merge($app->filterGet('calendrier/manifestation'), $_POST['filter']);
	}else{
		$filter = $app->filterGet('calendrier/manifestation');
	}

	$dir = ($filter['dir'] == '1') ? '-1' : '1';
	$mod =  $filter['mode'] ?: 'wait_moderation';
	$opt = array(
		'debug'  => 0,
		'search' => $filter['q'],
		'mode'   => $mod,
		'type'   => $filter['type'],
		'range'  => array('start' => $filter['start'], 'end' => $filter['end']),
		'limit'  => ($filter['limit'] ? : 30),
		'offset' => $filter['offset'],
		'sort'   => $filter['sort'],
		'dir'    => $filter['dir'],
		'format' => array('organisateur')
	);

	$data = $api->get($opt);

	$types = array(
		'1' => 'Collection',
		'2' => 'Auto',
		'3' => 'Moto'
	);

?><!DOCTYPE html>
<html lang="fr">
<head>
	<base href="../" />
	<?php include(COREINC . '/head.php'); ?>
<!--	<link rel="stylesheet" type="text/css" href="ui/css/all.css" />
	<link rel="stylesheet" type="text/css" href="ui/css/main.css" />
-->	<link rel="stylesheet" type="text/css" href="ui/vendor/bootstrap-datepicker/css/datepicker.css" />
</head>
<body>

<header><?php
	include(COREINC . '/top.php');
	include(dirname(__DIR__). '/ui/menu.php')
?></header>

<div id="app"><div class="row-fluid">

		<div class="tabset" style="margin:0;">
			<div class="view" id="tab-search">
				<form id="search" action="moderation/" method="post">
					<input type="hidden" name="filter[offset]" value="0" />

					<table border="0" cellpadding="0" cellspacing="0" class="form">
						<tr>
							<td><input type="search" name="filter[q]" value="<?php echo $filter['q'] ?>" size="30" placeholder="Recherche" /></td>
							<td><input type="text" name="filter[limit]" value="<?php echo $filter['limit'] ?>" size="6" placeholder="Nombre" /></td>
							<td>
								<input type="text" name="filter[start]" id="date-start" value="<?php echo $filter['start'] ?>" size="12" placeholder="Commence" />
								<input type="text" name="filter[end]"   id="date-end"   value="<?php echo $filter['end'] ?>"   size="12" placeholder="Se termine" />
							</td>
							<td><select name="filter[type]">
									<option></option><?php
									$a = $app->apiLoad('calendrierManifestationType');
									foreach($a->get() as $key => $value){
										$sel = ($key == $filter['type']) ? ' selected' : NULL;
										echo '<option value="'.$key.'"'.$sel.'>'.$a->name($key).'</option>';
									}
								?></select>
							</td>
							<td>
								A modérer
								<input type="radio" name="filter[mode]" value="wait_moderation" <?php if($mod == 'wait_moderation') echo 'checked' ?> >
								<input type="radio" name="filter[mode]" value="wait_email" <?php if($mod == 'wait_email') echo 'checked' ?> >
								Email non répondu
							</td>
							<td>
								<a class="btn btn-small" onclick="$('#search').submit();">Rechercher</a>
							</td>
						</tr>
					</table>


				</form>
			</div>
		</div>

		<div class="">

		<?php if(sizeof($data) > 0){ ?>

			<form method="post" action="manifestation/" id="listing">
				<table border="0" cellpadding="0" cellspacing="0" class="listing">
					<thead>
						<tr>
							<th            class=""><span>Mode</span></th>
							<th            class="order <?php if($filter['sort'] == 'name')    echo 'order'.$dir; ?>" onClick="document.location='manifestation/?cf&sort=name&dir=<?php echo $dir ?>'"><span>Intitulé</span></th>
							<th            class=""><span>Type</span></th>
							<th            class=""><span>Dept</span></th>
							<th            class=""><span>Ville</span></th>
							<th	width="75" class="order <?php if($filter['sort'] == 'start')   echo 'order'.$dir; ?>" onClick="document.location='manifestation/?cf&sort=date.start&dir=<?php echo $dir ?>'"><span>Commence</span></th>
							<th width="75" class="order <?php if($filter['sort'] == 'end')     echo 'order'.$dir; ?>" onClick="document.location='manifestation/?cf&sort=date.end&dir=<?php echo $dir ?>'"><span>Fin</span></th>
							<th            class=""><span>Organisateur</span></th>
							<th            class=""><span>Organisation</span></th>
							<th            class=""><span>Téléphone</span></th>
							<th            class=""><span>Email</span></th>
							<th width="20" class=""><i class="icon-envelope icon-white"></i></th>
							<th width="70"></th>
						</tr>
					</thead>
					<tbody>
					<?php foreach($data as $e){
							$oldest  = $date->oldest($e['date']);   $oldest  = (!$oldest)  ? '-' : strftime('%d %b %y', $oldest);
							$lastest = $date->lastest($e['date']);  $lastest = (!$lastest) ? '-' : strftime('%d %b %y', $lastest);
						?>
						<tr>
							<td><?php

								if($e['moderation'] == 'remove'){
									echo '[SUP]';
								}else
								if($e['moderation'] == 'update'){
									echo '[MAJ]';
								}else{
									echo '[NEW]';
								}

								?></td>
							<td><?php echo $e['name'] ?: $e['temp']['name'] ?></td>
							<td><?php echo $types[$e['mvs']['type']]; ?></td>
							<td><?php echo $e['temp']['city']['dep']; ?></td>
							<td><?php echo $e['temp']['city']['name']; ?></td>
							<td><?php echo $oldest ?></td>
							<td><?php echo $lastest; ?></td>
							<td><?php echo $e['organisateur']['lastname'].' '.$e['organisateur']['firstname']; ?></td>
							<td><?php echo $e['organisateur']['name']; ?></td>
							<td><?php echo $e['organisateur']['phone']; ?></td>
							<td><?php echo $e['organisateur']['email']; ?></td>
							<td><?php if($e['mode'] == 'wait_moderation'){ ?><i class="icon-ok"></i><?php } ?></td>
							<td>
								<a href="<?php echo $api->manifestationPermalink($e) ?>" target="_blank" class="icon-eye-open"></a>
								&nbsp;
								<a href="moderation/data?_id=<?php echo $e['_id'] ?>" class="icon-pencil"></a>
								&nbsp;
								<a href="moderation/contact?_id=<?php echo $e['_id'] ?>" class="icon-comment"></a>
							</td>
						</tr>
					<?php } ?>
					</tbody>
					<tfoot>
						<tr>
							<td colspan="3" class="pagination">
								<?php $app->pagination($api->total, $api->limit, $filter['offset'], 'manifestation/?cf&offset=%s'); ?>
							</td>
						</tr>
					</tfoot>
				</table>
			</form>

		<?php }else{ ?>
			<div class="message messageNotFound" style="margin-top: 5px">Pas de résultat</div>
		<?php } ?>
		</div>

</div></div>


<?php include(COREINC.'/end.php'); ?>
<script type="text/javascript" src="ui/vendor/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
<script type="text/javascript" src="manifestation/ui/js/manifestation.js"></script>

</body>
</html>