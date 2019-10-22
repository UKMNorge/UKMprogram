<?php
require_once(WP_PLUGIN_DIR.'/UKMprogram/liste.inc.php');
function UKMprogram_ajax_view($info){
if(sizeof($info['alle_innslag'])==0)
	die('Ingen innslag møter kravene til aktiv filtrering');
?>
	<div class="bandstoadd">
		<ul id="program_supplering" class="ukmdeltakere_liste" style="width: 1100px;">
		<?php
		for($i=0; $i<sizeof($info['alle_innslag']); $i++) {
			$inn = new innslag($info['alle_innslag'][$i]['b_id']);
			?>
			<li class="innslag" style="width: 1050px;" id="<?= $inn->g('b_id')?>">
				<div class="group add draggable"><img src="<?= WP_PLUGIN_URL?>/UKMprogram/leggtil.png" alt="Legg til" /></div>
				<div class="group order" style="display:none;font-size: 40px;padding-top:15px;"></div>
				<div class="group drag" style="display:none;"><img src="<?= WP_PLUGIN_URL?>/UKMprogram/draogslipp.png" /></div>
				<div class="group name"><?=UKMd_innslagsboks($inn,$info['m'],$info['tittellose_innslag'],true)?></div>
				<div class="clear"></div>
			</li>
		<?php
		}
	?></ul>
	</div>
<?php } ?>