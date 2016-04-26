<?php
/*
Plugin Name: Alphabet Listing
Plugin URI: http://www.tipsandtricks-hq.com/?p=4873
Description: Can be used to list post, page or categories with A to Z listing anywhere on your WordPress site
Version: 1.2
Author: Tips and Tricks HQ
Author URI: http://www.tipsandtricks-hq.com/
License: GPL2
*/

//initialize class
if (!class_exists("AplhabetPlugin")) {
	define('AL_PATH', plugin_dir_path( __FILE__ ) ); 
	define('AL_URL', plugin_dir_url( __FILE__ ) ); 
	define('AL_NAME', 'Alphabet Listing');
	define('AL_DIRECTORY', 'alphabet-listing');
	define('AL_VERSION', '1.0.0' );
	define('AL_BUILD', '1' );
	// i18n plugin domain for language files
	define( 'AL_I18N_DOMAIN', 'aplhabet_listing' );
 	require_once(AL_PATH . '/alphabet_listing_main.php');
}

$wp_al_plugin = new AlphabetPlugin();

//Actions and Filters	
if (isset($wp_al_plugin)) {
	//int
	$wp_al_plugin->aplhabet_listing_set_lang_file();
	//Actions
	add_action( 'wp_enqueue_scripts', array($wp_al_plugin,'inject_css'));
	add_action( 'admin_init', array($wp_al_plugin,'aplhabet_listing_register_settings'));
	add_action( 'admin_menu', array($wp_al_plugin,'alphabet_listing_create_menu'));
	//register hooks
	register_activation_hook(__FILE__, array($wp_al_plugin,'aplhabet_listing_activate'));
	register_deactivation_hook(__FILE__, array($wp_al_plugin,'aplhabet_listing_deactivate'));
	//Filters
	add_shortcode( 'atoz', array($wp_al_plugin,'atoz_shortcode') );
	if (!is_admin())
	{add_filter('widget_text', 'do_shortcode');}
	add_filter('the_excerpt', 'do_shortcode',11);
}