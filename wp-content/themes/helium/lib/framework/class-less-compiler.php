<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Hi there!  I\'m just a plugin, not much I can do when called directly.' );
}

/**
 * Youxi LESS Compiler
 *
 * This class caches and compiles LESS files
 *
 * @package   Youxi Themes
 * @author    Mairel Theafila <maimairel@yahoo.com>
 * @copyright Copyright (c) 2014, Mairel Theafila
 */

final class Youxi_LESS_Compiler {

	private $_less_parser_defaults = array(
		'use_cache' => false, 
		'compress'  => true
	);

	private $_less_parser;

	private static $_instance;

	private function __construct() {}

	public static function get() {
		if( ! is_a( self::$_instance, get_class() ) ) {
			self::$_instance = new Youxi_LESS_Compiler();
		}

		return self::$_instance;
	}

	private function serialize_vars( $vars ) {

		/* Include LESS parser */
		if( ! class_exists( 'Less_Parser' ) ) {
			require( get_template_directory() . '/lib/vendor/less.php/Less.php' );
		}

		return Less_Parser::serializeVars( $vars );
	}

	private function get_parser( $parser_args = array(), $reset = true ) {

		/* If the parser already exists */
		if( is_a( $this->_less_parser, 'Less_Parser' ) ) {
			if( $reset ) {
				$this->reset_parser( $parser_args );
			}
			return $this->_less_parser;
		}

		/* Include LESS parser */
		if( ! class_exists( 'Less_Parser' ) ) {
			require( dirname( __FILE__ ) . '/vendor/less.php/Less.php' );
		}

		/* Determine parser options */
		$parser_options = wp_parse_args( $this->_less_parser_defaults, $parser_args );

		return ( $this->_less_parser = new Less_Parser( $parser_options ) );
	}

	private function reset_parser( $parser_args = array() ) {

		if( is_a( $this->_less_parser, 'Less_Parser' ) ) {

			/* Determine parser options */
			$parser_options = wp_parse_args( $this->_less_parser_defaults, $parser_args );

			/* Reset Parser */
			$this->_less_parser->reset( $parser_options );
		}
	}

	private function get_cache_key() {
		return apply_filters( 'youxi_less_cache_key', 'youxi_less_cache' );
	}

	private function read_cache( $less_files ) {

		/* Get the cache object from wp_options table */
		$cache = get_option( $this->get_cache_key(), array() );

		/* Calculate hash key for the current less file */
		$cache_hash = md5( implode( ',', (array) $less_files ) );

		/* Return the cache if valid */
		if( isset( $cache[ $cache_hash ] ) && is_array( $cache[ $cache_hash ] ) ) {
			return $cache[ $cache_hash ];
		}

		return array();
	}

	private function update_cache( $less_files, $updated ) {

		/* Get the cache object from wp_options table */
		$cache = get_option( $this->get_cache_key(), array() );

		/* Calculate hash key for the current less file */
		$cache_hash = md5( implode( ',', (array) $less_files ) );

		/* Store the updated cache */
		$cache[ $cache_hash ] = $updated;

		if( ! add_option( $this->get_cache_key(), $cache, '', 'no' ) ) {
			update_option( $this->get_cache_key(), $cache );
		}
	}

	private function is_valid_cache( $cache, $key, $hash ) {

		if( ! is_array( $cache ) || ! isset( $cache[ $key ] ) ) {
			return false;
		}

		if( ! isset( $cache[ $key ]['hash'], $cache[ $key ]['css'] ) ) {
			return false;
		}

		return $hash == $cache[ $key ]['hash'] && is_string( $cache[ $key ]['css'] );
	}

	public function compile( $less_files, $var_sets ) {

		$output = '';

		/* Get the cache entry for this less file */
		$cache = $this->read_cache( $less_files );
		$cache_modified = false;

		/* Make sure cache is an array */
		if( ! is_array( $cache ) ) {
			$cache = array();
		}

		/* $var_sets should be an array of variables! */

		/* Parse and generate the styles */
		foreach( $var_sets as $vars_key => $vars ) {

			/* Serialize final vars */
			$serialized_vars = $this->serialize_vars( $vars );

			/*
				Calculate style hash.
				Make sure to add WordPress and theme version to the variables hash, 
				so the styles always get recompiled when updating theme/WordPress.
			*/
			$theme     = wp_get_theme();
			$theme_ver = ( $theme->exists() ? $theme->get( 'Version' ) : 1 );
			$wp_ver    = get_bloginfo( 'version' );

			/* Get files modification time */
			$filesmtime = '';
			foreach( (array) $less_files as $file ) {
				$filename = trailingslashit( get_template_directory() ) . trim( $file, '/\\' );
				if( is_readable( $filename ) ) {
					$filesmtime[] = filemtime( $filename );
				}
			}
			$filesmtime = implode( '_', $filesmtime );

			/* Calculate hash from serialized vars, files modification time, theme version and WP version */
			$vars_hash = md5( implode( '_', array( md5( $serialized_vars ), $filesmtime, $theme_ver, $wp_ver ) ) );

			/* Validate the cache by checking for keys and comparing variable hash */
			if( ! $this->is_valid_cache( $cache, $vars_key, $vars_hash ) ) {

				/* Get the parser again to also reset it */
				$parser = $this->get_parser();

				try {

					/* Parse the files first */
					foreach( (array) $less_files as $file ) {
						$parser->parseFile(
							trailingslashit( get_template_directory() ) . trim( $file, '/\\' ), 
							trailingslashit( get_template_directory_uri() ) . trim( $file, '/\\' )
						);
					}

					/* Now parse the variables */
					/* Remember! http://lesscss.org/features/#variables-feature-default-variables */
					$parser->parse( $serialized_vars );

					/* Store the result in the current cache */
					$cache[ $vars_key ] = array(
						'hash' => $vars_hash, 
						'css'  => $parser->getCss()
					);

					/* We've modified the cache */
					$cache_modified = true;

				} catch( Exception $e ) {

					if( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
						error_log( $e->getMessage() );
					}
					
					/* Remove the style in case of errors */
					unset( $cache[ $vars_key ] );
				}
			}

			/* Concantenate the style if it is valid */
			if( $this->is_valid_cache( $cache, $vars_key, $vars_hash ) ) {
				$output .= $cache[ $vars_key ]['css'];
			}
		}

		/* If the cache is modified */
		if( $cache_modified ) {
			$this->update_cache( $less_files, $cache );
		}

		return $output;
	}
}