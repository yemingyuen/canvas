<?php if( ( helium_get_option( 'blog_show_tags' ) && get_the_tags() ) 
	|| ( helium_get_option( 'blog_show_categories' ) && get_the_category() ) ):

?><div class="post-taxonomies"><?php

	if( helium_get_option( 'blog_show_tags' ) && get_the_tags() ):

	?><div class="post-tags">
		<?php the_tags( __( 'Tagged: ', 'hanabi' ) ); ?>
	</div><?php

	endif;

	if( helium_get_option( 'blog_show_categories' ) && get_the_category() ):

	?><div class="post-categories">
		<?php _e( 'Categories: ', 'hanabi' ); the_category( ', ' ); ?>
	</div><?php 

	endif; ?>

</div>
<?php endif; ?>