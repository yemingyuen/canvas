
;(function( $, window, document ) {

	"use strict";

	var _doc  = $( document ), 
		_win  = $( window ), 
		_body = $( document.body );

	var Helium = window.Helium = window.Helium || {};

	$.extend( Helium, {

		isHandheld: !! ( bowser.mobile || bowser.tablet ), 

		mql: window.matchMedia && window.matchMedia( '(min-width: 992px)' ), 

		init: function() {
			
			/* ==========================================================================
				Add Mobile Device class
			============================================================================= */
			if( ! Helium.isHandheld ) {
				$( 'html' ).addClass( 'desktop' );
			}

			/* ==========================================================================
				Setup Listeners
			============================================================================= */
			Helium.setupListeners();

			/* ==========================================================================
				Wait for Document.Ready
			============================================================================= */
			$( Helium.ready );
		}, 

		ready: function() {

			/* ==========================================================================
				Apply any patches/fixes
			============================================================================= */
			Helium.applyPatches();

			/* ==========================================================================
				AJAX Navigation
			============================================================================= */

			(function() {

				// Make sure AJAX navigation is enabled
				if( ! _helium.ajaxNavigation ) {
					return;
				}

				var rootUrl = History.getRootUrl()

				, isLoading = false

				, splitText = null

				, ajaxPreloader = document.createElement( 'span' )

				, contentAreaWrap = $( '.content-area-wrap' )

				, appendLoadingIndicator = function() {

					// Make sure the ajax
					if( isLoading && ! ajaxPreloader.parentNode ) {

						// Replace content title with loading text
						contentAreaWrap.find( '.content-header .content-title' )
							.html( ajaxPreloader )
							.transition({
								opacity: 1, 
								duration: 350, 
								ease: 'easeOutQuad', 
								complete: playLoadingIndicator
							});
					}
				}

				, playLoadingIndicator = function( backwards ) {

					if( isLoading ) {

						if( ! splitText ) {
							splitText = new SplitText( ajaxPreloader, { type: 'words,chars' } );
						}

						var chars = splitText.chars, 
							lastIndex = chars.length - 1, 
							target = ( backwards ? chars : Array.prototype.slice.call( chars ).reverse() );

						$.each( target, function( index ) {
							$( this ).transition({
								opacity: backwards ? 1 : 0, 
								x: backwards ? 0 : 10, 
								delay: ( index * 100 ), 
								duration: 400, 
								complete: ( index == lastIndex ? function() {
									playLoadingIndicator( ! backwards );
								} : $.noop )
							});
						});
					}
				}

				, hideOldContent = function() {

					// Get current elements
					contentAreaWrap.find( '.content-area .content-wrap' )
						.add( contentAreaWrap.find( '.content-area .content-header-affix' ).children() )
						.transition({
							opacity: 0, 
							duration: 350, 
							ease: 'easeOutQuad', 
							complete: appendLoadingIndicator
						});
				}

				, replaceOldContent = function( newContent ) {

					// Remove preloader from the DOM
					if( ajaxPreloader.parentNode ) {
						ajaxPreloader.parentNode.removeChild( ajaxPreloader );
					}

					// Reset ajax preloader
					if( splitText instanceof SplitText ) {
						$( splitText.chars ).css({ opacity: '', transition: '', transform: '' });
					}

					// Setup new content
					Helium.teardown( contentAreaWrap );
					Helium.setup( contentAreaWrap.html( newContent ) );

					// Force repaint
					contentAreaWrap[0].offsetHeight;

					// Animate
					contentAreaWrap.find( '.content-area' )
						.removeClass( 'beforeload' );

					// Stop loading
					isLoading = false;
				};

				// Assign ajax preloader text
				ajaxPreloader.innerHTML = _helium.ajaxNavigation.loadingText;

				// Initialize Ajax Navigator
				Helium.AjaxNavigator.init({

					linkSelector: [
						'a.ajaxify', 
						'.header .brand a', 
						'.main-nav .menu a', 
						'.portfolio a.portfolio-info-link', 
						'.pages-nav a', 
						'.post-tags a', 
						'.content-nav a', 
						'.post-title a', 
						'.post-meta a', 
						'.post-media a', 
						'.post-body a.more-link', 
						'.related-item-media a:not(.mfp-image)', 
						'.related-item-title a', 
						'.related-item-meta a', 
						'.edd-download-title a', 
						'a.edd-download-view-details', 
						'.grid-list a.grid-list-image-link.grid-list-page', 
						'.featured-portfolio-slider .entry-link', 
						'.search-entry-post-type a', 
						'.search-entry-title a'
					].join( ', ' ), 

					excludeUrls: _helium.ajaxNavigation.excludeUrls, 

					bodyClassReplacer: function( className ) {

						if( _body.hasClass( 'nav-open' ) ) {
							className = [ className, 'nav-open' ].join( ' ' );
						}

						_body[0].className = className;
					}, 

					stateChange: function( url, callback ) {

						isLoading = true;

						if( _win.scrollTop() == 0 || ! _helium.ajaxNavigation.scrollTop ) {
							hideOldContent();
						} else {
							$( 'html,body' ).finish().animate(
								{ scrollTop: 0 }, 500, 
								function() { hideOldContent(); callback(); }
							);
							return false;
						}
					}, 

					done: function( dom, url ) {

						var menu, 
							responseObj = $( dom ), 
							responseContentArea = responseObj.find( '.content-area' ), 
							responseMenuItems   = responseObj.find( '.main-nav .menu .menu-item' )

						// setTimeout(function() {

							// Match menu item classes with the response
							responseMenuItems.each(function() {
								if( this.id && ( menu = document.getElementById( this.id ) ) ) {
									if( $( menu ).hasClass( 'sub-menu-open' ) ) {
										$( this ).addClass( 'sub-menu-open' );
									}
									menu.className = this.className;
								}
							});

							// Hide the new content first
							responseContentArea.addClass( 'beforeload' );

							// Replace the old content with the new one
							replaceOldContent( responseContentArea[0].outerHTML );

							// Inform Google Analytics of the change
							if ( typeof window._gaq !== 'undefined' ) {
								window._gaq.push([ '_trackPageview', url.replace( rootUrl, '' ) ]);
							}

						// }, Math.random() * 2000 + 1200 );
					}
				});

			})();

			/* ==========================================================================
				Contextual Setups
			============================================================================= */

			Helium.setup( this, true );

			/* ==========================================================================
				Fire initial window resize callback
			============================================================================= */

			Helium.onResize();
		}, 

		onResize: function() {

			/* ==========================================================================
				Fullscreen Content Area
			============================================================================= */

			Helium.adjustFullscreenContent( _body );

			/* ==========================================================================
				Restore Menu
			============================================================================= */
			$( '.main-nav' ).find( 'ul' ).css( 'display', '' )
				.end().find( '.sub-menu-open' ).removeClass( 'sub-menu-open' );
		}, 

		onMqlChange: function( mql ) {

			// Unbind event handlers
			_doc.off( '.hoverIntent .helium.nav' );

			if( mql.matches ) {

				/* ==========================================================================
					Desktop Hover Intent
				============================================================================= */

				if( $.fn.hoverIntent ) {

					_doc.hoverIntent({
						over: function() {
							$( this ).children( 'ul.sub-menu' ).finish().slideDown()
								.closest( '.menu-item' ).addClass( 'sub-menu-open' );
						}, 
						out: $.noop, 
						selector: '.main-nav .menu-item-has-children'
					});
				}

				/* ==========================================================================
					Close Submenus
				============================================================================= */

				_doc.on( 'click.helium.nav', '.menu-item.sub-menu-open .subnav-close', function(e) {
					$( this ).closest( '.menu-item' ).removeClass( 'sub-menu-open' );
					$( this ).siblings( 'ul.sub-menu' )
						.finish().slideUp(function() {
							$( this ).find( 'ul.sub-menu' ).hide();
							$( this ).find( '.sub-menu-open' )
								.addBack().removeClass( 'sub-menu-open' );
						});
				});

			} else {

				/* ==========================================================================
					Mobile Navigation Toggle
				============================================================================= */

				_doc.on( 'click.helium.nav', '.header-toggle', function(e) {

					_body.toggleClass( 'header-open' );
					e.preventDefault();

				}).on( 'click.helium.nav', '.main-nav .menu-item a', function() {
					_body.removeClass( 'header-open' );
				});
			}
		}, 

		setupListeners: function() {

			/* ==========================================================================
				Window.Resize
			============================================================================= */

			_win.on( 'resize.helium orientationchange.helium', Helium.onResize );

			/* ==========================================================================
				Easy Digital Downloads Cart Events
			============================================================================= */

			_body.on( 'edd_cart_item_added', function(e, response) {
				var qty = $( '<div></div>' ).append( response.cart_item ).find( '.edd-cart-item' ).data( 'cart-quantity' );
				$( '.header-links .edd-shopping-cart .header-links-tooltip' ).text( qty );
			});

			/* ==========================================================================
				Back to Top
			============================================================================= */

			_doc.on( 'click.helium.btt', '.back-to-top > .btn', function( e ) {
				$( 'html,body' ).finish().animate({ scrollTop: 0 }, 500 );
				e.preventDefault();
			});

			/* ==========================================================================
				Search Form Modal
			============================================================================= */

			(function() {

				var openSearchForm = function() {
					if( $( '.search-wrap .search-form' ).length ) {
						$( '.search-wrap .search-form' )[0].reset();
					}
					_body.addClass( 'search-open' );
					_doc.on( 'keyup.helium.search', function( e ) {
						if( e.keyCode == 27 ) {
							closeSearchForm();
						}
					});
				}, 
				closeSearchForm = function() {
					_body.removeClass( 'search-open' );
					_doc.off( 'keyup.helium.search' );
				};

				_doc.on( 'click.helium.search', '.header-links .ajax-search-link a', function(e) {
					openSearchForm();
					e.preventDefault();
				});

				if( _helium.ajaxNavigation ) {
					_doc.on( 'submit', '.search-open .search-wrap .search-form', function(e) {
						var url = _helium.homeUrl + '?s=' + $( '.form-control', this ).val();
						closeSearchForm();
						History && History.pushState( null, null, url );
						e.preventDefault();
					});
				}
			})();

			/* ==========================================================================
				MediaQueryList Listener
			============================================================================= */

			if( Helium.mql ) {
				Helium.mql.addListener( Helium.onMqlChange );
				Helium.onMqlChange( Helium.mql );
			}
		}, 

		adjustFullscreenContent: function( context ) {

			$( '.content-area.fullscreen .content-wrap', context ).each(function() {
				$( this ).css({
					height: _win.height() - ( Helium.mql.matches ? 0 : $( this ).offset().top  )
				});
			});

		}, 

		setup: function( context, isInit ) {

			context = $( context );

			if( ! context.length )
				context = _body;

			/* ==========================================================================
				Fullscreen Content Area
			============================================================================= */

			Helium.adjustFullscreenContent( context );

			/* ==========================================================================
				Royal Slider
			============================================================================= */

			if( $.fn.royalSlider ) {
				$( '.royalSlider', context ).each(function() {
					$( this ).royalSlider( $.extend( true, {}, $( this ).data( 'rs-settings' ), {
						slidesSpacing: 0, 
						imageScalePadding: 0, 
						keyboardNavEnabled: true, 
						addActiveClass: true
					}));
				});
			}
			
			/* ==========================================================================
				Isotope Galleries
			============================================================================= */

			if( $.fn.isotope ) {
				$( '.gallery' ).each(function() {
					var gallery = $( this );
					gallery.imagesLoaded(function() {
						gallery.isotope();
					});
				});
			}

			/* ==========================================================================
				Justified Grids
			============================================================================= */
			
			if( $.fn.justifiedGrids ) {
				$( '.justified-grids', context ).justifiedGrids();
			}

			/* ==========================================================================
				GridLists
			============================================================================= */

			if( $.fn.heliumGridList ) {
				$( '.grid-list', context ).heliumGridList({
					afterAppend: function( instance, items ) {
						if( $( this ).is( '.edd-download-grid' ) ) {
							$( '.edd-no-js', items ).hide();
							$( 'a.edd-add-to-cart', items ).addClass( 'edd-has-js' );
						}
					}
				});
			}

			/* ==========================================================================
				Cycle 2
			============================================================================= */

			if( $.fn.cycle ) {
				$( '.cycle-slider', context ).cycle();
			}

			/* ==========================================================================
				MFP Galleries
			============================================================================= */

			if( $.fn.magnificPopup ) {
				$.each({
					'.gallery': '.gallery-item a', 
					'.grid-list-wrap': '.grid:visible .grid-list-image-link.grid-list-mfp', 
					'.related-items': '.related-item-media a.mfp-image', 
					'a.mfp-image': false
				}, function( selector, delegate ) {
					$( selector, context ).each(function() {
						$( this ).magnificPopup({
							delegate: delegate, 
							type: 'image', 
							gallery: delegate ? {
								enabled: true, 
								navigateByImgClick: true
							} : false
						});
					});
				});
			}

			/* ==========================================================================
				Team Popup
			============================================================================= */
			
			if( $.fn.magnificPopup ) {

				// var source = [
				// 	'{{#photo}}', 
				// 	'<figure class="team-photo">', 
				// 		'<img src="{{photo}}" alt="{{name}}">', 
				// 	'</figure>', 
				// 	'{{/photo}}', 
				// 	'<div class="team-info">', 
				// 		'<div class="team-header">', 
				// 			'<h3 class="team-name">{{name}}</h3>', 
				// 			'{{#role}}<p class="team-role">{{role}}</p>{{/role}}', 
				// 		'</div>', 
				// 		'{{#content}}', 
				// 		'<div class="team-description">', 
				// 			'{{content}}', 
				// 		'</div>', 
				// 		'{{/content}}', 
				// 		'{{#has_social}}', 
				// 		'<div class="team-social">', 
				// 			'<ul class="inline-list">', 
				// 				'{{#social_profiles}}', 
				// 				'<li><a href="{{url}}"><i class="{{icon}}"></i></a></li>', 
				// 				'{{/social_profiles}}', 
				// 			'</ul>', 
				// 		'</div>', 
				// 		'{{/has_social}}', 
				// 	'</div>'
				// ].join('');

				// console.log( Hogan.compile(source, {asString: true } ) );

				Helium.TeamTemplate = Helium.TeamTemplate || new Hogan.Template(function(c,p,i){var _=this;_.b(i=i||"");if(_.s(_.f("photo",c,p,1),c,p,0,10,82,"{{ }}")){_.rs(c,p,function(c,p,_){_.b("<figure class=\"team-photo\"><img src=\"");_.b(_.v(_.f("photo",c,p,0)));_.b("\" alt=\"");_.b(_.v(_.f("name",c,p,0)));_.b("\"></figure>");});c.pop();}_.b("<div class=\"team-info\"><div class=\"team-header\"><h3 class=\"team-name\">");_.b(_.v(_.f("name",c,p,0)));_.b("</h3>");if(_.s(_.f("role",c,p,1),c,p,0,184,217,"{{ }}")){_.rs(c,p,function(c,p,_){_.b("<p class=\"team-role\">");_.b(_.v(_.f("role",c,p,0)));_.b("</p>");});c.pop();}_.b("</div>");if(_.s(_.f("content",c,p,1),c,p,0,244,291,"{{ }}")){_.rs(c,p,function(c,p,_){_.b("<div class=\"team-description\">");_.b(_.v(_.f("content",c,p,0)));_.b("</div>");});c.pop();}if(_.s(_.f("has_social",c,p,1),c,p,0,318,453,"{{ }}")){_.rs(c,p,function(c,p,_){_.b("<div class=\"team-social\"><ul class=\"inline-list\">");if(_.s(_.f("social_profiles",c,p,1),c,p,0,367,422,"{{ }}")){_.rs(c,p,function(c,p,_){_.b("<li><a href=\"");_.b(_.v(_.f("url",c,p,0)));_.b("\"><i class=\"");_.b(_.v(_.f("icon",c,p,0)));_.b("\"></i></a></li>");});c.pop();}_.b("</ul></div>");});c.pop();}_.b("</div>");return _.fl();;});

				$( '.team .team-photo a', context ).magnificPopup({
					gallery: {
						enabled: true
					}, 
					inline: {
						markup: '<div class="team-popup"></div>'
					}, 
					callbacks: {
						elementParse: function( item ) {
							if( item.el ) {
								var data = $( item.el ).closest( '.team' ).data( 'team-data' );
								if( data ) {
									item = $.extend( true, item, { data: data });

									delete item.src;
									delete item.el;
								}
							}
							return item;
						}, 
						markupParse: function( template, values, item ) {
							template.empty().html( Helium.TeamTemplate.render( values ) );
						}
					}
				});
			}

			/* ==========================================================================
				FitVids
			============================================================================= */

			if( $.fn.fitVids ) {
				$( '.featured-content, .post-media', context ).fitVids();
			}

			/* ==========================================================================
				Google Maps
			============================================================================= */

			if( $.fn.youxiGoogleMaps ) {
				$( '.google-maps', context ).youxiGoogleMaps();
			}

			/* ==========================================================================
				AddThis
			============================================================================= */

			if( typeof addthis !== 'undefined' ) {
				addthis.toolbox( '.addthis_toolbox' );
			}

			/* ==========================================================================
				Easy Digital Downloads
			============================================================================= */

			if( ! isInit && _helium.edd ) {
				
				if( ! _helium.edd.ajaxDisabled ) {
					$( '.edd-no-js', context ).hide();
					$( 'a.edd-add-to-cart', context ).addClass( 'edd-has-js' );
				}

				if( window.edd_scripts ) {
					var isCheckout = ( _helium.edd.checkoutPage == window.location.href );
					window.edd_scripts.redirect_to_checkout = _helium.edd.straightToCheckout || isCheckout ? '1' : '0';
				}
			}

			/* ==========================================================================
				Contact Form 7
			============================================================================= */

			if( ! isInit ) {
				if( $.fn.wpcf7InitForm ) {
					$( 'div.wpcf7 > form', context ).wpcf7InitForm();
				}
			}

			/* ==========================================================================
				MEJS
			============================================================================= */

			! isInit && $.fn.mediaelementplayer && (function() {

				var settings = {};

				if ( typeof _wpmejsSettings !== 'undefined' ) {
					settings = _wpmejsSettings;
				}

				settings.success = function (mejs) {
					var autoplay, loop;

					if ( 'flash' === mejs.pluginType ) {
						autoplay = mejs.attributes.autoplay && 'false' !== mejs.attributes.autoplay;
						loop = mejs.attributes.loop && 'false' !== mejs.attributes.loop;

						autoplay && mejs.addEventListener( 'canplay', function () {
							mejs.play();
						}, false );

						loop && mejs.addEventListener( 'ended', function () {
							mejs.play();
						}, false );
					}
				};

				$( '.wp-audio-shortcode, .wp-video-shortcode', context ).mediaelementplayer( settings );
			})();
		}, 

		teardown: function( context ) {

			context = $( context );

			if( ! context.length )
				context = _body;

			/* ==========================================================================
				Royal Slider
			============================================================================= */

			if( $.fn.royalSlider ) {
				$( '.royalSlider', context ).each(function() {
					var api = $( this ).data( 'royalSlider' );
					api && api.destroy();
				});
			}
			
			/* ==========================================================================
				Justified Grids
			============================================================================= */
			
			if( $.fn.justifiedGrids ) {
				$( '.justified-grids', context ).justifiedGrids( 'destroy' );
			}

			/* ==========================================================================
				Cycle 2
			============================================================================= */
			if( $.fn.cycle ) {
				$( '.cycle-slider', context ).cycle( 'destroy' );
			}
			
			/* ==========================================================================
				Isotope Galleries
			============================================================================= */

			if( $.fn.isotope ) {
				$( '.gallery' ).isotope( 'destroy' );
			}

			/* ==========================================================================
				Grid List
			============================================================================= */

			$( '.grid-list', context ).each(function() {
				var api = $.data( this, 'helium.gridlist._inst' );
				if( api instanceof Helium.GridList ) {
					api.destroy();
				}
			});

			/* ==========================================================================
				MFP (clear cached instances)
			============================================================================= */

			if( $.magnificPopup ) {
				$.magnificPopup.instance.close();
				$.magnificPopup.instance.items = null;
				$.magnificPopup.instance.ev = null;
				$.magnificPopup.instance.st = null;
				$.magnificPopup.instance.contentContainer = null;
			}

			/* ==========================================================================
				Google Maps
			============================================================================= */

			if( $.fn.youxiGoogleMaps ) {
				$( '.google-maps', context ).youxiGoogleMaps( 'destroy' );
			}

			/* ==========================================================================
				Contact Form 7
			============================================================================= */

			if( $.fn.ajaxFormUnbind ) {
				$( 'div.wpcf7 > form', context )
					.ajaxFormUnbind().unbind( '.form-plugin' );
			}

			/* ==========================================================================
				MEJS
			============================================================================= */

			if( $.fn.mediaelementplayer ) {
				$( '.wp-audio-shortcode, .wp-video-shortcode', context )
					.mediaelementplayer( false );
			}
		}, 

		windowLoad: function() {}, 

		applyPatches: function() {

			/* ==========================================================================
				Media Element JS Patch for Fluid Video Players
			============================================================================= */

			(function( oldFn ) {

				$.fn.mediaelementplayer = function( options ) {
					if( false !== options ) {

						this.filter( '.wp-video-shortcode' )
							.css({ width: '100%', height: '100%' });

						options = $.extend( options, {
							audioHeight: 36
						});
					}

					return oldFn.apply( this, [ options ] );
				};
			})( $.fn.mediaelementplayer );
		}
	});

	Helium.AjaxNavigator = {

		defaults: {
			stateChange: $.noop, 
			done: $.noop, 
			fail: $.noop, 
			always: $.noop, 

			titleReplacer: null, 
			bodyClassReplacer: null, 

			stateChangeContext: null, 
			doneContext: null, 
			failContext: null, 
			alwaysContext: null, 

			titleReplacerContext: null, 
			bodyClassReplacerContext: null, 

			ajaxParams: {}, 
			linkSelector: 'a:not(.no-ajaxify)', 
			excludeUrls: []
		}, 

		_hash: (function() {
			return 'tag-' + Math.random().toString( 36 ).substr( 2, 9 );
		}), 

		init: function( options ) {

			if( window.History && window.History.enabled ) {

				this.options = $.extend( true, {}, this.defaults, options );

				_doc.on( 'click.ajaxnavigator', this.options.linkSelector, $.proxy( this.onClick, this ) );

				_win.on( 'statechange.ajaxnavigator', $.proxy( this.onStateChange, this ) );
			}
		}, 

		onClick: function( event ) {
			
			var link = event.currentTarget, 
				rootUrl = History.getRootUrl();

			if( 
				// Check for excluded Urls
				( $.map( this.options.excludeUrls || [], function( url ) { if( link.href.substring( 0, url.length ) === url ) return url; }).length ) || 

				// Only handle clicks from internal links
				( link.href.substring( 0, rootUrl.length ) !== rootUrl && link.href.indexOf( ':' ) > -1 ) || 

				// Middle click, cmd click, and ctrl click should open
				// links in a new tab as normal.
				( event.which > 1 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey ) || 

				// Ignore cross origin links
				( location.protocol !== link.protocol || location.hostname !== link.hostname ) || 

				// Ignore anchors on the same page
				( link.hash && link.href.replace( link.hash, '' ) === location.href.replace( location.hash, '' ) ) || 

				// Ignore empty anchor "foo.html#"
				( link.href === location.href + '#' ) || 

				// Ignore event with default prevented
				event.isDefaultPrevented()

			) {
				return;
			}

			History.pushState( null, null, link.href );

			event.preventDefault();
		}, 

		onStateChange: function( event ) {

			var state = History.getState(), 
				doRequestCallback = $.proxy( function() {
					this._doRequest.apply( this, [ state.url, this.options.ajaxParams ] );
				}, this );

			if( $.isFunction( this.options.stateChange ) ) {

				// Return false to do the request manually using the supplied callback as argument
				if( false === this.options.stateChange.apply( this.options.stateChangeContext || this, [ state.url, doRequestCallback ] ) ) {
					return;
				}
			}

			doRequestCallback();
		}, 

		_documentHtml: function( html, hash ) {

			// Prepare
			var result = String( html )
				.replace( /<(html|head|title|body)([\s\>])/gi, '<div data-' + hash + '="$1"$2' )
				.replace( /<\/(html|head|title|body)\>/gi, '</div>' )
			;

			// Return
			return $.trim( result );
		}, 

		_doRequest: function( url, params ) {

			$.ajax( url, $.extend( params, {
				context: this, 
				dataType: 'html', 
				type: 'GET'
			})).done( function( response ) {

				// Parse the returned HTML
				var _documentClean = $( document.createElement( 'div' ) ).append( $.parseHTML( response, true ) ), 
					_title         = _documentClean.find( 'title' ), 
					_body          = _documentClean.find( 'body' ), 
					_scripts       = _documentClean.find( 'script[src]' );

				// Since jQuery.parseHTML may remove <title>, <head>, or <body> we need to parse the manually
				var _hash     = this._hash(), 
					_document = $( this._documentHtml( response, _hash ) );

				// If <title> was stripped out by jQuery.parseHTML
				if( ! _title.length ) {
					_title = _document.find( '[data-' + _hash + '="title"]' );
				}

				// If <body> was stripped out by jQuery.parseHTML
				if( ! _body.length ) {
					_body = _document.find( '[data-' + _hash + '="body"]' );
				}

				// Replace document title
				if( _title.length ) {

					if( $.isFunction( this.options.titleReplacer ) ) {
						this.options.titleReplacer.apply( this.options.titleReplacerContext, [ _title.text() ] );
					} else {
						document.title = _title.text();
					}
				}

				// Replace body classes
				if( _body.length ) {

					if( $.isFunction( this.options.bodyClassReplacer ) ) {
						this.options.bodyClassReplacer.apply( this.options.bodyClassReplacerContext, [ _body[0].className ] );
					} else {
						document.body.className = _body[0].className;
					}
				}

				// Pass the jQuery parsed body contents to external callback for further processing
				if( $.isFunction( this.options.done ) ) {
					this.options.done.apply( this.options.doneContext || this, 
						[ $( '<div></div>' ).append( _documentClean ).html(), url ] );
				}
			})
			.fail( $.proxy( this.options.fail, this.options.failContext ) )
			.always( $.proxy( this.options.always, this.options.alwaysContext ) );

		}
	};

	Helium.init();

	/* ==========================================================================
		Window.Load
	============================================================================= */
	_win.load( Helium.windowLoad );

	/* EOF */

}) ( jQuery, window, document );