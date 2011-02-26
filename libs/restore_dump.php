<?php
/**
 * This script is called by ajax request and restores database dump.
 */
require_once( '../../../../wp-config.php' );

//checking user rights
if ( !current_user_can( 'manage_options' ) ) {
    header( 'HTTP/1.0 403 Forbidden' );
    exit;
}

if ( !wp_verify_nonce( $_POST[ 'mm_nonce' ], 'restore_dump' ) ) die( "Security check" );

global $wpdb, $EZSQL_ERROR;

if ( isset( $_FILES[ "mm_db_dump" ] ) ) {
    if ( is_uploaded_file( $_FILES[ "mm_db_dump" ][ "tmp_name" ] ) ) {
        $sql = file_get_contents( $_FILES[ "mm_db_dump" ][ 'tmp_name' ] );

		$sql = str_replace('##TABLE_PREFIX##', $wpdb->prefix, $sql);

        $sql_requests = array();
        preg_match_all( '/^(INSERT|DELETE).*?\;\s*?$/ims', $sql, $sql_requests );
        foreach ( $sql_requests[ 0 ] as $request ) {

            $wpdb->query( $request );
            
            if ( $EZSQL_ERROR ) {
                wp_redirect( $_SERVER[ 'HTTP_REFERER' ] . '&mm_restore_mes=err' );
                exit;
            }
        }
        wp_redirect( $_SERVER[ 'HTTP_REFERER' ] . '&mm_restore_mes=ok' );
    }
}