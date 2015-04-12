<?php
$related_posts_count = helium_get_option( 'blog_related_posts_count' );
$related_posts = helium_related_posts( $related_posts_count );

if( ! empty( $related_posts ) ):

?><section class="post-related-posts">

	<h4 class="post-section-title"><?php _e( 'Related Posts', 'helium' ); ?></h4>

	<div class="related-items row">

		<?php

		// Backup current post
		global $post;
		$the_post = $post;

		$related_posts = array_slice( $related_posts, 0, $related_posts_count );
		$col_width = min( 12, ( 12 / max( $related_posts_count, count( $related_posts ) ) ) );
		$use_lightbox = ( 'lightbox' === helium_get_option( 'blog_related_posts_behavior' ) );

		foreach( $related_posts as $post ): setup_postdata( $post );

		?><article <?php post_class( array( 'related-item', 'col-md-' . $col_width ) ); ?>>
			<?php if( has_post_thumbnail() ): 

				if( $use_lightbox ):
			?><figure class="related-item-media">
				<a href="<?php echo esc_url( wp_get_attachment_url( get_post_thumbnail_id() ) ) ?>" title="<?php the_title_attribute(); ?>" class="mfp-image">
					<?php the_post_thumbnail( 'helium_16by9' ); ?>
					<span class="overlay"></span>
				</a>
			</figure><?php
				else: 
			?><figure class="related-item-media">
				<a href="<?php echo esc_url( get_permalink() ) ?>" title="<?php the_title_attribute(); ?>">
					<?php the_post_thumbnail( 'helium_16by9' ); ?>
				</a>
			</figure>
			<?php
				endif;

			endif;

			the_title( '<h5 class="related-item-title"><a href="' . get_permalink() . '" rel="bookmark">', '</a></h5>' );

			?><p class="related-item-meta">
				<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
					<?php echo esc_html( get_the_date( helium_get_option( 'blog_date_format' ) ) ); ?>
				</time>
			</p>
			<div class="spacer-30 hidden-md hidden-lg"></div>
		</article>
		<?php endforeach;

		// Restore post
		$post = $the_post;
		if( is_a( $post, 'WP_Post' ) ) {
			setup_postdata( $post );
		}

		?>

	</div>

</section>
<?php endif; ?>