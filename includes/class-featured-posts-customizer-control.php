<?php
/**
 * The custom Customizer Control used to manage the selection of
 * featured posts for displayed on the front page.
 */

namespace WSU\News\Internal\Page_Curation\Customizer;

class Featured_Posts_Control extends \WP_Customize_Control {

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

		<div class="featured-post-selection">
			<label for="featured-post-title">Find featured story</label>
			<input id="featured-post-title" type="text" value="" />
		</div>

		<div class="selected-featured-posts">
			<?php
			for ( $i = 0; $i <= 3; $i++ ) {
				if ( isset( $post_ids[ $i ] ) ) {
					?>
					<div class="featured-post-single" data-featured-post-id="<?php echo esc_attr( $post_ids[ $i ] ); ?>">
						<p><?php echo esc_html( get_the_title( $post_ids[ $i ] ) ); ?></p>
						<?php
						if ( has_post_thumbnail( $post_ids[ $i ] ) ) {
							?>
							<figure>
								<img src="<?php echo esc_url( get_the_post_thumbnail_url( $post_ids[ $i ], 'spine-small_size' ) ); ?>"/>
							</figure>
							<?php
						} else {
							?>
							<div class="no-image">
								<p>No image assigned</p>
							</div>
							<?php
						}
						?>
						<button class="remove-featured">Remove</button>
					</div>
					<?php
				} else {
					?>
					<div class="featured-post-empty">
						No featured post selected for this area.
					</div>
					<?php
				}
			}
			?>
		</div>

		<input type="hidden" <?php $this->link(); ?> value="<?php echo esc_attr( implode( ',', $post_ids ) ); ?>"/>

		<script>
			jQuery( function( $ ) {
				var featured_post_input = $( "#featured-post-title" );
				featured_post_input.autocomplete( {
					source: "<?php echo esc_js( get_rest_url( get_current_blog_id(), '/insider/v1/featured' ) ); ?>",
					minLength: 2,
					// Add the selected item label to the input field rather than the value.
					select: function( event, ui ) {
						event.preventDefault();
						var next_slot = $( ".featured-post-empty" ).first();

						next_slot.removeClass( "featured-post-empty" ).addClass( "featured-post-single" );
						next_slot.data( "featured-post-id", ui.item.value );

						let image_markup = "<div class='no-image'><p>No image assigned</p></div>";

						if ( false !== ui.item.image ) {
							image_markup = "<figure><img src='" + ui.item.image + "'></figure>";
						}
						next_slot.html( "<p>" + ui.item.label + "</p>" + image_markup + "<button class=\"remove-featured\">Remove</button>" );

						$( this ).val( "" );

						var post_ids = [];

						$( ".featured-post-single" ).each( function() {
							post_ids.push( $( this ).data( "featured-post-id" ) );
						} );

						$( "input[data-customize-setting-link='featured_posts']" ).attr( "value", post_ids ).trigger( "change" );
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
