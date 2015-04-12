<?php get_header();

?><div class="content-area-wrap">

	<div class="content-area">

		<div class="content-header">

			<div class="content-header-affix clearfix"><?php

				?><h1 class="content-title">
					<?php printf( __( 'Search Results for &ldquo;%s&rdquo;', 'helium' ), get_search_query() ) ?>
				</h1>

			</div>

		</div>

		<div class="content-wrap">

			<div class="content-box clearfix">

				<div class="content-wrap-inner">

					<div class="container">

						<div class="row">

							<div class="col-lg-12">

								<h2 class="no-margin-top">
									<?php printf( __( 'Search Results for &ldquo;%s&rdquo;', 'helium' ), get_search_query() ) ?>
								</h2>

								<?php
								if( have_posts() ):

									while( have_posts() ) : the_post();
										get_template_part( 'template-parts/search-entry' );
									endwhile;
								else:
									_e( 'Sorry, but nothing matched your search criteria. Please try again with some different keywords.', 'helium' );
								endif; ?>

							</div>

						</div>

					</div>

				</div>

			</div>

			<?php helium_posts_pagination(); ?>

		</div>

	</div>

</div>
<?php get_footer(); ?>