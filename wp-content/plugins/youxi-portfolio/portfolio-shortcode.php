<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Hi there!  I\'m just a plugin, not much I can do when called directly.' );
}

/**
 * Fetch all portfolio categories
 */
function youxi_portfolio_shortcode_categories() {
	$result = array();
	$terms = get_terms( get_object_taxonomies( Youxi_Portfolio::post_type_name() ), array( 'hide_empty' => false ) );

	if( $terms && ! is_wp_error( $terms ) ) {
		foreach( $terms as $term ) {
			$result[ $term->term_id ] = $term->name;
		}
	}

	return $result;
}

/**
 * Portfolio shortcode handler.
 * do nothing and leave rendering of the portfolio completely to the theme
 */
function youxi_portfolio_shortcode_cb( $atts, $content, $tag ) {
	ob_start();
	$args = func_get_args();
	do_action_ref_array( 'youxi_portfolio_shortcode_output', $args );
	return ob_get_clean();
}

/**
 * Register shortcode
 */
function youxi_portfolio_shortcode_register( $manager ) {

	if( ! apply_filters( 'youxi_portfolio_register_shortcode', true ) ) {
		return;
	}

	/* Add a hook to make registering another shortcode category possible */
	do_action( 'youxi_portfolio_shortcode_register' );

	/********************************************************************************
	 * Portfolio shortcode
	 ********************************************************************************/
	$manager->add_shortcode( 'portfolio', array(
		'label' => __( 'Portfolio', 'youxi' ), 
		'category' => apply_filters( 'youxi_portfolio_shortcode_category', 'content' ), 
		'priority' => 75, 
		'atts' => apply_filters( 'youxi_portfolio_shortcode_atts', array(
			'exclude' => array(
				'type' => 'checkboxlist', 
				'label' => __( 'Excluded Categories', 'youxi' ), 
				'description' => __( 'Choose the portfolio categories to exclude.', 'youxi' ), 
				'choices' => 'youxi_portfolio_shortcode_categories', 
				'uncheckable' => true, 
				'serialize' => 'js:function( data ) {
					console.log(data);
					return $.map( data, function( data, key ) {
						if( !! parseInt( data ) )
							return key;
					});
				}', 
				'deserialize' => 'js:function( data ) {
					var temp = {};
					_.each( ( data + "" ).split( "," ), function( c ) {
						temp[ c ] = 1;
					});
					return temp;
				}'
			)
		)), 
		'callback' => 'youxi_portfolio_shortcode_cb'
	) );
}
add_action( 'youxi_shortcode_register', 'youxi_portfolio_shortcode_register' );
