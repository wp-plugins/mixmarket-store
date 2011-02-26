<?php
/**
 * This script is called by ajax request and creates database dump.
 */
require_once( '../../../../wp-config.php' );

//checking user rights
if ( !current_user_can( 'manage_options' ) ) {
    header( 'HTTP/1.0 403 Forbidden' );
    exit;
}

require_once( '../classes/base_model.php' );
require_once( '../classes/goods_model.php' );
require_once( '../classes/fields_model.php' );
require_once( '../classes/goods_types_model.php' );

header( "Content-Type: text/plain" );
header( 'Content-Disposition: attachment; filename="mixmarket_data.sql"' );

/* The three lines below basically make the
download non-cacheable */
header( "Cache-control: private" );
header( 'Pragma: private' );
header( 'Expires: ' . date( 'D, j M Y H:i:s T') );

global $wpdb;

//mm_goods_types table
$gtt = new Goods_Types_Model();
$mm_goods_types = $wpdb->get_results( 'SELECT * FROM ' . Goods_Types_Model::get_table_name(), ARRAY_A );

if ( !empty( $mm_goods_types ) ) {
    echo 'DELETE FROM ##TABLE_PREFIX##' . substr( Goods_Types_Model::get_table_name(), strlen( $wpdb->prefix) ) . ";\n\n";
    $gtt_types = $gtt->get_types();
    foreach ( $mm_goods_types as $key => $item ) {
        $values = array_values( $item );
        $res_array = array();
        for ( $i = 0; $i < count( $values ); $i++ ) {
            if ( $values[ $i ] == null ) {
                $res_array[] = 'NULL';
            }
            elseif ( $i > 0 && $gtt_types[ $i - 1 ] == '%s' ) {
                $res_array[] = "'" . $wpdb->escape( $values[ $i ] ) . "'";
            }
            else {
                $res_array[] = $values[ $i ];
            }
        }
        echo 'INSERT INTO ##TABLE_PREFIX##' . substr( Goods_Types_Model::get_table_name(), strlen( $wpdb->prefix) )
				. ' VALUES (' . implode( ', ', $res_array ) . "); \n";
    }
    echo "\n\n";
}

//mm_goods table
$gt = new Goods_Model();
$mm_goods = $wpdb->get_results( 'SELECT * FROM ' . Goods_Model::get_table_name(), ARRAY_A );

if ( !empty( $mm_goods ) ) {
    echo 'DELETE FROM ##TABLE_PREFIX##' . substr( Goods_Model::get_table_name()
			, strlen( $wpdb->prefix) ) . ";\n\n";
    $gt_types = $gt->get_types();
    foreach ($mm_goods as $key => $item ) {
        $values = array_values( $item );
        $res_array = array();
        for ( $i = 0; $i < count( $values ); $i++ ) {
            if ( $values[ $i ] == null ) {
                $res_array[] = 'NULL';
            }
            elseif ( $i > 0 && $gt_types[ $i - 1 ] == '%s' ) {
                $res_array[] = "'" . $wpdb->escape( $values[ $i ] ) . "'";
            }
            else {
                $res_array[] = $values[ $i ];
            }
        }
        echo 'INSERT INTO ##TABLE_PREFIX##' . substr( Goods_Model::get_table_name(), strlen( $wpdb->prefix ) )
				. ' VALUES (' . implode( ', ', $res_array ) . "); \n";
    }
    echo "\n\n";
}

//mm_fields table
$ft = new Goods_Model();
$mm_fields = $wpdb->get_results( 'SELECT * FROM ' . Fields_Model::get_table_name(), ARRAY_A );

if ( !empty( $mm_fields ) ) {
    echo 'DELETE FROM ##TABLE_PREFIX##' . substr( Fields_Model::get_table_name(), strlen( $wpdb->prefix ) ) . ";\n\n";
    $ft_types = $ft->get_types();
    foreach ($mm_fields as $key => $item ) {
        $values = array_values( $item );
        $res_array = array();
        for ( $i = 0; $i < count( $values ); $i++ ) {
            if ( $values[ $i ] == null ) {
                $res_array[] = 'NULL';
            }
            elseif ( $i > 0 && $ft_types[ $i - 1 ] == '%s' ) {
                $res_array[] = "'" . $wpdb->escape( $values[ $i ] ) . "'";
            }
            else {
                $res_array[] = $values[ $i ];
            }
        }
        echo 'INSERT INTO ##TABLE_PREFIX##' . substr( Fields_Model::get_table_name(), strlen( $wpdb->prefix ) ) . ' VALUES (' . implode( ', ', $res_array ) . "); \n";
    }
    echo "\n\n";
}