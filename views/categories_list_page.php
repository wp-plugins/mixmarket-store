<div class="wrap">
<h2><?php _e( 'Categories List', MM_TEXTDOMAIN ); ?></h2>

<?php if ( FALSE !== ( $mes = $this->get_message() ) ) { ?>
<div class="updated settings-error" id="setting-error-settings_updated">
    <p><strong><?php echo $mes; ?></strong></p>
</div>
<?php } ?>

<p><a class="button" href="admin.php?page=<?php echo $page_name; ?>&amp;action=create"><?php _e( __( 'Create category', MM_TEXTDOMAIN ) ); ?></a></p>

<div class="tablenav">
    <div class="tablenav-pages">
    </div> <!-- div.tablenav-pages -->
</div> <!-- div.tablenav -->
<div class="clear" />

<?php
if ( $categories ) {
?>

<table cellspacing="0" class="widefat fixed">
	<thead>
        <tr>
            <th style="width:40px" class="manage-column" scope="col"><?php _e( 'Id', MM_TEXTDOMAIN ); ?></th>
            <th style="" class="manage-column" scope="col"><?php _e( 'Name', MM_TEXTDOMAIN ); ?></th>
            <th style="" class="manage-column" scope="col"><?php _e( 'Slug', MM_TEXTDOMAIN ); ?></th>
        </tr>
    </thead>
    <tfoot>
        <tr>
            <th style="width:40px" class="manage-column" scope="col"><?php _e( 'Id', MM_TEXTDOMAIN ); ?></th>
            <th style="" class="manage-column" scope="col"><?php _e( 'Name', MM_TEXTDOMAIN ); ?></th>
            <th style="" class="manage-column" scope="col"><?php _e( 'Slug', MM_TEXTDOMAIN ); ?></th>
        </tr>
	</tfoot>
    <tbody>
<?php
	mm_the_nested_table( $categories, NULL, 0, $page_name, $permalink );
?>
    </tbody>
</table>

<?php
}
else {
    _e( 'No categories found', MM_TEXTDOMAIN );
}
?>

</div> <!-- div.wrap -->