<?php
/*
Plugin Name: Timeline Module For Divi
Plugin URI:  https://cooltimeline.com/divi
Description: A timeline module for Divi
Version:     1.0.3
Author:      CoolPlugins
Author URI:  https://coolplugins.net
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: timeline-module-for-divi
Domain Path: /languages

Timeline Module For Divi is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.

Timeline Module For Divi is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Timeline Module For Divi. If not, see https://www.gnu.org/licenses/gpl-2.0.html.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 

define('TMDIVI_V', '1.0.3');
define('TMDIVI_DIR', plugin_dir_path(__FILE__));
define('TMDIVI_URL', plugin_dir_url(__FILE__));
define('TMDIVI_MODULE_URL', plugin_dir_url(__FILE__) . 'includes/modules');
define('TMDIVI_MODULE_DIR', plugin_dir_path(__FILE__) . 'includes/modules');

register_activation_hook( __FILE__, array( 'TMDIVI_Timeline_Module_For_Divi', 'tmdivi_activate_plugin' ) );
class TMDIVI_Timeline_Module_For_Divi {

    public function __construct() {
        self::includes();
        add_action('divi_extensions_init', array($this, 'initialize_extension'));
        add_action( 'admin_init', array( $this, 'is_divi_theme_exist' ) );
        add_action('wp_loaded', array($this, 'load_child_items'));
    }


    public function is_divi_theme_exist(){
        if (!self::is_theme_activate('Divi')) {
            // Divi theme is not activated, display admin notice
            add_action('admin_notices', array($this, 'admin_notice_missing_divi_theme'));
        }   
        if ( is_admin() ) {
            require_once TMDIVI_DIR . 'admin/feedback/admin-feedback-form.php';
        }
    }
    /**
     * Initializes the extension.
     */
    public function initialize_extension() {
        require_once TMDIVI_DIR . '/includes/TimelineModuleForDivi.php';
    }
    
    public static function includes(){
        require_once TMDIVI_MODULE_DIR . '/assets-loader.php';
        new TMDIVI_AssetsLoader();
    }

    public static function is_theme_activate($target){
        $theme = wp_get_theme();
        if ($theme->name == $target || stripos($theme->parent_theme, $target) !== false) {
            return true;
        }
        if (apply_filters('divi_ghoster_ghosted_theme', '') == $target) {
            return true;
        }
        return false;
    }

    public function admin_notice_missing_divi_theme(){
        $message = esc_html__(
            'Timeline Module For Divi requires Divi (Theme) to be installed and activated.',
            'timeline-module-for-divi'
        );
        printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', esc_html( $message ) );
        deactivate_plugins(__FILE__);
    }  
    
    public function load_child_items()
    {
        require_once TMDIVI_MODULE_DIR . '/default-data-helper.php';
        if (!function_exists('et_fb_process_shortcode') || !class_exists(TMDIVI_DefaultDataHelper::class)) {
            return;
        }
        $data_helpers = new TMDIVI_DefaultDataHelper();
        $this->registerFiltersAndActions($data_helpers);
    }

    private function registerFiltersAndActions(TMDIVI_DefaultDataHelper $data_helpers)
    {
        add_filter('et_fb_backend_helpers', [$data_helpers, 'default_items_helpers'], 11);
        add_filter('et_fb_get_asset_helpers', [$data_helpers, 'asset_helpers'], 11);

        $enqueueScriptsCallback = function () use ($data_helpers) {
            wp_localize_script('et-frontend-builder', 'DCLBuilderBackend', $data_helpers->default_items_helpers());
        };

        add_action('wp_enqueue_scripts', $enqueueScriptsCallback);
        add_action('admin_enqueue_scripts', $enqueueScriptsCallback);
    }

    public static function tmdivi_activate_plugin() {
		update_option( 'tmdivi-v', TMDIVI_V );
		update_option( 'tmdivi-type', 'free' );
		update_option( 'tmdivi-installDate', gmdate( 'Y-m-d h:i:s' ) );
		update_option( 'tmdivi-defaultPlugin', true );

        if ( ! get_option( 'tmdivi-Boxes-ratingDiv' ) ) {
            update_option( 'tmdivi-Boxes-ratingDiv', 'no' );  // Update rating div
        }
	}

}

new TMDIVI_Timeline_Module_For_Divi();