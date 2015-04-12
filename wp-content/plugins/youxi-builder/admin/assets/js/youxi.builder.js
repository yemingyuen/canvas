/**
 * Youxi Builder JS
 *
 * This script contains the page builder base code
 *
 * @package   Youxi Builder
 * @author    Mairel Theafila <maimairel@yahoo.com>
 * @copyright Copyright (c) 2013, Mairel Theafila
 */

;(function( $, window, document, undefined ) {

	"use strict";

	$.Youxi.Builder.editor = {

		instances: {}, 

		add: function( editorId ) {

			var builder = this.get( editorId );

			if( builder ) {
				return builder;
			}

			builder = $.Youxi.Builder({
				editorId: editorId
			});

			this.instances[editorId] = builder;

			return builder;
		}, 

		get: function( editorId ) {
			return this.instances[editorId];
		}, 

		init: function() {

			/* Don't initialize multiple times */
			if( this.initialized )
				return;

			this.initialized = true;

			this.backupEditorExpand();

			/* Bail if tinymce or switcheditors is not present */
			if( ! window.switchEditors || typeof tinymce == 'undefined' )
				return;

			/* Determine whether the switch editor buttons floats to the left/right */
			var isFloatRight = ( 'right' == $( '.wp-switch-editor' ).css( 'float' ) );

			/* Move the builder toggle button */
			$( '.wp-switch-editor.switch-youxi-builder' ).each(function() {
				var wpEditorTabs = $( this ).closest( '.wp-editor-tools' ).find( '.wp-editor-tabs' );
				$( this ).removeAttr( 'style' )
					[ isFloatRight ? 'prependTo' : 'appendTo' ]( wpEditorTabs );
			});

			/* Backup switchEditors.go function */
			var builderEditor = this;
			this.switchEditorsGo = window.switchEditors.go;

			/* Override switchEditors.go */
			window.switchEditors.go = function( id, mode ) {

				var builderInstance = builderEditor.get( id );

				if( ! builderInstance || builderInstance.isHidden() ) {
					if( 'ypbl' !== mode ) {
						return builderEditor.switchEditorsGo.apply( this, arguments );
					}
				}

				var ed, wrap_id, txtarea_el,
					DOM = tinymce.DOM; //DOMUtils outside the editor iframe

				id = id || 'content';
				mode = mode || 'toggle';

				ed = tinyMCE.get( id );
				wrap_id = 'wp-' + id + '-wrap';
				txtarea_el = DOM.get( id );

				if( 'ypbl' === mode ) {

					// Bail if the builder is already visible
					if( builderInstance && ! builderInstance.isHidden() ) {
						return false;
					}

					// Hide the tmce editor
					if( ed && ! ed.isHidden() ) {
						ed.hide();
					}
					DOM.removeClass( wrap_id, 'tmce-active' );

					// Hide the html editor
					if( typeof( QTags ) !== 'undefined' ) {
						QTags.closeAllTags( id );
					}
					if( tinyMCEPreInit.mceInit[ id ] && tinyMCEPreInit.mceInit[ id ].wpautop ) {
						txtarea_el.value = this.pre_wpautop( txtarea_el.value );
					}
					DOM.hide( id );
					DOM.removeClass( wrap_id, 'html-active' );

					// Show the page builder
					if( ! builderInstance ) {
						builderInstance = builderEditor.add( id );
					}
					builderInstance.show();
					DOM.addClass( wrap_id, 'ypbl-active' );

				} else {

					/* Work around when we need to go to html mode */
					if( 'html' === mode ) {
						builderEditor.switchEditorsGo.apply( this, [ id, 'tmce' ] );
					} else if( 'tmce' === mode ) {
						builderEditor.switchEditorsGo.apply( this, [ id, 'html' ] );
					}

					if( builderInstance && ! builderInstance.isHidden() ) {
						builderInstance.hide();
					}
					DOM.removeClass( wrap_id, 'ypbl-active' );

					return builderEditor.switchEditorsGo.apply( this, arguments );
				}

				return false;
			}
		}, 

		backupEditorExpand: function() {
			/* Get the editor expand handler */
			if( ! $.Youxi.Builder.editorExpandChange ) {
				$.each( $( '#editor-expand-toggle' ).data( 'events' ).change || {}, function() {
					if( this.namespace == 'editor-expand' ) {
						$.Youxi.Builder.editorExpandChange = this.handler;
						return false;
					}
				});
			}
		}
	};

	/* Init builder on document ready */
	$(function() {
		$.Youxi.Builder.editor.init();
	});

}) ( jQuery, window, document );