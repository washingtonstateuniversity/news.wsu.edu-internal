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
	$sections = array();

	$settings = array();

	foreach( get_categories() as $category ) {
		$slug = $category->category_nicename;

		$sections[ $slug ] = array(
			'name' => $category->cat_name,
			'enabled' => isset( $settings[ $slug ] ) ? $settings[ $slug ]['enabled'] : 1,
			'count' => isset( $settings[ $slug ] ) ? $settings[ $slug ]['count'] : 4,
			'classes' => isset( $settings[ $slug ] ) ? $settings[ $slug ]['classes'] : 'bottom-divider',
		);
	}

	$sections = wp_json_encode( $sections );

	return $sections;
}
