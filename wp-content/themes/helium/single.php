<?php get_header(); ?>

<div class="content-area-wrap">

	<?php if( have_posts() ): the_post();
		get_template_part( 'template-parts/' . get_post_type() . '/entry' );
	endif; ?>

</div>

<?php get_footer(); ?>