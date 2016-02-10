<?php
function UKMprogram_save($info) {
	require_once('UKM/forestilling.class.php');
	$f = new forestilling($info['c_id']);
	$res = $f->ny_teknisk_rekkefolge($info['order']);
	if($res) {
		do_action('UKMprogram_save', 'lagre_teknisk_rekkefolge', $info['c_id']);
		die('Lagret teknisk rekkefølge!');
	}
	die('Lagring feilet, prøv igjen');
}
?>