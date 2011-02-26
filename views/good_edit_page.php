<div class="wrap">
<h2><?php _e( 'Update Item', MM_TEXTDOMAIN ); ?></h2>

<?php if ( FALSE !== ( $mes = $this->get_message() ) ) { ?>
<div class="updated settings-error" id="setting-error-settings_updated">
    <p><strong><?php echo $mes; ?></strong></p>
</div>
<?php } ?>

<form method="post" enctype="multipart/form-data" action="admin.php?page=<?php echo $page_name; ?>&amp;action=edit&amp;good_id=<?php echo $good->id; ?>">
    <?php wp_nonce_field( 'edit_good', 'mm_nonce' ); ?>
    <table class="form-table">
        <tr valign="top">
        <th scope="row"><?php _e( 'Item title', MM_TEXTDOMAIN ); ?></th>
        <td>
            <input type="text" name="mm_good[g_title]" value="<?php echo htmlspecialchars( $good->g_title ); ?>" size="70" />
            <?php echo $good->get_error( 'g_title', '<span class="error">', '</span>' ); ?>
        </td>
        </tr>

		<tr valign="top">
        <th scope="row"><?php _e( 'Item brand', MM_TEXTDOMAIN ); ?></th>
        <td>
            <input type="text" id="mm_good_g_brand" name="mm_good[g_brand]" value="<?php echo htmlspecialchars( $good->g_brand ); ?>" size="70" />
            <?php echo $good->get_error( 'g_brand', '<span class="error">', '</span>' ); ?>
        </td>
        </tr>

		<tr valign="top">
        <th scope="row"><?php _e( 'Item model', MM_TEXTDOMAIN ); ?></th>
        <td>
            <input type="text" name="mm_good[g_model_title]" value="<?php echo htmlspecialchars( $good->g_model_title ); ?>" size="70" />
            <?php echo $good->get_error( 'g_model_title', '<span class="error">', '</span>' ); ?>
        </td>
        </tr>

        <tr valign="top">
        <th scope="row"><?php _e( 'Item description', MM_TEXTDOMAIN ); ?></th>
        <td>
            <textarea name="mm_good[g_description]" cols="70" rows="10"><?php echo $good->g_description; ?></textarea>
            <?php echo $good->get_error( 'g_description', '<span class="error">', '</span>' ); ?>
        </td>
        </tr>

		<tr valign="top">
        <th scope="row"><?php _e( 'Item image', MM_TEXTDOMAIN ); ?></th>
        <td>
		<?php
		if ( $good->g_image != '' ) {
		?>
			<img src="<?php echo mm_get_image_url( $good, 'card' ); ?>" alt="<?php echo htmlspecialchars( $good->g_title ); ?>" />
			<a href="#" class="delete_image"><?php _e( 'Remove image', MM_TEXTDOMAIN ); ?></a>
			<br />
		<?php
		}
		?>
            <input type="file" name="g_image" value="<?php echo $good->g_image; ?>" size="70" />
            <?php echo $good->get_error( 'g_image', '<span class="error">', '</span>' ); ?>
        </td>
        </tr>

		<tr valign="top">
        <th scope="row"><?php _e( 'Add to Featured', MM_TEXTDOMAIN ); ?></th>
        <td>
			<?php $checked = ( $good->g_featured == 1 ) ? 'checked="checked"' : ''; ?>
            <input type="checkbox" name="mm_good[g_featured]" <?php echo $checked; ?> />
            <?php echo $good->get_error( 'g_featured', '<span class="error">', '</span>' ); ?>
        </td>
        </tr>

        <tr valign="top">
        <th scope="row"><?php _e( 'Item category', MM_TEXTDOMAIN ); ?></th>
        <td>
			<select name="mm_good[g_type_id]">
				<?php
				if ( FALSE !== $categories ) {
					mm_the_nested_select( $categories, NULL, 0, null, $category->id );
				}
				?>
			</select>
			<span><?php echo $category_name; ?></span>
        </td>
        </tr>
		
		<tr valign="top">
        <th scope="row"><?php _e( 'meta-description tag', MM_TEXTDOMAIN ); ?></th>
        <td>
            <input type="text" name="mm_good[g_meta_description]" value="<?php echo $good->g_meta_description; ?>" size="70" />
            <?php echo $good->get_error( 'g_meta_description', '<span class="error">', '</span>' ); ?>
        </td>
        </tr>

		<tr valign="top">
        <th scope="row"><?php _e( 'meta-keywords tag', MM_TEXTDOMAIN ); ?></th>
        <td>
            <input type="text" name="mm_good[g_meta_keywords]" value="<?php echo $good->g_meta_keywords; ?>" size="70" />
            <?php echo $good->get_error( 'g_meta_keywords', '<span class="error">', '</span>' ); ?>
        </td>
        </tr>
<?php
	foreach ( $fields as $name => $data ) {
?>
        <tr valign="top">
        <th scope="row"><?php echo $data[ 'title' ]; ?></th>
        <td>
		<?php mm_create_field( $name, $data, 'mm_goods_fields' ); ?>
        </td>
        </tr>
<?php
	}
?>
    </table>

    <p class="submit">
    <input type="submit" class="button-primary" value="<?php _e( 'Save Changes' ); ?>" />
    </p>
</form>

</div> <!-- div.wrap -->