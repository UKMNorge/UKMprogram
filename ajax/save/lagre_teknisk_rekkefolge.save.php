<?php
function UKMprogram_save($info) {
	require_once('UKM/forestilling.class.php');
	$f = new forestilling($info['c_id']);
	$res = $f->ny_teknisk_rekkefolge($info['order']);
	if($res)
		die('Lagret teknisk rekkefølge!');
	die('Lagring feilet, prøv igjen');
}
?>