<?php

// Make sure the plugin is active
if( ! defined( 'YOUXI_POST_FORMAT_VERSION' ) ) {
	return;
}

if( ! function_exists( 'helium_youxi_post_format_gallery_metabox' ) ):

function helium_youxi_post_format_gallery_metabox( $metabox ) {

	if( is_array( $metabox ) && isset( $metabox['fields'] ) ) {
		$metabox['fields'] = array_merge( array(
			'type' => array(
				'type' => 'radio', 
				'label' => __( 'Gallery Type', 'helium' ), 
				'description' => __( 'Choose the gallery type for this post.', 'helium' ), 
				'choices' => array(
					'slider' => __( 'Slider', 'helium' ), 
					'justified' => __( 'Justified Gallery', 'helium' )
				), 
				'std' => 'slider'
			), 
			'autoHeight' => array(
				'type' => 'switch', 
				'label' => __( 'Slider: Auto Height', 'helium' ), 
				'description' => __( 'Switch to automatically update slider height based on each slide.', 'helium' ), 
				'std' => true, 
				'criteria' => 'type:is(slider)'
			), 
			'autoScaleSliderRatio' => array(
				'type' => 'aspect-ratio', 
				'label' => __( 'Slider: Aspect Ratio', 'helium' ), 
				'description' => __( 'Specify the slider aspect ratio when auto height is disabled.', 'helium' ), 
				'std' => array( 'width' => 4, 'height' => 3 ), 
				'criteria' => 'type:is(slider),autoHeight:is(0)'
			), 
			'imageScaleMode' => array(
				'type' => 'select', 
				'label' => __( 'Slider: Image Scale Mode', 'helium' ), 
				'description' => __( 'Specify the slider image scaling mode.', 'helium' ), 
				'choices' => array(
					'fill' => __( 'Fill', 'helium' ), 
					'fit' => __( 'Fit', 'helium' )
				), 
				'std' => 'fill', 
				'criteria' => 'type:is(slider),autoHeight:is(0)'
			), 
			'controlNavigation' => array(
				'type' => 'switch', 
				'label' => __( 'Slider: Navigation Bullets', 'helium' ), 
				'description' => __( 'Switch to toggle the slider navigation bullets.', 'helium' ), 
				'std' => true, 
				'criteria' => 'type:is(slider)'
			), 
			'arrowsNav' => array(
				'type' => 'switch', 
				'label' => __( 'Slider: Navigation Arrows', 'helium' ), 
				'description' => __( 'Switch to toggle the slider navigation arrows.', 'helium' ), 
				'std' => true, 
				'criteria' => 'type:is(slider)'
			), 
			'loop' => array(
				'type' => 'switch', 
				'label' => __( 'Slider: Loop', 'helium' ), 
				'description' => __( 'Switch to allow the slider to go to the first from the last slide.', 'helium' ), 
				'std' => false, 
				'criteria' => 'type:is(slider)'
			), 
			'slidesOrientation' => array(
				'type' => 'select', 
				'label' => __( 'Slider: Orientation', 'helium' ), 
				'description' => __( 'Specify the slider orientation.', 'helium' ), 
				'choices' => array(
					'vertical' => __( 'Vertical', 'helium' ), 
					'horizontal' => __( 'Horizontal', 'helium' )
				), 
				'std' => 'horizontal', 
				'criteria' => 'type:is(slider),autoHeight:is(0)'
			), 
			'transitionType' => array(
				'type' => 'select', 
				'label' => __( 'Slider: Transition Type', 'helium' ), 
				'description' => __( 'Specify the slider transition type.', 'helium' ), 
				'choices' => array(
					'move' => __( 'Move', 'helium' ), 
					'fade' => __( 'Fade', 'helium' )
				), 
				'std' => 'move', 
				'criteria' => 'type:is(slider)'
			), 
			'transitionSpeed' => array(
				'type' => 'uislider', 
				'label' => __( 'Slider: Transition Speed', 'helium' ), 
				'description' => __( 'Specify the slider transition speed.', 'helium' ), 
				'widgetopts' => array(
					'min' => 100, 
					'max' => 5000, 
					'step' => 10
				), 
				'std' => 600, 
				'criteria' => 'type:is(slider)'
			)
		), $metabox['fields'] );
	}

	return $metabox;
}
endif;
add_filter( 'youxi_post_format_gallery_metabox', 'helium_youxi_post_format_gallery_metabox' );