<?php

use UKMNorge\Arrangement\Arrangement;
use UKMNorge\Arrangement\Program\Write;

require_once('UKM/Autoloader.php');


$arrangement = new Arrangement( get_option('pl_id') );
$hendelse = $arrangement->getProgram()->get( $_POST['hendelse'] );
$success = Write::setRekkefolgeMotsatt( $hendelse );

UKMprogram::addResponseData( 'success', $success );