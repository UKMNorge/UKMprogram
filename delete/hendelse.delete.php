<?php

use UKMNorge\Arrangement\Arrangement;
use UKMNorge\Arrangement\Program\Write;

UKMprogramLandsfestivalen::setupLogger();

$arrangement = new Arrangement(get_option('pl_id'));
$hendelse = $arrangement->getProgram()->get( $_GET['c_id'] );

try {
    Write::slett( $hendelse );
    UKMprogramLandsfestivalen::getFlashbag()->success('Hendelsen er slettet!');
} catch( Exception $e ) {
    UKMprogramLandsfestivalen::getFlashbag()->error('Klarte ikke å slette '. $hendelse->getNavn() .'. Systemet sa: '. $e->getMessage());
}