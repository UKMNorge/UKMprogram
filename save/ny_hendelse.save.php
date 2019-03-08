<?php

require_once('UKM/monstring.class.php');
require_once('UKM/write_forestilling.class.php');

$monstring = new monstring_v2( get_option('pl_id') );

$start_string = $_POST['start_date'] .'-'. $_POST['start_time'];
$start = DateTime::createFromFormat('d.m.Y-H:i', $start_string);

$hendelse = write_forestilling::create( 
	$monstring,
	$_POST['navn'],
	$start
);

$_POST['id'] = $hendelse->getId();

#UKMprogram::getFlashbag()->add('success', $_POST['navn'] .' opprettet');
require_once('hendelse.save.php');