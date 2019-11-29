<?php

use UKMNorge\Arrangement\Arrangement;
use UKMNorge\Innslag\Typer\Typer;

require_once('UKM/Autoloader.php');

$monstring = new Arrangement( get_option('pl_id') );

// MULIGENS VIS VALG AV HENDELSER
if( !isset( $_GET['hendelser'] ) ) {

	$hendelser = [];
	foreach( $monstring->getProgram()->getAll() as $hendelse ) {
		if( $hendelse->getType() == 'default' ) {
			$hendelser[] = $hendelse->getId();
		}
	}
	
	// Hvis mønstringen har mer enn 6 hendelser, vis en selector først.
	// Det er uansett mulig å velge flere, men det er naturlig å anta
	// at man ikke ønsker å jobbe med alle disse på en gang.
	if( sizeof( $hendelser ) > 6 ) {
		UKMprogram::setAction('grovsort/select');
	} else {
		UKMprogram::setAction('grovsort/sorter');

		$typer = [];
		$innslag_typer = Typer::getAllTyper();
		foreach( $innslag_typer as $type ) {
			if( $type->getKey() == 'scene' ) {
				foreach( Typer::getAllScene() as $subtype ) {
					$typer[] = $subtype;
				}
			} else {
				$typer[] = $type;
			}
		}
		UKMprogram::addViewData('innslag_typer', $typer);
	}
} else {
	$hendelser = explode('-', $_GET['hendelser'] );
}

UKMprogram::addViewData('show', $hendelser);
UKMprogram::addViewData('monstring', $monstring);