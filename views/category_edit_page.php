<div class="wrap">
<h2><?php _e( 'Update Category', MM_TEXTDOMAIN ); ?></h2>

<?php if ( FALSE !== ( $mes = $this->get_message() ) ) { ?>
<div class="updated settings-error" id="setting-error-settings_updated">
    <p><strong><?php echo $mes; ?></strong></p>
</div>
<?php } ?>

<p><a class="button" href="admin.php?page=<?php echo $page_name; ?>&amp;action=create"><?php _e( __( 'Create category', MM_TEXTDOMAIN ) ); ?></a></p>

<div id="mm_category_form">
<form method="post" action="admin.php?page=<?php echo $page_name; ?>&amp;action=edit&amp;category_id=<?php echo $category->id; ?>">
    <?php wp_nonce_field( 'edit_category', 'mm_nonce' ); ?>
    <table class="form-table" id="mm_category_form_table">
        <tr valign="top">
        <th scope="row"><?php _e( 'Category name', MM_TEXTDOMAIN ); ?></th>
        <td>
            <input type="text" name="mm_category[gt_name]" value="<?php echo htmlspecialchars( $category->gt_name ); ?>" size="70" />
            <?php echo $category->get_error( 'gt_name', '<span class="error">', '</span>' ); ?>
        </td>
        </tr>

        <tr valign="top">
        <th scope="row"><?php _e( 'Category fields', MM_TEXTDOMAIN ); ?></th>
        <td>
            <textarea class="category_fields" name="mm_category[gt_fields]" cols="57" rows="10"><?php echo $category->gt_fields; ?></textarea>
            <?php echo $category->get_error( 'gt_fields', '<span class="error">', '</span>' ); ?>
        </td>
        </tr>

        <tr valign="top">
        <th scope="row"><?php _e( 'Category slug', MM_TEXTDOMAIN ); ?></th>
        <td>
            <input type="text" name="mm_category[gt_slug]" value="<?php echo $category->gt_slug; ?>" size="70" />
            <?php echo $category->get_error( 'gt_slug', '<span class="error">', '</span>' ); ?>
        </td>
        </tr>

        <tr valign="top">
        <th scope="row"><?php _e( 'Category parent', MM_TEXTDOMAIN ); ?></th>
        <td>
            <select name="mm_category[gt_parent]">
                <option value="0">---</option>
        <?php
			if ( FALSE !== $categories ) {
				mm_the_nested_select( $categories, NULL, 0, $category, $category->gt_parent );
			}
        ?>
            </select>
        </td>
        </tr>
        
        <tr valign="top">
        <th scope="row"><?php _e( 'Category description', MM_TEXTDOMAIN ); ?></th>
        <td>
            <textarea name="mm_category[gt_description]" cols="57" rows="10"><?php echo $category->gt_description; ?></textarea>
            <?php echo $category->get_error( 'gt_description', '<span class="error">', '</span>' ); ?>
        </td>
        </tr>

        <tr valign="top">
        <th scope="row"><?php _e( 'Category title', MM_TEXTDOMAIN ); ?></th>
        <td>
            <input type="text" name="mm_category[gt_title]" value="<?php echo $category->gt_title; ?>" size="70" />
            <?php echo $category->get_error( 'gt_title', '<span class="error">', '</span>' ); ?>
        </td>
        </tr>

        <tr valign="top">
        <th scope="row"><?php _e( 'meta-description tag', MM_TEXTDOMAIN ); ?></th>
        <td>
            <input type="text" name="mm_category[gt_meta_description]" value="<?php echo $category->gt_meta_description; ?>" size="70" />
            <?php echo $category->get_error( 'gt_meta_description', '<span class="error">', '</span>' ); ?>
        </td>
        </tr>
		
        <tr valign="top">
        <th scope="row"><?php _e( 'meta-keywords tag', MM_TEXTDOMAIN ); ?></th>
        <td>
            <input type="text" name="mm_category[gt_meta_keywords]" value="<?php echo $category->gt_meta_keywords; ?>" size="70" />
            <?php echo $category->get_error( 'gt_meta_keywords', '<span class="error">', '</span>' ); ?>
        </td>
        </tr>
    </table>

    <p class="submit">
    <input type="submit" class="button-primary" value="<?php _e( 'Save Changes' ); ?>" />
    </p>
</form>
</div> <!-- div#mm_category_form -->

<?php $this->render( MM_DIR . '/views/category_fields_info.php'); ?>

</div> <!-- div.wrap -->