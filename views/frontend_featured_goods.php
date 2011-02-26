<?php include( MM_DIR . '/views/frontend_categories.php' ); ?>

<h1><?php _e( 'Featured items', MM_TEXTDOMAIN ); ?></h1>

<div id="mm_featured_goods">
<?php if ( FALSE === $goods ) {
    echo '<h3>' . __( 'No featured items found', MM_TEXTDOMAIN ) . '</h3>';
} else {
	foreach ( $goods as $key => $good ) {
		$this->render( MM_DIR . '/views/frontend_good_short.php', array(
			'good' => $good,
			'permalink' => $permalink,
		) );
	}
} ?>
</div>
