<?php
if ( ! defined( 'ABSPATH' ) ) {
	die();
}

/**
 * Youxi Customize Google Font Control
 *
 * This class adds a Google Font control to WordPress customizer
 *
 * @package   Youxi Themes Theme Utils
 * @author    Mairel Theafila <maimairel@yahoo.com>
 * @copyright Copyright (c) 2014-2015, Mairel Theafila
 */

class Youxi_Customize_Google_Font_Control extends WP_Customize_Control {

	public $type = 'youxi_google_font';

	public function enqueue() {

		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		wp_enqueue_script( 'youxi-google-font-control', 
			get_template_directory_uri() . "/lib/framework/customizer/controls/assets/js/google-font-control{$suffix}.js", 
			array( 'jquery' ), '1.0', true
		);

		wp_localize_script( 'youxi-google-font-control', '_youxiCustomizeGoogleFonts', Youxi_Google_Font::fetch() );
	}

	public function render_content() {

		if( ! class_exists( 'Youxi_Google_Font' ) ) {
			require( get_template_directory() . '/lib/framework/class-google-font.php' );
		}

		$family_val = '';
		$variant_val = '';
		$subsets_val = array();

		$variant_options = array();
		$subsets_options = array();

		$google_fonts = Youxi_Google_Font::fetch();
		$value = Youxi_Google_Font::parse_str( $this->value() );

		if( isset( $value['family'] ) ) {

			$family_val = $value['family'];

			if( isset( $value['variant'] ) ) {
				$variant_val = $value['variant'];
				$variant_options = Youxi_Google_Font::get_variants( $family_val );
			}

			if( isset( $value['subsets'] ) && is_array( $value['subsets'] ) ) {
				$subsets_val = $value['subsets'];
				$subsets_options = Youxi_Google_Font::get_subsets( $family_val );
			}
		}

		?><label>

			<?php if ( ! empty( $this->label ) ) : ?>
				<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			<?php endif;

			if ( ! empty( $this->description ) ) : ?>
				<span class="description customize-control-description"><?php echo $this->description; ?></span>
			<?php endif; ?>

			<input type="hidden" class="youxi-google-font-value" <?php $this->link(); ?>>

			<select class="youxi-google-font-control youxi-google-font-family" style="width: 100%">
				<option value=""<?php selected( '', $family_val ) ?>><?php _e( 'Inherit', 'youxi' ); ?></option>
				<?php foreach ( $google_fonts as $font ):
					$font_family = preg_replace( '/\s+/', '+', $font['family'] );
				?>
				<option value="<?php echo esc_attr( $font_family ) ?>"<?php selected( $font_family, $family_val ) ?>><?php 
					echo esc_html( $font['family'] );
				?></option>
				<?php endforeach; ?>
			</select>

			<select class="youxi-google-font-control youxi-google-font-variant" style="width: 100%;<?php if( ! $family_val ) echo ' display: none;'; ?>"<?php disabled( '', $family_val ) ?>>
				<option value=""><?php _e( 'Select a Font Variant', 'youxi' ); ?></option>
				<?php foreach( $variant_options as $font_variant ): ?>
				<option value="<?php echo esc_attr( $font_variant ) ?>"<?php selected( $variant_val, $font_variant ) ?>><?php 
					echo esc_html( $font_variant );
				?></option>
				<?php endforeach; ?>
			</select>

			<div class="youxi-google-font-control youxi-google-font-subsets">
				<?php foreach( $subsets_options as $subset ): ?>
				<label>
					<input type="checkbox" value="<?php echo esc_attr( $subset ) ?>"<?php checked( true, in_array( $subset, $subsets_val ) ) ?>>
					<?php echo esc_html( $subset ); ?>
					<br>
				</label>
				<?php endforeach; ?>
			</div>

		</label>
		<?php
	}
}
