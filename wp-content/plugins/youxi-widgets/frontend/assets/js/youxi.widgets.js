
;(function( $, window, document, undefined ) {

	"use strict";

	var windowLoad = {

		'flickr': function( config ) {

			if( $.fn.jflickrfeed ) {

				$( '.flickr-feed' ).each(function() {

					var flickrId = $( this ).data( 'flickr-id' );
					var limit    = $( this ).data( 'limit' ) || 9;
					
					$( document.createElement( 'ul' ) )
						.jflickrfeed( $.extend( true, config && config.defaults || {}, { qstrings: { id: flickrId }, limit: limit }))
						.prependTo( this );
				});
			}
		}, 

		'twitter': function( config ) {

			if( $.fn.miniTweets ) {

				$( '.twitter-feed' ).each(function() {
					
					$( document.createElement( 'ul' ) )
						.miniTweets( $.extend( true, config && config.defaults || {}, $( this ).data() ) )
						.prependTo( this );
				});
			}
		}, 

		'google-maps': function( config ) {

			if( $.fn.youxiGoogleMaps ) {

				$( '.google-maps' ).each(function() {
					$( this ).youxiGoogleMaps( $.extend( true, config && config.defaults || {}, $( this ).data() ) );
				});
			}
		}, 

		'instagram': function( config ) {

			$( '.instagram-feed' ).each(function() {

				var _this = this, 
					options = $.extend( { username: '', count: 8, imageSize: 'thumbnail' }, $( this ).data() );				

				$.post( _youxiWidgets.ajaxUrl, { action: config.ajaxAction, instagram: options }, $.noop, 'json' )
				
					.done(function( response ) {

						if( response.success && $.isArray( response.data ) ) {

							$( document.createElement( 'ul' ) )

								.html( $.map( response.data, function( data ) {

									var link = data.link || '#';
									var caption = data.caption && data.caption.text || '';
									var imageUrl = data.images && data.images.hasOwnProperty( options.imageSize ) ? data.images[ options.imageSize ].url : '';

									/* Make sure image urls are in the same URL scheme */
									imageUrl = imageUrl.replace( /^https?:\/\//, '//' );

									return $( document.createElement( 'li' ) )
										.html( $( document.createElement( 'a' ) )
											.attr( { href: link, title: caption, target: '_blank' } )
											.html( $( document.createElement( 'img' ) ) .attr( { src: imageUrl, alt: caption } ) )
										);
								})).prependTo( _this );

						} else {
							$( _this ).html( '<div class="alert alert-danger">' + 
								( response.data ? response.data.error_message : '' ) + '</div>' );
						}
					});
			});
		}
	};

	var documentReady = {

		'rotatingquotes': function( config ) {

			if( $.fn.quovolver ) {

				$( '.quovolver' ).each(function() {
					$( this ).quovolver( $.extend( true, config && config.defaults || {}, $( this ).data() ) );
				});
			}
		}
	};

	var maybeSetup = function( widgets ) {
		var context = this;

		$.each( widgets, function( id_base, fn ) {
			if( _youxiWidgets.hasOwnProperty( id_base ) ) {
				$.isFunction( fn ) && fn.apply( context, [ _youxiWidgets[ id_base ] ] );
			}
		});
	};

	$( window ).one( 'load.youxi-widget', function() {
		maybeSetup.apply( this, [ windowLoad ] );
	});

	$( function() {
		maybeSetup.apply( this, [ documentReady ] );
	});

}) ( jQuery, window, document );