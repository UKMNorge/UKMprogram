<?php
function UKMprogram_save($info){
	UKM_loader('api/forestilling.class');
	$c = new forestilling($info['c_id']);
	$res = $c->fjern($info['b_id']);
	die(json_encode(array('result'=>$res)));
}
?>