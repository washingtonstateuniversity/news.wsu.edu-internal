<?php

require_once __DIR__ . '/includes/theme-images.php';
require_once __DIR__ . '/includes/content-syndicate.php';
require_once __DIR__ . '/includes/page-curation.php';
require_once __DIR__ . '/includes/page-curation-customizer.php';
require_once __DIR__ . '/includes/featured-stories.php';
require_once __DIR__ . '/includes/announcements.php';
require_once __DIR__ . '/includes/mailchimp.php';

add_filter( 'spine_child_theme_version', 'internal_news_theme_version' );
/**
 * Provides a theme version for use in cache busting.
 *
 * @since 0.0.1
 *
 * @return string
 */
function internal_news_theme_version() {
	return '0.8.1';
}

add_action( 'wp_enqueue_scripts', 'internal_news_enqueue_scripts' );
/**
 * Enqueues custom styles.
 *
 * @since 0.0.1
 */
function internal_news_enqueue_scripts() {
	wp_enqueue_style( 'source_sans_pro', '//fonts.googleapis.com/css?family=Source+Sans+Pro:300,300i,400,400i,900,900i' );

}

add_action( 'wp_footer', 'internal_news_social_media_icons' );
/**
 * Provides social media sharing icons.
 *
 * @since 0.0.1
 */
function internal_news_social_media_icons() {
	if ( ! is_single() ) {
		return;
	}
	?>
	<svg class="social-media-icons" xmlns="http://www.w3.org/2000/svg">
		<symbol id="social-media-icon_linkedin" viewbox="0 0 20 20">
			<path d="M20 20h-4v-6.999c0-1.92-.847-2.991-2.366-2.991-1.653 0-2.634 1.116-2.634 2.991V20H7V7h4v1.462s1.255-2.202 4.083-2.202C17.912 6.26 20 7.986 20 11.558V20zM2.442 4.921A2.451 2.451 0 0 1 0 2.46 2.451 2.451 0 0 1 2.442 0a2.451 2.451 0 0 1 2.441 2.46 2.45 2.45 0 0 1-2.441 2.461zM0 20h5V7H0v13z"></path>
		</symbol>
		<symbol id="social-media-icon_twitter" viewbox="0 0 20 16">
			<path d="M6.29 16c7.547 0 11.675-6.156 11.675-11.495 0-.175 0-.35-.012-.522A8.265 8.265 0 0 0 20 1.89a8.273 8.273 0 0 1-2.356.637A4.07 4.07 0 0 0 19.448.293a8.303 8.303 0 0 1-2.606.98 4.153 4.153 0 0 0-5.806-.175 4.006 4.006 0 0 0-1.187 3.86A11.717 11.717 0 0 1 1.392.738 4.005 4.005 0 0 0 2.663 6.13 4.122 4.122 0 0 1 .8 5.625v.051C.801 7.6 2.178 9.255 4.092 9.636a4.144 4.144 0 0 1-1.852.069c.537 1.646 2.078 2.773 3.833 2.806A8.315 8.315 0 0 1 0 14.185a11.754 11.754 0 0 0 6.29 1.812"></path>
		</symbol>
		<symbol id="social-media-icon_facebook" viewbox="0 0 10 20">
			<path d="M6.821 20v-9h2.733L10 7H6.821V5.052C6.821 4.022 6.848 3 8.287 3h1.458V.14c0-.043-1.253-.14-2.52-.14C4.58 0 2.924 1.657 2.924 4.7V7H0v4h2.923v9h3.898z"></path>
		</symbol>
	</svg>
	<?php
}

add_action( 'wsu_register_inline_svg', 'internal_news_register_masthead_svg' );
/**
 * Register the masthead SVG for the WSU Inline SVG plugin.
 *
 * @since 0.1.4
 */
function internal_news_register_masthead_svg() {
	ob_start();
	?>
	<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 669.2 68.7">
		<title>WSU Insider Beta</title>
		<g fill="#a61d2f">
			<path d="M1.4 2.5H17l3.2 25.2c0.6 6 1.3 12.1 2 18.1h0.4c1.1-6 2.1-12.1 3.2-18.1l5.5-25.2h12.8l5.5 25.2c1.1 5.8 2.1 12 3.2 18.1H53c0.6-6.1 1.3-12.2 1.9-18.1l3.2-25.2h14.5l-9.6 57.6H43.7l-4.4-22.9c-0.9-4.4-1.6-9.2-2.1-13.5h-0.4c-0.6 4.3-1.2 9-2.1 13.5l-4.3 22.9h-19L1.4 2.5z"/>
			<path d="M71.9 53l8.7-10.5c4.1 3.3 9.2 5.6 13.5 5.6 4.6 0 6.6-1.5 6.6-4.1 0-2.7-2.9-3.6-7.8-5.6l-7.2-3c-6.2-2.5-11.6-7.7-11.6-16 0-9.8 8.9-17.9 21.4-17.9 6.6 0 13.8 2.5 19.1 7.7l-7.6 9.6c-3.9-2.7-7.3-4.2-11.5-4.2 -3.7 0-6 1.3-6 3.9 0 2.7 3.3 3.7 8.5 5.8l7 2.7c7.2 2.8 11.3 7.8 11.3 15.8 0 9.7-8.2 18.4-22.5 18.4C86.5 61.2 78.1 58.6 71.9 53z"/>
			<path d="M120.7 32.3V2.5h15.2v31.6c0 10.3 2.7 14 8.7 14 6 0 8.9-3.7 8.9-14V2.5h14.7v29.8c0 19.7-7.4 28.9-23.6 28.9C128.5 61.2 120.7 52 120.7 32.3z"/>
			<path d="M195.8 2.5H211v57.6h-15.2V2.5z"/>
			<path d="M219.4 2.5H235l12.8 25.7 5.5 12.9h0.4c-0.7-6.2-1.9-14.9-1.9-22V2.5h14.5v57.6h-15.6l-12.8-25.8 -5.5-12.9h-0.4c0.7 6.6 1.9 14.9 1.9 22v16.7h-14.5V2.5z"/>
			<path d="M270.9 53l8.7-10.5c4.1 3.3 9.2 5.6 13.5 5.6 4.6 0 6.6-1.5 6.6-4.1 0-2.7-2.9-3.6-7.8-5.6l-7.2-3c-6.2-2.5-11.6-7.7-11.6-16 0-9.8 8.9-17.9 21.4-17.9 6.6 0 13.8 2.5 19.1 7.7l-7.6 9.6c-3.9-2.7-7.3-4.2-11.5-4.2 -3.7 0-6 1.3-6 3.9 0 2.7 3.3 3.7 8.5 5.8l7 2.7c7.2 2.8 11.3 7.8 11.3 15.8 0 9.7-8.2 18.4-22.5 18.4C285.5 61.2 277.2 58.6 270.9 53z"/>
			<path d="M320.1 2.5h15.2v57.6h-15.2V2.5z"/>
			<path d="M343.6 2.5h17c17.6 0 29.6 8.2 29.6 28.5s-12.1 29.1-28.7 29.1h-17.9V2.5zM359.8 47.9c8.3 0 14.9-3.4 14.9-16.8 0-13.5-6.6-16.3-14.9-16.3h-0.9v33.2H359.8z"/>
			<path d="M395 2.5h37.2v12.8h-22v9H429v12.8h-18.8v10.3h22.9v12.8H395V2.5z"/>
			<path d="M439.8 2.5h21.8c12.4 0 23 4.3 23 18.6 0 8.2-3.7 13.7-9.3 16.7l12.5 22.3h-17l-9.9-19.3h-5.9v19.3h-15.2V2.5zM460.6 28.8c6 0 9.2-2.7 9.2-7.6s-3.2-6.6-9.2-6.6h-5.5v14.2H460.6z"/>
		</g>
		<g fill="#333334">
			<path d="M519.8 55.9h-0.3l-1.1 4.4h-10.8V4.2h13.9v13.1l-0.3 5.8c2.8-2.6 6.4-4 9.9-4 9.5 0 15.7 8.1 15.7 20.4 0 13.9-8.2 21.8-16.8 21.8C526.4 61.2 522.9 59.4 519.8 55.9zM532.5 39.6c0-6.6-1.9-9.3-5.6-9.3 -2.1 0-3.7 0.8-5.5 2.9V48c1.6 1.5 3.4 1.9 5.2 1.9C529.8 49.9 532.5 47.4 532.5 39.6z"/>
			<path d="M550.2 40.1c0-13.1 9.5-21.1 19.3-21.1 11.9 0 17.6 8.7 17.6 19.5 0 2.4-0.3 4.8-0.6 5.8h-23c1.3 4.8 4.8 6.4 9.3 6.4 2.7 0 5.3-0.8 8.2-2.4l4.5 8.2c-4.4 3.1-10.2 4.7-14.7 4.7C559.2 61.2 550.2 53.5 550.2 40.1zM575.4 35.3c0-3.1-1.3-5.8-5.5-5.8 -3.1 0-5.6 1.8-6.4 5.8H575.4z"/>
			<path d="M595.7 44.8v-14h-5.3V20.4l6.1-0.5 1.6-10.5h11.4V20h9.2v10.8h-9.2v13.8c0 4.4 2.3 5.9 4.8 5.9 1.3 0 2.6-0.3 3.5-0.6l2.1 10c-2.1 0.6-5.1 1.5-9.5 1.5C600 61.2 595.7 54.6 595.7 44.8z"/>
			<path d="M624.9 48.8c0-8.4 6.4-13.1 21.9-14.7 -0.3-2.9-2.1-4.2-5.6-4.2 -2.9 0-6 1.1-10.2 3.4l-4.8-9c5.6-3.4 11.4-5.3 17.7-5.3 10.5 0 16.8 5.6 16.8 18.9v22.4h-11.3l-1-3.9h-0.3c-3.2 2.9-6.8 4.8-11.1 4.8C629.4 61.2 624.9 55.4 624.9 48.8zM646.8 47.8v-5.5c-6.6 1-8.7 3.1-8.7 5.3 0 1.9 1.3 2.9 3.7 2.9C644 50.6 645.3 49.4 646.8 47.8z"/>
		</g>
	</svg>
	<?php
	$masthead = ob_get_clean();

	wsu_register_inline_svg( 'masthead', $masthead );
}
