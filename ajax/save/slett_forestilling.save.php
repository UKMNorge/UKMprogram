<?php
function UKMprogram_save($info) {
	require_once('UKM/forestilling.class.php');

	$url = parse_url($_SERVER['HTTP_REFERER']);
	parse_str($url['query'], $query);
	
	$f = new forestilling($query['c_id']);
	$res = $f->slett();

	
	die($url['scheme'].'://'.$url['host'].$url['path'].'?page='.$query['page']);
}
?>