<?php
$related_items_count = helium_get_option( 'portfolio_related_items_count' );
$related_posts = helium_related_posts( $related_items_count );

if( ! empty( $related_posts ) ):

?><div class="content-box clearfix">

	<div class="content-wrap-inner three-quarters-vertical-padding">

		<div class="container">

			<div class="row">
				
				<div class="col-lg-12">
					<h4 class="bordered no-margin-top"><?php _e( 'Related Items', 'helium' ); ?></h4>
				</div>

			</div>

			<div class="related-items row">
				<?php

				// Backup current post
				global $post;
				$the_post = $post;

				$related_posts = array_slice( $related_posts, 0, $related_items_count );
				$col_width = min( 12, ( 12 / max( $related_items_count, count( $related_posts ) ) ) );
				$use_lightbox = ( 'lightbox' === helium_get_option( 'portfolio_related_items_behavior' ) );

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
						<?php echo get_the_term_list( get_the_ID(), youxi_portfolio_tax_name(), '', ', ' ); ?>
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

		</div>

	</div>

</div>
<?php endif; ?>