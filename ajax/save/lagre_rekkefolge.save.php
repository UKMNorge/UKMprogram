<?php
function UKMprogram_save($info) {
	UKM_loader('api/forestilling.class');
	$f = new forestilling($info['c_id']);
	$res = $f->ny_rekkefolge($info['order'],true);
	if($res)
		die('Lagret rekkefølge!');
	die('Lagring feilet, prøv igjen');
}
?>