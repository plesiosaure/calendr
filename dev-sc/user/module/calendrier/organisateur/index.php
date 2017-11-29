<?php

	if(!defined('COREINC')) die('Direct access not allowed');
	$api  = $app->apiLoad('calendrierOrganisateur');
	$sync = $app->apiLoad('calendrierMvsSync');

	// REMOVE //////////////////////////////////////////////////////////////////////////////////////////////////////////
	if(isset($_GET['remove'])){
		$mvs = $sync->organisateurDelete(array('_id' => $_GET['remove']));
		if($mvs) $api->del(array('_id' => $_GET['remove']));
		$app->go('./');
	}else
	if(count($_POST['off']) > 0){
		foreach($_POST['off'] as $e){
		#	$mvs = $sync->organisateurDelete(array('_id' => $_GET['remove']));
		#	if($mvs) $api->del(array('_id' => $e));
		}
		$app->go('./');
	}

	// FILTER //////////////////////////////////////////////////////////////////////////////////////////////////////////
	if(isset($_GET['reset'])){
		$app->filterReset('calendrier/organisateur');
		$app->go('./');
	}else
	if(isset($_GET['cf'])){
		$app->filterSet('calendrier/organisateur', $_GET);
		$filter = array_merge($app->filterGet('calendrier/organisateur'), $_GET);
	}else
	if(isset($_POST['filter'])){
		$app->filterSet('calendrier/organisateur', $_POST['filter']);
		$filter = array_merge($app->filterGet('calendrier/organisateur'), $_POST['filter']);
	}else{
		$filter = $app->filterGet('calendrier/organisateur');
	}

	$dir = ($filter['dir'] == '1') ? '-1' : '1';

	$data = $api->get(array(
		'debug'     => false,
		'search'    => $filter['q'],
		'type'      => $filter['type'],
		'range'     => array('start' => $filter['start'], 'end' => $filter['end']),
		'limit'     => $filter['limit'],
		'offset'    => $filter['offset'],
		'sort'      => $filter['sort'],
		'dir'       => $filter['dir']
	));

?><!DOCTYPE html>
<html lang="fr">
<head>
	<base href="../" />
	<?php include(COREINC . '/head.php'); ?>
	<link rel="stylesheet" type="text/css" href="ui/css/main.css" />
	<link rel="stylesheet" type="text/css" href="ui/css/all.css" />
</head>
<body>

<header><?php
	include(COREINC . '/top.php');
	include(dirname(__DIR__). '/ui/menu.php')
?></header>

<div class="inject-subnav-right hide">
	<li><a href="organisateur/data" class="btn btn-small btn-success">Nouvel organisateur</a></li>
</div>

<div id="app"><div class="row-fluid">

		<div class="tabset span3" style="margin:0;">
			<div class="view" id="tab-search">
				<form id="search" action="organisateur/" method="post">
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
							<td colspan="2">
								<div class="btn-group-center"><div class="btn-group">
									<a class="btn btn-small" onclick="$('#search').submit();">Rechercher</a>
									<a class="btn btn-small" href="organisateur/data">Nouveau</a>
									<a class="btn btn-small" href="organisateur/?reset">Annuler</a>
								</div></div>
							</td>
						</tr>
					</table>

				</form>
			</div>
		</div>

		<div class="span9">
		<?php if(sizeof($data) > 0){ ?>

			<form method="post" action="organisateur/" id="listing">
				<table border="0" cellpadding="0" cellspacing="0" class="listing">
					<thead>
						<tr>
							<th width="20"  class="icone"><i class="icon-remove icon-white"></i></th>
							<th             class="order <?php if($filter['sort'] == 'name')   echo 'order'.$dir; ?>" onClick="document.location='organisateur/?cf&sort=name&dir=<?php echo $dir ?>'"><span>Nom</span></th>
							<th width="100"></th>
						</tr>
					</thead>
					<tbody>
					<?php foreach($data as $e){ ?>
						<tr>
							<td class="icone"><input type="checkbox" name="off[]" value="<?php echo $e['_id'] ?>" class="cb" /></td>
							<td><a href="organisateur/data?_id=<?php echo $e['_id'] ?>"><?php echo $e['name'] ?></a></td>
							<td></td>
						</tr>
					<?php } ?>
					</tbody>
					<tfoot>
						<tr>
							<td height="25"><input type="checkbox" onchange="cbchange(this);" /></td>
							<td><a onClick="off();" class="btn btn-mini">Désactiver la selection</a></td>
							<td class="pagination" align="right"><?php $app->pagination($api->total, $api->limit, $filter['offset'], 'organisateur/?cf&offset=%s'); ?></td>
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

</body>
</html>