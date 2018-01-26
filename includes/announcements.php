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
add_action( 'generate_rewrite_rules', 'WSU\News\Internal\Announcements\generate_date_archive_rewrite_rules', 10, 1 );
add_action( 'pre_get_posts', 'WSU\News\Internal\Announcements\filter_archive_query' );
add_filter( 'spine_get_title', 'WSU\News\Internal\Announcements\filter_page_title', 11, 3 );
add_action( 'wp_ajax_copy_announcement_to_post', 'WSU\News\Internal\Announcements\ajax_copy_announcement_to_post' );

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
	add_meta_box( 'copy_to_post', 'Copy to Post', 'WSU\News\Internal\Announcements\display_copy_meta_box', get_post_type_slug(), 'side' );
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
 * Display a meta box that allows for an announcement to be copied to a
 * post on WSU Insider.
 *
 * @since 0.8.0
 *
 * @param \WP_Post $post Current post object.
 */
function display_copy_meta_box( $post ) {
	$status = get_post_meta( $post->ID, '_copied_post_id', true );
	$post_link = false;

	if ( $status ) {
		$copied_post = get_post( $status );
		if ( $copied_post && 'post' === $copied_post->post_type && 'trash' !== $copied_post->post_status ) {
			$post_link = get_edit_post_link( $copied_post->ID );
			?>
			<div id="copied-posts">
				<p><a href="<?php echo esc_url( $post_link ); ?>">Edit the post</a> that was copied from this announcement.</p>
			</div>
			<?php
		} else {
			delete_post_meta( $copied_post->ID, '_copied_post_id' );
		}
	}

	if ( false === $post_link ) {
		?>
		<style>
			#copy-post {
				text-decoration: underline;
				color: #0073aa;
				transition-property: border, background, color;
				transition-duration: .05s;
				transition-timing-function: ease-in-out;
				cursor: pointer;
			}

			#copy-post:hover {
				color: #00a0d2;
			}
		</style>
		<div id="copied-posts"></div>
		<div class="copy-post-container">
			<span id="copy-post" data-post-id="<?php echo esc_attr( $post->ID ); ?>">Copy this announcement</span> to a new post.
		</div>
		<script type="text/javascript">
			(function( $, window ) {
				$( "#copy-post" ).on( "click", function() {
					let post_id = $( this ).data( "post-id" );
					let copy_nonce = '<?php echo esc_js( wp_create_nonce( 'copy-post-nonce' ) ); ?>';

					$.ajax( {
						url: window.ajaxurl,
						type: "POST",
						data: {
							action: "copy_announcement_to_post",
							_ajax_nonce: copy_nonce,
							post_id: post_id
						}
					} ).done( function( data ) {
						if ( true === data.success ) {
							$( "#copied-posts" ).html( "Copy successful. <a href='" + data.data + "'>Edit the new post</a>." );
							$( ".copy-post-container").remove();
						} else {
							$( ".copy-post-container").append( "<p><strong>Copy unsuccessful.</strong> Error: " + data.data + "</p>" );
						}
					} );
				} );
			})( jQuery, window );
		</script>
		<?php
	}
}

/**
 * Handle an ajax request to copy an announcement to a new post.
 *
 * @since 0.8.0
 */
function ajax_copy_announcement_to_post() {
	check_ajax_referer( 'copy-post-nonce' );

	$post = get_post( absint( $_POST['post_id'] ) );

	if ( ! $post || get_post_type_slug() !== $post->post_type ) {
		wp_send_json_error( 'This is not an announcement.' );
		wp_die();
	}

	$new_post = array(
		'post_title' => $post->post_title,
		'post_content' => $post->post_content,
		'post_type' => 'post',
		'post_status' => 'draft',
	);
	$created_post = wp_insert_post( $new_post );

	if ( is_wp_error( $created_post ) ) {
		wp_send_json_error( $created_post->get_error_message() );
		wp_die();
	}

	$edit_post_link = get_edit_post_link( $created_post );
	update_post_meta( $post->ID, '_copied_post_id', $created_post );

	wp_send_json_success( $edit_post_link );
	wp_die();
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

	$post_date = date( 'Y-m-d H:i:s', strtotime( $_POST['date'] ) ); // WPCS: CSRF Ok.
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
				'teeny'         => true,
				'dfw'           => false,
				'tinymce'       => array(
					'toolbar1' => 'bold italic bullist numlist link',
					'content_css' => get_stylesheet_directory_uri() . '/style.css',
					'valid_styles' => '{ "*": "" }', // Disable inline styles.
					'valid_elements' => 'a[href],strong/b,em/i,p,ul,ol,li', // Allow only a subset of HTML elements.
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
 * Generate day based archive rewrite rules for announcements.
 *
 * @since 0.7.0
 *
 * @param \WP_Rewrite $wp_rewrite
 *
 * @return \WP_Rewrite
 */
function generate_date_archive_rewrite_rules( $wp_rewrite ) {
	$rules = array();

	$post_type = get_post_type_object( get_post_type_slug() );

	if ( false === $post_type->has_archive ) {
		return $wp_rewrite;
	}

	$query = 'index.php?post_type=' . get_post_type_slug();
	$rule = $post_type->has_archive . '/([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})';

	$query .= '&year=' . $wp_rewrite->preg_index( 1 );
	$query .= '&month=' . $wp_rewrite->preg_index( 2 );
	$query .= '&day=' . $wp_rewrite->preg_index( 3 );

	$rules[ $rule . '/?$' ] = $query;

	$wp_rewrite->rules = $rules + $wp_rewrite->rules;

	return $wp_rewrite;
}

/**
 * Filter the main query so that all announcements for a given day are shown.
 *
 * Works with:
 *    - insider.wsu.edu/announcements/ (current day)
 *    - insider.wsu.edu/announcements/2018/01/19/ (single day)
 *
 * @since 0.7.0
 *
 * @param \WP_Query $wp_query
 */
function filter_archive_query( $wp_query ) {
	if ( is_admin() ) {
		return;
	}

	if ( $wp_query->is_post_type_archive( get_post_type_slug() ) && ! $wp_query->is_date() && $wp_query->is_main_query() ) {
		$date_query = array(
			array(
				'year' => date( 'Y', current_time( 'timestamp' ) ),
				'month' => date( 'n', current_time( 'timestamp' ) ),
				'day' => date( 'j', current_time( 'timestamp' ) ),
			),
		);
		$wp_query->set( 'date_query', $date_query );
		$wp_query->set( 'posts_per_page', '-1' );
	} elseif ( $wp_query->is_post_type_archive( get_post_type_slug() ) && $wp_query->is_date() && $wp_query->is_main_query() ) {
		$wp_query->set( 'posts_per_page', '-1' );
	}
}

/**
 * Retrieve a list of posts to display from the most recent day
 * that had announcements.
 *
 * For use as a backup when a current day does not have announcements.
 *
 * @since 0.7.2
 *
 * @return bool|\WP_Query
 */
function get_previous_day_archive_posts() {
	$days = 1;
	$date = time();

	while ( $days <= 9 ) {
		$previous_day = date( 'j', $date - ( DAY_IN_SECONDS * $days ) );
		$previous_month = date( 'm', $date - ( DAY_IN_SECONDS * $days ) );
		$previous_year = date( 'Y', $date - ( DAY_IN_SECONDS * $days ) );

		$previous_posts = new \WP_Query( array(
			'post_type' => get_post_type_slug(),
			'posts_per_page' => -1,
			'date_query' => array(
				'year' => $previous_year,
				'month' => $previous_month,
				'day' => $previous_day,
			),
		) );

		if ( $previous_posts->have_posts() ) {
			return $previous_posts;
		}

		$days++;
	}

	return false;
}

/**
 * Generate the URLs used to view previous and next date archives.
 *
 * @since 0.7.0
 *
 * @param string $date
 *
 * @return array
 */
function get_date_archive_pagination_urls( $date ) {
	$days = 1;

	while ( $days <= 9 ) {
		$previous_day = date( 'j', strtotime( $date ) - ( DAY_IN_SECONDS * $days ) );
		$previous_month = date( 'm', strtotime( $date ) - ( DAY_IN_SECONDS * $days ) );
		$previous_year = date( 'Y', strtotime( $date ) - ( DAY_IN_SECONDS * $days ) );

		$previous_check = get_posts( array(
			'post_type' => get_post_type_slug(),
			'posts_per_page' => 1,
			'fields' => 'ids',
			'date_query' => array(
				'year' => $previous_year,
				'month' => $previous_month,
				'day' => $previous_day,
			),
		) );

		if ( 0 < count( $previous_check ) ) {
			break;
		}

		$days++;
	}

	$days = 1;

	while ( $days <= 9 ) {
		if ( is_post_type_archive( get_post_type_slug() ) && ! is_day() ) {
			$next_check = array();
			break;
		}

		if ( strtotime( $date ) > ( time() - DAY_IN_SECONDS ) ) {
			$next_check = array();
			break;
		}

		$next_day = date( 'j', strtotime( $date ) + ( DAY_IN_SECONDS * $days ) );
		$next_month = date( 'm', strtotime( $date ) + ( DAY_IN_SECONDS * $days ) );
		$next_year = date( 'Y', strtotime( $date ) + ( DAY_IN_SECONDS * $days ) );

		$next_check = get_posts( array(
			'post_type' => get_post_type_slug(),
			'posts_per_page' => 1,
			'fields' => 'ids',
			'date_query' => array(
				'year' => $next_year,
				'month' => $next_month,
				'day' => $next_day,
			),
		) );

		if ( 0 < count( $next_check ) ) {
			break;
		}

		$days++;
	}

	if ( 0 !== count( $previous_check ) ) {
		$previous_url = home_url( 'announcements/' . $previous_year . '/' . $previous_month . '/' . $previous_day . '/' );
	} else {
		$previous_url = false;
	}

	if ( 0 !== count( $next_check ) ) {
		$next_url = home_url( 'announcements/' . $next_year . '/' . $next_month . '/' . $next_day . '/' );
	} else {
		$next_url = false;
	}

	return array(
		'previous' => $previous_url,
		'next' => $next_url,
	);
}

/**
 * Filter the document title used for daily announcement archives.
 *
 * @since 0.7.2
 *
 * @param string $title
 * @param string $site_part
 * @param string $global_part
 *
 * @return string
 */
function filter_page_title( $title, $site_part, $global_part ) {
	if ( is_post_type_archive( get_post_type_slug() ) && is_day() ) {
		$title = get_the_date( 'F j, Y' ) . ' Announcements | ' . $site_part . $global_part;
	}

	return $title;
}
