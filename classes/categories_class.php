<?php
if ( !class_exists( 'Mix_Market_Categories' ) ) {
    /**
     * Controller for working with categories.
     */
    class Mix_Market_Categories extends Base_Controller {
        //page $_GET parameter
        private $categories_page = 'mixmarket_categories';

        public function __construct() {
            //creating pages
            add_action( 'admin_menu', array( $this, 'create_pages' ) );
            //handling request
            add_action( 'init', array( $this, 'handle_request' ) );
        }

        /**
         * This method creates Categories page in admin menu.
         */
        public function create_pages() {
            $categories_page = add_submenu_page( 'mixmarket_options'
                , __( 'Categories', MM_TEXTDOMAIN )
                , __( 'Categories', MM_TEXTDOMAIN )
                , 'manage_options'
                , $this->categories_page
                , array( $this, 'render_categories_page' )
            );
            //adding JS code for "confirm deletion" action
			add_action( 'admin_print_scripts-' . $categories_page, array( $this, 'load_scripts' ) );
		}

        /**
         * This method includes and localizes JS code
         */
        public function load_scripts() {
            wp_enqueue_script( 'mm_confirm_delete', MM_URL . '/js/confirm_delete.js'
                    , array( 'jquery' )
                    , '1.0', true );
            wp_enqueue_script( 'mm_tinymce', MM_URL . '/js/tinymce/tiny_mce.js', array(), '1.0', true );
            wp_enqueue_script( 'mm_tinymce_setup', MM_URL . '/js/tiny_mce_setup.js', array( 'mm_tinymce' ), '1.0', true );
			wp_localize_script( 'mm_confirm_delete', 'mm_confirm_settings', array(
                'delete_message' => __( 'Are you sure? All subcategories will be also deleted.' ),
			) );
        }

        /**
         * This methos analyze action parameter and creates appropriate page.
         * It is called AFTER handle_request method by wordPress.
         */
        public function render_categories_page() {
            if ( isset( $_GET[ 'action' ] ) ) {
                switch ( $_GET[ 'action' ] ) {
                    case 'create' :
                        $this->show_create_category_page();
                        break;
                    case 'edit' :
                        $this->show_edit_category_page();
                        break;
                    default :
                        $this->show_categories_list();
                        break;
                }
            }
            else {
                $this->show_categories_list();
            }
        }

        /**
         * Retreving data and rendering category creation page
         *
         * @global Goods_Types_Model $category current category (may be created by 'create' method)
         */
        public function show_create_category_page() {
            global $category;

            if ( $category == null ) {
                $category = new Goods_Types_Model();
            }

            //we need all categories for building parent categories list
            $categories = Goods_Types_Model::get_all_categories();

            $this->render( MM_DIR . '/views/category_create_page.php'
                    , array(
                        'page_name' => $this->categories_page,
                        'category' => $category,
                        'categories' => $categories,
                    )
                );
        }

        /**
         * Retreving data and rendering category editing page
         *
         * @global Goods_Types_Model $category current category (may be created by 'edit' method)
         */
        public function show_edit_category_page() {
            global $category;

            $categories = Goods_Types_Model::get_all_categories();

            $this->render( MM_DIR . '/views/category_edit_page.php'
                    , array(
                        'page_name' => $this->categories_page,
                        'category' => $category,
                        'categories' => $categories,
                    )
                );
        }

        /**
         * Renders categories list
         */
        public function show_categories_list() {
            $categories = Goods_Types_Model::get_all_categories();
            $catalog_page = get_option( 'mm_catalog_page' );
            $permalink = get_permalink( $catalog_page );
            $this->render( MM_DIR . '/views/categories_list_page.php'
                    , array(
                        'page_name' => $this->categories_page,
                        'categories' => $categories,
                        'permalink' => $permalink,
                    ) );
        }

        /**
         * This method is called by WordPress engine during init action.
         * It analize action parameter and call apropriate method t process it.
         */
        public function handle_request() {
            if ( isset( $_GET[ 'page' ] ) && $_GET[ 'page' ] == $this->categories_page ) {
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
         * This methos creates a new category in database (if all fields are valid).
         * If category is successfully created it sends redirect to categories list page.
         * Otherwise it prepared global $category object for showing errors on category creation page.
         *
         * @global Goods_Types_Model $category current category
         */
        public function create() {
            global $category;

            if ( isset( $_POST[ 'mm_category' ] ) ) {
                if ( !wp_verify_nonce( $_POST[ 'mm_nonce' ], 'add_category' ) ) die( __( "Security check", MM_TEXTDOMAIN ) );
                $category = new Goods_Types_Model();
                $category->set_fields( $_POST[ 'mm_category' ] );
                if ( $category->validate() ) {
                    $category->save();
                    $this->set_message( __( 'Category saved', MM_TEXTDOMAIN ) );
                    wp_redirect( 'admin.php?page='.$this->categories_page );
                    exit;
                }
                else {
                    $this->set_message( __( 'Error saving category', MM_TEXTDOMAIN ) );
                }
            }
        }

        /**
         * This methos updates a category in database (if all fields are valid).
         * If category is successfully updated it sends redirect to categories list page.
         * Otherwise it prepared global $category object for showing errors on category edit page.
         *
         * @global Goods_Types_Model $category current category
         */
        public function edit() {
            global $category;

            //if we can not find the category - redirect to list page
            if ( !isset( $_GET[ 'category_id' ] )
                    || FALSE === ( $category = Goods_Types_Model::get_by_id( $_GET[ 'category_id' ] ) ) ) {
                wp_redirect( 'admin.php?page='.$this->categories_page );
                exit;
            }
            //if we receive post data - trying to save category
            if ( isset( $_POST[ 'mm_category' ] ) ) {
                if ( !wp_verify_nonce( $_POST[ 'mm_nonce' ], 'edit_category' ) ) die( __( "Security check", MM_TEXTDOMAIN ) );
                $category = Goods_Types_Model::get_by_id( $_GET[ 'category_id' ] );
                $category->set_fields( $_POST[ 'mm_category' ] );
                if ( $category->validate() ) {
                    $category->save();
                    $this->set_message( __( 'Category saved', MM_TEXTDOMAIN ) );
                    wp_redirect( 'admin.php?page='.$this->categories_page );
                    exit;
                }
                else {
                    $this->set_message( __( 'Error saving category', MM_TEXTDOMAIN ) );
                }
            }
        }

        /**
         * This method deletes category from the database.
         * If category is successfully updated it sends redirect to categories list page.
         */
        public function delete() {
            if ( !wp_verify_nonce( $_GET[ '_mm_nonce' ], 'delete_category' ) ) die( __( "Security check", MM_TEXTDOMAIN ) );
            if ( isset( $_GET[ 'category_id' ] ) && is_numeric( $_GET[ 'category_id' ] ) ) {
                Goods_Types_Model::delete( $_GET[ 'category_id' ] );
                $this->set_message( __( 'Category deleted', MM_TEXTDOMAIN ) );
                wp_redirect( 'admin.php?page='.$this->categories_page );
                exit;
            }
            else {
                $this->set_message( __( 'Can\'t delete category', MM_TEXTDOMAIN ) );
            }
        }
    }
    //object of this class
    $mmc = new Mix_Market_Categories();
}
