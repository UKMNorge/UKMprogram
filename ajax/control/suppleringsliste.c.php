<?php
function UKMprogram_ajax_controller($info){
	UKM_loader('api/innslag.class|api/forestilling.class|api/monstring.class');
	$m = new monstring(get_option('pl_id'));
	$c = new forestilling($info['c_id']);

	$alle_innslag = UKMp_filtrer($m->innslag(),$c,$info['type'],$info['deltari']);

	return array('tittellose_innslag' => array(4,5,8,9),
				 'alle_innslag' => $alle_innslag,
				 'm' => $m);
}


function UKMp_filtrer($alle_innslag,$c,$type,$deltari) {
	$filtered = array();
	$type = explode('_',$type);
	$deltari = explode('_',$deltari);

	// Loop alle innslag og kjør over filtrene	
	foreach($alle_innslag as $i) {
		////////////////////////////////////////////////////////////////
		// Sjekk at innslaget ikke er med i forestillingen
		if($c->er_med($i['b_id']))
			continue;

		////////////////////////////////////////////////////////////////
		// Filtersjekk 1 (er ikke med i forestilling, oppfyller liste 1)
		// Hvis det er innslag av kun 1 type (else: alle)
		$inn = new innslag($i['b_id']);
		if($type[0]=='kun'){
			$type[1] = (int)$type[1];
			// Sjekk at innslaget er av riktig type
			if($type[1] != $i['bt_id'])
				continue;
			// Det er sceneinnslag, og vi finsorterer så lenge ikke alle skal vises
			if($type[1]==1&&$type[2]!='alle') {
				if(strtolower($inn->g('kategori'))!=strtolower($type[2]))
					continue;
			}		
		}
		
		////////////////////////////////////////////////////////////////
		// Filtersjekk 2 (er ikke med i forestilling, oppfyller liste 1)
		// Om innslaget skal være med i et gitt antall forestillinger
		if($deltari[0]=='antall') {
			$antall = $inn->antall_forestillinger(get_option('pl_id'),$c->g('c_id'));
			switch($deltari[1]) {
				case 'uavhengig':
					break;
				case 'ingen':
					if($antall > 0)
						continue 2;
					break;
				default:
					if($antall != (int)$deltari[1])
						continue 2;
			}
		// Om innslaget er med i en gitt forestilling
		} else {
			$check_c = new forestilling($deltari[1]);
			if(!$check_c->er_med($inn->g('b_id')))
				continue;
		}

		////////////////////////////////////////////////////////////////
		// Innslaget skal vises
		$filtered[] = $i;	
	}
	return $filtered;
}
?>