<?php
require_once('UKM/innslag.class.php');
function UKMdeltakere_ajax_controller($bid){
	$place = new monstring(get_option('pl_id'));
	$kategorier = array('annet'=>'Annet på scenen','dans'=>'Dans','litteratur'=>'Litteratur','musikk'=>'Musikk','teater'=>'Teater');
	
	$innslag = new innslag($bid,false);
	if(get_option('site_type')!='kommune')
		$innslag->videresendte(get_option('pl_id'));
	
	$b_description = $innslag->g('b_description');
//	var_dump($b_description);
	$visinnslag = (empty($b_description)) ? false : true;
		
	$titler = $innslag->titler(get_option('pl_id'));
	
	return array('innslag'=>$innslag, 'visbeskrivelse'=>$visinnslag, 'kategorier'=>$kategorier, 'titler'=>$titler);
}
?>