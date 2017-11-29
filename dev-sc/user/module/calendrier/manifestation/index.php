<?php

	if(!defined('COREINC')) die('Direct access not allowed');
	$api  = $app->apiLoad('calendrierManifestation');
	$date = $app->apiLoad('calendrierManifestationDate');
	$sync = $app->apiLoad('calendrierMvsSync');

	// CANCEL //////////////////////////////////////////////////////////////////////////////////////////////////////////
	if(isset($_GET['cancel'])){
		$api->cleanBackup($_GET['cancel']);
		$app->go('./');
	}


	// REMOVE //////////////////////////////////////////////////////////////////////////////////////////////////////////
	if(isset($_GET['remove'])){
		$mvs = $sync->manifestationDelete(array('_id' => $_GET['remove']));
		if($mvs) $api->del(array('_id' => $_GET['remove']));
		$app->go('./');
	}else
	if(count($_POST['off']) > 0){
		foreach($_POST['off'] as $e){
			$api->off(array('_id' => $e));
		}
		$app->go('./');
	}

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
	$mod = ($filter['moderate'] == 'yes');
	$opt = array(
		'debug'  => 0,
		'search' => $filter['q'],
		'type'   => $filter['type'],
		'range'  => array('start' => $filter['start'], 'end' => $filter['end']),
		'limit'  => ($filter['limit'] ? : 30),
		'offset' => $filter['offset'],
		'sort'   => $filter['sort'],
		'dir'    => $filter['dir']
	);

	if($mod) $opt['hasBackup'] = $mod;

	$data = $api->get($opt);

?><!DOCTYPE html>
<html lang="fr">
<head>
	<base href="../" />
	<?php include(COREINC . '/head.php'); ?>
	<link rel="stylesheet" type="text/css" href="ui/css/all.css" />
	<link rel="stylesheet" type="text/css" href="ui/css/main.css" />
	<link rel="stylesheet" type="text/css" href="ui/vendor/bootstrap-datepicker/css/datepicker.css" />
</head>
<body>

<header><?php
	include(COREINC . '/top.php');
	include(dirname(__DIR__). '/ui/menu.php')
?></header>

<div class="inject-subnav-right hide">
	<li><a href="manifestation/data" class="btn btn-small btn-success">Nouvelle manifestation</a></li>
</div>

<div id="app"><div class="row-fluid">

		<div class="tabset span3" style="margin:0;">
			<div class="view" id="tab-search">
				<form id="search" action="manifestation/" method="post">
					<input type="hidden" name="filter[offset]" value="0" />

					<table border="0" cellpadding="0" cellspacing="0" class="form">
						<tr>
							<td width="100">Recherche</td>
							<td><input type="search" name="filter[q]" value="<?php echo $filter['q'] ?>" size="30" /></td>
						</tr>
						<tr>
							<td>Combien</td>
							<td><input type="text" name="filter[limit]" value="<?php echo $filter['limit'] ?>" size="3" /></td>
						</tr>
						<tr>
							<td>Date</td>
							<td>
								<input type="text" name="filter[start]" id="date-start" value="<?php echo $filter['start'] ?>" size="12" placeholder="Commence" />
								<input type="text" name="filter[end]"   id="date-end"   value="<?php echo $filter['end'] ?>"   size="12" placeholder="Se termine" />
							</td>
						</tr>
						<tr>
							<td>Type</td>
							<td><select name="filter[type]">
									<option></option><?php
									$a = $app->apiLoad('calendrierManifestationType');
									foreach($a->get() as $key => $value){
										$sel = ($key == $filter['type']) ? ' selected' : NULL;
										echo '<option value="'.$key.'"'.$sel.'>'.$a->name($key).'</option>';
									}
								?></select>
							</td>
						</tr>
						<tr>
							<td>A modérer</td>
							<td>
								<input type="hidden" name="filter[moderate]" value="">
								<input type="checkbox" name="filter[moderate]" value="yes" <?php if($mod) echo 'checked' ?> >
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<div class="btn-group-center"><div class="btn-group">
									<a class="btn btn-small" onclick="$('#search').submit();">Rechercher</a>
									<a class="btn btn-small" href="manifestation/data">Nouveau</a>
									<a class="btn btn-small" href="manifestation/?reset">Tout afficher</a>
								</div></div>
							</td>
						</tr>
					</table>

				</form>
			</div>
		</div>

		<div class="span9">

		<?php if(isset($_GET['_id'])){ ?>
			<div class="message messageValid">
				Vous venez d'éditer <a href="manifestation/data?_id=<?php echo $_GET['_id'] ?>">une manifestation</a>
			</div>
		<?php } ?>

		<?php if(sizeof($data) > 0){ ?>

			<form method="post" action="manifestation/" id="listing">
				<table border="0" cellpadding="0" cellspacing="0" class="listing">
					<thead>
						<tr>
							<th width="20" class="icone"><i class="icon-remove icon-white"></i></th>
							<th width="20" class="icone"><i class="icon-calendar icon-white"></i></th>
							<th width="75" class="order <?php if($filter['sort'] == 'created') echo 'order'.$dir; ?>" onClick="document.location='manifestation/?cf&sort=created&dir=<?php echo $dir ?>'"><span>Création</span></th>
							<th	width="75" class="order <?php if($filter['sort'] == 'start')   echo 'order'.$dir; ?>" onClick="document.location='manifestation/?cf&sort=date.start&dir=<?php echo $dir ?>'"><span>Commence</span></th>
							<th width="75" class="order <?php if($filter['sort'] == 'end')     echo 'order'.$dir; ?>" onClick="document.location='manifestation/?cf&sort=date.end&dir=<?php echo $dir ?>'"><span>Fin</span></th>
							<th            class="order <?php if($filter['sort'] == 'name')    echo 'order'.$dir; ?>" onClick="document.location='manifestation/?cf&sort=name&dir=<?php echo $dir ?>'"><span>Intitulé</span></th>
							<th width="20" class="icone" style="text-align:right;"><i class="icon-retweet icon-white"></i></th>
						</tr>
					</thead>
					<tbody>
					<?php foreach($data as $e){
							$create  = strftime('%d %b %y', $e['created']->sec);
							$oldest  = $date->oldest($e['date']);   $oldest  = (!$oldest)  ? '-' : strftime('%d %b %y', $oldest);
							$lastest = $date->lastest($e['date']);  $lastest = (!$lastest) ? '-' : strftime('%d %b %y', $lastest);
						?>
						<tr>
							<td class="icone"><input type="checkbox" name="off[]" value="<?php echo $e['_id'] ?>" class="cb" /></td>
							<td class="icone"><a href="manifestation/date?_id=<?php echo $e['_id']; ?>"><i class="icon-calendar"></i></a></td>
							<td><a href="manifestation/data?_id=<?php echo $e['_id'] ?>"><?php echo $create ?></a></td>
							<td><a href="manifestation/data?_id=<?php echo $e['_id'] ?>"><?php echo $oldest ?></a></td>
							<td><a href="manifestation/data?_id=<?php echo $e['_id'] ?>"><?php echo $lastest; ?></a></td>
							<td><a href="manifestation/data?_id=<?php echo $e['_id'] ?>"><?php echo $e['name'] ?></a></td>
							<td align="right"><?php
								echo array_key_exists('backup', $e)
									? '<a href="manifestation/diff?_id='.$e['_id'].'"><i class="icon-retweet"></i></a>'
									: ''
							?></td>
						</tr>
					<?php } ?>
					</tbody>
					<tfoot>
						<tr>
							<td height="25"><input type="checkbox" onchange="$('input.cb').attr('checked',this.checked);" /></td>
							<td colspan="5"><a onClick="off();" class="btn btn-mini">Désactiver la selection</a></td>
							<td class="pagination"><?php $app->pagination($api->total, $api->limit, $filter['offset'], 'manifestation/?cf&offset=%s'); ?></td>
						</tr>
					</tfoot>
				</table>
			</form>

		<?php }else{ ?>
			<div class="message messageNotFound">Pas de résultat</div>
		<?php } ?>
		</div>

</div></div>


<?php include(COREINC.'/end.php'); ?>
<script type="text/javascript" src="ui/vendor/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
<script type="text/javascript" src="manifestation/ui/js/manifestation.js"></script>

</body>
</html>