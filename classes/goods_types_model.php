<?php
if ( !class_exists( 'Goods_Types_Model' ) ) {
	/**
     * Category model class. Corresponds to table mm_goods_types.
	 * This table contains data for catalogue categories.
	 */
    class Goods_Types_Model extends Base_Model {

        public function  __construct() {
            parent::__construct();
			//category fields
            $this->fields = array(
                'gt_name' => '',
                'gt_slug' => '',
                'gt_fields' => '',
                'gt_parent' => 'NULL',
                'gt_description' => '',
                'gt_meta_description' => '',
                'gt_title' => '',
				'gt_meta_keywords' => '',
            );
			//validation rules
            $this->rules = array(
                'gt_name' => array( 'name' => 'required' ),
                'gt_parent' => array( 'name' => 'numeric' ),
            );
			//fields types
            $this->fields_types = array(
                '%s',
                '%s',
                '%s',
                '%d',
                '%s',
                '%s',
                '%s',
                '%s',
            );
        }

		/**
		 * This method returns table name with WordPress prefix
		 *
		 * @global object $wpdb WordPress database object
		 * @return string table name
		 */
        public static function get_table_name() {
            global $wpdb;
            return $wpdb->prefix . 'mm_goods_types';
        }

		/**
		 * This method returns all existing categories
		 *
		 * @global object $wpdb WordPress database object
		 * @return array of Goods_Types_Model objects
		 */
        public static function get_all_categories() {
            global $wpdb;
            $cats = $wpdb->get_results( "SELECT * FROM " . self::get_table_name(), ARRAY_A );
            if ( empty( $cats ) ) {
                return FALSE;
            }
            $categories = array();
            foreach ($cats as $row) {
                $new_category = new Goods_Types_Model();

                $new_category->id = $row[ 'gt_id' ];
                $new_category->gt_name = $row[ 'gt_name' ];
                $new_category->gt_slug = $row[ 'gt_slug' ];
                $new_category->gt_fields = $row[ 'gt_fields' ];
                $new_category->gt_parent = $row[ 'gt_parent' ];
				$category->gt_description = $cat[ 'gt_description' ];
				$category->gt_meta_description = $cat[ 'gt_meta_description' ];
				$category->gt_title = $cat[ 'gt_title' ];
				$category->gt_meta_keywords = $cat[ 'gt_meta_keywords' ];

                $categories[] = $new_category;
            }
            return $categories;
        }

		/**
		 * This method searches for category by its id
		 *
		 * @global object $wpdb WordPress database object
		 * @param int $id category id
		 * @return mixed Goods_Types_Model object if the category was found, FALSE - if not
		 */
        public static function get_by_id( $id ) {
            global $wpdb;
            if ( !is_numeric( $id ) ) {
                return FALSE;
            }
            $cat = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM " . self::get_table_name() . " WHERE gt_id=%d", $id ), ARRAY_A );
            if ( NULL === $cat ) {
                return FALSE;
            }
            $category = new Goods_Types_Model();

            $category->id = $cat[ 'gt_id' ];
            $category->gt_name = $cat[ 'gt_name' ];
            $category->gt_slug = $cat[ 'gt_slug' ];
            $category->gt_fields = $cat[ 'gt_fields' ];
            $category->gt_parent = $cat[ 'gt_parent' ];
            $category->gt_description = $cat[ 'gt_description' ];
            $category->gt_meta_description = $cat[ 'gt_meta_description' ];
            $category->gt_title = $cat[ 'gt_title' ];
			$category->gt_meta_keywords = $cat[ 'gt_meta_keywords' ];

            return $category;
        }

		/**
		 * Saves the current category.
		 * All fields values will be purified before saving.
		 *
		 * @global object $wpdb WordPress database object
		 * @global $EZSQL_ERROR WordPress database error
		 * @return boolean TRUE if category was successfully saved, FALSE - if not
		 */
        public function save() {
            global $wpdb;

            if ( $this->gt_slug == null || trim( $this->gt_slug ) == '' ) {
                $this->gt_slug = sanitize_title( $this->gt_name );
            }
            else {
                $this->gt_slug = sanitize_title( $this->gt_slug );
            }

            if ( $this->gt_parent == '0' ) {
                $this->gt_parent = 'NULL';
            }

            $i = 0;
            foreach ( $this->fields as $key => $field ) {
                if ( $this->fields_types[ $i ] == '%s' ) {
                    $this->fields[ $key ] = '"'.$wpdb->escape( $field ).'"';
                }
                $i++;
            }

			$this->purify_fields();

			if ( $this->is_new() ) {
				//checking if slug is unique
				$slugs = $wpdb->get_results( 'SELECT gt_slug FROM '.$this->get_table_name(), ARRAY_A );
                foreach ( $slugs as $slug ) {
                    if ( $this->gt_slug == $slug[ 'gt_slug' ] ) {
                        $this->gt_slug = $this->generate_slug( $this->gt_slug, $slugs, 1 );
                        break;
                    }
                }
                //saving category
                $wpdb->query( 'INSERT INTO ' . $this->get_table_name() . ' ( '
                        . implode( ',', array_keys( $this->fields ) )
                        . ' ) VALUES ( ' . implode( ',', $this->fields ) . ' ) ' );
                $this->id = $wpdb->insert_id;
            }
            else {
                //updating category
                $fields = array();
                foreach ( $this->fields as $key => $value ) {
                    $fields[] = $key . '=' . $value;
                }
                $wpdb->query( 'UPDATE ' . $this->get_table_name() . ' SET '
                        . implode( ',', $fields ) . ' WHERE gt_id=' . $this->id );
            }

            global $EZSQL_ERROR;
            if ( $EZSQL_ERROR ) {
                return false;
            }
            else {
                return true;
            }
        }

		/**
		 * Deletes the specified category.
		 * All child categories will be deleted too.
		 *
		 * @global object $wpdb WordPress database object
		 * @param int $id category id
		 * @return boolean TRUE if the category was successfully deleted, FALSE - if not
		 */
        public static function delete( $id ) {
            global $wpdb;
            if ( isset( $id ) && is_numeric( $id ) ) {
                //deleting category, all subcategories will be deleted automatically
                $wpdb->query( $wpdb->prepare( 'DELETE FROM ' . Goods_Types_Model::get_table_name() . ' WHERE gt_id=%d', $id ) );
                return true;
            }
            return false;
        }

		/**
		 * Searches for specified category and returns its name
		 *
		 * @global object $wpdb WordPress database object
		 * @param int $id
		 * @return string name of the category or &nbsp; if the category was not found
		 */
        public static function get_category_name_by_id( $id ) {
            global $wpdb;
            $cat = $wpdb->get_row( $wpdb->prepare( 'SELECT gt_name FROM ' . self::get_table_name() . ' WHERE gt_id=%d', $id ), ARRAY_A );
            if ( !is_array( $cat ) ) {
                return '&nbsp;';
            }
            else {
                return $cat[ 'gt_name' ];
            }
        }

		/**
		 * Searches for category by its slug
		 *
		 * @global object $wpdb WordPress database object
		 * @param string $slug category slug
		 * @return mixed object of Goods_Types_Model type or FALSE if category was not found
		 */
        public static function get_category_by_slug( $slug ) {
            global $wpdb;
            $cat = $wpdb->get_row( $wpdb->prepare( 'SELECT * FROM ' . self::get_table_name() . ' WHERE gt_slug=%s', $slug ), ARRAY_A );
            if ( !is_array( $cat ) ) {
                return FALSE;
            }

            $category = new Goods_Types_Model();

            $category->id = $cat[ 'gt_id' ];
            $category->gt_name = $cat[ 'gt_name' ];
            $category->gt_slug = $cat[ 'gt_slug' ];
            $category->gt_fields = $cat[ 'gt_fields' ];
            $category->gt_parent = $cat[ 'gt_parent' ];
            $category->gt_description = $cat[ 'gt_description' ];
            $category->gt_meta_description = $cat[ 'gt_meta_description' ];
            $category->gt_title = $cat[ 'gt_title' ];
			$category->gt_meta_keywords = $cat[ 'gt_meta_keywords' ];

			return $category;
        }

        public function get_parents( $category ) {
            global $wpdb;

            $current = $category;
			$parents = array();

            while ( $current->gt_parent != NULL ) {
				$parent = Goods_Types_Model::get_by_id( $current->gt_parent );
				$parents[] = $parent;
				$current = $parent;
            }

			return array_reverse( $parents );
        }

		public function get_subcategories_ids( &$categories, $parent_id, &$res ) {
			foreach ( $categories as $category ) {
				if ( $category->gt_parent == $parent_id ) {
					$res[] = $category->id;
					Goods_Types_Model::get_subcategories_ids( $categories, $category->id, $res );
				}
			}
		}

		public function get_fields() {
			if ( $this->gt_fields == '' ) {
				//getting fields from parent category
				if ( $this->gt_parent != NULL) {
					$parent_category = Goods_Types_Model::get_by_id( $this->gt_parent );
					return $parent_category->gt_fields;
				}
			}
			else {
				return $this->gt_fields;
			}
		}

		/**
		 * This method generates slug for category.
		 * If slug is already exists, this method adds a number to it.
		 *
		 * @param string $slug base category slug
		 * @param array $slugs already existing slugs
		 * @param int $i counter
		 * @return string new category slug
		 */
		private function generate_slug( $slug, $slugs, $i ) {
            $new_slug = $slug . '-' . $i;
            foreach ( $slugs as $s ) {
                if ( $new_slug == $s[ 'gt_slug' ] ) {
                    $i++;
                    return $this->generate_slug( $slug, $slugs, $i );
                }
            }
            return $new_slug;
        }
    }
}