<?php
/*
Plugin Name: Youxi Widgets
Plugin URI: http://www.themeforest.net/user/nagaemas
Description: This plugin provides a set of commonly used widgets whose output can be dynamically customized with templates and filters. The widgets are also able to detect the widget area it's located on, making it possible to output different layouts on different widget areas.
Version: 1.2.6
Author: YouxiThemes
Author URI: http://www.themeforest.net/user/nagaemas
License: Envato Marketplace Licence

Changelog:
1.2.6 - 10/03/2015
- Fix a bug on the instagram widget that causes multiple widgets on the same page to break

1.2.5 - 31/01/2015
- Added image size option for instagram widget
- Make sure instagram widget pulls images through the same HTTP protocol

1.2.4 - 16/12/2014
- Added `Open links in a new window/tab` option on Social Widget
- Refactor Social Widget
- Tidy up styling of Social Widget form
- Ensure all widget forms get passed through `esc_attr`

1.2.3 - 14/12/2014
- Fixed a bug that prevents adding a dot (.) in instagram widget username

1.2.2 - 04/12/2014
- Added a filter for custom orderby queries on the Posts widget
- Added option to select which post meta to show on the Posts widget

1.2.1 - 19/11/2014
- Fixed a bug causing widgets not to load the setup script if enqueue_scripts is false

1.2 - 07/11/2014
- Added `youxi_widgets_recognized_social_icons` filter to modify the available social icons
- Added `tag__not_in` option for the recent posts widget
- Introduce Posts Widget which allows advanced post queries
- Introduce Instagram Widget
- Rename Youxi_Tweets_Widget to Youxi_Twitter_Widget
- Enhance frontend widgets setup script to allow configuration passing
- Updated OAuth library to the latest version to prevent conflicts with PHP OAuth module
- Deprecate video widget, use default WordPress text widget instead
- Make sure all widgets are WPML compatible

1.1.1 - 14/08/2014
- Replace GMAP3 plugin files with smaller custom build files
- Minor code optimizations
- Added LESS file for social icons

1.1 - 02/07/2014
- External scripts enqueing filtered through `youxi_widgets_{$id_base}_enqueue_scripts`
- Widget scripts initialization handling via `youxi_widgets_allow_{$id_base}_setup`
- Replaced jQuery.tweet.js with jQuery.miniTweets.js
- Removed `youxi_widgets_enqueue_scripts` filter
- Added `Categories to Exclude` on Recent Posts widget

1.0.3 - 31/03/2014
- Changed equalHeight to true for rotating quotes widget
- Make sure TwitterOAuth and OAuth utils doesn't conflict with other plugins
- Added `youxi_widgets_enqueue_scripts` filter that can be used to prevent enqueuing default assets
- Added `suppress_filters` to recent posts widget for WPML compatibility

1.0.1 - 19/10/2013
- Added monochrome option to Google Maps

1.0
- Initial release
*/

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Hi there!  I\'m just a plugin, not much I can do when called directly.' );
}

define( 'YOUXI_WIDGETS_VERSION', '1.2.6' );

define( 'YOUXI_WIDGETS_DIR', plugin_dir_path( __FILE__ ) );

define( 'YOUXI_WIDGETS_URL', plugin_dir_url( __FILE__ ) );

define( 'YOUXI_WIDGETS_LANG_DIR', dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

if( is_admin() ) {
	require( YOUXI_WIDGETS_DIR . 'admin/admin.php' );
}

function youxi_widgets_init() {

	require( YOUXI_WIDGETS_DIR . 'class-widget-base.php' );

	/* WPML compatibility */
	if( function_exists( 'icl_register_string' ) ) {
		require( YOUXI_WIDGETS_DIR . 'admin/wpml.php' );
	}

	if( apply_filters( 'youxi_widgets_use_flickr', true ) ) {
		if( ! class_exists( 'Youxi_Flickr_Widget' ) ) {
			require( YOUXI_WIDGETS_DIR . 'class-widget-flickr.php' );
		}
		register_widget( 'Youxi_Flickr_Widget' );
	}

	if( apply_filters( 'youxi_widgets_use_google_map', true ) ) {
		if( ! class_exists( 'Youxi_Google_Maps_Widget' ) ) {
			require( YOUXI_WIDGETS_DIR . 'class-widget-gmap.php' );
		}
		register_widget( 'Youxi_Google_Maps_Widget' );
	}

	if( apply_filters( 'youxi_widgets_use_instagram', true ) ) {
		if( ! class_exists( 'Youxi_Instagram_Widget' ) ) {
			require( YOUXI_WIDGETS_DIR . 'class-widget-instagram.php' );
		}
		register_widget( 'Youxi_Instagram_Widget' );
	}

	if( apply_filters( 'youxi_widgets_use_posts', true ) ) {
		if( ! class_exists( 'Youxi_Posts_Widget' ) ) {
			require( YOUXI_WIDGETS_DIR . 'class-widget-posts.php' );
		}
		register_widget( 'Youxi_Posts_Widget' );
	}

	if( apply_filters( 'youxi_widgets_use_quote', true ) ) {
		if( ! class_exists( 'Youxi_Quote_Widget' ) ) {
			require( YOUXI_WIDGETS_DIR . 'class-widget-quote.php' );
		}
		register_widget( 'Youxi_Quote_Widget' );
	}

	if( apply_filters( 'youxi_widgets_use_recent_posts', true ) ) {
		if( ! class_exists( 'Youxi_Recent_Posts_Widget' ) ) {
			require( YOUXI_WIDGETS_DIR . 'class-widget-recent-posts.php' );
		}
		register_widget( 'Youxi_Recent_Posts_Widget' );
	}

	if( apply_filters( 'youxi_widgets_use_rotating_quotes', true ) ) {
		if( ! class_exists( 'Youxi_Rotating_Quotes_Widget' ) ) {
			require( YOUXI_WIDGETS_DIR . 'class-widget-rotating-quotes.php' );
		}
		register_widget( 'Youxi_Rotating_Quotes_Widget' );
	}

	if( apply_filters( 'youxi_widgets_use_social', true ) ) {
		if( ! class_exists( 'Youxi_Social_Widget' ) ) {
			require( YOUXI_WIDGETS_DIR . 'class-widget-social.php' );
		}
		register_widget( 'Youxi_Social_Widget' );
	}

	if( apply_filters( 'youxi_widgets_use_twitter', true ) ) {
		if( ! class_exists( 'Youxi_Twitter_Widget' ) ) {
			require( YOUXI_WIDGETS_DIR . 'class-widget-twitter.php' );
		}
		register_widget( 'Youxi_Twitter_Widget' );
	}

	if( apply_filters( 'youxi_widgets_use_video', true ) ) {
		if( ! class_exists( 'Youxi_Video_Widget' ) ) {
			require( YOUXI_WIDGETS_DIR . 'class-widget-video.php' );
		}
		register_widget( 'Youxi_Video_Widget' );
	}
}
add_action( 'widgets_init', 'youxi_widgets_init' );

function youxi_widgets_plugins_loaded() {

	/* Load Language File */
	load_plugin_textdomain( 'youxi', false, YOUXI_WIDGETS_LANG_DIR );
}
add_action( 'plugins_loaded', 'youxi_widgets_plugins_loaded' );
