<?php

	if(!defined('COREINC')) die('Direct access not allowed');
	$api  = $app->apiLoad('calendrierLog');

	// ACTION //////////////////////////////////////////////////////////////////////////////////////////////////////////
	if(isset($_GET['flush'])){
		$api->clear();
		$app->go('./');
	}

	// FILTER //////////////////////////////////////////////////////////////////////////////////////////////////////////
	if(isset($_GET['reset'])){
		$app->filterReset('apilog/index');
		$app->go('./');
	}else
	if(isset($_GET['cf'])){
		$app->filterSet('apilog/index', $_GET);
		$filter = array_merge($app->filterGet('apilog/index'), $_GET);
	}else
	if(isset($_POST['filter'])){
		$app->filterSet('apilog/index', $_POST['filter']);
		$filter = array_merge($app->filterGet('apilog/index'), $_POST['filter']);
	}else{
		$filter = $app->filterGet('apilog/index');
	}

	$dir = ($filter['dir'] == '1') ? '-1' : '1';
	$err = $filter['error'] ?: 'all';

	$opt = array(
		'debug'    => 0,
		'search'   => $filter['q'],
		'range'    => array('start' => $filter['start'], 'end' => $filter['end']),
		'limit'    => $filter['limit'],
		'offset'   => $filter['offset'],
		'sort'     => $filter['sort'],
		'dir'      => $filter['dir'],
		'format'   => array(),
	);

	if(!empty($filter['id_manif']))	$opt['id_manif'] = $filter['id_manif'];

	if($err == 'yes' OR $err == 'not') $opt['error'] = $err == 'yes';

	$data = $api->get($opt);

?><!DOCTYPE html>
<html lang="fr">
<head>
	<base href="../" />
	<?php include(COREINC . '/head.php'); ?>
	<link rel="stylesheet" type="text/css" href="ui/css/main.css" />
	<link rel="stylesheet" type="text/css" href="ui/css/all.css" />
	<link rel="stylesheet" type="text/css" href="ui/vendor/bootstrap-datepicker/css/datepicker.css" />
</head>
<body>

<header><?php
	include(COREINC . '/top.php');
	include(dirname(__DIR__). '/ui/menu.php')
?></header>

<div id="app"><div class="row-fluid">

		<div class="tabset span3" style="margin:0;">
			<div class="view" id="tab-search">
				<form id="search" action="apilog/" method="post">
					<input type="hidden" name="filter[offset]" value="0" />

					<table border="0" cellpadding="0" cellspacing="0" class="form">
						<tr>
							<td width="100">Recherche (URL)</td>
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
							<td>En erreur</td>
							<td>
								<input type="radio" name="filter[error]" value="all" <?php if($err == 'all') echo 'checked' ?> > -
								<input type="radio" name="filter[error]" value="yes" <?php if($err == 'yes') echo 'checked' ?> > Oui
								<input type="radio" name="filter[error]" value="not" <?php if($err == 'not') echo 'checked' ?> > Non
							</td>
						</tr>
						<tr>
							<td width="100">ID SuperCal</td>
							<td><input type="text" name="filter[id_manif]" value="<?php echo $filter['id_manif'] ?>" size="30" /></td>
						</tr>
						<tr>
							<td colspan="2">
								<div class="btn-group-center"><div class="btn-group">
									<a class="btn btn-small" onclick="$('#search').submit();">Rechercher</a>
									<a class="btn btn-small" href="apilog/?reset">Tout afficher</a>
									<a class="btn btn-small btn-danger" onclick="emptyCollection()" style="color:#fff">Vider le log</a>
								</div></div>
							</td>
						</tr>
					</table>

				</form>
			</div>
		</div>

		<div class="span9">
		<?php if(sizeof($data) > 0){ ?>

			<table border="0" cellpadding="0" cellspacing="0" class="listing">
				<thead>
				<tr>
					<th width="75">Origine</th>
					<th width="120" class="order <?php if ($filter['sort'] == 'create') echo 'order' . $dir; ?>" onClick="document.location='apilog/?cf&sort=date&dir=<?php echo $dir ?>'"><span>Date</span></th>
					<th width="120"><span>Temps (sec)</span></th>
					<th><span>URL</span></th>
					<th data-width="400"><span>Résultat</span></th>
					<th width="5"></th>
				</tr>
				</thead>
				<tbody>
				<?php foreach ($data as $e) {
					$date = strftime('%Y/%m/%d %H:%M:%S', $e['date']);
					$link = 'apilog/view?_id=' . $e['_id'];
					?>
					<tr>
						<td><code><?php echo strtoupper($e['from']); ?></code></td>
						<td><a href="<?php echo $link ?>"><?php echo $date ?></a></td>
						<td><a href="<?php echo $link ?>"><?php echo $e['time']; ?></a></td>
						<td><a href="<?php echo $link ?>"><?php echo '<code>' . $e['method'] . '</code> ' . $e['url'] ?></a></td>
						<td><?php echo (!$e['success']) ? $e['raw']['msg'] : '<i class="icon icon-ok-sign"></i>'; ?></td>
						<td>
							<?php if($e['success'] === false) { ?>
							<a href="apilog/replay?_id=<?php echo $e['_id'] ?>"><i class="icon icon-refresh"></i></a>
							<?php } #$app->pre($e); ?>
						</td>
					</tr>
				<?php } ?>
				</tbody>
				<tfoot>
					<tr>
						<td colspan="3" class="pagination">
							<?php $app->pagination($api->total, $api->limit, $filter['offset'], 'apilog/?cf&offset=%s'); ?>
						</td>
					</tr>
				</tfoot>
			</table>

		<?php }else{ ?>
			<div class="message messageNotFound">Pas de donnée</div>
		<?php } ?>
		</div>

</div></div>


<?php include(COREINC.'/end.php'); ?>
<script type="text/javascript" src="ui/vendor/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
<script type="text/javascript" src="apilog/ui/js/apilog.js"></script>

</body>
</html>