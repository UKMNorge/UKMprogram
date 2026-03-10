<?php

use UKMNorge\Arrangement\Arrangement;
use UKMNorge\Arrangement\Aktivitet\Aktivitet;
use UKMNorge\Arrangement\Program\Write;
use UKMNorge\Innslag\Innslag;

require_once('UKM/Autoloader.php');

$arrangement = new Arrangement( get_option('pl_id') );
$hendelse = $arrangement->getProgram()->get( $_POST['hendelse'] );
$rekkefolgeData = json_decode(stripslashes($_POST['rekkefolge']), true);


$items = [];
foreach ($rekkefolgeData as $entry) {
    if ($entry['type'] === 'innslag') {
        $items[] = new Innslag((int) $entry['id']);
    } elseif ($entry['type'] === 'aktivitet') {
        $items[] = new Aktivitet((int) $entry['id']);
    }
}

Write::redefineOrder( $hendelse, $items );

UKMprogram::addResponseData('success', true);