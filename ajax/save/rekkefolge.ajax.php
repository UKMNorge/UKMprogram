<?php

use UKMNorge\Arrangement\Arrangement;
use UKMNorge\Arrangement\Program\Write;

require_once('UKM/Autoloader.php');


$arrangement = new Arrangement( get_option('pl_id') );
$hendelse = $arrangement->getProgram()->get( $_POST['hendelse'] );

//$hendelse->$function( $_POST['state'] == 'true' );
//Write::save( $hendelse );

// Sett til true, fordi try-catch vil sette false hvis write feiler
UKMprogram::addResponseData('success', true);