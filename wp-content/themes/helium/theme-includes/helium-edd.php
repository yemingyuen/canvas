<?php

/* ==========================================================================
	EDD Archive Options
============================================================================= */

if( ! function_exists( 'helium_edd_grid_defaults' ) ):

function helium_edd_grid_defaults() {

	return wp_parse_args( array(
		'show_filter'               => helium_get_option( 'edd_grid_show_filter' ), 
		'pagination'                => helium_get_option( 'edd_grid_pagination' ), 
		'ajax_button_text'          => helium_get_option( 'edd_grid_ajax_button_text' ), 
		'ajax_button_complete_text' => helium_get_option( 'edd_grid_ajax_button_complete_text' ), 
		'posts_per_page'            => helium_get_option( 'edd_grid_posts_per_page' ), 
		'include'                   => helium_get_option( 'edd_grid_include' ), 
		'behavior'                  => helium_get_option( 'edd_grid_behavior' ), 
		'columns'                   => helium_get_option( 'edd_grid_columns' )
	), array(
		'show_filter'               => true, 
		'pagination'                => 'ajax', 
		'ajax_button_text'          => 'Load More', 
		'ajax_button_complete_text' => 'No More Items', 
		'posts_per_page'            => get_option( 'posts_per_page' ), 
		'include'                   => array(), 
		'behavior'                  => 'lightbox', 
		'columns'                   => 4
	));
}
endif;

/* ==========================================================================
	EDD Archive Page
============================================================================= */

if( ! function_exists( 'helium_edd_ot_type_select_choices' ) ):

function helium_edd_ot_type_select_choices( $choices, $field_id ) {

	if( 'edd_archive_page' == $field_id ) {

		$pages = get_posts(array(
			'post_type'  => 'page', 
			'meta_key'   => '_wp_page_template', 
			'meta_value' => 'archive-download.php'
		));

		if( $pages ) {
			foreach( $pages as $page ) {
				$choices[] = array(
					'label' => $page->post_title, 
					'value' => $page->ID, 
					'src'   => ''
				);
			}
		}
	}

	return $choices;
}
endif;
add_filter( 'ot_type_select_choices', 'helium_edd_ot_type_select_choices', 10, 2 );
