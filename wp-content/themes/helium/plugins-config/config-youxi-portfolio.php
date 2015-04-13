<?php

// Make sure the plugin is active
if( ! defined( 'YOUXI_PORTFOLIO_VERSION' ) ) {
	add_filter( 'theme_page_templates', 'helium_remove_portfolio_page_template' );
	return;
}

/* ==========================================================================
	Portfolio General
============================================================================= */

function helium_modify_portfolio_tax( $taxonomies ) {
	if( isset( $taxonomies[ youxi_portfolio_tax_name() ] ) ) {
		$taxonomies[ youxi_portfolio_tax_name() ]['hierarchical'] = true;
	}
	return $taxonomies;
}
add_filter( 'youxi_portfolio_cpt_taxonomies', 'helium_modify_portfolio_tax' );

/**
 * Add the tinymce and page builder to one page blocks
 */
if( ! function_exists( 'helium_portfolio_tinymce_post_types' ) ) {

	function helium_portfolio_tinymce_post_types( $post_types ) {
		
		if( function_exists( 'youxi_portfolio_cpt_name' ) ) {
			if( ! is_array( $post_types ) ) {
				$post_types = array( $post_types );
			}
			$post_types[] = youxi_portfolio_cpt_name();
		}
		return $post_types;
	}
}
// add_filter( 'youxi_builder_post_types', 'helium_portfolio_tinymce_post_types' );
add_filter( 'youxi_shortcode_tinymce_post_types', 'helium_portfolio_tinymce_post_types' );

/**
 * Portfolio Metaboxes
 */
if( ! function_exists( 'helium_youxi_portfolio_cpt_metaboxes' ) ):

	function helium_youxi_portfolio_cpt_metaboxes( $metaboxes ) {

		$metaboxes['general'] = array(
			'title' => __( 'General', 'helium' ), 
			'fields' => array(
				'url' => array(
					'type' => 'url', 
					'label' => __( 'URL', 'helium' ), 
					'description' => __( 'Enter here the portfolio URL.', 'helium' ), 
					'show_admin_column' => true, 
					'std' => ''
				), 
				'client' => array(
					'type' => 'text', 
					'label' => __( 'Client', 'helium' ), 
					'description' => __( 'Enter here the portfolio client.', 'helium' ), 
					'std' => ''
				), 
				'client_url' => array(
					'type' => 'text', 
					'label' => __( 'Client URL', 'helium' ), 
					'description' => __( 'Enter here the portfolio client url.', 'helium' ), 
					'std' => ''
				), 
				'featured' => array(
					'type' => 'switch', 
					'label' => __( 'Featured', 'helium' ), 
					'description' => __( 'Switch to mark the portfolio as featured and show it on the portfolio slider.', 'helium' ), 
					'std' => false, 
					'scalar' => true
				)
			)
		);

		$metaboxes['layout'] = array(
			'title' => __( 'Layout', 'helium' ), 
			'fields' => array(
				'page_layout' => array(
					'type' => 'select', 
					'label' => __( 'Portfolio Page Layout', 'helium' ), 
					'description' => __( 'Specify the layout of the portfolio page.', 'helium' ), 
					'choices' => array(
						'boxed' => __( 'Boxed', 'helium' ), 
						'fullwidth' => __( 'Fullwidth', 'helium' )
					), 
					'std' => 'boxed', 
					'scalar' => true
				), 
				'archive_page' => array(
					'type' => 'select', 
					'label' => __( 'Portfolio Archive Page', 'helium' ), 
					'description' => __( 'Choose the archive page of this portfolio item.', 'helium' ), 
					'choices' => 'helium_portfolio_pages', 
					'std' => 0, 
					'scalar' => true
				), 
				'media_position' => array(
					'type' => 'select', 
					'label' => __( 'Portfolio Media Layout', 'helium' ), 
					'description' => __( 'Specify how the portfolio media is displayed.', 'helium' ), 
					'choices' => array(
						'top' => __( 'Top', 'helium' ), 
						'left' => __( 'Left', 'helium' ), 
						'right' => __( 'Right', 'helium' )
					), 
					'std' => 'top'
				), 
				'details_position' => array(
					'type' => 'select', 
					'label' => __( 'Portfolio Details Layout', 'helium' ), 
					'description' => __( 'Specify how the portfolio details is displayed. Choosing left or right for media position will move the details to the bottom.', 'helium' ), 
					'choices' => array(
						'hidden' => __( 'Hidden', 'helium' ), 
						'left' => __( 'Left', 'helium' ), 
						'right' => __( 'Right', 'helium' )
					), 
					'std' => 'left'
				), 
				'details' => array(
					'type' => 'repeater', 
					'label' => __( 'Details', 'helium' ), 
					'description' => __( 'Specify the portfolio details to show.', 'helium' ), 
					'fields' => array(
						'type' => array(
							'type' => 'select', 
							'label' => __( 'Detail Type', 'helium' ), 
							'description' => __( 'Choose the portfolio detail type.', 'helium' ), 
							'choices' => array(
								'categories' => __( 'Categories', 'helium' ), 
								'url' => __( 'URL', 'helium' ), 
								'client' => __( 'Client', 'helium' ), 
								'share' => __( 'Share', 'helium' ), 
								'custom' => __( 'Custom', 'helium' )
							), 
							'std' => 'custom'
						), 
						'label' => array(
							'type' => 'text', 
							'label' => __( 'Label', 'helium' ), 
							'description' => __( 'Enter the detail label.', 'helium' ), 
							'std' => '', 
						), 
						'custom_value' => array(
							'type' => 'textarea', 
							'label' => __( 'Custom Value', 'helium' ), 
							'description' => __( 'Enter the custom detail value.', 'helium' ), 
							'std' => '', 
							'criteria' => 'type:is(custom)'
						)
					), 
					'min' => 0, 
					'preview_template' => '{{ data.label }}', 
					'std' => '', 
					'criteria' => 'details_position:not(hidden)'
				)
			)
		);

		$metaboxes['media'] = array(
			'title' => __( 'Media', 'helium' ), 
			'fields' => array(
				'type' => array(
					'type' => 'select', 
					'label' => __( 'Media Type', 'helium' ), 
					'description' => __( 'Choose the type of media to display.', 'helium' ), 
					'choices' => array(
						'featured-image' => __( 'Featured Image', 'helium' ), 
						'stacked' => __( 'Stacked Images', 'helium' ), 
						'slider' => __( 'Slider', 'helium' ), 
						'justified-grids' => __( 'Justified Grids', 'helium' ), 
						'video' => __( 'Video', 'helium' ), 
						'audio' => __( 'Audio', 'helium' )
					), 
					'std' => 'featured-image'
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
				), 
				'images' => array(
					'type' => 'image', 
					'label' => __( 'Images', 'helium' ), 
					'description' => __( 'Choose here the images to use.', 'helium' ), 
					'multiple' => 'add', 
					'criteria' => 'type:not(featured-image),type:not(video),type:not(audio)'
				), 
				'video_type' => array(
					'type' => 'select', 
					'label' => __( 'Video Type', 'helium' ), 
					'description' => __( 'Choose here the video type.', 'helium' ), 
					'choices' => array(
						'embed' => __( 'Embedded (YouTube/Vimeo)', 'helium' ), 
						'hosted' => __( 'Hosted', 'helium' )
					), 
					'std' => 'hosted', 
					'criteria' => 'type:is(video)'
				), 
				'video_embed' => array(
					'type' => 'textarea', 
					'label' => __( 'Video Embed Code (YouTube/Vimeo)', 'helium' ), 
					'description' => __( 'Enter here the video embed code (YouTube/Vimeo).', 'helium' ), 
					'std' => '', 
					'criteria' => 'type:is(video),video_type:is(embed)'
				), 
				'video_src' => array(
					'type' => 'upload', 
					'label' => __( 'Video Source', 'helium' ), 
					'library_type' => 'video', 
					'description' => __( 'Choose here the hosted video source.', 'helium' ), 
					'criteria' => 'type:is(video),video_type:is(hosted)'
				), 
				'video_poster' => array(
					'type' => 'image', 
					'multiple' => false, 
					'label' => __( 'Video Poster', 'helium' ), 
					'description' => __( 'Upload here an image that will be used either as the poster or fallback for unsupported devices.', 'helium' ), 
					'criteria' => 'type:is(video),video_type:is(hosted)'
				), 
				'audio_type' => array(
					'type' => 'select', 
					'label' => __( 'Audio Type', 'helium' ), 
					'description' => __( 'Choose here the audio type.', 'helium' ), 
					'choices' => array(
						'embed' => __( 'Embedded (SoundCloud)', 'helium' ), 
						'hosted' => __( 'Hosted', 'helium' )
					), 
					'std' => 'hosted', 
					'criteria' => 'type:is(audio)'
				), 
				'audio_embed' => array(
					'type' => 'textarea', 
					'label' => __( 'Embed Code (SoundCloud)', 'helium' ), 
					'description' => __( 'Enter here the audio embed code (SoundCloud).', 'helium' ), 
					'std' => '', 
					'criteria' => 'type:is(audio),audio_type:is(embed)'
				), 
				'audio_src' => array(
					'type' => 'upload', 
					'label' => __( 'Audio Source', 'helium' ), 
					'library_type' => 'audio', 
					'description' => __( 'Choose here the hosted audio source.', 'helium' ), 
					'criteria' => 'type:is(audio),audio_type:is(hosted)'
				)
			)
		);

		return $metaboxes;
	}
endif;
add_filter( 'youxi_portfolio_cpt_metaboxes', 'helium_youxi_portfolio_cpt_metaboxes' );

/* ==========================================================================
	Add portfolio archive metabox to pages
============================================================================= */

if( ! function_exists( 'helium_add_portfolio_metabox' ) ) {

	function helium_add_portfolio_metabox() {

		$metaboxes = array();

		/* Portfolio Archive Page Template */
		$metaboxes['portfolio_grid_settings'] = array(

			'title' => __( 'Page Template: Portfolio', 'helium' ), 

			'fields' => array(
				'use_defaults' => array(
					'type' => 'switch', 
					'label' => __( 'Use Default Settings', 'helium' ), 
					'description' => __( 'Switch to use the default portfolio grid settings.', 'helium' ), 
					'std' => false
				), 
				'show_filter' => array(
					'type' => 'switch', 
					'label' => __( 'Show Filter', 'helium' ), 
					'description' => __( 'Switch to display the portfolio filter.', 'helium' ), 
					'criteria' => 'use_defaults:is(0)', 
					'std' => true
				), 
				'pagination' => array(
					'type' => 'select', 
					'label' => __( 'Pagination Type', 'helium' ), 
					'description' => __( 'Specify the portfolio pagination type.', 'helium' ), 
					'choices' => array(
						'ajax' => __( 'AJAX', 'helium' ), 
						'infinite' => __( 'Infinite', 'helium' ), 
						'numbered' => __( 'Numbered', 'helium' ), 
						'prev_next' => __( 'Prev/Next', 'helium' ), 
						'show_all' => __( 'None (Show all)', 'helium' )
					), 
					'criteria' => 'use_defaults:is(0)', 
					'std' => 'ajax'
				), 
				'ajax_button_text' => array(
					'type' => 'text', 
					'label' => __( 'AJAX Button Text', 'helium' ), 
					'description' => __( 'Specify the text to display on the AJAX load more button.', 'helium' ), 
					'std' => 'Load More', 
					'criteria' => 'pagination:is(ajax),use_defaults:is(0)'
				), 
				'ajax_button_complete_text' => array(
					'type' => 'text', 
					'label' => __( 'AJAX Button Complete Text', 'helium' ), 
					'description' => __( 'Specify the text to display on the AJAX load more button when there are no more items to load.', 'helium' ), 
					'std' => 'No More Items', 
					'criteria' => 'pagination:is(ajax),use_defaults:is(0)'
				), 
				'posts_per_page' => array(
					'type' => 'uislider', 
					'label' => __( 'Posts Per Page', 'helium' ), 
					'description' => __( 'Specify how many portfolio items to show per page.', 'helium' ), 
					'widgetopts' => array(
						'min' => 1, 
						'max' => 20, 
						'step' => 1
					), 
					'criteria' => 'use_defaults:is(0)', 
					'std' => 10
				), 
				'include' => array(
					'type' => 'checkboxlist', 
					'label' => __( 'Included Categories', 'helium' ), 
					'description' => __( 'Specify the portfolio categories to include (leave unchecked to include all).', 'helium' ), 
					'choices' => get_terms( youxi_portfolio_tax_name(), array( 'fields' => 'id=>name', 'hide_empty' => false ) ), 
					'criteria' => 'use_defaults:is(0)'
				), 
				'behavior' => array(
					'type' => 'select', 
					'label' => __( 'Behavior', 'helium' ), 
					'description' => __( 'Specify the behavior when clicking the thumbnail image.', 'helium' ), 
					'choices' => array(
						'none' => __( 'None', 'helium' ), 
						'lightbox' => __( 'Show Image in Lightbox', 'helium' ), 
						'page' => __( 'Go to Detail Page', 'helium' )
					), 
					'criteria' => 'use_defaults:is(0)'
				), 
				'orderby' => array(
					'type' => 'select', 
					'label' => __( 'Order By', 'helium' ), 
					'description' => __( 'Specify in what order the items should be displayed.', 'helium' ), 
					'choices' => array(
						'date' => __( 'Date', 'helium' ), 
						'menu_order' => __( 'Menu Order', 'helium' ), 
						'title' => __( 'Title', 'helium' ), 
						'ID' => __( 'ID', 'helium' ), 
						'rand' => __( 'Random', 'helium' )
					), 
					'criteria' => 'use_defaults:is(0)', 
					'std' => 'date'
				), 
				'order' => array(
					'type' => 'select', 
					'label' => __( 'Order', 'helium' ), 
					'description' => __( 'Specify how to order the items.', 'helium' ), 
					'choices' => array(
						'DESC' => __( 'Descending', 'helium' ), 
						'ASC' => __( 'Ascending', 'helium' )
					), 
					'criteria' => 'use_defaults:is(0)', 
					'std' => 'DESC'
				), 
				'layout' => array(
					'type' => 'select', 
					'label' => __( 'Layout', 'helium' ), 
					'description' => __( 'Specify the portfolio layout.', 'helium' ), 
					'choices' => array(
						'classic'    => __( 'Classic', 'helium' ), 
						'masonry'    => __( 'Masonry', 'helium' ), 
						'justified'  => __( 'Justified', 'helium' )
					), 
					'criteria' => 'use_defaults:is(0)', 
					'std' => 'justified'
				), 
				'columns' => array(
					'type' => 'uislider', 
					'label' => __( 'Columns', 'helium' ), 
					'description' => __( 'Specify in how many columns the items should be displayed in the masonry/classic layout.', 'helium' ), 
					'widgetopts' => array(
						'min' => 3, 
						'max' => 5, 
						'step' => 1
					), 
					'std' => 4, 
					'criteria' => 'use_defaults:is(0),layout:not(justified)'
				)
			)
		);

		$metaboxes['portfolio_slider_settings'] = array(

			'title' => __( 'Page Template: Portfolio Slider', 'helium' ), 

			'fields' => array(
				'posts_per_page' => array(
					'type' => 'uislider', 
					'label' => __( 'Number of Slides', 'helium' ), 
					'description' => __( 'Specify how many portfolio items to show on the slider.', 'helium' ), 
					'widgetopts' => array(
						'min' => 1, 
						'max' => 10, 
						'step' => 1
					), 
					'std' => 5
				), 
				'orderby' => array(
					'type' => 'select', 
					'label' => __( 'Order By', 'helium' ), 
					'description' => __( 'Specify in what order the items should be displayed.', 'helium' ), 
					'choices' => array(
						'date' => __( 'Date', 'helium' ), 
						'menu_order' => __( 'Menu Order', 'helium' ), 
						'title' => __( 'Title', 'helium' ), 
						'ID' => __( 'ID', 'helium' ), 
						'rand' => __( 'Random', 'helium' )
					), 
					'std' => 'date'
				), 
				'order' => array(
					'type' => 'select', 
					'label' => __( 'Order', 'helium' ), 
					'description' => __( 'Specify how to order the items.', 'helium' ), 
					'choices' => array(
						'DESC' => __( 'Descending', 'helium' ), 
						'ASC' => __( 'Ascending', 'helium' )
					), 
					'std' => 'DESC'
				)
			)
		);

		/* Create the 'page' post type object */
		$post_type_object = Youxi_Post_Type::get( 'page' );

		/* Add the metaboxes */
		foreach( $metaboxes as $metabox_id => $metabox ) {
			$post_type_object->add_meta_box( new Youxi_Metabox( $metabox_id, $metabox ) );
		}
	}
}
add_action( 'init', 'helium_add_portfolio_metabox' );

/* ==========================================================================
	Portfolio Shortcode
============================================================================= */

add_filter( 'youxi_portfolio_register_shortcode', '__return_false' );

/* ==========================================================================
	Disable Gallery (Youxi Portfolio 1.2+)
============================================================================= */

add_filter( 'youxi_portfolio_use_gallery', '__return_false' );

/* ==========================================================================
	Remove Portfolio Page Template
============================================================================= */

function helium_remove_portfolio_page_template( $templates ) {
	unset( $templates['archive-portfolio.php'] );
	unset( $templates['page-templates/portfolio-slider.php'] );
	return $templates;
}
