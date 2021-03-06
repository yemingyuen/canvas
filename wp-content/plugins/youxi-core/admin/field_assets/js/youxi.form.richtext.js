/**
 * Youxi Richtext Form Field JS
 *
 * This script contains the initialization code for the richtext form field.
 *
 * @package   Youxi Core
 * @author    Mairel Theafila <maimairel@yahoo.com>
 * @copyright Copyright (c) 2013, Mairel Theafila
 */
;(function( $, window, document, undefined ) {

	"use strict";

	if( $.Youxi.Form.Manager ) {

		$.Youxi.Form.Manager.addCallbacks( 'richtext', function( context ) {

			$( context ).on( 'click.wp-editor', '.wp-editor-wrap', function( e ) {
				
				if( this.id ) {
					window.wpActiveEditor = this.id.slice( 3, -5 );
				}

			}).find( 'textarea.youxi-tmce-ajax' ).each( function() {

				if( typeof tinymce !== 'undefined' && 'tinymce' == getUserSetting( 'editor' ) && tinyMCEPreInit.mceInit ) {

					var ed = tinymce.get( this.id ), 
						settings = tinyMCEPreInit.mceInit[ this.id ];

					if( ! ed && settings ) {
						try {
							tinymce.init( settings );
							if( ! window.wpActiveEditor ) {
								window.wpActiveEditor = this.id;
							}
						} catch( e ) {}
					}
				}

				if( typeof QTags !== 'undefined' ) {

					var qtinit = tinyMCEPreInit.qtInit && tinyMCEPreInit.qtInit[ this.id ];
					try {
						if( qtinit ) {
							quicktags( qtinit );
							QTags._buttonsInit();
							
							if( ! window.wpActiveEditor ) {
								window.wpActiveEditor = this.id;
							}
						}
					} catch( e ) {}
				}
			});

		}, function( context ) {

			$( context ).find( 'textarea.youxi-tmce-ajax' ).each( function() {

				if( typeof tinymce !== 'undefined' && tinymce.get( this.id ) ) {
					tinymce.remove( '#' + this.id );
				}
			}).off( 'click.wp-editor' );

		});
	}

})( jQuery, window, document );