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
		$post_ids = $this->value();

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

		<script>
			jQuery( function() {
				$( ".featured-post-input" ).autocomplete( {
					source: "<?php echo esc_js( get_rest_url( get_current_blog_id(), '/insider/v1/featured' ) ); ?>",
					minLength: 2,
					select: function( event, ui ) {
						event.preventDefault();
						jQuery( this ).val( ui.item.label );
						// add value to hidden input / order
					},
					focus: function( event, ui ) {
						event.preventDefault();
						jQuery( this ).val( ui.item.label );
						// add value to hidden input / order
					}
				} );
			} );
		</script>

		<div class="featured-posts-selection">
			<?php
			for ( $i = 0; $i <= 4; $i++ ) {
				if ( isset( $post_ids[ $i ] ) ) {
					$value = get_the_title( $post_ids[ $i ] );
				} else {
					$value = '';
				}
				?>
				<div class="featured-post-single">
					<label for="featured-post-<?php echo esc_attr( $i ); ?>">Feature <?php echo esc_html( $i + 1 ); ?></label>
					<input id="featured-post-<?php echo esc_attr( $i ); ?>" class="featured-post-input" type="text" value="<?php echo esc_html( $value ); ?>" />
				</div>

				<?php
			}

			?>
		</div>
		<input type="hidden" <?php $this->link(); ?> value="<?php echo wp_json_encode( $post_ids ); ?>"/>
		<?php
	}
}
