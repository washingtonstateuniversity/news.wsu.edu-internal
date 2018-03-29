<?php

namespace WSU\News\Internal\Taxonomy;

add_action( 'init', 'WSU\News\Internal\Taxonomy\register_term_meta', 10 );

add_action( 'category_add_form_fields', 'WSU\News\Internal\Taxonomy\add_clerical_field' );
add_action( 'category_edit_form_fields', 'WSU\News\Internal\Taxonomy\edit_clerical_field' );

add_action( 'edit_category', 'WSU\News\Internal\Taxonomy\save_clerical_field' );
add_action( 'create_category', 'WSU\News\Internal\Taxonomy\save_clerical_field' );

add_filter( 'manage_edit-category_columns', 'WSU\News\Internal\Taxonomy\edit_term_columns', 10, 1 );
add_filter( 'manage_category_custom_column', 'WSU\News\Internal\Taxonomy\display_term_column', 10, 3 );

/**
 * Register meta assigned to terms.
 *
 * @since 0.10.0
 */
function register_term_meta() {
	register_meta( 'term', 'clerical_term', 'WSU\News\Internal\Taxonomy\sanitize_clerical_flag' );
}

/**
 * Sanitize a term's clerical flag to be either "yes" or "no".
 *
 * @since 0.10.0
 *
 * @param string $clerical Then unsanitized clerical flag.
 * @return string The sanitized clerical flag. Either "yes" or "no".
 */
function sanitize_clerical_flag( $clerical ) {
	if ( 'yes' !== $clerical ) {
		$clerical = 'no';
	}

	return $clerical;
}

/**
 * Retrieve a term's clerical flag.
 *
 * @since 0.10.0
 *
 * @param int $term_id The ID of the term.
 * @return string The term's clerical flag.
 */
function get_clerical_flag( $term_id ) {
	$clerical = get_term_meta( $term_id, 'clerical_flag', true );
	$clerical = sanitize_clerical_flag( $clerical );

	return $clerical;
}

/**
 * Determine if a term should be considered clerical.
 *
 * @since 0.10.0
 *
 * @param int $term_id
 * @return bool True if clerical, false if not.
 */
function is_term_clerical( $term_id ) {
	$clerical = get_clerical_flag( $term_id );

	if ( 'yes' === $clerical ) {
		return true;
	}

	return false;
}

/**
 * Add a select box to capture a term's clerical status for new terms.
 *
 * @since 0.10.0
 */
function add_clerical_field() {
	wp_nonce_field( 'add', 'clerical_flag_nonce' );
	?>
	<div class="form-field">
		<label for="clerical_flag">Clerical Category</label>
		<select name="clerical_flag" id="clerical_flag">
				<option value="no">No</option>
				<option value="yes">Yes</option>
		</select>
		<p>Clerical categories can be used to organize posts, but their names are not displayed on the front page or on individual views.</p>
	</div>
	<?php
}

/**
 * Add a select box to capture a term's clerical status on existing terms.
 *
 * @since 0.10.0
 *
 * @param \WP_Term $term The term being edited.
 */
function edit_clerical_field( $term ) {
	$clerical = get_clerical_flag( $term->term_id );
	?>
	<tr class="form-field">
		<th scope="row">
			<label for="clerical_flag">Clerical Category</label>
		</th>
		<td>
			<?php wp_nonce_field( 'add', 'clerical_flag_nonce' ); ?>
			<select name="clerical_flag" id="clerical_flag">
				<option value="no" <?php selected( $clerical, 'no' ); ?>>No</option>
				<option value="yes" <?php selected( $clerical, 'yes' ); ?>>Yes</option>
			</select>
			<p class="description">Clerical categories can be used to organize posts, but their names are not displayed on the front page or on individual views.</p>
		</td>
	</tr>
	<?php
}

/**
 * Save a term's clerical flag on both the add new and edit term screens.
 *
 * @since 0.10.0
 *
 * @param int $term_id The term being saved.
 */
function save_clerical_field( $term_id ) {
	if ( ! isset( $_POST['clerical_flag'] ) || ! isset( $_POST['clerical_flag_nonce'] ) || ! wp_verify_nonce( $_POST['clerical_flag_nonce'], 'add' ) ) {
		return;
	}

	$clerical_flag = sanitize_clerical_flag( $_POST['clerical_flag'] );
	update_term_meta( $term_id, 'clerical_flag', $clerical_flag );
}

/**
 * Edit the columns displayed in the list table showing existing terms.
 *
 * @since 0.10.0
 *
 * @param array $columns Columns output at the top of the term list table.
 * @return array Modified list of columns.
 */
function edit_term_columns( $columns ) {
	$columns['clerical_flag'] = 'Clerical';

	return $columns;
}

/**
 * Display the value of each row (term) for a custom column.
 *
 * @since 0.10.0
 *
 * @param string $output  Value to output inside the column/row.
 * @param string $column  The name of the column being output.
 * @param int    $term_id The ID of the term associated with the current row.
 * @return void
 */
function display_term_column( $output, $column, $term_id ) {
	if ( 'clerical_flag' === $column ) {
		return ucwords( get_clerical_flag( $term_id ) );
	}

	return $output;
}
