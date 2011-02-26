<?php
if ( !class_exists( 'Mix_Market_Goods' ) ) {
	/**
	 * This controller creates pages for managing items.
	 */

    class Mix_Market_Goods extends Base_Controller {
		//pagination
        public $goods_per_page = 20;
		//page name
        public $goods_page = 'mixmarket_goods';

		/**
		 * Adding two actions
		 * admin_menu - creates admin pages
		 * init - handle request
		 */
        public function __construct() {
            add_action( 'admin_menu', array( $this, 'create_pages' ) );
            add_action( 'init', array( $this, 'handle_request' ) );
	//		add_action( 'admin_print_styles' . $goods_page, array( $this, 'load_styles' ) );
        }

		/**
		 * This mrthod creates Items submenu and adding nessesary JS scripts
		 */
        public function create_pages() {
            $goods_page = add_submenu_page( 'mixmarket_options'
                , __( 'Items', MM_TEXTDOMAIN )
                , __( 'Items', MM_TEXTDOMAIN )
                , 'manage_options'
                , $this->goods_page
                , array( $this, 'render_goods_page' )
            );
			add_action( 'admin_print_scripts-' . $goods_page, array( $this, 'load_scripts' ) );
		}

		/**
		 * This method loads TinyMce editor, jquery autocomplete plugin and confirm delete scripts
		 */
        public function load_scripts() {
            wp_enqueue_script( 'mm_confirm_delete', MM_URL . '/js/confirm_delete.js'
                    , array( 'jquery' )
                    , '1.0', true );
            wp_enqueue_script( 'mm_delete_image', MM_URL . '/js/delete_image.js'
                    , array( 'jquery' )
                    , '1.0', true );
            wp_enqueue_script( 'mm_tinymce', MM_URL . '/js/tinymce/tiny_mce.js', array(), '1.0', true );
            //wp_enqueue_script( 'mm_tinymce_gz_setup', MM_URL . '/js/tiny_mce_gz_setup.js', array( 'mm_tinymce' ), '1.0', true );
            wp_enqueue_script( 'mm_tinymce_setup', MM_URL . '/js/tiny_mce_setup.js', array( 'mm_tinymce' ), '1.0', true );
			wp_enqueue_script( 'mm_autocomplete', MM_URL . '/js/jquery.autocomplete.min.js', array( 'jquery' ), '1.0', true );
			wp_enqueue_script( 'mm_autocomplete_setup', MM_URL . '/js/autocomplete_setup.js', array( 'mm_autocomplete' ), '1.0', true );
			wp_localize_script( 'mm_confirm_delete', 'mm_confirm_settings', array(
                'delete_message' => __( 'Are you sure?' ),
				'editor_language' => substr( get_locale(), 0, 2 )
			) );
			$brands = Goods_Model::get_all_brands();
			wp_localize_script( 'mm_autocomplete_setup', 'mm_brands', array(
                'brands' => implode( '|', $brands ),
			) );
			wp_localize_script( 'mm_delete_image', 'mm_delete_image', array(
                'delete_message' => __( 'Are you sure?' ),
                'script_url' => MM_URL . '/ajax/delete_image.php',
			) );
        }

		/**
		 * This methos adds additional CSS styles
		 */
		public function load_styles() {
			wp_enqueue_style( 'mm_autocomplete_styles', MM_URL . '/css/jquery.autocomplete.css' );
		}

		/**
		 * This method renders item page depending on value on $_GET[ 'action' ] parameter.
		 */
        public function render_goods_page() {
            if ( isset( $_GET[ 'action' ] ) ) {
                switch ( $_GET[ 'action' ] ) {
                    case 'create' :
                        $this->show_create_good_page();
                        break;
                    case 'edit' :
                        $this->show_edit_good_page();
                        break;
                    default :
                        $this->show_goods_list();
                        break;
                }
            }
            else {
                $this->show_goods_list();
            }
        }

		/**
		 * This method creates New Item form
		 *
		 * @global Goods_Model $good item data, may be setted during init action
		 * @global Goods_Types_Model $category item category, must be setted during init action
		 */
        public function show_create_good_page() {
            global $good;
			global $category;

            if ( $good == null ) {
                $good = new Goods_Model();
            }

            $this->render( MM_DIR . '/views/good_create_page.php'
                    , array(
                        'page_name' => $this->goods_page,
                        'good' => $good,
						'category' => $category,
						'fields' => parse_ini_string( $category->get_fields(), true ),
                    )
                );
        }

		/**
		 * This method creates Edit Item form
		 *
		 * @global Goods_Model $good item data
		 */
        public function show_edit_good_page() {
            global $good;

			$category = Goods_Types_Model::get_by_id( $good->g_type_id );
			$categories = Goods_Types_Model::get_all_categories();

			//creating additional category fields
			$fields = parse_ini_string( $category->get_fields(), true );
			foreach ( $fields as $name => $field ) {
				$field_data = Goods_Model::get_custom_field( $name, $good->id );
				if ( FALSE !== $field_data ) {
					switch ( $field[ 'type' ] ) {
						case 'text' :
							$fields[ $name ][ 'value' ] = $field_data[ 'f_value' ];
							break;
						case 'checkbox' :
							if ( $field_data[ 'f_value' ] == 'on' ) {
								$fields[ $name ][ 'checked' ] = $field_data[ 'f_value' ];
							}
							break;
						case 'select' :
							$fields[ $name ][ 'value' ] = $field_data[ 'f_value' ];
							break;
						case 'textarea' :
							$fields[ $name ][ 'value' ] = $field_data[ 'f_value' ];
							break;
					}
				}
			}

            $this->render( MM_DIR . '/views/good_edit_page.php'
                    , array(
                        'page_name' => $this->goods_page,
                        'good' => $good,
						'category' => $category,
						'fields' => $fields,
						'categories' => $categories,
                    )
                );
        }

		/**
		 * This method renders items list
		 */
        public function show_goods_list() {

			//getting categories
			$categories = Goods_Types_Model::get_all_categories();
			if ( FALSE === $categories ) {
				$this->set_message( __( 'You have to create at least one category, before you can create an item.', MM_TEXTDOMAIN ) );
			}

			//setting up pagination
			$page = 1;
			if ( isset( $_GET[ 'p' ] ) && is_numeric( $_GET[ 'p' ] ) ) {
				$page = ( int )$_GET[ 'p' ];
			}

			require_once MM_DIR . '/libs/paginateit.php';
			//setting up pagination
			$paginator = new PaginateIt();

			$current_category = false;
			if ( isset( $_GET[ 'category_id' ] ) && is_numeric( $_GET[ 'category_id' ] )
					&& false != ( $current_category = Goods_Types_Model::get_by_id( $_GET[ 'category_id' ] ) ) ) {
				$goods = Goods_Model::get_category_goods( $current_category->id, $page, $this->goods_per_page, $paginator );
			}
			else {
				$goods = Goods_Model::get_all_goods( $page, $this->goods_per_page, $paginator );
			}

            $catalog_page = get_permalink( get_option( 'mm_catalog_page' ) );

			$this->render( MM_DIR . '/views/goods_list_page.php'
                    , array(
                        'page_name' => $this->goods_page,
                        'goods' => $goods,
						'categories' => $categories,
						'page_links' => $paginator->GetPageLinks(),
                        'catalog_page' => $catalog_page,
						'current_category' => $current_category,
                    ) );
        }

		/**
		 * This method analyzes request parameters and calls appropriate method
		 */
        public function handle_request() {
            if ( isset( $_GET[ 'page' ] ) && $_GET[ 'page' ] == $this->goods_page ) {
                if ( isset( $_GET[ 'action' ] ) ) {
                    switch ( $_GET[ 'action' ] ) {
                        case 'create':
                            $this->create();
                            break;
                        case 'edit':
                            $this->edit();
                            break;
                        case 'delete':
                            $this->delete();
                            break;
                    }
                }
            }
        }

		/**
		 * Creates new Item and saves it into DataBase.
		 * If item is successfully created - redirect to items list,
		 * if errors occure - shows form with errors descriptions.
		 * If user attempts to create item of none existing category - redirects to items list page
		 *
		 * @global Goods_Model $good
		 * @global Goods_Types_Model $category
		 */
        public function create() {
            global $good;
			global $category;

            if ( isset( $_POST[ 'mm_good' ] ) ) {
                if ( !wp_verify_nonce( $_POST[ 'mm_nonce' ], 'add_good' ) ) die( __( "Security check", MM_TEXTDOMAIN ) );
                $good = new Goods_Model();
                $good->set_fields( $_POST[ 'mm_good' ] );
                if ( $good->validate() ) {
                    $good->save();
					if ( isset( $_POST[ 'mm_goods_fields' ] ) ) {
						$category = Goods_Types_Model::get_by_id( $good->g_type_id );
						$good->save_custom_fields( $_POST[ 'mm_goods_fields' ], $category );
					}
                    $this->set_message( __( 'Item saved', MM_TEXTDOMAIN ) );
                    wp_redirect( 'admin.php?page='.$this->goods_page );
                    exit;
                }
                else {
					$category = Goods_Types_Model::get_by_id( $_POST[ 'mm_good' ][ 'g_type_id' ] );
					if ( FALSE === $category ) {
						$this->errors[ 'g_type_id' ] = __( 'Unknown category', MM_TEXTDOMAIN );
					}
                    $this->set_message( __( 'Save item error', MM_TEXTDOMAIN ) );
                }
            }
			else {
				//if user attempts to create item of none existing category - redirects to items list page
				if ( !isset( $_POST[ 'g_type_id' ] ) || FALSE === ( $category = Goods_Types_Model::get_by_id( $_POST[ 'g_type_id' ] ) ) ) {
                    $this->set_message( __( 'Unknown category', MM_TEXTDOMAIN ) );
                    wp_redirect( 'admin.php?page='.$this->goods_page );
                    exit;
				}
			}
        }

		/**
		 * This method updates existing item in database.
		 * In case of success - redirects to the item update page and shows
		 * appropriate method.
		 *
		 * @global Goods_Model $good item data
		 */
        public function edit() {
            global $good;
			global $category;

            //if we can not find the good - redirect to list page
            if ( !isset( $_GET[ 'good_id' ] )
                    || FALSE === ( $good = Goods_Model::get_by_id( $_GET[ 'good_id' ] ) ) ) {
                $this->set_message( __( 'Can\'t find this item', MM_TEXTDOMAIN ) );
                wp_redirect( 'admin.php?page=' . $this->goods_page );
                exit;
            }
            //if we receive post data - trying to save good
            if ( isset( $_POST[ 'mm_good' ] ) ) {
                if ( !wp_verify_nonce( $_POST[ 'mm_nonce' ], 'edit_good' ) ) die( __( "Security check", MM_TEXTDOMAIN ) );
                $good = Goods_Model::get_by_id( $_GET[ 'good_id' ] );

				if ( FALSE === $good ) {
                    $this->set_message( __( 'Can\'t find this item', MM_TEXTDOMAIN ) );
                    wp_redirect( 'admin.php?page=' . $this->goods_page );
                    exit;
				}
				$good->set_fields( $_POST[ 'mm_good' ] );
                if ( $good->validate() ) {
                    $good->save();
					if ( isset( $_POST[ 'mm_goods_fields' ] ) ) {
						$category = Goods_Types_Model::get_by_id( $good->g_type_id );
						$good->save_custom_fields( $_POST[ 'mm_goods_fields' ], $category );
					}
                    $this->set_message( __( 'Item updated', MM_TEXTDOMAIN ) );
                    wp_redirect( 'admin.php?page=' . $this->goods_page . '&action=edit&good_id=' . $good->id );
                    exit;
                }
                else {
                    $this->set_message( __( 'Update item error', MM_TEXTDOMAIN ) );
                }
            }
        }

		/**
		 * This method deletes item and redirects to items list page
		 */
        public function delete() {
            if ( !wp_verify_nonce( $_GET[ '_mm_nonce' ], 'delete_good' ) ) die( __( "Security check", MM_TEXTDOMAIN ) );
            if ( isset( $_GET[ 'good_id' ] ) && is_numeric( $_GET[ 'good_id' ] ) ) {
                Goods_Model::delete( $_GET[ 'good_id' ] );
                $this->set_message( __( 'Item deleted', MM_TEXTDOMAIN ) );
                wp_redirect( 'admin.php?page='.$this->goods_page );
                exit;
            }
            else {
                $this->set_message( __( 'Can\'t delete item', MM_TEXTDOMAIN ) );
            }
        }
    }
    $mmc = new Mix_Market_Goods();
}
