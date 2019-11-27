<?php

use UKMNorge\Arrangement\Arrangement;
use UKMNorge\Arrangement\Program\Hendelser;

require_once('UKM/Autoloader.php');

$arrangement = new Arrangement( get_option('pl_id') );
$program = Hendelser::sorterPerDag( 
	$arrangement->getProgram()->getAbsoluteAll()
);

UKMprogram::addViewData('arrangement', $arrangement);
UKMprogram::addViewData('program', $program);

if( sizeof($program) == 0 ) {
    UKMprogram::setAction('veiviser');
    UKMprogram::includeActionController();
}