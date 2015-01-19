<?php
function UKMprogram_save($info) {
	require_once('UKM/forestilling.class.php');

	$url = parse_url($_SERVER['HTTP_REFERER']);
	parse_str($url['query'], $query);
	
	$c = new forestilling($query['c_id']);
	$new_c = $c->duplicate();	
	do_action('UKMprogram_save', 'dupliser_forestilling', $new_c->g('c_id'));
	die($url['scheme'].'://'.$url['host'].$url['path'].'?page='.$query['page'].'&c_id='.$new_c->g('c_id'));
}
?>