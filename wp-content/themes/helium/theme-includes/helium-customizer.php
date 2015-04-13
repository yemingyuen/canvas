<?php

if( ! class_exists( 'Youxi_Customize_Manager' ) ) {
	require( get_template_directory() . '/lib/framework/customizer/class-manager.php' );
}

class Helium_Customize_Manager extends Youxi_Customize_Manager {

	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct();
		add_action( 'customize_register', array( $this, 'site_customizer' ) );
		add_action( 'customize_register', array( $this, 'color_customizer' ) );
		add_action( 'customize_register', array( $this, 'typography_customizer' ) );
		add_action( 'customize_register', array( $this, 'blog_customizer' ) );

		if( defined( 'YOUXI_PORTFOLIO_VERSION' ) ) {
			add_action( 'customize_register', array( $this, 'portfolio_customizer' ) );
		}
		if( class_exists( 'Easy_Digital_Downloads' ) ) {
			add_action( 'customize_register', array( $this, 'edd_customizer' ) );
		}
	}

	public function pre_customize( $wp_customize ) {

		parent::pre_customize( $wp_customize );

		/* Remove predefined sections and controls */
		$wp_customize->remove_section( 'nav' );
	}

	public function site_customizer( $wp_customize ) {

		$prefix = $this->prefix();

		/* Section: Header */

		$wp_customize->add_section( $prefix . '_header', array(
			'title' => __( 'Header', 'helium' ), 
			'priority' => 41
		));

		/* Header Settings */

		$wp_customize->add_setting( $prefix . '[logo_image]', array(
			'default' => '', 
			'sanitize_callback' => 'esc_url_raw'
		));
		$wp_customize->add_setting( $prefix . '[logo_height]', array(
			'default' => 25, 
			'sanitize_callback' => 'absint'
		));
		$wp_customize->add_setting( $prefix . '[show_search]', array(
			'default' => true, 
			'sanitize_callback' => array( get_class(), 'sanitize_boolean' )
		));
		$wp_customize->add_setting( $prefix . '[copyright_text]', array(
			'default' => __( '&copy; Youxi Themes. 2012-2014. All Rights Reserved.', 'helium' ), 
			'sanitize_callback' => 'sanitize_text_field'
		));

		/* Header Controls */

		$wp_customize->add_control( new WP_Customize_Image_Control(
			$wp_customize, $prefix . '[logo_image]', array(
				'label' => __( 'Logo Image', 'helium' ), 
				'section' => $prefix . '_header', 
				'priority' => 1
			)
		));
		$wp_customize->add_control( new Youxi_Customize_Range_Control(
			$wp_customize, $prefix . '[logo_height]', array(
				'label' => __( 'Max Logo Height', 'helium' ), 
				'section' => $prefix . '_header', 
				'min' => 0, 
				'max' => 150, 
				'step' => 1, 
				'priority' => 2
			)
		));
		$wp_customize->add_control( new Youxi_Customize_Switch_Control(
			$wp_customize, $prefix . '[show_search]', array(
				'label' => __( 'Show Search', 'helium' ), 
				'section' => $prefix . '_header', 
				'priority' => 3
			)
		));
		$wp_customize->add_control( $prefix . '[copyright_text]', array(
			'label' => __( 'Copyright Text', 'helium' ), 
			'section' => $prefix . '_header', 
			'type' => 'text', 
			'priority' => 4
		));
	}

	public function color_customizer( $wp_customize ) {

		$prefix = $this->prefix();

		/* Styling Settings */

		$wp_customize->add_setting( $prefix . '[accent_color]', array(
			'default' => '#3dc9b3', 
			'sanitize_callback' => 'sanitize_hex_color'
		));

		/* Styling Controls */

		$wp_customize->add_control( new WP_Customize_Color_Control(
			$wp_customize, $prefix . '[accent_color]', array(
				'label' => __( 'Accent Color', 'helium' ), 
				'section' => 'colors', 
				'priority' => 1
			)
		));
	}

	public function typography_customizer( $wp_customize ) {

		$prefix = $this->prefix();

		/* Section: Typography */

		$wp_customize->add_section( $prefix . '_typography', array(
			'title' => __( 'Typography', 'helium' ), 
			'priority' => 41
		));

		/* Typography Settings */

		$wp_customize->add_setting( $prefix . '[headings_1234_font]', array(
			'default' => ''
		));
		$wp_customize->add_setting( $prefix . '[headings_56_font]', array(
			'default' => ''
		));
		$wp_customize->add_setting( $prefix . '[body_font]', array(
			'default' => ''
		));
		$wp_customize->add_setting( $prefix . '[menu_font]', array(
			'default' => ''
		));
		$wp_customize->add_setting( $prefix . '[blockquote_font]', array(
			'default' => ''
		));
		$wp_customize->add_setting( $prefix . '[gridlist_title_font]', array(
			'default' => ''
		));
		$wp_customize->add_setting( $prefix . '[gridlist_subtitle_font]', array(
			'default' => ''
		));
		$wp_customize->add_setting( $prefix . '[content_nav_font]', array(
			'default' => ''
		));


		/* Typography Controls */

		$wp_customize->add_control( new Youxi_Customize_Google_Font_Control(
			$wp_customize, $prefix . '[headings_1234_font]', array(
				'label' => __( 'H1, H2, H3, H4 Font', 'helium' ), 
				'section' => $prefix . '_typography'
			)
		));
		$wp_customize->add_control( new Youxi_Customize_Google_Font_Control(
			$wp_customize, $prefix . '[headings_56_font]', array(
				'label' => __( 'H5, H6 Font', 'helium' ), 
				'section' => $prefix . '_typography'
			)
		));
		$wp_customize->add_control( new Youxi_Customize_Google_Font_Control(
			$wp_customize, $prefix . '[body_font]', array(
				'label' => __( 'Body Font', 'helium' ), 
				'section' => $prefix . '_typography'
			)
		));
		$wp_customize->add_control( new Youxi_Customize_Google_Font_Control(
			$wp_customize, $prefix . '[menu_font]', array(
				'label' => __( 'Menu Font', 'helium' ), 
				'section' => $prefix . '_typography'
			)
		));
		$wp_customize->add_control( new Youxi_Customize_Google_Font_Control(
			$wp_customize, $prefix . '[blockquote_font]', array(
				'label' => __( 'Blockquote Font', 'helium' ), 
				'section' => $prefix . '_typography'
			)
		));
		$wp_customize->add_control( new Youxi_Customize_Google_Font_Control(
			$wp_customize, $prefix . '[gridlist_title_font]', array(
				'label' => __( 'Gridlist Title Font', 'helium' ), 
				'section' => $prefix . '_typography'
			)
		));
		$wp_customize->add_control( new Youxi_Customize_Google_Font_Control(
			$wp_customize, $prefix . '[gridlist_subtitle_font]', array(
				'label' => __( 'Gridlist Subtitle Font', 'helium' ), 
				'section' => $prefix . '_typography'
			)
		));
		$wp_customize->add_control( new Youxi_Customize_Google_Font_Control(
			$wp_customize, $prefix . '[content_nav_font]', array(
				'label' => __( 'Content Navigation Font', 'helium' ), 
				'section' => $prefix . '_typography'
			)
		));
	}

	public function blog_customizer( $wp_customize ) {

		$prefix = $this->prefix();

		/* Panel: Blog */

		if( method_exists( $wp_customize, 'add_panel' ) ) {
			$section_priority = 0;
			$section_title_prefix = '';
			$wp_customize->add_panel( $prefix . '_blog', array(
				'title' => __( 'Blog', 'helium' ), 
				'priority' => 42
			));
		} else {
			$section_priority = 42;
			$section_title_prefix = __( 'Blog', 'helium' ) . ' ';
		}

		/* Section: Entries */

		$wp_customize->add_section( $prefix . '_blog_entries', array(
			'title' => $section_title_prefix . __( 'Entries', 'helium' ), 
			'priority' => ++$section_priority, 
			'panel' => $prefix . '_blog'
		));

		/* Entries Settings */

		$wp_customize->add_setting( $prefix . '[blog_date_format]', array(
			'default' => 'F d, Y', 
			'sanitize_callback' => 'sanitize_text_field'
		));
		$wp_customize->add_setting( $prefix . '[hidden_post_meta]', array(
			'default' => array()
		));

		/* Entries Controls */

		$wp_customize->add_control( $prefix . '[blog_date_format]', array(
			'label' => __( 'Date Format', 'helium' ), 
			'section' => $prefix . '_blog_entries', 
			'type' => 'text', 
			'priority' => 1
		));
		$wp_customize->add_control( new Youxi_Customize_Multicheck_Control(
			$wp_customize, $prefix . '[hidden_post_meta]', array(
				'label' => __( 'Hide Post Meta', 'helium' ), 
				'section' => $prefix . '_blog_entries', 
				'choices' => array(
					'author' => __( 'Author', 'helium' ), 
					'category' => __( 'Category', 'helium' ), 
					'tags' => __( 'Tags', 'helium' ), 
					'comments' => __( 'Comments', 'helium' ), 
					'permalink' => __( 'Permalink', 'helium' )
				), 
				'priority' => 2
			)
		));

		/* Section: Posts */

		$wp_customize->add_section( $prefix . '_blog_posts', array(
			'title' => $section_title_prefix . __( 'Posts', 'helium' ), 
			'priority' => ++$section_priority, 
			'panel' => $prefix . '_blog'
		));

		/* Posts Settings */

		$wp_customize->add_setting( $prefix . '[blog_show_tags]', array(
			'default' => true, 
			'sanitize_callback' => array( get_class(), 'sanitize_boolean' )
		));
		$wp_customize->add_setting( $prefix . '[blog_sharing]', array(
			'default' => true, 
			'sanitize_callback' => array( get_class(), 'sanitize_boolean' )
		));
		$wp_customize->add_setting( $prefix . '[blog_show_author]', array(
			'default' => true, 
			'sanitize_callback' => array( get_class(), 'sanitize_boolean' )
		));
		$wp_customize->add_setting( $prefix . '[blog_related_posts]', array(
			'default' => true, 
			'sanitize_callback' => array( get_class(), 'sanitize_boolean' )
		));
		$wp_customize->add_setting( $prefix . '[blog_related_posts_count]', array(
			'default' => 3, 
			'sanitize_callback' => 'absint'
		));
		$wp_customize->add_setting( $prefix . '[blog_related_posts_behavior]', array(
			'default' => 'lightbox'
		));


		/* Posts Controls */

		$wp_customize->add_control( new Youxi_Customize_Switch_Control(
			$wp_customize, $prefix . '[blog_show_tags]', array(
				'label' => __( 'Show Tags', 'helium' ), 
				'section' => $prefix . '_blog_posts', 
				'priority' => 3
			)
		));
		$wp_customize->add_control( new Youxi_Customize_Switch_Control(
			$wp_customize, $prefix . '[blog_sharing]', array(
				'label' => __( 'Show Sharing Buttons', 'helium' ), 
				'section' => $prefix . '_blog_posts', 
				'priority' => 4
			)
		));
		$wp_customize->add_control( new Youxi_Customize_Switch_Control(
			$wp_customize, $prefix . '[blog_show_author]', array(
				'label' => __( 'Show Author', 'helium' ), 
				'section' => $prefix . '_blog_posts', 
				'priority' => 5
			)
		));
		$wp_customize->add_control( new Youxi_Customize_Switch_Control(
			$wp_customize, $prefix . '[blog_related_posts]', array(
				'label' => __( 'Show Related Posts', 'helium' ), 
				'section' => $prefix . '_blog_posts', 
				'priority' => 6
			)
		));
		$wp_customize->add_control( new Youxi_Customize_Range_Control(
			$wp_customize, $prefix . '[blog_related_posts_count]', array(
				'label' => __( 'Related Posts Count', 'helium' ), 
				'section' => $prefix . '_blog_posts', 
				'min' => 3, 
				'max' => 4, 
				'step' => 1, 
				'priority' => 7
			)
		));
		$wp_customize->add_control( $prefix . '[blog_related_posts_behavior]', array(
			'label' => __( 'Related Posts Behavior', 'helium' ), 
			'section' => $prefix . '_blog_posts', 
			'type' => 'select', 
			'choices' => array(
				'lightbox' => __( 'Show Lightbox', 'helium' ), 
				'permalink' => __( 'Go to Post', 'helium' )
			), 
			'priority' => 8
		));

		/* Section: Summary */

		$wp_customize->add_section( $prefix . '_blog_summary', array(
			'title' => $section_title_prefix . __( 'Summary', 'helium' ), 
			'priority' => ++$section_priority, 
			'panel' => $prefix . '_blog'
		));

		/* Summary Settings */

		$wp_customize->add_setting( $prefix . '[blog_summary]', array(
			'default' => 'the_excerpt'
		));
		$wp_customize->add_setting( $prefix . '[blog_excerpt_length]', array(
			'default' => 100, 
			'sanitize_callback' => 'absint'
		));

		/* Summary Controls */

		$wp_customize->add_control( $prefix . '[blog_summary]', array(
			'label' => __( 'Summary Display', 'helium' ), 
			'section' => $prefix . '_blog_summary', 
			'type' => 'radio', 
			'choices' => array(
				'the_excerpt' => __( 'Excerpt', 'helium' ), 
				'the_content' => __( 'More Tag', 'helium' ), 
			), 
			'priority' => 1
		));
		$wp_customize->add_control( new Youxi_Customize_Range_Control(
			$wp_customize, $prefix . '[blog_excerpt_length]', array(
				'label' => __( 'Excerpt Length', 'helium' ), 
				'section' => $prefix . '_blog_summary', 
				'min' => 55, 
				'max' => 250, 
				'step' => 1, 
				'priority' => 2
			)
		));	

		/* Section: Layout */

		$wp_customize->add_section( $prefix . '_blog_layout', array(
			'title' => $section_title_prefix . __( 'Layout', 'helium' ), 
			'priority' => ++$section_priority, 
			'panel' => $prefix . '_blog'
		));

		/* Layout Settings */

		$wp_customize->add_setting( $prefix . '[blog_index_layout]', array(
			'default' => 'boxed'
		));
		$wp_customize->add_setting( $prefix . '[blog_archive_layout]', array(
			'default' => 'boxed'
		));
		$wp_customize->add_setting( $prefix . '[blog_single_layout]', array(
			'default' => 'boxed'
		));

		/* Layout Controls */

		$wp_customize->add_control( $prefix . '[blog_index_layout]', array(
			'label' => __( 'Index', 'helium' ), 
			'section' => $prefix . '_blog_layout', 
			'type' => 'select', 
			'choices' => array(
				'boxed' => __( 'Boxed', 'helium' ), 
				'fullwidth' => __( 'Fullwidth', 'helium' ), 
			), 
			'priority' => 1
		));
		$wp_customize->add_control( $prefix . '[blog_archive_layout]', array(
			'label' => __( 'Archive', 'helium' ), 
			'section' => $prefix . '_blog_layout', 
			'type' => 'select', 
			'choices' => array(
				'boxed' => __( 'Boxed', 'helium' ), 
				'fullwidth' => __( 'Fullwidth', 'helium' ), 
			), 
			'priority' => 2
		));
		$wp_customize->add_control( $prefix . '[blog_single_layout]', array(
			'label' => __( 'Single', 'helium' ), 
			'section' => $prefix . '_blog_layout', 
			'type' => 'select', 
			'choices' => array(
				'boxed' => __( 'Boxed', 'helium' ), 
				'fullwidth' => __( 'Fullwidth', 'helium' ), 
			), 
			'priority' => 3
		));


		/* Section: Titles */

		$wp_customize->add_section( $prefix . '_blog_titles', array(
			'title' => $section_title_prefix . __( 'Titles', 'helium' ), 
			'priority' => ++$section_priority, 
			'panel' => $prefix . '_blog'
		));

		/* Titles Settings */

		$wp_customize->add_setting( $prefix . '[blog_index_title]', array(
			'default' => __( 'Welcome to Our Blog', 'helium' ), 
			'sanitize_callback' => 'sanitize_text_field'
		));
		$wp_customize->add_setting( $prefix . '[blog_single_title]', array(
			'default' => __( 'Currently Reading', 'helium' ), 
			'sanitize_callback' => 'sanitize_text_field'
		));
		$wp_customize->add_setting( $prefix . '[blog_category_title]', array(
			'default' => __( 'Category: {category}', 'helium' ), 
			'sanitize_callback' => 'sanitize_text_field'
		));
		$wp_customize->add_setting( $prefix . '[blog_tag_title]', array(
			'default' => __( 'Posts Tagged &lsquo;{tag}&rsquo;', 'helium' ), 
			'sanitize_callback' => 'sanitize_text_field'
		));
		$wp_customize->add_setting( $prefix . '[blog_author_title]', array(
			'default' => __( 'Posts by {author}', 'helium' ), 
			'sanitize_callback' => 'sanitize_text_field'
		));
		$wp_customize->add_setting( $prefix . '[blog_date_title]', array(
			'default' => __( 'Archive for {date}', 'helium' ), 
			'sanitize_callback' => 'sanitize_text_field'
		));

		/* Titles Controls */

		$wp_customize->add_control( $prefix . '[blog_index_title]', array(
			'label' => __( 'Index', 'helium' ), 
			'section' => $prefix . '_blog_titles', 
			'type' => 'text', 
			'priority' => 1
		));
		$wp_customize->add_control( $prefix . '[blog_single_title]', array(
			'label' => __( 'Single', 'helium' ), 
			'section' => $prefix . '_blog_titles', 
			'type' => 'text', 
			'description' => __( 'Use <strong>{title}</strong> for the post title.', 'helium' ), 
			'priority' => 2
		));
		$wp_customize->add_control( $prefix . '[blog_category_title]', array(
			'label' => __( 'Category Archive', 'helium' ), 
			'section' => $prefix . '_blog_titles', 
			'type' => 'text', 
			'description' => __( 'Use <strong>{category}</strong> for the category name.', 'helium' ), 
			'priority' => 3
		));
		$wp_customize->add_control( $prefix . '[blog_tag_title]', array(
			'label' => __( 'Tag Archive', 'helium' ), 
			'section' => $prefix . '_blog_titles', 
			'type' => 'text', 
			'description' => __( 'Use <strong>{tag}</strong> for the tag name.', 'helium' ), 
			'priority' => 4
		));
		$wp_customize->add_control( $prefix . '[blog_author_title]', array(
			'label' => __( 'Author Archive', 'helium' ), 
			'section' => $prefix . '_blog_titles', 
			'type' => 'text', 
			'description' => __( 'Use <strong>{author}</strong> for the author name.', 'helium' ), 
			'priority' => 5
		));
		$wp_customize->add_control( $prefix . '[blog_date_title]', array(
			'label' => __( 'Date Archive', 'helium' ), 
			'section' => $prefix . '_blog_titles', 
			'type' => 'text', 
			'description' => __( 'Use <strong>{date}</strong> for the date.', 'helium' ), 
			'priority' => 6
		));
	}

	public function portfolio_customizer( $wp_customize ) {

		$prefix = $this->prefix();

		if( method_exists( $wp_customize, 'add_panel' ) ) {
			$section_priority  = 0;
			$section_title_prefix = '';
			$wp_customize->add_panel( $prefix . '_portfolio', array(
				'title' => __( 'Portfolio', 'helium' ), 
				'priority' => 46
			));
		} else {
			$section_priority = 46;
			$section_title_prefix = __( 'Portfolio', 'helium' ) . ' ';
		}

		/* Section: Single */

		$wp_customize->add_section( $prefix . '_portfolio_single', array(
			'title' => $section_title_prefix . __( 'Single Item', 'helium' ), 
			'priority' => ++$section_priority, 
			'panel' => $prefix . '_portfolio'
		));

		/* Single Settings */

		$wp_customize->add_setting( $prefix . '[portfolio_show_related_items]', array(
			'default' => true, 
			'sanitize_callback' => array( get_class(), 'sanitize_boolean' )
		));
		$wp_customize->add_setting( $prefix . '[portfolio_related_items_count]', array(
			'default' => 3, 
			'sanitize_callback' => 'absint'
		));
		$wp_customize->add_setting( $prefix . '[portfolio_related_items_behavior]', array(
			'default' => 'lightbox'
		));

		/* Single Controls */

		$wp_customize->add_control( new Youxi_Customize_Switch_Control(
			$wp_customize, $prefix . '[portfolio_show_related_items]', array(
				'label' => __( 'Show Related Items', 'helium' ), 
				'section' => $prefix . '_portfolio_single', 
				'priority' => 1
			)
		));
		$wp_customize->add_control( new Youxi_Customize_Range_Control(
			$wp_customize, $prefix . '[portfolio_related_items_count]', array(
				'label' => __( 'Related Items Count', 'helium' ), 
				'section' => $prefix . '_portfolio_single', 
				'min' => 3, 
				'max' => 4, 
				'step' => 1, 
				'priority' => 2
			)
		));
		$wp_customize->add_control( $prefix . '[portfolio_related_items_behavior]', array(
			'label' => __( 'Related Items Behavior', 'helium' ), 
			'section' => $prefix . '_portfolio_single', 
			'type' => 'select', 
			'choices' => array(
				'lightbox' => __( 'Show Lightbox', 'helium' ), 
				'permalink' => __( 'Go to Post', 'helium' )
			), 
			'priority' => 3
		));


		/* Section: Archive */

		$wp_customize->add_section( $prefix . '_portfolio_archive', array(
			'title' => $section_title_prefix . __( 'Archive', 'helium' ), 
			'priority' => ++$section_priority, 
			'panel' => $prefix . '_portfolio'
		));

		/* Archive Settings */

		$wp_customize->add_setting( $prefix . '[portfolio_archive_page_title]', array(
			'default' => __( 'Portfolio Archive', 'helium' ), 
			'sanitize_callback' => 'sanitize_text_field'
		));

		/* Archive Controls */

		$wp_customize->add_control( $prefix . '[portfolio_archive_page_title]', array(
			'label' => __( 'Page Title', 'helium' ), 
			'section' => $prefix . '_portfolio_archive', 
			'type' => 'text', 
			'priority' => 1
		));

		/* Section: Grid */

		$wp_customize->add_section( $prefix . '_portfolio_grid', array(
			'title' => $section_title_prefix . __( 'Grid Settings', 'helium' ), 
			'priority' => ++$section_priority, 
			'panel' => $prefix . '_portfolio'
		));

		/* Grid Settings */

		$wp_customize->add_setting( $prefix . '[portfolio_grid_show_filter]', array(
			'default' => true, 
			'sanitize_callback' => array( get_class(), 'sanitize_boolean' )
		));
		$wp_customize->add_setting( $prefix . '[portfolio_grid_pagination]', array(
			'default' => 'ajax'
		));
		$wp_customize->add_setting( $prefix . '[portfolio_grid_ajax_button_text]', array(
			'default' => __( 'Load More', 'helium' ), 
			'sanitize_callback' => 'sanitize_text_field'
		));
		$wp_customize->add_setting( $prefix . '[portfolio_grid_ajax_button_complete_text]', array(
			'default' => __( 'No More Items', 'helium' ), 
			'sanitize_callback' => 'sanitize_text_field'
		));
		$wp_customize->add_setting( $prefix . '[portfolio_grid_posts_per_page]', array(
			'default' => get_option( 'posts_per_page' ), 
			'sanitize_callback' => 'absint'
		));
		$wp_customize->add_setting( $prefix . '[portfolio_grid_include]', array(
			'default' => array()
		));
		$wp_customize->add_setting( $prefix . '[portfolio_grid_behavior]', array(
			'default' => 'lightbox'
		));
		$wp_customize->add_setting( $prefix . '[portfolio_grid_orderby]', array(
			'default' => 'date'
		));
		$wp_customize->add_setting( $prefix . '[portfolio_grid_order]', array(
			'default' => 'DESC'
		));
		$wp_customize->add_setting( $prefix . '[portfolio_grid_layout]', array(
			'default' => 'masonry'
		));
		$wp_customize->add_setting( $prefix . '[portfolio_grid_columns]', array(
			'default' => 4, 
			'sanitize_callback' => 'absint'
		));
		// $wp_customize->add_setting( $prefix . '[portfolio_grid_justified_min_height]', array(
		// 	'default' => 240
		// ));

		/* Grid Controls */

		$wp_customize->add_control( new Youxi_Customize_Switch_Control(
			$wp_customize, $prefix . '[portfolio_grid_show_filter]', array(
				'label' => __( 'Show Filter', 'helium' ), 
				'section' => $prefix . '_portfolio_grid', 
				'priority' => 2
			)
		));
		$wp_customize->add_control( $prefix . '[portfolio_grid_pagination]', array(
			'label' => __( 'Pagination', 'helium' ), 
			'section' => $prefix . '_portfolio_grid', 
			'type' => 'select', 
			'choices' => array(
				'ajax' => __( 'AJAX', 'helium' ), 
				'infinite' => __( 'Infinite', 'helium' ), 
				'numbered' => __( 'Numbered', 'helium' ), 
				'prev_next' => __( 'Prev/Next', 'helium' ), 
				'show_all' => __( 'None (Show all)', 'helium' )
			), 
			'priority' => 3
		));
		$wp_customize->add_control( $prefix . '[portfolio_grid_ajax_button_text]', array(
			'label' => __( 'AJAX Button Text', 'helium' ), 
			'section' => $prefix . '_portfolio_grid', 
			'type' => 'text', 
			'priority' => 4
		));
		$wp_customize->add_control( $prefix . '[portfolio_grid_ajax_button_complete_text]', array(
			'label' => __( 'AJAX Button Complete Text', 'helium' ), 
			'section' => $prefix . '_portfolio_grid', 
			'type' => 'text', 
			'priority' => 5
		));
		$wp_customize->add_control( new Youxi_Customize_Range_Control(
			$wp_customize, $prefix . '[portfolio_grid_posts_per_page]', array(
				'label' => __( 'Items per Page', 'helium' ), 
				'section' => $prefix . '_portfolio_grid', 
				'min' => 1, 
				'max' => 20, 
				'step' => 1, 
				'priority' => 5.5
			)
		));
		$wp_customize->add_control( new Youxi_Customize_Multicheck_Control(
			$wp_customize, $prefix . '[portfolio_grid_include]', array(
				'label' => __( 'Included Categories', 'helium' ), 
				'section' => $prefix . '_portfolio_grid', 
				'choices' => get_terms( youxi_portfolio_tax_name(), array( 'fields' => 'id=>name' ) ), 
				'priority' => 5.6, 
				'description' => __( 'Uncheck all to include all categories.', 'helium' )
			)
		));
		$wp_customize->add_control( $prefix . '[portfolio_grid_behavior]', array(
			'label' => __( 'Behavior', 'helium' ), 
			'section' => $prefix . '_portfolio_grid', 
			'type' => 'select', 
			'choices' => array(
				'none' => __( 'None', 'helium' ), 
				'lightbox' => __( 'Show Image in Lightbox', 'helium' ), 
				'page' => __( 'Go to Detail Page', 'helium' )
			), 
			'priority' => 6
		));
		$wp_customize->add_control( $prefix . '[portfolio_grid_orderby]', array(
			'label' => __( 'Order By', 'helium' ), 
			'section' => $prefix . '_portfolio_grid', 
			'type' => 'select', 
			'choices' => array(
				'date' => __( 'Date', 'helium' ), 
				'menu_order' => __( 'Menu Order', 'helium' ), 
				'title' => __( 'Title', 'helium' ), 
				'ID' => __( 'ID', 'helium' ), 
				'rand' => __( 'Random', 'helium' )
			), 
			'priority' => 7
		));
		$wp_customize->add_control( $prefix . '[portfolio_grid_order]', array(
			'label' => __( 'Order', 'helium' ), 
			'section' => $prefix . '_portfolio_grid', 
			'type' => 'select', 
			'choices' => array(
				'DESC' => __( 'Descending', 'helium' ), 
				'ASC' => __( 'Ascending', 'helium' )
			), 
			'priority' => 8
		));
		$wp_customize->add_control( $prefix . '[portfolio_grid_layout]', array(
			'label' => __( 'Layout', 'helium' ), 
			'section' => $prefix . '_portfolio_grid', 
			'type' => 'select', 
			'choices' => array(
				'masonry' => __( 'Masonry', 'helium' ), 
				'classic' => __( 'Classic', 'helium' ), 
				'justified' => __( 'Justified', 'helium' )
			), 
			'priority' => 9
		));
		$wp_customize->add_control( new Youxi_Customize_Range_Control(
			$wp_customize, $prefix . '[portfolio_grid_columns]', array(
				'label' => __( 'Columns (Masonry/Classic)', 'helium' ), 
				'section' => $prefix . '_portfolio_grid', 
				'min' => 3, 
				'max' => 5, 
				'step' => 1, 
				'priority' => 10
			)
		));
		// $wp_customize->add_control( new Youxi_Customize_Range_Control(
		// 	$wp_customize, $prefix . '[portfolio_grid_justified_min_height]', array(
		// 		'label' => __( 'Minimum Height (Justified)', 'helium' ), 
		// 		'section' => $prefix . '_portfolio_grid', 
		// 		'min' => 100, 
		// 		'max' => 600, 
		// 		'step' => 10, 
		// 		'priority' => 10
		// 	)
		// ));

	}

	public function edd_customizer( $wp_customize ) {

		$prefix = $this->prefix();

		if( method_exists( $wp_customize, 'add_panel' ) ) {
			$section_priority = 0;
			$section_title_prefix = '';
			$wp_customize->add_panel( $prefix . '_edd', array(
				'title' => __( 'Easy Digital Downloads', 'helium' ), 
				'priority' => 48
			));
		} else {
			$section_priority = 48;
			$section_title_prefix = __( 'EDD', 'helium' ) . ' ';
		}

		/* Section: General */

		$wp_customize->add_section( $prefix . '_edd_general', array(
			'title' => $section_title_prefix . __( 'General', 'helium' ), 
			'priority' => ++$section_priority, 
			'panel' => $prefix . '_edd'
		));

		/* General Settings */

		$wp_customize->add_setting( $prefix . '[edd_show_cart]', array(
			'default' => true, 
			'sanitize_callback' => array( get_class(), 'sanitize_boolean' )
		));

		/* General Controls */

		$wp_customize->add_control( new Youxi_Customize_Switch_Control(
			$wp_customize, $prefix . '[edd_show_cart]', array(
				'label' => __( 'Show Cart in Header', 'helium' ), 
				'section' => $prefix . '_edd_general', 
				'priority' => 1
			)
		));

		/* Section: Single */

		$wp_customize->add_section( $prefix . '_edd_single', array(
			'title' => $section_title_prefix . __( 'Single Downloads', 'helium' ), 
			'priority' => ++$section_priority, 
			'panel' => $prefix . '_edd'
		));

		/* Single Settings */
		
		$wp_customize->add_setting( $prefix . '[edd_show_categories]', array(
			'default' => true, 
			'sanitize_callback' => array( get_class(), 'sanitize_boolean' )
		));
		$wp_customize->add_setting( $prefix . '[edd_show_tags]', array(
			'default' => true, 
			'sanitize_callback' => array( get_class(), 'sanitize_boolean' )
		));
		$wp_customize->add_setting( $prefix . '[edd_show_sharing_buttons]', array(
			'default' => true, 
			'sanitize_callback' => array( get_class(), 'sanitize_boolean' )
		));
		$wp_customize->add_setting( $prefix . '[edd_show_related_items]', array(
			'default' => true, 
			'sanitize_callback' => array( get_class(), 'sanitize_boolean' )
		));
		$wp_customize->add_setting( $prefix . '[edd_related_items_count]', array(
			'default' => 3, 
			'sanitize_callback' => 'absint'
		));
		$wp_customize->add_setting( $prefix . '[edd_related_items_behavior]', array(
			'default' => 'lightbox'
		));
		

		/* Single Controls */

		$wp_customize->add_control( new Youxi_Customize_Switch_Control(
			$wp_customize, $prefix . '[edd_show_categories]', array(
				'label' => __( 'Show Categories', 'helium' ), 
				'section' => $prefix . '_edd_single', 
				'priority' => 1
			)
		));
		$wp_customize->add_control( new Youxi_Customize_Switch_Control(
			$wp_customize, $prefix . '[edd_show_tags]', array(
				'label' => __( 'Show Tags', 'helium' ), 
				'section' => $prefix . '_edd_single', 
				'priority' => 2
			)
		));
		$wp_customize->add_control( new Youxi_Customize_Switch_Control(
			$wp_customize, $prefix . '[edd_show_sharing_buttons]', array(
				'label' => __( 'Show Sharing Buttons', 'helium' ), 
				'section' => $prefix . '_edd_single', 
				'priority' => 3
			)
		));
		$wp_customize->add_control( new Youxi_Customize_Switch_Control(
			$wp_customize, $prefix . '[edd_show_related_items]', array(
				'label' => __( 'Show Related Items', 'helium' ), 
				'section' => $prefix . '_edd_single', 
				'priority' => 4
			)
		));
		$wp_customize->add_control( new Youxi_Customize_Range_Control(
			$wp_customize, $prefix . '[edd_related_items_count]', array(
				'label' => __( 'Related Items Count', 'helium' ), 
				'section' => $prefix . '_edd_single', 
				'min' => 3, 
				'max' => 4, 
				'step' => 1, 
				'priority' => 5
			)
		));
		$wp_customize->add_control( $prefix . '[edd_related_items_behavior]', array(
			'label' => __( 'Related Items Behavior', 'helium' ), 
			'section' => $prefix . '_edd_single', 
			'type' => 'select', 
			'choices' => array(
				'lightbox' => __( 'Show Lightbox', 'helium' ), 
				'permalink' => __( 'Go to Post', 'helium' )
			), 
			'priority' => 6
		));

		/* Section: Archive */

		$wp_customize->add_section( $prefix . '_edd_archive', array(
			'title' => $section_title_prefix . __( 'Archive', 'helium' ), 
			'priority' => ++$section_priority, 
			'panel' => $prefix . '_edd'
		));

		/* Archive Settings */

		$wp_customize->add_setting( $prefix . '[edd_archive_page_title]', array(
			'default' => __( 'Downloads Archive', 'helium' ), 
			'sanitize_callback' => 'sanitize_text_field'
		));

		/* Archive Controls */

		$wp_customize->add_control( $prefix . '[edd_archive_page_title]', array(
			'label' => __( 'Page Title', 'helium' ), 
			'section' => $prefix . '_edd_archive', 
			'type' => 'text', 
			'priority' => 1
		));

		/* Section: Grid */

		$wp_customize->add_section( $prefix . '_edd_grid', array(
			'title' => $section_title_prefix . __( 'Grid Settings', 'helium' ), 
			'priority' => ++$section_priority, 
			'panel' => $prefix . '_edd'
		));

		/* Grid Settings */

		$wp_customize->add_setting( $prefix . '[edd_grid_pagination]', array(
			'default' => 'ajax'
		));
		$wp_customize->add_setting( $prefix . '[edd_grid_ajax_button_text]', array(
			'default' => __( 'Load More', 'helium' ), 
			'sanitize_callback' => 'sanitize_text_field'
		));
		$wp_customize->add_setting( $prefix . '[edd_grid_ajax_button_complete_text]', array(
			'default' => __( 'No More Items', 'helium' ), 
			'sanitize_callback' => 'sanitize_text_field'
		));
		$wp_customize->add_setting( $prefix . '[edd_grid_posts_per_page]', array(
			'default' => get_option( 'posts_per_page' ), 
			'sanitize_callback' => 'absint'
		));
		$wp_customize->add_setting( $prefix . '[edd_grid_include]', array(
			'default' => array()
		));
		$wp_customize->add_setting( $prefix . '[edd_grid_behavior]', array(
			'default' => 'lightbox'
		));
		$wp_customize->add_setting( $prefix . '[edd_grid_columns]', array(
			'default' => 4, 
			'sanitize_callback' => 'absint'
		));

		/* Archive Controls */

		$wp_customize->add_control( $prefix . '[edd_grid_pagination]', array(
			'label' => __( 'Pagination', 'helium' ), 
			'section' => $prefix . '_edd_grid', 
			'type' => 'select', 
			'choices' => array(
				'ajax' => __( 'AJAX', 'helium' ), 
				'infinite' => __( 'Infinite', 'helium' ), 
				'numbered' => __( 'Numbered', 'helium' ), 
				'prev_next' => __( 'Prev/Next', 'helium' ), 
				'show_all' => __( 'None (Show all)', 'helium' )
			), 
			'priority' => 3
		));
		$wp_customize->add_control( $prefix . '[edd_grid_ajax_button_text]', array(
			'label' => __( 'AJAX Button Text', 'helium' ), 
			'section' => $prefix . '_edd_grid', 
			'type' => 'text', 
			'priority' => 4
		));
		$wp_customize->add_control( $prefix . '[edd_grid_ajax_button_complete_text]', array(
			'label' => __( 'AJAX Button Complete Text', 'helium' ), 
			'section' => $prefix . '_edd_grid', 
			'type' => 'text', 
			'priority' => 5
		));
		$wp_customize->add_control( new Youxi_Customize_Range_Control(
			$wp_customize, $prefix . '[edd_grid_posts_per_page]', array(
				'label' => __( 'Items per Page', 'helium' ), 
				'section' => $prefix . '_edd_grid', 
				'min' => 1, 
				'max' => 20, 
				'step' => 1, 
				'priority' => 5.5
			)
		));
		$wp_customize->add_control( new Youxi_Customize_Multicheck_Control(
			$wp_customize, $prefix . '[edd_grid_include]', array(
				'label' => __( 'Included Categories', 'helium' ), 
				'section' => $prefix . '_edd_grid', 
				'choices' => get_terms( 'download_category', array( 'fields' => 'id=>name', 'hide_empty' => false ) ), 
				'priority' => 5.6, 
				'description' => __( 'Uncheck all to include all categories.', 'helium' )
			)
		));
		$wp_customize->add_control( $prefix . '[edd_grid_behavior]', array(
			'label' => __( 'Behavior', 'helium' ), 
			'section' => $prefix . '_edd_grid', 
			'type' => 'select', 
			'choices' => array(
				'none' => __( 'None', 'helium' ), 
				'lightbox' => __( 'Show Image in Lightbox', 'helium' ), 
				'page' => __( 'Go to Detail Page', 'helium' )
			), 
			'priority' => 6
		));
		$wp_customize->add_control( new Youxi_Customize_Range_Control(
			$wp_customize, $prefix . '[edd_grid_columns]', array(
				'label' => __( 'Number of Columns', 'helium' ), 
				'section' => $prefix . '_edd_grid', 
				'min' => 3, 
				'max' => 5, 
				'step' => 1, 
				'priority' => 7
			)
		));

		// foreach( $wp_customize->settings() as $setting ) {
		// 	if( preg_match( '/^helium_settings\[/', $setting->id ) ) {
		// 		printf( "'%s' => '%s', \n", $setting->id, $setting->default );
		// 	}
		// }
	}
}
new Helium_Customize_Manager();
