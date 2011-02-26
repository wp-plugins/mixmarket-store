<?php
if ( $breadcrumbs !== '' ) {
	echo '<div class="mm_breadcrumbs">' . $breadcrumbs . '</div>';
}
?>
<h1><?php _e( 'Brand', MM_TEXTDOMAIN ); ?>: "<?php echo $brand; ?>"</h1>

<div id="mm_brand_goods">
<?php if ( FALSE === $goods ) {
    echo '<h2>' . __( 'No items found for this brand', MM_TEXTDOMAIN ) . '</h2>';
} else {
	foreach ( $goods as $key => $good ) {
		$this->render( MM_DIR . '/views/frontend_good_short.php', array(
			'good' => $good,
			'permalink' => $permalink,
		) );
	}
	if ( null != $pagination ) {
		echo __( 'Pages:', MM_TEXTDOMAIN ) . implode( '&nbsp;', $pagination);
	}
} ?>
</div>
