<?php
function UKMprogram_save($info){
	require_once('UKM/forestilling.class.php');
	$c = new forestilling($info['c_id']);

	$ramme = isset($_POST['ramme']) ? 'ramme' : 'detalj';
	$status = $c->synlig($ramme);

	if($ramme=='ramme') {
		$_POST['c_visible_program'] = $status ? 'false' : 'true';
		$_POST['log_current_value_c_visible_program'] = $status ? 'true' : 'false';
	} else {
		$_POST['c_visible_detail'] = $status ? 'false' : 'true';
		$_POST['log_current_value_c_visible_detail'] = $status ? 'true' : 'false';
	}
	
	if($ramme == 'ramme' && $status) {
		$text = 'Vises ikke i rammeprogram';
		$c->update('c_visible_program');
	} elseif($ramme == 'ramme') {
		$text = 'Vises i rammeprogram';
		$c->update('c_visible_program');
	} elseif($ramme == 'detalj' && $status) {
		$text = 'Detaljprogram ikke offentlig';
		$c->update('c_visible_detail');
	} elseif($ramme == 'detalj') {
		$text = 'Detaljprogram offentlig';
		$c->update('c_visible_detail');
	}
	
	$icon = $status ? 'red' : 'light-green';
	
	die(json_encode(array('id'=>$info['c_id'], 'icon'=>$icon, 'text'=>$text)));
}
?>