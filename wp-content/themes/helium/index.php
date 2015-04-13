<?php get_header();

?><div class="content-area-wrap">

	<div class="content-area <?php echo esc_attr( helium_get_option( 'blog_index_layout' ) ) ?>">

		<div class="content-header">

			<div class="content-header-affix"><?php

				?><h1 class="content-title">
					<?php echo helium_get_option( 'blog_index_title' ); ?>
				</h1>

			</div>

		</div>

		<div class="content-wrap">

			<?php
			while( have_posts() ) : the_post();
				get_template_part( 'template-parts/post/' . 'default' . '/entry', get_post_format() );
			endwhile;

			helium_posts_pagination(); ?>

		</div>

	</div>

</div>
<?php get_footer(); ?>