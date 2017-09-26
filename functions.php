<?php

include_once __DIR__ . '/includes/theme-images.php';

add_action( 'wp_enqueue_scripts', 'internal_news_enqueue_scripts' );
/**
 * Enqueue custom styles.
 *
 * @since 0.0.1
 */
function internal_news_enqueue_scripts() {
	wp_enqueue_style( 'source_sans_pro', '//fonts.googleapis.com/css?family=Source+Sans+Pro:300,300i,400,400i,900,900i' );

}
