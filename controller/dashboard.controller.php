<?php

use UKMNorge\Arrangement\Arrangement;
use UKMNorge\Arrangement\Program\Hendelser;
use UKMNorge\Meta\Write as WriteMeta;

require_once('UKM/Autoloader.php');

$arrangement = new Arrangement( get_option('pl_id') );
$program = Hendelser::sorterPerDag( 
	$arrangement->getProgram()->getAbsoluteAll()
);

if( isset($_GET['do']) && $_GET['do'] == 'changeView' && isset($_GET['to'])) {
    $setup = $arrangement->getMeta('program_editor')->set( $_GET['to'] );
    WriteMeta::set($setup);
}

UKMprogram::addViewData('arrangement', $arrangement);
UKMprogram::addViewData('program', $program);

// Inntil vi har forestillinger, vis veiviser
if( sizeof($program) == 0 ) {
    UKMprogram::setAction('veiviser');
    UKMprogram::includeActionController();
}
// Vi har forestillinger, og jobber med enkel setup
elseif( $arrangement->getMetaValue('program_editor') == 'enkel' ) {
    UKMprogram::setAction('hendelser/enkel');
}
// Vi har forestillinger, og jobber med avansert setup
else {
    UKMprogram::setAction('hendelser/avansert');
}