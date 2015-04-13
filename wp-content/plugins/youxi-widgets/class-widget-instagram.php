<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Hi there!  I\'m just a plugin, not much I can do when called directly.' );
}

class Youxi_Instagram_Widget extends Youxi_WP_Widget {

	private static $ajax_hook_registered = false;

	public function __construct() {

		$widget_opts  = array( 'classname' => 'instagram-widget', 'description' => __( 'Use this widget to display your Instagram feed.', 'youxi' ) );
		$control_opts = array();

		// Initialize WP_Widget
		parent::__construct( 'instagram-widget', __( 'Youxi &raquo; Instagram', 'youxi' ), $widget_opts, $control_opts );

		if( ! self::$ajax_hook_registered ) {

			$ajax_action = apply_filters( 'youxi_widgets_instagram_ajax_action', 'youxi_get_instafeed' );

			if( ! has_action( "wp_ajax_{$ajax_action}"  ) ) {
				add_action( "wp_ajax_{$ajax_action}", array( 'Youxi_Instagram_Widget', 'get_feed' ) );
			}
			if( ! has_action( "wp_ajax_nopriv_{$ajax_action}" ) ) {
				add_action( "wp_ajax_nopriv_{$ajax_action}", array( 'Youxi_Instagram_Widget', 'get_feed' ) );
			}

			self::$ajax_hook_registered = true;
		}
	}


	public function widget( $args, $instance ) {
		
		extract( $args, EXTR_SKIP );

		$instance = wp_parse_args( (array) $instance, array(
			'title'    => '', 
			'username' => '', 
			'count'    => 8
		) );

		$instance = apply_filters( "youxi_widgets_{$this->id_base}_instance", $instance, $this->id );

		echo $before_widget;

		if( isset( $instance['title'] ) && ! empty( $instance['title'] ) )
			echo $before_title . apply_filters( 'widget_title', $instance['title'] ) . $after_title;

		$this->maybe_load_template( $id, $instance );

		echo $after_widget;
	}

	public function form( $instance ) {

		$vars = wp_parse_args( (array) $instance, array(
			'title'    => __( 'My Instagram Feed', 'youxi' ), 
			'username' => '', 
			'count'    => 8
		));

		extract( $vars );

		?><p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( 'Title', 'youxi' ); ?>:</label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'username' ) ); ?>"><?php _e( 'Instagram Username', 'youxi' ); ?>:</label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'username' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'username' ) ); ?>" type="text" value="<?php echo esc_attr( $username ); ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'count' ) ); ?>"><?php _e( 'Maximum Photos', 'youxi' ); ?>:</label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'count' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'count' ) ); ?>" type="number" min="1" value="<?php echo esc_attr( $count ); ?>">
		</p>
		<?php 
	}

	public function update( $new_instance, $old_instance ) {

		$instance = array();

		$instance['title']    = strip_tags( $new_instance['title'] );
		$instance['username'] = strip_tags( preg_replace( '/[^\w.]/', '', $new_instance['username'] ) );
		$instance['count']    = absint( strip_tags( $new_instance['count'] ) );

		return apply_filters( "youxi_widgets_{$this->id_base}_new_instance", $instance, $this->id );
	}

	public function config_vars( $vars ) {

		$vars = parent::config_vars( $vars );

		$widget_name = preg_replace( array( '/\W/', '/_?widget_?/' ), '', $this->id_base );
		if( isset( $vars[ $widget_name ] ) ) {
			$vars[ $widget_name ] = array_merge( $vars[ $widget_name ], array(
				'ajaxAction' => apply_filters( 'youxi_widgets_instagram_ajax_action', 'youxi_get_instafeed' )
			));
		}

		return $vars;
	}

	public static function sanitize( &$value, $key ) {

		if( preg_match( '/^(text|full_name|bio)$/', $key ) ) {
			$value = preg_replace( '/([0-9|#][\x{20E3}])|[\x{00ae}|\x{00a9}|\x{203C}|\x{2047}|\x{2048}|\x{2049}|\x{3030}|\x{303D}|\x{2139}|\x{2122}|\x{3297}|\x{3299}][\x{FE00}-\x{FEFF}]?|[\x{2190}-\x{21FF}][\x{FE00}-\x{FEFF}]?|[\x{2300}-\x{23FF}][\x{FE00}-\x{FEFF}]?|[\x{2460}-\x{24FF}][\x{FE00}-\x{FEFF}]?|[\x{25A0}-\x{25FF}][\x{FE00}-\x{FEFF}]?|[\x{2600}-\x{27BF}][\x{FE00}-\x{FEFF}]?|[\x{2900}-\x{297F}][\x{FE00}-\x{FEFF}]?|[\x{2B00}-\x{2BF0}][\x{FE00}-\x{FEFF}]?|[\x{1F000}-\x{1F6FF}][\x{FE00}-\x{FEFF}]?/u', '', $value );
		}
	}

	public static function get_feed() {

		if( isset( $_REQUEST['instagram'] ) ) {

			extract( wp_parse_args( $_REQUEST['instagram'], array(
				'username'  => '', 
				'count'     => 8, 
				'client_id' => apply_filters( 'youxi_widgets_instagram_client_id', '48105b2c76194d68bc02cc2d2e19822a' )
			)));

			$cache_key = apply_filters( 'youxi_instagram_transient_prefix', 'youxi_instagram_' ) . $username;

			// Try reading from cache first
			$data = get_transient( $cache_key );

			// If the cache is invalid, we'll make an API request
			if( ! is_array( $data ) ) {

				// Get a user by user id
				$url = 'https://api.instagram.com/v1/users/search';
				$url = add_query_arg( array( 'q' => $username, 'client_id' => $client_id, 'count' => 1 ), $url );

				$response = wp_remote_get( $url, array(
					'timeout' => 10, 
					'sslverify' => false
				));

				if( 200 == wp_remote_retrieve_response_code( $response ) ) {

					$body = wp_remote_retrieve_body( $response );
					$body = json_decode( $body, true );

					if( ! is_null( $body ) && isset( $body['data'], $body['data'][0], $body['data'][0]['id'] ) ) {
						
						$url = 'https://api.instagram.com/v1/users/' . $body['data'][0]['id'] . '/media/recent';
						$url = add_query_arg( array( 'count' => $count, 'client_id' => $client_id ), $url );

						$response = wp_remote_get( $url, array(
							'timeout' => 10, 
							'sslverify' => false
						));

						if( 200 == wp_remote_retrieve_response_code( $response ) ) {

							$body = wp_remote_retrieve_body( $response );
							$body = json_decode( $body, true );

							if( ! is_null( $body ) && isset( $body['data'] ) ) {
								
								$data = $body['data'];
								array_walk_recursive( $data, array( get_class(), 'sanitize' ) );
								set_transient( $cache_key, $data, HOUR_IN_SECONDS );

								wp_send_json_success( $data );
							}
						}
					} else {
						wp_send_json_error( array( 'error_message' => __( 'The username ' . $username . ' is invalid.' ) ) );
					}
				}

				$body = wp_remote_retrieve_body( $response );
				$body = json_decode( $body, true );
				if( isset( $body['meta'], $body['meta']['error_message'] ) ) {
					wp_send_json_error( $body['meta'] );
				}

			} else {
				wp_send_json_success( $data );
			}
		}

		wp_send_json_error();
	}
}