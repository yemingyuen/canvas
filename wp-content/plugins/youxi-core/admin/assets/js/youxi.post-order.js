/**
 * Youxi Post Order JS
 *
 * This script contains the initialization code for the post order screen.
 *
 * @package   Youxi Core
 * @author    Mairel Theafila <maimairel@yahoo.com>
 * @copyright Copyright (c) 2013, Mairel Theafila
 */
;(function( $, window, document, undefined ) {

	"use strict";

	$( document ).ready(function() {

		if( $.fn.sortable ) {

			$( '.youxi-post-order-items-holder' ).sortable({

				update: function( event, ui ) {

					var orderResult = $( this ).sortable( 'toArray', { attribute: 'data-post-id' } );

					wp.ajax.post( 'youxi-post-order-save', {
						menu_order: orderResult, 
						nonce: YouxiPostOrder.nonce
					}).done(function( response ) {
					}).fail(function( error ) {
						console.log( error );
					});
				}
			});

		}

	});


})( jQuery, window, document );