<?php
abstract class Youxi_Customize_Manager {

	/**
	 * Theme mod prefix
	 */
	private $_prefix;

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'customize_register', array( $this, 'pre_customize' ));
	}
	
	public function prefix() {
		if( ! empty( $this->_prefix ) ) {
			return $this->_prefix;
		}
		$theme = wp_get_theme();
		return ( $this->_prefix = preg_replace( '/\W/', '_', $theme->stylesheet ) . '_settings' );
	}

	public function pre_customize( $wp_customize ) {
		require( 'controls/class-google-font-control.php' );
		require( 'controls/class-multicheck-control.php' );
		require( 'controls/class-range-control.php' );
		require( 'controls/class-switch-control.php' );
	}

	public static function sanitize_boolean( $value ) {
		return (bool) $value;
	}
}
