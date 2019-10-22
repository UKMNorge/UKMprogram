<?php

use UKMNorge\Arrangement\Arrangement;
use UKMNorge\Arrangement\Program\Hendelser;

require_once('UKM/Autoloader.php');

$monstring = new Arrangement( get_option('pl_id') );
$program = Hendelser::sorterPerDag( 
	$monstring->getProgram()->getAbsoluteAll()
);

UKMprogram::addViewData('monstring', $monstring);
UKMprogram::addViewData('program', $program);