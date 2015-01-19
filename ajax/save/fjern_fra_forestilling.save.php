<?php
function UKMprogram_save($info){
	require_once('UKM/forestilling.class.php');
	$c = new forestilling($info['c_id']);
	$res = $c->fjern($info['b_id']);
	do_action('UKMprogram_save', 'fjern_fra_forestilling', $info['c_id'], $info['b_id']);
	die(json_encode(array('result'=>$res)));
}
?>