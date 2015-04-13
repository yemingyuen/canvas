<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Hi there!  I\'m just a plugin, not much I can do when called directly.' );
}

if( ! class_exists( 'Youxi_Gallery' ) ) {

	class Youxi_Gallery {

		private static $__registered = false;

		/* Register gallery post type */
		public static function register() {

			/* Make sure we're allowed to register the gallery */
			if( ! apply_filters( 'youxi_portfolio_use_gallery', true ) || self::$__registered ) {
				return;
			}
			self::$__registered = true;

			/* Get the post type settings */
			$settings = wp_parse_args( self::post_type_args(), array(
				'args' => array(), 
				'labels' => array(), 
				'metaboxes' => array(), 
				'taxonomies' => array()
			));

			extract( $settings, EXTR_SKIP );

			/* Merge the labels into the args */
			$args = array_merge( $args, compact( 'labels' ) );

			/* Create the post type object */
			$post_type_object = Youxi_Post_Type::get( self::post_type_name(), $args );

			/* Add the metaboxes */
			foreach( $metaboxes as $metabox_id => $metabox ) {
				$post_type_object->add_meta_box( new Youxi_Metabox( $metabox_id, $metabox ) );
			}

			/* Add the taxonomies */
			foreach( $taxonomies  as $tax_id => $taxonomy ) {
				$post_type_object->add_taxonomy( new Youxi_Taxonomy( $tax_id, $taxonomy ) );
			}

			if( is_admin() ) {

				/* Attach post type ordering page */
				$ordering_page = new Youxi_Post_Order_Page(
					__( 'Order Galleries', 'youxi' ), 
					__( 'Order Galleries', 'youxi' ), 
					'youxi-gallery-order-page'
				);
				$post_type_object->add_submenu_page( $ordering_page );
			}

			/* Register the post type */
			$post_type_object->register();
		}

		/* The post type name for gallery */
		public static function post_type_name() {
			return apply_filters( 'youxi_gallery_post_type_name', 'gallery' );
		}

		/* The default taxonomy name for gallery */
		public static function taxonomy_name() {
			return apply_filters( 'youxi_gallery_taxonomy_name', 'gallery-category' );
		}

		/* The one page post type arguments */
		public static function post_type_args() {

			$taxonomies = array();
			$taxonomies[ self::taxonomy_name() ] = array(
				'labels' => array(
					'name'                       => __( 'Gallery Categories', 'youxi' ), 
					'singular_name'              => __( 'Gallery Category', 'youxi' ), 
					'all_items'                  => __( 'All Gallery Categories', 'youxi' ), 
					'edit_item'                  => __( 'Edit Gallery Category', 'youxi' ), 
					'view_item'                  => __( 'View Gallery Category', 'youxi' ), 
					'update_item'                => __( 'Update Gallery Category', 'youxi' ), 
					'add_new_item'               => __( 'Add New Gallery Category', 'youxi' ), 
					'new_item_name'              => __( 'New Gallery Category Name', 'youxi' ), 
					'parent_item'                => __( 'Parent Gallery Category', 'youxi' ), 
					'parent_item_colon'          => __( 'Parent Gallery Category: ', 'youxi' ), 
					'search_items'               => __( 'Search Gallery Categories', 'youxi' ), 
					'popular_items'              => __( 'Popular Gallery Categories', 'youxi' ), 
					'separate_items_with_commas' => __( 'Separate gallery categories with commas', 'youxi' ), 
					'add_or_remove_items'        => __( 'Add or remove gallery categories', 'youxi' ), 
					'choose_from_most_used'      => __( 'Choose from the most used gallery categories', 'youxi' ), 
					'not_found'                  => __( 'No gallery categories found.', 'youxi' )
				), 
				'show_tagcloud' => false, 
				'show_admin_column' => true, 
				'show_in_nav_menus' => false
			);

			/* Return the settings for the gallery cpt */
			return array(

				'args' => apply_filters( 'youxi_gallery_cpt_args', array(
					'description' => __( 'This post type is used to save your gallery.', 'youxi' ), 
					'capability_type' => 'post', 
					'public' => true, 
					'menu_icon' => 'dashicons-format-gallery', 
					'has_archive' => true, 
					'show_in_nav_menus' => true, 
					'supports' => array( 'title', 'editor', 'thumbnail', 'page-attributes' )
				) ), 

				'labels' => apply_filters( 'youxi_gallery_cpt_labels', array(
					'name'               => __( 'Galleries', 'youxi' ), 
					'singular_name'      => __( 'Gallery', 'youxi' ), 
					'all_items'          => __( 'All Galleries', 'youxi' ), 
					'add_new'            => __( 'Add New Gallery', 'youxi' ),
					'add_new_item'       => __( 'Add New Gallery', 'youxi' ),
					'edit_item'          => __( 'Edit Gallery', 'youxi' ),
					'view_item'          => __( 'View Gallery', 'youxi' ),
					'search_items'       => __( 'Search Gallery', 'youxi' ),
					'not_found'          => __( 'Gallery not found', 'youxi' ),
					'not_found_in_trash' => __( 'Gallery not found in trash', 'youxi' ),
					'parent_item_colon'  => __( 'Gallery: ', 'youxi' )
				) ), 

				'metaboxes' => apply_filters( 'youxi_gallery_cpt_metaboxes', array() ), 

				'taxonomies' => apply_filters( 'youxi_gallery_cpt_taxonomies', $taxonomies )
			);
		}
	}
}

add_action( 'init', array( 'Youxi_Gallery', 'register' ) );
