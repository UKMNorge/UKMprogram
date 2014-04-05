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

	/*
if( $varighet <= 60*30 ) {
		$varighet = 60*45;
	} else {
		$varighet = $varighet * 1.2;
	}
*/
	$stop = $start + $varighet;
	
	// Start 3 min før
	$varighet += 60*3;
	$start = $start - 60*3;
	
	$perioder[] = (object) array('navn' => $navn, 'start' => $start, 'stop' => $stop);
}

/*
foreach( $perioder as $i => $data ) {
	echo '<h2>'. $i .' - '. $data->navn.'</h2>';
	echo 'start: '. date('d.m.Y H:i:s', $data->start)
		.'stop: '. date('d.m.Y H:i:s', $data->stop)
		.'varig: '. $data->varighet;
}
*/

update_option('ukm_hendelser_perioder', $perioder);