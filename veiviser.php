<?php
function UKMprogram_veiviser($m) {
	global $alle_dager,$return;
	$start = $m->starter();
	$stop = $m->slutter();

	$dager = UKMp_dager($m);
	_ret('<form action="?page='.$_GET['page'].'" id="hugeform" method="post">'
		.'<h2>Veiviser</h2>'
		.'<strong>Du har ikke opprettet noen forestillinger, og har derfor kommet til veiviseren.</strong>'
		.'<br />'
		.'N&aring;r du har fullf&oslash;rt veiviseren, vil den forsvinne. '
		.'Du vil da ikke se den igjen, s&aring;fremt du ikke sletter alle dine hendelser'
		);
	$anbefalinger = UKMp_anbefalinger();
	$analyse = UKMp_pameldte_analyser($m, $dager, $anbefalinger);
	
	$analysis = '<h2>Systemets analyse og anbefalinger</h2>'
			.   '<ul>';

	// Burde mønstringen utvides til flere dager?
		if($analyse['utvid'])
			$analysis .= 
				'<li class="critical">'
				.'UKM Norge anbefaler at du utvider m&oslash;nstringen din til flere dager! '
				.'<a href="?page=UKMMonstring">Endre m&oslash;nstringen</a>'
				.'</li>';

	
	// Dager og antall hendelser per dag
	if($analyse['antall_dager'] == 1) {
		$analysis .= 
			'<li>Din m&oslash;nstring skal skje i l&oslash;pet av 1 dag, '
				. $alle_dager[end($dager['dager'])].'dag. '
				.'UKM Norge anbefaler i utgangspunktet '.$anbefalinger['forestillinger_per_dag'][end($dager['dager'])]
				.' hendels'.($anbefalinger['forestillinger_per_dag'][end($dager['dager'])]>1 ? 'er':'e')
				.' per dag.</li>'
			.'<li>Hvis du vil ha en pause midtveis anbefaler vi 2 scene-hendelser p&aring; '
				.$alle_dager[end($dager['dager'])].'dag.</li>';
	} else {
		$analysis .= '<li>Din m&oslash;nstring skal skje i l&oslash;pet av '.$analyse['antall_dager'].' dager</li>';

		/*
foreach($analyse['hendelser'] as $dato => $antall) {
			for($i=0; $i<$antall; $i++)
				_ret('<input type="hidden" name="hendelser_fordeling[]" value="'
					.$dato. '.'.$anbefalinger['start_forestilling'][$antall][$i].'" />');
		}
*/
	}

	// Kunstutstilling
		$analysis .= '<li>Du har '.$analyse['antall_kunstinnslag'].' kunstinnslag til din m&oslash;nstring. '
			.'UKM Norge anbefaler kunstutstilling som egen hendelse</li>';
		
	// Antall innslag av forskjellige typer
		if(!$analyse['tell_film_som_scene'])
			$analysis .= '<li>Du har '.$analyse['antall_filminnslag'].' filminnslag til din m&oslash;nstring. '
				.'UKM Norge anbefaler filmforestilling som egen hendelse</li>';
		else 
			$analysis .= '<li>Du har '.$analyse['antall_filminnslag'].' filminnslag til din m&oslash;nstring. '
				.'UKM Norge anbefaler at filmene vises som en del av scene-forestillingen(e)</li>';

	// Sceneinnslag
		$analysis .= '<li>Du har '.$analyse['antall_sceneinnslag'].' scene-innslag til din m&oslash;nstring. '
				.'UKM Norge anbefaler (basert p&aring; standartid '.$anbefalinger['varighet']['innslag'].' min per innslag) '
				. $analyse['antall_forestillinger'] . ' scene-hendelser</li>'
			.'<li>Du m&aring; i snitt ha '.$analyse['antall_per_sceneforestilling'].' innslag per scene-forestilling</li>'
			;

	$analysis .= '</ul>';
		
	
	_ret('<h2>Opprett mine hendelser</h2>'
		.' &nbsp; '
		.'<input type="checkbox" name="kunstutstilling" checked="checked" />'
		.'&nbsp;  Kunstutstilling'
		.'<br />'
		.' &nbsp; '
		.'<input type="checkbox" name="filmforestilling" '.$analyse['opprett_filmforestilling'].' />'
		.'&nbsp;  Filmforestilling'
		.'<br />'
		.'<input type="text" name="forestillinger" style="width: 25px;" value="'.$analyse['antall_forestillinger'].'" />'
		.' Forestillinger'
		.'<input type="hidden" name="antall_per_sceneforestilling" value="'.$analyse['antall_per_sceneforestilling'].'" />'
		.'<input type="hidden" name="tell_film_som_scene" value="'.$analyse['tell_film_som_scene'].'" />'
		.'<br />'
		.'<br />'
		.'<strong>Fordeling av innslag</strong><br />'
		.'<label style="font-weight: normal;"><input type="radio" name="fordel_innslag" checked="checked" value="false" /> Ikke fordel innslag</label>'
		.'<br />'
		.'<label style="font-weight: normal;"><input type="radio" name="fordel_innslag" value="alfabetisk" /> Fordel alfabetisk</label>'
		.'<br />'
		.'<br />'

		.'<input type="submit" value="Opprett hendelser" name="veiviser_submit" />'
		.'</form>');
		
	_ret('<br /><br />'.$analysis);
	return $return;
}

function UKMp_pameldte_analyser($m, $dager, $anbefalinger) {
	global $alle_dager;

	$pameldte = $m->innslag_btid();
	if(!is_array($pameldte))
		$pameldte = array();
	$alle_bt_id = $m->getAllBandTypes();
	
	$stat['antall_dager'] = sizeof($dager['dager']);
	
	foreach($pameldte as $btid => $innslag_i_bt) {
		$key = sizeof($stat['typer_innslag']);
		$stat['typer_innslag'][$btid] = $alle_bt_id[$btid]['bt_name'];
		$stat['antall_innslag'][$btid] = sizeof($innslag_i_bt);
		
		$innslag[$btid] = $array_innslag;
	}
	
	// Hvis videoinnslag mindre enn anbefalt for egen forestilling
	if($stat['antall_innslag'][2] > $anbefalinger['minimum_filmer_for_egen_forestilling']) {
		$stat['tell_film_som_scene'] = false;
		$stat['opprett_filmforestilling'] = ' checked="checked"';
	} else {
		$stat['tell_film_som_scene'] = true;
		$stat['opprett_filmforestilling'] = '';
	}
	
	
	foreach($pameldte as $btid => $innslag_i_bt) {
		foreach($innslag_i_bt as $i => $innslag) {
			switch($btid) {
				case 1:
					$stat['antall_sceneinnslag'] += 1;
				break;					
				case 2:
					if($stat['tell_film_som_scene'] && $btid == 2)
						$stat['antall_sceneinnslag'] += 1;
					$stat['antall_filminnslag'] += 1;
				break;
				case 3:
					$stat['antall_kunstinnslag'] += 1;
				break;
			}
		}
	}
	
	
	$stat['antall_forestillinger'] = (int)round($stat['antall_sceneinnslag'] / $anbefalinger['antall_innslag_per_forestilling']);
	if($stat['antall_forestillinger'] == 2 
		&& $stat['antall_sceneinnslag'] < $anbefalinger['antall_innslag_for_to_forestillinger'])
		$stat['antall_forestillinger'] = 1;
	if($stat['antall_forestillinger'] == 0)
		$stat['antall_forestillinger'] = 1;

	if( ($stat['antall_forestillinger'] / sizeof($dager['dager'])) > 2)
		$stat['utvid'] = true;
	else
		$stat['utvid'] = false;
		
		
	foreach($dager['dager'] as $dato => $dag)
		$stat['kapasitet_alle_dager'] += $anbefalinger['forestillinger_per_dag'][$dag];

	$ingen_forestillinger = array();
	if($stat['kapasitet_alle_dager'] > $stat['antall_forestillinger']) {
		$kapasitet_forste_dag = $anbefalinger['forestillinger_per_dag'][reset($dager['dager'])];
		$kapasitet_siste_dag = $anbefalinger['forestillinger_per_dag'][end($dager['dager'])];

		if($stat['antall_forestillinger'] == ($stat['kapasitet_alle_dager']-$kapasitet_forste_dag-$kapasitet_siste_dag)) {
			$start = array_search(reset($dager['dager']), $dager['dager']);
			$stop = array_search(end($dager['dager']), $dager['dager']);
			$ingen_forestillinger = array($start, $stop);
			
		}elseif($stat['antall_forestillinger'] == $stat['kapasitet_alle_dager']-$kapasitet_forste_dag)
			$ingen_forestillinger = array(array_search(reset($dager['dager']), $dager['dager']));
		elseif(is_int($stat['kapasitet_alle_dager'] / $stat['antall_dager']))
			$antall_per_dag = (int)round($stat['antall_forestillinger'] / $stat['antall_dager']);
		
		foreach($dager['dager'] as $dato => $dag) {
			$stat['hendelser'][$dato] = (in_array($dato,$ingen_forestillinger)
											? 0 
											: (isset($antall_per_dag)
												? $antall_per_dag
												: $anbefalinger['forestillinger_per_dag'][$dag]
											)
										);
			$antall_hendelser_fordelt += $stat['hendelser'][$dato];
		}
		$stat['rest_fordelte_forestillinger'] = $stat['antall_forestillinger'] - $antall_hendelser_fordelt;
	}

	#echo '<pre>'; var_dump($stat); echo '</pre>';	
	$stat['antall_per_sceneforestilling'] = (int)round($stat['antall_sceneinnslag'] / $stat['antall_forestillinger']);

	return $stat;	
}

function UKMp_anbefalinger() {
	$r['forestillinger_per_dag'] = array(1 => 1,
									2 => 1,
									3 => 1,
									4 => 1,
									5 => 1,
									6 => 2,
									7 => 2);
	// Hvis landsmønstring, anbefal 2 forestillinger også på mandag (tross alt ferie)
	if(get_option('site_type')=='land')
		$r['forestillinger_per_dag'][1] = 2;
	
	$r['start_forestilling'][1] = array(18);
	$r['start_forestilling'][2] = array(13,18);
	$r['start_forestilling'][3] = array(10,14,19);
	
	
	$r['varighet']['forestilling'] = 90; // Minutter
	$r['varighet']['innslag'] = 5; // Minutter et innslag bør være under inkl konferansier
	$r['varighet']['konferansier'] = 0.5; // Et halvt minutt (korrigerer innslagstiden til 4:30)
	
	$r['antall_innslag_per_forestilling'] = round($r['varighet']['forestilling'] / $r['varighet']['innslag']);
	$r['antall_innslag_for_to_forestillinger'] = round($r['antall_innslag_per_forestilling']*1.5);
	$r['minimum_filmer_for_egen_forestilling'] = 5;
	
	return $r;
}

function _ret($text) {
	global $return;
	$return .= $text;
}
?>