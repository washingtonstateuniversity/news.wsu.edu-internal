<?php

namespace WSU\News\Internal\Content_Syndicate;

add_action( 'rest_api_init', 'WSU\News\Internal\Content_Syndicate\register_api_fields' );
add_filter( 'wsu_content_syndicate_host_data', 'WSU\News\Internal\Content_Syndicate\manage_subset_data', 10, 2 );
add_filter( 'wsuwp_content_syndicate_json_output', 'WSU\News\Internal\Content_Syndicate\wsuwp_json_output', 10, 3 );
add_filter( 'wsuwp_content_syndicate_default_atts', 'WSU\News\Internal\Content_Syndicate\append_default_attributes' );
add_filter( 'wsuwp_content_syndicate_taxonomy_filters', 'WSU\News\Internal\Content_Syndicate\modify_rest_url', 10, 2 );
add_action( 'rest_query_vars', 'WSU\News\Internal\Content_Syndicate\rest_query_vars' );
add_filter( 'query_vars', 'WSU\News\Internal\Content_Syndicate\query_vars' );
add_filter( 'rest_post_query', 'WSU\News\Internal\Content_Syndicate\rest_post_query', 12 );

/**
 * Register a syndicate_categories field in the REST API to provide specific
 * data on categories that should appear with posts pulled in content syndicate.
 */
function register_api_fields() {
	register_rest_field( 'post', 'syndicate_categories', array(
		'get_callback' => 'WSU\News\Internal\Content_Syndicate\get_api_syndicate_categories',
	) );
}

/**
 * Return the category data required by content syndicate.
 *
 * @param array            $object     The current post being processed.
 * @param string           $field_name Name of the field being retrieved.
 * @param \WP_Rest_Request $request    The full current REST request.
 *
 * @return mixed Category data associated with the post.
 */
function get_api_syndicate_categories( $object, $field_name, $request ) {
	if ( 'syndicate_categories' !== $field_name ) {
		return null;
	}

	$categories = wp_get_post_categories( $object['id'] );
	$data = array();

	foreach ( $categories as $category ) {
		$term = get_term( $category );
		$data[] = array(
			'id' => $term->term_id,
			'slug' => $term->slug,
			'name' => $term->name,
			'url' => get_category_link( $term->term_id ),
		);
	}

	return $data;
}

/**
 * Ensure the subset data in content syndicate has been populated
 * with category information from the REST API.
 *
 * @param $subset
 * @param $post
 *
 * @return mixed
 */
function manage_subset_data( $subset, $post ) {
	if ( isset( $post->syndicate_categories ) ) {
		$subset->categories = $post->syndicate_categories;
	} else {
		$subset->categories = array();
	}

	return $subset;
}

/**
 * Provide fallback URLs if thumbnail sizes have not been generated
 * for a post pulled in with content syndicate.
 *
 * @param \stdClass $content
 * @param array     $atts    Array of attributes passed to the shortcode.
 *
 * @return string
 */
function get_image_url( $content, $atts = array() ) {
	// If no embedded featured media exists, use the full thumbnail.
	if ( ! isset( $content->featured_media )
		|| ! isset( $content->featured_media->media_details )
		|| ! isset( $content->featured_media->media_details->sizes ) ) {
		return $content->thumbnail;
	}

	$sizes = $content->featured_media->media_details->sizes;

	// Use a larger featured image for featured stories.
	if ( isset( $atts['featured'] ) && 'yes' === $atts['featured'] ) {
		if ( isset( $sizes->{'spine-large_size'} ) ) {
			return $sizes->{'spine-large_size'}->source_url;
		}

		if ( isset( $sizes->{'large'} ) ) {
			return $sizes->{'large'}->source_url;
		}

		if ( isset( $sizes->{'medium_large'} ) ) {
			return $sizes->{'medium_large'}->source_url;
		}
	}

	if ( isset( $sizes->{'spine-small_size'} ) ) {
		return $sizes->{'spine-small_size'}->source_url;
	}

	if ( isset( $sizes->{'medium_large'} ) ) {
		return $sizes->{'medium_large'}->source_url;
	}

	if ( isset( $sizes->{'large'} ) ) {
		return $sizes->{'large'}->source_url;
	}

	return $content->thumbnail;
}

/**
 * Provide custom output for the wsuwp_json shortcode.
 *
 * @param string $content
 * @param array  $data
 * @param array  $atts
 *
 * @return string
 */
function wsuwp_json_output( $content, $data, $atts ) {
	if ( isset( $atts['featured'] ) && 'yes' === $atts['featured'] ) {
		$deck_class = 'deck--featured';
	} else {
		$deck_class = '';
	}

	// Provide a default output for when no `output` attribute is included.
	if ( 'json' === $atts['output'] ) {
		ob_start();
		?>
		<div class="deck <?php echo esc_attr( $deck_class ); ?>">
			<?php
			$offset_x = 0;
			foreach ( $data as $content ) {
				if ( $offset_x < absint( $atts['offset'] ) ) {
					$offset_x++;
					continue;
				}

				?>
				<article class="card card--news">
					<?php if ( ! is_front_page() ) { ?>
					<span class="card-categories">
						<?php
						$category_output = array();
						foreach ( $content->categories as $category ) {
							$category_output[] = esc_html( $category->name );
						}

						$category_output = implode( ', ', $category_output );
						echo $category_output; // @codingStandardsIgnoreLine
						?>
					</span>
					<?php } ?>
					<?php if ( ! empty( $content->thumbnail ) ) : ?>
						<?php $image_url = get_image_url( $content, $atts ); ?>
					<figure class="card-image">
						<a href="<?php echo esc_url( $content->link ); ?>"><img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $content->featured_media->alt_text ); ?>"></a>
					</figure>
					<?php endif; ?>
					<header class="card-title">
						<a href="<?php echo esc_url( $content->link ); ?>"><?php echo esc_html( $content->title ); ?></a>
					</header>
					<div class="card-date"><?php echo esc_html( date( $atts['date_format'], strtotime( $content->date ) ) ); ?></div>
					<div class="card-excerpt">
						<?php echo wp_kses_post( $content->excerpt ); ?>
					</div>
				</article>
				<?php
			}
			?>
		</div>
		<?php
		$content = ob_get_contents();
		ob_end_clean();
	}

	// Handle the output of content syndicate items from WSU News.
	if ( 'wsu-news' === $atts['output'] ) {
		ob_start();

		foreach ( $data as $content ) {
			$excerpt = $content->excerpt;

			// Most posts have an image, a strong tag, and possibly some other inline
			// styles at the top. Make everything a paragraph.
			$excerpt = strip_tags( $excerpt, '<p>' );

			// Almost all posts start with things like "PULLMAN, WA.  – " and other
			// pre-article content that we don't want to display. We can strip this
			// off reliably enough and deal with other instances on a case by case basis.
			$excerpt = str_replace( '&#8211;', ' – ', $excerpt );
			$excerpt = explode( ' – ', $excerpt );
			if ( 1 < count( $excerpt ) ) {
				array_shift( $excerpt );
			}

			// Just in case the ' – ' was used elsewhere in the excerpt, we don't want
			// everything to break.
			$excerpt = implode( ' – ', $excerpt );

			// Rebuild the paragraph that we broke.
			$excerpt = '<p>' . $excerpt;
			?>
			<article class="card card--news">
				<header class="card-title">
					<a href="<?php echo esc_url( $content->link ); ?>"><?php echo esc_html( $content->title ); ?></a>
				</header>
				<div class="card-date"><?php echo esc_html( date( $atts['date_format'], strtotime( $content->date ) ) ); ?></div>
				<div class="card-excerpt">
					<?php echo wp_kses_post( $excerpt ); ?>
				</div>
			</article>
			<?php
		}

		$content = ob_get_contents();
		ob_end_clean();
	}

	// Handle the output of content syndicate items from WSU Events.
	if ( 'wsu-events' === $atts['output'] ) {
		ob_start();

		foreach ( $data as $content ) {
			?>
			<article class="card card--event">
				<div class="card-date"><?php echo esc_html( date( $atts['date_format'], strtotime( $content->start_date ) ) ); ?>
					<span class="card-time"><?php echo esc_html( date( 'g:i a', strtotime( $content->start_date ) ) ); ?> - <?php echo esc_html( date( 'g:i a', strtotime( $content->end_date ) ) ); ?></span>
				</div>
				<header class="card-title">
					<a href="<?php echo esc_url( $content->link ); ?>"><?php echo esc_html( $content->title ); ?></a>
				</header>
			</article>
			<?php
		}

		$content = ob_get_contents();
		ob_end_clean();
	}

	return $content;
}

/**
 * Add support for a "featured" flag.
 *
 * @param array $atts WSUWP Content Syndicate shortcode attributes.
 *
 * @return array Modified list of default shortcode attributes.
 */
function append_default_attributes( $atts ) {
	$atts['featured'] = '';

	return $atts;
}

/**
 * Include the featured flag as part of the REST API request.
 *
 * @param string $request_url
 * @param array $atts
 *
 * @return string
 */
function modify_rest_url( $request_url, $atts ) {
	if ( ! in_array( $atts['featured'], array( 'yes', 'no' ), true ) ) {
		return $request_url;
	}

	$request_url = add_query_arg( array(
		'filter[featured]' => $atts['featured'],
	), $request_url );

	return $request_url;
}

/**
 * Make the `meta_query` argument available to the REST API request.
 *
 * @param array $vars
 *
 * @return array
 */
function rest_query_vars( $vars ) {
	array_push( $vars, 'meta_query' );

	return $vars;
}

/**
 * Filter the query vars that the plugin expects to be available through the
 * the `filter` query argument attached to REST request URLs.
 *
 * @param array $vars
 *
 * @return array
 */
function query_vars( $vars ) {
	array_push( $vars, 'featured' );

	return $vars;
}

/**
 * Build a meta query from the featured flag passed via filter parameters
 * in the REST API request.
 *
 * @param array $args
 *
 * @return array
 */
function rest_post_query( $args ) {
	if ( ! isset( $args['featured'] ) || ! in_array( $args['featured'], array( 'yes', 'no' ), true ) ) {
		return $args;
	}

	if ( 'yes' === $args['featured'] ) {
		$args['meta_query'] = array(
			array(
				'key' => '_news_internal_featured',
				'value' => $args['featured'],
			),
		);
	} elseif ( 'no' === $args['featured'] ) {
		$args['meta_query'] = array(
			array(
				'key' => '_news_internal_featured',
				'compare' => 'NOT EXISTS',
			),
		);
	}

	return $args;
}
