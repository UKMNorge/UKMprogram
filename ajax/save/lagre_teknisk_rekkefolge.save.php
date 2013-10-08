<?php
function UKMprogram_save($info) {
	UKM_loader('api/forestilling.class');
	$f = new forestilling($info['c_id']);
	$res = $f->ny_teknisk_rekkefolge($info['order']);
	if($res)
		die('Lagret teknisk rekkefølge!');
	die('Lagring feilet, prøv igjen');
}
?>