<?php  
/* 
Plugin Name: UKM Program
Plugin URI: http://www.ukm-norge.no
Description: UKM Norge admin
Author: UKM Norge / M Mandal 
Version: 2.0 
Author URI: http://www.ukm-norge.no
*/


require_once('UKM/wp_modul.class.php');

class UKMprogram extends UKMWPmodul {
    public static $action = 'dashboard';
    public static $path_plugin = null;

    /**
     * Register hooks
     */
    public static function hook() {
        add_action(
			'UKM_admin_menu', 
			['UKMprogram', 'meny'],
			200
		);
		add_filter(
			'UKM_admin_menu_conditions', 
			['UKMprogram', 'meny_conditions']
		);
    }

    /**
     * Add menu
     */
    public static function meny() {		
		UKM_add_menu_page(
			'monstring',
			'Program', 
			'Program', 
			'editor', 
			'UKMprogram', 
			['UKMprogram', 'renderAdmin'], 
			'//ico.ukm.no/chart-menu.png',
			10
		);

		UKM_add_scripts_and_styles(
			'UKMprogram_renderAdmin',
			['UKMprogram', 'scripts_and_styles']
		);
	}
	

	public static function meny_conditions( $_CONDITIONS ) {
		return array_merge( $_CONDITIONS, 
			['UKMprogram_renderAdmin' => 'monstring_er_registrert']
		);
	}

    public static function scripts_and_styles() {
		wp_enqueue_script('WPbootstrap3_js');
		wp_enqueue_style('WPbootstrap3_css');
		wp_enqueue_script('jqueryGoogleUI', '//ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/jquery-ui.min.js');
        wp_enqueue_style('UKMprogram_css', plugin_dir_url( __FILE__ ) .'UKMprogram.css' );
        wp_enqueue_script('UKMprogram_js', plugin_dir_url( __FILE__ ) .'UKMprogram.js' );

    }
}

## HOOK MENU AND SCRIPTS
UKMprogram::init( __DIR__ );
UKMprogram::hook();

/*

function UKMdeltakere_ajax_handler() {
	if(isset($_POST['c'])&&isset($_POST['v'])&&isset($_POST['i'])) {
		require_once('ajax/control/'.$_POST['c'].'.c.php');
		require_once('ajax/view/'.$_POST['v'].'.v.php');

		die(UKMdeltakere_ajax_view(UKMdeltakere_ajax_controller($_POST['i'])));	
	}
	
	if(isset($_POST['save'])) {
		require_once('ajax/save/'.$_POST['save'].'.save.php');
		UKMdeltakere_save();
	}
}


if(is_admin()) {
	
	add_action('wp_ajax_UKMdeltakere_gui', 'UKMdeltakere_ajax_handler');

	global $blog_id;

	
	if(isset($_POST['save']) && strpos($_POST['action'],'UKMprogram_')!==false) {
		require_once('UKM/inc/toolkit.inc.php');

		require_once('ajax/save/'.$_POST['save'].'.save.php');
	}

	add_action('wp_ajax_UKMprogram_ajax', 'UKMprogram_ajax');
}


function UKMMprogram_dash_shortcut( $shortcuts ) {	
	$shortcut = new stdClass();
	$shortcut->url = 'admin.php?page=UKMprogram_admin';
	$shortcut->title = 'Program';
	$shortcut->icon = '//ico.ukm.no/chart-menu.png';
	$shortcuts[] = $shortcut;
	
	return $shortcuts;
}

## INCLUDE SCRIPTS
function UKMprogram_scriptsandstyles() {
	wp_enqueue_style( 'jquery-ui-style', WP_PLUGIN_URL .'/UKMNorge/js/css/jquery-ui-1.7.3.custom.css');
	wp_enqueue_style( 'UKMdeltakere_program_style', WP_PLUGIN_URL .'/UKMprogram/deltakere.style.css');
	wp_enqueue_style( 'UKMprogram_program', WP_PLUGIN_URL .'/UKMprogram/program.style.css');
	wp_enqueue_style('WPbootstrap3_css');

	wp_enqueue_script('jquery');
	wp_enqueue_script('jqueryGoogleUI', '//ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/jquery-ui.min.js');


	wp_enqueue_script('UKMdeltakere_script', WP_PLUGIN_URL . '/UKMprogram/deltakere.script.js' );
#	wp_enqueue_script('UKMdeltakere_script_modernizer', WP_PLUGIN_URL . '/UKMdeltakere/modernizr.input.js');
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
			<span class="tab_header">Tid, sted og info</span><img src="//ico.ukm.no/info-sirkel-256.png" width="20" /><br>
		</div>
	</a>
	<a href="?page=<?=$_GET['page']?>&c_id=<?=$_GET['c_id']?>&tab=2" <?=($_GET['tab']=='2'?' class="active"':'')?>>
		<div>
			<span class="tab_header">Rekkefølge forestilling</span><img src="//ico.ukm.no/clipboard-256.png" width="20" /><br>
		</div>
	</a>

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


function UKMdeltakere_tittelgui($btid,$kategori) {
	$felter['musikk']	= array('tittel'=>'Tittel',
								'melodi_av'=>'Melodi av',
								'tekst_av'=>'Tekst av',
								'varighet'=>'Varighet');
	$felter['annet']	= array('tittel'=>'Tittel',
								'melodi_av'=>'Evt melodi',
								'tekst_av'=>'Evt tekst',
								'varighet'=>'Varighet');
	$felter['dans']		= array('tittel'=>'Tittel',
								'koreografi'=>'Koreografi',
								'varighet'=>'Varighet');
	$felter['teater']	= array('tittel'=>'Tittel',
								'melodi_av'=>'Komponist',
								'tekst_av'=>'Forfatter',
								'varighet'=>'Varighet');
	$felter['litteratur']=array('tittel'=>'Tittel',
								'melodi_av'=>'Evt komponist',
								'tekst_av'=>'Evt medforfatter',
								'varighet'=>'Varighet');
	$felter[6]			= array('tittel'=>'Navn på gruppen/artisten',
								'beskrivelse'=>'Beskrivelse');
	$felter[3]			= array('tittel'=>'Tittel',
								'beskrivelse'=>'Beskrivelse',
								'teknikk'=>'Teknikk',
								'type'=>'Type');
	$felter[2]			= array('tittel'=>'Tittel',
								'format'=>'Format',
								'varighet'=>'Varighet');
	switch($btid) {
		case 1:
			switch(strtolower($kategori)){
				case 'musikk':		return $felter['musikk'];
				case 'dans':		return $felter['dans'];
				case 'teater':		return $felter['teater'];
				case 'litteratur':	return $felter['litteratur'];
				default: 		return $felter['annet'];
			}
			break;
		case 2:
		case 3:
		case 6:
			return $felter[$btid];
	}
	return array();
}
*/
?>
