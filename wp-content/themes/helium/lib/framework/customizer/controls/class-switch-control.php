<?php
class Youxi_Customize_Switch_Control extends WP_Customize_Control {

	public $type = 'youxi_switch';

	/**
	 * Enqueue control related scripts/styles.
	 *
	 * @since 3.4.0
	 */
	public function enqueue() {

		wp_enqueue_script( 'jquery-ui-slider' );

		wp_register_style( 'switchery', 
			get_template_directory_uri() . '/lib/framework/customizer/controls/assets/plugins/switchery/switchery.css', 
			array(), '0.6.3', 'screen'
		);
		wp_register_script( 'switchery',
			get_template_directory_uri() . '/lib/framework/customizer/controls/assets/plugins/switchery/switchery.js', 
			array(), '0.6.3', true
		);

		wp_enqueue_style( 'switchery' );
		wp_enqueue_script( 'youxi-switch-control', 
			get_template_directory_uri() . '/lib/framework/customizer/controls/assets/js/switch-control.js', 
			array( 'switchery' ), false, true
		);
	}

	public function render_content() {
		?>
		<label>
			<?php if ( ! empty( $this->label ) ) : ?>
				<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			<?php endif;
			if ( ! empty( $this->description ) ) : ?>
				<span class="description customize-control-description"><?php echo $this->description; ?></span>
			<?php endif; ?>
			<input type="checkbox" value="<?php echo esc_attr( $this->value() ); ?>" <?php $this->link(); checked( $this->value() ); ?>>
		</label>
		<?php
	}
}