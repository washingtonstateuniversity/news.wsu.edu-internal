<?php

namespace WSU\News\Internal\Page_Curation;

/**
 * Retrieve the available sections and the settings assigned to each
 * for curation on the page.
 *
 * @since 0.5.0
 *
 * @return array
 */
function get_sections() {
	$settings = get_option( 'page_curation' );

	if ( ! empty( $settings ) ) {
		$settings = json_decode( $settings, true );
	} else {
		$settings = array();
	}

	foreach ( get_categories() as $category ) {
		if ( isset( $settings[ $category->slug ] ) ) {
			$settings[ $category->slug ]['name'] = $category->cat_name;
		} else {
			$settings[ $category->slug ] = array(
				'name' => $category->cat_name,
			);
		}
	}

	// Remove old categories that have been deleted.
	foreach ( $settings as $slug => $data ) {
		if ( ! isset( $data['name'] ) ) {
			unset( $settings[ $slug ] );
		}
	}

	foreach ( $settings as $slug => $setting ) {
		$settings[ $slug ]['count'] = isset( $settings[ $slug ]['count'] ) ? $settings[ $slug ]['count'] : 4;
		$settings[ $slug ]['classes'] = isset( $settings[ $slug ]['classes'] ) ? $settings[ $slug ]['classes'] : 'bottom-divider';
	}

	return $settings;
}

/**
 * Retrieve the current Good to Know posts displayed on the front page.
 *
 * @since 0.6.0
 *
 * @return array|\WP_Query
 */
function get_gtk_posts( $output = 'ids' ) {
	$args = array(
		'post_type' => 'post',
		'post_status' => 'publish',
		'posts_per_page' => 5,
	);

	$gtk_post_ids = get_option( 'gtk_posts', false );

	$gtk_post_ids = explode( ',', $gtk_post_ids );
	$args['post__in'] = $gtk_post_ids;
	$args['orderby'] = 'post__in';

	if ( 'ids' === $output ) {
		$args['fields'] = 'ids';
	}

	$gtk_query = new \WP_Query( $args );

	if ( 'ids' === $output ) {
		wp_reset_postdata();
		return $gtk_query;
	}

	return $gtk_query;
}

/**
 * Retrieve the current featured posts displayed on the front page.
 *
 * @since 0.6.0
 *
 * @return array|\WP_Query
 */
function get_featured_posts( $output = 'ids' ) {
	$args = array(
		'post_type' => 'post',
		'post_status' => 'publish',
		'posts_per_page' => 4,
	);

	$featured_post_ids = get_option( 'featured_posts', false );

	if ( false === $featured_post_ids || is_object( $featured_post_ids ) ) {
		$args['meta_query'] = array(
			array(
				'key' => '_news_internal_featured',
				'value' => 'yes',
			),
		);
	} else {
		$featured_post_ids = explode( ',', $featured_post_ids );
		$args['post__in'] = $featured_post_ids;
		$args['orderby'] = 'post__in';
	}

	if ( 'ids' === $output ) {
		$args['fields'] = 'ids';
	}

	$featured_query = new \WP_Query( $args );

	if ( 'ids' === $output ) {
		wp_reset_postdata();
		return $featured_query;
	}

	return $featured_query;
}
