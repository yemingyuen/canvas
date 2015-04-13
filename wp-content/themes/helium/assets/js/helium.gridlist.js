
;(function( $, window, document ) {

	"use strict";

	var _doc  = $( document ), 
		_win  = $( window ), 
		_body = $( document.body );

	// jQuery on an empty object, we are going to use this as our Queue
	var ajaxQueue = $({});

	$.ajaxQueue = function( ajaxOpts ) {
		var jqXHR,
		dfd = $.Deferred(),
		promise = dfd.promise();

		// run the actual query
		function doRequest( next ) {
			jqXHR = $.ajax( ajaxOpts );
			jqXHR.done( dfd.resolve )
				.fail( dfd.reject )
				.then( next, next );
		}

		// queue our ajax request
		ajaxQueue.queue( doRequest );

		// add the abort method
		promise.abort = function( statusText ) {

		// proxy abort to the jqXHR if it is active
		if ( jqXHR ) {
			return jqXHR.abort( statusText );
		}

		// if there wasn't already a jqXHR we need to remove from queue
		var queue = ajaxQueue.queue(),
		index = $.inArray( doRequest, queue );

		if ( index > -1 ) {
			queue.splice( index, 1 );
		}

		// and then reject the deferred
		dfd.rejectWith( ajaxOpts.context || ajaxOpts, [ promise, statusText, "" ] );
			return promise;
		};

		return promise;
	}

	var Helium = window.Helium = window.Helium || {};

	Helium.GridList = function( element, options ) {
		this.element = $( element );
		return this._init( options );
	}

	Helium.GridList.instances = 0;

	Helium.GridList.prototype = {

		_mql: window.matchMedia && window.matchMedia( '(min-width: 992px)' ), 

		_defaults: {
			itemSelector: '.grid', 
			itemsWrapperSelector: '.grid-list-wrap', 
			itemsFilterSelector: '.grid-list-filter', 
			paginationSelector: '.grid-list-nav', 

			layout: 'masonry', 

			pagination: false, 
			ajaxLoadingText: 'Loading', 
			ajaxButtonText: 'Load More', 
			ajaxButtonCompleteText: 'No More Items', 

			beforeAppend: $.noop, 
			afterAppend: $.noop
		}, 

		_init: function( options ) {

			this.options = $.extend( true, {}, this._defaults, options, this._extractOptions( this.element.data() ) );

			this.eventNamespace = '.helium.gridlist._inst' + ( ++Helium.GridList.instances );

			this.itemsFilter  = this.element.find( this.options.itemsFilterSelector );
			this.itemsWrapper = this.element.find( this.options.itemsWrapperSelector );
			this.items        = this.itemsWrapper.find( this.options.itemSelector );
			this.pagination   = this.element.find( this.options.paginationSelector );

			this._createAdapter();
			this._createAjaxNav();
			this._bindHandlers();
		}, 

		_afterFilter: function() {
			if( 'infinite' === this.options.pagination && $.waypoints ) {
				$.waypoints( 'refresh' );
			}
		}, 

		_bindHandlers: function() {

			var self = this, 
				infiniteCallback;

			this._bindImageLoad();

			this.itemsFilter.on( 'click' + this.eventNamespace, '.filter[data-filter]', function( e ) {

				self.itemsFilter.find( '.filter.active' )
					.removeClass( 'active' );
				$( this ).addClass( 'active' );

				self.filter( $( this ).data( 'filter' ) );

				if( ( self._mql && ! self._mql.matches ) || ( ! self._mql && _win.width() < 992 ) ) {
					self.itemsFilter.find( '.filter-items' ).slideUp();
				}
					
				e.preventDefault();
			});

			if( this._mql ) {
				this._mqlListenerProxy = $.proxy( this._mqlListener, this );
				this._mql.addListener( this._mqlListenerProxy );
				this._mqlListener( this._mql );
			} else {
				this._mqlListener({ matches: _win.width() >= 992 });
			}

			if( this.ajaxPagerLink && this.ajaxPagerLink.length ) {

				if( 'infinite' === this.options.pagination ) {
					this._initializeInfinite( infiniteCallback );
					this.ajaxPagerLink.on( 'click' + this.eventNamespace, function(e) {
						e.preventDefault();
					});
				} else {
					this.ajaxPagerLink.on( 'click' + this.eventNamespace, function(e) {
						self._query( this.href, 'ajax' );
						e.preventDefault();
					});
				}
			}
		}, 

		_bindImageLoad: function( items ) {

			var t = this;

			if( items ) {
				items = $( items ).filter( this.options.itemSelector );
			} else {
				items = this.items;
			}

			items.each(function() {
				var img = $( this ).find( 'img' );
				if( img.length ) {
					if( img[0].complete ) {
						img.closest( t.options.itemSelector ).addClass( 'loaded' );
					} else {
						img.one( 'load' + t.eventNamespace, function() {
							img.closest( t.options.itemSelector ).addClass( 'loaded' );
						});
					}
				} else {
					$( this ).addClass( 'loaded' );
				}
			});
		}, 

		_createAdapter: function() {
			var adapter = this.options.layout, adapterOpts = {};
			adapter = adapter.charAt( 0 ).toUpperCase() + adapter.substr( 1 );
			if( Helium.GridList.Adapter.hasOwnProperty( adapter ) ) {
				adapterOpts = $.extend( adapterOpts, {
					selector: this.options.itemSelector, 
					afterAppend: this._updateAjaxPager, 
					afterAppendScope: this, 
					afterFilter: this._afterFilter, 
					afterFilterScope: this
				});
				this.adapter = new Helium.GridList.Adapter[ adapter ]( this, adapterOpts );
			}
		}, 

		_createAjaxNav: function() {

			if( ! this.options.pagination.match( /^(ajax|infinite)$/ ) ) {
				return;
			}

			var selector = '.content-nav .page-numbers:not(.current):not(.next):not(.prev):not(.dots)', 
				navLinks = this.pagination.find( selector );

			this.ajaxLinks = navLinks.map(function() { return this.href; }).get();

			if( this.ajaxLinks.length ) {

				this.ajaxPagerLink = $( document.createElement( 'a' ) )
					.addClass( 'gridlist-ajax-link' )
					.text( this.options.ajaxButtonText )
					.attr( 'href', this.ajaxLinks.shift() );

				this.pagination.find( '.content-nav > ul' ).empty()
					.append( $( '<li class="content-nav-link"></li>' ).append( this.ajaxPagerLink ) );
			}
		}, 

		_extractOptions: function( data ) {
			var options = {};
			$.each( data, function( key, value ) {
				if( /^gridlist(.+)/.test( key ) ) {
					key = key.match( /^gridlist(.+)/ )[1];
					key = key.charAt(0).toLowerCase() + key.substr( 1 );
					options[ key ] = value;
				}
			});

			return options;
		}, 

		_initializeInfinite: function() {

			if( $.fn.waypoint && this.ajaxPagerLink.length ) {

				this.itemsWrapper.waypoint({
					offset: 'bottom-in-view', 
					triggerOnce: true, 
					handler: $.proxy( function( direction ) {
						if( 'down' === direction ) {
							this._query( this.ajaxPagerLink[0].href, 'infinite' );
						}
					}, this )
				});
			}
		}, 

		_mqlListener: function( mql ) {
			if( ! mql.matches ) {
				this.itemsFilter.on( 'click' + this.eventNamespace, '.filter-label', function( e ) {

					$( this ).next( '.filter-items' ).slideToggle();
					e.preventDefault();
				});
			} else {
				this.itemsFilter.off( 'click' + this.eventNamespace, '.filter-label' )
					.find( '.filter-items' ).css( 'display', '' );
			}
		}, 

		_updateAjaxPager: function() {

			if( this.ajaxLinks && this.ajaxPagerLink ) {

				var next;
				if( next = this.ajaxLinks.shift() ) {
					this.ajaxPagerLink
						.attr( 'href', next )
						.text( this.options.ajaxButtonText )
						.closest( '.content-nav-link' )
							.removeClass( 'disabled' );

					if( 'infinite' === this.options.pagination ) {
						this._initializeInfinite();
					}
				} else {
					this.ajaxPagerLink.off( this.eventNamespace )
						.text( this.options.ajaxButtonCompleteText )
						.removeAttr( 'href' )
						.closest( '.content-nav-link' )
							.addClass( 'disabled' );
				}
			}
		}, 

		_query: function( url, mode ) {

			if( ! url ) {
				return;
			}

			$.ajaxQueue({
				type: 'GET', 
				dataType: 'html', 
				url: url, 
				context: this, 
				beforeSend: function() {
					$( this.ajaxPagerLink )
						.text( this.options.ajaxLoadingText )
						.closest( '.content-nav-link' )
							.addClass( 'disabled' );
				}
			}).done(function( response ) {
				var items = $( response )
					.find( this.options.itemsWrapperSelector + ' ' + this.options.itemSelector );
				this.append( items );
			});
		}, 

		append: function( items ) {

			if( this.adapter ) {

				items = $( items ).filter( this.options.itemSelector );

				if( $.isFunction( this.options.beforeAppend ) ) {
					this.options.beforeAppend.apply( this.element, [ this, items ] );
				}

				this.items = this.items.add( items );
				this.itemsWrapper.append( items );
				this._bindImageLoad( items );
				this.adapter._append( items );

				if( $.isFunction( this.options.afterAppend ) ) {
					this.options.afterAppend.apply( this.element, [ this, items ] );
				}
			}
		}, 

		filter: function( filter ) {
			if( this.adapter ) {
				this.adapter._filter( filter );
			}
		}, 

		destroy: function() {

			if( this.adapter ) {
				this.adapter._destroy();
				this.adapter = null;
			}

			if( this.itemsFilter ) {
				this.itemsFilter.off( this.eventNamespace );
			}

			if( this.ajaxPagerLink ) {
				this.ajaxPagerLink.off( this.eventNamespace );
				this.ajaxPagerLink = null;
				this.ajaxLinks = null;
			}

			if( $.fn.waypoint ) {
				this.itemsWrapper.waypoint( 'destroy' );
			}

			this.itemsFilter = null;
			this.itemsWrapper = null;
			this.items = null;
			this.pagination = null;

			if( this._mql ) {
				if( $.isFunction( this._mqlListenerProxy ) ) {
					this._mql.removeListener( this._mqlListenerProxy );
				}
				this._mqlListenerProxy = null;
				this._mql = null;
			}

			$.removeData( this.element.get(0), 'helium.gridlist._inst' );

			this.element = null;
		}
	};

	$.fn.heliumGridList = function( options ) {
		return this.each(function() {
			if( ! $.data( this, 'helium.gridlist._inst' ) ) {
				$.data( this, 'helium.gridlist._inst', new Helium.GridList( this, options ) );
			}
		});
	}

	/* GridList Adapters */

	var Adapter = Helium.GridList.Adapter = function( manager, options ) {
		this.manager = manager;
		return this._init( options );
	};

	// Inheritance method from Backbone.js
	Adapter.extend = function( protoProps, staticProps ) {
		var parent = this;
		var child;

		// The constructor function for the new subclass is either defined by you
		// (the "constructor" property in your `extend` definition), or defaulted
		// by us to simply call the parent's constructor.
		if (protoProps && protoProps.hasOwnProperty( 'constructor' ) ) {
			child = protoProps.constructor;
		} else {
			child = function(){ return parent.apply(this, arguments); };
		}

		// Add static properties to the constructor function, if supplied.
		$.extend( child, parent, staticProps );

		// Set the prototype chain to inherit from `parent`, without calling
		// `parent`'s constructor function.
		var Surrogate = function(){ this.constructor = child; };
		Surrogate.prototype = parent.prototype;
		child.prototype = new Surrogate;

		// Add prototype properties (instance properties) to the subclass,
		// if supplied.
		if (protoProps) $.extend( child.prototype, protoProps );

		// Set a convenience property in case the parent's prototype is needed
		// later.
		child.__super__ = parent.prototype;

		return child;
	};

	Adapter.prototype = {

		_defaults: {
			afterAppend: $.noop, 
			afterAppendScope: window, 
			afterFilter: $.noop, 
			afterFilterScope: window
		}, 

		_init: function( options ) {
			this.options = $.extend( this._defaults, options );
		}, 

		_append: function( items ) {}, 

		_filter: function( filter ) {}, 

		_destroy: function() {
			this.manager = null;
		}
	};

	Helium.GridList.Adapter.Classic = Adapter.extend({

		_append: function( items ) {

			if( items && items.length ) {

				items = this._currentFilter ? 
					items.hide().filter( this._currentFilter ).show() : items;

				if( this._visibleItems ) {
					this._visibleItems = this._visibleItems.add( items );
				}

				if( Helium.GridList.animationEnabled ) {

					if( ! this._timeline ) {
						this._timeline = new TimelineLite({ paused: true });
					}

					this._timeline
						.progress( 1 ).kill().clear( true )
						.staggerFromTo(
							items, 0.2, 
							{ autoAlpha: 0, y: -30 }, 
							{ autoAlpha: 1, y: 0, clearProps: 'visibility,opacity,y' }, 
							0.1, '+=0', 
							this.options.afterAppend, null, 
							this.options.afterAppendScope ).play();
				} else {
					if( $.isFunction( this.options.afterAppend ) ) {
						this.options.afterAppend.apply( this.options.afterAppendScope );
					}
				}
			}
		}, 

		_filter: function( filter ) {

			var itemsWrapper = this.manager.itemsWrapper
				, items = this.manager.items
				, hide, show;

			hide = this._visibleItems || items;
			show = items;
			
			if( 'string' == typeof filter && '*' != filter ) {
				this._currentFilter = filter;
				this._visibleItems = show = items.filter( filter );
			} else {
				this._currentFilter = null;
				this._visibleItems = null;
			}

			if( ! Helium.GridList.animationEnabled ) {
				hide.css( 'display', 'none' );
				show.css( 'display', '' );
				if( $.isFunction( this.options.afterFilter ) ) {
					this.options.afterFilter.apply( this.options.afterFilterScope );
				}
			} else {
				if( ! this._timeline ) {
					this._timeline = new TimelineLite({ paused: true });
				}

				this._timeline
					.progress( 1 ).kill().clear( true )
					.to( hide, 0.2, {
						autoAlpha: 0, 
						y: -30, 
						onComplete: function() {
							hide.css( 'display', 'none' );
							show.css( 'display', '' );
						}
					})
					.staggerFromTo( 
						show, 0.2, 
						{ opacity: 0, y: -30 }, 
						{ autoAlpha: 1, y: 0, clearProps: 'visibility,opacity,y' }, 
						0.1, '+=0', 
						this.options.afterFilter, null, 
						this.options.afterFilterScope ).play();
			}
		}, 

		_destroy: function() {
			this._currentFilter = null;
			this._visibleItems = null;

			if( Helium.GridList.animationEnabled && this._timeline instanceof TimelineLite ) {
				this._timeline.kill().clear( true );
				this._timeline = null;
			}

			Adapter.prototype._destroy.apply( this, arguments );
		}
	});

	Helium.GridList.Adapter.Masonry = Adapter.extend({

		_init: function( options ) {
			Adapter.prototype._init.apply( this, arguments );

			var itemsWrapper = this.manager.itemsWrapper, 
				beforeArrangeWidth;

			itemsWrapper.isotope( $.extend( true, {
				itemSelector: this.manager.options.itemSelector, 
				isInitLayout: false, 
				masonry: {
					columnWidth: '.grid-sizer'
				}
			}, this.options ) );

			beforeArrangeWidth = itemsWrapper.outerWidth();
			itemsWrapper.isotope( 'arrange' );

			if( beforeArrangeWidth != itemsWrapper.outerWidth() ) {
				itemsWrapper.isotope( 'arrange' );
			}
		}, 

		_append: function( items ) {
			if( items && items.length ) {
				this.manager.itemsWrapper.isotope( 'once', 'layoutComplete', 
					$.proxy( this.options.afterAppend, this.options.afterAppendScope ) );
				this.manager.itemsWrapper.isotope( 'appended', items );
			}
		}, 

		_filter: function( filter ) {
			this.manager.itemsWrapper.isotope( 'once', 'layoutComplete', 
				$.proxy( this.options.afterFilter, this.options.afterFilterScope ) );
			this.manager.itemsWrapper.isotope({ filter: filter });
		}, 

		_destroy: function() {
			this.manager.itemsWrapper.isotope( 'destroy' );
			Adapter.prototype._destroy.apply( this, arguments );
		}
	});

	Helium.GridList.Adapter.Justified = Adapter.extend({

		_currentFilter: null, 

		_init: function( options ) {
			Adapter.prototype._init.apply( this, arguments );

			this.manager.itemsWrapper.justifiedGrids( $.extend( true, {
				selector: this.manager.options.itemSelector, 
				margin: 30, 
				ratio: 'img',
				assignHeight: false
			}, this.options ) );
		}, 

		_append: function( items ) {

			if( items && items.length ) {

				var lastRowItems = this.manager.itemsWrapper.justifiedGrids( 'getLastRow' ), 
					visibleItems = this._currentFilter ? items.filter( this._currentFilter ) : items;

				// Hide all items first
				items.hide();

				if( Helium.GridList.animationEnabled && visibleItems.length && lastRowItems.length ) {

					TweenLite.to( lastRowItems, 0.2, {
						autoAlpha: 0, y: -30, 
						onCompleteParams: [ items ], 
						onCompleteScope: this, 
						onComplete: function( items ) {

							var itemsNeedLayout = this.manager.itemsWrapper.justifiedGrids( 'append', items, true );
								
							if( itemsNeedLayout.length ) {
								if( ! this._timeline ) {
									this._timeline = new TimelineLite({ paused: true });
								}

								this._timeline
									.progress( 1 ).kill().clear( true )
									.staggerFromTo( 
										itemsNeedLayout, 0.2, 
										{ autoAlpha: 0, y: -30 }, 
										{ autoAlpha: 1, y: 0, clearProps: 'visibility,opacity,y' }, 
										0.1, '+=0', 
										this.options.afterAppend, null, 
										this.options.afterAppendScope ).play();
							}
						}
					});
				} else {
					this.manager.itemsWrapper.justifiedGrids( 'append', items, true );
					if( $.isFunction( this.options.afterAppend ) ) {
						this.options.afterAppend.apply( this.options.afterAppendScope );
					}
				}
			}
		}, 

		_filter: function( filter ) {

			if( ! this._currentFilter || this._currentFilter != filter ) {

				var itemsWrapper = this.manager.itemsWrapper, 
					hide, show;

				this._currentFilter = filter;

				if( ! Helium.GridList.animationEnabled ) {
					this.manager.itemsWrapper.justifiedGrids( 'filter', filter );
					if( $.isFunction( this.options.afterFilter ) ) {
						this.options.afterFilter.apply( this.options.afterFilterScope );
					}
				} else {

					hide = itemsWrapper.justifiedGrids( 'getItems', true );
					show = itemsWrapper.justifiedGrids( 'getItems' ).filter( filter );

					if( ! this._timeline ) {
						this._timeline = new TimelineLite({ paused: true });
					}

					this._timeline
						.progress( 1 ).kill().clear( true )
						.to( hide, 0.2, {
							autoAlpha: 0, 
							y: -30, 
							onComplete: function() {
								itemsWrapper.justifiedGrids( 'filter', filter );
							}
						})
						.staggerFromTo( 
							show, 0.2, 
							{ opacity: 0, y: -30 }, 
							{ autoAlpha: 1, y: 0, clearProps: 'visibility,opacity,y' }, 
							0.1, '+=0', 
							this.options.afterFilter, null, 
							this.options.afterFilterScope ).play();
				}
			}
		}, 

		_destroy: function() {
			this._currentFilter = null;
			this.manager.itemsWrapper.justifiedGrids( 'destroy' );

			if( Helium.GridList.animationEnabled && this._timeline instanceof TimelineLite ) {
				this._timeline.kill().clear( true );
				this._timeline = null;
			}

			Adapter.prototype._destroy.apply( this, arguments );
		}
	});

	$(function() {
		Helium.GridList.animationEnabled = ( 'undefined' !== typeof TimelineLite ) && ! Helium.isMobile;
	});

	/* EOF */

}) ( jQuery, window, document );