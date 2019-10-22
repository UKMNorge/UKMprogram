<?php

require_once('UKM/monstring.class.php');
require_once('UKM/forestilling.class.php');

$perioder = array();

$m = new monstring( get_option('pl_id') );

$hendelser = $m->forestillinger();

foreach( $hendelser as $c ) {
	$h = new forestilling( $c['c_id'] );
	$innslag = $h->innslag();
	$start = $h->get('c_start');
	$navn = $h->get('c_name');
	
	// Beregn varighet og stopp-tidspunkt
	$varighet = $h->varighet();
	$stop = $start + $varighet + (60*5);
	
	// Start 5 min før
	$start = $start - 60*5;
	
	if( $varighet > (60*5) ) {
		$perioder[] = (object) array('navn' => $navn, 'start' => $start, 'stop' => $stop);
	}
}

update_option('ukm_hendelser_perioder', $perioder);