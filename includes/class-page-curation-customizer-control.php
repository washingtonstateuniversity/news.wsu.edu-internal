<?php

namespace WSU\News\Internal\Page_Curation\Customizer;

class Customizer_Control extends \WP_Customize_Control {

	/**
	 * Enqueue jQuery Sortable and its dependencies.
	 * @access  public
	 * @since   2.0.0
	 * @return  void
	 */
	public function enqueue() {
		wp_enqueue_script( 'jquery-ui-core' );
		wp_enqueue_script( 'jquery-ui-sortable' );
	}

	/**
	 * Display list of ordered components.
	 * @access  public
	 * @since   2.0.0
	 * @return  void
	 */
	public function render_content() {
		$section_settings = $this->value();

		if ( empty( $section_settings ) ) {
			$section_settings = $this->input_attrs;
		} else {
			$section_settings = json_decode( $section_settings, true );
		}

		foreach ( get_categories() as $category ) {
			if ( isset( $section_settings[ $category->slug ] ) ) {
				$section_settings[ $category->slug ]['name'] = $category->cat_name;
			} else {
				$section_settings[ $category->slug ] = array(
					'name' => $category->cat_name,
				);
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
				$settings = $this->_apply_unset_defaults( $settings );
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

	private function _apply_unset_defaults( $setting ) {
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
