<?php
class Youxi_Customize_Range_Control extends WP_Customize_Control {

	public $type = 'youxi_range';

	public $min = 0;

	public $max = 100;

	public $step = null;

	/**
	 * Refresh the parameters passed to the JavaScript via JSON.
	 *
	 * @since 3.4.0
	 * @uses WP_Customize_Control::to_json()
	 */
	public function to_json() {
		parent::to_json();
		$this->json['ui'] = array(
			'min' => $this->min, 
			'max' => $this->max, 
			'step' => $this->step
		);
	}

	/**
	 * Enqueue control related scripts/styles.
	 *
	 * @since 3.4.0
	 */
	public function enqueue() {

		wp_enqueue_script( 'jquery-ui-slider' );

		wp_enqueue_style( 'youxi-range-control', 
			get_template_directory_uri() . '/lib/framework/customizer/controls/assets/css/range-control.css', 
			array(), '1.0', 'screen'
		);
		wp_enqueue_script( 'youxi-range-control', 
			get_template_directory_uri() . '/lib/framework/customizer/controls/assets/js/range-control.js', 
			array( 'jquery-ui-slider' ), '1.0', true
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
			<input type="text" readonly value="<?php echo esc_attr( $this->value() ); ?>" <?php $this->link(); ?>>
			<div class="youxi-range-control"></div>
		</label>
		<?php
	}
}