<?php
/**
 * Mixmarket widget.
 * On frontend shows category name and links to items in this category.
 * Allows to select category and set the number of links, that will be shown.
 */
class MM_Widget extends WP_Widget {
    function MM_Widget() {
        parent::WP_Widget( false, $name = 'Mixmarket' );
    }

    /**
     * This method creates widget frontend.
     */
    function widget( $args, $instance ) {
        extract( $args );
        //getting category
        if ( $instance[ 'category' ] == 0 ) {
            $goods = Goods_Model::get_featured_goods( 1, $instance[ 'items_num' ] );
            $category_name = apply_filters( 'widget_title', __( 'Featured', MM_TEXTDOMAIN ) );
        }
        else {
            $category = Goods_Types_Model::get_by_id( $instance[ 'category' ] );
            $category_name = apply_filters( 'widget_title', $category->gt_name );

			$rnd_order = ( $instance[ 'items_order' ] == 'on' ) ? true : false;

            require_once( MM_DIR . '/libs/paginateit.php' );
            $paginator = new PaginateIt();
            $goods = Goods_Model::get_category_goods( $instance[ 'category' ], 1, $instance[ 'items_num' ], $paginator, true, $rnd_order );
        }

        $catalog_page = get_option( 'mm_catalog_page' );
        $permalink = get_permalink( $post->ID );
        ?>
              <?php echo $before_widget; ?>
                  <?php if ( $category_name )
                        echo $before_title . $category_name . $after_title; ?>
              <?php
              if ( $goods ) {
                  echo '<ul>';
                  foreach ( $goods as $good ) {
					  echo '<li>';
					  if ( $instance[ 'show_image' ] == 'on' ) {
						  echo mm_get_title_link( $good, $permalink, true );
					  }
                      echo mm_get_title_link( $good, $permalink );
					  echo '</li>';
                  }
                  echo '</ul>';
              }
              else {
                  _e( 'No items found', MM_TEXTDOMAIN );
              }
              ?>
              <?php echo $after_widget; ?>
        <?php
    }

    /**
     * This method updates category settings.
     */
    function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        $instance[ 'items_num' ] = strip_tags( $new_instance[ 'items_num' ] );
        $instance[ 'category' ] = strip_tags( $new_instance[ 'category' ] );
        $instance[ 'items_order' ] = strip_tags( $new_instance[ 'items_order' ] );
        $instance[ 'show_image' ] = strip_tags( $new_instance[ 'show_image' ] );
        return $instance;
    }

    /**
     * This method creates widget settings form
     */
    function form($instance) {
        $current_category_id = $instance[ 'category' ];
        $categories = Goods_Types_Model::get_all_categories();
        $items_num = $instance[ 'items_num' ];
		$items_order = $instance[ 'items_order' ];
		$show_image = $instance[ 'show_image' ];
        ?>
            <p><label for="<?php echo $this->get_field_id( 'category' ); ?>"><?php _e( 'Category:', MM_TEXTDOMAIN ); ?>
                    <select id="<?php echo $this->get_field_id( 'category' ); ?>" name="<?php echo $this->get_field_name( 'category' ); ?>">
                        <?php $selected = ( $current_category_id == 0 ) ? ' selected="selected"' : ''; ?>
                        <option value="0"<?php echo $selected; ?>><?php _e( 'Featured', MM_TEXTDOMAIN ); ?></option>
                    <?php mm_the_nested_select( $categories, NULL, 0, NULL, $current_category_id ); ?>
                    </select>
            </label></p>
            <p><label for="<?php echo $this->get_field_id( 'items_num' ); ?>"><?php _e( 'Items number:', MM_TEXTDOMAIN ); ?> <input class="widefat" id="<?php echo $this->get_field_id( 'items_num' ); ?>" name="<?php echo $this->get_field_name( 'items_num' ); ?>" type="text" value="<?php echo $items_num; ?>" /></label></p>
            <p><label for="<?php echo $this->get_field_id( 'items_order' ); ?>"><?php _e( 'Random order:', MM_TEXTDOMAIN ); ?> <input id="<?php echo $this->get_field_id( 'items_order' ); ?>" name="<?php echo $this->get_field_name( 'items_order' ); ?>" type="checkbox" <?php echo ( 'on' == $items_order ) ? 'checked="checked"' : ''; ?> /></label></p>
            <p><label for="<?php echo $this->get_field_id( 'show_image' ); ?>"><?php _e( 'Show image:', MM_TEXTDOMAIN ); ?> <input id="<?php echo $this->get_field_id( 'show_image' ); ?>" name="<?php echo $this->get_field_name( 'show_image' ); ?>" type="checkbox" <?php echo ( 'on' == $show_image ) ? 'checked="checked"' : ''; ?> /></label></p>
        <?php
    }

}