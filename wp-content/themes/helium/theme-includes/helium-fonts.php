<?php

if( ! function_exists( 'helium_font_options' ) ):

function helium_font_options() {

	static $cached = null;
	
	$options = helium_get_all_options();
	$option_keys = helium_font_option_keys();

	return ( $cached = array_intersect_key( $options, array_flip( $option_keys ) ) );
}
endif;

if( ! function_exists( 'helium_default_font_options' ) ):

function helium_default_font_options() {
	return apply_filters( 'helium_default_font_settings', array(
		'headings_1234_font' => array(
			'default' => 'Roboto:700', 
			'include_weight_set' => true
		), 
		'headings_56_font' => array(
			'default' => 'Roboto:500', 
			'include_weight_set' => true
		), 
		'body_font' => array(
			'default' => 'Roboto:300', 
			'include_weight_set' => true, 
			'include_weights' => array( 700 )
		), 
		'menu_font' => array(
			'default' => 'Roboto:700'
		), 
		'blockquote_font' => array(
			'default' => 'Lora:italic'
		), 
		'gridlist_title_font' => array(
			'default' => 'Roboto:700'
		), 
		'gridlist_subtitle_font' => array(), 
		'content_nav_font' => array(
			'default' => 'Roboto:700'
		)
	));
}
endif;

if( ! function_exists( 'helium_font_option_keys' ) ):

function helium_font_option_keys() {

	static $option_keys = array();
	
	if( empty( $option_keys ) ) {
		$option_keys = array_keys( helium_default_font_options() );
	}

	return $option_keys;
}
endif;

if( ! function_exists( 'helium_css_to_less_vars' ) ):

function helium_css_to_less_vars( $font_css ) {

	$font_vars = array();

	foreach( $font_css as $key => $css ) {

		if( ! is_array( $css ) ) {
			continue;
		}

		$font_var_key = preg_replace( array( '/_/', '/(-|_)?font$/' ), array( '-' ), $key );

		foreach( array( 'family', 'weight', 'style' ) as $prop ) {

			if( isset( $css['font-' . $prop ] ) ) {
				$font_vars[ $font_var_key . '-font-' . $prop  ] = $css['font-' . $prop ];
			}
		}
	}

	return $font_vars;
}
endif;
