<?php
if ( !class_exists( 'Fields_Model' ) ) {
	/**
     * Fields model class. Corresponds to table mm_fields.
	 * This table contains data for additional categories fields.
	 */
    class Fields_Model {
        public function  __construct() {
            parent::__construct();
			//table fields
            $this->fields = array(
                'g_name' => '',
                'g_value' => '',
                'g_type' => '',
                'g_goods_id' => '',
            );
			//table fields types
            $this->fields_types = array(
                '%s',
                '%s',
                '%s',
                '%d',
            );
        }

		/**
		 * Returns name of the corresponding table with WP database prefix.
		 *
		 * @global object $wpdb WP database class
		 * @return string table name with WP prefix
		 */
        public static function get_table_name() {
            global $wpdb;
            return $wpdb->prefix . 'mm_fields';
        }

        public static function get_field_values( $field_name, $category_ids ) {
            global $wpdb;

            $g_table = Goods_Model::get_table_name();
            $f_table = Fields_Model::get_table_name();
            $c_table = Goods_Types_Model::get_table_name();

            $res = $wpdb->get_results( $wpdb->prepare(
					'SELECT DISTINCT f_value'
					. ' FROM ' . $f_table
					. ' INNER JOIN ' . $g_table . ' ON ' . $g_table . '.g_id = ' . $f_table . '.f_goods_id'
					. ' INNER JOIN ' . $c_table . ' ON ' . $g_table . '.g_type_id = ' . $c_table . '.gt_id'
					. ' WHERE ' . $c_table . '.gt_id IN (' . implode( ',', $category_ids ) . ') AND f_name=%s',
					$field_name
				), ARRAY_A);

			if ( empty( $res ) ) {
				return FALSE;
			}

			$res_array = array();
			foreach ( $res as $row ) {
				$res_array[ $row[ 'f_value' ] ] = $row[ 'f_value' ];
			}

			return $res_array;
        }
    }
}