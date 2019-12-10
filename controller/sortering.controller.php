<?php

use UKMNorge\Arrangement\Arrangement;
use UKMNorge\Innslag\Typer\Typer;

require_once('UKM/Autoloader.php');

$arrangement = new Arrangement( get_option('pl_id') );
UKMprogram::setAction('grovsort/sorter');

// MULIGENS VIS VALG AV HENDELSER
if( !isset( $_GET['hendelser'] ) ) {
	$hendelser = [];
	foreach( $arrangement->getProgram()->getAbsoluteAll() as $hendelse ) {
		if( $hendelse->getType() == 'default' ) {
			$hendelser[] = $hendelse->getId();
		}
	}
	
	// Hvis mønstringen har mer enn 6 hendelser, vis en selector først.
	// Det er uansett mulig å velge flere, men det er naturlig å anta
	// at man ikke ønsker å jobbe med alle disse på en gang.
	if( sizeof( $hendelser ) > 2 ) {
		UKMprogram::setAction('grovsort/select');
	} else {
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
    UKMprogram::addViewData('numHendelser', sizeof($hendelser));
} else {
	$hendelser = explode('-', $_GET['hendelser'] );
}

UKMprogram::addViewData('show', $hendelser);
UKMprogram::addViewData('arrangement', $arrangement);