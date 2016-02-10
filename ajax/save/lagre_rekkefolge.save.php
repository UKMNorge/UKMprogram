<?php
function UKMprogram_save($info) {
	require_once('UKM/forestilling.class.php');
	$f = new forestilling($info['c_id']);
	$res = $f->ny_rekkefolge($info['order'],true);
	if($res) {
		do_action('UKMprogram_save', 'lagret_rekkefolge', $info['c_id']);
		die('Lagret rekkefølge!');
	}
	die('Lagring feilet, prøv igjen');
}
?>