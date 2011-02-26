<div id="mm_categories">
<?php echo '<h1>' . __( 'Categories', MM_TEXTDOMAIN ) . '</h1>';
if ( FALSE === $categories ) {
    echo '<h2>' . __( 'No categories found', MM_TEXTDOMAIN ) . '</h2>';
} else {
    echo mm_get_multi_col_list( $categories, $permalink, 2 );
} ?>
</div>
<div class="clear"></div>