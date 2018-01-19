<?php

namespace WSU\News\Internal\Announcements;

function get_post_type_slug() {
	return 'wsu_announcement';
}

add_action( 'init', 'WSU\News\Internal\Announcements\register_post_type' );
add_action( 'add_meta_boxes', 'WSU\News\Internal\Announcements\add_meta_boxes' );
add_action( 'manage_' . get_post_type_slug() . '_posts_custom_column', 'WSU\News\Internal\Announcements\manage_list_table_email_column', 10, 2 );
add_filter( 'manage_edit-' . get_post_type_slug() . '_columns', 'WSU\News\Internal\Announcements\manage_list_table_columns', 10, 1 );
add_action( 'wp_ajax_submit_announcement', 'WSU\News\Internal\Announcements\ajax_callback' );
add_action( 'wp_ajax_nopriv_submit_announcement', 'WSU\News\Internal\Announcements\ajax_callback' );
add_shortcode( 'wsu_announcement_form', 'WSU\News\Internal\Announcements\output_submission_form' );
add_action( 'pre_get_posts', 'WSU\News\Internal\Announcements\filter_archive_query' );

/**
 * Register the post type used for announcements.
 *
 * Forked from the original WSU News & Announcements plugin
 * and adapted for use on WSU Insider.
 *
 * @since 0.7.0
 */
function register_post_type() {
	$labels = array(
		'name'               => 'Announcements',
		'singular_name'      => 'Announcement',
		'add_new'            => 'Add New',
		'add_new_item'       => 'Add New Announcement',
		'edit_item'          => 'Edit Announcement',
		'new_item'           => 'New Announcement',
		'all_items'          => 'All Announcements',
		'view_item'          => 'View Announcement',
		'search_items'       => 'Search Announcements',
		'not_found'          => 'No announcements found',
		'not_found_in_trash' => 'No announcements found in Trash',
		'parent_item_colon'  => '',
		'menu_name'          => 'Announcements',
	);

	$args = array(
		'labels'             => $labels,
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => true,
		'rewrite'            => array(
			'slug' => 'announcement',
		),
		'capability_type'    => 'post',
		'has_archive'        => 'announcements',
		'hierarchical'       => false,
		'menu_position'      => 5,
		'supports'           => array( 'title', 'editor' ),
		'show_in_rest'       => true,
		'rest_base'          => 'announcements',
	);

	\register_post_type( get_post_type_slug(), $args );
}

/**
 * Add meta boxes used in the announcement edit screen.
 *
 * Forked from the original WSU News & Announcements plugin
 * and adapted for use on WSU Insider.
 *
 * @since 0.7.0
 */
function add_meta_boxes() {
	add_meta_box( 'wsu_announcement_email', 'Announcement Submitted By:', 'WSU\News\Internal\Announcements\display_email_meta_box', get_post_type_slug(), 'side' );
}

/**
 * Display the email associated with the announcement submission.
 *
 * Forked from the original WSU News & Announcements plugin
 * and adapted for use on WSU Insider.
 *
 * @since 0.7.0
 *
 * @param \WP_Post $post Current post object.
 */
function display_email_meta_box( $post ) {
	$email = get_post_meta( $post->ID, '_announcement_contact_email', true );

	if ( ! $email ) {
		echo '<strong>No email submitted with announcement.</strong>';
	} else {
		echo esc_html( $email );
	}
}

/**
 * Modify the columns in the post type list table.
 *
 * Forked from the original WSU News & Announcements plugin
 * and adapted for use on WSU Insider.
 *
 * @since 0.7.0
 *
 * @param array $columns Current list of columns and their names.
 *
 * @return array Modified list of columns.
 */
function manage_list_table_columns( $columns ) {
	// We may use categories and tags, but we don't need them on this screen.
	unset( $columns['categories'] );
	unset( $columns['tags'] );
	unset( $columns['date'] );

	// Add our custom columns. Move date to the end of the array after we unset it above.
	$columns['contact_email'] = 'Contact Email';
	$columns['date'] = 'Publish Date';

	return $columns;
}

/**
 * Handle output for the contact email column in the announcement list table.
 *
 * Forked from the original WSU News & Announcements plugin
 * and adapted for use on WSU Insider.
 *
 * @since 0.7.0
 *
 * @param string $column_name Current column being displayed.
 * @param int    $post_id     Post ID of the current row being displayed.
 */
function manage_list_table_email_column( $column_name, $post_id ) {
	if ( 'contact_email' !== $column_name ) {
		return;
	}

	$contact_email = get_post_meta( $post_id, '_announcement_contact_email', true );
	if ( $contact_email ) {
		echo esc_html( $contact_email );
	}
}

/**
 * Handle the ajax submission of the announcement form.
 *
 * Forked from the original WSU News & Announcements plugin
 * and adapted for use on WSU Insider.
 *
 * @since 0.7.0
 */
function ajax_callback() {
	if ( ! DOING_AJAX || ! isset( $_POST['action'] ) || 'submit_announcement' !== $_POST['action'] ) { // WPCS: CSRF Ok.
		die();
	}

	// If the honeypot input has anything filled in, we can bail.
	if ( isset( $_POST['other'] ) && '' !== $_POST['other'] ) { // WPCS: CSRF Ok.
		die();
	}

	$title = $_POST['title']; // WPCS: CSRF Ok. Sanitized in wp_insert_post().
	$text  = wp_kses_post( $_POST['text'] );
	$email = sanitize_email( $_POST['email'] );

	// If a websubmission user exists, we'll use that user ID.
	$user = get_user_by( 'slug', 'websubmission' );

	if ( is_wp_error( $user ) || false === $user ) {
		$user_id = 0;
	} else {
		$user_id = $user->ID;
	}

	$post_date = date( 'Y-m-d H:i:s', strtotime( $_POST['date'] ) ); // WPCS: Ok.
	$post_date_gmt = get_gmt_from_date( $post_date );

	$post_data = array(
		'comment_status' => 'closed',
		'ping_status'    => 'closed',
		'post_author'    => $user_id,
		'post_content'   => $text,    // Sanitized with wp_kses_post(), probably overly so.
		'post_title'     => $title,   // Sanitized in wp_insert_post().
		'post_type'      => 'wsu_announcement',
		'post_status'    => 'pending',
		'post_date'      => $post_date,
		'post_date_gmt'  => $post_date_gmt,
	);
	$post_id = wp_insert_post( $post_data );

	if ( is_wp_error( $post_id ) ) {
		echo 'error';
		exit;
	}

	update_post_meta( $post_id, '_announcement_contact_email', $email );

	echo 'success';
	exit;
}

/**
 * Setup the announcement form for output when the shortcode is used.
 *
 * Forked from the original WSU News & Announcements plugin
 * and adapted for use on WSU Insider.
 *
 * @since 0.7.0
 *
 * @return string Contains form to be output.
 */
function output_submission_form() {
	// Enqueue jQuery UI's datepicker to provide an interface for the publish date(s).
	wp_enqueue_script( 'jquery-ui-datepicker' );
	wp_enqueue_style( 'jquery-ui-core', 'https://code.jquery.com/ui/1.12.1/themes/smoothness/jquery-ui.css' );

	// Enqueue the Javascript needed to handle the form submission properly.
	wp_enqueue_script( 'wsu-news-announcement-form', get_stylesheet_directory_uri() . '/includes/js/announcements-form.js', array(), false, true );

	// Provide a global variable containing the ajax URL that we can access
	wp_localize_script( 'wsu-news-announcement-form', 'announcementSubmission', array(
		'ajaxurl' => admin_url( 'admin-ajax.php' ),
	) );

	wp_enqueue_style( 'wsu-news-announcement-form', get_stylesheet_directory_uri() . '/includes/css/announcements-form.css' );

	// Build the output to return for use by the shortcode.
	ob_start();
	?>
	<div id="announcement-submission-form" class="announcement-form">
		<form action="#" class="">
			<label for="announcement-form-title">Announcement Title:</label>
			<input type="text" id="announcement-form-title" class="announcement-form-input" name="announcement-title" value="" />
			<label for="announcement-form-text">Announcement Text:</label>
			<?php
			$editor_settings = array(
				'wpautop'       => true,
				'media_buttons' => false,
				'textarea_name' => 'announcement-text',
				'textarea_rows' => 15,
				'editor_class'  => 'announcement-form-input',
				'teeny'         => false,
				'dfw'           => false,
				'tinymce'       => array(
					'theme_advanced_disable' => 'wp_more, fullscreen, wp_help',
				),
				'quicktags'     => false,
			);
			wp_editor( '', 'announcement-form-text', $editor_settings );
			?>
			<label for="announcement-form-date">What date should this announcement be published on?</label><br>
			<input type="text" id="announcement-form-date" class="announcement-form-input announcement-form-date-input" name="announcement-date" value="" />
			<br>
			<br>
			<label for="announcement-form-email">Your Email Address:</label><br>
			<input type="text" id="announcement-form-email" class="announcement-form-input" name="announcement-email" value="" />
			<div id="announcement-other-wrap">
				If you see the following input box, please leave it empty.
				<label for="announcement-form-other">Other Input:</label>
				<input type="text" id="announcement-form-other" class="announcement-form-input" name="announcement-other" value="" />
			</div>
			<div id="announcement-submit-wrap">
				<input type="submit" id="announcement-form-submit" class="announcement-form-input" value="Submit Announcement" />
			</div>
		</form>
	</div>
	<?php
	$output = ob_get_contents();
	ob_end_clean();

	return $output;
}

/**
 *
 * @since 0.7.0
 *
 * @param \WP_Query $wp_query
 */
function filter_archive_query( $wp_query ) {
	if ( ! $wp_query->is_post_type_archive( get_post_type_slug() ) ) {
		return;
	}

	$date_query = array(
		array(
			'month' => date( 'n', current_time( 'timestamp' ) ),
			'day' => date( 'j', current_time( 'timestamp' ) )
		)
	);
	$wp_query->set( 'date_query', $date_query );
	$wp_query->set( 'posts_per_page', '-1' );
}
