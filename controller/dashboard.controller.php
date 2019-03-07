<?php

require_once('UKM/monstring.class.php');
require_once('UKM/forestillinger.collection.php');

$monstring = new monstring_v2( get_option('pl_id') );
$program = program::sorterPerDag( 
	$monstring->getProgram()->getAbsoluteAll()
);

UKMprogram::addViewData('monstring', $monstring);
UKMprogram::addViewData('program', $program);