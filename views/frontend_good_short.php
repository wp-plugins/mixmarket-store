<div id="item_<?php echo $good->id; ?>" class="mm_item_short">
	<h2><?php echo mm_get_title_link( $good, $permalink ); ?></h2>
	<img src="<?php echo mm_get_image_url( $good, 'card' ); ?>" alt="<?php echo $good->g_title; ?>" />
	<div class="short_info">
		<div class="mm_brand"><strong><?php _e( 'Brand', MM_TEXTDOMAIN ); ?></strong>: <?php echo mm_get_brand_link( $good, $permalink ); ?></div>
		<div class="mm_model"><strong><?php _e( 'Model', MM_TEXTDOMAIN ); ?></strong>: <?php echo mm_get_model_link( $good, $permalink ); ?></div>
	</div>
	<div class="clear"></div>
	<div class="mm_description">
		<p><strong><?php _e( 'Description', MM_TEXTDOMAIN ); ?></strong></p>
		<div class="mm_description_content"><?php echo force_balance_tags( mm_truncate_utf8( $good->g_description, 100, TRUE, TRUE ) ); ?></div>
	</div>
</div>