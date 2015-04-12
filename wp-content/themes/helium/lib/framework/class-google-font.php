<?php
if ( ! defined( 'ABSPATH' ) ) {
	die();
}

/**
 * Youxi Google Font
 *
 * This class provides helper methods to ease working with Google Fonts
 *
 * @package   Youxi Themes Theme Utils
 * @author    Mairel Theafila <maimairel@yahoo.com>
 * @copyright Copyright (c) 2014-2015, Mairel Theafila
 */

final class Youxi_Google_Font {

	protected static $cached_fonts;

	/**
	 * Parse a single variant Google Font string using regular expression: Roboto:400&subset=latin,latin-ext
	 */
	public static function parse_regex( $str ) {

		$pattern = '/^([a-zA-Z\d+ ]+)(?::([1-9]00|(?:(?:[1-3]|[5-9])00)?italic|regular))?(?:&subset=((?:[a-zA-Z-]+,)*(?:[a-zA-Z-]+)))?$/';
		$google_fonts = self::fetch();

		$matches = array();
		if( preg_match( $pattern, $str, $matches ) ) {

			if( isset( $matches[1] ) && ( $family = $matches[1] ) && isset( $google_fonts[ preg_replace( '/\s+/', '', $family ) ] ) ) {

				if( isset( $matches[2] ) ) {
					$variant = $matches[2];
				} else {
					$variant = '';
				}

				$variant = self::sanitize_variant( $family, $variant );
				$weight  = self::variant2weight( $variant );
				$style   = self::variant2style( $variant );

				if( isset( $matches[3] ) ) {
					$subsets = array_filter( explode( ',', $matches[3] ) );
				} else {
					$subsets = array();
				}

				return compact( 'family', 'variant', 'weight', 'style', 'subsets' );
			}
		}

		return false;
	}

	/**
	 * Parse a Google Font string using string functions: Roboto:400&subset=latin,latin-ext
	 */
	public static function parse_str( $str ) {

		$google_fonts = self::fetch();

		$font = explode( ':', $str );
		$len  = count( $font );
		if( $len > 0 && ( $family = $font[0] ) && isset( $google_fonts[ preg_replace( '/\s+/', '', $family ) ] ) ) {

			if( $len > 1 ) {
				$variant = $font[1];
			} else {
				$variant = '';
			}

			$subsets = explode( '&subset=', $variant );
			if( count( $subsets ) > 1 ) {
				$variant = $subsets[0];
				$subsets = array_filter( explode( ',', $subsets[1] ) );
			} else {
				$subsets = array();
			}

			$variant = self::sanitize_variant( $family, $variant );
			$weight  = self::variant2weight( $variant );
			$style   = self::variant2style( $variant );

			return compact( 'family', 'variant', 'weight', 'style', 'subsets' );
		}

		return false;
	}

	/**
	 * Compare default font settings to a set of user specified arguments, 
	 * Convert the result to CSS' font family, font-style, font-weight
	 * Convert the result to Google Font family and variants
	 */
	public static function parse( $args, $defaults ) {

		$css = array();
		$families = array();
		$subsets = array();

		foreach( $defaults as $key => $default_arg ) {

			$option = isset( $args[ $key ] ) ? $args[ $key ] : false;
			if( empty( $option ) ) {
				if( isset( $default_arg['default'] ) ) {
					$option = $default_arg['default'];
				} else {
					continue;
				}
			}

			if( $font = self::parse_str( $option ) ) {

				/* CSS properties */
				$css[ $key ] = array(
					'font-family' => '"' . str_replace( '+', ' ', $font['family'] ) . '"', 
					'font-weight' => $font['weight'], 
					'font-style' => $font['style']
				);

				/* Google Font families */
				if( ! isset( $families[ $font['family'] ] ) ) {
					$families[ $font['family'] ] = array();
				}

				/* Font inclusion settings */
				$include_weight_set = isset( $default_arg['include_weight_set'] ) && $default_arg['include_weight_set'];
				$include_weights = isset( $default_arg['include_weights'] ) ? $default_arg['include_weights'] : array();

				/* Include weight set */
				if( $include_weight_set ) {
					$families[ $font['family'] ] = array_merge( $families[ $font['family'] ], 
						Youxi_Google_Font::weight_set( $font['family'], $font['variant'] ) );
				} else {
					$families[ $font['family'] ][] = $font['variant'];
				}

				/* Include additional weights */
				if( is_array( $include_weights ) ) {
					
					foreach( $include_weights as $weight ) {
						$weight_set = Youxi_Google_Font::weight_set( $font['family'], $weight );
						if( $include_weight_set ) {
							$families[ $font['family'] ] = array_merge( $families[ $font['family'] ], $weight_set );
						} else {
							foreach( $weight_set as $var ) {
								$var_style = self::variant2style( $var );
								if( $font['style'] == $var_style ) {
									$families[ $font['family'] ][] = $var;
								}
							}
						}
					}
				}

				/* Make sure the family doesn't contain duplicate values */
				$families[ $font['family'] ] = array_unique( $families[ $font['family'] ] );

				/* Google Font subsets */
				if( isset( $font['subsets'] ) && is_array( $font['subsets'] ) ) {
					$subsets = array_merge( $subsets, $font['subsets'] );
				}
			}
		}

		/* Make sure the subsets doesn't contain duplicate values */
		$subsets = array_unique( $subsets );

		return compact( 'css', 'families', 'subsets' );
	}

	/**
	 * Sanitize a Google Font variant so it matches the actual presentation, 
	 * or return an empty string if the variant does not exists.
	 */
	public static function sanitize_variant( $family, $variant ) {

		$style  = self::variant2style( $variant );
		$weight = self::variant2weight( $variant );
		
		foreach( self::get_variants( $family ) as $var ) {
			$var_weight = self::variant2weight( $var );
			$var_style  = self::variant2style( $var );

			/* Bingo */
			if( $var_weight == $weight && $var_style == $style ) {
				return $var;
			}
		}

		return '';
	}

	/**
	 * Convert a Google Font variant to font-style
	 */
	public static function variant2style( $variant ) {
		return preg_match( '/italic/', $variant ) ? 'italic' : 'normal';
	}

	/**
	 * Convert a Google Font variant to font-weight
	 */
	public static function variant2weight( $variant ) {
		return preg_replace( array( '/^$|^(regular|italic)$/', '/italic$/' ), array( '400' ), $variant );
	}

	/**
	 * Construct a Google Font request URL from a set of requested fonts
	 */
	public static function request_url( $fonts, $subsets = array() ) {

		$urls = array();
		foreach( $fonts as $family => $variants ) {
			$variants = array_unique( $variants );
			sort( $variants, SORT_STRING );
			$urls[] = implode( ':', array( $family, implode( ',', $variants ) ) );
		}

		$query_args = array( 'family' => implode( '|', $urls ) );
		if( is_array( $subsets ) && ! empty( $subsets ) ) {
			$query_args['subset'] = implode( ',', $subsets );
		}

		return add_query_arg( $query_args, '//fonts.googleapis.com/css' );
	}

	/**
	 * Get all font styles based on variant weight
	 */
	public static function weight_set( $family, $variant ) {

		$variants = array();
		$weight = self::variant2weight( $variant );
		
		foreach( self::get_variants( $family ) as $variant ) {
			$variant_weight = self::variant2weight( $variant );
			if( $weight == $variant_weight ) {
				$variants[] = $variant;
			}
		}

		return $variants;
	}

	/**
	 * Get all variants of a font family
	 */
	public static function get_variants( $family ) {

		$google_fonts = self::fetch();
		$family = preg_replace( '/\s+/', '+', $family );

		if( isset( $google_fonts[ $family ], $google_fonts[ $family ]['variants'] ) ) {
			return $google_fonts[ $family ]['variants'];
		}

		return array();
	}

	/**
	 * Get all subsets of a font family
	 */
	public static function get_subsets( $family ) {

		$google_fonts = self::fetch();
		$family = preg_replace( '/\s+/', '+', $family );

		if( isset( $google_fonts[ $family ], $google_fonts[ $family ]['subsets'] ) ) {
			return $google_fonts[ $family ]['subsets'];
		}

		return array();
	}
	/**
	 * Fetch Google Fonts and cache it for a week
	 */
	public static function fetch() {

		/* See if we have the fonts in internal cache */
		if( ! is_null( self::$cached_fonts ) && is_array( self::$cached_fonts ) && ! empty( self::$cached_fonts ) ) {
			return self::$cached_fonts;
		}

		/* Try to get Google Fonts first in case some external code provides the fonts */
		$google_fonts = apply_filters( 'youxi_google_fonts_cache', array() );

		/* If no fonts are fetched, get it from cache */
		if( empty( $google_fonts ) ) {

			/* Google Fonts cache key */
			$google_fonts_cache_key = apply_filters( 'youxi_google_fonts_cache_key', 'youxi_google_fonts_cache' );
			$google_fonts = get_transient( $google_fonts_cache_key );
		}

		/* If we still don't have the fonts, let's get it directly from Google */
		if ( ! is_array( $google_fonts ) || empty( $google_fonts ) ) {

			$google_fonts = array();

			/* API url and key */
			$google_fonts_api_url = apply_filters( 'youxi_google_fonts_api_url', 'https://www.googleapis.com/webfonts/v1/webfonts' );
			$google_fonts_api_key = apply_filters( 'youxi_google_fonts_api_key', 'AIzaSyC-0ipgZdTRp2jeOct8w9GuPqjBX5LDDHE' );

			/* API arguments */
			$google_fonts_fields = apply_filters( 'youxi_google_fonts_fields', array( 'family', 'variants', 'subsets' ) );
			$google_fonts_sort   = apply_filters( 'youxi_google_fonts_sort', 'alpha' );

			/* Initiate API request */
			$google_fonts_query_args = array(
				'key'    => $google_fonts_api_key, 
				'fields' => 'items(' . implode( ',', $google_fonts_fields ) . ')', 
				'sort'   => $google_fonts_sort
			);

			/* Build and make the request */
			$google_fonts_query = add_query_arg( $google_fonts_query_args, $google_fonts_api_url );
			$google_fonts_response = wp_safe_remote_get( $google_fonts_query, array( 'sslverify' => false, 'timeout' => 15 ) );

			/* continue if we got a valid response */
			if ( 200 == wp_remote_retrieve_response_code( $google_fonts_response ) ) {

				if ( $response_body = wp_remote_retrieve_body( $google_fonts_response ) ) {

					/* JSON decode the response body and cache the result */
					$google_fonts_data = json_decode( trim( $response_body ), true );

					if ( is_array( $google_fonts_data ) && isset( $google_fonts_data['items'] ) ) {

						$google_fonts = $google_fonts_data['items'];

						$google_font_keys = wp_list_pluck( $google_fonts, 'family' );
						$google_font_keys = implode( '|', $google_font_keys );
						$google_font_keys = preg_replace( '/\s+/', '+', $google_font_keys );
						$google_font_keys = explode( '|', $google_font_keys );

						$google_fonts = array_combine( $google_font_keys, $google_fonts );

						set_transient( $google_fonts_cache_key, $google_fonts, WEEK_IN_SECONDS );
					}

				}

			}

		}

		return ( self::$cached_fonts = is_array( $google_fonts ) ? $google_fonts : array() );
	}
}