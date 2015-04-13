<?php

/* ==========================================================================
	Demo Content Importer
============================================================================= */

if( ! function_exists( 'helium_demo_content' ) ):

function helium_demo_content( $demo ) {

	return array_merge( $demo, array(
		'default' => array(
			'screenshot' => get_template_directory_uri() . '/screenshot.png', 
			'name' => __( 'Default', 'helium' ), 
			'content' => array(
				'xml' => get_template_directory() . '/demo/helium.wordpress.2014-11-08.xml', 
				'widgets' => '{"header_widget_area":{"text-2":{"title":"About","text":"We\u2019re Helium, a web design agency. We love design and we try to make the web a better place.","filter":false},"social-widget-2":{"title":"We\'re Social","items":[{"url":"#","title":"Facebook","icon":"facebook"},{"url":"#","title":"Twitter","icon":"twitter"},{"url":"#","title":"Google+","icon":"googleplus"},{"url":"#","title":"Pinterest","icon":"pinterest"},{"url":"#","title":"RSS","icon":"rss"}]},"flickr-widget-2":{"title":"My Flickr Feed","flickr_id":"","limit":8},"instagram-widget-2":{"title":"Instagram Feed","username":"judesi_lau","count":8},"twitter-widget-2":{"title":"Recent Tweets","username":"envato","count":2}}}', 
				'theme-options' => '', 
				'frontpage_displays' => array(
					'show_on_front'  => 'page', 
					'page_on_front'  => 1411, 
					'page_for_posts' => 845
				), 
				'nav_menu_locations' => array(
					'main-menu' => 'the-menu'
				)
			)
		)
	));
}
endif;

if( apply_filters( 'helium_show_demo_importer', true ) ) {

	if( is_readable( get_template_directory() . '/lib/importer/class-importer.php' ) ) {
		
		require( get_template_directory() . '/lib/importer/class-importer.php' );
		add_filter( 'youxi_demo_importer_demos', 'helium_demo_content' );
	}
}
