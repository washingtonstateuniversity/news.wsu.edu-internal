<?php
/**
 * The custom Customizer Control used to manage the selection of
 * the "Good to Know" section on the front page.
 */

namespace WSU\News\Internal\Page_Curation\Customizer;

class Good_To_Know_Control extends \WP_Customize_Control {

	/**
	 * Enqueue jQuery Autocomplete and its dependencies.
	 *
	 * @since   0.6.0
	 */
	public function enqueue() {
		wp_enqueue_script( 'jquery-ui-core' );
		wp_enqueue_script( 'jquery-ui-autocomplete' );
	}

	/**
	 * Output the elements used to select featured posts for display on
	 * the front page.
	 *
	 * @since 0.6.0
	 */
	public function render_content() {
		$post_ids = explode( ',', $this->value() );

		if ( ! $post_ids ) {
			$post_ids = array();
		}

		if ( ! empty( $this->label ) ) : ?>
			<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
		<?php endif;
		if ( ! empty( $this->description ) ) : ?>
			<span class="description customize-control-description"><?php echo esc_html( $this->description ); ?></span>
		<?php endif;
		?>

		<div class="gtk-post-selection">
			<label for="gtk-post-title">Find story</label>
			<input id="gtk-post-title" type="text" value="" />
		</div>

		<div class="selected-gtk-posts">
			<?php
			for ( $i = 0; $i <= 4; $i++ ) {
				if ( isset( $post_ids[ $i ] ) ) {
					?>
					<div class="gtk-post-single" data-gtk-post-id="<?php echo esc_attr( $post_ids[ $i ] ); ?>">
						<p><?php echo esc_html( get_the_title( $post_ids[ $i ] ) ); ?></p>
						<button class="remove-gtk">Remove</button>
					</div>
					<?php
				} else {
					?>
					<div class="gtk-post-empty">
						No Good to Know post selected for this area.
					</div>
					<?php
				}
			}
			?>
		</div>

		<input type="hidden" <?php $this->link(); ?> value="<?php echo esc_attr( implode( ',', $post_ids ) ); ?>"/>

		<script>
			jQuery( function() {
				let gtk_post_input = $( "#gtk-post-title" );
				gtk_post_input.autocomplete( {
					source: "<?php echo esc_js( get_rest_url( get_current_blog_id(), '/insider/v1/featured' ) ); ?>",
					minLength: 2,
					// Add the selected item label to the input field rather than the value.
					select: function( event, ui ) {
						event.preventDefault();
						var next_slot = $( ".gtk-post-empty" ).first();

						next_slot.removeClass( "gtk-post-empty" ).addClass( "gtk-post-single" );
						next_slot.data( "gtk-post-id", ui.item.value );

						next_slot.html( "<p>" + ui.item.label + "</p><button class=\"remove-gtk\">Remove</button>" );

						$( this ).val( "" );

						let post_ids = [];

						$( ".gtk-post-single" ).each( function() {
							post_ids.push( $( this ).data( "gtk-post-id" ) );
						} );

						$( "input[data-customize-setting-link='gtk_posts']" ).attr( "value", post_ids ).trigger( "change" );
					},
					// Don't show selected titles in the input before selection.
					focus: function( event, ui ) {
						event.preventDefault();
					}
				} );
			} );
		</script>
		<?php
	}
}
