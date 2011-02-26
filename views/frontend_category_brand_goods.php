<?php
if ( $breadcrumbs !== '' ) {
	echo '<div class="mm_breadcrumbs">' . $breadcrumbs . '</div>';
}
if ( FALSE !== $goods ) {
    $this->render( MM_DIR . '/views/frontend_category_search.php', array(
        'category' => $category,
        'fields' => $fields,
        'permalink' => $permalink,
        'categories' => $categories,
        'conditions' => $conditions,
        'fields_conditions' => $fields_conditions,
		'category_brands' => $category_brands,
		'brand' => $brand,
		'category_ids' => $category_ids,
    ) );
}
else {
    echo mm_get_multi_col_list( &$categories, $permalink, 1000, $cur_depth = 0, $category->id );
}
if ( false !== $category_brands ) {
	echo '<ul class="category_brands">';
	foreach ( $category_brands as $cur_brand ) {
		echo '<li>' . mm_get_category_brand_link( $category, $cur_brand, $permalink )
				. ' ' . $cur_brand[ 'items_count' ] . '</li>';
	}
	echo '</ul>';
}
?>
<div class="clear"></div>

<h1><?php _e( 'Category', MM_TEXTDOMAIN ); ?>: "<?php echo $category->gt_name; ?>".
	<?php _e( 'Brand', MM_TEXTDOMAIN ); ?>: "<?php echo $brand; ?>"</h1>

<div id="mm_category_goods">
<?php if ( FALSE === $goods ) {
//    echo '<h2>' . __( 'No items of the selected brand found in this category', MM_TEXTDOMAIN ) . '</h2>';
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
