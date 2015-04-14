<?php

get_header();

if( have_posts() ): the_post();

	$layout = wp_parse_args( $post->layout, array(
	'page_layout'  => 'boxed', 
	'wrap_content' => true
));

?><div class="content-area-wrap">

	<article <?php post_class( array( 'content-area', $layout['page_layout'] ) ); ?>>

		<header class="content-header">

			<div class="content-header-affix">
			
				<div class="breadcrumbs" xmlns:v="http://rdf.data-vocabulary.org/#">
					<?php if(function_exists('bcn_display')) {
						bcn_display();
    				}?>
				</div>
			</div>

		</header>

		<div class="content-wrap">

			<div class="content-box">

				<?php if( has_post_thumbnail() ):

				?><div class="featured-content">
					<figure class="featured-image">
						<?php the_post_thumbnail( 'full' ); ?>
					</figure>
				</div>
				<?php endif;

				if( $layout['wrap_content'] ):

				?><div class="content-wrap-inner">

					<div class="container">

						<div class="row">

							<div class="col-lg-12">
							<?php endif; ?>
							<div class="entry-title-page">
								<?php the_title('<h3 class="bordered no-margin">', '</h3>'); ?>
							</div>
								<div class="entry-content">
									<?php the_content(); ?>
								</div>

								<?php wp_link_pages(array(
									'before' => '<nav class="pages-nav"><ul>', 
									'after' => '</ul></nav>', 
									'separator' => '', 
									'pagelink' => '<span class="pages-nav-item">%</span>'
								));

							if( $layout['wrap_content'] ):

							?></div>

						</div>

					</div>

				</div>
				<?php endif; ?>

			</div>

		</div>

	</article>
</div>
<?php
endif;

get_footer(); ?>