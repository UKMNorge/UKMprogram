<?php
function UKMprogram_save($info){
	require_once('UKM/forestilling.class.php');
	$c = new forestilling($info['c_id']);
	$res = $c->fjern($info['b_id']);
	die(json_encode(array('result'=>$res)));
}
?>