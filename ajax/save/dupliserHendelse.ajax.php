<?php

use UKMNorge\Arrangement\Arrangement;
use UKMNorge\Arrangement\Program\Write;
use UKMNorge\Arrangement\Program\Hendelse;

require_once('UKM/Autoloader.php');

try {
	# Opprett hendelse fra gitt ID
	$monstring = new Arrangement( get_option('pl_id') );
	$hendelse = $monstring->getProgram()->get( $_POST['hendelse'] );

	// Sjekk at hendelsen tilhører den mønstringssiden vi er inne på
	if( $hendelse->getMonstring()->getId() != get_option('pl_id') ) {
		throw new Exception("Du kan kun duplisere dine egne hendelser!");
	}

	$ny_hendelse = Write::dupliser($hendelse);

	UKMprogram::addResponseData('success', true);
	UKMprogram::addResponseData('hendelse', $ny_hendelse);
} catch ( Exception $e ) {
	UKMprogram::addResponseData('success', false);
	UKMprogram::addResponseData('message', $e->getMessage());
}
