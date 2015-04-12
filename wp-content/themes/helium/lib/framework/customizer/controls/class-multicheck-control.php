<?php
if ( ! defined( 'ABSPATH' ) ) {
	die();
}

/**
 * Youxi Customize Multicheck Control
 *
 * This class adds a multiple select control to WordPress customizer
 *
 * @package   Youxi Themes Theme Utils
 * @author    Mairel Theafila <maimairel@yahoo.com>
 * @copyright Copyright (c) 2014-2015, Mairel Theafila
 */

class Youxi_Customize_Multicheck_Control extends WP_Customize_Control {

	public $type = 'youxi_multicheck';

	public function enqueue() {

		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		wp_enqueue_script( 'youxi-multicheck-control', 
			get_template_directory_uri() . "/lib/framework/customizer/controls/assets/js/multicheck-control{$suffix}.js", 
			array( 'jquery' ), '1.0', true
		);
	}

	public function render_content() {
		if ( empty( $this->choices ) )
			return;
		?>
		<label>
			<?php if ( ! empty( $this->label ) ) : ?>
				<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			<?php endif;
			if ( ! empty( $this->description ) ) : ?>
				<span class="description customize-control-description"><?php echo $this->description; ?></span>
			<?php endif; ?>
			<select multiple <?php $this->link(); ?>>
				<?php foreach ( $this->choices as $value => $label ): 
					echo '<option value="' . esc_attr( $value ) . '"' . selected( in_array( $value, (array) $this->value() ), true, false ) . '>' . $label . '</option>';
				endforeach; ?>
			</select>
		</label>
		<div class="youxi-multicheck-checkboxes" style="display: none;">
			<?php foreach( $this->choices as $value => $label ):
			?>
			<label>
				<input type="checkbox" <?php checked( in_array( $value, (array) $this->value() ), true ); ?>>
				<?php echo $label; ?>
				<br>
			</label>
			<?php endforeach; ?>
		</div>
		<?php
	}
}