<?php

/* ==========================================================================
	Text Domain
============================================================================= */

if( ! function_exists( 'helium_load_theme_textdomain' ) ):

function helium_load_theme_textdomain() {
	load_theme_textdomain( 'helium', get_template_directory() . '/languages' );
}
add_action( 'after_setup_theme', 'helium_load_theme_textdomain' );
endif;

/* ==========================================================================
	Theme Support
============================================================================= */

if( ! function_exists( 'helium_add_theme_support' ) ):

function helium_add_theme_support() {

	/*
	 * Enable support for Post Formats.
	 * See http://codex.wordpress.org/Post_Formats
	 */
	add_theme_support( 'post-formats', array( 'image', 'video', 'audio', 'gallery' ) );

	/*
	 * Let WordPress manage the document title.
	 * By adding theme support, we declare that this theme does not use a
	 * hard-coded <title> tag in the document head, and expect WordPress to
	 * provide it for us.
	 */
	add_theme_support( 'title-tag' );

	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption' ) );

	// Add RSS feed links to <head> for posts and comments.
	add_theme_support( 'automatic-feed-links' );

	// Enable support for Post Thumbnails
	add_theme_support( 'post-thumbnails' );

	// This theme uses its own gallery styles.
	add_filter( 'use_default_gallery_style', '__return_false' );
}
endif;
add_action( 'init', 'helium_add_theme_support' );

/* ==========================================================================
	Theme Compatibility
============================================================================= */

if( ! function_exists( '_wp_render_title_tag' ) ):
	function helium_render_title() {
		echo '<title>' . wp_title( '|', false, 'right' ) . "</title>" . PHP_EOL;
	}
	add_action( 'wp_head', 'helium_render_title' );
endif;

/* ==========================================================================
	Image Sizes
============================================================================= */

if( ! function_exists( 'helium_add_image_sizes' ) ):

function helium_add_image_sizes() {

	$image_sizes = apply_filters( 'helium_wp_image_sizes', array(
		'helium_square' => array(
			'width'  => 640, 
			'height' => 640, 
			'crop'   => true
		), 
		'helium_4by3' => array(
			'width'  => 400, 
			'height' => 300, 
			'crop'   => true
		), 
		'helium_16by9' => array(
			'width'  => 800, 
			'height' => 450, 
			'crop'   => true
		)
	));

	foreach( $image_sizes as $name => $size ) {

		/* Skip reserved names */
		if( preg_match( '/^((post-)?thumbnail|thumb|medium|large)$/', $name ) ) {
			continue;
		}

		$size = wp_parse_args( $size, array(
			'width'  => 0, 
			'height' => 0, 
			'crop'   => false
		));
		add_image_size( $name, $size['width'], $size['height'], $size['crop'] );
	}
}
endif;
add_action( 'init', 'helium_add_image_sizes' );

/* ==========================================================================
	Image Size Names
============================================================================= */

if( ! function_exists( 'helium_image_size_names' ) ):

function helium_image_size_names( $names ) {

	$image_sizes = apply_filters( 'helium_wp_image_sizes', array() );
	foreach( $image_sizes as $name => $size ) {

		/* Skip reserved names */
		if( preg_match( '/^((post-)?thumbnail|thumb|medium|large)$/', $name ) ) {
			continue;
		}

		if( isset( $size['label'] ) && '' != $size['label'] ) {
			$names[ $name ] = $size['label'];
		}
	}

	return $names;
}
endif;
add_filter( 'image_size_names_choose', 'helium_image_size_names' );

/* ==========================================================================
	Nav Menus
============================================================================= */

if( ! function_exists( 'helium_register_nav_menus' ) ):

function helium_register_nav_menus() {

	$nav_menus = array(
		'main-menu' => __( 'Main Menu', 'helium' )
	);
	register_nav_menus( $nav_menus );
}
endif;
add_action( 'init', 'helium_register_nav_menus' );

/* ==========================================================================
	Body Class
============================================================================= */

if( ! function_exists( 'helium_body_class' ) ):

function helium_body_class( $classes ) {
	return $classes;
}
endif;
add_filter( 'body_class', 'helium_body_class' );

/* ==========================================================================
	Widgets
============================================================================= */

if( ! function_exists( 'helium_widgets_init' ) ):

function helium_widgets_init() {

	register_sidebar(array(
		'name' => __( 'Header Widget Area', 'helium' ), 
		'id' => 'header_widget_area', 
		'description' => __( 'This is the header widget area.', 'helium' ), 
		'before_widget' => '<div class="widget">', 
		'after_widget' => '</div>', 
		'before_title' => '<h4 class="widget-title">', 
		'after_title' => '</h4>'
	));
}
endif;
add_action( 'widgets_init', 'helium_widgets_init' );

/* ==========================================================================
	Other WP Filters
============================================================================= */

/**
 * Deregister Default WordPress MEJS Styles
 */
if( ! function_exists( 'helium_wp_mediaelement' ) ):

function helium_wp_mediaelement() {

	/* Dequeue default wp mediaelement style */
	wp_deregister_style( 'mediaelement' );
	wp_deregister_style( 'wp-mediaelement' );
}
endif;
add_action( 'wp_enqueue_scripts', 'helium_wp_mediaelement' );

/* ==========================================================================
	Filter `wp_title` to fix empty titles on static front page
============================================================================= */

if( ! function_exists( 'helium_wp_title' ) ):

function helium_wp_title( $title ) {

	if( empty( $title ) && ( is_home() || is_front_page() ) ) {
		return get_bloginfo( 'name' ) . ' | ' . get_bloginfo( 'description' );
	}

	return $title;
}
endif;
add_filter( 'wp_title', 'helium_wp_title' );

/* ==========================================================================
	User Social Profiles
============================================================================= */

if( ! function_exists( 'helium_user_social_profiles' ) ):

function helium_user_social_profiles() {
	return array(
		'twitter'     => __( 'Twitter', 'helium' ), 
		'facebook'    => __( 'Facebook', 'helium' ), 
		'googleplus'  => __( 'Google+', 'helium' ), 
		'pinterest'   => __( 'Pinterest', 'helium' ), 
		'linkedin'    => __( 'LinkedIn', 'helium' ), 
		'youtube'     => __( 'YouTube', 'helium' ), 
		'vimeo'       => __( 'Vimeo', 'helium' ), 
		'tumblr'      => __( 'tumblr', 'helium' ), 
		'instagram'   => __( 'Instagram', 'helium' ), 
		'flickr'      => __( 'Flickr', 'helium' ), 
		'dribbble'    => __( 'dribbble', 'helium' ), 
		'foursquare'  => __( 'Foursquare', 'helium' ), 
		'forrst'      => __( 'Forrst', 'helium' ), 
		'vkontakte'   => __( 'VKontakte', 'helium' ), 
		'wordpress'   => __( 'WordPress', 'helium' ), 
		'stumbleupon' => __( 'StumbleUpon', 'helium' ), 
		'yahoo'       => __( 'Yahoo!', 'helium' ), 
		'blogger'     => __( 'Blogger', 'helium' ), 
		'soundcloud'  => __( 'SoundCloud', 'helium' )
	);
}
endif;

/**
 * User Contact Methods
 */
if( ! function_exists( 'helium_user_contactmethods' ) ):

function helium_user_contactmethods( $methods ) {
	return array_merge( $methods, helium_user_social_profiles() );
}
endif;
add_filter( 'user_contactmethods', 'helium_user_contactmethods' );

/* ==========================================================================
	Modify Stylesheet URI
============================================================================= */

if( ! function_exists( 'helium_stylesheet_uri' ) ):

function helium_stylesheet_uri( $stylesheet_uri, $stylesheet_dir_uri ) {

	if( ! is_child_theme() ) {
		if( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
			return $stylesheet_dir_uri . "/assets/css/helium.css";
		}
		return $stylesheet_dir_uri . "/assets/css/helium.min.css";
	}

	return $stylesheet_uri;	
}
endif;
add_filter( 'stylesheet_uri', 'helium_stylesheet_uri', 10, 2 );

/* ==========================================================================
	Favicon
============================================================================= */

function helium_add_favicon() {
	if( $favicon = helium_get_option( 'favicon' ) ) {
		echo '<link rel="shortcut icon" href="' . esc_url( $favicon ) . '">' . PHP_EOL;
	}
}
add_action( 'wp_head', 'helium_add_favicon' );

/* ==========================================================================
	Scripts and Styles
============================================================================= */

if( ! function_exists( 'helium_wp_enqueue_script' ) ):

function helium_wp_enqueue_script() {
	
	/* Get theme version */
	$wp_theme = wp_get_theme();
	$theme_version = $wp_theme->exists() ? $wp_theme->get( 'Version' ) : false;

	/* Get script debug status */
	$script_debug = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG;
	$suffix = $script_debug ? '' : '.min';

	if( function_exists( 'is_customize_preview' ) ) {
		$is_customize_preview = is_customize_preview();
	} else {
		global $wp_customize;
		$is_customize_preview = is_a( $wp_customize, 'WP_Customize_Manager' ) && $wp_customize->is_preview();
	}

	$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

	/* Parse Google Fonts */
	$font_settings = Youxi_Google_Font::parse( helium_font_options(), helium_default_font_options() );

	/* Register Core Styles */
	wp_register_style( 'bootstrap', get_template_directory_uri() . "/assets/bootstrap/css/bootstrap{$suffix}.css", array(), '3.3.4', 'screen' );
	wp_register_style( 'helium', get_stylesheet_uri(), array( 'bootstrap' ), $theme_version, 'screen' );

	wp_register_style( 'google-fonts', Youxi_Google_Font::request_url( $font_settings['families'], $font_settings['subsets'] ), array(), $theme_version, 'screen' );

	/* Register Icons */
	wp_register_style( 'fontawesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css', array(), '4.3.0', 'screen' );

	/* Register Plugin Styles */
	wp_register_style( 'magnific-popup', get_template_directory_uri() . "/assets/plugins/mfp/mfp.css", array(), '1.0.0', 'screen' );
	wp_register_style( 'royalslider', get_template_directory_uri() . "/assets/plugins/royalslider/royalslider{$suffix}.css", array(), '1.0.5', 'screen' );

	/* Enqueue Icons */
	wp_enqueue_style( 'google-fonts' );
	wp_enqueue_style( 'fontawesome' );

	/* Enqueue Core Styles */
	wp_enqueue_style( 'helium' );	

	/* Make sure the LESS compiler exists */
	if( ! class_exists( 'Youxi_LESS_Compiler' ) ) {
		require( get_template_directory() . '/lib/framework/class-less-compiler.php' );
	}
	$less_compiler = Youxi_LESS_Compiler::get();

	/* Get the accent color setting */
	$brand_primary = helium_get_option( 'accent_color', helium_default_accent_color() );

	/* Custom accent color styles */
	if( helium_default_accent_color() !== $brand_primary ) {
		wp_add_inline_style( 'bootstrap', $less_compiler->compile( '/assets/less/mods/bootstrap.less', array( 'bs-override' => array( 'brand-primary' => $brand_primary ) ) ) );
	}

	/* Custom theme styles */
	$header_logo_height = helium_get_option( 'logo_height' ) . 'px';

	wp_add_inline_style( 'helium', $less_compiler->compile( '/assets/less/mods/theme-options.less', array(
		'theme-options' => array_merge(
			array( 'logo-height' => $header_logo_height, 'brand-primary' => $brand_primary ), 
			helium_css_to_less_vars( $font_settings['css'] )
		)
	)));

	/* Custom user styles */
	wp_add_inline_style( 'helium', helium_get_option( 'custom_css' ) );

	if( $script_debug ) {
		wp_register_script( 'helium-plugins', get_template_directory_uri() . "/assets/js/helium.plugins.js", array( 'jquery' ), $theme_version, true );
		wp_register_script( 'helium-gridlist', get_template_directory_uri() . "/assets/js/helium.gridlist.js", array( 'jquery' ), $theme_version, true );
		wp_register_script( 'helium', get_template_directory_uri() . "/assets/js/helium.setup.js", array( 'jquery', 'helium-plugins', 'helium-gridlist' ), $theme_version, true );
	} else {
		wp_register_script( 'helium', get_template_directory_uri() . "/assets/js/helium.min.js", array( 'jquery' ), $theme_version, true );
	}

	/* Register plugin scripts */
	wp_register_script( 'magnific-popup', get_template_directory_uri() . "/assets/plugins/mfp/jquery.mfp-1.0.0{$suffix}.js", array( 'jquery' ), '1.0.0', true );
	wp_register_script( 'isotope', get_template_directory_uri() . "/assets/plugins/isotope/isotope.pkgd{$suffix}.js", array( 'jquery' ), '2.1.1', true );
	wp_register_script( 'royalslider', get_template_directory_uri() . "/assets/plugins/royalslider/jquery.royalslider-9.5.7.min.js", array( 'jquery' ), '9.5.7', true );
	wp_register_script( 'gmap3', get_template_directory_uri() . "/assets/plugins/gmap/gmap3{$suffix}.js", array( 'jquery' ), '6.0.0.', true );

	/* AddThis widget script */
	wp_register_script( 'addthis', 'http://s7.addthis.com/js/300/addthis_widget.js', array(), 300, true );

	/* Pass configuration to frontend */
	wp_localize_script( 'helium', '_helium', apply_filters( 'helium_js_vars', array(
		'ajaxUrl'         => admin_url( 'admin-ajax.php' ), 
		'homeUrl'         => home_url( '/' ), 
		'ajaxNavigation'  => helium_get_option( 'ajax_navigation' ) && ! $is_customize_preview ? array(
			'scrollTop'   => helium_get_option( 'ajax_navigation_scroll_top' ), 
			'loadingText' => helium_get_option( 'ajax_navigation_loading_text' ), 
			'excludeUrls' => array( includes_url(), content_url(), wp_login_url(), plugins_url(), admin_url() )
		) : false
	)));

	/* Enqueue core script */
	wp_enqueue_script( 'helium' );

	/* Enqueue plugins */
	wp_enqueue_script( 'isotope' );
	wp_enqueue_script( 'gmap3' );

	wp_enqueue_script( 'royalslider' );
	wp_enqueue_style( 'royalslider' );

	wp_enqueue_script( 'magnific-popup' );
	wp_enqueue_style( 'magnific-popup' );

	/* Enqueue wp-mediaelement if AJAX is enabled */
	if( helium_get_option( 'ajax_navigation' ) ) {
		wp_enqueue_script( 'wp-mediaelement' );
	}

	/* Enqueue AddThis script on blog pages */
	if( is_singular( array( 'post', 'portfolio', 'download' ) ) || helium_get_option( 'ajax_navigation' ) ) {

		$addthis_config = array( 'ui_delay' => 100 );
		if( $addthis_profile_id = helium_get_option( 'addthis_profile_id' ) ) {
			$addthis_config['pubid'] = $addthis_profile_id;
		}
		wp_enqueue_script( 'addthis' );
		wp_localize_script( 'addthis', 'addthis_config', $addthis_config );
	}

	/* Enqueue comment-reply */
	if( is_singular( 'post' ) && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
endif;
add_action( 'wp_enqueue_scripts', 'helium_wp_enqueue_script' );
