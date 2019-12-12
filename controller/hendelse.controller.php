<?php

require_once('UKM/monstring.class.php');

$arrangement = new monstring_v2( get_option('pl_id') );

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

UKMprogram::addViewData(
    'posts',
    !$results ? [] : $results
);

UKMprogram::addViewData(
    'categories',
    get_categories()
);

UKMprogram::addViewData('arrangement', $arrangement);
if( $_GET['id'] !== 'new' ) {
	$hendelse = $arrangement->getProgram()->get( $_GET['id'] );
	UKMprogram::addViewData('hendelse', $hendelse);
}