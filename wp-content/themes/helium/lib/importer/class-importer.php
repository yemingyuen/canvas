<?php

function handle_nav_menu_locations( $data ) {

	$menu_locations = get_nav_menu_locations();
	$new_menu_locations = array();

	foreach( (array) $data as $menu_location => $slug ) {
		if( ! is_string( $slug ) ) {
			continue;
		}
		$menu = wp_get_nav_menus( compact( 'slug' ) );
		if( ! empty( $menu ) && isset( $menu[0] ) ) {
			$new_menu_locations[ $menu_location ] = $menu[0]->term_id;
		}
	}

	if( empty( $new_menu_locations ) ) {
		return __( 'No nav menu locations updated', 'youxi' );
	}

	set_theme_mod( 'nav_menu_locations', 
		array_merge( $menu_locations, $new_menu_locations ) );

	$result = array();
	foreach( $new_menu_locations as $location => $id ) {
		$result[] =  $location . ': ' . $id;
	}

	return empty( $result ) ? 
		__( 'No nav menu locations updated', 'youxi' ) : implode( ', ', $result );
}

function handle_frontpage_displays( $data ) {

	extract( wp_parse_args( $data, array(
		'show_on_front'  => 'posts', 
		'page_on_front'  => '', 
		'page_for_posts' => ''
	)));

	$result = array();
	if( ! empty( $show_on_front ) ) {
		update_option( 'show_on_front', $show_on_front );
		$result[] = 'show_on_front: ' . $show_on_front;
	}
	if( ! empty( $page_on_front ) ) {
		update_option( 'page_on_front', $page_on_front );
		$result[] = 'page_on_front: ' . $page_on_front;
	}
	if( ! empty( $page_for_posts ) ) {
		update_option( 'page_for_posts', $page_for_posts );
		$result[] = 'page_for_posts: ' . $page_for_posts;
	}

	return empty( $result ) ? 
		__( 'Frontpage display settings not imported.', 'youxi' ) : implode( ', ', $result );
}

function handle_xml_import( $data ) {

	if( ! class_exists( 'WP_Import' ) ) {
		$wp_importer = get_template_directory() . '/lib/vendor/wordpress-importer/wordpress-importer.php';
		if( is_readable( $wp_importer ) ) {
			require $wp_importer;
		} else {
			return new WP_Error( 'xml_wp_importer_missing', __( 'The WordPress importer class can\'t be found.', 'youxi' ) );
		}
	}

	// Import the content
	$wp_import = new WP_Import();
	$wp_import->fetch_attachments = true;

	ob_start();

	set_time_limit(0);
	$wp_import->import( $data );

	return ob_get_clean();
}

function handle_widgets_import( $data ) {

	// Decode and validate the data
	if ( ! is_array( $data ) ) {

		$data = json_decode( $data, true );
		if( empty( $data ) || ! is_array( $data ) ) {
			return new WP_Error( 'widgets_invalid_data', __( 'Widgets import data could not be read.', 'youxi' ) );
		}
	}

	global $wp_registered_sidebars, $wp_registered_widget_controls;

	// Get all available widgets site supports
	$available_widgets = array();
	foreach( $wp_registered_widget_controls as $widget ) {

		if ( ! empty( $widget['id_base'] ) && ! isset( $available_widgets[$widget['id_base']] ) ) { // no dupes

			$available_widgets[$widget['id_base']]['id_base'] = $widget['id_base'];
			$available_widgets[$widget['id_base']]['name'] = $widget['name'];
		}
	}

	// Get all existing widget instances
	$widget_instances = array();
	foreach ( $available_widgets as $widget_data ) {
		$widget_instances[$widget_data['id_base']] = get_option( 'widget_' . $widget_data['id_base'] );
	}

	// Begin results
	$results = array();

	// Loop import data's sidebars
	foreach ( $data as $sidebar_id => $widgets ) {

		// Skip inactive widgets
		// (should not be in export file)
		if ( 'wp_inactive_widgets' == $sidebar_id ) {
			continue;
		}

		// Check if sidebar is available on this site
		// Otherwise add widgets to inactive, and say so
		if ( isset( $wp_registered_sidebars[$sidebar_id] ) ) {
			$sidebar_available = true;
			$use_sidebar_id = $sidebar_id;
			$sidebar_message_type = 'success';
			$sidebar_message = '';
		} else {
			$sidebar_available = false;
			$use_sidebar_id = 'wp_inactive_widgets'; // add to inactive if sidebar does not exist in theme
			$sidebar_message_type = 'error';
			$sidebar_message = __( 'Sidebar does not exist in theme (using Inactive)', 'youxi' );
		}

		// Result for sidebar
		$results[$sidebar_id]['name'] = ! empty( $wp_registered_sidebars[$sidebar_id]['name'] ) ? $wp_registered_sidebars[$sidebar_id]['name'] : $sidebar_id; // sidebar name if theme supports it; otherwise ID
		$results[$sidebar_id]['message_type'] = $sidebar_message_type;
		$results[$sidebar_id]['message'] = $sidebar_message;
		$results[$sidebar_id]['widgets'] = array();

		// Loop widgets
		foreach ( $widgets as $widget_instance_id => $widget ) {

			$fail = false;

			// Get id_base (remove -# from end) and instance ID number
			$id_base = preg_replace( '/-[0-9]+$/', '', $widget_instance_id );
			$instance_id_number = str_replace( $id_base . '-', '', $widget_instance_id );

			// Does site support this widget?
			if ( ! $fail && ! isset( $available_widgets[$id_base] ) ) {
				$fail = true;
				$widget_message_type = 'error';
				$widget_message = __( 'Site does not support widget', 'youxi' ); // explain why widget not imported
			}

			// Does widget with identical settings already exist in same sidebar?
			if ( ! $fail && isset( $widget_instances[$id_base] ) ) {

				// Get existing widgets in this sidebar
				$sidebars_widgets = get_option( 'sidebars_widgets' );
				$sidebar_widgets = isset( $sidebars_widgets[$use_sidebar_id] ) ? $sidebars_widgets[$use_sidebar_id] : array(); // check Inactive if that's where will go

				// Loop widgets with ID base
				$single_widget_instances = ! empty( $widget_instances[$id_base] ) ? $widget_instances[$id_base] : array();
				foreach ( $single_widget_instances as $check_id => $check_widget ) {

					// Is widget in same sidebar and has identical settings?
					if ( in_array( "$id_base-$check_id", $sidebar_widgets ) && (array) $widget == $check_widget ) {

						$fail = true;
						$widget_message_type = 'warning';
						$widget_message = __( 'Widget already exists', 'youxi' ); // explain why widget not imported

						break;

					}
	
				}

			}

			// No failure
			if ( ! $fail ) {

				// Add widget instance
				$single_widget_instances = get_option( 'widget_' . $id_base ); // all instances for that widget ID base, get fresh every time
				$single_widget_instances = ! empty( $single_widget_instances ) ? $single_widget_instances : array( '_multiwidget' => 1 ); // start fresh if have to
				$single_widget_instances[] = (array) $widget; // add it

					// Get the key it was given
					end( $single_widget_instances );
					$new_instance_id_number = key( $single_widget_instances );

					// If key is 0, make it 1
					// When 0, an issue can occur where adding a widget causes data from other widget to load, and the widget doesn't stick (reload wipes it)
					if ( '0' === strval( $new_instance_id_number ) ) {
						$new_instance_id_number = 1;
						$single_widget_instances[$new_instance_id_number] = $single_widget_instances[0];
						unset( $single_widget_instances[0] );
					}

					// Move _multiwidget to end of array for uniformity
					if ( isset( $single_widget_instances['_multiwidget'] ) ) {
						$multiwidget = $single_widget_instances['_multiwidget'];
						unset( $single_widget_instances['_multiwidget'] );
						$single_widget_instances['_multiwidget'] = $multiwidget;
					}

					// Update option with new widget
					update_option( 'widget_' . $id_base, $single_widget_instances );

				// Assign widget instance to sidebar
				$sidebars_widgets = get_option( 'sidebars_widgets' ); // which sidebars have which widgets, get fresh every time
				$new_instance_id = $id_base . '-' . $new_instance_id_number; // use ID number from new widget instance
				$sidebars_widgets[$use_sidebar_id][] = $new_instance_id; // add new instance to sidebar
				update_option( 'sidebars_widgets', $sidebars_widgets ); // save the amended data

				// Success message
				if ( $sidebar_available ) {
					$widget_message_type = 'success';
					$widget_message = __( 'Imported', 'youxi' );
				} else {
					$widget_message_type = 'warning';
					$widget_message = __( 'Imported to Inactive', 'youxi' );
				}

			}

			// Result for widget instance
			$results[$sidebar_id]['widgets'][$widget_instance_id]['name'] = isset( $available_widgets[$id_base]['name'] ) ? $available_widgets[$id_base]['name'] : $id_base; // widget name or ID if name not available (not supported by site)
			$results[$sidebar_id]['widgets'][$widget_instance_id]['title'] = $widget->title ? $widget->title : __( 'No Title', 'youxi' ); // show "No Title" if widget instance is untitled
			$results[$sidebar_id]['widgets'][$widget_instance_id]['message_type'] = $widget_message_type;
			$results[$sidebar_id]['widgets'][$widget_instance_id]['message'] = $widget_message;

		}

	}

	return $results;
}

function handle_ot_import( $data ) {

	if( ! class_exists( 'OT_Loader' ) ) {
		return new WP_Error( 'theme_options_ot_not_found', __( 'Option Tree is not installed, theme options not imported.', 'youxi' ) );
	}

	try {

		/* textarea value */
		$options = unserialize( ot_decode( $data ) );

		/* get settings array */
		$settings = get_option( ot_settings_id() );

		/* has options */
		if ( is_array( $options ) ) {

			/* validate options */
			if ( is_array( $settings ) ) {

				foreach( $settings['settings'] as $setting ) {

					if ( isset( $options[$setting['id']] ) ) {

						$content = ot_stripslashes( $options[$setting['id']] );

						$options[$setting['id']] = ot_validate_setting( $content, $setting['type'], $setting['id'] );

					}

				}

			}

			/* update the option tree array */
			update_option( ot_options_id(), $options );
		}
	} catch( Exception $e ) {
		return new WP_Error( 'theme_options_unknown_error', $e->getMessage() );
	}

	return __( 'Theme options successfully imported.', 'youxi' );
}

final class Youxi_Demo_Importer_Page {

	private $page_hook;

	private $importers = array();

	public function __construct() {

		add_action( 'admin_menu', array( $this, 'admin_menu' ) );

		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );

		add_action( 'wp_ajax_youxi_import_demo', array( $this, 'wp_ajax_handle_import' ) );
	}

	public function admin_menu() {

		$this->page_hook = add_management_page(
			__( 'Youxi Demo Content Importer', 'youxi' ), __( 'Youxi Importer', 'youxi' ), 
			'import', 'youxi-importer', array( $this, 'importer_page_callback' )
		);
	}

	public function admin_enqueue_scripts( $hook ) {

		if( $hook != $this->page_hook ) {
			return;
		}

		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		wp_enqueue_style( 'youxi-demo-importer', 
			get_template_directory_uri() . '/lib/importer/assets/css/youxi.demo-importer.css', 
			array(), '0.1' );

		wp_enqueue_script( 'ajax-queue', 
			get_template_directory_uri() . "/lib/importer/assets/plugins/ajaxqueue/jquery.ajaxQueue{$suffix}.js", 
			array( 'jquery' ), '0.1.2', true );

		wp_enqueue_script( 'youxi-demo-importer', 
			get_template_directory_uri() . "/lib/importer/assets/js/youxi.demo-importer{$suffix}.js", 
			array( 'ajax-queue' ), '0.1', true );

		wp_localize_script( 'youxi-demo-importer', '_demoImporterSettings', $this->get_import_settings() );
	}

	public function wp_ajax_error( $error ) {
		if( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			wp_send_json_error( compact( 'error' ) );
		} else {
			wp_die( '-1' );
		}
	}

	public function wp_ajax_handle_import() {

		if( ! isset( $_POST['task'] ) ) {
			$this->wp_ajax_error( __( 'Invalid request.', 'youxi' ) );
		}

		$task = wp_parse_args( $_POST['task'], array( 'task_id' => '', 'demo_id' => '' ) );

		$available_demos = $this->get_available_demos();
		if( ! array_key_exists( $task['demo_id'], $available_demos ) ) {
			$this->wp_ajax_error( __( 'Invalid demo content requested.', 'youxi' ) );
		}

		if( ! in_array( $task['task_id'], array_keys( $this->get_import_tasks() ) ) ) {
			$this->wp_ajax_error( __( 'Invalid import task.', 'youxi' ) );
		}

		check_ajax_referer( 'import_demo_' . $task['demo_id'] );

		// Everything OK, let's do the job
		$result = $this->do_import( $task );
		if( ! is_wp_error( $result ) ) {

			// We have imported something, let's remember that
			if( ! add_option( '_youxi_demo_importer_has_import', true, '', 'no' ) ) {
				update_option( '_youxi_demo_importer_has_import', true );
			}

			// Return the result whatever it is
			wp_send_json_success( compact( 'result' ) );

		} else {
			if( is_wp_error( $result ) ) {
				$this->wp_ajax_error( $result->get_error_message() );
			} else {
				$this->wp_ajax_error( __( 'An unknown error has occured.', 'youxi' ) );
			}
		}
	}

	public function do_import( $task ) {

		$available_demos = $this->get_available_demos();
		extract( wp_parse_args( $task, array( 'task_id' => '', 'demo_id' => '' ) ), EXTR_SKIP );

		if( isset( $available_demos[ $demo_id ] ) ) {

			$current_demo = $available_demos[ $demo_id ];

			if( isset( $current_demo['content'][ $task_id ] ) ) {

				$handler = $this->get_import_handler( $task_id );
				if( is_callable( $handler ) ) {
					return call_user_func( $handler, $current_demo['content'][ $task_id ] );
				} else {
					return new WP_Error( 'importer_invalid_task', sprintf( __( 'The handler for the task %s is invalid.', 'youxi' ), $task_id ) );
				}
			}
		}
	}

	public function get_available_demos() {
		return apply_filters( 'youxi_demo_importer_demos', array() );
	}

	public function get_import_tasks() {
		return apply_filters( 'youxi_demo_importer_tasks', array(
			'xml' => array(
				'status' => __( 'Importing posts, pages, comments, custom fields, categories, and tags.', 'youxi' ), 
				'handler' => 'handle_xml_import'
			), 
			'theme-options' => array(
				'status' => __( 'Importing theme options', 'youxi' ), 
				'handler' => 'handle_ot_import'
			), 
			'widgets' => array(
				'status' => __( 'Importing widgets', 'youxi' ), 
				'handler' => 'handle_widgets_import'
			), 
			'frontpage_displays' => array(
				'status' => __( 'Importing front page options', 'youxi' ), 
				'handler' => 'handle_frontpage_displays'
			), 
			'nav_menu_locations' => array(
				'status' => __( 'Importing nav menu locations', 'youxi' ), 
				'handler' => 'handle_nav_menu_locations'
			)
		));
	}

	public function get_import_handler( $id ) {
		$tasks = $this->get_import_tasks();
		if( isset( $tasks[ $id ], $tasks[ $id ]['handler'] ) ) {
			return $tasks[ $id ]['handler'];
		}
	}

	public function get_import_settings() {
		return apply_filters( 'youxi_demo_importer_settings', array(
			'ajaxUrl'                  => admin_url( 'admin-ajax.php' ), 
			'ajaxAction'               => 'youxi_import_demo', 
			'importTasks'              => $this->get_import_tasks(), 
			'doneMessage'              => __( 'Import Completed', 'youxi' ), 
			'failMessage'              => __( 'Import Failed', 'youxi' ), 
			'hasPreviousImportMessage' => __( 'You have previously imported a demo content, are you sure you want to import again?' ), 
			'beforeUnloadMessage'      => __( 'You haven\'t finishid importing the demo content. If you leave now, the demo content will not be imported.', 'youxi' ), 
			'importFinishTimeout'      => 2000, 
			'importDebug'              => defined( 'WP_DEBUG' ) && WP_DEBUG, 
			'hasPreviousImport'        => get_option( '_youxi_demo_importer_has_import', false )
		));
	}

	public function importer_page_callback() {
		?>
		<div class="wrap demo-importer">

			<h2><?php _e( 'Youxi Demo Content Importer', 'youxi' ) ?></h2>

			<?php if( $available_demos = $this->get_available_demos() ): ?>

			<div class="theme-browser demo-browser rendered">

				<div class="themes demos">

					<?php foreach( $available_demos as $id => $args ):
						$args = wp_parse_args( $args, array(
							'screenshot' => '', 
							'name' => ''
						));
					?>

					<div class="theme demo-content active" tabindex="0" data-demo-id="<?php echo esc_attr( $id ) ?>" data-wp-nonce="<?php echo wp_create_nonce( 'import_demo_' . $id ) ?>">

						<div class="theme-screenshot demo-screenshot">
							<img src="<?php echo esc_url( $args['screenshot'] ) ?>" alt="<?php echo esc_attr( $args['name'] ) ?>">
						</div>

						<span class="more-details"></span>

						<h3 class="theme-name demo-name"><?php echo esc_html( $args['name'] ) ?></h3>

						<div class="theme-actions demo-actions">
							<button type="button" class="button button-primary"><?php _e( 'Import', 'youxi' ) ?></button>
						</div>

					</div>

					<?php endforeach; ?>

				</div>

				<br class="clear">

			</div>

			<?php else:
				echo '<div class="error settings-error"><p>' . __( 'There are no available demo content to import.', 'youxi' ) . '</p></div>';
			endif;
			?>
		</div>
		<?php
	}
}
new Youxi_Demo_Importer_Page();
