<?php

function UKMprogram_list() {
	$place = new monstring(get_option('pl_id'));
	
	if(sizeof($place->forestillinger('c_start',false))==0) {
		?><h1>Program for <?= $place->g('pl_name')?></h1><?php
		require_once('veiviser.php');
		echo UKMprogram_veiviser($place);
	} elseif(isset($_GET['c_id'])) {
		if(isset($_GET['tab'])&&$_GET['tab']==2)
			require_once('rekkefolge.php');
		elseif(isset($_GET['tab'])&&$_GET['tab']==3)
			require_once('rekkefolge_teknisk.php');
		else
			require_once('forestilling.php');
	
		echo UKMprogram_forestilling($place);
	} else {
		require_once('dash.php');
	}
}
?>

