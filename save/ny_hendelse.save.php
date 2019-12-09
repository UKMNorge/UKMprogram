<?php

use UKMNorge\Arrangement\Arrangement;
use UKMNorge\Arrangement\Program\Write;

require_once('UKM/Autoloader.php');

$monstring = new Arrangement( get_option('pl_id') );

$start_string = $_POST['start_date'] .'-'. $_POST['start_time'];
$start = DateTime::createFromFormat('d.m.Y-H:i', $start_string);

$hendelse = Write::create( 
	$monstring,
	$_POST['navn'],
	$start
);

$_POST['id'] = $hendelse->getId();

die('TODO: opprett side');

require_once('hendelse.save.php');