<?php

use UKMNorge\Arrangement\Arrangement;
use UKMNorge\Arrangement\Program\Hendelser;
use UKMNorge\Meta\Write as WriteMeta;
use UKMNorge\Wordpress\Blog;

require_once('UKM/Autoloader.php');

if( isset($_GET['delete'] ) && $_GET['delete'] == 'hendelse') {
    require_once( UKMprogramLandsfestivalen::getPluginPath() .'delete/hendelse.delete.php');
}

$arrangement = new Arrangement( get_option('pl_id') );
$program = Hendelser::sorterPerDag( 
	$arrangement->getProgram()->getAbsoluteAll()
);

// Opprett deltakerprogram-siden, hvis denne ikke eksisterer
$blog_id = Blog::getIdByPath( $arrangement->getPath() );
if( !Blog::harSide($blog_id,'deltakerprogram')) {
    Blog::opprettSide(
        $blog_id,
        'deltakerprogram',
        'Deltakerprogram',
        'deltakerprogram'
    );
}

if( isset($_GET['do']) && $_GET['do'] == 'changeView' && isset($_GET['to'])) {
    $setup = $arrangement->getMeta('program_editor')->set( $_GET['to'] );
    WriteMeta::set($setup);
}



UKMprogramLandsfestivalen::addViewData(
    'grense',
    [
        'innslag' => $grense_avansert_innslag,
        'hendelser' => $grense_avansert_hendelser
    ]
);

UKMprogramLandsfestivalen::addViewData('arrangement', $arrangement);
UKMprogramLandsfestivalen::addViewData('program', $program);

// Inntil vi har forestillinger, vis veiviser
if( sizeof($program) == 0 ) {
    UKMprogramLandsfestivalen::setAction('veiviser');
    UKMprogramLandsfestivalen::includeActionController();
}
// Vi har forestillinger, og jobber med enkel setup
elseif( $arrangement->getMetaValue('program_editor') == 'enkel' ) {
    
    $grense_avansert_innslag = 40;
    $grense_avansert_hendelser = 4;
    if( $arrangement->getMetaValue('program_editor') == 'enkel' ) {
        if( $arrangement->getInnslag()->getAntall() > $grense_avansert_innslag ) {
            UKMprogramLandsfestivalen::getFlashbag()->info(
                'Når du har så mange innslag, er det muligens bedre for deg å bruke den '.
                '<a href="?page='. $_GET['page'] .'&do=changeView&to=avansert">avanserte visningen</a>'
            );
        } elseif( $arrangement->getProgram()->getAntall() > $grense_avansert_hendelser ) {
            UKMprogramLandsfestivalen::getFlashbag()->info(
                'Når du har så mange hendelser, er det muligens bedre for deg å bruke den '.
                '<a href="?page='. $_GET['page'] .'&do=changeView&to=avansert">avanserte visningen</a>'
            );
        }
    }

    UKMprogramLandsfestivalen::setAction('hendelser/enkel');
}
// Vi har forestillinger, og jobber med avansert setup
else {
    UKMprogramLandsfestivalen::setAction('hendelser/avansert');
}