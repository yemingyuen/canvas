<?php

// Make sure the plugin is active
if( ! defined( 'YOUXI_WIDGETS_VERSION' ) ) {
	return;
}

/* ==========================================================================
	Youxi Widgets plugin config
============================================================================= */

/**
 * Disable Enqueuing Scripts
 */
add_filter( 'youxi_widgets_social-widget_enqueue_scripts', '__return_false' );
add_filter( 'youxi_widgets_google-maps-widget_enqueue_scripts', '__return_false' );
add_filter( 'youxi_widgets_flickr-widget_enqueue_scripts', '__return_false' );
add_filter( 'youxi_widgets_allow_google-maps-widget_setup', '__return_false' );

/**
 * Disable Widgets temporarily
 */
add_filter( 'youxi_widgets_use_recent_posts', '__return_false' );
add_filter( 'youxi_widgets_use_video', '__return_false' );
add_filter( 'youxi_widgets_use_quote', '__return_false' );
add_filter( 'youxi_widgets_use_rotating_quotes', '__return_false' );

/**
 * Fetch Twitter Keys from Theme Options
 */
if( ! function_exists( 'helium_widgets_twitter_keys' ) ):

function helium_widgets_twitter_keys( $keys ) {
	if( function_exists( 'helium_get_option' ) ) {
		return array(
			'consumer_key'       => helium_get_option( 'twitter_consumer_key' ), 
			'consumer_secret'    => helium_get_option( 'twitter_consumer_secret' ), 
			'oauth_token'        => helium_get_option( 'twitter_access_token' ), 
			'oauth_token_secret' => helium_get_option( 'twitter_access_token_secret' )
		);
	}

	return $keys;
}
endif;
add_filter( 'youxi_widgets_twitter_keys', 'helium_widgets_twitter_keys' );

/**
 * Set Widget Templates Directory
 */
if( ! function_exists( 'helium_widgets_template_dir' ) ):

function helium_widgets_template_dir( $path ) {
	return trailingslashit( 'widget-templates' );
}
endif;
add_filter( 'youxi_widgets_template_dir', 'helium_widgets_template_dir' );

/**
 * Match Widget Area Locations
 */
if( ! function_exists( 'helium_widget_sidebar_location' ) ):

	function helium_widget_sidebar_location( $sidebar_id ) {
		$regexes = array(
			'/^footer_widget_area_\d+$/' => 'footer'
		);

		foreach( $regexes as $regex => $location ) {
			if( preg_match( $regex, $sidebar_id ) ) {
				return $location;
			}
		}

		return 'sidebar';
	}
endif;
add_filter( 'youxi_widgets_sidebar_location', 'helium_widget_sidebar_location' );

/**
 * Recognized Social Icons
 */
if( ! function_exists( 'helium_youxi_widgets_social_icons' ) ):

function helium_youxi_widgets_social_icons( $icons ) {

	return array(
		'500px' => '500px', 
		'6' => '6', 
		'apple' => 'apple', 
		'bebo' => 'bebo', 
		'behance' => 'behance', 
		'blogger' => 'blogger', 
		'buffer' => 'buffer', 
		'chimein' => 'chimein', 
		'coderwall' => 'coderwall', 
		'dailymotion' => 'dailymotion', 
		'delicious' => 'delicious', 
		'deviantart' => 'deviantart', 
		'digg' => 'digg', 
		'disqus' => 'disqus', 
		'dribbble' => 'dribbble', 
		'envato' => 'envato', 
		'facebook' => 'facebook', 
		'feedburner' => 'feedburner', 
		'flattr' => 'flattr', 
		'flickr' => 'flickr', 
		'forrst' => 'forrst', 
		'foursquare' => 'foursquare', 
		'friendfeed' => 'friendfeed', 
		'github' => 'github', 
		'googleplus' => 'googleplus', 
		'grooveshark' => 'grooveshark', 
		'identica' => 'identica', 
		'instagram' => 'instagram', 
		'lanyrd' => 'lanyrd', 
		'lastfm' => 'lastfm', 
		'linkedin' => 'linkedin', 
		'myspace' => 'myspace', 
		'netcodes' => 'netcodes', 
		'newsvine' => 'newsvine', 
		'outlook' => 'outlook', 
		'pinterest' => 'pinterest', 
		'playstore' => 'playstore', 
		'reddit' => 'reddit', 
		'rss' => 'rss', 
		'skype' => 'skype', 
		'slideshare' => 'slideshare', 
		'soundcloud' => 'soundcloud', 
		'spotify' => 'spotify', 
		'steam' => 'steam', 
		'stumbleupon' => 'stumbleupon', 
		'technorati' => 'technorati', 
		'tripadvisor' => 'tripadvisor', 
		'tumblr' => 'tumblr', 
		'twitter' => 'twitter', 
		'viadeo' => 'viadeo', 
		'vimeo' => 'vimeo', 
		'vine' => 'vine', 
		'vkontakte' => 'vkontakte', 
		'wikipedia' => 'wikipedia', 
		'windows' => 'windows', 
		'wordpress' => 'wordpress', 
		'xbox' => 'xbox', 
		'xing' => 'xing', 
		'yahoo' => 'yahoo', 
		'yelp' => 'yelp', 
		'youtube' => 'youtube', 
		'zerply' => 'zerply', 
		'zynga' => 'zynga'
	);
}
endif;
add_filter( 'youxi_widgets_recognized_social_icons', 'helium_youxi_widgets_social_icons' );