<?php
if ( $breadcrumbs !== '' ) {
	echo '<div class="mm_breadcrumbs">' . $breadcrumbs . '</div>';
}

$this->render( MM_DIR . '/views/frontend_category_search.php', array(
	'category' => $category,
	'fields' => $fields,
	'permalink' => $permalink,
	'categories' => $categories,
	'conditions' => $conditions,
	'fields_conditions' => $fields_conditions,
	'category_brands' => $category_brands,
	'category_ids' => $category_ids,
) ); ?>

<h1><?php _e( 'Search results', MM_TEXTDOMAIN ); ?></h1>

<div id="mm_search_goods">
<?php if ( FALSE === $goods ) {
    echo '<h2>' . __( 'No items found', MM_TEXTDOMAIN ) . '</h2>';
} else {
	foreach ( $goods as $key => $good ) {
		$this->render( MM_DIR . '/views/frontend_good_short.php', array(
			'good' => $good,
			'permalink' => $permalink,
		) );
	}
} ?>
</div>
