<?php
if ( !class_exists( 'Goods_Model' ) ) {
	/**
     * Goods model class. Corresponds to table mm_goods.
	 * This table contains catalogue items data.
	 */
    class Goods_Model extends Base_Model {

        public function  __construct() {
            parent::__construct();
			//table fields
            $this->fields = array(
                'g_title' => '',
                'g_model_title' => '',
                'g_model' => '',
                'g_brand' => '',
                'g_brand_slug' => '',
                'g_description' => '',
                'g_image' => '',
                'g_image_url' => '',
                'g_image_thumb' => '',
                'g_image_thumb_url' => '',
                'g_add_date' => '',
                'g_type_id' => '',
                'g_featured' => 0,
                'g_meta_description' => '',
                'g_meta_keywords' => '',
            );
			//validation rules
            $this->rules = array(
                'g_title' => array( 'name' => 'required' ),
                'g_model_title' => array( 'name' => 'required' ),
                'g_brand' => array( 'name' => 'required' ),
                'g_type_id' => array( 'name' => 'numeric' ),
            );
			//fields types (used in DB queries)
            $this->fields_types = array(
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%d',
                '%d',
                '%s',
                '%s',
            );
        }

		/**
		 * Return table name with WP prefix
		 *
		 * @global object $wpdb WordPress database object
		 * @return string table name
		 */
        public static function get_table_name() {
            global $wpdb;
            return $wpdb->prefix . 'mm_goods';
        }

		/**
		 * This method returns a specified page of items list.
		 * Additionally this methos configures PaginateIt.
		 *
		 * @global object $wpdb WordPress database object
		 * @param int $page selected page
		 * @param int $items_on_page number of items on the page
		 * @param PaginateIt $paginator paginator object
		 * @return array of Goods_Model objects
		 */
        public static function get_all_goods( $page = 1, $items_on_page = 20, &$paginator = null ) {
            global $wpdb;

			$paginator->SetCurrentPage( $page );
			$paginator->SetItemsPerPage( $items_on_page );
			$paginator->SetQueryStringVar( 'p' );
			$paginator->SetQueryString( 'page=mixmarket_goods' );
			$paginator->SetLinksFormat( '&laquo;','&nbsp;','&raquo;' );

			//counting items in DB
			$count_goods = $wpdb->get_results( 'SELECT COUNT(*) AS goods_count FROM ' . self::get_table_name(), ARRAY_A );
			if ( ( int )$count_goods === 0 ) {
				return false;
			}
			$paginator->SetItemCount( ( int )$count_goods[ 0 ][ 'goods_count' ] );

			//getting selected items
			$goods = $wpdb->get_results( "SELECT * FROM " . self::get_table_name() . " ORDER BY g_add_date DESC " . $paginator->GetSqlLimit(), ARRAY_A );

			if ( empty( $goods ) ) {
                return FALSE;
            }
			//creating Goods_Model object
            $goods_arr = array();
            foreach ($goods as $row) {
                $new_good = new Goods_Model();

                $new_good->id = $row[ 'g_id' ];
				$new_good->set_fields( $row );

                $goods_arr[] = $new_good;
            }
            return $goods_arr;
        }

		/**
		 * This method searches for item by its id.
		 *
		 * @global object $wpdb WordPress database object
		 * @param int $id item id
		 * @return Goods_Model item if it was found or FALSE - if not
		 */
        public static function get_by_id( $id ) {
            global $wpdb;
            if ( !is_numeric( $id ) ) {
                return FALSE;
            }
            $g = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM " . self::get_table_name() . " WHERE g_id=%d", $id ), ARRAY_A );
            if ( NULL === $g ) {
                return FALSE;
            }
            $good = new Goods_Model();

			$good->id = $g[ 'g_id' ];
			$good->set_fields( $g );

            return $good;
        }

		/**
		 * Checks fields of current model.
		 * additionally this method saves thumbnail.
		 *
		 * @return boolean TRUE if validation succeded, FALSE - if not
		 */
		public function validate() {
			$res = true;
			$res = parent::validate();

			if ( FALSE === $res ) {
				return $res;
			}

            //saving file and creating thumbnail
            $valid_types  = array( "gif", "jpg", "png", "jpeg" );

            if ( isset( $_FILES[ "g_image" ] ) ) {
                if ( is_uploaded_file( $_FILES[ "g_image" ][ "tmp_name" ] ) ) {
                    $file_name_parts = explode( '.', $_FILES[ "g_image" ][ "name" ] );

                    if ( !in_array( $file_name_parts[ count( $file_name_parts ) - 1 ], $valid_types ) ) {
						$res = false;
                        $this->errors[ 'g_image' ] = __( 'Incorrect file type', MM_TEXTDOMAIN );
                    }
                    else {
						$card_size = get_option( 'mm_image_width' );
						if ( false === $card_size ) {
							$card_size = 200;
						}

						$thumb_size = get_option( 'mm_image_thumb_width' );
						if ( false === $thumb_size ) {
							$thumb_size = 50;
						}

                        $upload = wp_upload_bits( $_FILES[ "g_image" ][ "name" ]
                                , null, file_get_contents( $_FILES[ "g_image" ][ "tmp_name" ]));
                        if ( $upload[ 'error' ] ) {
                            //file upload error
							$res = false;
                            $this->errors[ 'g_image' ] = __( 'Image upload error', MM_TEXTDOMAIN );
                        }
                        
                        $img_dir = str_replace( '\\', '/', $upload[ 'file' ] );
                        $this->g_image = substr( $img_dir, strlen( WP_CONTENT_DIR ) );
                        $this->g_image_url = substr( $upload[ 'url' ], strlen( WP_CONTENT_URL ) );


                        $card = PhpThumbFactory::create( WP_CONTENT_DIR . $this->g_image );
                        $card->resize( $card_size, $card_size );
                        $pieces = explode( '.', WP_CONTENT_DIR . $this->g_image );
                        $pieces[ count( $pieces ) - 2 ] .= '-'.$card_size.'x'.$card_size;
                        $card_path = implode( '.', $pieces );
                        $card->save( $card_path );

                        $thumb = PhpThumbFactory::create( WP_CONTENT_DIR . $this->g_image );
                        $thumb->resize( $thumb_size, $thumb_size );
                        $pieces = explode( '.', WP_CONTENT_DIR . $this->g_image );
                        $pieces[ count( $pieces ) - 2 ] .= '-'.$thumb_size.'x'.$thumb_size;
                        $thumb_path = implode( '.', $pieces );
                        $thumb->save( $thumb_path );

//                        $thumb_dir = str_replace( '\\', '/', $thumb_path );
//                        $this->g_image_thumb = substr( $thumb_dir, strlen( WP_CONTENT_DIR ) );
//
//                        $pieces_url = explode( '.', $upload[ 'url' ] );
//                        $pieces_url[ count( $pieces_url ) - 2 ] .= '-200x200';
//
//                        $thumb_url = implode( '.', $pieces_url );
//                        $this->g_image_thumb_url = substr( $thumb_url, strlen( WP_CONTENT_URL ) );
                    }
                }
            }
			return $res;
		}

		/**
		 * Massive fields assignment.
		 * Sets values to g_add_date and g_featured fields.
		 *
		 * @param array $data fields data
		 */
		public function set_fields( $data ) {
            parent::set_fields( $data );

            $this->g_add_date = date( "Y-m-d H:i:s", time() );
			if ( $data[ 'g_featured' ] == 'on' || $data[ 'g_featured' ] == 1 ) {
				$this->g_featured = 1;
			}
			else {
				$this->g_featured = 0;
			}
        }

		/**
		 * This method saves current object in database
		 *
		 * @global object $wpdb WordPress database object
		 * @global <type> $EZSQL_ERROR database error variable
		 * @return boolean TRUE if save operation was successfull, FALSE - if not
		 */
        public function save() {
            global $wpdb;

			$this->purify_fields();

            $this->g_brand_slug = sanitize_title( $this->g_brand );
            $this->g_model = sanitize_title( $this->g_model_title );

            if ( $this->is_new() ) {
                $wpdb->query( $wpdb->prepare( 'INSERT INTO ' . $this->get_table_name() . ' ( '
                        . implode( ',', array_keys( $this->fields ) )
                        . ' ) VALUES ( ' . implode( ',', $this->fields_types ) . ' ) ', array_values( $this->fields ) ) );
                $this->id = $wpdb->insert_id;
            }
            else {
                //updating good
                $fields = array();
                $i = 0;
                foreach ( $this->fields as $key => $value ) {
                    $fields[] = $key . '=' . $this->fields_types[ $i ];
                    $i++;
                }
                $wpdb->query( $wpdb->prepare( 'UPDATE ' . $this->get_table_name() . ' SET '
                        . implode( ',', $fields ) . ' WHERE g_id=' . $this->id, array_values( $this->fields ) ) );
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
		 * This methos saves additional fields, that defined in category.
		 *
		 * @global object $wpdb WordPress database object
		 * @global HTMLPurifier $purifier html purifier object
		 * @param array $fields fields data
		 */
		public function save_custom_fields( $fields, $category ) {
			global $wpdb, $purifier;
			
			if ( !$this->is_new() ) {
				self::delete_custom_fields( $this->id );
			}

			$_fields = stripslashes_deep( $fields );
			$custom_fields = parse_ini_string( $category->get_fields(), true );

			foreach ( $custom_fields as $name => $field ) {
				if ( isset( $_fields[ $name ] ) ) {
					$value = $_fields[ $name ];
				}
				elseif ( $field[ 'type' ] == 'checkbox' ) {
					$value = 'off';
				}
				else {
					continue;
				}

				$wpdb->query( $wpdb->prepare( 'INSERT INTO ' . Fields_Model::get_table_name()
						. ' (f_name, f_value, f_goods_id) VALUES (%s, %s, %d)', array( $purifier->purify( $name ), $purifier->purify( $value ), $this->id ) ) );
			}
//			foreach ( $_fields as $name => $value ) {
//				$wpdb->query( $wpdb->prepare( 'INSERT INTO ' . Fields_Model::get_table_name()
//						. ' (f_name, f_value, f_goods_id) VALUES (%s, %s, %d)', array( $purifier->purify( $name ), $purifier->purify( $value ), $this->id ) ) );
//			}
		}

		/**
		 * This methos deletes the item additional fields from the database
		 *
		 * @global object $wpdb WordPress database object
		 * @param int $id the item id
		 * @return boolean TRUE if deletion was successfull, FALSE - if not
		 */
		public static function delete_custom_fields( $id ) {
			global $wpdb;
            if ( isset( $id ) && is_numeric( $id ) ) {
                //deleting custom fields for the good
                $wpdb->query( $wpdb->prepare( 'DELETE FROM ' . Fields_Model::get_table_name() . ' WHERE f_goods_id=%d', $id ) );
                return true;
            }
            return false;
		}

		/**
		 * This method deletes specified item from the database
		 *
		 * @global object $wpdb WordPress database object
		 * @param int $id the item id
		 * @return boolean TRUE if deletion was successfull, FALSE - if not
		 */
        public static function delete( $id ) {
            global $wpdb;
            if ( isset( $id ) && is_numeric( $id ) ) {
                //deleting good
                $wpdb->query( $wpdb->prepare( 'DELETE FROM ' . Goods_Model::get_table_name() . ' WHERE g_id=%d', $id ) );
                return true;
            }
            return false;
        }

		/**
		 * This method retrieves item name by its id
		 *
		 * @global object $wpdb WordPress database object
		 * @param int $id item id
		 * @return string item name or &nbsp; if item was not found
		 */
        public static function get_good_name_by_id( $id ) {
            global $wpdb;
            $cat = $wpdb->get_row( $wpdb->prepare( 'SELECT g_title FROM ' . self::get_table_name() . ' WHERE g_id=%d', $id ), ARRAY_A );
            if ( !is_array( $cat ) ) {
                return '&nbsp;';
            }
            else {
                return $cat[ 'g_name' ];
            }
        }

		/**
		 * This method returns a field value for specified item
		 *
		 * @global object $wpdb WordPress database object
		 * @param string $name name of the field
		 * @param int $good_id item id
		 * @return mixed field value
		 */
		public static function get_custom_field( $name, $good_id ) {
			global $wpdb;
			if ( isset( $name ) && '' != trim( $name ) && isset( $good_id ) && is_numeric( $good_id ) ) {
	            $field = $wpdb->get_row( $wpdb->prepare( 'SELECT * FROM '
						. Fields_Model::get_table_name() . ' WHERE f_name=%s AND f_goods_id=%d', $name, $good_id ), ARRAY_A );
				if ( !is_array( $field ) ) {
					return false;
				}
				else {
                    $field[ 'f_value' ] = stripslashes( $field[ 'f_value' ] );
					return $field;
				}
			}
			return false;
		}

		public static function get_all_custom_fields( $good_id ) {
			global $wpdb;
			if ( isset( $good_id ) && is_numeric( $good_id ) ) {
	            $fields = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM '
						. Fields_Model::get_table_name() . ' WHERE f_goods_id=%d', $good_id ), ARRAY_A );

				if ( empty( $fields ) ) {
					return false;
				}
				else {
					foreach ( $fields as $key => $field ) {
	                    $fields[ $key ][ 'f_value' ] = stripslashes( $field[ 'f_value' ] );
					}
					return $fields;
				}
			}
			return false;
		}

		/**
		 * This method returns a list of featured items.
		 * If there is more items than specified in $items_on_page parameter, random
		 * items are returened.
		 *
		 * @global object $wpdb WordPress database object
		 * @param int $page page number
		 * @param int $items_on_page number of items on the page
		 * @return array objects of Goods_Model type
		 */
        public static function get_featured_goods( $page = 1, $items_on_page = 20 ) {
            global $wpdb;

			$goods = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM " . self::get_table_name() . " WHERE g_featured=1 ORDER BY RAND() LIMIT %d", $items_on_page ), ARRAY_A );

			if ( empty( $goods ) ) {
                return FALSE;
            }
            $goods_arr = array();
            foreach ($goods as $row) {
                $new_good = new Goods_Model();

                $new_good->id = $row[ 'g_id' ];
				$new_good->set_fields( $row );

                $goods_arr[] = $new_good;
            }
            return $goods_arr;
        }

		/**
		 * This method returns selected page of items for the specified category.
		 * Additionally it sets parameters of the PaginateIt object
		 *
		 * @global object $wpdb WordPress database object
		 * @param int $category_id the category id
		 * @param int $page selected page
		 * @param int $items_on_page number of items on the page
		 * @param PaginateIt $paginator paginator object
		 * @return array objects of Goods_Model type
		 */
		public static function get_category_goods( $category_id, $page = 1, $items_on_page = 20, &$paginator = null, $with_subs = false, $rnd_order = false ) {
            global $wpdb;

			$paginator->SetCurrentPage( $page );
			$paginator->SetItemsPerPage( $items_on_page );
			$paginator->SetQueryStringVar( 'p' );
			$paginator->SetLinksFormat( '&laquo;','&nbsp;','&raquo;' );

			$order = '';
			if ( $rnd_order ) {
				$order = ' ORDER BY RAND() ';
			}
			else {
				$order = ' ORDER BY g_add_date DESC ';
			}
			if ( $with_subs ) {
				$ids[] = $category_id;
				$categories = Goods_Types_Model::get_all_categories();
				Goods_Types_Model::get_subcategories_ids($categories, $category_id, $ids);
				$count_goods = $wpdb->get_results( $wpdb->prepare( 'SELECT COUNT(*) AS goods_count FROM '
						. self::get_table_name() . " WHERE g_type_id IN (" . implode( ',', $ids ) . ") " ), ARRAY_A );
			}
			else {
				$count_goods = $wpdb->get_results( $wpdb->prepare( 'SELECT COUNT(*) AS goods_count FROM '
						. self::get_table_name() . " WHERE g_type_id=%d ", $category_id ), ARRAY_A );
			}
			if ( ( int )$count_goods === 0 ) {
				return false;
			}
			$paginator->SetItemCount( ( int )$count_goods[ 0 ][ 'goods_count' ] );

			if ( $with_subs ) {
				$goods = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM " . self::get_table_name()
						. " WHERE g_type_id IN (" . implode( ',', $ids ) . ") " . $order . $paginator->GetSqlLimit(), $category_id ), ARRAY_A );
			}
			else {
				$goods = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM " . self::get_table_name()
						. " WHERE g_type_id=%d " . $order . $paginator->GetSqlLimit(), $category_id ), ARRAY_A );
			}

			if ( empty( $goods ) ) {
                return FALSE;
            }
            $goods_arr = array();
            foreach ($goods as $row) {
                $new_good = new Goods_Model();

                $new_good->id = $row[ 'g_id' ];
				$new_good->set_fields( $row );

                $goods_arr[] = $new_good;
            }
            return $goods_arr;
		}

		/**
		 * This method searches for item by its brand and model
		 *
		 * @global object $wpdb WordPress database object
		 * @param string $brand_slug brand name
		 * @param string $model model name
		 * @return Goods_Model the item that was found, FALSE - if no item was found
		 */
        public static function get_by_brand_and_model( $brand_slug, $model ) {
            global $wpdb;
            $g = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM " . self::get_table_name() . " WHERE g_brand_slug=%s AND g_model=%s", $brand_slug, $model ), ARRAY_A );
            if ( NULL === $g ) {
                return FALSE;
            }
            $good = new Goods_Model();

			$good->id = $g[ 'g_id' ];
			$good->set_fields( $g );

            return $good;
        }

		/**
		 * This method returns the number of items in the category
		 *
		 * @global object $wpdb WordPress database object
		 * @param int $category_id category id
		 * @return int number of items in the specified category
		 */
        public static function count_goods_in_category( $category_id, $sub_ids = null ) {
            global $wpdb;

			if ( $sub_ids != null ) {
				$count_goods = $wpdb->get_results( $wpdb->prepare( 'SELECT COUNT(*) AS goods_count FROM '
						. self::get_table_name() . " WHERE g_type_id IN (" . implode( ',', $sub_ids ) . ") " ), ARRAY_A );
			}
			else {
				$count_goods = $wpdb->get_results( $wpdb->prepare( 'SELECT COUNT(*) AS goods_count FROM '
						. self::get_table_name() . " WHERE g_type_id=%d ", $category_id ), ARRAY_A );
			}

			if ( ( int )$count_goods === 0 ) {
				return false;
			}

			return ( int )$count_goods[ 0 ][ 'goods_count' ];
        }

		/**
		 * This method returns the number of items in the category
		 *
		 * @global object $wpdb WordPress database object
		 * @param int $category_id category id
		 * @return int number of items in the specified category
		 */
        public static function count_goods_in_category_brand( $category_id, $brand, $sub_ids = null ) {
            global $wpdb;

			if ( $sub_ids != null ) {
				$count_goods = $wpdb->get_results( $wpdb->prepare( 'SELECT COUNT(*) AS goods_count FROM '
						. self::get_table_name() . " WHERE g_type_id IN (" . implode( ',', $sub_ids )
						. ") AND g_brand_slug=%s", $brand ), ARRAY_A );
			}
			else {
				$count_goods = $wpdb->get_results( $wpdb->prepare( 'SELECT COUNT(*) AS goods_count FROM ' . self::get_table_name()
						. " WHERE g_type_id=%d AND g_brand_slug=%s", $category_id, $brand ), ARRAY_A );
			}
			if ( ( int )$count_goods === 0 ) {
				return false;
			}

			return ( int )$count_goods[ 0 ][ 'goods_count' ];
        }

		/**
		 * This method checks if the specified brand exists in database
		 *
		 * @global object $wpdb WordPress database object
		 * @param string $brand_slug slug of a brand
		 * @return boolean TRUE if brand exists, FALSE - if not
		 */
        public static function is_brand_exists( $brand_slug ) {
            global $wpdb;

			$goods = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM ' . self::get_table_name() . " WHERE g_brand=%s", $brand_slug ), ARRAY_A );
			if ( empty( $goods ) ) {
				return false;
			}

			return true;
        }

		public static function get_category_brands( $category, $ids = null ) {
			global $wpdb;

			if ( null != $ids ) {
				$brands = $wpdb->get_results( $wpdb->prepare( 'SELECT g_brand, g_brand_slug, COUNT(*) AS items_count FROM '
					. $wpdb->prefix . 'mm_goods WHERE g_type_id IN (' . implode( ',', $ids ) . ') GROUP BY g_brand' ), ARRAY_A );
			}
			else {
				$brands = $wpdb->get_results( $wpdb->prepare( 'SELECT g_brand, g_brand_slug, COUNT(*) AS items_count FROM '
					. $wpdb->prefix . 'mm_goods WHERE g_type_id=%d GROUP BY g_brand', $category->id ), ARRAY_A );
			}

			if ( empty( $brands ) ) {
				return FALSE;
			}
			return $brands;
		}

		/**
		 * This method returns number of items of specified brand
		 *
		 * @global object $wpdb WordPress database object
		 * @param string $brand_slug slug of a brand
		 * @return mixed number of items or FALSE if there is no items
		 */
        public static function count_goods_in_brand( $brand_slug ) {
            global $wpdb;

			$count_goods = $wpdb->get_results( $wpdb->prepare( 'SELECT COUNT(*) AS goods_count FROM ' . self::get_table_name() . " WHERE g_brand_slug=%s", $brand_slug ), ARRAY_A );
			if ( ( int )$count_goods === 0 ) {
				return false;
			}

			return ( int )$count_goods[ 0 ][ 'goods_count' ];
        }

		/**
		 * This method returns a selected page of items for specified category
		 *
		 * @global object $wpdb WordPress database object
		 * @global object $wp_rewrite WordPress rewrite object
		 * @param Goods_Types_Model $category the category object
		 * @param int $page selected page
		 * @param int $items_on_page number of items on a page
		 * @param string $permalink link to the catalogue page
		 * @param PaginateIt $pagination pagination object
		 * @return mixed array of Goods_Model objects if any was found or FALSE - if not
		 */
		public static function get_goods_by_category( $category, $page = 1, $items_on_page = 20, $permalink = '', &$pagination = null, $with_subs = false ) {
            global $wpdb, $wp_rewrite;

			$ids = array();
			if ( $with_subs ) {
				$ids[] = $category->id;
				$categories = Goods_Types_Model::get_all_categories();
				Goods_Types_Model::get_subcategories_ids($categories, $category->id, $ids);
	            $num_of_goods = Goods_Model::count_goods_in_category( $category->id, $ids );
			}
			else {
	            $num_of_goods = Goods_Model::count_goods_in_category( $category->id );
			}


            $pages_num = ceil( ( int )$num_of_goods / ( int )$items_on_page );

			if ( $page > $pages_num || !is_int( $page ) || $page < 1 ) {
				//this page is not exists
				return FALSE;
			}

			$first_good = ( $page - 1 ) * $items_on_page;

			//checking if permalinks are turn on
            if ( $wp_rewrite->using_permalinks() ) {
				$slash = ( substr( $permalink, -1 ) != '/' ) ? '/' : '';
                $base = $permalink . $slash . 'category/' . $category->gt_slug . '/page/%_%';
				$format = '%#%';
            }
            else {
                $base = $permalink . '&mm_category=' . $category->gt_slug . '&mm_page=%_%';
				$format = '%#%';
            }

			//pagination parameters
            $args = array(
                'base' => $base, // http://example.com/all_posts.php%_% : %_% is replaced by format (below)
                'format' => $format, // ?page=%#% : %#% is replaced by the page number
                'total' => $pages_num,
                'current' => $page,
                'show_all' => false,
                'prev_next' => true,
                'prev_text' => __('&laquo; Previous'),
                'next_text' => __('Next &raquo;'),
                'end_size' => 1,
                'mid_size' => 2,
                'type' => 'array',
                'add_args' => false, // array of query args to add
                'add_fragment' => ''
            );

            $pagination = paginate_links( $args );

			if ( $with_subs ) {
				$goods = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM " . self::get_table_name()
						. " WHERE g_type_id IN (" . implode( ',', $ids ) . ") LIMIT "
						. $first_good . ',' . $items_on_page ), ARRAY_A );
			}
			else {
				$goods = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM " . self::get_table_name()
						. " WHERE g_type_id=%d LIMIT " . $first_good . ',' . $items_on_page, $category->id ), ARRAY_A );
			}

			if ( empty( $goods ) ) {
                return FALSE;
            }
            $goods_arr = array();
            foreach ($goods as $row) {
                $new_good = new Goods_Model();

                $new_good->id = $row[ 'g_id' ];
				$new_good->set_fields( $row );

                $goods_arr[] = $new_good;
            }
            return $goods_arr;
        }

		/**
		 * This method returns a selected page of items for specified brand of the category
		 *
		 * @global object $wpdb WordPress database object
		 * @global object $wp_rewrite WordPress rewrite object
		 * @param string $brand selected brand
		 * @param Goods_Types_Model $category the category object
		 * @param int $page selected page
		 * @param int $items_on_page number of items on a page
		 * @param string $permalink link to the catalogue page
		 * @param PaginateIt $pagination pagination object
		 * @return mixed array of Goods_Model objects if any was found or FALSE - if not
		 */
		public static function get_goods_by_category_brand( $brand_slug, $category, $page = 1, $items_on_page = 20, $permalink = '', &$pagination = null, $with_subs = false ) {
            global $wpdb, $wp_rewrite;

			$ids = array();
			if ( $with_subs ) {
				$ids[] = $category->id;
				$categories = Goods_Types_Model::get_all_categories();
				Goods_Types_Model::get_subcategories_ids($categories, $category->id, $ids);
	            $num_of_goods = Goods_Model::count_goods_in_category_brand( $category->id, $brand_slug, $ids );
			}
			else {
	            $num_of_goods = Goods_Model::count_goods_in_category_brand( $category->id, $brand_slug );
			}

            $pages_num = ceil( ( int )$num_of_goods / ( int )$items_on_page );

			if ( $page > $pages_num || !is_int( $page ) || $page < 1 ) {
				//this page is not exists
				return FALSE;
			}

			$first_good = ( $page - 1 ) * $items_on_page;

			//checking if permalinks are turn on
            if ( $wp_rewrite->using_permalinks() ) {
				$slash = ( substr( $permalink, -1 ) != '/' ) ? '/' : '';
                $base = $permalink . $slash . 'category/' . $category->gt_slug . '/' . $brand_slug . '/page/%_%';
				$format = '%#%';
            }
            else {
                $base = $permalink . '&mm_category=' . $category->gt_slug . '&mm_brand=' . $brand_slug . '&mm_page=%_%';
				$format = '%#%';
            }

			//pagination parameters
            $args = array(
                'base' => $base, // http://example.com/all_posts.php%_% : %_% is replaced by format (below)
                'format' => $format, // ?page=%#% : %#% is replaced by the page number
                'total' => $pages_num,
                'current' => $page,
                'show_all' => false,
                'prev_next' => true,
                'prev_text' => __('&laquo; Previous'),
                'next_text' => __('Next &raquo;'),
                'end_size' => 1,
                'mid_size' => 2,
                'type' => 'array',
                'add_args' => false, // array of query args to add
                'add_fragment' => ''
            );

            $pagination = paginate_links( $args );

			if ( $with_subs ) {
				$ids = array();
				$ids[] = $category->id;
				$categories = Goods_Types_Model::get_all_categories();
				Goods_Types_Model::get_subcategories_ids($categories, $category->id, $ids);
				$goods = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM " . self::get_table_name()
						. " WHERE g_type_id IN (" . implode( ',', $ids ) . ") AND g_brand_slug=%s LIMIT "
						. $first_good . ',' . $items_on_page, $brand_slug ), ARRAY_A );
			}
			else {
				$goods = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM " . self::get_table_name()
						. " WHERE g_type_id=%d AND g_brand_slug=%s LIMIT " . $first_good . ',' . $items_on_page, $category->id, $brand_slug ), ARRAY_A );
			}

			if ( empty( $goods ) ) {
                return FALSE;
            }
            $goods_arr = array();
            foreach ($goods as $row) {
                $new_good = new Goods_Model();

                $new_good->id = $row[ 'g_id' ];
				$new_good->set_fields( $row );

                $goods_arr[] = $new_good;
            }
            return $goods_arr;
        }

		/**
		 * This method returns a selected page of items for specified brand
		 *
		 * @global object $wpdb WordPress database object
		 * @global object $wp_rewrite WordPress rewrite object
		 * @param string $brand_slug name of brand
		 * @param int $page selected page
		 * @param int $items_on_page number of items on a page
		 * @param string $permalink link to the catalogue page
		 * @param PaginateIt $pagination pagination object
		 * @return mixed array of Goods_Model objects if any was found or FALSE - if not
		 */
        public static function get_goods_by_brand( $brand_slug, $page = 1, $items_on_page = 20, $permalink = '', &$pagination = null ) {
            global $wpdb, $wp_rewrite;

            $num_of_goods = Goods_Model::count_goods_in_brand( $brand_slug );

            $pages_num = ceil( ( int )$num_of_goods / ( int )$items_on_page );

			if ( $page > $pages_num || !is_int( $page ) || $page < 1 ) {
				//this page is not exists
				return FALSE;
			}

			$first_good = ( $page - 1 ) * $items_on_page;

			//checking if permalinks are turn on
            if ( $wp_rewrite->using_permalinks() ) {
				$slash = ( substr( $permalink, -1 ) != '/' ) ? '/' : '';
                $base = $permalink . $slash . $brand_slug . '/page/%_%';
				$format = '%#%';
            }
            else {
                $base = $permalink . '&mm_brand=' . $brand_slug . '&mm_page=%_%';
				$format = '%#%';
            }

			//pagination parameters
            $args = array(
                'base' => $base, // http://example.com/all_posts.php%_% : %_% is replaced by format (below)
                'format' => $format, // ?page=%#% : %#% is replaced by the page number
                'total' => $pages_num,
                'current' => $page,
                'show_all' => false,
                'prev_next' => true,
                'prev_text' => __('&laquo; Previous'),
                'next_text' => __('Next &raquo;'),
                'end_size' => 1,
                'mid_size' => 2,
                'type' => 'array',
                'add_args' => false, // array of query args to add
                'add_fragment' => ''
            );

            $pagination = paginate_links( $args );

			$goods = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM " . self::get_table_name() . " WHERE g_brand_slug=%s LIMIT " . $first_good . ',' . $items_on_page, $brand_slug ), ARRAY_A );

			if ( empty( $goods ) ) {
                return FALSE;
            }
            $goods_arr = array();
            foreach ($goods as $row) {
                $new_good = new Goods_Model();

                $new_good->id = $row[ 'g_id' ];
				$new_good->set_fields( $row );

                $goods_arr[] = $new_good;
            }
            return $goods_arr;
        }

		/**
		 * This method returns names of all existing brands
		 *
		 * @global object $wpdb WordPress database object
		 * @return array brands that was found
		 */
		public static function get_all_brands() {
			global $wpdb;

			$brands = $wpdb->get_col( 'SELECT DISTINCT g_brand FROM ' . Goods_Model::get_table_name() );

			return $brands;
		}

        public static function get_brand_model_title( $brand, $model ) {
            global $wpdb;

            $title = $wpdb->get_col( $wpdb->prepare( 'SELECT g_title FROM ' . Goods_Model::get_table_name() . ' WHERE g_brand_slug=%s AND g_model=%s', $brand, $model ) );

            if ( NULL === $title ) {
                return '404 ' . __( 'Not found', MM_TEXTDOMAIN );
            }
            else {
                return $title[0];
            }
        }

		public static function get_brand_by_slug( $brand_slug ) {
			global $wpdb;

			if ( !isset( $brand_slug ) || '' == trim( $brand_slug ) ) {
				return false;
			}

			$brand = $wpdb->get_col( $wpdb->prepare( 'SELECT g_brand FROM ' . Goods_Model::get_table_name()
					. ' WHERE g_brand_slug=%s', $brand_slug ) );
			if ( NULL === $brand ) {
				return false;
			}
			else {
				return $brand[0];
			}
		}

		public static function get_subcategories_featured_goods( $categories, $category, $page = 1, $items_on_page = 20 ) {
            global $wpdb;

			$cats_ids = array();
			Goods_Types_Model::get_subcategories_ids( $categories, $category->id, $cats_ids );

			$goods = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM " . self::get_table_name()
					. " WHERE g_featured=1 AND g_type_id IN (" . implode( ',', $cats_ids )
					. ") ORDER BY RAND() LIMIT %d", $items_on_page ), ARRAY_A );

			if ( empty( $goods ) ) {
                return FALSE;
            }
            $goods_arr = array();
            foreach ($goods as $row) {
                $new_good = new Goods_Model();

                $new_good->id = $row[ 'g_id' ];
				$new_good->set_fields( $row );

                $goods_arr[] = $new_good;
            }
            return $goods_arr;
		}

		/**
		 * This method searches item in specified category
		 *
		 * @global object $wpdb WordPress database object
		 * @global  $EZSQL_ERROR WordPress database error
		 * @param array $conditions conditions for main category fields
		 * @param array $fields_conditions conditions for additional category fields
		 * @param Goods_Types_Model $category current category object
		 * @return Goods_Model
		 */
		public static function search( $conditions, $fields_conditions, $category, $with_subs = false ) {
			global $wpdb;

			//searching goods by general fields
			if ( $with_subs ) {
				$ids[] = $category->id;
				$categories = Goods_Types_Model::get_all_categories();
				Goods_Types_Model::get_subcategories_ids($categories, $category->id, $ids);
				$goods = $wpdb->get_results( 'SELECT * FROM ' . Goods_Model::get_table_name()
						. Goods_Model::generate_where( $conditions, $ids ), ARRAY_A );
			}
			else {
				$goods = $wpdb->get_results( 'SELECT * FROM ' . Goods_Model::get_table_name()
						. Goods_Model::generate_where( $conditions ), ARRAY_A );
			}

			if ( empty( $goods ) ) {
                return FALSE;
            }
            $goods_arr = array();
            foreach ($goods as $row) {
                $new_good = new Goods_Model();

                $new_good->id = $row[ 'g_id' ];
				$new_good->set_fields( $row );

                $goods_arr[] = $new_good;
            }

			//searching goods by additional category fields
			if ( $category !== null && !empty( $fields_conditions ) ) {
				foreach ( $fields_conditions as $field_name => $field_value ) {
					$fields = $wpdb->get_col( 'SELECT DISTINCT f_goods_id FROM ' . Fields_Model::get_table_name()
							. Goods_Model::generate_fields_where( $field_name, $field_value, $category ) );
					if ( !$fields ) {
						return FALSE;
					}
					//removing goods from $goods_arr that have not corresponding element in $fields array
					foreach ( $goods_arr as $key => $good ) {
						if ( !in_array( $good->id, $fields ) ) {
							unset( $goods_arr[ $key ] );
						}
					}
				}
			}

			//checking for errors
            global $EZSQL_ERROR;
            if ( $EZSQL_ERROR ) {
                return false;
            }

            return $goods_arr;
		}

		/**
		 * This method WHERE part of a query for search in main category fields
		 *
		 * @global object $wpdb WordPress database object
		 * @param array $conditions search parameters
		 * @return string WHERE part of a query
		 */
		private static function generate_where( $conditions, $ids = null ) {
			global $wpdb;
			if ( empty( $conditions ) ) {
				return '';
			}
			$where = ' WHERE ';
			$and = '';
			foreach ( $conditions as $field => $condition ) {
				if ( $field == 'category_id' ) {
					if ( $ids != null ) {
						$where .= $and . 'g_type_id IN (' . implode( ',', $ids ) . ') ';
					}
					else {
						$where .= $and . 'g_type_id=' . $wpdb->escape( $condition ) . ' ';
					}
				}
				else {
					$where .= $and . $field . ' LIKE "' . $wpdb->escape( '%' . $condition . '%' ) . '" ';
				}
				$and = ' AND ';
			}
			return $where;
		}

		/**
		 * This method WHERE part of a query for search in additional category fields
		 *
		 * @global object $wpdb WordPress database object
		 * @param string $field_name name of the field
		 * @param string $field_value value of the field
		 * @param Goods_Types_Model $category current category object
		 * @return string WHERE part of a query
		 */
		private static function generate_fields_where( $field_name, $field_value, $category ) {
			global $wpdb;
			$where = ' WHERE ';
			$category_fields = parse_ini_string( $category->get_fields(), true );
			switch ( $category_fields[ $field_name ][ 'type' ] ) {
				//we are using equal for text and textarea and LIKE for checkbox and select
				case 'text' :
					$where .= ' (f_name="' . $wpdb->escape( $field_name )
						. '" AND f_value LIKE "' . $wpdb->escape( '%' . $field_value . '%' ) . '") ';
					break;
				case 'checkbox' :
					$where .= ' (f_name="' . $wpdb->escape( $field_name )
						. '" AND f_value="' . $wpdb->escape( $field_value ) . '") ';
					break;
				case 'select' :
					$where .= ' (f_name="' . $wpdb->escape( $field_name )
						. '" AND f_value="' . $wpdb->escape( $field_value ) . '") ';
					break;
				case 'textarea' :
					$where .= ' (f_name="' . $wpdb->escape( $field_name )
						. '" AND f_value LIKE "' . $wpdb->escape( '%' . $field_value . '%' ) . '") ';
					break;
			}
			return $where;
		}
    }
}