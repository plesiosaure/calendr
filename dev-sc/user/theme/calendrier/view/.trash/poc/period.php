<?php

$mon = new Mongo("mongodb://mongo01.kappuccino.net");
$db  = $mon->calendrier;
$col = $db->annonce;

$start  = new MongoDate(strtotime("2012-02-01 00:00:00"));
$end    = new MongoDate(strtotime("2012-02-01 00:00:00"));

$data = $col->find(array(
	'genre' => 'auto',
	'$or'   => array(
		array('start' => array('$gte' => $start)),
		array('end'   => array('$lte' => $end))
	)
));


$data = iterator_to_array($data);

$this->pre($data);

