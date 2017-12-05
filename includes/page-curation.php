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
