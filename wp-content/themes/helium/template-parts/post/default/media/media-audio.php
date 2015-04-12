<?php

if( $post_format_meta = helium_extract_post_format_meta() ):

?><section class="post-media post-media-audio-<?php echo esc_attr( $post_format_meta['type'] ) ?>">

<?php if( has_post_thumbnail() ):

?><figure class="post-featured-image">
	<?php the_post_thumbnail( 'full' ); ?>
</figure>
<?php endif;

switch( $post_format_meta['type'] ):
	case 'embed':
		global $wp_embed;
		if( is_a( $wp_embed, 'WP_Embed' ) ):
			echo $wp_embed->autoembed( $post_format_meta['embed'] );
		else:
			echo $post_format_meta['embed'];
		endif;
		break;
	case 'hosted':
		// Check if the attachment is an audio
		if( 0 === strpos( get_post_mime_type( $post_format_meta['src'] ), 'audio/' ) ):
			echo wp_audio_shortcode(array(
				'src' => wp_get_attachment_url( $post_format_meta['src'] )
			));
		endif;
		break;
endswitch;

?></section>
<?php else:
	get_template_part( 'template-parts/post/media/media' );
endif; ?>