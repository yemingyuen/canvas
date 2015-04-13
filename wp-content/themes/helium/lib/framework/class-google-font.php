<?php
final class Youxi_Google_Font {

	protected static $cached_fonts;

	/**
	 * Parse a Google Font string: Roboto:700&subset=latin
	 */
	public static function parse( $str ) {
		$font = explode( ':', $str );
		$len  = count( $font );

		if( $len > 0 ) {

			if( $len > 1 ) {
				$variant = $font[1];
			} else {
				$variant = '';
			}

			return array(
				'family'  => $font[0], 
				'variant' => $variant, 
				'weight'  => self::variant2weight( $variant ), 
				'style'   => self::variant2style( $variant )
			);
		}

		return array();
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
		return preg_replace( array( '/^$|^(regular|italic)$/', '/^bold$/', '/italic$/' ), array( '400', '700' ), $variant );
	}

	/**
	 * Construct a Google Font request URL from a set of requested fonts
	 */
	public static function request_url( $fonts ) {
		$urls = array();
		foreach( $fonts as $family => $variants ) {
			$variants = array_unique( $variants );
			sort( $variants, SORT_STRING );
			$urls[] = implode( ':', array( $family, implode( ',', $variants ) ) );
		}

		return esc_url( '//fonts.googleapis.com/css?family=' . implode( '|', $urls ) );
	}

	/**
	 * Get the normal and italic variants based on a font weight
	 */
	public static function weight_set( $family, $variant ) {

		$google_fonts = self::fetch();
		$weight = self::variant2weight( $variant );
		$variants = array();

		if( is_array( $google_fonts ) && isset( $google_fonts[ $family ], $google_fonts[ $family ]['variants'] ) ) {
			foreach( (array) $google_fonts[ $family ]['variants'] as $variant ) {
				$variant_weight = self::variant2weight( $variant );
				if( preg_match( "/^{$weight}(italic)?$/", $variant_weight ) ) {
					$variants[] = $variant;
				}
			}
		}

		return $variants;
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
			$google_fonts_sort   = apply_filters( 'youxi_google_fonts_sort', 'popularity' );

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