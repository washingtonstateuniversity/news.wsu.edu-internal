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
		//$section_settings = json_decode( $this->value(), true );
		$section_settings = $this->value();

		if ( empty( $section_settings ) ) {
			$section_settings = json_decode( $this->input_attrs, true );
		}
		//$section_settings = explode( ',', $this->value() );


		?>
		<?php
		if ( ! empty( $this->label ) ) : ?>
			<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
		<?php endif;
		if ( ! empty( $this->description ) ) : ?>
			<span class="description customize-control-description"><?php echo $this->description ; ?></span>
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
		<input type="hidden" <?php $this->link(); ?> value="<?php echo esc_attr( $this->value() ); ?>"/>
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

	/**
	 * Re-order the components in the given array, based on the stored order.
	 * @access  private
	 * @since   1.0.0
	 * @return  array An array of components, in the correct order.
	 */
	private function _reorder_components ( $components, $order ) {
		$order_entries = array();
		if ( '' != $order ) {
			$order_entries = explode( ',', $order );
		}

		// Re-order the components according to the stored order.
		if ( 0 < count( $order_entries ) ) {
			$original_components = $components; // Make a backup before we overwrite.
			$components = array();
			foreach ( $order_entries as $k => $v ) {
				if ( $this->_is_component_disabled( $v ) ) {
					$v = str_replace( '[disabled]', '', $v );
				}

				// Only add to array if component still exists
				if ( isset( $original_components[ $v ] ) ) {
					$components[ $v ] = $original_components[ $v ];
					unset( $original_components[ $v ] );
				}
			}
			if ( 0 < count( $original_components ) ) {
				$components = array_merge( $components, $original_components );
			}
		}

		return $components;
	} // End _reorder_components()

	/**
	 * Check if a component is disabled.
	 * @access  private
	 * @since   2.0.0
	 * @return  boolean True if a component if disabled.
	 */
	private function _is_component_disabled ( $component ) {
		if ( false !== strpos( $component, '[disabled]' ) ) {
			return true;
		}
		return false;
	}

	/**
	 * Return the disabled components in the given array, based on the format of the key.
	 * @access  private
	 * @since   2.0.0
	 * @return  array An array of disabled components.
	 */
	private function _get_disabled_components ( $saved_components, $all_components ) {
		$disabled = array();
		if ( '' != $saved_components ) {
			$saved_components = explode( ',', $saved_components );

			if ( 0 < count( $saved_components ) ) {
				foreach ( $saved_components as $k => $v ) {
					if ( $this->_is_component_disabled( $v ) ) {
						$v = str_replace( '[disabled]', '', $v );
						$disabled[] = $v;
					}
					unset( $all_components[ $v ] );
				}
			}

			// Disable new components
			if ( 0 < count( $all_components ) ) {
				foreach ( $all_components as $k => $v ) {
					$disabled[] = $k;
				}
			}
		}
		return $disabled;
	}
}
