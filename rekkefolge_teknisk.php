<?php
function UKMprogram_forestilling($m) {
require_once(WP_PLUGIN_DIR.'/UKMdeltakere/liste.inc.php');
	$tittellose_innslag = array(4,5,8,9);
	$c = new forestilling($_GET['c_id'],true);
	$innslagene = $c->concertBands();
	echo UKMProg_tabs($c);
	?>

	<h3>Rekkefølge teknisk prøve</h3>
	<input type="hidden" id="innslagsrekkefolge" value="<?=$_GET['c_id']?>" />
	<ul id="program_teknisk_rekkefolge" class="ukmdeltakere_liste" style="width: 1100px;">
	<?php

	for($i=0; $i<sizeof($innslagene); $i++) {
		$inn = new innslag($innslagene[$i]['b_id']);
		?>
		<li class="innslag" style="width: 1050px;" id="<?= $inn->g('b_id')?>">
			<div class="group order" style="font-size: 40px;padding-top:15px;"></div>
			<div class="group drag draggable"><img src="<?= WP_PLUGIN_URL?>/UKMprogram/draogslipp.png" /></div>
			<div class="group name"><?=UKMd_innslagsboks($inn,$m,$tittellose_innslag,true,true)?></div>
			<div class="clear"></div>
		</li>
		<?php
	}
	?></ul>
	<?php
}
?>