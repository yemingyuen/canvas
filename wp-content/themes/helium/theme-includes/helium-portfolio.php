<?php

/* ==========================================================================
	Portfolio Grid Defaults
============================================================================= */

if( ! function_exists( 'helium_portfolio_grid_defaults' ) ):

function helium_portfolio_grid_defaults() {

	return wp_parse_args( array(
		'show_filter'               => helium_get_option( 'portfolio_grid_show_filter' ), 
		'pagination'                => helium_get_option( 'portfolio_grid_pagination' ), 
		'ajax_button_text'          => helium_get_option( 'portfolio_grid_ajax_button_text' ), 
		'ajax_button_complete_text' => helium_get_option( 'portfolio_grid_ajax_button_complete_text' ), 
		'posts_per_page'            => helium_get_option( 'portfolio_grid_posts_per_page' ), 
		'include'                   => helium_get_option( 'portfolio_grid_include' ), 
		'behavior'                  => helium_get_option( 'portfolio_grid_behavior' ), 
		'orderby'                   => helium_get_option( 'portfolio_grid_orderby' ), 
		'order'                     => helium_get_option( 'portfolio_grid_order' ), 
		'layout'                    => helium_get_option( 'portfolio_grid_layout' ), 
		'columns'                   => helium_get_option( 'portfolio_grid_columns' )
	), array(
		'show_filter'               => true, 
		'pagination'                => 'ajax', 
		'ajax_button_text'          => 'Load More', 
		'ajax_button_complete_text' => 'No More Items', 
		'posts_per_page'            => get_option( 'posts_per_page' ), 
		'include'                   => array(), 
		'behavior'                  => 'lightbox', 
		'orderby'                   => 'date', 
		'order'                     => 'DESC', 
		'layout'                    => 'masonry', 
		'columns'                   => 4
	));
}
endif;

/* ==========================================================================
	Portfolio Body Class
============================================================================= */

if( ! function_exists( 'helium_portfolio_body_class' ) ):

function helium_portfolio_body_class( $classes ) {

	if( function_exists( 'youxi_portfolio_cpt_name' ) && is_singular( youxi_portfolio_cpt_name() ) ) {

		$post = get_queried_object();
		if( is_a( $post, 'WP_Post' ) ) {

			/* Layout metadata */
			$layout = wp_parse_args( $post->layout, array(
				'media_position'   => 'top', 
				'details_position' => 'left'
			));

			/* Validate layout positions */
			if( ! preg_match( '/^top|(lef|righ)t$/', $layout['media_position'] ) ) {
				$layout['media_position'] = 'top';
			}

			if( ! preg_match( '/^hidden|(lef|righ)t$/', $layout['details_position'] ) ) {
				$layout['details_position'] = 'left';
			}

			/* Media metadata */
			$media = wp_parse_args( $post->media, array(
				'type' => 'featured-image'
			));

			/* Validate media type */
			if( ! preg_match( '/^(featur|stack|justifi)ed(-(image|grids))?|slider|(vide|audi)o$/', $media['type'] ) ) {
				$media['type'] = 'featured-image';
			}

			$classes = array_merge( $classes, array(
				"single-{$post->post_type}-media-" . $layout['media_position'], 
				"single-{$post->post_type}-media-" . $media['type'], 
				"single-{$post->post_type}-details-" . $layout['details_position']
			));
		}
	}

	return $classes;
}
endif;
add_filter( 'body_class', 'helium_portfolio_body_class' );

/* ==========================================================================
	Portfolio Pages
============================================================================= */

if( ! function_exists( 'helium_portfolio_pages' ) ):

function helium_portfolio_pages() {

	$choices = array(
		'default' => __( 'Default Archive', 'helium' )
	);
	
	$pages = get_posts(array(
		'post_type'  => 'page', 
		'meta_key'   => '_wp_page_template', 
		'meta_value' => 'archive-portfolio.php'
	));

	if( $pages ) {
		foreach( $pages as $page ) {
			$choices[ $page->ID ] = $page->post_title;
		}
	}

	return $choices;
}
endif;

/* ==========================================================================
	Portfolio Archive Page
============================================================================= */

if( ! function_exists( 'helium_portfolio_image_sizes' ) ):

function helium_portfolio_image_sizes( $sizes ) {
	return array_merge( $sizes, array(
		'helium_portfolio_thumb_4by3' => array(
			'width' => 720, 
			'height' => 540, 
			'crop' => true
		), 
		'helium_portfolio_thumb_square' => array(
			'width' => 720, 
			'height' => 720, 
			'crop' => true
		), 
		'helium_portfolio_thumb' => array(
			'width' => 720
		)
	));
}
endif;
add_filter( 'helium_wp_image_sizes', 'helium_portfolio_image_sizes' );