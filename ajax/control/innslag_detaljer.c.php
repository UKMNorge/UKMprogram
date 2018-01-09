<?php
require_once('UKM/innslag.class.php');
require_once('UKM/forestilling.class.php');

function UKMdeltakere_ajax_controller($bid){
	$innslag = new innslag($bid,false);
	if(get_option('site_type')!='kommune')
		$innslag->videresendte(get_option('pl_id'));
	$f_info = array();
	
	$forestillinger = $innslag->forestillinger(get_option('pl_id'));
	foreach($forestillinger as $f_id => $order) {
		$forestilling = new forestilling($f_id);
		$f_info[$f_id] = array('id'=>$forestilling->g('c_id'),
							   'navn' => $forestilling->g('c_name'),
							   'start'=> $forestilling->starter(),
							   'nummer'=> $order,
							   'antall'=> $forestilling->antall_innslag(),
							   'varighet'=> $forestilling->g('varighet'));
	}
	return array('innslag'=>$innslag,
				 'titler'=>$innslag->titler(get_option('pl_id')),
				 'advarsler'=>$innslag->warnings(get_option('pl_id')),
				 'forestillinger'=>$f_info);
}
?>