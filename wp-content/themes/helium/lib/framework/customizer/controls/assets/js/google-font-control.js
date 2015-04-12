
;(function( wp, $ ) {

	var api = wp.customize
	if( api ) {

		api.Youxi = api.Youxi || {};
		api.Youxi.GoogleFontControl = api.Control.extend({

			createOption: function( variant ) {
				return $( document.createElement( 'option' ) )
					.attr( 'value', variant ).text( variant );
			}, 

			createCheckbox: function( subset ) {
				return $( document.createElement( 'label' ) )
					.html( '<input type="checkbox" value="' + subset + '">' + subset + '<br>' );
			}, 

			getValue: function() {
				var value = [], 
					family = this.familyDropdown.val(), 
					variant = this.variantDropdown.val(), 
					subsets = this.subsetsContainer.find( ':checkbox:checked' ).map(function() {
						return this.value;
					}).get();

				if( family ) {

					value.push( family );
					if( variant ) {
						value.push( variant );
					}

					return value.join( ':' ) + ( subsets.length ? ( '&subset=' + subsets.join( ',' ) ) : '' );
				}

				return '';
			}, 

			updateVariants: function( family ) {

				var font, control = this;
				this.variantDropdown.children().first().nextAll().remove();

				if( family && ( font = api.Youxi.GoogleFontControl.Fonts[ family ] ) ) {
					this.variantDropdown.append( $.map( font.variants || [], control.createOption ) )
						.prop( 'disabled', false ).show();
				} else {
					this.variantDropdown.prop( 'disabled', true ).hide();
				}
			}, 

			updateSubsets: function( family ) {

				var font, control = this;
				this.subsetsContainer.empty();

				if( family && ( font = api.Youxi.GoogleFontControl.Fonts[ family ] ) ) {
					this.subsetsContainer.html( $.map( font.subsets || [], control.createCheckbox ) ).show();
				} else {
					this.subsetsContainer.hide();
				}
			}, 

			ready: function() {

				var control = this;

				this.familyDropdown  = $( '.youxi-google-font-family', this.container );
				this.variantDropdown = $( '.youxi-google-font-variant', this.container );
				this.subsetsContainer = $( '.youxi-google-font-subsets', this.container );

				this.dropdowns = this.familyDropdown
					.add( this.variantDropdown );

				this.familyDropdown.on( 'change', function() {
					if( control.currentFamily != this.value ) {
						control.currentFamily = this.value;
						control.updateVariants( this.value );
						control.updateSubsets( this.value );
					}
				});

				this.dropdowns.on( 'change', function() {
					control.setting.set( control.getValue() );
				});

				this.subsetsContainer.on( 'change', ':checkbox', function() {
					control.setting.set( control.getValue() );
				});
			}
		}, {
			Fonts: _youxiCustomizeGoogleFonts || {}
		});

		$.extend( api.controlConstructor, { youxi_google_font: api.Youxi.GoogleFontControl });
	}

})( window.wp, jQuery );