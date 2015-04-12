<?php
if ( ! defined( 'ABSPATH' ) ) {
	die();
}

/**
 * Youxi Customize Range Control
 *
 * This class adds an jQuery UI slider control to WordPress customizer
 *
 * @package   Youxi Themes Theme Utils
 * @author    Mairel Theafila <maimairel@yahoo.com>
 * @copyright Copyright (c) 2014-2015, Mairel Theafila
 */

class Youxi_Customize_Range_Control extends WP_Customize_Control {

	public $type = 'youxi_range';

	public $min = 0;

	public $max = 100;

	public $step = null;

	public function to_json() {
		parent::to_json();
		$this->json['ui'] = array(
			'min' => $this->min, 
			'max' => $this->max, 
			'step' => $this->step
		);
	}

	public function enqueue() {

		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		wp_enqueue_script( 'jquery-ui-slider' );

		wp_enqueue_style( 'youxi-range-control', 
			get_template_directory_uri() . '/lib/framework/customizer/controls/assets/css/range-control.css', 
			array(), '1.0', 'screen'
		);
		wp_enqueue_script( 'youxi-range-control', 
			get_template_directory_uri() . "/lib/framework/customizer/controls/assets/js/range-control{$suffix}.js", 
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