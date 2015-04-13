<?php get_header();

?><div class="content-area-wrap">

	<div class="content-area <?php echo esc_attr( helium_get_option( 'blog_archive_layout' ) ) ?>">

		<div class="content-header">

			<div class="content-header-affix"><?php

					$the_title = '';

					if( is_category() ):
						$strtr = array( '{category}' => single_cat_title( '', false ));
						$ot_prefix = 'blog_category';
					elseif( is_tag() ):
						$strtr = array( '{tag}' => single_tag_title( '', false ));
						$ot_prefix = 'blog_tag';
					elseif( is_author() ):
						$strtr = array( '{author}' => get_the_author() );
						$ot_prefix = 'blog_author';
					elseif( is_day() ):
						$strtr = array( '{date}' => get_the_date( __( 'F d, Y', 'helium' ) ) );
						$ot_prefix = 'blog_date';
					elseif( is_month() ):
						$strtr = array( '{date}' => get_the_date( __( 'F, Y', 'helium' ) ) );
						$ot_prefix = 'blog_date';
					elseif( is_year() ):
						$strtr = array( '{date}' => get_the_date( __( 'Y', 'helium' ) ) );
						$ot_prefix = 'blog_date';
					endif;

					if( isset( $strtr, $ot_prefix ) ):
						$the_title = strtr( helium_get_option( $ot_prefix . '_title' ), $strtr );
					else:
						$the_title = esc_html__( 'Archive', 'helium' );
					endif;

				?><h1 class="content-title">
					<?php echo $the_title; ?>
				</h1>

			</div>

		</div>

		<div class="content-wrap">

			<?php
			while( have_posts() ) : the_post();
				get_template_part( 'template-parts/post/default/entry', get_post_format() );
			endwhile;

			helium_posts_pagination(); ?>

		</div>

	</div>

</div>
<?php get_footer(); ?>