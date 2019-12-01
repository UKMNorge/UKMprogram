<?php

use UKMNorge\Arrangement\Arrangement;
use UKMNorge\Arrangement\Program\Write;
use UKMNorge\Meta\Write as WriteMeta;

$arrangement = new Arrangement( get_option('pl_id') );

if( $_SERVER['request_method'] == 'POST' ) {
    $setup = $arrangement->getMeta('program_editor')->set( in_array($_POST['antall'],['en','to']) ? 'enkel' : 'avansert' );
    WriteMeta::set($setup);

    $utstilling = false;

    switch( $_POST['antall'] ) {
        case 'to':
            $utstilling = Write::create(
                $arrangement,
                'Utstilling',
                $arrangement->getStart()
            );
        case 'en':
            $hendelse = Write::create(
                $arrangement,
                'Forestilling',
                $arrangement->getStart()
            );
        break;
    }

    if( $_POST['leggtil'] == 'yes' ) {
        // Reload utstilling og hendelse for å få riktig kontekst
        if( $utstilling ) {
            $utstilling = $arrangement->getProgram()->get($utstilling->getId());
        }
        $hendelse = $arrangement->getProgram()->get( $hendelse->getId() );

        foreach( $arrangement->getInnslag()->getAll() as $innslag ) {
            if( $_POST['antall'] == 'to' && $innslag->getType()->getKey() == 'utstilling' ) {
                $utstilling->getInnslag()->leggTil( $innslag );
                Write::leggTil( $utstilling, $innslag );
            } else {
                $hendelse->getInnslag()->leggTil( $innslag );
                Write::leggTil( $hendelse, $innslag );
            }
        }
    }
}

UKMprogram::setAction('dashboard');
UKMprogram::includeActionController();