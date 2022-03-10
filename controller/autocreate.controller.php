<?php

use UKMNorge\Arrangement\Arrangement;
use UKMNorge\Arrangement\Program\Write;
use UKMNorge\Meta\Write as WriteMeta;

$arrangement = new Arrangement(get_option('pl_id'));


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    UKMprogram::setAction('dashboard');

    WriteMeta::set(
        $arrangement->getMeta('program_editor')->set('enkel')
    );

    $hendelser = [
        'utstilling' => 'Utstilling',
        'arrangor' => 'Arrangører',
        'nettredaksjon' => 'Media'
    ];

    // Finn ut om standard-hendelsene skal opprettes
    foreach ($hendelser as $key => $create) {
        if (!isset($_POST[$key]) || (isset($_POST[$key]) && $_POST[$key] == 'no')) {
            $hendelser[$key] = false;
        }
    }


    switch ($_POST['antall']) {
        case 'en':
            $hendelser['forestilling'] = 'Forestilling';
            break;
        case 'noen':
            if ($_POST['forestilling'] == 'en') {
                $hendelser['forestilling'] = 'Forestilling';
            } else {
                $hendelser['for_pause'] = 'Før pause';
                $hendelser['etter_pause'] = 'Etter pause';
            }
            break;
        case 'flere':
            // Viktig at "flere" går videre, og ikke stopper i default
            // break;
        default:
            $_GET['id'] = 'new';
            UKMprogram::setAction('hendelse');
            break;
    }

    // Opprett hendelser som skal opprettes
    foreach( $hendelser as $key => $name ) {
        if ($name) {
            if( $key == 'etter_pause' ) {
                $start = $arrangement->getStart()->modify('+90 minutes');
            } else {
                $start = $arrangement->getStart();
            }
            $hendelse = Write::create(
                $arrangement,
                $name,
                $start
            );

            // Reset program, da load ikke kjøres for iterasjon 2 
            // (loaded er loaded, nemlig. Gjør ikke det igjen, for å si det sånn)
            $arrangement->resetProgram();
            // Reload for å få riktig kontekst (egentlig burde vel dette skjedd @ create?)
            $$key = $arrangement->getProgram()->get($hendelse->getId());
        } else {
            $$key = false;
        }
    }

    if ($_POST['leggtil'] == 'yes') {
        $count_forestilling_innslag = 0;

        foreach ($arrangement->getInnslag()->getAll() as $innslag) {
            $type_key = $innslag->getType()->getKey();
            switch( $type_key ) {
                case 'utstilling':
                case 'arrangor':
                case 'nettredaksjon':
                    if( $$type_key ) {
                        $$type_key->getInnslag()->leggTil($innslag);
                        Write::leggTil($$type_key, $innslag);
                    }
                break;
                default:
                    $count_forestilling_innslag++;
                    // Hvis vi har bare én forestilling
                    if( $forestilling ) {
                        $forestilling->getInnslag()->leggTil($innslag);
                        Write::leggTil($forestilling, $innslag);
                    }
                    // Hvis vi har flere
                    elseif( $for_pause && $etter_pause ) {
                        if( $count_forestilling_innslag < ($arrangement->getInnslag()->getAntall() / 2)) {
                            $for_pause->getInnslag()->leggTil($innslag);
                            Write::leggTil($for_pause, $innslag);
                        } else {
                            $etter_pause->getInnslag()->leggTil($innslag);
                            Write::leggTil($etter_pause, $innslag);
                        }
                    }
                break;
            }
        }
    }
} else {
    UKMprogram::setAction('dashboard');
}
UKMprogram::includeActionController();
