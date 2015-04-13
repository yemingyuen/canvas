<?php

/* ==========================================================================
	Posts Pagination
============================================================================= */

if( ! function_exists( 'helium_posts_pagination' ) ):

function helium_posts_pagination( $pagination_type = 'numbered', $query = null ) {

	global $wp_query, $wp_rewrite;

	if( ! $query || ! is_a( $query, 'WP_Query' ) ) {
		$query = $wp_query;
	}

	// Don't print empty markup if there's only one page.
	if( $query->max_num_pages < 2 ) {
		return;
	}

	if( preg_match( '/^(infinite|ajax|numbered)$/', $pagination_type ) ):

		if( is_front_page() && ! is_home() ) {
			$current = get_query_var( 'page' ) ? intval( get_query_var( 'page' ) ) : 1;
		} else {
			$current = get_query_var( 'paged' ) ? intval( get_query_var( 'paged' ) ) : 1;
		}

		$pagenum_link = html_entity_decode( get_pagenum_link() );
		$query_args   = array();
		$url_parts    = explode( '?', $pagenum_link );

		if ( isset( $url_parts[1] ) ) {
			wp_parse_str( $url_parts[1], $query_args );
		}

		$pagenum_link = remove_query_arg( array_keys( $query_args ), $pagenum_link );
		$pagenum_link = trailingslashit( $pagenum_link ) . '%_%';

		$format  = $wp_rewrite->using_index_permalinks() && ! strpos( $pagenum_link, 'index.php' ) ? 'index.php/' : '';
		$format .= $wp_rewrite->using_permalinks() ? user_trailingslashit( $wp_rewrite->pagination_base . '/%#%', 'paged' ) : '?paged=%#%';

		// Set up paginated links.
		$links = paginate_links( array(
			'base' => $pagenum_link, 
			'format' => $format, 
			'total' => $query->max_num_pages, 
			'current' => $current, 
			'show_all' => preg_match( '/^(infinite|ajax)$/', $pagination_type ), 
			'mid_size' => 2, 
			'end_size' => 1, 
			'add_args' => array_map( 'urlencode', $query_args ),
			'type' => 'array', 
			'prev_text' => '<span class="content-nav-link-wrap"><i class="fa fa-chevron-left"></i></span>', 
			'next_text' => '<span class="content-nav-link-wrap"><i class="fa fa-chevron-right"></i></span>', 
			'before_page_number' => '<span class="content-nav-link-wrap">', 
			'after_page_number' => '</span>'
		));

		if( $links ):
			echo '<div class="content-box">';
				
				echo '<div class="content-wrap-inner no-padding">';

					echo '<nav class="content-nav" role="navigation" itemscope itemtype="http://schema.org/SiteNavigationElement">';

						echo '<ul>';

						foreach( $links as $link ):

							echo '<li class="content-nav-link">' . $link . '</li>';

						endforeach;

						echo '</ul>';

					echo '</nav>';

				echo '</div>';

			echo '</div>';
		endif;
	else:
		echo '<div class="content-box">';

			echo '<div class="content-wrap-inner no-padding">';

				echo '<nav class="content-nav" role="navigation" itemscope itemtype="http://schema.org/SiteNavigationElement">';

					echo '<ul>';

						$next_posts_link = get_next_posts_link(
							'<span class="content-nav-link-wrap">' . 
								'<span class="fa fa-chevron-left"></span>' .
								'<span class="content-nav-link-label">' . __( 'Older Posts', 'helium' ) . '</span>' . 
							'</span>', $wp_query->max_num_pages
						);
						if( $next_posts_link ) {
							echo '<li class="content-nav-link">' . $next_posts_link . '</li>';
						}

						$prev_posts_link = get_previous_posts_link(
							'<span class="content-nav-link-wrap">' . 
								'<span class="content-nav-link-label">' . __( 'Newer Posts', 'helium' ) . '</span>' . 
								'<span class="fa fa-chevron-right"></span>' . 
							'</span>', $wp_query->max_num_pages
						);
						if( $prev_posts_link ) {
							echo '<li class="content-nav-link">' . $prev_posts_link . '</li>';
						}

					echo '</ul>';

				echo '</nav>';

			echo '</div>';

		echo '</div>';
	endif;
}
endif;

/* ==========================================================================
	Related Posts
============================================================================= */

if( ! function_exists( 'helium_related_posts' ) ):

function helium_related_posts( $limit = 4, $post_id = null ) {

	$post = get_post( $post_id );
	$posts = array();

	if( is_a( $post, 'WP_Post' ) ) {

		$tax_query = array(
			'relation' => 'OR'
		);

		foreach( get_object_taxonomies( $post->post_type ) as $taxonomy ) {
			if( 'post_format' != $taxonomy && $terms = get_the_terms( $post->ID, $taxonomy ) ) {
				$tax_query[] = array(
					'taxonomy' => $taxonomy, 
					'field' => 'id', 
					'terms' => array_values( wp_list_pluck( $terms, 'term_id' ) )
				);
			}
		}

		$posts = get_posts(array(
			'post_type' => $post->post_type, 
			'tax_query' => $tax_query, 
			'posts_per_page' => $limit, 
			'post__not_in' => array( $post->ID ), 
			'orderby' => 'RAND', 
			'suppress_filters' => false
		));
	}

	return $posts;
}
endif;

/* ==========================================================================
	Excerpt Length
============================================================================= */

if( ! function_exists( 'helium_excerpt_length' ) ):

function helium_excerpt_length( $len ) {
	if( is_search() ) {
		return $len;
	}
	return helium_get_option( 'blog_excerpt_length' );
}
endif;
add_filter( 'excerpt_length', 'helium_excerpt_length' );

/* ==========================================================================
	`the_content_more_link`
============================================================================= */

if( ! function_exists( 'helium_the_content_more_link' ) ):

function helium_the_content_more_link( $more_link ) {
	return '<div class="more-link-wrap">' . $more_link . '</div>';
}
endif;
add_filter( 'the_content_more_link', 'helium_the_content_more_link' );

/* ==========================================================================
	http://schema.org integration for `comments_popup_link`
============================================================================= */

if( ! function_exists( 'helium_comments_popup_link_attributes' ) ):

function helium_comments_popup_link_attributes( $attributes ) {
	return 'itemprop="discussionUrl"';
}
endif;
add_filter( 'comments_popup_link_attributes', 'helium_comments_popup_link_attributes' );

/* ==========================================================================
	Post Format
============================================================================= */

if( ! function_exists( 'helium_extract_post_format_meta' ) ):

function helium_extract_post_format_meta( $post = null ) {

	$post = get_post( $post );
	if( is_a( $post, 'WP_Post' ) && function_exists( 'youxi_post_format_id' ) ) {

		$post_format = get_post_format( $post->ID );
		$meta_key    = youxi_post_format_id( $post_format );
		$post_meta   = (array) $post->$meta_key;

		switch( $post_format ) {
			case 'video':
				$post_meta = wp_parse_args( $post_meta, array(
					'type' => '', 
					'embed' => '', 
					'src' => '', 
					'poster' => ''
				));
				if( ( 'embed' == $post_meta['type'] && '' !== $post_meta['embed'] ) || 
					( 'hosted' == $post_meta['type'] && '' !== $post_meta['src'] ) ) {
					return $post_meta;
				}
				break;
			case 'audio':
				$post_meta = wp_parse_args( $post_meta, array(
					'type' => '', 
					'embed' => '', 
					'src' => ''
				));
				if( ( 'embed' == $post_meta['type'] && '' !== $post_meta['embed'] ) || 
					( 'hosted' == $post_meta['type'] && '' !== $post_meta['src'] ) ) {
					return $post_meta;
				}
				break;
			case 'gallery':
				$post_meta = wp_parse_args( $post_meta, array( 'images' => array(), 'type' => 'slider' ) );
				if( ! empty( $post_meta['images'] ) && is_array( $post_meta['images'] ) ) {
					return $post_meta;
				}
				break;
			default:
				break;
		}

	}
}
endif;
