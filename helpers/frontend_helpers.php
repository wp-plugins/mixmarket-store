<?php
/**
 * This method generates category link based on WP permalink settings
 *
 * @global object $wp_rewrite WordPress rewrite rules
 * @param Goods_Types_Model $category category data
 * @param string $page_link link to mixmarket catalogue page
 * @return string category link
 */
function mm_get_category_link( $category, $page_link ) {
	global $wp_rewrite;
	if ( $wp_rewrite->using_permalinks() ) {
		$slash = ( substr( $page_link, -1 ) != '/' ) ? '/' : '';
	    return '<a href="' . $page_link . $slash . 'category/' . $category->gt_slug . '">' . $category->gt_name . '</a>';
	}
	else {
		//if home page
		if ( strstr( $page_link, 'page_id' ) === false ) {
			$page_id = get_option( 'mm_catalog_page' );
			$page_link .= '?page_id=' . $page_id;
		}
	    return '<a href="' . $page_link . '&mm_category=' . $category->gt_slug . '">' . $category->gt_name . '</a>';
	}
}

function mm_get_category_url( $category, $page_link ) {
	global $wp_rewrite;
	if ( $wp_rewrite->using_permalinks() ) {
		$slash = ( substr( $page_link, -1 ) != '/' ) ? '/' : '';
	    return $page_link . $slash . 'category/' . $category->gt_slug;
	}
	else {
		//if home page
		if ( strstr( $page_link, 'page_id' ) === false ) {
			$page_id = get_option( 'mm_catalog_page' );
			$page_link .= '?page_id=' . $page_id;
		}
	    return $page_link . '&mm_category=' . $category->gt_slug;
	}
}

/**
 * This method generates link for the item based on WP permalink settings
 *
 * @global object $wp_rewrite WordPress rewrite rules
 * @param Goods_Model $good item data
 * @param string $page_link link to mixmarket catalogue page
 * @return string item link
 */
function mm_get_title_link( $good, $page_link, $show_image = false ) {
	global $wp_rewrite;
	if ( $wp_rewrite->using_permalinks() ) {
		$slash = ( substr( $page_link, -1 ) != '/' ) ? '/' : '';
		if ( $show_image ) {
		    return '<a href="' . $page_link . $slash . $good->g_brand_slug . '/' . $good->g_model . '"><img src="' . mm_get_image_url( $good, 'thumb' ) . '" alt="' . $good->g_title . '" /></a>';
		}
		else {
		    return '<a href="' . $page_link . $slash . $good->g_brand_slug . '/' . $good->g_model . '">' . $good->g_title . '</a>';
		}
	}
	else {
		//if home page
		if ( strstr( $page_link, 'page_id' ) === false ) {
			$page_id = get_option( 'mm_catalog_page' );
			$page_link .= '?page_id=' . $page_id;
		}
		if ( $show_image ) {
		    return '<a href="' . $page_link . '&mm_brand=' . $good->g_brand_slug . '&mm_model=' . $good->g_model . '"><img src="' . mm_get_image_url( $good, 'thumb' ) . '" alt="' . $good->g_title . '" /></a>';
		}
		else {
		    return '<a href="' . $page_link . '&mm_brand=' . $good->g_brand_slug . '&mm_model=' . $good->g_model . '">' . $good->g_title . '</a>';
		}
	}
}

/**
 * This method generates link for the item brand based on WP permalink settings
 *
 * @global object $wp_rewrite WordPress rewrite rules
 * @param Goods_Model $good item data
 * @param string $page_link link to mixmarket catalogue page
 * @return string brand link
 */
function mm_get_brand_link( $good, $page_link ) {
	global $wp_rewrite;
	if ( $wp_rewrite->using_permalinks() ) {
		$slash = ( substr( $page_link, -1 ) != '/' ) ? '/' : '';
	    return '<a href="' . $page_link . $slash . $good->g_brand_slug . '">' . $good->g_brand . '</a>';
	}
	else {
		//if home page
		if ( strstr( $page_link, 'page_id' ) === false ) {
			$page_id = get_option( 'mm_catalog_page' );
			$page_link .= '?page_id=' . $page_id;
		}
	    return '<a href="' . $page_link . '&mm_brand=' . $good->g_brand_slug . '">' . $good->g_brand . '</a>';
	}
}

function mm_get_category_brand_link( $category, $brand, $page_link ) {
	global $wp_rewrite;
	if ( $wp_rewrite->using_permalinks() ) {
		$slash = ( substr( $page_link, -1 ) != '/' ) ? '/' : '';
	    return '<a href="' . $page_link . $slash . 'category/' . $category->gt_slug . '/' . $brand[ 'g_brand_slug' ] . '">' . $brand[ 'g_brand' ] . '</a>';
	}
	else {
		//if home page
		if ( strstr( $page_link, 'page_id' ) === false ) {
			$page_id = get_option( 'mm_catalog_page' );
			$page_link .= '?page_id=' . $page_id;
		}
	    return '<a href="' . $page_link . '&mm_brand=' . $brand[ 'g_brand_slug' ] . '&mm_category=' . $category->gt_slug . '">' . $brand[ 'g_brand' ] . '</a>';
	}
}

/**
 * This method generates link for the item model based on WP permalink settings
 *
 * @global object $wp_rewrite WordPress rewrite rules
 * @param Goods_Model $good item data
 * @param string $page_link link to mixmarket catalogue page
 * @return string model link
 */
function mm_get_model_link( $good, $page_link ) {
	global $wp_rewrite;
	if ( $wp_rewrite->using_permalinks() ) {
		$slash = ( substr( $page_link, -1 ) != '/' ) ? '/' : '';
	    return '<a href="' . $page_link . $slash . $good->g_brand_slug . '/' . $good->g_model . '">' . $good->g_model_title . '</a>';
	}
	else {
		//if home page
		if ( strstr( $page_link, 'page_id' ) === false ) {
			$page_id = get_option( 'mm_catalog_page' );
			$page_link .= '?page_id=' . $page_id;
		}
	    return '<a href="' . $page_link . '&mm_brand=' . $good->g_brand_slug . '&mm_model=' . $good->g_model . '">' . $good->g_model_title . '</a>';
	}
}

/**
 * This method generates link to the item view
 *
 * @global object $wp_rewrite WordPress rewrite rules
 * @param Goods_Model $good item data
 * @param string $page_link link to mixmarket catalogue page
 * @return string view link
 */
function mm_get_view_link( $good, $page_link ) {
	global $wp_rewrite;
	if ( $wp_rewrite->using_permalinks() ) {
		$slash = ( substr( $page_link, -1 ) != '/' ) ? '/' : '';
	    return $page_link . $slash . $good->g_brand_slug . '/' . $good->g_model;
	}
	else {
		//if home page
		if ( strstr( $page_link, 'page_id' ) === false ) {
			$page_id = get_option( 'mm_catalog_page' );
			$page_link .= '?page_id=' . $page_id;
		}
	    return $page_link . '&mm_brand=' . $good->g_brand_slug . '&mm_model=' . $good->g_model;
	}
}

/**
 * Generates link to the catalogue search page
 *
 * @global object $wp_rewrite WordPress rewrite rules
 * @param string $page_link link to mixmarket catalogue page
 * @return string catalogue search link
 */
function mm_get_search_link( $page_link ) {
	global $wp_rewrite;
	if ( $wp_rewrite->using_permalinks() ) {
		$slash = ( substr( $page_link, -1 ) != '/' ) ? '/' : '';
	    return $page_link . $slash . 'search/';
	}
	else {
		//if home page
		if ( strstr( $page_link, 'page_id' ) === false ) {
			$page_id = get_option( 'mm_catalog_page' );
			$page_link .= '?page_id=' . $page_id;
		}
	    return $page_link . '&mm_search=1';
	}
}

/**
 * This method generates nested html list of categories.
 *
 * @param array $categories catalogue of categories
 * @param string $permalink link to the catalogue page
 * @param int $parent_id parent category id
 * @param Goods_Types_Model $current_cat current category
 * @return string html list with categories
 */
function mm_get_nested_list( $categories, $permalink = '', $parent_id = NULL, $current_cat = null ) {
    $count_sub = 0;
    $items = '';
	foreach ( $categories as $category ) {
		if ( $category->gt_parent == $parent_id && $category->id != $current_cat->id ) {
            $items .= '<li>' . mm_get_category_link( $category, $permalink );
			$items .= mm_get_nested_list( $categories, $permalink, $category->id, $current_cat );
            $items .= '</li>';
            $count_sub++;
		}
	}
    if ( $count_sub > 0 ) {
        return '<ul>' . $items . '</ul>';
    }
}

function mm_get_multi_col_list( $categories, $permalink, $depth, $cur_depth = 0, $parent_id = NULL ) {
    $count_sub = 0;
    $items = '';
    $root = '';
    if ( $cur_depth >= $depth ) {
        return;
    }
	foreach ( $categories as $category ) {
		if ( $category->gt_parent == $parent_id ) {
            if ( $cur_depth == 0 ) {
                $root .= '<div class="mm_category"><h2>' . mm_get_category_link( $category, $permalink ) . '</h2>';
                $root .= mm_get_multi_col_list( $categories, $permalink, $depth, $cur_depth + 1, $category->id ) . '</div>';
            }
            else {
                $items .= '<li>' . mm_get_category_link( $category, $permalink );
                $items .= mm_get_multi_col_list( $categories, $permalink, $depth, $cur_depth + 1, $category->id );
                $items .= '</li>';
                $count_sub++;
            }
		}
	}
    if ( $count_sub > 0 ) {
        $items = '<ul>' . $items . '</ul>';
    }

    if ( $cur_depth == 0 ) {
        return  $root;
    }
    else {
        return $items;
    }
}

function mm_truncate_utf8( $string, $len, $wordsafe = FALSE, $dots = FALSE ) {
    $slen = strlen( $string );
    if ( $slen <= $len ) {
        return $string;
    }
    if ( $wordsafe ) {
        $end = $len;
        while ( ( $string[--$len] != ' ' ) && ( $len > 0 ) ) {

        };
        if ($len == 0) {
            $len = $end;
        }
    }
    if ( ( ord( $string[$len] ) < 0x80 ) || ( ord($string[$len]) >= 0xC0 ) ) {
        return substr( $string, 0, $len ) . ( $dots ? ' ...' : '' );
    }
    while ( --$len >= 0 && ord( $string[$len] ) >= 0x80 && ord( $string[$len] ) < 0xC0 ) {

    };
    return substr( $string, 0, $len ) . ( $dots ? ' ...' : '' );
}

function mm_get_image_url( $good, $type ) {
	$url = WP_CONTENT_URL;
	$pieces = explode( '.', $good->g_image_url );
	switch ( $type ) {
		case 'card' :
			$card_size = get_option( 'mm_image_width' );
			if ( false === $card_size ) {
				$card_size = 200;
			}
			$pieces[ count( $pieces ) - 2 ] .= '-'.$card_size.'x'.$card_size;
			$url .= implode( '.', $pieces );
			break;
		case 'thumb' :
			$thumb_size = get_option( 'mm_image_thumb_width' );
			if ( false === $thumb_size ) {
				$thumb_size = 50;
			}
			$pieces[ count( $pieces ) - 2 ] .= '-'.$thumb_size.'x'.$thumb_size;
			$url .= implode( '.', $pieces );
			break;
		default :
			$url .= $good->g_image_url;
			break;
	}
	return $url;
}