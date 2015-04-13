<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Hi there!  I\'m just a plugin, not much I can do when called directly.' );
}

class Youxi_Posts_Widget extends Youxi_WP_Widget {

	public function __construct() {

		$widget_opts  = array( 'classname' => 'posts-widget', 'description' => __( 'Use this widget to display your posts based on a specific criteria.', 'youxi' ) );
		$control_opts = array();

		// Initialize WP_Widget
		parent::__construct( 'posts-widget', __( 'Youxi &raquo; Posts', 'youxi' ), $widget_opts, $control_opts );
	}

	public function widget( $args, $instance ) {

		extract( $args, EXTR_SKIP );

		$instance = wp_parse_args( (array) $instance, array(
			'title' => '', 
			'posts_per_page' => 3, 
			'category__not_in' => array(), 
			'tag__not_in' => array(), 
			'meta_display' => 'date', 
			'order' => 'DESC', 
			'orderby' => 'post_date'
		));

		$instance = apply_filters( "youxi_widgets_{$this->id_base}_instance", $instance, $this->id );

		extract( $instance, EXTR_SKIP );

		echo $before_widget;

		if( ! empty( $title ) ) {
			echo $before_title . apply_filters( 'widget_title', $instance['title'] ) . $after_title;
		}

		global $post;
		$tmp_post = $post;

		// Setup the query
		$query_args = apply_filters( "youxi_widgets_{$this->id_base}_query_args", array(
			'posts_per_page' => $posts_per_page, 
			'category__not_in' => (array) $category__not_in, 
			'tag__not_in' => (array) $tag__not_in, 
			'order' => $order, 
			'orderby' => $orderby, 
			'suppress_filters' => false
		));
		$posts = get_posts( $query_args );

		$this->maybe_load_template( $id, compact( 'meta_display', 'posts' ) );

		$post = $tmp_post;
		if( is_a( $post, 'WP_Post' ) ) {
			setup_postdata( $post );
		}

		echo $after_widget;
	}

	public function form( $instance ) {

		$vars = wp_parse_args( (array) $instance, array(
			'title' => '', 
			'posts_per_page' => 3, 
			'category__not_in' => array(), 
			'tag__not_in' => array(), 
			'meta_display' => 'date', 
			'order' => 'DESC', 
			'orderby' => 'post_date'
		));

		extract( $vars );

		?><p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( 'Title', 'youxi' ); ?>:</label> 
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'posts_per_page' ) ); ?>"><?php _e( 'Number of Posts', 'youxi' ); ?>:</label> 
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'posts_per_page' ) ); ?>" name="<?php echo $this->get_field_name( 'posts_per_page' ); ?>" type="text" value="<?php echo esc_attr( $posts_per_page ); ?>">
		</p>
		<?php if( $categories = get_categories() ): ?>
		<p>
			<label><?php _e( 'Categories to Exclude', 'youxi' ); ?>:</label> 
			<br>
			<?php foreach( $categories as $index => $term ): ?>
				<input id="<?php echo esc_attr( $this->get_field_id( 'category__not_in' ) . "_{$index}" ) ?>" type="checkbox" name="<?php echo esc_attr( $this->get_field_name( 'category__not_in' ) ); ?>[]" value="<?php echo esc_attr( $term->term_id ) ?>" <?php checked( in_array( $term->term_id, (array) $category__not_in ) ) ?>>
				<label for="<?php echo esc_attr( $this->get_field_id( 'category__not_in' ) . "_{$index}" ) ?>"><?php echo esc_html( $term->name ) ?></label>
				<br>
			<?php endforeach; ?>
		</p>
		<?php endif;
		if( $tags = get_tags() ): ?>
		<p>
			<label><?php _e( 'Tags to Exclude', 'youxi' ); ?>:</label> 
			<br>
			<?php foreach( $tags as $index => $term ): ?>
				<input id="<?php echo esc_attr( $this->get_field_id( 'tag__not_in' ) . "_{$index}" ) ?>" type="checkbox" name="<?php echo esc_attr( $this->get_field_name( 'tag__not_in' ) ) ?>[]" value="<?php echo esc_attr( $term->term_id ) ?>" <?php checked( in_array( $term->term_id, (array) $tag__not_in ) ) ?>>
				<label for="<?php echo esc_attr( $this->get_field_id( 'tag__not_in' ) . "_{$index}" ) ?>"><?php echo esc_html( $term->name ) ?></label>
				<br>
			<?php endforeach; ?>
		</p>
		<?php endif; ?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'meta_display' ) ); ?>"><?php _e( 'Meta Display', 'youxi' ); ?>:</label>
			<select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'meta_display' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'meta_display' ) ); ?>">
				<?php
				$meta_display_choices = apply_filters( "youxi_widgets_{$this->id_base}_meta_display_choices", array(
					'date' => __( 'Date', 'youxi' ), 
					'author' => __( 'Author', 'youxi' ), 
					'category' => __( 'Category', 'youxi' ), 
					'tags' => __( 'Tags', 'youxi' )
				));
				foreach( $meta_display_choices as $meta => $label ): ?>
				<option value="<?php echo esc_attr( $meta ) ?>" <?php selected( $meta_display, $meta ) ?>>
					<?php echo esc_html( $label ); ?>
				</option>
				<?php endforeach; ?>
			</select>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'order' ) ); ?>"><?php _e( 'Order', 'youxi' ); ?>:</label>
			<select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'order' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'order' ) ); ?>">
				<?php foreach( array( 'desc' => __( 'Descending', 'youxi' ), 'asc' => __( 'Ascending', 'youxi' ) ) as $_order => $label ): ?>
				<option value="<?php echo esc_attr( strtoupper( $_order ) ) ?>" <?php selected( $order, $_order ) ?>>
					<?php echo esc_html( $label ); ?>
				</option>
				<?php endforeach; ?>
			</select>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'orderby' ) ); ?>"><?php _e( 'Order By', 'youxi' ); ?>:</label>
			<select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'orderby' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'orderby' ) ); ?>">
				<?php
				$orderby_choices = apply_filters( "youxi_widgets_{$this->id_base}_orderby_choices", array(
					'date' => __( 'Date', 'youxi' ), 
					'comment_count' => __( 'Comment Count', 'youxi' ), 
					'author' => __( 'Author', 'youxi' ), 
					'title' => __( 'Post Title', 'youxi' ), 
					'modified' => __( 'Last Modified Date', 'youxi' ), 
					'rand' => __( 'Random', 'youxi' ), 
					'ID' => 'ID'
				));
				foreach( $orderby_choices as $_orderby => $label ): ?>
				<option value="<?php echo esc_attr( $_orderby ) ?>" <?php selected( $orderby, $_orderby ) ?>>
					<?php echo esc_html( $label ); ?>
				</option>
				<?php endforeach; ?>
			</select>
		</p>
		<?php
	}

	public function update( $new_instance, $old_instance ) {

		$new_instance = wp_parse_args( $new_instance, array(
			'title' => '', 
			'posts_per_page' => 3, 
			'category__not_in' => array(), 
			'tag__not_in' => array(), 
			'meta_display' => 'date', 
			'order' => 'DESC', 
			'orderby' => 'post_date'
		));
		$valid_categories = wp_list_pluck( get_categories(), 'term_id' );
		$valid_tags       = wp_list_pluck( get_tags(), 'term_id' );

		$instance = array(
			'title'            => strip_tags( $new_instance['title'] ), 
			'posts_per_page'   => intval( strip_tags( $new_instance['posts_per_page'] ) ), 
			'category__not_in' => array_intersect( $valid_categories, (array) $new_instance['category__not_in'] ), 
			'tag__not_in'      => array_intersect( $valid_categories, (array) $new_instance['tag__not_in'] ), 
			'meta_display'     => $new_instance['meta_display'], 
			'order'            => $new_instance['order'], 
			'orderby'          => $new_instance['orderby']
		);

		return apply_filters( "youxi_widgets_{$this->id_base}_new_instance", $instance, $this->id );
	}
}