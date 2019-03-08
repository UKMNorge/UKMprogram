<?php

if( !isset( $function ) ) {
	throw new Exception('INC-fil mangler $function-parameter');
}

require_once('UKM/monstring.class.php');
require_once('UKM/write_forestilling.class.php');

$monstring = new monstring_v2( get_option('pl_id') );
$hendelse = $monstring->getProgram()->get( $_POST['hendelse'] );

$hendelse->$function( $_POST['state'] == 'true' );

write_forestilling::save( $hendelse );

// Sett til true, fordi try-catch vil sette false hvis write feiler
UKMprogram::addResponseData('success', true);