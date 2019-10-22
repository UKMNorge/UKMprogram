<?php
require_once('UKM/innslag.class.php');
function UKMdeltakere_ajax_controller($p_id){
	$place = new monstring(get_option('pl_id'));
	$b_id = $_POST['i'];
	
	$innslag = new innslag($b_id);
	if(get_option('site_type')!='kommune')
		$innslag->videresendte(get_option('pl_id'));
	
	$personer = $innslag->personer();
	
	$kommuner = $place->kommuneArray();
	$person = new person( $personer[0]['p_id'], $b_id );
	
	$return = array('person' => $person, 'kommuner' => $kommuner, 'innslag'=>$innslag);
	
	return $return;
}
?>