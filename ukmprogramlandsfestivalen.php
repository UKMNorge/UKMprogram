<?php  
/* 
Plugin Name: UKM Program Landsfestivalen
Plugin URI: http://www.ukm-norge.no
Description: UKM Norge admin
Author: UKM Norge / Kushtrim Aliu
Version: 1.0 
Author URI: http://www.ukm-norge.no
*/

use UKMNorge\Wordpress\Modul;
use UKMNorge\Arrangement\Arrangement;


// TODO
#do_action('UKMprogramLandsfestivalen_save', 'lagre', $_POST['c_id']); @ hendelse save

require_once('UKM/Autoloader.php');

class UKMprogramLandsfestivalen extends Modul {
    public static $action = 'dashboard';
    public static $path_plugin = null;

    /**
     * Register hooks
     */
    public static function hook() {
		add_action(
			'wp_ajax_UKMprogramV2_ajax', 
			['UKMprogramLandsfestivalen','ajax']
		);

        if( get_option('pl_id') ) {
            add_action(
                'admin_menu', 
                ['UKMprogramLandsfestivalen', 'meny'],
                200
            );
        }
    }

    /**
     * Add menu
     */
    public static function meny() {		
		$arrangement = new Arrangement( get_option( 'pl_id ') );
		
		if(!$arrangement->erKunstgalleri()) {
			$page = add_submenu_page(
				'index.php',
				'Program Landsfestivalen', 
				'Program Landsfestivalen', 
				'editor', 
				'UKMprogramLandsfestivalen', 
				['UKMprogramLandsfestivalen', 'renderAdmin']
			);
	
			add_action(
				'admin_print_styles-' . $page,
				['UKMprogramLandsfestivalen', 'scripts_and_styles']
			);
		}
	}
	

	public static function meny_conditions( $_CONDITIONS ) {
		return array_merge( $_CONDITIONS, 
			['UKMprogramLandsfestivalen_renderAdmin' => 'monstring_er_registrert']
		);
	}

    public static function scripts_and_styles() {
		wp_enqueue_script('WPbootstrap3_js');
		wp_enqueue_style('WPbootstrap3_css');
		wp_enqueue_script('jqueryGoogleUI', '//ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/jquery-ui.min.js');
        wp_enqueue_style('UKMprogramLandsfestivalen_css', static::getPluginUrl() .'UKMprogram.css' );
        wp_enqueue_script('UKMprogramLandsfestivalen_js_supply', static::getPluginUrl() .'js/supply.js');
        wp_enqueue_script('UKMprogramLandsfestivalen_js_hendelse', static::getPluginUrl() .'js/hendelse.js');
        wp_enqueue_script('UKMprogramLandsfestivalen_js_hendelser', static::getPluginUrl() .'js/hendelser.js');
        wp_enqueue_script('UKMprogramLandsfestivalen_js_innslag', static::getPluginUrl() .'js/innslag.js');
        wp_enqueue_script('UKMprogramLandsfestivalen_js_filter', static::getPluginUrl() .'js/filter.js');
        wp_enqueue_script('UKMprogramLandsfestivalen_js_reverser', static::getPluginUrl() .'js/reverser.js');
        wp_enqueue_script('UKMprogramLandsfestivalen_js', static::getPluginUrl() .'UKMprogram.js', array( 'wp-color-picker' ));

        wp_enqueue_style( 'wp-color-picker' );
	}
	
	public static function save( $case ) {
		switch( $case ) {
			case 'hendelse':
				if( $_POST['id'] == 'new' ) {
					self::require('save/ny_hendelse.save.php');
				} else {
					self::require('save/hendelse.save.php');
				}
				break;
		}
	}
}

## HOOK MENU AND SCRIPTS
UKMprogramLandsfestivalen::init( __DIR__ );
UKMprogramLandsfestivalen::hook();