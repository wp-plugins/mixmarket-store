<form method="post" action="<?php echo mm_get_search_link( $permalink ); ?>" id="mm_search">
    <table>
		<tr valign="top">
        <th scope="row"><?php _e( 'Item brand', MM_TEXTDOMAIN ); ?></th>
        <td>
			<?php
			if ( isset( $brand ) ) {
				$sel_brand = $brand;
			}
			else {
				$sel_brand = $conditions[ 'g_brand' ];
			}
			?>
            <select name="mm_search[g_brand]">
			<?php
			echo '<option value="">---</option>';
			foreach ( $category_brands as $cur_brand ) {
				$selected = ( $sel_brand == $cur_brand[ 'g_brand' ] ) ? ' selected="selected"' : '';
				echo '<option value="' . $cur_brand[ 'g_brand' ] . '"' . $selected . '>'
					. $cur_brand[ 'g_brand' ] . '</option>';
			}
			?>
			</select>
        </td>
        </tr>

        <tr valign="top">
        <th scope="row"><?php _e( 'Item description', MM_TEXTDOMAIN ); ?></th>
        <td>
            <input type="text" name="mm_search[g_description]" value="<?php echo $conditions[ 'g_description' ]; ?>" size="30" />
        </td>
        </tr>

		<input type="hidden" name="mm_search[category_id]" value="<?php echo $category->id; ?>" />

<?php
	foreach ( $fields as $name => $data ) {
		if ( isset( $fields_conditions[ $name ] ) ) {
			$data[ 'value' ] = $fields_conditions[ $name ];
		}

        $f_type = $data[ 'type' ];
        $use_db_values = false;
        $db_values = null;
        if ( isset( $data[ 'utype' ] ) && $data[ 'type' ] != $data[ 'utype' ] ) {
            $f_type = $data[ 'utype' ];
            $use_db_values = true;
            $db_values = Fields_Model::get_field_values( $name, $category_ids );
        }
?>
        <tr valign="top">
        <th scope="row"><?php echo $data[ 'title' ]; ?></th>
        <td>
		<?php mm_create_field_frontend( $name, $data, 'mm_search_fields', $use_db_values, $db_values ); ?>
        </td>
        </tr>
<?php
	}
?>
    </table>

    <p>
    <input type="submit" value="<?php _e( 'Search', MM_TEXTDOMAIN ); ?>" />
    </p>
</form>
