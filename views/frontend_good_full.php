<?php
if ( $breadcrumbs !== '' ) {
	echo '<div class="mm_breadcrumbs">' . $breadcrumbs . '</div>';
}
?>
<div id="item_<?php echo $good->id; ?>" class="mm_item_full">
	<h1><?php echo mm_get_title_link( $good, $permalink ); ?></h1>
	<img src="<?php echo mm_get_image_url( $good, 'card' ); ?>" alt="<?php echo $good->g_title; ?>" />
	<div class="short_info">
	<div class="mm_brand"><strong><?php _e( 'Brand', MM_TEXTDOMAIN ); ?></strong>: <?php echo mm_get_brand_link( $good, $permalink ); ?></div>
	<div class="mm_model"><strong><?php _e( 'Model', MM_TEXTDOMAIN ); ?></strong>: <?php echo mm_get_model_link( $good, $permalink ); ?></div>
<?php
	if ( FALSE !== $fields ) {
		foreach ( $fields as $key => $field ) {
//			if ( $custom_fields[ $field[ 'f_name' ] ][ 'type' ] == 'select' ) {
//				$vals = explode( '|', $custom_fields[ $field[ 'f_name' ] ][ 'values' ] );
//				$value = $vals[ $field[ 'f_value' ] ];
//			}
			if ( $custom_fields[ $field[ 'f_name' ] ][ 'type' ] == 'checkbox' ) {
                $value = ( $field[ 'f_value' ] == 'on' ) ? __( 'Yes', MM_TEXTDOMAIN ) : __( 'No', MM_TEXTDOMAIN );
			}
            else {
				$value = $field[ 'f_value' ];
            }
			if ( $value != '' ) {
				echo '<div><strong>' . $custom_fields[ $field[ 'f_name' ] ][ 'title' ] . '</strong>: ' . $value . '</div>';
			}
		}
	}
?>
	</div>
	<div class="clear"></div>
<?php
foreach ( $blocks as $block_id ) {
	switch ( $block_id ) {
		case 0:
			?>
			<div class="mm_buy">
				<div id="mixgk_<?php echo $partner_id; ?>"></div>
			</div>
			<?php
			break;
		case 1:
			?>
			<div class="mm_description">
				<p><strong><?php echo $blocks_data[ $block_id ][ 'title' ]; ?></strong></p>
				<div class="mm_description_content"><?php echo $good->g_description; ?></div>
			</div>
			<?php
			break;
		case 2:
			?>
			<div class="mm_links">
				<p><strong><?php echo $blocks_data[ $block_id ][ 'title' ]; ?></strong></p>
				<div class="mm_links_content"><?php echo $good->g_review_links; ?></div>
			</div>
			<?php
			break;
	}
}
?>
</div>