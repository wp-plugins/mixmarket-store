<?php
if ( !class_exists( 'Mix_Market' ) ) {
    /**
     * Service controller. Sets rewrite rules, creates database tables and mixmarket menu.
     */
    class Mix_Market extends Base_Controller {

        public function __construct() {
            add_action( 'admin_menu', array( $this, 'create_pages' ) );
            add_filter( 'rewrite_rules_array', array( $this, 'add_rewrite_rules' ) );
        }

        /**
         * This method adds rules to WordPress rewrite rules array.
         *
         * @global object $wp_rewrite WordPress rewrite rules
         * @param array $rules current rewrite rules array
         * @return array updated rewrite rule array
         */
        public function add_rewrite_rules( $rules ) {
			global $wp_rewrite;

			if ( $wp_rewrite->using_permalinks() ) {
				if ( function_exists( 'home_url' ) ) {
					$blog_url = home_url();
				}
				else {
					$blog_url = get_bloginfo( 'siteurl' );
				}
				$catalog_page_id = get_option( 'mm_catalog_page' );
				$catalog_page = get_permalink( $catalog_page_id );
				$catalog_page_name = substr( $catalog_page, strlen( $blog_url ) );
				//adding trailing slash
				if ( substr( $catalog_page_name, -1, 1 ) != '/' ) {
					$catalog_page_name .= '/';
				}
				//remofing forvard slash
				if ( substr( $catalog_page_name, 0, 1 ) == '/' ) {
					$catalog_page_name = substr( $catalog_page_name, 1 );
				}

	            $new_rules = array(
                    $catalog_page_name . 'category/([^/]+)/([^/]+)/page/(\d+)/?$' => 'index.php?page_id=' . $catalog_page_id . '&mm_category=$matches[1]&mm_brand=$matches[2]&mm_page=$matches[3]',
                    $catalog_page_name . 'category/([^/]+)/page/(\d+)/?$' => 'index.php?page_id=' . $catalog_page_id . '&mm_category=$matches[1]&mm_page=$matches[2]',
                    $catalog_page_name . 'category/([^/]+)/page/?$' => 'index.php?page_id=' . $catalog_page_id . '&mm_category=$matches[1]&mm_page=1',
                    $catalog_page_name . 'category/([^/]+)/([^/]+)/?$' => 'index.php?page_id=' . $catalog_page_id . '&mm_category=$matches[1]&mm_brand=$matches[2]',
                    $catalog_page_name . 'category/([^/]+)/?$' => 'index.php?page_id=' . $catalog_page_id . '&mm_category=$matches[1]',
                    $catalog_page_name . 'search/?$' => 'index.php?page_id=' . $catalog_page_id . '&mm_search=1',
                    $catalog_page_name . '([^/]+)/page/(\d+)/?$' => 'index.php?page_id=' . $catalog_page_id . '&mm_brand=$matches[1]&mm_page=$matches[2]',
                    $catalog_page_name . '([^/]+)/page/?$' => 'index.php?page_id=' . $catalog_page_id . '&mm_brand=$matches[1]&mm_page=1',
                    $catalog_page_name . '([^/]+)/?$' => 'index.php?page_id=' . $catalog_page_id . '&mm_brand=$matches[1]',
                    $catalog_page_name . '([^/]+?)/([^/]+?)/?$' => 'index.php?page_id=' . $catalog_page_id . '&mm_brand=$matches[1]&mm_model=$matches[2]',
                );
	            $rules = $new_rules + $rules;
			}

			return $rules;
        }

        /**
         * This method creates database table.
         *
         * @global object $wpdb WordPress database object
         */
        public static function create_tables() {
            global $wpdb;

            $goods_table = Goods_Model::get_table_name();
            $goods_types_table = Goods_Types_Model::get_table_name();
			$fields_table = Fields_Model::get_table_name();

			$goods_types_sql = 'CREATE  TABLE IF NOT EXISTS ' . $goods_types_table . ' (
              gt_id INT NOT NULL AUTO_INCREMENT ,
              gt_name VARCHAR(255) NOT NULL ,
              gt_slug VARCHAR(255) NOT NULL ,
              gt_fields TEXT NULL ,
              gt_parent INT NULL ,
              gt_description TEXT NULL ,
              gt_meta_description VARCHAR(255) NULL ,
              gt_title VARCHAR(255) NULL ,
              gt_meta_keywords VARCHAR(255) NULL ,
              PRIMARY KEY  (gt_id) ,
              INDEX parent  (gt_parent ASC) ,
              CONSTRAINT parent
                FOREIGN KEY  (gt_parent )
                REFERENCES  ' . $goods_types_table . ' (gt_id )
                ON DELETE CASCADE
                ON UPDATE CASCADE)
            ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;';
			$wpdb->query( $goods_types_sql );

            $goods_table_sql = 'CREATE  TABLE IF NOT EXISTS ' . $goods_table . ' (
			  g_id INT NOT NULL AUTO_INCREMENT ,
			  g_title VARCHAR(255) NOT NULL ,
			  g_model_title VARCHAR(255) NOT NULL ,
			  g_model VARCHAR(255) NOT NULL ,
			  g_brand VARCHAR(255) NOT NULL ,
			  g_brand_slug VARCHAR(255) NOT NULL ,
			  g_description TEXT NULL ,
			  g_image VARCHAR(255) NULL ,
              g_image_url VARCHAR(255) NULL ,
              g_image_thumb VARCHAR(255) NULL ,
              g_image_thumb_url VARCHAR(255) NULL ,
			  g_add_date DATETIME NOT NULL ,
			  g_type_id INT NOT NULL ,
			  g_featured TINYINT(1)  NOT NULL ,
              g_meta_description VARCHAR(255) NULL ,
              g_meta_keywords VARCHAR(255) NULL ,
			  PRIMARY KEY  (g_id) ,
			  INDEX type_id  (g_type_id ASC) ,
			  CONSTRAINT type_id
				FOREIGN KEY  (g_type_id )
				REFERENCES  ' . $goods_types_table . ' (gt_id )
				ON DELETE CASCADE
				ON UPDATE CASCADE)
			ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;';
            $wpdb->query( $goods_table_sql );

            $fields_table_sql = 'CREATE  TABLE IF NOT EXISTS ' . $fields_table . ' (
              f_id INT NOT NULL AUTO_INCREMENT ,
              f_name VARCHAR(255) NOT NULL ,
              f_value TEXT NOT NULL ,
              f_type VARCHAR(45) NULL ,
              f_goods_id INT NOT NULL ,
              PRIMARY KEY  (f_id) ,
              INDEX goods_id  (f_goods_id ASC) ,
              CONSTRAINT goods_id
                FOREIGN KEY  (f_goods_id )
                REFERENCES  ' . $goods_table . ' (g_id )
                ON DELETE CASCADE
                ON UPDATE CASCADE)
            ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;';

            $wpdb->query( $fields_table_sql );
		}

        /**
         * This method adds Mixmarket menu and submenu options page.
         */
		public function create_pages() {
            add_menu_page( __( 'MixMarket', MM_TEXTDOMAIN )
                , __( 'MixMarket', MM_TEXTDOMAIN )
                , 'manage_options'
                , 'mixmarket_options'
                , array( $this, 'render_options_page' )
            );

            $options_page = add_submenu_page( 'mixmarket_options'
                , __( 'Options', MM_TEXTDOMAIN )
                , __( 'Options', MM_TEXTDOMAIN )
                , 'manage_options'
                , 'mixmarket_options'
                , array( $this, 'render_options_page' )
            );

            add_action( 'admin_init', array( $this, 'register_settings' ) );
            add_action( 'admin_print_scripts-' . $options_page, array( $this, 'load_scripts' ) );
            add_action( 'admin_print_styles', array( $this, 'load_styles' ) );
		}

        /**
         * Regestering plugin settings.
         */
        public function register_settings() {
            register_setting( 'mm-options', 'mm_products_per_page' );
            register_setting( 'mm-options', 'mm_products_on_category_home' );
            register_setting( 'mm-options', 'mm_catalog_page' );
            register_setting( 'mm-options', 'mm_blocks_order' );
            register_setting( 'mm-options', 'mm_partner_id' );
            register_setting( 'mm-options', 'mm_use_standard_css' );
            register_setting( 'mm-options', 'mm_image_width' );
            register_setting( 'mm-options', 'mm_image_height' );
            register_setting( 'mm-options', 'mm_image_thumb_width' );
            register_setting( 'mm-options', 'mm_image_thumb_height' );
        }

        /**
         * This method creates options page
         *
         * @global object $wp_rewrite WordPress rewrite rules
         * @global array $mm_blocks blocks data
         */
        public function render_options_page() {
			global $wp_rewrite, $mm_blocks;
			$wp_rewrite->flush_rules();

			$mm_blocks[0] = array( 'name' => 'buy', 'title' => __( 'Where to buy', MM_TEXTDOMAIN ) );
			$mm_blocks[1] = array( 'name' => 'description', 'title' => __( 'Description', MM_TEXTDOMAIN ) );
//			$mm_blocks[2] = array( 'name' => 'links', 'title' => __( 'Links to reviews', MM_TEXTDOMAIN ) );

			$this->render( MM_DIR . '/views/options_page.php', array( 'blocks' => $mm_blocks ) );
        }

        /**
         * This method loads JS code for sorting blocks.
         */
        public function load_scripts() {
            wp_enqueue_script( 'mm_blocks_order', MM_URL . '/js/blocks_order.js'
                    , array( 'jquery-ui-sortable' )
                    , '1.0', true );
			$data = array(
				'select_file' => __( 'Please, select a file', MM_TEXTDOMAIN ),
                'confirm_data_delete' => __( 'Do you really want to delete all data and deactivate plugin?', MM_TEXTDOMAIN ),
			);
			wp_localize_script('mm_blocks_order', 'MM_Options', $data);
        }

        /**
         * Load plugin specific styles for admin page
         */
        public function load_styles() {
            wp_enqueue_style( 'mm_plugin_styles', MM_URL . '/css/mixmarket.css' );
        }
    }

    $mm = new Mix_Market();
}
