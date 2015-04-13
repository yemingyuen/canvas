
;(function( wp, $ ) {

	var api = wp.customize
	if( api ) {

		api.Youxi = api.Youxi || {};
		api.Youxi.GoogleFontControl = api.Control.extend({

			getValue: function() {
				var value = [], 
					family = this.familyDropdown.val(), 
					variant = this.variantDropdown.val();

				if( family ) {
					value.push( family );
					if( variant ) {
						value.push( variant );
					}
				}

				return value.join( ':' );
			}, 

			updateVariants: function( family, value ) {
				var font;
				if( font = api.Youxi.GoogleFontControl.Fonts[ family ] ) {

					this.variantDropdown.prop( 'disabled', false );
					this.variantDropdown.children().first().nextAll().remove();
					this.variantDropdown.append( $.map( font.variants || [], function( variant ) {
						return $( '<option></option>' ).attr({
							value: variant
						}).text( variant );
					})).val( value );

				} else {
					this.variantDropdown.val( '' ).prop( 'disabled', true );
					this.setting.set( '' );
				}
			}, 

			updateValue: function() {
				this.setting.set( this.getValue() );
			}, 

			ready: function() {
				this.familyDropdown = this.container.find( '.youxi-google-font-family' ), 
				this.variantDropdown = this.container.find( '.youxi-google-font-variant' );

				var control = this, 
					setting = this.setting().toString().split( ':' );

				if( setting.length > 0 ) {
					this.familyDropdown.val( setting[0] );
					this.updateVariants( setting[0], setting.length > 1 ? setting[1] : '' );
					this.updateValue();
				}

				this.familyDropdown.on( 'change', function() {
					control.updateVariants( this.value );
					control.updateValue();
				});
				this.variantDropdown.on( 'change', function() {
					control.updateValue();
				});
			}
		}, {
			Fonts: _youxiCustomizeGoogleFonts || {}
		});

		$.extend( api.controlConstructor, { youxi_google_font: api.Youxi.GoogleFontControl });
	}

})( window.wp, jQuery );