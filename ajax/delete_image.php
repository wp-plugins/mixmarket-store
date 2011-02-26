<?php
require_once( '../../../../wp-config.php' );
require_once( '../classes/goods_model.php' );
require_once( '../libs/htmlpurifier-4.2.0-lite/library/HTMLPurifier.auto.php');

global $purifier;

//configuring HTMLPurifier
$config = HTMLPurifier_Config::createDefault();
$config->set( 'Cache.SerializerPath', WP_CONTENT_DIR . '/uploads' );
$purifier = new HTMLPurifier( $config );

if ( !current_user_can( 'edit_post' ) ) {
	echo json_encode( array( 'error' => 'Forbidden' ) );
	die();
}

if ( !isset( $_POST[ 'id' ] ) || trim( $_POST[ 'id' ] ) == '' ) {
	echo json_encode( array( 'error' => 'No input data specified' ) );
	die();
}

$good = Goods_Model::get_by_id( $_POST[ 'id' ] );

if ( false === $good ) {
	echo json_encode( array( 'error' => 'The item is not found' ) );
	die();
}

//$upload_dir = wp_upload_dir();
//$base_dir = $upload_dir[ 'basedir' ];

$card = mm_get_image_url( $good, 'card' );
$thumb = mm_get_image_url( $good, 'thumb' );

@unlink( WP_CONTENT_DIR . $good->g_image );
@unlink( str_replace( WP_CONTENT_URL, WP_CONTENT_DIR, $card ) );
@unlink( str_replace( WP_CONTENT_URL, WP_CONTENT_DIR, $thumb ) );

$good->g_image = '';
$good->g_image_url = '';
$good->g_image_thumb = '';
$good->g_image_thumb_url = '';

$good->save();

echo json_encode( array( 'status' => 'OK' ) );