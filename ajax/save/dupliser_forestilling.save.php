<?php
function UKMprogram_save($info) {
	UKM_loader('sql|api/forestilling.class');

	$url = parse_url($_SERVER['HTTP_REFERER']);
	parse_str($url['query'], $query);
	
	$c = new forestilling($query['c_id']);
	$new_c = $c->duplicate();	
	die($url['scheme'].'://'.$url['host'].$url['path'].'?page='.$query['page'].'&c_id='.$new_c->g('c_id'));
}
?>