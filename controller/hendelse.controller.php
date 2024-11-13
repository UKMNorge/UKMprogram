<?php

use UKMNorge\Arrangement\Arrangement;

require_once('UKM/Autoloader.php');

$arrangement = new Arrangement(intval( get_option('pl_id') ));

// A sql query to return all post titles
global $wpdb;
$posts = [];
$results = $wpdb->get_results(
    $wpdb->prepare( 
        "SELECT ID, post_title, post_type FROM {$wpdb->posts} WHERE post_status = 'publish' ORDER BY post_title ASC",
        []
        ), 
    ARRAY_A 
);

UKMprogramLandsfestivalen::addViewData(
    'posts',
    !$results ? [] : $results
);

UKMprogramLandsfestivalen::addViewData(
    'categories',
    get_categories()
);

UKMprogramLandsfestivalen::addViewData('arrangement', $arrangement);
if( $_GET['id'] !== 'new' ) {
	$hendelse = $arrangement->getProgram()->get( $_GET['id'] );
	UKMprogramLandsfestivalen::addViewData('hendelse', $hendelse);
}