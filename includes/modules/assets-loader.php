<?php

if( !defined('ABSPATH') ){
    exit;
}

class TMDIVI_AssetsLoader{
    public function __construct(){
		add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
    }

    public function enqueue_scripts(){
        $js_path = TMDIVI_URL . 'assets/js/';
        wp_register_script('tmdivi-vertical', $js_path . 'tm_divi_vertical.min.js', array('jquery'),TMDIVI_V , true);    


        // load assets only on visual builder
        // if (function_exists('et_core_is_fb_enabled') && et_core_is_fb_enabled()) {
        // }  
  }

}

