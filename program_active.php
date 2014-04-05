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
	
	// Beregn varighet og stopp-tidspunkt
	$varighet = $forestilling->varighet();
	if( $varighet <= 60*30 ) {
		$varighet = 60*45;
	} else {
		$varighet = $varighet * 1.1;
	}
	$stop = $start + $varighet;
	
	// Start 8 min før
	$varighet += 60*8;
	$start = $start - 60*8;
	
	$perioder[] = (object) array('start' => $start, 'stop' => $stop);
}

var_dump( $perioder );

foreach( $perioder as $i => $data ) {
	echo '<h2>'. $i .'</h2>';
	echo 'start: '. date('d.m.Y H:i:s', $data->start)
		.'stop: '. date('d.m.Y H:i:s', $data->stop);
}