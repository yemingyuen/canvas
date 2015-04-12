<?php

/* ==========================================================================
	Get options from the customizer
============================================================================= */

function helium_option_keys() {

	static $option_keys = array();
	
	if( empty( $option_keys ) ) {
		$option_keys = array_keys( helium_default_options() );
	}

	return $option_keys;
}

function helium_default_options() {
	return array(
		'logo_image' => '', 
		'logo_height' => 25, 
		'show_search' => true, 
		'copyright_text' => __( '&copy; Youxi Themes. 2012-2014. All Rights Reserved.', 'helium' ), 
		'accent_color' => '#3dc9b3', 
		'headings_1234_font' => '', 
		'headings_56_font' => '', 
		'body_font' => '', 
		'menu_font' => '', 
		'blockquote_font' => '', 
		'gridlist_title_font' => '', 
		'gridlist_subtitle_font' => '', 
		'content_nav_font' => '', 
		'blog_date_format' => 'F d, Y', 
		'hidden_post_meta' => array(), 
		'blog_show_tags' => true, 
		'blog_sharing' => true, 
		'blog_show_author' => true, 
		'blog_related_posts' => true, 
		'blog_related_posts_count' => 3, 
		'blog_related_posts_behavior' => 'lightbox', 
		'blog_summary' => 'the_excerpt', 
		'blog_excerpt_length' => 100, 
		'blog_index_layout' => 'boxed', 
		'blog_archive_layout' => 'boxed', 
		'blog_single_layout' => 'boxed', 
		'blog_index_title' => __( 'Welcome to Our Blog', 'helium' ), 
		'blog_single_title' => __( 'Currently Reading', 'helium' ), 
		'blog_category_title' => __( 'Category: {category}', 'helium' ), 
		'blog_tag_title' => __( 'Posts Tagged &lsquo;{tag}&rsquo;', 'helium' ), 
		'blog_author_title' => __( 'Posts by {author}', 'helium' ), 
		'blog_date_title' => __( 'Archive for {date}', 'helium' ), 
		'portfolio_show_related_items' => true, 
		'portfolio_related_items_count' => 3, 
		'portfolio_related_items_behavior' => 'lightbox', 
		'portfolio_archive_page_title' => __( 'Portfolio Archive', 'helium' ), 
		'portfolio_grid_show_filter' => true, 
		'portfolio_grid_pagination' => 'ajax', 
		'portfolio_grid_ajax_button_text' => __( 'Load More', 'helium' ), 
		'portfolio_grid_ajax_button_complete_text' => __( 'No More Items', 'helium' ), 
		'portfolio_grid_posts_per_page' => 10, 
		'portfolio_grid_include' => array(), 
		'portfolio_grid_behavior' => 'lightbox', 
		'portfolio_grid_orderby' => 'date', 
		'portfolio_grid_order' => 'DESC', 
		'portfolio_grid_layout' => 'masonry', 
		'portfolio_grid_columns' => 4, 
		'edd_show_cart' => true, 
		'edd_show_categories' => true, 
		'edd_show_tags' => true, 
		'edd_show_sharing_buttons' => true, 
		'edd_show_related_items' => true, 
		'edd_related_items_count' => 3, 
		'edd_related_items_behavior' => 'lightbox', 
		'edd_archive_page_title' => __( 'Downloads Archive', 'helium' ), 
		'edd_grid_pagination' => 'ajax', 
		'edd_grid_ajax_button_text' => __( 'Load More', 'helium' ), 
		'edd_grid_ajax_button_complete_text' => __( 'No More Items', 'helium' ), 
		'edd_grid_posts_per_page' => 10, 
		'edd_grid_include' => array(), 
		'edd_grid_behavior' => 'lightbox', 
		'edd_grid_columns' => 4
	);
}

function helium_get_all_options() {

	static $theme_options_cache = null;

	if( ! is_null( $theme_options_cache ) ) {
		return $theme_options_cache;
	}

	$theme = wp_get_theme();
	$theme_mod_key = preg_replace( '/\W/', '_', $theme->stylesheet ) . '_settings';

	$options = get_theme_mod( $theme_mod_key, '__not_initialized' );

	if( '__not_initialized' === $options ) {
		$options = helium_default_options();
		set_theme_mod( $theme_mod_key, $options );
	}

	return ( $theme_options_cache = $options );
}

function helium_get_option( $option_id, $default = '' ) {

	static $cached_options = array();

	if( in_array( $option_id, helium_option_keys() ) && isset( $_GET[ $option_id ] ) ) {
		return $_GET[ $option_id ];
	}

	if( isset( $cached_options[ $option_id ] ) ) {
		return $cached_options[ $option_id ];
	}

	$ot_option_keys = array(
		'addthis_sharing_buttons', 
		'addthis_profile_id', 
		'ajax_navigation', 
		'ajax_navigation_scroll_top', 
		'ajax_navigation_loading_text', 
		'twitter_consumer_key', 
		'twitter_consumer_secret', 
		'twitter_access_token', 
		'twitter_access_token_secret', 
		'envato_username', 
		'envato_api_key', 
		'custom_css', 
		'favicon'
	);
	$ot_on_off_options = array(
		'ajax_navigation', 
		'ajax_navigation_scroll_top'
	);

	if( in_array( $option_id, $ot_option_keys ) ) {
		if( in_array( $option_id, $ot_on_off_options ) ) {
			$return  = ( 'on' === ot_get_option( $option_id, $default ) );
		} else {
			$return = ot_get_option( $option_id, $default );	
		}

		return ( $cached_options[ $option_id ] = $return );
	}

	$options = helium_get_all_options();

	return isset( $options[ $option_id ] ) ? ( $cached_options[ $option_id ] = $options[ $option_id ] ) : $default;
}

/* ==========================================================================
	Get default accent color
============================================================================= */

function helium_default_accent_color() {
	return apply_filters( 'helium_default_accent_color', '#3dc9b3' );
}

/* ==========================================================================
	Default Walker Class
============================================================================= */

if( ! class_exists( 'Helium_Walker_Nav_Menu' ) ):

	class Helium_Walker_Nav_Menu extends Walker_Nav_Menu {

		function start_lvl( &$output, $depth = 0, $args = array() ) {
			$output .= '<span class="subnav-close"></span>';
			parent::start_lvl( $output, $depth, $args );
		}
	}
endif;

/* ==========================================================================
	Menu Fallback if none was specified
============================================================================= */

if( ! function_exists( 'helium_fallback_menu' ) ):

function helium_fallback_menu() {
	?>
	<ul class="menu">
		<li class="menu-item menu-item-home<?php if( is_front_page() ) echo ' current-menu-item'; ?>">
			<a href="<?php echo home_url( '/' ); ?>"><?php _e( 'Home', 'helium' ); ?></a>
		</li>
		<?php wp_list_pages( 'title_li=&sort_column=menu_order' ); ?>
	</ul>
	<?php
}
endif;

/* ==========================================================================
	Recognized Sidebars
============================================================================= */

if( ! function_exists( 'helium_recognized_sidebars' ) ) {

	function helium_recognized_sidebars( $sidebars ) {
		$recognized = array();
		foreach( $sidebars as $id => $sidebar ) {
			if( ! preg_match( '/^footer_widget_area_\d+$/', $id ) ) {
				$recognized[ $id ] = $sidebar;
			}
		}
		return $recognized;
	}
}
add_filter( 'ot_recognized_sidebars', 'helium_recognized_sidebars' );
add_filter( 'youxi_shortcode_recognized_sidebars', 'helium_recognized_sidebars' );

/* ==========================================================================
	Automatic Theme Updates
============================================================================= */

function helium_check_theme_updates( $updates ) {

	if( isset( $updates->checked ) ) {

		/* Get Envato username and API key */
		$envato_username = helium_get_option( 'envato_username' );
		$envato_apikey   = helium_get_option( 'envato_api_key' );

		if( '' !== $envato_username && '' !== $envato_apikey ) {
			if( ! class_exists( 'Pixelentity_Themes_Updater' ) ) {
				require( get_template_directory() . '/lib/class-pixelentity-themes-updater.php' );
			}

			$updater = new Pixelentity_Themes_Updater( $envato_username, $envato_apikey );
			$updates = $updater->check( $updates );
		}
	}

	return $updates;
}
add_filter( 'pre_set_site_transient_update_themes', 'helium_check_theme_updates' );

/* ==========================================================================
	Post Pages Link
============================================================================= */

if( ! function_exists( 'helium_link_pages_link' ) ):

function helium_link_pages_link( $link ) {
	return '<li>' . $link . '</li>';
}
endif;
add_filter( 'wp_link_pages_link', 'helium_link_pages_link' );

/* ==========================================================================
	RoyalSlider Settings
============================================================================= */

function helium_rs_settings( $settings ) {

	$settings = wp_parse_args( $settings, array(
		'autoHeight'           => true, 
		'autoScaleSliderRatio' => array( 'width' => 4, 'height' => 3 ), 
		'imageScaleMode'       => 'fill', 
		'controlNavigation'    => true, 
		'arrowsNav'            => true, 
		'loop'                 => true, 
		'slidesOrientation'    => 'horizontal', 
		'transitionType'       => 'move', 
		'transitionSpeed'      => 600
	));

	if( $settings['autoHeight'] ) {

		$rs_settings = array(
			'autoHeight' => true, 
			'autoScaleSlider' => false, 
			'imageScaleMode' => 'none', 
			'imageAlignCenter' => false
		);

		// Vertical slide orientation is not supported if autoHeight is enabled
		$settings['slidesOrientation'] = 'horizontal';
	} else {
		$rs_settings = array(
			'autoScaleSlider' => true, 
			'autoScaleSliderWidth' => $settings['autoScaleSliderRatio']['width'], 
			'autoScaleSliderHeight' => $settings['autoScaleSliderRatio']['height'], 
			'imageScaleMode' => $settings['imageScaleMode']
		);
	}

	$rs_settings = array_merge( $rs_settings, array(
		'controlNavigation' => $settings['controlNavigation'] ? 'bullets' : 'none', 
		'arrowsNav' => (bool) $settings['arrowsNav'], 
		'loop' => (bool) $settings['loop'], 
		'slidesOrientation' => $settings['slidesOrientation'], 
		'transitionType' => $settings['transitionType'], 
		'transitionSpeed' => $settings['transitionSpeed']
	));

	return json_encode( $rs_settings );
}