<?php
//PHP 5.3+ has builtin function
if(!function_exists('parse_ini_string')){
    /**
     * Parses string in ini-file format
     *
     * @param string $str ini-string
     * @param boolean $ProcessSections whether to get a multidimensional array, with the section names and settings included
     * @return array parsed data
     */
    function parse_ini_string($str, $ProcessSections=false){
        $lines  = explode("\n", $str);
        $return = Array();
        $inSect = false;
        foreach($lines as $line){
            $line = trim($line);
            if(!$line || $line[0] == "#" || $line[0] == ";")
                continue;
            if($line[0] == "[" && $endIdx = strpos($line, "]")){
                $inSect = substr($line, 1, $endIdx-1);
                continue;
            }
            if(!strpos($line, '=')) // (We don't use "=== false" because value 0 is not valid as well)
                continue;

            $tmp = explode("=", $line, 2);
            if($ProcessSections && $inSect)
                $return[$inSect][trim($tmp[0])] = ltrim($tmp[1]);
            else
                $return[trim($tmp[0])] = ltrim($tmp[1]);
        }
        return $return;
    }
}

/**
 * Generates html markup of additional category field (admin interface).
 *
 * @param string $name field name
 * @param array $data field data
 * @param string $group field group name
 * @return string html markup
 */
function mm_create_field( $name, $data, $group = 'mm_search_fields' ) {
	if ( !isset( $name ) || 0 == preg_match( '/^[a-z0-9-_]+$/i', $name ) ) {
		echo '<p class="error">' . __( 'Parse field error', MM_TEXTDOMAIN ) . '</p>';
		return;
	}
	if ( !isset( $data[ 'title' ] ) ) {
		echo '<p class="error">' . __( 'Field title is not set', MM_TEXTDOMAIN ) . '</p>';
		return;
	}
	if ( !isset( $data[ 'type' ] ) ) {
		echo '<p class="error">' . __( 'Field type is not set', MM_TEXTDOMAIN ) . '</p>';
		return;
	}

	switch ( $data[ 'type' ] ) {
		case 'text' :
			$value = isset( $data[ 'value' ] ) ? $data[ 'value' ] : '';
			echo '<input type="text" name="' . $group . '[' . $name . ']" value="' . htmlspecialchars( $value ) . '" size="70" />';
			break;
		case 'checkbox' :
			$checked = isset( $data[ 'value' ] ) ? 'checked="checked"' : '';
			echo '<input type="checkbox" name="' . $group . '[' . $name . ']" ' . $checked . ' />';
			break;
		case 'select' :
			if ( !isset( $data[ 'values' ] ) || '' == trim( $data[ 'values' ] ) ) {
				echo '<p class="error">' . __( 'Values for select field not set', MM_TEXTDOMAIN ) . '</p>';
			}
			$selected = isset( $data[ 'value' ] ) ? $data[ 'value' ] : '-1';
			echo '<select name="' . $group . '[' . $name . ']">';
				$def_selected = ( '-1' == $selected ) ? ' selected="selected"' : '';
				echo '<option value="none"' . $def_selected . '>---</option>';
			$values = explode( '|', $data[ 'values' ] );
			foreach ( $values as $key => $value ) {
				$filtered_value = htmlspecialchars( $value );
				$sel = '';
				if ( $filtered_value == $selected ) {
					$sel = ' selected="selected"';
				}
				echo '<option value="' . $filtered_value . '"' . $sel . '>' . $filtered_value . '</option>';
			}
			echo '</select>';
			break;
		case 'textarea' :
			$value = isset( $data[ 'value' ] ) ? $data[ 'value' ] : '';
			echo '<textarea name="' . $group . '[' . $name . ']" cols="70" rows="10">' . $value . '</textarea>';
//			echo '<input type="text" name="' . $group . '[' . $name . ']" value="' . htmlspecialchars( $value ) . '" size="70" />';
			break;
		default :
			break;
	}
}

/**
 * Generates html markup of additional category field (frontend interface - search form).
 *
 * @param string $name field name
 * @param array $data field data
 * @param string $group field group name
 * @return string html markup
 */
function mm_create_field_frontend( $name, $data, $group = 'mm_search_fields', $use_db_values = false, $db_values = null ) {
	if ( !isset( $name ) || 0 == preg_match( '/^[a-z0-9-_]+$/i', $name ) ) {
		echo '<p class="error">' . __( 'Parse field error', MM_TEXTDOMAIN ) . '</p>';
		return;
	}
	if ( !isset( $data[ 'title' ] ) ) {
		echo '<p class="error">' . __( 'Field title is not set', MM_TEXTDOMAIN ) . '</p>';
		return;
	}
	if ( !isset( $data[ 'type' ] ) ) {
		echo '<p class="error">' . __( 'Field type is not set', MM_TEXTDOMAIN ) . '</p>';
		return;
	}

	$f_type = ( isset( $data[ 'utype' ] ) && trim( $data[ 'utype' ] ) != '' ) ? $data[ 'utype' ] : $data[ 'type' ];

    switch ( $f_type ) {
		case 'text' :
			$value = isset( $data[ 'value' ] ) ? $data[ 'value' ] : '';
			echo '<input type="text" name="' . $group . '[' . $name . ']" value="' . htmlspecialchars( $value ) . '" size="30" />';
			break;
		case 'checkbox' :
			$checked = isset( $data[ 'value' ] ) ? 'checked="checked"' : '';
			echo '<input type="checkbox" name="' . $group . '[' . $name . ']" ' . $checked . ' />';
			break;
		case 'select' :
            if ( $use_db_values ) {
                $values = $db_values;
            }
            else {
                if ( !isset( $data[ 'values' ] ) || '' == trim( $data[ 'values' ] ) ) {
                    echo '<p class="error">' . __( 'Values for select field not set', MM_TEXTDOMAIN ) . '</p>';
                }
    			$values = explode( '|', $data[ 'values' ] );
            }
			$selected = isset( $data[ 'value' ] ) ? $data[ 'value' ] : '-1';
			echo '<select name="' . $group . '[' . $name . ']">';
				$def_selected = ( '-1' == $selected ) ? ' selected="selected"' : '';
				echo '<option value=""' . $def_selected . '>---</option>';
				if ( is_array( $values ) ) {
					foreach ( $values as $key => $value ) {
						$filtered_value = htmlspecialchars( $value );
						$sel = '';
						if ( $filtered_value == $selected ) {
							$sel = ' selected="selected"';
						}
						echo '<option value="' . $filtered_value . '"' . $sel . '>' . $filtered_value . '</option>';
					}
				}
			echo '</select>';
			break;
		case 'textarea' :
			$value = isset( $data[ 'value' ] ) ? $data[ 'value' ] : '';
//			echo '<textarea name="' . $group . '[' . $name . ']" cols="70" rows="10">' . $value . '</textarea>';
			echo '<input type="text" name="' . $group . '[' . $name . ']" value="' . htmlspecialchars( $value ) . '" size="30" />';
			break;
		default :
			break;
	}
}