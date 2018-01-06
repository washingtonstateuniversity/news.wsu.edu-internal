<?php
/**
 * The custom Customizer Control used to manage the order and
 * options for the display of category sections on a page.
 *
 * Thanks to the Homepage Control plugin for providing a good
 * guide for the initial implementation. Some code here may be
 * from that GPLv3 licensed project.
 *
 * https://github.com/woocommerce/homepage-control
 */

namespace WSU\News\Internal\Page_Curation\Customizer;

class Customizer_Control extends \WP_Customize_Control {

	/**
	 * Enqueue jQuery Sortable and its dependencies.
	 *
	 * @since   0.5.0
	 */
	public function enqueue() {
		wp_enqueue_script( 'jquery-ui-core' );
		wp_enqueue_script( 'jquery-ui-sortable' );
	}

	/**
	 * Output the inputs used to sort and capture data about the
	 * display of category sections.
	 *
	 * @since   0.5.0
	 */
	public function render_content() {
		$section_settings = $this->value();

		if ( empty( $section_settings ) ) {
			$section_settings = $this->input_attrs;
		} else {
			$section_settings = json_decode( $section_settings, true );
		}

		foreach ( get_categories( array(
			'hide_empty' => false,
		) ) as $category ) {
			if ( isset( $section_settings[ $category->slug ] ) ) {
				$section_settings[ $category->slug ]['name'] = $category->cat_name;
			} else {
				$section_settings[ $category->slug ] = array(
					'name' => $category->cat_name,
				);
			}
		}

		// Remove old categories that have been deleted.
		foreach ( $section_settings as $slug => $data ) {
			if ( ! isset( $data['name'] ) ) {
				unset( $section_settings[ $slug ] );
			}
		}

		?>
		<?php
		if ( ! empty( $this->label ) ) : ?>
			<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
		<?php endif;
		if ( ! empty( $this->description ) ) : ?>
			<span class="description customize-control-description"><?php echo esc_html( $this->description ); ?></span>
		<?php endif;
		?>

		<ul class="page-curation">
			<?php
			foreach ( $section_settings as $slug => $settings ) {
				$settings = $this->apply_unset_defaults( $settings );
				?>
				<li id="section-<?php echo esc_attr( $slug ); ?>" class="page-curation-section">
					<div class="section-title"><?php echo esc_html( $settings['name'] ); ?></div>
					<div class="section-count">
						<label>Count: <input type="text" value="<?php echo esc_attr( $settings['count'] ); ?>"></label>
					</div>
					<div class="section-classes">
						<label>Classes: <input type="text" value="<?php echo esc_attr( $settings['classes'] ); ?>"></label>
					</div>
				</li>
				<?php
			}

			?>
		</ul>
		<input type="hidden" <?php $this->link(); ?> value="<?php echo wp_json_encode( $section_settings ); ?>"/>
		<?php
	}

	/**
	 * Apply the defaults used for each category section in the
	 * Customizer.
	 *
	 * @since 0.5.0
	 *
	 * @param array $setting The existing setting.
	 *
	 * @return array The modified setting with defaults as required.
	 */
	private function apply_unset_defaults( $setting ) {
		$defaults = array(
			'name' => 'Unknown',
			'count' => 4,
			'classes' => 'bottom-divider',
		);

		if ( ! is_array( $setting ) ) {
			$setting = $defaults;
		} else {
			$setting = wp_parse_args( $setting, $defaults );
		}

		return $setting;
	}
}
