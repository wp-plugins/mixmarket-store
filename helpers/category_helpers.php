<?php
/**
 * This recursive function creates nested list of categories options.
 * Current category will be omitted to protect from moving subcategory higher than its parent.
 *
 * @param array $categories all categories
 * @param int $parent_id parent category id
 * @param int $depth recursion depth
 * @param Goods_Types_Model $current_cat current category
 * @param int $selected_id selected category id (parent category)
 */
function mm_the_nested_select( $categories, $parent_id = NULL, $depth = 0, $current_cat = null, $selected_id = 0 ) {
	$depth++;
	foreach ( $categories as $category ) {
		if ( $category->gt_parent == $parent_id && $category->id != $current_cat->id ) {
			$prefix = '';
			for ( $i = 0; $i < $depth - 1; $i++ ) {
				$prefix .= '&mdash;';
			}
			$selected = '';
			if ( $category->id == $selected_id ) {
				$selected = ' selected="selected"';
			}
			echo '<option value="' . $category->id . '"' . $selected . '>' . $prefix . ' ' . htmlspecialchars( $category->gt_name ) . '</option>';
			mm_the_nested_select( $categories, $category->id, $depth, $current_cat, $selected_id );
		}
	}
}

/**
 * This method generates table with categories list.
 * Categories are grouped by parent.
 *
 * @param array $categories all categories
 * @param int $parent_id parent category id
 * @param int $depth recursion depth
 * @param string $page_name plugin page name
 */
function mm_the_nested_table( $categories, $parent_id = NULL, $depth = 0, $page_name = '', $permalink = '' ) {
	$depth++;
    foreach ( $categories as $category ) {
		if ( $category->gt_parent == $parent_id ) {
			$prefix = '';
			for ( $i = 0; $i < $depth - 1; $i++ ) {
				$prefix .= '&mdash;';
			}
?>
        <tr valign="top" valign="top">
            <td style="width:40px"><?php echo $category->id; ?></td>
            <td>
                <strong><?php echo $prefix . ' ' . $category->gt_name; ?></strong>
                <div class="row-actions">
                    <span class="view">
                        <a href="<?php echo mm_get_category_url( $category, $permalink ); ?>"><?php _e( 'View', MM_TEXTDOMAIN ); ?></a> |
                    </span>
                    <span class="edit">
                        <a href="admin.php?page=<?php echo $page_name; ?>&amp;action=edit&amp;category_id=<?php echo $category->id; ?>"><?php _e( 'Edit', MM_TEXTDOMAIN ); ?></a> |
                    </span>
                    <span class="trash">
                        <a href="admin.php?page=<?php echo $page_name; ?>&amp;action=delete&amp;category_id=<?php echo $category->id; ?>&amp;_mm_nonce=<?php echo wp_create_nonce( 'delete_category' ); ?>" class="submitdelete"><?php _e( 'Delete', MM_TEXTDOMAIN ); ?></a>
                    </span>
                </div>
            </td>
            <td><?php echo $category->gt_slug; ?></td>
        </tr>
<?php
			mm_the_nested_table( $categories, $category->id, $depth, $page_name, $permalink );
		}
    }
}
