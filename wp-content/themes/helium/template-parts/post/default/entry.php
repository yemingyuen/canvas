<article <?php post_class(); ?> itemscope itemtype="http://schema.org/Article">

	<div class="content-box">

		<?php get_template_part( 'template-parts/post/default/parts/header', get_post_format() ); ?>
		<?php get_template_part( 'template-parts/post/default/media/media', get_post_format() ); ?>

		<div class="post-entry">

			<div class="content-wrap-inner">

				<div class="container">

					<div class="row">

						<div class="col-lg-12">

							<section class="entry-content post-body" itemprop="articleBody"><?php

								if( ! is_single() && 'the_excerpt' === helium_get_option( 'blog_summary' ) ):
									the_excerpt();

								?><div class="more-link-wrap">
									<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" rel="bookmark" class="more-link"><?php _e( 'Continue Reading &rarr;', 'helium' ); ?></a>
								</div><?php

								else: the_content( __( 'Continue Reading &rarr;', 'hanabi' ) ); endif;

							?></section>

							<?php if( is_single() ):

								wp_link_pages(array(
									'before' => '<section class="posts-pages-nav"><nav class="pages-nav"><ul>', 
									'after' => '</ul></nav></section>', 
									'separator' => '', 
									'pagelink' => '<span class="pages-nav-item">%</span>'
								));

								if( helium_get_option( 'blog_show_tags' ) && get_the_tags() )  :

								?><section class="post-tags"><?php the_tags( '', '' ); ?></section>

								<?php endif;

								if( helium_get_option( 'blog_sharing' ) ):
									get_template_part( 'template-parts/post/default/parts/sharing', get_post_format() );
								endif;

								if( is_multi_author() || helium_get_option( 'blog_show_author' ) ):
									get_template_part( 'template-parts/post/default/parts/author', get_post_format() );
								endif;

								if( helium_get_option( 'blog_related_posts' ) ):
									get_template_part( 'template-parts/post/default/parts/related', get_post_format() );
								endif;

								if( have_comments() || comments_open() ):
									comments_template();
								endif;

							endif;
							?>

						</div>

					</div>

				</div>

			</div>

		</div>

	</div>

</article>
