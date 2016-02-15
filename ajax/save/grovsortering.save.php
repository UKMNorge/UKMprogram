<?php
function UKMprogram_save($info) {
	require_once('UKM/forestilling.class.php');
	
	$f = new forestilling($info['forestilling']);
	
	// Hvis det ikke er flere innslag igjen
	// (grunnet drag-n-drop skal det alltid være 1 innslag før
	// hendelsen blir tømt. Alt annet er potensiell feil)
	if(empty($info['innslag']) && $f->antall_innslag()==1) {
		$f->fjern_alle_innslag();
		echo json_encode(array('antall'=>0,'c_id'=>$info['forestilling']));
		do_action('UKMprogram_save', 'fjern_alle_innslag', $info['forestilling']);
		die();
	}

	$rekkefolge = implode(',', $info['innslag']);
	$antall = $f->helt_ny_rekkefolge($rekkefolge);
	do_action('UKMprogram_save', 'grovsortering', $info['forestilling']);
	

	echo json_encode(array('antall'=>(!$antall?0:$antall),'c_id'=>$info['forestilling'], 'varighet'=>$f->tid()));	
	die();
}
?>