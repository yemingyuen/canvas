<?php

// Make sure the plugin is active
if( ! defined( 'YOUXI_CORE_VERSION' ) ) {
	return;
}

/* ==========================================================================
	Add metabox to pages
============================================================================= */

if( ! function_exists( 'helium_add_page_metabox' ) ) {

	function helium_add_page_metabox() {

		$metaboxes = array();

		/* Layout */
		$metaboxes['layout'] = array(
			'title' => __( 'Layout', 'helium' ), 
			'fields' => array(
				'page_layout' => array(
					'type' => 'select', 
					'label' => __( 'Page Layout', 'helium' ), 
					'description' => __( 'Specify the layout of the page (does not have any effect on custom page templates).', 'helium' ), 
					'choices' => array(
						'fullwidth' => __( 'Fullwidth', 'helium' ), 
						'boxed' => __( 'Boxed', 'helium' )
					), 
					'std' => 'boxed'
				), 
				'wrap_content' => array(
					'type' => 'switch', 
					'label' => __( 'Wrap Content', 'helium' ), 
					'description' => __( 'Switch to automatically wrap the post content inside a container. Switch off to use advanced row layouts.', 'helium' ), 
					'std' => true
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
add_action( 'init', 'helium_add_page_metabox' );
