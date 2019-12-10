<?php  
/* 
Plugin Name: UKM Program
Plugin URI: http://www.ukm-norge.no
Description: UKM Norge admin
Author: UKM Norge / M Mandal 
Version: 2.0 
Author URI: http://www.ukm-norge.no
*/

use UKMNorge\Wordpress\Modul;

// TODO
#do_action('UKMprogram_save', 'lagre', $_POST['c_id']); @ hendelse save

require_once('UKM/Autoloader.php');

class UKMprogram extends Modul {
    public static $action = 'dashboard';
    public static $path_plugin = null;

    /**
     * Register hooks
     */
    public static function hook() {
		add_action(
			'wp_ajax_UKMprogramV2_ajax', 
			['UKMprogram','ajax']
		);

        add_action(
			'admin_menu', 
			['UKMprogram', 'meny'],
			200
		);
    }

    /**
     * Add menu
     */
    public static function meny() {		
		$page = add_submenu_page(
			'UKMmonstring',
			'Program', 
			'Program', 
			'editor', 
			'UKMprogram', 
			['UKMprogram', 'renderAdmin']
		);

		add_action(
			'admin_print_styles-' . $page,
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
        wp_enqueue_script('UKMprogram_js', plugin_dir_url( __FILE__ ) .'UKMprogram.js', array( 'wp-color-picker' ));

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
UKMprogram::init( __DIR__ );
UKMprogram::hook();