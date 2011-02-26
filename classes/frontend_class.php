<?php
if ( !class_exists( 'Mix_Market_Frontend' ) ) {
	/**
	 * Controller for creating frontend interface.
	 */
    class Mix_Market_Frontend extends Base_Controller {

        private $permalink = '';
		private $breadcrumbs;

		public function __construct() {
			add_action( 'send_headers', array( $this, 'headers' ) );
            add_filter( 'the_content', array( $this, 'generate_items_list' ) );
            add_filter( 'wp_title', array( $this, 'generate_title' ) );
            add_filter( 'query_vars', array( $this, 'add_query_vars' ) );
			add_action( 'wp_footer', array( $this, 'insert_js_code' ) );
            add_action( 'wp_print_styles', array( $this, 'load_styles' ) );
			add_action( 'wp_head', array( $this, 'add_meta_tags' ) );

			$this->breadcrumbs = new MM_Breadcrumbs();
        }

		public function headers( $data ) {
			global $category, $brand, $model, $good;

            $search = urldecode( $data->query_vars[ 'mm_search' ] );
            $brand_slug = urldecode( $data->query_vars[ 'mm_brand' ] );
            $model = urldecode( $data->query_vars[ 'mm_model' ] );
            $category_slug = urldecode( $data->query_vars[ 'mm_category' ] );
            $page = urldecode( $data->query_vars[ 'mm_page' ] );

			if ( !empty( $search ) ) {
			}
			elseif ( !empty( $category_slug ) && !empty( $brand_slug ) ) {
				$category = Goods_Types_Model::get_category_by_slug( $category_slug );
				$brand = Goods_Model::get_brand_by_slug( $brand_slug );
				if ( !$category ) {
					header("Status: 404 Not Found");
				}
				//$this->show_category_brand( $category_slug, $brand_slug, $page );
			}
			elseif ( !empty( $brand_slug ) && empty( $model ) ) {
				$brand = Goods_Model::get_brand_by_slug( $brand_slug );
				if ( !$brand_exists ) {
					header("Status: 404 Not Found");
				}
				//$this->show_brand( $brand_slug );
			}
			elseif ( !empty( $brand_slug ) && !empty( $model ) ) {
				$good = Goods_Model::get_by_brand_and_model( $brand_slug, $model );
				if ( false === $good ) {
					header("Status: 404 Not Found");
				}
				//$this->show_good( $brand_slug, $model );
			}
			elseif ( !empty( $category_slug ) ) {
				$category = Goods_Types_Model::get_category_by_slug( $category_slug );
				if ( !$category ) {
					header("Status: 404 Not Found");
				}
				//$this->show_category( $category_slug, $page );
			}
			else {
				//$this->show_featured_goods();
			}
		}

		function add_meta_tags() {
			global $category, $good;

			if ( NULL !== $category ) {
				if ( '' != $category->gt_meta_description ) {
					echo '<meta name="description" content="' . $category->gt_meta_description . '" />';
				}
				if ( '' != $category->gt_meta_keywords ) {
					echo '<meta name="keywords" content="' . $category->gt_meta_keywords . '" />';
				}
			}
			elseif ( NULL !== $good ) {
				if ( '' != $good->g_meta_description ) {
					echo '<meta name="description" content="' . $good->g_meta_description . '" />';
				}
				if ( '' != $good->g_meta_keywords ) {
					echo '<meta name="keywords" content="' . $good->g_meta_keywords . '" />';
				}
			}
		}

		/**
		 * This method is called by WP engine.
		 * Reserved for changing catalog pages title tags.
		 *
		 * @param string $title title tag content
		 * @return string updated title tag
		 */
		public function generate_title( $title ) {
            global $wp_query;
            $search = urldecode( $wp_query->query_vars[ 'mm_search' ] );
            $brand_slug = urldecode( $wp_query->query_vars[ 'mm_brand' ] );
            $model = urldecode( $wp_query->query_vars[ 'mm_model' ] );
            $category_slug = urldecode( $wp_query->query_vars[ 'mm_category' ] );
            $page = urldecode( $wp_query->query_vars[ 'mm_page' ] );

            if ( !empty( $search ) ) {
                $title = __( 'Search', MM_TEXTDOMAIN ) . ' | ';
            }
            elseif ( !empty( $category_slug ) && !empty( $brand_slug ) ) {
                $title = $brand_slug . ' | ';
			}
            elseif ( !empty( $brand_slug ) && empty( $model ) ) {
                $title = $brand_slug . ' | ';
            }
            elseif ( !empty( $brand_slug ) && !empty( $model ) ) {
                $title = Goods_Model::get_brand_model_title( $brand_slug, $model ) . ' | ';
            }
            elseif ( !empty( $category_slug ) ) {
                $category = Goods_Types_Model::get_category_by_slug( $category_slug );
                $title = $category->gt_title . ' | ';
            }
            else {
                //trying to find featured items (main page)
                $title = __( 'Home', MM_TEXTDOMAIN ) . ' | ';
            }

			return $title;
		}

        /**
         * This method parses query data and generates items list
         *
         * @global object $post post data
         * @global object $wp_query wordpress query data
         * @param string $content page content
         * @return string
         */
		public function generate_items_list( $content ) {
            global $post;
            $catalog_page_id = get_option( 'mm_catalog_page' );
			$this->breadcrumbs->set_home( '<a href="' . get_permalink( $catalog_page_id )
					. '">' . __( 'Home', MM_TEXTDOMAIN ) . '</a>' );

            if ( $post->ID == $catalog_page_id ) {
                $this->permalink = get_permalink( $post->ID );

                global $wp_query;
                $search = urldecode( $wp_query->query_vars[ 'mm_search' ] );
                $brand_slug = urldecode( $wp_query->query_vars[ 'mm_brand' ] );
                $model = urldecode( $wp_query->query_vars[ 'mm_model' ] );
                $category_slug = urldecode( $wp_query->query_vars[ 'mm_category' ] );
                $page = urldecode( $wp_query->query_vars[ 'mm_page' ] );

                if ( !empty( $search ) ) {
					$this->show_search();
                }
	            elseif ( !empty( $category_slug ) && !empty( $brand_slug ) ) {
                    //trying to create items list for brand of the category
					$this->show_category_brand( $category_slug, $brand_slug, $page );
				}
                elseif ( !empty( $brand_slug ) && empty( $model ) ) {
                    //trying to find items by brand
					$this->show_brand( $brand_slug );
				}
                elseif ( !empty( $brand_slug ) && !empty( $model ) ) {
                    //trying to find item by model AND brand
					$this->show_good( $brand_slug, $model );
                }
                elseif ( !empty( $category_slug ) ) {
                    //trying to create items list for category
					$this->show_category( $category_slug, $page );
                }
                else {
                    //trying to find featured items (main page)
                    $this->show_featured_goods();
                }
                return;
            }
            return $content;
		}

        /**
         * This method generates catalog main page (only featureg items are shown)
         */
        public function show_featured_goods() {
			$categories = Goods_Types_Model::get_all_categories();

            $goods_per_page = get_option( 'mm_products_per_page' );
            if ( !$goods_per_page ) {
                $goods_per_page = 10;
            }
			$goods = Goods_Model::get_featured_goods( 1, $goods_per_page );

			$this->render( MM_DIR . '/views/frontend_featured_goods.php'
                    , array(
                        'goods' => $goods,
						'categories' => $categories,
                        'permalink' => $this->permalink,
                    ) );
        }

        /**
         * This method creates page with full item description
         *
         * @global array $mm_blocks blocks data
         * @param string $brand_slug brand name
         * @param string $model model name
         */
		public function show_good( $brand_slug, $model ) {
			global $mm_blocks;
			global $category, $brand, $model, $good;

			$mm_blocks[0] = array( 'name' => 'buy', 'title' => __( 'Where to buy', MM_TEXTDOMAIN ) );
			$mm_blocks[1] = array( 'name' => 'description', 'title' => __( 'Description', MM_TEXTDOMAIN ) );
//			$mm_blocks[2] = array( 'name' => 'links', 'title' => __( 'Links to reviews', MM_TEXTDOMAIN ) );

			if ( false !== $good ) {
				$fields = Goods_Model::get_all_custom_fields( $good->id );
				$category = Goods_Types_Model::get_by_id( $good->g_type_id );
				$custom_fields = parse_ini_string( $category->get_fields(), true );
			}
			else {
				$fields = FALSE;
				$category = FALSE;
				$custom_fields = FALSE;
				$this->render( MM_DIR . '/views/404_good.php', array(
					'brand_slug' => $brand_slug,
                    'model' => $model,
				) );
                return;
			}

			$partner_id = get_option( 'mm_partner_id' );
			$blocks = explode( '|', get_option( 'mm_blocks_order' ) );

    		$this->breadcrumbs->add_link( mm_get_brand_link( $good, $this->permalink ) );
    		$this->breadcrumbs->add_link( $good->g_title );

            $this->render( MM_DIR . '/views/frontend_good_full.php'
                    , array(
                        'good' => $good,
						'fields' => $fields,
						'custom_fields' => $custom_fields,
                        'permalink' => $this->permalink,
						'partner_id' => $partner_id,
						'blocks' => $blocks,
						'blocks_data' => $mm_blocks,
                        'breadcrumbs' => $this->breadcrumbs->render(),
                    ) );
		}

        /**
         * This method generates list of items for a selected category
         *
         * @param string $category_slug slug of a category
         * @param mixed $page page (for pagination)
         */
		public function show_category( $category_slug, $page = null ) {
			global $category, $brand, $model;

            $cur_page = 1;
            if ( isset( $page ) && !empty( $page ) ) {
                $cur_page = ( int )$page;
            }

			if ( !$category ) {
				$this->render( MM_DIR . '/views/404_category.php', array(
					'slug' => $category_slug,
				) );
			}
			else {
                //setting up pagination

                $goods_per_page = get_option( 'mm_products_on_category_home' );
                if ( !$goods_per_page ) {
                    $goods_per_page = 10;
                }

                $pagination = array();

				$categories = Goods_Types_Model::get_all_categories();
				$show_search_form = true;
                $goods = Goods_Model::get_goods_by_category( $category, $cur_page, $goods_per_page, $this->permalink, $pagination, true );
				if ( FALSE === $goods ) {
					//trying to get featured goods from subcategories
					$goods = Goods_Model::get_subcategories_featured_goods( $categories, $category, $cur_page, $goods_per_page );
					$show_search_form = false;
				}

				$conditions = array();
				$fields_conditions = array();

				$parents = Goods_Types_Model::get_parents( $category );
				if ( !empty( $parents ) ) {
					foreach ( $parents as $parent ) {
						$this->breadcrumbs->add_link( mm_get_category_link( $parent, $this->permalink ) );
					}
				}
           		$this->breadcrumbs->add_link( $category->gt_name );

				$ids[] = $category->id;
				Goods_Types_Model::get_subcategories_ids($categories, $category->id, $ids);
				$category_brands = Goods_Model::get_category_brands( $category, $ids );

				$this->render( MM_DIR . '/views/frontend_categories_goods.php'
						, array(
							'goods' => $goods,
							'category' => $category,
							'permalink' => $this->permalink,
							'pagination' => $pagination,
							'fields' => parse_ini_string( $category->get_fields(), true ),
							'categories' => $categories,
							'conditions' => $conditions,
							'fields_conditions' => $fields_conditions,
							'breadcrumbs' => $this->breadcrumbs->render(),
							'category_brands' => $category_brands,
							'show_search_form' => $show_search_form,
							'category_ids' => $ids,
						) );
			}
		}

		public function show_category_brand( $category_slug, $brand_slug, $page = null ) {
			global $category, $brand, $model;

            $cur_page = 1;
            if ( isset( $page ) && !empty( $page ) ) {
                $cur_page = ( int )$page;
            }

			if ( !$category ) {
				$this->render( MM_DIR . '/views/404_category_brand.php', array(
					'slug' => $category_slug,
					'brand' => $brand,
				) );
			}
			else {
                //setting up pagination

                $goods_per_page = get_option( 'mm_products_on_category_home' );
                if ( !$goods_per_page ) {
                    $goods_per_page = 10;
                }

                $pagination = array();

                $goods = Goods_Model::get_goods_by_category_brand( $brand_slug, $category, $cur_page, $goods_per_page, $this->permalink, $pagination, true );
				$categories = Goods_Types_Model::get_all_categories();

				$conditions = array();
				$fields_conditions = array();

				$parents = Goods_Types_Model::get_parents( $category );
				if ( !empty( $parents ) ) {
					foreach ( $parents as $parent ) {
						$this->breadcrumbs->add_link( mm_get_category_link( $parent, $this->permalink ) );
					}
				}
           		$this->breadcrumbs->add_link( $category->gt_name );

				$ids[] = $category->id;
				Goods_Types_Model::get_subcategories_ids($categories, $category->id, $ids);
				$category_brands = Goods_Model::get_category_brands( $category, $ids );

				$this->render( MM_DIR . '/views/frontend_category_brand_goods.php'
						, array(
							'goods' => $goods,
							'category' => $category,
							'brand' => $brand,
							'permalink' => $this->permalink,
							'pagination' => $pagination,
							'fields' => parse_ini_string( $category->get_fields(), true ),
							'categories' => $categories,
							'conditions' => $conditions,
							'fields_conditions' => $fields_conditions,
							'breadcrumbs' => $this->breadcrumbs->render(),
							'category_brands' => $category_brands,
							'category_ids' => $ids,
						) );
			}
		}

        /**
         * This method generates list of items for a selected brand
         *
         * @param string $category_slug slug of a category
         * @param mixed $page page (for pagination)
         */
		public function show_brand( $brand_slug, $page = null ) {
			global $category, $brand, $model;

            $cur_page = 1;
            if ( isset( $page ) && !empty( $page ) ) {
                $cur_page = ( int )$page;
            }

			//$brand_exists = Goods_Model::is_brand_exists( $brand_slug );

			if ( !$brand ) {
				$this->render( MM_DIR . '/views/404_brand.php', array(
					'brand' => $brand,
				) );
			}
			else {
                //setting up pagination

                $goods_per_page = get_option( 'mm_products_per_page' );
                if ( !$goods_per_page ) {
                    $goods_per_page = 10;
                }

                $pagination = array();

                $goods = Goods_Model::get_goods_by_brand( $brand_slug, $cur_page, $goods_per_page, $this->permalink, $pagination );

    			$this->breadcrumbs->add_link( $brand );

				$this->render( MM_DIR . '/views/frontend_brand_goods.php'
						, array(
							'goods' => $goods,
							'brand' => $brand,
							'permalink' => $this->permalink,
							'pagination' => $pagination,
                            'breadcrumbs' => $this->breadcrumbs->render(),
						) );
			}
		}

        /**
         * This method parses search query, searching items and showing them
         */
		public function show_search() {
	        //getting search parameters $_POST[ 'mm_search' ] and $_POST[ 'mm_search_fields' ]
			$general_fields = array();
			if ( isset( $_POST[ 'mm_search' ] ) ) {
				$general_fields = $_POST[ 'mm_search' ];
			}
            //category specific search parameters
			$category_fields = array();
			if ( isset( $_POST[ 'mm_search_fields' ] ) ) {
				$category_fields = $_POST[ 'mm_search_fields' ];
			}

			//general search
			$conditions = array();
			foreach ( $general_fields as $name => $value ) {
				if ( '' != trim( $value ) ) {
					$conditions[ $name ] = $value;
				}
			}

			$category = null;
			$fields_conditions = array();
			if ( isset( $general_fields[ 'category_id' ] )
					&& false != ( $category = Goods_Types_Model::get_by_id( $general_fields[ 'category_id' ] ) ) ) {
				//search in category fields
				$fields = parse_ini_string( $category->get_fields(), true );
				foreach ( $fields as $name => $field ) {
					if ( isset( $category_fields[ $name ] ) && '' != trim( $category_fields[ $name ] ) ) {
						switch ( $field[ 'type' ] ) {
							case 'text' :
								$fields_conditions[ $name ] = $category_fields[ $name ];
								break;
							case 'checkbox' :
								$fields_conditions[ $name ] = 'on';
								break;
							case 'select' :
								if ( 'none' != $category_fields[ $name ] ) {
									$fields_conditions[ $name ] = $category_fields[ $name ];
								}
								break;
							case 'textarea' :
								$fields_conditions[ $name ] = $category_fields[ $name ];
								break;
						}
					}
				}
			}

			$goods = Goods_Model::search( $conditions, $fields_conditions, $category, true );

			$categories = Goods_Types_Model::get_all_categories();

			$this->breadcrumbs->add_link( __( 'Search', MM_TEXTDOMAIN ) );

			$ids[] = $category->id;
			Goods_Types_Model::get_subcategories_ids($categories, $category->id, $ids);
			$category_brands = Goods_Model::get_category_brands( $category, $ids );

			$this->render( MM_DIR . '/views/frontend_search_goods.php'
					, array(
						'goods' => $goods,
						'category' => $category,
						'permalink' => $this->permalink,
						'fields' => parse_ini_string( $category->get_fields(), true ),
						'categories' => $categories,
						'conditions' => $conditions,
						'fields_conditions' => $fields_conditions,
						'breadcrumbs' => $this->breadcrumbs->render(),
						'category_brands' => $category_brands,
						'category_ids' => $ids,
					) );
		}

        /**
         * Adding additional variables to query. This variables will be accessible throw $wp_query global
         * This method is called by WP.
         *
         * @param array $q_vars WP array with query vars
         * @return string
         */
		public function add_query_vars( $q_vars ) {
            $q_vars[] = 'mm_search';
            $q_vars[] = 'mm_brand';
            $q_vars[] = 'mm_model';
            $q_vars[] = 'mm_category';
            $q_vars[] = 'mm_page';
            return $q_vars;
        }

        /**
         * This method generate JS code for getting items prices from mixmarket.biz.
         *
         * @global object $wp_query query data
         */
		public function insert_js_code() {
			global $wp_query;

			$brand = $wp_query->query_vars[ 'mm_brand' ];
			$model = $wp_query->query_vars[ 'mm_model' ];
			$partner_id = get_option( 'mm_partner_id' );
			if ( !empty( $brand ) && !empty( $model ) && $partner_id ) {
?>
<script>
	document.write('<scr' + 'ipt language="javascript" type="text/javascript" src="http://<?php echo $partner_id; ?>.gk.mixmarket.biz/<?php echo $partner_id; ?>/?type=vert&pagesize=5&brand=<?php echo $brand; ?>&model=<?php echo $model; ?>&cat_id=&div=mixgk_<?php echo $partner_id; ?>&r=' + escape(document.referrer) + '&rnd=' + Math.round(Math.random() * 100000) + '" charset="windows-1251"><' + '/scr' + 'ipt>');
</script>
<?php
			}
		}

        public function load_styles() {
            if ( 'on' == get_option( 'mm_use_standard_css' ) ) {
                wp_enqueue_style( 'mm_frontend_css', MM_URL . '/css/mixmarket-frontend.css' );
            }
        }
    }
    $mmfe = new Mix_Market_Frontend();
}
