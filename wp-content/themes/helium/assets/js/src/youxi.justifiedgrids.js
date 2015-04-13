
// Youxi Justified Grids
;(function( $, window, document, undefined ) {

	"use strict";
	
	/* ==========================================================================
		Justified Image Grids. Algorithm thanks to Terry Mun
		https://medium.com/coding-design/7742e6f93d9e
	============================================================================= */
	var JustifiedGrids = function( element, options ) {
		this.element = $( element );
		return this.init( options );
	};

	JustifiedGrids.prototype = {

		defaults: {
			selector: false, 
			ratio: false, 
			target: false, 

			assignHeight: true, 
			margin: 0, 
			minWidth: 320, 
			minHeight: 240, 

			justifyLastRow: true, 
			immediateLayout: true, 

			mainClass: 'justified-grids', 
			failClass: 'justified-grids-fail', 
			gridBreakClass: 'justified-grids-break', 
			lastColumnClass: 'last-col', 
			lastRowClass: 'last-row'
		}, 

		init: function( options ) {

			if( ! this.isInitialized ) {

				this.instanceId = '.jg' + ( JustifiedGrids.instanceId++ );
				this.options = $.extend( true, {}, this.defaults, options );

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
		}, 

		append: function( items, layoutLastRow ) {

			var visibleItems, itemsNeedLayout;

			// Extract only proper items
			items = $( items ).filter( this.options.selector );

			// Add the items to the collection
			this.items = this.items.add( items );

			// Prepare the items
			this._prepare( items );

			// If we're filtering items
			visibleItems = this.activeFilter ? items.filter( this.activeFilter ) : items;

			// Toggle items
			items.not( visibleItems.css( 'display', '' ) ).css( 'display', 'none' );

			// Add the visible items to the visible items collection
			this.visibleItems = this.visibleItems.add( visibleItems );

			// Prepare items that need layout
			itemsNeedLayout = visibleItems;

			// Stop here if the new items are hidden
			if( itemsNeedLayout.length ) {

				// If we need to layout last row
				if( layoutLastRow || ! this.options.justifyLastRow ) {
					itemsNeedLayout = this.getLastRow().add( itemsNeedLayout );
				}

				this._doLayout( itemsNeedLayout );
			}

			// Return all items that need layout
			return itemsNeedLayout;
		}, 

		destroy: function() {

			var self = this;

			this.element.removeClass( this.options.mainClass );
			this.element.find( '.' + this.options.gridBreakClass ).remove();
			this.items
				.css({ display: '', marginRight: '' })
				.removeClass( [ this.options.lastRowClass, this.options.lastColumnClass, this.options.failClass ].join( ' ' ) )
				.each(function() {
					var data = $.data( this, self.instanceId );
					$.removeData( this, self.instanceId );
					if( data && data.target && data.target.length ) {
						data.target.css({ width: '', height: '' });
					}
				});

			if( this.resizeTimeout ) clearTimeout( this.resizeTimeout );
			$( window ).off( this.instanceId );

			$.removeData( this.element.get( 0 ), 'jg.instance' );

			this.element = null;
			this.items = null;
			this.visibleItems = null;
			this.activeFilter = null;
			this.visibleLastRow = null;
		}, 

		filter: function( filter, preventLayout ) {
			
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
		}, 

		getItems: function( visibleOnly ) {
			return visibleOnly ? this.visibleItems : this.items;
		}, 

		getLastRow: function() {
			if( this.visibleLastRow && this.visibleLastRow.length ) {
				return $( this.visibleLastRow );
			}
			return $();
		}, 

		layout: function() {

			// Hide all items except for the visible items
			this.items.not( this.visibleItems.css( 'display', '' ) )
				.css( 'display', 'none' );

			// Reset item classes
			this.items.removeClass( [ this.options.lastRowClass, this.options.lastColumnClass ].join( ' ' ) );

			// Process the visible items
			this._doLayout();
		}, 

		_bindHandlers: function() {

			var self = this;

			// Refresh on window resize
			$( window ).on( 'resize' + this.instanceId + ' orientationchange' + this.instanceId, function() {
				if( self.resizeTimeout ) {
					clearTimeout( self.resizeTimeout );
				}
				self.resizeTimeout = setTimeout(function() {
					self.layout();
				}, 50 );
			});
		}, 

		_prepare: function( items ) {

			var i
			, len
			, w, h
			, ratio
			, item
			, target;

			if( ! items || ! items.length ) {
				return;
			}

			// Only proceed with items inside this instance's items collection
			items = this.items.filter( items );

			// Image data are stored in an array with the index referencing the item index
			for( i = 0, len = items.length; i < len; i++ ) {

				target = ! this.options.target ? $( items[i] ) 
					: $( items[i] ).find( this.options.target );

				// Get the ratio from a callback
				if( $.isFunction( this.options.ratio ) ) {

					ratio = this.options.ratio.apply( this.element, [ items[i], this.options ] );

				} else {

					item = ! this.options.ratio ? $( items[i] ) 
						: $( items[i] ).find( this.options.ratio );

					if( item.length ) {

						if( item.is( 'img' ) ) {

							// Try getting ratio from the image width and height attributes
							if( ( w = item.attr( 'width' ) ) && ( h = item.attr( 'height' ) ) ) {

								ratio = parseInt( w ) / parseInt( h );

							// Fallback, use nativeWidth/nativeHeight
							} else {

								// Get the image ratio, only if it's already loaded
								if( item[0].complete ) {
									ratio = JustifiedGrids.getImageRatio( item[0] );
								}
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
					$( items[i] ).addClass( this.options.failClass );
					return;
				}

				$.data( items[i], this.instanceId, {
					item: items[i], 
					target: target, 
					ratio: ratio, 
					calcWidth: Math.max( this.options.minWidth, ratio * this.options.minHeight )
				});
			}
		}, 

		_doLayout: function( layoutItems ) {

			var items = layoutItems;

			if( ! items || ! items.length ) {
				items = this.visibleItems;
			}

			// Bail if no items supplied
			if( items.length ) {

				var data
				, i, len
				, calculatedWidth
				, attempts = 0
				, elementWidth
				, afterWidth
				, totalWidth
				, sumRatios
				, rows
				, currentRow;

				do {

					// Initialize variables
					totalWidth = 0;
					sumRatios = 0;
					rows = [];
					currentRow = [];

					elementWidth = JustifiedGrids.getWidth( this.element );

					// Remove all breaks first
					if( ! layoutItems ) {
						this.element.find( '.' + this.options.gridBreakClass ).remove();
					}

					// Do the algorithm
					for( i = 0, len = items.length; i < len; i++ ) {

						if( data = $.data( items[i], this.instanceId ) ) {

							/*
								Calculate the width following minHeight and minWidth. 
								We'll try setting all items width and height as long as it doesn't overflow the container.
							*/
							calculatedWidth = Math.min( data.calcWidth, elementWidth );
							calculatedWidth += this.options.margin;

							/* The row is full, generate the row and move the current item to the next row */
							if( totalWidth + calculatedWidth >= elementWidth ) {

								/* 
									Since the last row won't have margins, we'll try substracting the margin from totalWidth and 
									check if it still overflows in case a very wide margin is set.
								*/
								if( totalWidth + calculatedWidth - this.options.margin >= elementWidth ) {

									rows.push({ row: currentRow, totalWidth: totalWidth, sumRatios: sumRatios });
									totalWidth = 0;
									sumRatios = 0;
									currentRow = [];
								}
							}

							// Keep filling the row
							currentRow.push( data );

							// Push the current ratio
							sumRatios += data.ratio;

							// Accumulate the filled row width
							totalWidth += calculatedWidth;
						}
					}

					// If there are still rows left
					if( currentRow.length ) {
						rows.push({ row: currentRow, totalWidth: totalWidth, sumRatios: sumRatios });
					}

					// Now layout all the items in each row
					for( i = 0, len = rows.length; i < len; i++ ) {
						if( i + 1 == len ) {
							this._layoutRow( rows[i], true, this.options.justifyLastRow ? elementWidth : rows[i].totalWidth );
						} else {
							this._layoutRow( rows[i], false, elementWidth );
						}
					}

				} while( elementWidth != JustifiedGrids.getWidth( this.element ) && ++attempts < 3 );
			}
		}, 

		_layoutRow: function( rowData, isLastRow, elementWidth ) {

			var row = rowData.row, 
				sumRatios  = rowData.sumRatios, 
				availableWidth = elementWidth, 
				height, css, curr, 
				i, len = row.length;

			availableWidth = elementWidth;
			if( this.options.margin ) {
				availableWidth -= ( len - 1 ) * this.options.margin;
			}

			height = Math.max( this.options.minHeight, availableWidth / sumRatios );

			// Process the row
			for( i = 0; i < len; i++ ) {

				curr = row[i];

				if( curr.target.length ) {

					css = {};
					css.width = ( height * curr.ratio );
					if( css.width > elementWidth ) {
						css.width = elementWidth;
					}

					if( this.options.assignHeight ) {
						css.height = height;
					}

					curr.target.css( css );
				}

				if( i + 1 == len ) {

					$( curr.item )
						.addClass( this.options.lastColumnClass )
						.css( 'marginRight', '' );

					if( ! isLastRow ) {
						$( curr.item ).after( '<div class="' + this.options.gridBreakClass + '"></div>' );
					}
				} else {
					$( curr.item ).css( 'marginRight', this.options.margin );
				}
			}

			if( isLastRow ) {
				this.visibleLastRow = $( row ).map(function() {
					return this.item;
				}).addClass( this.options.lastRowClass );
			}
		}
	};

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

		elem = $( elem ).get( 0 );
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

})( jQuery, window, document );
