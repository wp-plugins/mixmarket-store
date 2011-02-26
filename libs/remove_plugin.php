<?php
/**
 * This script deactivates plugin and removes its database.
 */
require_once( '../../../../wp-config.php' );

//checking user rights
if ( !current_user_can( 'manage_options' ) ) {
    header( 'HTTP/1.0 403 Forbidden' );
    exit;
}

if ( !wp_verify_nonce( $_POST[ 'mm_remove_nonce' ], 'remove_plugin' ) ) die( "Security check" );

global $wpdb, $EZSQL_ERROR;

//removing plugin tables
$wpdb->query( 'DROP TABLE ' . Fields_Model::get_table_name() );
$wpdb->query( 'DROP TABLE ' . Goods_Model::get_table_name() );
$wpdb->query( 'DROP TABLE ' . Goods_Types_Model::get_table_name() );

//deactivating plugin
$active_plugins = get_option('active_plugins');

if ( is_array( $active_plugins ) ) {
    foreach ( $active_plugins as $key => $plugin ) {
        if ( 'mixmarket/mixmarket.php' == $plugin ) {
            unset( $active_plugins[ $key ] );
        }
    }
    update_option( 'active_plugins', $active_plugins );
}

//redirect to Dashboard
$admin_url = '';
if ( function_exists( 'get_admin_url' ) ) {
    $admin_url = get_admin_url();
}
else {
    $admin_url = get_home_url();
    if ( '/' == substr( $admin_url, -1, 1 ) ) {
        $admin_url .= 'wp-admin/index.php';
    }
    else {
        $admin_url .= '/wp-admin/index.php';
    }
}

wp_redirect( $admin_url );
