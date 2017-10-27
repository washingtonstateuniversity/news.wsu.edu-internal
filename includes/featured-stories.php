<?php

namespace WSU\News\Internal\Featured_Stories;

add_action( 'pre_get_posts', 'WSU\News\Internal\Featured_Stories\filter_query_for_featured_posts', 10 );
add_action( 'add_meta_boxes', 'WSU\News\Internal\Featured_Stories\add_meta_boxes', 10 );
add_action( 'save_post', 'WSU\News\Internal\Featured_Stories\save_post', 10, 2 );
add_filter( 'manage_post_posts_columns', 'WSU\News\Internal\Featured_Stories\manage_posts_columns', 10, 1 );
add_action( 'manage_post_posts_custom_column', 'WSU\News\Internal\Featured_Stories\manage_posts_custom_column', 10, 2 );

/**
 * @param \WP_Query $query
 */
function filter_query_for_featured_posts( $query ) {
	if ( ! $query->is_main_query() || ! $query->is_category() ) {
		return;
	}

	// The most recent featured story displays on the top of every paginated
	// category archive page.
	$query->set( 'posts_per_page', 1 );
	$query->set( 'meta_query', array(
		array(
			'key' => '_news_internal_featured',
			'value' => 'yes',
		),
	) );
}

/**
 * Adds meta boxes used to manage featured stories.
 *
 * @param string $post_type
 */
function add_meta_boxes( $post_type ) {
	if ( 'post' !== $post_type ) {
		return;
	}

	add_meta_box( 'news_internal_featured_meta', 'Featured Status', 'WSU\News\Internal\Featured_Stories\display_meta_box', 'post', 'side', 'high' );
}

/**
 * Displays the meta box used to capture a post's featured status.
 *
 * @param \WP_Post $post
 */
function display_meta_box( $post ) {
	$featured = get_post_meta( $post->ID, '_news_internal_featured', true );

	if ( 'yes' !== $featured ) {
		$featured = 'no';
	}

	?>
	<label for="featured-status-select">Featured Status:</label>
	<select id="featured-status-select" name="featured_status_select">
		<option value="no" <?php selected( 'no', $featured ); ?>>No</option>
		<option value="yes" <?php selected( 'yes', $featured ); ?>>Yes</option>
	</select>
	<?php
}

/**
 * Saves the featured status of a post.
 *
 * @param int     $post_id
 * @param \WP_Post $post
 */
function save_post( $post_id, $post ) {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( 'auto-draft' === $post->post_status ) {
		return;
	}

	if ( ! isset( $_POST['featured_status_select'] ) ) { // @codingStandardsIgnoreLine
		return;
	}

	if ( 'yes' === $_POST['featured_status_select'] ) { // @codingStandardsIgnoreLine
		update_post_meta( $post_id, '_news_internal_featured', 'yes' );
	} else {
		delete_post_meta( $post_id, '_news_internal_featured' );
	}
}

/**
 * Add a custom column to the posts list table for featured items.
 *
 * This also removes the comments column.
 *
 * @since 0.5.1
 *
 * @param array $post_columns
 *
 * @return array
 */
function manage_posts_columns( $post_columns ) {
	unset( $post_columns['comments'] );
	unset( $post_columns['date'] );
	unset( $post_columns['wsu_last_updated'] );

	$post_columns['item_featured'] = 'Featured Post';
	$post_columns['date'] = 'Date';
	$post_columns['wsu_last_updated'] = 'Last Updated';

	return $post_columns;
}

/**
 * Output a post's featured status in a custom list table column.
 *
 * @since 0.5.1
 *
 * @param string $column_name
 * @param int    $post_id
 */
function manage_posts_custom_column( $column_name, $post_id ) {
	if ( 'item_featured' === $column_name ) {
		$featured = get_post_meta( $post_id, '_news_internal_featured', true );

		if ( 'yes' !== $featured ) {
			$featured = 'no';
		}

		echo esc_html( ucwords( $featured ) );
	}
}
