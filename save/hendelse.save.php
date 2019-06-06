<?php

require_once('UKM/write_forestilling.class.php');

$start_string = $_POST['start_date'] .'-'. $_POST['start_time'];
$start = DateTime::createFromFormat('d.m.Y-H:i', $start_string);

$hendelse = new forestilling_v2( $_POST['id'] );

// BASIS-INFO
$hendelse->setNavn( $_POST['navn'] );
$hendelse->setSted( $_POST['sted'] );
$hendelse->setStart( $start );
$hendelse->setIntern( $_POST['intern'] == 'true' );
$hendelse->setType( $_POST['type'] );

// HVIS HENDELSEN HAR BESKRIVELSE
if( isset( $_POST['beskrivelse'] ) ) {
	$hendelse->setBeskrivelse( $_POST['beskrivelse'] );
}

// HVIS TYPE:POST
if( isset( $_POST['post_id'] ) ) {
	$hendelse->setTypePostId( $_POST['post_id'] );
}
// HVIS TYPE:KATEGORI
if( isset( $_POST['category_id'] ) ) {
	$hendelse->setTypeCategoryId( $_POST['category_id'] );
}

// EVT ANGI OPPMØTETID
if( isset( $_POST['angi_oppmote'] ) && $_POST['angi_oppmote'] == 'true' ) {
	$hendelse->setOppmoteFor( $_POST['oppmote_for'] );
	$hendelse->setOppmoteDelay( $_POST['oppmote_forskyving'] );
	$hendelse->setSynligOppmotetid( $_POST['oppmote_synlig'] == 'true' );
} else {
	$hendelse->setOppmoteFor(0);
	$hendelse->setOppmoteDelay(0);
	$hendelse->setSynligOppmotetid( false );
}

// SYNLIGHET
$hendelse->setSynligRammeprogram( $_POST['synlig_ramme'] == 'true' );
$hendelse->setSynligDetaljprogram( $_POST['synlig_detalj'] == 'true' );

write_forestilling::save( $hendelse );

UKMprogram::getFlashbag()->add('success', $_POST['navn'] .' lagret');
