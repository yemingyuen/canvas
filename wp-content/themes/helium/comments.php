<?php
/*
 * If the current post is protected by a password and the visitor has not yet
 * entered the password we will return early without loading the comments.
 */
if( post_password_required() ) {
	return;
}

if( have_comments() ):

?><section id="comments" class="post-comments">

	<h4 class="post-section-title">
		<?php if( get_comments_number() > 0 ):
			printf( _n( 'One comment on &ldquo;%2$s&rdquo;', '%1$s comments on &ldquo;%2$s&rdquo;', get_comments_number(), 'helium' ),
				number_format_i18n( get_comments_number() ), get_the_title() );
		else:
			printf( __( 'No comment on &ldquo;%2$s&rdquo;', 'helium' ), get_the_title() );
		endif;
		?>
	</h4>

	<ul class="comment-list">
		<?php wp_list_comments( array( 'callback' => 'helium_comment' )); ?>
	</ul>

	<?php if( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : 
	?><nav class="post-comments-nav">
		<div class="nav-previous"><?php previous_comments_link( __( '&larr; Older Comments', 'helium' ) ); ?></div>
		<div class="nav-next"><?php next_comments_link( __( 'Newer Comments &rarr;', 'helium' ) ); ?></div>
	</nav>
	<?php endif; ?>

	<?php if( ! comments_open() ):
	?><div class="alert alert-warning">
		<?php _e( 'Comments are closed for this post.', 'helium' ); ?>
	</div>
	<?php endif; ?>

</section>
<?php endif;

?><section class="post-comments-form">
	<?php comment_form(); ?>
</section>