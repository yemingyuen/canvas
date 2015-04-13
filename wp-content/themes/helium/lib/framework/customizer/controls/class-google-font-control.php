<?php
class Youxi_Customize_Google_Font_Control extends WP_Customize_Control {

	public $type = 'youxi_google_font';

	/**
	 * Enqueue control related scripts/styles.
	 *
	 * @since 3.4.0
	 */
	public function enqueue() {

		wp_enqueue_style( 'youxi-google-font-control', 
			get_template_directory_uri() . '/lib/framework/customizer/controls/assets/css/google-font-control.css', 
			array(), '1.0', 'screen'
		);
		wp_enqueue_script( 'youxi-google-font-control', 
			get_template_directory_uri() . '/lib/framework/customizer/controls/assets/js/google-font-control.js', 
			array( 'jquery' ), '1.0', true
		);
		wp_localize_script( 'youxi-google-font-control', '_youxiCustomizeGoogleFonts', Youxi_Google_Font::fetch() );
	}

	public function render_content() {

		$google_fonts = Youxi_Google_Font::fetch();

		if ( empty( $google_fonts ) )
			return;
		?>
		<label>
			<?php if ( ! empty( $this->label ) ) : ?>
				<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			<?php endif;
			if ( ! empty( $this->description ) ) : ?>
				<span class="description customize-control-description"><?php echo $this->description; ?></span>
			<?php endif; ?>
			<input type="hidden" class="youxi-google-font-value" <?php $this->link(); ?>>
			<select class="youxi-google-font-family">
				<option value="" selected><?php _e( 'Inherit', 'helium' ); ?></option>
				<?php
				foreach ( $google_fonts as $font )
					echo '<option value="' . esc_attr( preg_replace( '/\s+/', '+', $font['family'] ) ) . '">' . $font['family'] . '</option>';
				?>
			</select>
			<br>
			<select class="youxi-google-font-variant" disabled>
				<option value="" selected><?php _e( 'Select a Font Variant', 'helium' ); ?></option>
			</select>
		</label>
		<?php
	}
}