<?php  
/* 
Plugin Name: UKM Program
Plugin URI: http://www.ukm-norge.no
Description: UKM Norge admin
Author: UKM Norge / M Mandal 
Version: 1.0 
Author URI: http://www.ukm-norge.no
*/
if(is_admin()) {
	global $blog_id;
	if($blog_id != 1)
		add_action('UKM_admin_menu', 'UKMprogram_menu',200);

	require_once('program.ajax.php');
	
	if(isset($_POST['save']) && strpos($_POST['action'],'UKMprogram_')!==false)
		require_once('ajax/save/'.$_POST['save'].'.save.php');

	add_action('wp_ajax_UKMprogram_ajax', 'UKMprogram_ajax');
	require_once('UKM/inc/phaseout.ico.inc.php');
	require_once('UKM/inc/ukmlog.inc.php');
}

## CREATE A MENU
function UKMprogram_menu() {
	UKM_add_menu_page('monstring','Program', 'Program', 'editor', 'UKMprogram_admin', 'UKMprogram_admin', 'http://ico.ukm.no/chart-menu.png',10);    

	UKM_add_scripts_and_styles('UKMprogram_admin', 'UKMprogram_scriptsandstyles' );
}
## INCLUDE SCRIPTS
function UKMprogram_scriptsandstyles() {
	wp_enqueue_style( 'jquery-ui-style', WP_PLUGIN_URL .'/UKMNorge/js/css/jquery-ui-1.7.3.custom.css');
	wp_enqueue_style( 'UKMdeltakere_style', WP_PLUGIN_URL .'/UKMdeltakere/deltakere.style.css');
	wp_enqueue_style( 'UKMprogram_program', WP_PLUGIN_URL .'/UKMprogram/program.style.css');
	wp_enqueue_style('WPbootstrap3_css');

	wp_enqueue_script('jquery');
	wp_enqueue_script('jqueryGoogleUI', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/jquery-ui.min.js');

/*
	wp_enqueue_script('jquery-ui-core');
	wp_enqueue_script('jquery-ui-sortable');
	wp_enqueue_script('jquery-ui-effects-core');
	wp_enqueue_script('jquery-ui-effects', '/wp-content/plugins/project_manager/scripts/ui.effects.js');
	wp_enqueue_script( 'jquery-ui-datepicker' );
*/

	wp_enqueue_script('UKMdeltakere_script', WP_PLUGIN_URL . '/UKMdeltakere/deltakere.script.js' );
	wp_enqueue_script('UKMdeltakere_script_modernizer', WP_PLUGIN_URL . '/UKMdeltakere/modernizr.input.js');
	wp_enqueue_script('UKMdeltakere_script_temp', WP_PLUGIN_URL . '/UKMdeltakere/temp.script.js' );

	wp_enqueue_script('UKMprogram_script', WP_PLUGIN_URL . '/UKMprogram/program.script.js' );

}

## SHOW STATS OF PLACES
function UKMprogram_admin() {
	global $UKMN, $lang;
	
	require_once('program_active.php');

	require_once('UKM/form.class.php');
	require_once('UKM/inc/toolkit.inc.php');
	require_once('UKM/forestilling.class.php');
	require_once('program.gui.php');

	
	echo '<div class="wrap">'.UKMprogram_list().'</div>';	

}



if(isset($_POST['veiviser_submit'])) {
	UKMP_veiviser_save();
}


/// HJELPERE VEIVISER LAGRING OG KALENDERTING


function UKMP_veiviser_save(){
	$m = new monstring(get_option('pl_id'));
	$monstringsstart = explode('.',str_replace(array(' kl. ',':'),'.',$m->starter()));
			
	$forestillinger = (int)$_POST['forestillinger'];
	$fordelte_forestillinger = sizeof($_POST['hendelser_fordeling']);
			
	if($forestillinger > $fordelte_forestillinger)
		$ekstra_forestillinger = $forestillinger - $fordelte_forestillinger;
	else 
		$ekstra_forestillinger = 0;

	$counter_per_forestilling = 0;
	$counter_nr_forestilling = 0;
	
	$fordeling = array();
	

	$pameldte = $m->innslag_btid();
	if($_POST['fordel_innslag'] == 'alfabetisk')
	foreach($pameldte as $btid => $innslag_i_bt) {
		foreach($innslag_i_bt as $i => $innslag) {
			switch($btid) {
				case 1:
					$counter_per_forestilling++;
					$fordeling['forestilling'][$counter_nr_forestilling][] = $innslag['b_id'];
					if($counter_per_forestilling >= $_POST['antall_per_sceneforestilling'] && $counter_nr_forestilling < (int)$_POST['forestillinger']-1){
						$counter_nr_forestilling++;
						$counter_per_forestilling = 0;
					}
				break;					
				case 2:
					if($_POST['tell_film_som_scene']) {
						$counter_per_forestilling++;
						$fordeling['forestilling'][$counter_nr_forestilling][] = $innslag['b_id'];
						if($counter_per_forestilling >= $_POST['antall_per_sceneforestilling'] && $counter_nr_forestilling < (int)$_POST['forestillinger']-1){
							$counter_nr_forestilling++;
							$counter_per_forestilling = 0;
						}
					} else {
						$fordeling['filmforestilling'][] = $innslag['b_id'];
					}
				break;
				case 3:
					$fordeling['kunstutstilling'][] = $innslag['b_id'];
				break;
			}
		}
	}
	
	#echo '<pre>'. var_export($fordeling,true).'</pre>';
	#die();
					
	if(is_array($_POST['hendelser_fordeling'])) {
		foreach($_POST['hendelser_fordeling'] as $i => $tid) {
			$forestillingstart = explode('.',$tid);
			$start = mktime($forestillingstart[2],0,0,$forestillingstart[1],$forestillingstart[0],$monstringsstart[2]);
			$forestilling[] = UKMP_save_create_forestilling('Forestilling '. ($i+1), $start);
		}
		for($j=0; $j<$ekstra_forestillinger; $j++) {
			$i++;
			$start = mktime($monstringsstart[3],0,0,$monstringsstart[1],$monstringsstart[0],$monstringsstart[2]);		
			$forestilling[] = UKMP_save_create_forestilling('Forestilling '. ($i+1), $start);
		}
	} else {
		for($k=0; $k<$forestillinger; $k++) {
			$start = mktime($monstringsstart[3],0,0,$monstringsstart[1],$monstringsstart[0],$monstringsstart[2]);		
			$forestilling[] = UKMP_save_create_forestilling('Forestilling '. ($k+1), $start);
		}
	}
	$start = mktime($monstringsstart[3],0,0,$monstringsstart[1],$monstringsstart[0],$monstringsstart[2]);
	if(isset($_POST['kunstutstilling']))
		$kunstutstilling = UKMP_save_create_forestilling('Kunstutstilling', $start);
	if(isset($_POST['filmforestilling']))
		$filmforestilling = UKMP_save_create_forestilling('Filmforestilling', $start);
		
		
	foreach($forestilling as $teller => $c_id) {
		if(is_array($fordeling['forestilling'][$teller]))
		foreach($fordeling['forestilling'][$teller] as $teller2 => $b_id)
			UKMP_save_rel_b_c($b_id, $c_id, $teller2+1);
	}
	if(isset($_POST['kunstutstilling']))
		if(is_array($fordeling['kunstutstilling']))
		foreach($fordeling['kunstutstilling'] as $teller2 => $b_id)
			UKMP_save_rel_b_c($b_id, $kunstutstilling, $teller2+1);
	if(isset($_POST['filmforestilling']))
		if(is_array($fordeling['filmforestilling']))
		foreach($fordeling['filmforestilling'] as $teller2 => $b_id)
			UKMP_save_rel_b_c($b_id, $filmforestilling, $teller2+1);
	
}

function UKMP_save_rel_b_c($b_id, $c_id, $order){ 
	$qry = new SQLins('smartukm_rel_b_c');
	$qry->add('b_id', $b_id);
	$qry->add('c_id', $c_id);
	$qry->add('order',$order);
	$qry->run();
}

function UKMP_save_create_forestilling($name, $start) {
	$qry = new SQLins('smartukm_concert');
	$qry->add('c_name', $name);
	$qry->add('pl_id', get_option('pl_id'));
	$qry->add('c_start', $start);
	$qry->add('c_visible_program','false');
	$qry->run();
	
	return $qry->insid();
}


function UKMp_dager($m) {
#	return array('ar'=>2012,'maned'=>6,'dager'=>array('22.06'=>5));
	$start = explode('.', date('d.m.Y.H.i', $m->g('pl_start')));
	$stop = explode('.', date('d.m.Y.H.i', $m->g('pl_stop')));
	
	$maned = $start[1];
	$ar	   = $start[2];
	
	if($stop[0] >= $start[0]) {
		for($i=$start[0]-1; $i<$stop[0]; $i++)
			$days[($i+1).'.'.$maned] = date('N', mktime(0,0,0,$maned, $i+1, $ar));
			
		return array('ar'=>$ar, 'maned'=>$maned, 'dager'=>$days);		
	## Sluttdato er mindre enn startdato, ergo er sluttdato neste måned!
	} else {
		for($i=$start[0]-1; $i<cal_days_in_month(CAL_GREGORIAN, $maned, $ar); $i++)
			$days[($i+1).'.'.$maned] = date('N', mktime(0,0,0,$maned, $i+1, $ar));
		for($i=0; $i<$stop[0]; $i++) 
			$days[($i+1).'.'.$maned+1] = date('N', mktime(0,0,0,$maned+1, $i+1, $ar));
			
		return array('ar'=>$ar, 'maned'=>$maned, 'dager'=>$days);		
	}
}

function UKMprog_tabs($c) {
?>
<h1><?= $c->g('c_name')?></h1>
<div class="ukmdeltakere_tabs">

	<a href="?page=<?=$_GET['page']?>&c_id=<?=$_GET['c_id']?>&tab=1" <?=((!isset($_GET['tab'])||$_GET['tab']=='1')?' class="active"':'')?>>
		<div>
			<span class="tab_header">Tid, sted og info</span><?= UKMN_icoAlt('info-sirkel', "", 20) ?><br>
		</div>
	</a>
	<a href="?page=<?=$_GET['page']?>&c_id=<?=$_GET['c_id']?>&tab=2" <?=($_GET['tab']=='2'?' class="active"':'')?>>
		<div>
			<span class="tab_header">Rekkefølge forestilling</span><?= UKMN_icoAlt('clipboard', "", 22) ?><br>
		</div>
	</a>
<?php /*	<a href="?page=<?=$_GET['page']?>&c_id=<?=$_GET['c_id']?>&tab=3" <?=($_GET['tab']=='3'?' class="active"':'')?>>
		<div>
			<span class="tab_header">Rekkefølge teknisk prøve</span><?= UKMN_icoAlt('settings', "", 20) ?><br>
		</div>
	</a>
	*/ ?>
</div>
<div class="ukmdeltakere_tabs_desc">
	<span>
		<?php
		switch( $_GET['tab'] ) {
			case '8':
				echo 'Stat8';
				break;
			case '5-6':
				echo 'Stat5-6';
				break;
			case '1-4':
				echo 'Stat1-4';
				break;
		}
		?>
	</span>
</div><?php
}
?>
