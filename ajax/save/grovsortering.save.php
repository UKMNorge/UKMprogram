<?php
function UKMprogram_save($info) {
	UKM_loader('api/forestilling.class');
	
	$f = new forestilling($info['forestilling']);
	
	// Hvis det ikke er flere innslag igjen
	// (grunnet drag-n-drop skal det alltid være 1 innslag før
	// hendelsen blir tømt. Alt annet er potensiell feil)
	if(empty($info['innslag']) && $f->antall_innslag()==1) {
		$f->fjern_alle_innslag();
		echo json_encode(array('antall'=>0,'c_id'=>$info['forestilling']));
		die();		
	}

	$rekkefolge = implode(',', $info['innslag']);
	$antall = $f->helt_ny_rekkefolge($rekkefolge);
	
	echo json_encode(array('antall'=>(!$antall?0:$antall),'c_id'=>$info['forestilling'], 'varighet'=>$f->tid()));	
	die();
}
?>