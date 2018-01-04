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

		if ( ! empty( $this->label ) ) : ?>
			<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
		<?php endif;
		if ( ! empty( $this->description ) ) : ?>
			<span class="description customize-control-description"><?php echo esc_html( $this->description ); ?></span>
		<?php endif;
		?>

		<!-- These styles were copied directly from WordPress core and may need to be adjusted -->
		<style>
			input[type="text"].ui-autocomplete-loading {
				background-image: url(../images/loading.gif); /* This definitely needs to be adjusted */
				background-repeat: no-repeat;
				background-position: right center;
				visibility: visible;
			}

			input.ui-autocomplete-input.open {
				border-bottom-color: transparent;
			}

			.ui-autocomplete {
				z-index: 500110;
				padding: 0;
				margin: 0;
				list-style: none;
				position: absolute;
				border: 1px solid #5b9dd9;
				box-shadow: 0 1px 2px rgba( 30, 140, 190, 0.8 );
				background-color: #fff;
			}

			.ui-autocomplete li {
				margin-bottom: 0;
				padding: 4px 10px;
				white-space: nowrap;
				text-align: left;
				cursor: pointer;
			}

			/* Colors for the wplink toolbar autocomplete. */
			.ui-autocomplete .ui-state-focus {
				background-color: #ddd;
			}
		</style>

		<div class="featured-post-selection">
			<label for="featured-post-title">Find featured story</label>
			<input id="featured-post-title" type="text" value="" />
		</div>

		<div class="selected-featured-posts">
			<?php
			for ( $i = 0; $i <= 4; $i++ ) {
				if ( isset( $post_ids[ $i ] ) ) {
					?>
					<div class="featured-post-single" data-featured-post-id="<?php echo esc_attr( $post_ids[ $i ] ); ?>">
						<p><?php echo esc_html( get_the_title( $post_ids[ $i ] ) ); ?></p>
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
			jQuery( function() {
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
						next_slot.html( "<p>" + ui.item.label + "</p>" );

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
