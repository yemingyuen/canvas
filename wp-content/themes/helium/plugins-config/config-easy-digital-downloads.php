<?php

// Make sure the plugin is active
if( ! class_exists( 'Easy_Digital_Downloads' ) ) {
	add_filter( 'theme_page_templates', 'helium_remove_edd_page_template' );
	return;
}

/* ==========================================================================
	Remove Download Button
============================================================================= */

remove_action( 'edd_after_download_content', 'edd_append_purchase_link' );

/* ==========================================================================
	Remove Builtin Microdata
============================================================================= */

add_filter( 'edd_add_schema_microdata', '__return_false' );

/* ==========================================================================
	Quick fix for Easy Digital Downloads bug #2632 (EDD < 2.2)
============================================================================= */

function helium_edd_fix() {
	remove_action( 'template_redirect', 'edd_disable_woo_ssl_on_checkout', 9 );
}
if( version_compare( EDD_VERSION, '2.2', 'lt' ) ) {
	add_action( 'template_redirect', 'helium_edd_fix', 1 );
}

/* ==========================================================================
	Remove EDD Page Template
============================================================================= */

function helium_remove_edd_page_template( $templates ) {
	unset( $templates['archive-download.php'] );
	return $templates;
}

/* ==========================================================================
	Add metabox to pages
============================================================================= */

if( ! function_exists( 'helium_add_edd_metabox' ) ) {

	function helium_add_edd_metabox() {

		// Make sure the core plugin is active
		if( ! defined( 'YOUXI_CORE_VERSION' ) ) {
			return;
		}

		$metaboxes = array();

		$metaboxes['edd_grid_settings'] = array(

			'title' => __( 'Page Template: EDD Store', 'helium' ), 

			'page_template' => 'archive-download', 

			'fields' => array(
				'use_defaults' => array(
					'type' => 'switch', 
					'label' => __( 'Use Default Settings', 'helium' ), 
					'description' => __( 'Switch to use the default Easy Digital Downloads grid settings.', 'helium' ), 
					'std' => false
				), 
				'show_filter' => array(
					'type' => 'switch', 
					'label' => __( 'Show Filter', 'helium' ), 
					'description' => __( 'Switch to display the downloads filter.', 'helium' ), 
					'criteria' => 'use_defaults:is(0)', 
					'std' => true
				), 
				'pagination' => array(
					'type' => 'select', 
					'label' => __( 'Pagination Type', 'helium' ), 
					'description' => __( 'Specify the downloads pagination type.', 'helium' ), 
					'choices' => array(
						'ajax' => __( 'AJAX', 'helium' ), 
						'infinite' => __( 'Infinite', 'helium' ), 
						'numbered' => __( 'Numbered', 'helium' ), 
						'prev_next' => __( 'Prev/Next', 'helium' ), 
						'show_all' => __( 'None (Show all)', 'helium' )
					), 
					'criteria' => 'use_defaults:is(0)', 
					'std' => 'ajax'
				), 
				'ajax_button_text' => array(
					'type' => 'text', 
					'label' => __( 'AJAX Button Text', 'helium' ), 
					'description' => __( 'Specify the text to display on the AJAX load more button.', 'helium' ), 
					'std' => 'Load More', 
					'criteria' => 'pagination:is(ajax),use_defaults:is(0)'
				), 
				'ajax_button_complete_text' => array(
					'type' => 'text', 
					'label' => __( 'AJAX Button Complete Text', 'helium' ), 
					'description' => __( 'Specify the text to display on the AJAX load more button when there are no more items to load.', 'helium' ), 
					'std' => 'No More Items', 
					'criteria' => 'pagination:is(ajax),use_defaults:is(0)'
				), 
				'posts_per_page' => array(
					'type' => 'uislider', 
					'label' => __( 'Posts Per Page', 'helium' ), 
					'description' => __( 'Specify how many download items to show per page.', 'helium' ), 
					'widgetopts' => array(
						'min' => 1, 
						'max' => 20, 
						'step' => 1
					), 
					'criteria' => 'use_defaults:is(0)', 
					'std' => 10
				), 
				'include' => array(
					'type' => 'checkboxlist', 
					'label' => __( 'Included Categories', 'helium' ), 
					'description' => __( 'Specify the download categories to include (leave unchecked to include all).', 'helium' ), 
					'choices' => get_terms( 'download_category', array( 'fields' => 'id=>name', 'hide_empty' => false ) ), 
					'criteria' => 'use_defaults:is(0)'
				), 
				'behavior' => array(
					'type' => 'select', 
					'label' => __( 'Behavior', 'helium' ), 
					'description' => __( 'Specify the behavior when clicking the thumbnail image.', 'helium' ), 
					'choices' => array(
						'none' => __( 'None', 'helium' ), 
						'lightbox' => __( 'Show Image in Lightbox', 'helium' ), 
						'page' => __( 'Go to Detail Page', 'helium' )
					), 
					'criteria' => 'use_defaults:is(0)'
				), 
				'columns' => array(
					'type' => 'uislider', 
					'label' => __( 'Columns', 'helium' ), 
					'description' => __( 'Specify in how many columns the items should be displayed.', 'helium' ), 
					'widgetopts' => array(
						'min' => 3, 
						'max' => 5, 
						'step' => 1
					), 
					'criteria' => 'use_defaults:is(0)', 
					'std' => 4
				)
			)
		);

		/* Create the 'page' post type object */
		$post_type_object = Youxi_Post_Type::get( 'page' );

		/* Add the metaboxes */
		foreach( $metaboxes as $metabox_id => $metabox ) {
			$post_type_object->add_meta_box( new Youxi_Metabox( $metabox_id, $metabox ) );
		}
	}
}
add_action( 'init', 'helium_add_edd_metabox' );

/* ==========================================================================
	JS Config Vars
============================================================================= */

function edd_helium_js_vars( $vars ) {
	return array_merge( $vars, array(
		'edd' => array(
			'ajaxDisabled' => edd_is_ajax_disabled(), 
			'straightToCheckout' => edd_straight_to_checkout(), 
			'checkoutPage' => edd_get_checkout_uri()
		)
	));
}
add_filter( 'helium_js_vars', 'edd_helium_js_vars' );

/* ==========================================================================
	EDD Button Colors
============================================================================= */

function helium_edd_button_colors( $button_colors ) {
	return array(
		'turquoise'   => array(
			'label' => __( 'Turquoise', 'edd' ),
			'hex'   => '#3dc9b3'
		),
		'white'   => array(
			'label' => __( 'White', 'edd' ),
			'hex'   => '#fff'
		),
		'blue'      => array(
			'label' => __( 'Blue', 'edd' ),
			'hex'   => '#428bca'
		),
		'red'       => array(
			'label' => __( 'Red', 'edd' ),
			'hex'   => '#d9534f'
		),
		'green'     => array(
			'label' => __( 'Green', 'edd' ),
			'hex'   => '#5cb85c'
		),
		'orange'    => array(
			'label' => __( 'Orange', 'edd' ),
			'hex'   => '#f0ad4e'
		),
		'dark-gray' => array(
			'label' => __( 'Dark Gray', 'edd' ),
			'hex'   => '#363636'
		)
	);
}
add_filter( 'edd_button_colors', 'helium_edd_button_colors' );