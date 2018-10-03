<?php
require_once('UKM/inc/phaseout.ico.inc.php');

function UKMd_innslagsboks($inn,$place,$tittellose_innslag,$forestilling=false,$tekniskprove=false) {
	if(!in_array($inn->g('bt_id'),$tittellose_innslag)) { ?>
		<div class="group ">
			<div class="ikon_detaljer">
				<?=UKMN_icoButton('sirkel-pluss',16,'vis detaljer')?>
			</div>
			<div class="ikon_detaljer_skjul"><?=UKMN_icoButton('sirkel-minus',16,'skjul detaljer')?></div>
		</div>
		<?php } else { ?>
		<div class="group">
			<div class="ikon_detaljer"></div>
			<div class="ikon_detaljer_skjul"></div>
		</div>
	<?php } ?>
	<div class="group">
		<div class="navn">
			<em><?php
				$b_name = $inn->g('b_name');
				if( strlen($b_name) > 25 ) { $b_name = shortString($b_name, 22); } ?>
				<?=$b_name?>
			</em>
		</div>

		<div class="kontaktperson smallerFont permaGray">
			<?php $kontaktperson = $inn->kontaktperson(); ?>
			<?= (strlen($kontaktperson->g('p_firstname')) > 18 ? shortString($kontaktperson->g('p_firstname'),15) : $kontaktperson->g('p_firstname'))?> (<?= ($kontaktperson->getAge()==26 ? 'over 25' : $kontaktperson->getAge()) ?>)&nbsp;<span class="phone"><?=$kontaktperson->getNicePhoneWithColor()?></span>
		</div>
	</div>

	<div class="group">
		<div class="kategori"><?=ucfirst(str_replace(' på scenen','', strtolower($inn->g('b_kategori'))))?></div>
		<div class="sjanger smallerFont permaGray"><?=$inn->g('b_sjanger')?></div>
	</div>

	<div class="group">
		<div class="personer">
			<?php
				if( intval( $inn->num_personer() ) != 1 ) {
					echo $inn->num_personer() . ' personer';
				}
				else {
					echo '1 person';
				}
			?></div>
		<div class="snittalder smallerFont permaGray">
			<?php
				$inn->loadGEO();
				echo $inn->g('fylke');
				/**
				echo 'Snitt ';
				$alder = 0;
				foreach( $inn->personObjekter() as $person ) {
					$alder = $alder + $person->getAge();
				}
				if( $inn->num_personer() > 0 ) {
					$alder = round(($alder / $inn->num_personer())/0.5)*0.5;
					if (round($alder) != $alder)
						$alder = ($alder - 0.5).' &frac12;';
				}
				echo ($alder==26 ? 'over 25' : $alder) . ' &aring;r';
				**/
			?>
		</div>
	</div>

	<div class="group">
		<?php
			if(!in_array($inn->g('bt_id'),$tittellose_innslag)){
				$inn->kalkuler_titler(get_option('pl_id'));
				if($inn->g('bt_id')==1||$inn->g('bt_id')==2) { ?>
					<div class="tid"><?=$inn->g('tid');?></div>
					<div class="titler smallerFont permaGray"><?=$inn->g('antall_titler_lesbart');?></div>	
			<?php } else {
					$inn->kalkuler_titler(get_option('pl_id')); ?>
					<div class="tid"><?=$inn->g('antall_titler_lesbart');?></div>
					<div class="titler smallerFont permaGray">&nbsp;</div>	
			<?php	}
			 } else { ?>
				<div class="tid">&nbsp;</div>
			<?php } ?>
	</div>
	<div class="group ikon_alert">
		<?php
		if($_GET['stat']!='1-4') {
			$warnings = $inn->warnings($place->g('pl_id'));
			if(!empty($warnings))
				echo '<div title="'.$warnings.'" alt="'.$warnings.'">'
					. UKMN_icoButton('emblem-important',18,'<p>krever oppmerksomhet</p>')
					.'</div>';
		} else {
			echo '<div class="ikon_duplicates">'
					. UKMN_icoButton('zoom',18,'<p>sjekk for duplikater</p>')
					.'</div>';
		}
		?></div>
	<?php 
	if(!$forestilling) {
	} elseif(!$tekniskprove) { ?>
			<div class="group_right ikon_meld_av_forestilling clickable"><?=UKMN_icoButton('trash',16,'fjern fra hendelsen')?></div>
	<?php } else { ?>
		<div class="group_right"></div>
	<?php } ?>
	<div class="clear"></div>
	<div class="detaljer" id="detaljer_<?=$inn->g('b_id')?>"></div>
<?php
}
?>