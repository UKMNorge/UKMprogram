<?php
require_once('UKM/innslag.class.php');
$m = new monstring(get_option('pl_id'));
$forestillinger = $m->forestillinger('c_start',false);

$vis_kommune = true;
if($m->g('type') == 'kommune' && !$m->fellesmonstring())
	$vis_kommune = false;
if($m->g('type') == 'land')
	$vis_kommune = null;

$dager = UKMp_dager($m);
$dager = $dager['dager'];
$alle_dager = array('s&oslash;n','man','tirs','ons','tors','fre','l&oslash;r','s&oslash;n');

function li_innslag($inn, $vis_kommune, $ignore_shortstring=false) {
	$ant_tegn = 18;
	$katogsjan = $inn->g('kategori_og_sjanger');
	if(empty($katogsjan))
		$katogsjan = $inn->g('bt_name');
	$inn->loadGEO();
	
	$geodata = $vis_kommune === null ? $inn->g('fylke') : $inn->g('kommune');
	
	# Generer tittelliste, fordi flere ikke skjønner 
	# at det kan være flere bokser per artist og glemmer noen.
	#	asgeirsh@ukmmedia.no
	#	10.02.16
	$titler = $inn->titler($m->pl_id);
	$t = '';
	// Hvis innslaget er tittelløst, type konferansier, media eller arrangør
	if ($inn->tittellos()) {
		$personer = $inn->personObjekter();
		foreach ($personer as $person) {
			#var_dump($person);
			#var_dump($person->get('instrument'));
			if (empty($person->get('instrument'))) {
				$t .= 'Ingen rolle valgt';
			}
			else {
				$t .= $person->get('instrument') . ', ';	
			}
		}
	}
	else {
		foreach ($titler as $tittel) {
			$t .= $tittel->tittel .', ';
		}
	}
	$t = rtrim($t, ', ');

	return '<li class="dash_innslag dragable" '
			.	'id="innslag_'.$inn->g('b_id').'" '
#			.	'class="forestilling_rekkefolge" '
			.	'data-state="0" '
			.	'data-type="&nbsp; '.$katogsjan.'" '
			.	'data-name="'.$inn->g('b_name').'&nbsp;" '
			.	'data-kommune="'.($vis_kommune!==false ? $geodata.'&nbsp;' : 'false').'" '
			.	'data-time="&nbsp; '.$inn->tid(get_option('pl_id')).'" '
			.	'data-titler="'. $t .'" '
			.	'data-antall="&nbsp; Deltar i '.$inn->antall_hendelser(get_option('pl_id')).' hendelser" '
			.	'data-tall="'.$inn->antall_hendelser(get_option('pl_id')).'" '
#			.	'data-varighet="'.$inn->varighet(get_option('pl_id')).'"'
            .   'data-ignore-shortstring="'. ($ignore_shortstring?'true':'false') .'"'
			.'>'
				.$inn->g('b_name')
			.'</li>';
}


#	$total_bredde = 900;
#	$padding = 10;
#	$bredde = floor(($total_bredde-($padding*sizeof($dager))*2.01)/sizeof($dager));
#	if($bredde < 250)
#		$bredde = 250;

foreach($forestillinger as $i => $f) {
//	$teller +=1;
	$forestilling = new forestilling($f['c_id']);
	$innslag = $forestilling->innslag();
	$sortable_lister[] = '#dash_forestilling_'.$forestilling->g('c_id');

	$ramme = 'Vises '. ($forestilling->synlig() ? '' : 'ikke'). ' i rammeprogram';
	$rammeIkon = $forestilling->synlig() ? 'light-green' : 'red';

	$detalj = 'Detaljprogram '. ($forestilling->synlig('detalj') ? ' offentlig' : 'ikke offentlig'). '';
	$detaljIkon = $forestilling->synlig('detalj')? 'light-green' : 'red';

	$ret = '
	<ul class="dash_forestilling" data-show="true" id="dash_forestilling_'.$forestilling->g('c_id').'">
		<li class="forestilling_detaljer" id="'.$forestilling->g('c_id').'">'
			.'<span class="name clickable">'
				. UKMN_ico('pencil',16,'rediger').' '. $f['c_name']
			.'</span>
			<div class="tid">kl. '. date('H:i', $f['c_start']).'</div>
			<div class="clear"></div>
			<div class="varighet">'. $forestilling->tid().'</div>
			<div class="antall">'. $forestilling->antall_innslag().' innslag</div>
			<div class="clear"></div>
			<div class="rammeprogram clickable">
				<img src="http://ico.ukm.no/circle-'.$rammeIkon.'-32.png" width="11" />
				<span>'. $ramme.'</span>
			</div>
			<div class="detaljprogram clickable">
				<img src="http://ico.ukm.no/circle-'.$detaljIkon.'-32.png" width="11" />
				<span>'. $detalj.'</span>
			</div>
			<div class="clear"></div>
			<div class="hideshow_details clickable">
				<div class="hideshow_icons">
					<img src="http://ico.ukm.no/sirkel-minus-256.png" width="11" />
					<span>Skjul rekkefølge</span>
				</div>
				<div class="hideshow_icons" style="display:none;">
					<img src="http://ico.ukm.no/plus-32.png" width="11" />
					<span>Vis rekkefølge</span>
				</div>
			</div>
			<div class="clear"></div>

		</li>';
	foreach($innslag as $i) {
		$inn = new innslag($i['b_id']);
		$ret .= li_innslag($inn, $vis_kommune);
	}	
	
		
	$ret .= '</ul>';
//	if ($teller%3 == 0) $ret .= '</div><div class="clear">';
	$sorterte_forestillinger[date('d.m',$f['c_start'])] .= $ret;
}
?>
<h1>Program for <?= $m->g('pl_name')?></h1>
<div class="row">
	<div class="col-xs-12 col-md-8">
		<div class="container_forklaring alert alert-info" style=" "> 
			<p style="margin-top: 0; font-weight: bold;">Hvordan bruke modulen? <a href="#" id="readmore">Les mer</a></p>
			<div id="forklaring_tekst" style="display:none;">
				<p>For å kjapt fordele innslag på forestillinger kan du dra en boks fra «Deltar ikke i noen hendelse» til en hendelse eller forestilling til venstre.</p>
				<p>For å finpusse rekkefølgen kan du trykke på blyanten til venstre for forestillingsnavnet og trykke «Endre rekkefølge». Her finner du gode verktøy for å sortere innslagene, bygge dramaturgi og sette opp en god sammensetting av innslag.</p>
				<p>Hvis du har innslag som har meldt på to titler, som f.eks et band med to eller flere låter, kan du dra de inn fra bolken «Alle innslag» for å få de med på flere enn én hendelse.</p>
				<p><b>TIPS:</b></p>
				<ol class="">
					<li>Når mønstringen starter skal du ikke ha noen innslag i listen "Deltar ikke i noen hendelse".</li>
					<li>Hvis du skal ha pause i forestillingen, oppretter du "Forestilling 1 før pause" og "Forestilling 1 etter pause" e.l som to hendelser.</li>
					<li>Hvis tekniske prøver ikke følger rekkefølgen i forestillingen, opprett hendelsen "Teknisk prøve forestilling X". Ofte er det hensiktsmessig å skjule denne fra rammeprogram / detaljprogram.</li>
					<li>For mer info om hvert enkelt innslag, trykk på det blå info-ikonet.</li>
				</ol>
			</div>
		</div>
	</div>
</div>
<div class="clearfix"></div>
<div class="row">
    <div class="col-xs-12">
        <a href="#" id="leggtilforestilling" class="btn btn-success">Legg til ny hendelse</a>
    </div>
</div>
<div class="clear"></div>
<div class="clearfix"></div>
<div style="width: 95%;">
	<div class="row">
		<div class="col-xs-8">
    		<div class="pull-right">
                <a href="#" id="hide_all_details" class="btn btn-xs btn-primary" data-text="Vis rekkefølge for alle hendelser">Skjul rekkefølge for alle hendelser</a>
                <select id="show_same_details">
                    <option value="0" selected="selected" disabled="disabled">Vis samme info for alle innslag</option>
                    <option value="0">Navn på innslag</option>
                    <option value="1">Kategori</option>
                    <option value="2">Varighet</option>
                    <option value="3">Antall hendelser</option>
                    <option value="4">Geografisk tilhørighet</option>
                    <option value="5">Titler / rolle</option>
                </select>
    		</div>
            <div class="clearfix"></div>
			<div class="container_forestilling_dag"><?php
				$teller = 0;
				###########################################
				## LAGT TIL 10.01.2013 DA FORESTILLINGER UTENFOR MØNSTRINGEN (TEKNISKE PRØVER)
				## FALT UT AV ADMIN-SYSTEMET
				###########################################
				
				if(empty($sorterte_forestillinger)) {
					$loopthis = $dager;
				} else {
					$loopthis = $sorterte_forestillinger;
				}
				foreach($loopthis as $dato => $dag) {
					$teller += 1;
					$test = explode('.',$dato);
					if(strlen($test[0])==1)
						$dato = '0'.$dato;
						
					$find_dag = date('N', mktime(0,0,0,$test[1],$test[0],get_option('season')));
				?>
				<div class="forestilling_dag">
					<div class="header"><?= ucfirst($alle_dager[$find_dag])?>dag <?= $dato ?></div>
						<?= $sorterte_forestillinger[$dato] ?>
				</div>
				<?php 
					
				} ?>
				<div class="clear"></div>
			</div>
		</div>
		<div class="col-xs-4 col-lg-3">
			<div class="container_suppleringsliste">
				<h3>Deltar ikke i noen hendelse</h3>
				<button class="button btn btn-info" id="changeData">Bla i info for hvert innslag</button>
				<ul id="dash_supplering"><?php
					$m = new monstring(get_option('pl_id'));
					$innslag = $m->innslag();
					foreach($innslag as $i) {
						$inn = new innslag($i['b_id']);
						if($inn->antall_forestillinger(get_option('pl_id'))==0)
							echo li_innslag($inn, $vis_kommune, true);
						#echo '<li class="dragable dash_innslag" id="innslag_'.$inn->g('b_id').'">'.$inn->g('b_name').'</li>';
					}
			?>	</ul>
				<div class="clear"></div>
			</div>
			<div class="clear"></div>
			<div class="container_suppleringsliste">
				<h3>Alle innslag</h3>
				(Skal du fjerne et innslag fra en hendelse, trekk den tilbake hit)
				<ul id="dash_alle"><?php
					$m = new monstring(get_option('pl_id'));
					$innslag = $m->innslag();
					foreach($innslag as $i) {
						$inn = new innslag($i['b_id']);
						echo li_innslag($inn, $vis_kommune, true);
						#'<li class="dragable dash_innslag" id="innslag_'.$inn->g('b_id').'">'.$inn->g('b_name').'</li>';
					}
			?>	</ul>
				<div class="clear"></div>
			</div>
		</div>
	</div>
</div>


