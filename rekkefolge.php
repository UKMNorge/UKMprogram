<?php
require_once('UKM/innslag.class.php');
function UKMprogram_forestilling($m) {
require_once(WP_PLUGIN_DIR.'/UKMdeltakere/liste.inc.php');
	$tittellose_innslag = array(4,5,8,9);
	$c = new forestilling($_GET['c_id']);
	$innslagene = $c->concertBands();
	
	$alle_innslag = $m->innslag();
	
	$type_innslag['alle_innslag'] = 'Alle innslag';
	foreach($alle_innslag as $a_i) {
		$a_inn = new innslag($a_i['b_id']);
		switch($a_inn->g('bt_id')) {
			case 1:
				$type_innslag['kun_1_alle'] = 'Kun sceneinnslag';
				$type_innslag['kun_1_'.strtolower($a_inn->g('kategori'))] = ' &nbsp; Kun '. strtolower($a_inn->g('kategori')).'innslag';
			break;
			default:
				$type_innslag['kun_'.$a_inn->g('bt_id')] = 'Kun '.strtolower($a_inn->g('bt_name')).'innslag';
			break;		
		}
	}
	ksort($type_innslag);
	
	$deltar_i['antall_ingen'] = 'ikke deltar i noen hendelse';
	$deltar_i['antall_uavhengig'] = 'deltar i ingen, en eller flere hendelser';
	$deltar_i['antall_1'] = 'deltar i én hendelse';
	$deltar_i['antall_2'] = 'deltar i to hendelser';
	$deltar_i['antall_3'] = 'deltar i tre hendelser';
	$hendelser = $m->forestillinger('c_start',false);
	foreach($hendelser as $hendelse)
		if($hendelse['c_id']!==$_GET['c_id'])
			$deltar_i['hendelse_'.$hendelse['c_id']] = 'deltar i &quot;'.$hendelse['c_name'].'&quot;';
	
	?>
	<?=UKMProg_tabs($c)?>	
	<h3>Suppleringsliste (innslag som ikke er med i denne hendelsen)</h3>
	<a href="#" id="visSuppleringsliste">Vis suppleringslisten</a>
	<a href="#" id="skjulSuppleringsliste" style="display:none;">Skjul suppleringslisten</a>
	<div id="supplering_filterboks" style="display:none;">
		Viser 
			<select id="supplering_type_innslag"><?php
				foreach($type_innslag as $val => $text)
					echo '<option value="'.$val.'">'.strtolower($text).'</option>';
			?></select> 
		som 
			<select id="supplering_deltar_i"><?php
				foreach($deltar_i as $val => $text)
					echo '<option value="'.$val.'">'.$text.'</option>';
			?></select>
		og ikke er med i denne hendelsen
		<input type="button" id="supplering_filtrer" value="Filtrér" />
	</div>
	<div id="suppleringsliste"></div>
	
	<h3>Detaljprogram (rekkefølge i hendelsen)</h3>
	<input type="hidden" id="innslagsrekkefolge" value="<?=$_GET['c_id']?>" />
	<ul id="program_rekkefolge" class="ukmdeltakere_liste" style="width: 1100px;">
	<?php

	for($i=0; $i<sizeof($innslagene); $i++) {
		$inn = new innslag($innslagene[$i]['b_id']);
		?>
		<li class="innslag" style="width: 1050px;" id="<?= $inn->g('b_id')?>">
			<div class="group add" style="display:none;"><img src="<?= WP_PLUGIN_URL?>/UKMprogram/leggtil.png" alt="Legg til" /></div>
			<div class="group order" style="font-size: 40px;padding-top:15px;"></div>
			<div class="group drag draggable"><img src="<?= WP_PLUGIN_URL?>/UKMprogram/draogslipp.png" /></div>
			<div class="group name"><?=UKMd_innslagsboks($inn,$m,$tittellose_innslag,true)?></div>
			<div class="clear"></div>
		</li>
		<?php
	}
	?></ul>
	<?php
}
?>