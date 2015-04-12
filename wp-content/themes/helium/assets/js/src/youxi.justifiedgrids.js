
!function($) {

	"use strict";

	var _win = $( window );
	
	/* ==========================================================================
		Justified Image Grids. Algorithm thanks to Terry Mun
		https://medium.com/coding-design/7742e6f93d9e
	============================================================================= */
	var JustifiedGrids = function( element, options ) {
		this.element = $( element );
		return this.init( options );
	};

	/* Core */
	JustifiedGrids.prototype = {

		defaults: {
			selector: false
			, ratio: false
			, target: false

			, assignHeight: true
			, assignBottomMargin: false
			, margin: 0
			, minWidth: 320
			, minHeight: 240

			, justifyLastRow: true
			, immediateLayout: true

			, mainClass: 'justified-grids'
			, gridBreakClass: 'justified-grids-break'

			, itemClass: 'justified-grids-item'
			, failClass: 'justified-grids-item-fail'
			, lastColumnClass: 'last-col'
			, lastRowClass: 'last-row'
		}

		, init: function( options ) {

			if( ! this.isInitialized ) {

				this.instanceId = '.jg' + ( JustifiedGrids.instanceId++ );
				this.options = $.extend( true, {}, this.defaults, options, this.extractOptions( this.element.data() ) );

				this.element.addClass( this.options.mainClass );
				this.items = this.element.children( this.options.selector );

				// Hide every child that does not pass the selector
				this.element.children().not( this.items ).hide();

				// Prepare the items
				this._prepare( this.items );

				// Bind event handlers
				this._bindHandlers();

				this.visibleItems = this.items;
				this.layout();

				this.isInitialized = true;
			}
		}

		, destroy: function() {

			var _this = this
				, _opts = this.options;

			this.element.removeClass( _opts.mainClass );
			this.element.find( '.' + _opts.gridBreakClass ).remove();
			this.items
				.css({ display: '', marginRight: '', marginBottom: '' })
				.removeClass( [ _opts.itemClass, _opts.failClass, _opts.lastRowClass, _opts.lastColumnClass ].join( ' ' ) )
				.each(function() {
					var data = $.data( this, _this.instanceId );
					$.removeData( this, _this.instanceId );
					if( data && data.target && data.target.length ) {
						data.target.css({ width: '', height: '' });
					}
				});

			this.resizeTimeout && clearTimeout( this.resizeTimeout );
			_win.off( this.instanceId );

			$.removeData( this.element.get( 0 ), 'jg.instance' );

			this.element = null;
			this.items = null;
			this.visibleItems = null;
			this.activeFilter = null;
			this.visibleLastRow = null;
		}

		, extractOptions: function( data ) {

			var options = {};
			$.each( data, function( key, value ) {
				if( /^justified(.+)/.test( key ) ) {
					key = key.match( /^justified(.+)/ )[1];
					key = key.charAt(0).toLowerCase() + key.substr( 1 );
					options[ key ] = value;
				}
			});
			return options;
		}

		, layout: function() {

			// Hide all items except for the visible items
			this.items.not( this.visibleItems.css( 'display', '' ) )
				.css( 'display', 'none' );

			// Reset item classes
			this.items.removeClass( [
				this.options.lastRowClass, 
				this.options.lastColumnClass
			].join( ' ' ) );

			// Process the visible items
			this._doLayout();
		}

		, _bindHandlers: function() {

			var _this = this;

			// Refresh on window resize
			_win.on( 'resize' + this.instanceId + ' orientationchange' + this.instanceId, function() {
				_this.resizeTimeout && clearTimeout( _this.resizeTimeout );
				_this.resizeTimeout = setTimeout(function() {
					_this.layout();
				}, 50 );
			});
		}

		, _prepare: function( items ) {

			var i, len, w, h
				, ratio, item, target
				, _opts = this.options;

			if( ! items || ! items.length ) {
				return;
			}

			// Only proceed with items inside this instance's items collection
			items = this.items.filter( items );

			// Image data are stored in an array with the index referencing the item index
			for( i = 0, len = items.length; i < len; i++ ) {

				target = ! _opts.target ? $( items[i] ) 
					: $( items[i] ).find( _opts.target );

				// Get the ratio from a callback
				if( $.isFunction( _opts.ratio ) ) {

					ratio = _opts.ratio.apply( this.element, [ items[i], _opts ] );

				} else {

					item = ! _opts.ratio ? $( items[i] ) 
						: $( items[i] ).find( _opts.ratio );

					if( item.length ) {

						if( item.is( 'img' ) ) {

							// Try getting ratio from the image width and height attributes
							if( ( w = item.attr( 'width' ) ) && ( h = item.attr( 'height' ) ) ) {

								ratio = parseInt( w ) / parseInt( h );

							// Fallback, use nativeWidth/nativeHeight
							} else if( item[0].complete ) {
								ratio = JustifiedGrids.getImageRatio( item[0] );
							}

						} else if( ratio = item.data( 'aspect-ratio' ) ) {

							ratio = ratio.split( ':' );
							if( ratio.length >= 2 ) {
								ratio = ( parseInt( ratio[0] ) / Math.max( 1, parseInt( ratio[1] ) ) );
							}
						}
					}
				}

				// Failed retrieving ratio, assign hidden class to this item
				if( ! ratio ) {
					$( items[i] ).addClass( _opts.failClass );
					continue;
				} else {
					$( items[i] ).addClass( _opts.itemClass );
				}

				$.data( items[i], this.instanceId, {
					item: items[i], 
					target: target, 
					ratio: ratio, 
					calcWidth: Math.max( _opts.minWidth, ratio * _opts.minHeight )
				});
			}
		}

		, _doLayout: function( items, keepBreaks ) {

			var i, len, item
				, elementWidth, currentWidth
				, afterWidth, totalWidth
				, calculatedWidth
				, sumRatios
				, rows, currentRow
				, _opts = this.options
				, attempts = 0;

			if( ! items || ! items.length ) {
				items = this.visibleItems;
			}

			if( items.length ) {

				while( attempts++ < 3 ) {

					// Initialize variables
					totalWidth = 0;
					sumRatios = 0;
					rows = [];
					currentRow = [];

					if( ! elementWidth ) {
						elementWidth = JustifiedGrids.getWidth( this.element );
					}

					// Do the algorithm
					for( i = 0, len = items.length; i < len; i++ ) {

						if( item = $.data( items[i], this.instanceId ) ) {

							// Calculate the width respecting minHeight and minWidth
							calculatedWidth = Math.min( item.calcWidth, elementWidth ) + _opts.margin;

							/* 
								The row is full, generate the row and move the current item to the next row.
								Since the last row won't have margins, we'll try substracting the margin from totalWidth and 
								check if it overflows in case a very wide margin is set.
							*/
							if( totalWidth + calculatedWidth - _opts.margin >= elementWidth ) {

								rows.push({ row: currentRow, totalWidth: totalWidth, sumRatios: sumRatios });
								totalWidth = 0;
								sumRatios = 0;
								currentRow = [];
							}

							// Keep filling the row
							currentRow.push( item );

							// Push the current ratio
							sumRatios += item.ratio;

							// Accumulate the filled row width
							totalWidth += calculatedWidth;
						}
					}

					// If there are still rows left
					if( currentRow.length ) {
						rows.push({ row: currentRow, totalWidth: totalWidth, sumRatios: sumRatios });
					}

					// Remove breaks if specified
					if( ! keepBreaks ) {
						this.element.find( '.' + _opts.gridBreakClass ).remove();
					}

					// Now layout all the items in each row
					for( i = 0, len = rows.length; i < len; i++ ) {
						this._layoutRow( rows[i], elementWidth, i + 1 == len );
					}

					// Check for overflows
					currentWidth = JustifiedGrids.getWidth( this.element );
					if( elementWidth != currentWidth ) {
						elementWidth = currentWidth;
					} else {
						break;
					}
				}
			}
		}

		, _layoutRow: function( rowData, elementWidth, isLastRow ) {

			var row = rowData.row
				, sumRatios = rowData.sumRatios
				, availableWidth
				, height, css, curr
				, $currItem, i
				, len = row.length
				, _opts = this.options;

			if( isLastRow && ! _opts.justifyLastRow ) {
				elementWidth = rowData.totalWidth;
			}

			availableWidth = elementWidth;
			if( _opts.margin ) {
				availableWidth -= ( len - 1 ) * _opts.margin;
			}

			height = Math.max( _opts.minHeight, availableWidth / sumRatios );

			// Process the row
			for( i = 0; i < len; i++ ) {

				curr = row[i];
				$currItem = $( curr.item );

				if( curr.target.length ) {

					css = {};
					css.width = ( height * curr.ratio );

					// Only one item in this row
					if( css.width > elementWidth ) {
						css.width = elementWidth;
						height = elementWidth / curr.ratio;
					}

					if( _opts.assignHeight ) {
						css.height = height;
					}

					curr.target.css( css );
				}

				if( i + 1 == len ) {

					$currItem.addClass( _opts.lastColumnClass ).css( 'marginRight', '' );

					if( ! isLastRow ) {
						$currItem.after( '<br class="' + _opts.gridBreakClass + '" />' );
					}
				} else {
					$currItem.css( 'marginRight', _opts.margin );
				}

				if( ! isLastRow && _opts.assignBottomMargin ) {
					$currItem.css( 'marginBottom', _opts.margin );
				}
			}

			if( isLastRow ) {
				this.visibleLastRow = $( row ).map(function() {
					return this.item;
				}).addClass( _opts.lastRowClass ).css( 'marginBottom', '' );
			}
		}
	};

	/* Filter and append methods */
	$.extend( JustifiedGrids.prototype, {

		append: function( items, layoutLastRow ) {

			var visibleItems
				, itemsNeedLayout
				, _opts = this.options;

			// Extract only proper items
			items = $( items ).filter( _opts.selector );

			// Add the items to the collection
			this.items = this.items.add( items );

			// Prepare the items
			this._prepare( items );

			// If we're filtering items
			visibleItems = this.activeFilter ? items.filter( this.activeFilter ) : items;

			// Toggle items
			items.not( visibleItems.css( 'display', '' ) ).css( 'display', 'none' );

			// Add the visible items to the visible items collection
			if( ! this.visibleItems ) {
				this.visibleItems = this.items;
			}
			this.visibleItems = this.visibleItems.add( visibleItems );

			// Prepare items that need layout
			itemsNeedLayout = visibleItems;

			// Stop here if the new items are hidden
			if( itemsNeedLayout.length ) {

				// If we need to layout last row
				if( layoutLastRow || ! _opts.justifyLastRow ) {
					itemsNeedLayout = this.getLastRow().add( itemsNeedLayout );
				}

				this._doLayout( itemsNeedLayout, true );
			}

			// Return all items that need layout
			return itemsNeedLayout;
		}

		, filter: function( filter, preventLayout ) {
			
			if( ! this.activeFilter || filter != this.activeFilter ) {

				this.visibleItems = this.items;
				if( ( 'string' == typeof filter && '*' != filter ) || filter instanceof jQuery ) {
					this.activeFilter = filter;
					this.visibleItems = this.visibleItems.filter( filter );
				} else {
					this.activeFilter = null;
				}

				if( ! preventLayout ) {
					this.layout();
				}
			}
		}

		, getItems: function( visibleOnly ) {
			return visibleOnly ? this.visibleItems : this.items;
		}

		, getLastRow: function() {
			if( this.visibleLastRow && this.visibleLastRow.length ) {
				return $( this.visibleLastRow );
			}
			return $();
		}
	});

	$.fn.justifiedGrids = function( options ) {

		if( 'string' == typeof options ) {

			var i, len, instance, returnValue;
			var args = Array.prototype.slice.call( arguments, 1 );

			for( i = 0, len = this.length; i < len; i++ ) {

				instance = $.data( this[i], 'jg.instance' );
				if( instance instanceof JustifiedGrids ) {
					if( '_' !== options[0] && $.isFunction( instance[options ] ) ) {
						returnValue = instance[options].apply( instance, args );
						if( 'undefined' !== typeof returnValue ) {
							return returnValue;
						}
					} else {
						console.error( 'The method JustifiedGrids.' + options + ' doesn\'t exists.' );
					}
				} else {
					if( 'destroy' != options ) {
						console.error( 'Calling JustifiedGrids.' + options + ' before initialization.' );
					}
				}
			}
		} else {
			this.each(function() {

				var instance = $.data( this, 'jg.instance' );
				if( instance instanceof JustifiedGrids ) {
					return;
				}
				$.data( this, 'jg.instance', new JustifiedGrids( this, options ) );
			});
		}

		return this;
	}

	/* ==========================================================================
		Static Properties
	============================================================================= */

	JustifiedGrids.instanceId = 0;

	JustifiedGrids.getImageRatio = function( node ) {
		if( node && 'img' == node.tagName.toLowerCase() ) {
			if( 'naturalWidth' in node && 'naturalHeight' in node ) {
				return node.naturalWidth / node.naturalHeight;
			}

			var img = new Image();
			img.src = node.src;

			return img.width / img.height;
		}
	}

	JustifiedGrids.getWidth = function( elem ) {
		
		var computedStyle, width;

		elem = $( elem )[0];
		if( window.getComputedStyle ) {
			computedStyle = getComputedStyle( elem );
		} else {
			computedStyle = elem.currentStyle;
		}

		if( computedStyle.hasOwnProperty( 'width' ) ) {
			width = parseFloat( computedStyle.width );
			if( width && ( width + '' ).indexOf( '%' ) === -1 ) {
				return width;
			}
		}

		return elem.offsetWidth;
	}

}( jQuery );
