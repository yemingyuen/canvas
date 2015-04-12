<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Hi there!  I\'m just a plugin, not much I can do when called directly.' );
}
/**
 * Youxi Likes Class
 *
 * This class is a helper class to add `Like This` functionality to posts
 *
 * @package   Youxi Core
 * @author    Mairel Theafila <maimairel@gmail.com>
 * @copyright Copyright (c) 2013-2015, Mairel Theafila
 */
if( ! class_exists( 'Youxi_Likes' ) ) {

	class Youxi_Likes {

		public static function get_the_link_attributes( $post_id, $attr = array() ) {

			$classes = array( 'youxi-likes-button' );

			if( self::is_liked( $post_id ) ) {
				$classes[] = 'youxi-likes-liked';
			}
			if( isset( $attr['class'] ) && is_string( $attr['class'] ) ) {
				$classes[] = $attr['class'];
			}

			$attr['class'] = implode( ' ', $classes );

			$html = '';

			foreach( array_merge( (array) $attr, array(
				'data-likes-post-id' => $post_id, 
				'data-likes-ajax-url' => admin_url( 'admin-ajax.php' ), 
				'data-likes-ajax-action' => 'add_likes', 
				'data-likes-ajax-nonce' => wp_create_nonce( 'youxi_likes_nonce_' . $post_id )
			)) as $key => $val ) {
				$html .= " {$key}" . '="' . esc_attr( $val ) . '"';
			}

			return $html;
		}

		public static function the_link_attributes( $post_id, $attr = array() ) {
			echo self::get_the_link_attributes( $post_id, $attr );
		}

		public static function get_the_likes( $post_id ) {
			$likes_count = get_post_meta( $post_id, '_youxi_likes_count', true );
			if( empty( $likes_count ) || ! is_numeric( $likes_count ) ) {
				$likes_count = 0;
			}
			return $likes_count;
		}

		public static function the_likes( $post_id ) {
			echo self::get_the_likes( $post_id );
		}

		public static function get_the_feedback( $post_id ) {
			return '<span class="youxi-likes-count">' . self::get_the_likes( $post_id ) . '</span>';
		}

		public static function the_feedback( $post_id ) {
			echo self::get_the_feedback( $post_id );
		}

		public static function is_liked( $post_id ) {
			return isset( $_COOKIE[ 'wordpress_youxi_likes_' . $post_id ] ) && 
				'' !== get_post_meta( $post_id, '_youxi_likes_count', true );
		}

		public static function ajax_add_like() {

			if( ! isset( $_POST['post_id'] ) ) {
				exit;
			}

			$post_id = $_POST['post_id'];
			check_ajax_referer( 'youxi_likes_nonce_' . $post_id );
			$cookie_name = 'wordpress_youxi_likes_' . $post_id;

			if( ! self::is_liked( $post_id ) ) {

				if( self::add_like( $post_id ) ) {
					setcookie( $cookie_name, true, time() + self::expires_after(), SITECOOKIEPATH );
					wp_send_json_success( array( 'count' => self::get_the_likes( $post_id ) ) );
				}
			}

			exit;
		}

		public static function enqueue() {

			$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

			wp_enqueue_script(
				'youxi-likes', 
				YOUXI_CORE_URL . "frontend/assets/js/youxi.likes{$suffix}.js", 
				array( 'jquery' ), 
				YOUXI_CORE_VERSION, 
				true
			);
		}

		public static function expires_after() {
			return apply_filters( 'youxi_likes_expires_after', WEEK_IN_SECONDS );
		}

		public static function allowed_post_types() {
			return apply_filters( 'youxi_likes_allowed_post_type', array() );
		}

		private static function add_like( $post_id ) {

			if( ! in_array( get_post_type( $post_id ), self::allowed_post_types() ) ) {
				return false;
			}

			$likes_count = get_post_meta( $post_id, '_youxi_likes_count', true );
			if( empty( $likes_count ) || ! is_numeric( $likes_count ) ) {
				$likes_count = 0;
			}

			return update_post_meta( $post_id, '_youxi_likes_count', ++$likes_count );
		}
	}
}
add_action( 'wp_ajax_add_likes', array( 'Youxi_Likes', 'ajax_add_like' ) );
add_action( 'wp_ajax_nopriv_add_likes', array( 'Youxi_Likes', 'ajax_add_like' ) );
