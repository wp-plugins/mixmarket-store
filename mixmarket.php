<?php
/*
Plugin Name: MixMarket Store
Plugin URI: http://www.mixmarket.biz/doc/partners/goods/programs/?from=wordpress
Description: The plugin allows to create goods catalog and integrate it with MixMarket network.
Version: 0.1
Author:  Партнерская сеть Миксмаркет 
Author URI: http://www.mixmarket.biz/doc/partners/goods/programs/?from=wordpress
License: GPLv3
*/

if ( !function_exists( 'add_action' ) ) {
    header( 'Status: 403 Forbidden' );
    header( 'HTTP/1.1 403 Forbidden' );
    exit();
}

if ( function_exists( 'add_action' ) ) {
    //WordPress definitions
    if ( !defined( 'WP_CONTENT_URL' ) )
        define( 'WP_CONTENT_URL', get_option( 'siteurl' ) . '/wp-content' );
    if ( !defined( 'WP_CONTENT_DIR' ) )
        define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
    if ( !defined( 'WP_PLUGIN_URL' ) )
        define( 'WP_PLUGIN_URL', WP_CONTENT_URL . '/plugins' );
    if ( !defined( 'WP_PLUGIN_DIR' ) )
        define( 'WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins' );
    if ( !defined( 'PLUGINDIR' ) )
        define( 'PLUGINDIR', 'wp-content/plugins' ); // Relative to ABSPATH. For back compat.
    if ( !defined('WP_LANG_DIR' ) )
        define( 'WP_LANG_DIR', WP_CONTENT_DIR . '/languages' );

    // plugin definitions
    define( 'MM_BASENAME', plugin_basename( __FILE__ ) );
    define( 'MM_BASEDIR', dirname( plugin_basename( __FILE__ ) ) );
    define( 'MM_DIR', dirname( __FILE__ ) );
    define( 'MM_TEXTDOMAIN', 'mixmarket' );
    define( 'MM_URL', WP_PLUGIN_URL . '/' . MM_BASEDIR );
}

//blocks data
global $mm_blocks;

//loading scripts
require_once( 'helpers/frontend_helpers.php' );
require_once( 'classes/base_model.php' );
require_once( 'classes/base_controller.php' );
require_once( 'classes/goods_model.php' );
require_once( 'classes/goods_types_model.php' );
require_once( 'classes/fields_model.php' );
require_once( 'helpers/ini_helper.php' );
require_once( 'helpers/category_helpers.php' );

//creating widget
require_once( 'classes/mm_widget.php' );
add_action('widgets_init', create_function('', 'return register_widget("MM_Widget");'));

if ( is_admin() ) {
    //creating admin interface
	if ( '' == session_id() ) {
		session_start();
	}

	require_once( 'libs/htmlpurifier-4.2.0-lite/library/HTMLPurifier.auto.php');
	global $purifier;

    //configuring HTMLPurifier
	$config = HTMLPurifier_Config::createDefault();
	$config->set( 'Cache.SerializerPath', WP_CONTENT_DIR . '/uploads' );
	$purifier = new HTMLPurifier( $config );

    require_once( 'libs/phpthumb/ThumbLib.inc.php');
    require_once( 'classes/mixmarket_class.php' );
    require_once( 'classes/categories_class.php' );
    require_once( 'classes/goods_class.php' );
    
    register_activation_hook( __FILE__, array( 'Mix_Market', 'create_tables' ) );
}
else {
	require_once( 'libs/mm_breadcrumbs.php' );
    //creating catalog on frontend
    require_once( 'classes/frontend_class.php' );
}

//loading localization file (.mo files should be in language folder)
add_action( 'init', 'mm_textdomain' );

function mm_textdomain() {
	if ( function_exists( 'load_plugin_textdomain' ) ) {
		load_plugin_textdomain( MM_TEXTDOMAIN, false, MM_TEXTDOMAIN . '/languages/' );
	}
}