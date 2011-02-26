<div class="wrap">
<h2><?php _e( 'Items List', MM_TEXTDOMAIN ); ?></h2>

<?php if ( FALSE !== ( $mes = $this->get_message() ) ) { ?>
<div class="updated settings-error" id="setting-error-settings_updated">
    <p><strong><?php echo $mes; ?></strong></p>
</div>
<?php } ?>

<?php if ( FALSE !== $categories ) { ?>
<form method="post" action="admin.php?page=<?php echo $page_name; ?>&amp;action=create">
<p>
	<label><?php _e( 'Item category', MM_TEXTDOMAIN ); ?>
	<select name="g_type_id">
        <?php
		if ( FALSE !== $categories ) {
			mm_the_nested_select( $categories );
		}
        ?>
    </select></label>
	<input type="submit" class="button-secondary" value="<?php _e( 'Create Item', MM_TEXTDOMAIN ); ?>" />
</p>
</form>
<?php } ?>

<?php if ( FALSE !== $categories ) { ?>
<form method="get" action="admin.php" class="mm_inline">
<p class="mm_inline">
	<input type="hidden" name="page" value="<?php echo $page_name; ?>" />
	<select name="category_id">
        <?php
		if ( FALSE !== $categories ) {
			mm_the_nested_select( $categories );
		}
        ?>
    </select>
	<input type="submit" class="button-secondary" value="<?php _e( 'Filter', MM_TEXTDOMAIN ); ?>" />
</p>
</form>
<?php } ?>

<?php if ( false != $current_category ) { ?>
<div class="mm_selected_category mm_inline"><?php
	_e( 'Selected category:', MM_TEXTDOMAIN );
	echo ' ' . mm_get_category_link( $current_category, $catalog_page );
	echo '&nbsp;<a class="button-secondary" href="admin.php?page=' . $page_name . '">' . __( 'Reset filter', MM_TEXTDOMAIN ) . '</a>';
?></div>
<?php } ?>

<?php
if ( $goods ) {
?>

<div class="tablenav">
    <div class="tablenav-pages">
		<span class="displaying-num"><?php _e( 'Pages:', MM_TEXTDOMAIN ); ?></span>
		<?php echo $page_links; ?>
    </div> <!-- div.tablenav-pages -->
</div> <!-- div.tablenav -->
<div class="clear" />

<table cellspacing="0" class="widefat fixed">
	<thead>
        <tr>
            <th style="width:40px" class="manage-column" scope="col"><?php _e( 'Id', MM_TEXTDOMAIN ); ?></th>
            <th style="" class="manage-column" scope="col"><?php _e( 'Title', MM_TEXTDOMAIN ); ?></th>
            <th style="" class="manage-column" scope="col"><?php _e( 'Brand', MM_TEXTDOMAIN ); ?></th>
            <th style="" class="manage-column" scope="col"><?php _e( 'Model', MM_TEXTDOMAIN ); ?></th>
            <th style="" class="manage-column" scope="col"><?php _e( 'Category', MM_TEXTDOMAIN ); ?></th>
        </tr>
    </thead>
    <tfoot>
        <tr>
            <th style="width:40px" class="manage-column" scope="col"><?php _e( 'Id', MM_TEXTDOMAIN ); ?></th>
            <th style="" class="manage-column" scope="col"><?php _e( 'Title', MM_TEXTDOMAIN ); ?></th>
            <th style="" class="manage-column" scope="col"><?php _e( 'Brand', MM_TEXTDOMAIN ); ?></th>
            <th style="" class="manage-column" scope="col"><?php _e( 'Model', MM_TEXTDOMAIN ); ?></th>
            <th style="" class="manage-column" scope="col"><?php _e( 'Category', MM_TEXTDOMAIN ); ?></th>
        </tr>
	</tfoot>
    <tbody>
<?php
    foreach ( $goods as $good ) {
?>
        <tr valign="top" valign="top">
            <td style="width:40px"><?php echo $good->id; ?></td>
            <td>
                <strong><?php echo $good->g_title; ?></strong>
                <div class="row-actions">
                    <span class="view">
                        <a href="<?php echo mm_get_view_link( $good, $catalog_page ); ?>"><?php _e( 'View', MM_TEXTDOMAIN ); ?></a> |
                    </span>
                    <span class="edit">
                        <a href="admin.php?page=<?php echo $page_name; ?>&amp;action=edit&amp;good_id=<?php echo $good->id; ?>"><?php _e( 'Edit', MM_TEXTDOMAIN ); ?></a> |
                    </span>
                    <span class="trash">
                        <a href="admin.php?page=<?php echo $page_name; ?>&amp;action=delete&amp;good_id=<?php echo $good->id; ?>&amp;_mm_nonce=<?php echo wp_create_nonce( 'delete_good' ); ?>" class="submitdelete"><?php _e( 'Delete', MM_TEXTDOMAIN ); ?></a>
                    </span>
                </div>
            </td>
            <td><?php echo $good->g_brand; ?></td>
            <td><?php echo $good->g_model_title; ?></td>
            <td><?php
			foreach ( $categories as $category ) {
				if ( $good->g_type_id == $category->id ) {
					echo mm_get_category_link( $category, $catalog_page );
					break;
				}
			}
			?></td>
        </tr>
<?php
    }
?>
    </tbody>
</table>


<div class="tablenav">
    <div class="tablenav-pages">
		<span class="displaying-num"><?php _e( 'Pages:', MM_TEXTDOMAIN ); ?></span>
		<?php echo $page_links; ?>
    </div> <!-- div.tablenav-pages -->
</div> <!-- div.tablenav -->
<div class="clear" />

<?php
}
else {
    _e( 'No items found', MM_TEXTDOMAIN );
}
?>

</div> <!-- div.wrap -->