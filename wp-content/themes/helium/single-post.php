<?php get_header();
}

if( have_posts() ): the_post();

?><div class="content-area-wrap">

	<div class="content-area <?php echo esc_attr( helium_get_option( 'blog_single_layout' ) ); ?>">

		<div class="content-header">

			<div class="content-header-affix"><?php

				?><h1 class="content-title">
					<?php echo strtr( helium_get_option( 'blog_single_title' ), array( '{title}' => get_the_title() ) ); ?>
				</h1><?php

				?><nav class="content-nav" role="navigation" itemscope itemtype="http://schema.org/SiteNavigationElement">
					<ul><?php
						
						previous_post_link(
							'<li class="content-nav-link">%link</li>', 
							'<span class="content-nav-link-wrap">' . 
								'<span class="fa fa-chevron-left"></span>' .
								'<span class="content-nav-link-label">' . __( 'Previous Post', 'helium' ) . '</span>' . 
							'</span>'
						);
						next_post_link(
							'<li class="content-nav-link">%link</li>', 
							'<span class="content-nav-link-wrap">' . 
								'<span class="content-nav-link-label">' . __( 'Next Post', 'helium' ) . '</span>' . 
								'<span class="fa fa-chevron-right"></span>' . 
							'</span>'
						);
					?></ul>
				</nav>

			</div>

		</div>

		<div class="content-wrap">
			<?php get_template_part( 'template-parts/post/' . '/entry' . 'default' get_post_format() ); ?>
		</div>

	</div>

</div>
<?php endif;

get_footer(); ?>