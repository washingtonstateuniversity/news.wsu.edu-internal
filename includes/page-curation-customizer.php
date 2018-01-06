<?php

namespace WSU\News\Internal\Page_Curation\Customizer;

add_filter( 'customize_register', 'WSU\News\Internal\Page_Curation\Customizer\register_featured_posts' );
add_filter( 'customize_register', 'WSU\News\Internal\Page_Curation\Customizer\register_gtk_posts' );
add_action( 'rest_api_init', 'WSU\News\Internal\Page_Curation\Customizer\register_rest_route' );

add_filter( 'customize_register', 'WSU\News\Internal\Page_Curation\Customizer\customize_register' );
add_action( 'customize_controls_print_footer_scripts', 'WSU\News\Internal\Page_Curation\Customizer\enqueue_scripts' );
add_action( 'customize_controls_enqueue_scripts', 'WSU\News\Internal\Page_Curation\Customizer\enqueue_styles' );

/**
 * Register a custom endpoint to handle lookups for featured
 * posts from the Customizer.
 *
 * @since 0.6.0
 */
function register_rest_route() {
	\register_rest_route( 'insider/v1', '/featured', array(
		'methods'  => 'GET',
		'callback' => 'WSU\News\Internal\Page_Curation\Customizer\rest_search_featured',
	) );
}

/**
 * Return search results for featured posts.
 *
 * @since 0.6.0
 *
 * @param \WP_REST_Request $request
 *
 * @return array
 */
function rest_search_featured( $request ) {
	if ( empty( $request['term'] ) ) {
		return array();
	}

	$results = new \WP_Query( array(
		'post_type' => array( 'post' ),
		'post_status' => 'publish',
		'posts_per_page' => 20,
		's' => sanitize_text_field( $request['term'] ),
	) );

	$posts = array();
	foreach ( $results->posts as $post ) {
		if ( ! $post->ID ) {
			continue;
		}

		$posts[] = array(
			'value' => $post->ID,
			'label' => trim( esc_html( strip_tags( get_the_title( $post ) ) ) ),
			'image' => esc_url( get_the_post_thumbnail_url( $post, 'spine-small_size' ) ),
		);
	}

	return $posts;
}


function register_gtk_posts( $wp_customize ) {

	$wp_customize->add_section( 'gtk_posts', array(
		'title' => 'Good to Know Posts',
		'priority' => 9,
		'capability' => 'publish_pages',
		'active_callback' => 'is_front_page',
	) );

	$wp_customize->add_setting( 'gtk_posts', array(
		'default' => \WSU\News\Internal\Page_Curation\get_gtk_posts(),
		'type' => 'option',
		'capability' => 'publish_pages',
	) );

	include_once __DIR__ . '/class-good-to-know-customizer-control.php';

	$wp_customize->add_control( new \WSU\News\Internal\Page_Curation\Customizer\Good_To_Know_Control( $wp_customize, 'gtk_posts', array(
		'description'       => 'Curate Good to Know on the front page.',
		'section'           => 'gtk_posts',
		'settings'          => 'gtk_posts',
		'input_attrs'       => \WSU\News\Internal\Page_Curation\get_gtk_posts(),
		'priority'          => 10,
		'type'              => 'hidden',
		'sanitize_callback' => 'WSU\News\Internal\Page_Curation\Customizer\sanitize_sections',
	) ) );
}

/**
 * Register the section, setting, and control used for curating featured
 * posts at the top of the front page.
 *
 * @since 0.6.0
 *
 * @param \WP_Customize_Manager $wp_customize
 */
function register_featured_posts( $wp_customize ) {

	$wp_customize->add_section( 'featured_posts', array(
		'title' => 'Featured Posts',
		'priority' => 9,
		'capability' => 'publish_pages',
		'active_callback' => 'is_front_page',
	) );

	$wp_customize->add_setting( 'featured_posts', array(
		'default' => \WSU\News\Internal\Page_Curation\get_featured_posts(),
		'type' => 'option',
		'capability' => 'publish_pages',
	) );

	include_once __DIR__ . '/class-featured-posts-customizer-control.php';

	$wp_customize->add_control( new \WSU\News\Internal\Page_Curation\Customizer\Featured_Posts_Control( $wp_customize, 'featured_posts', array(
		'description'       => 'Curate featured posts displayed on the front page.',
		'section'           => 'featured_posts',
		'settings'          => 'featured_posts',
		'input_attrs'       => \WSU\News\Internal\Page_Curation\get_featured_posts(),
		'priority'          => 10,
		'type'              => 'hidden',
		'sanitize_callback' => 'WSU\News\Internal\Page_Curation\Customizer\sanitize_sections',
	) ) );
}

/**
 * Register the section, setting, and control used for section curation
 * on the front page.
 *
 * @since 0.5.0
 *
 * @param \WP_Customize_Manager $wp_customize
 */
function customize_register( $wp_customize ) {

	$wp_customize->add_section( 'page_curation', array(
		'title' => 'Page Sections',
		'priority' => 10,
		'capability' => 'publish_pages',
		'active_callback' => 'is_front_page',
	) );

	$wp_customize->add_setting( 'page_curation', array(
		'default' => \WSU\News\Internal\Page_Curation\get_sections(),
		'type' => 'option',
		'capability' => 'publish_pages',
	) );

	include_once __DIR__ . '/class-page-curation-customizer-control.php';

	$wp_customize->add_control( new \WSU\News\Internal\Page_Curation\Customizer\Customizer_Control( $wp_customize, 'page_curation', array(
		'description'       => 'Curate available sections for display on this page. Drag and drop each section to set the display order.',
		'section'           => 'page_curation',
		'settings'          => 'page_curation',
		'input_attrs'       => \WSU\News\Internal\Page_Curation\get_sections(),
		'priority'          => 10,
		'type'              => 'hidden',
		'sanitize_callback' => 'WSU\News\Internal\Page_Curation\Customizer\sanitize_sections',
	) ) );
}

/**
 * Enqueue the additional scripts required for front page curation in
 * the Customizer.
 *
 * @since 0.5.0
 */
function enqueue_scripts() {
	wp_enqueue_script( 'page-curation-sortables', esc_url( get_stylesheet_directory_uri() . '/includes/js/page-curation-customizer.js' ), array( 'jquery', 'jquery-ui-sortable' ), spine_get_child_version() );
}

/**
 * Enqueue the additional styles required for front page curation in
 * the Customizer.
 *
 * @since 0.5.0
 */
function enqueue_styles() {
	wp_enqueue_style( 'page-curation-customizer', esc_url( get_stylesheet_directory_uri() . '/includes/css/page-curation-customizer.css' ), '', spine_get_child_version() );
}

/**
 * Sanitize the new saved input of curated sections from the Customizer.
 *
 * @since 0.5.0
 *
 * @param $input
 *
 * @return mixed
 */
function sanitize_sections( $input ) {
	return $input;
}
