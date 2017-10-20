<?php

namespace WSU\News\Internal\Page_Curation\Customizer;

add_filter( 'customize_register', 'WSU\News\Internal\Page_Curation\Customizer\customize_register' );
add_action( 'customize_controls_print_footer_scripts', 'WSU\News\Internal\Page_Curation\Customizer\enqueue_scripts' );
add_action( 'customize_controls_enqueue_scripts', 'WSU\News\Internal\Page_Curation\Customizer\enqueue_styles' );

/**
 * Register the section, setting, and control used for page curation.
 *
 * @since 0.5.0
 *
 * @param \WP_Customize_Manager $wp_customize
 */
function customize_register( $wp_customize ) {

	$wp_customize->add_section( 'page_curation', array(
		'title' => 'Page Curation',
		'priority' => 10,
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
 * Enqueue the additional scripts required for page curation in
 * the Customizer.
 *
 * @since 0.5.0
 */
function enqueue_scripts() {
	wp_enqueue_script( 'page-curation-sortables', esc_url( get_stylesheet_directory_uri() . '/includes/js/page-curation-customizer.js' ), array( 'jquery', 'jquery-ui-sortable' ), spine_get_child_version() );
}

/**
 * Enqueue the additional styles required for page curation in
 * the Customizer.
 *
 * @since 0.5.0
 */
function enqueue_styles() {
	wp_enqueue_style( 'page-curation-customizer',  esc_url( get_stylesheet_directory_uri() . '/includes/css/page-curation-customizer.css' ), '', spine_get_child_version() );
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

function format_defaults () {
	$components = \WSU\News\Internal\Page_Curation\get_sections();
	$defaults = array();

	foreach ( $components as $k => $v ) {
		if ( apply_filters( 'homepage_control_hide_' . $k, false ) ) {
			$defaults[] = '[disabled]' . $k;
		} else {
			$defaults[] = $k;
		}
	}

	return join( ',', $defaults );
}
