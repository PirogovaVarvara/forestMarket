<?php
/**
 * Framework tabbed field file.
 *
 * @link https://shapedplugin.com
 * @since 1.4.0
 *
 * @package Woo_Category_Slider_Free
 * @subpackage Woo_Category_Slider_Free/admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	die;
} // Cannot access directly.

/**
 *
 * Field: tabbed
 *
 * @since 1.4.0
 * @version 1.4.0
 */
if ( ! class_exists( 'SP_WCS_Field_tabbed' ) ) {
	/**
	 *
	 * Field: 1.4.0
	 *
	 * @since 1.4.0
	 * @version 1.4.0
	 */
	class SP_WCS_Field_tabbed extends SP_WCS_Fields {

		/**
		 * Field constructor.
		 *
		 * @param array  $field The field type.
		 * @param string $value The values of the field.
		 * @param string $unique The unique ID for the field.
		 * @param string $where To where show the output CSS.
		 * @param string $parent The parent args.
		 */
		public function __construct( $field, $value = '', $unique = '', $where = '', $parent = '' ) {
			parent::__construct( $field, $value, $unique, $where, $parent );
		}

		/**
		 * Render field
		 *
		 * @return void
		 */
		public function render() {
			$unallows = array( 'tabbed' );
			echo wp_kses_post( $this->field_before() );
			echo '<div class="spf-tabbed-nav">';
			foreach ( $this->field['tabs'] as $key => $tab ) {

				$tabbed_icon   = ( ! empty( $tab['icon'] ) ) ? $tab['icon'] : '';
				$tabbed_active = ( empty( $key ) ) ? ' class="spf-tabbed-active"' : '';

				echo '<a class="spf-tab-item-' . esc_attr( $key ) . '" href="#"' . wp_kses_post( $tabbed_active ) . '>' . $tabbed_icon . wp_kses_post( $tab['title'] ) . '</a>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- intentionally not escaped to support html tags.
			}
			echo '</div>';

			echo '<div class="spf-tabbed-sections">';
			foreach ( $this->field['tabs'] as $key => $tab ) {

				$tabbed_hidden = ( ! empty( $key ) ) ? ' hidden' : '';
				echo '<div class="spf-tabbed-section' . esc_attr( $tabbed_hidden ) . '">';

				foreach ( $tab['fields'] as $field ) {
					if ( in_array( $field['type'], $unallows ) ) {
						$field['_notice'] = true;
					}
					$field_id      = ( isset( $field['id'] ) ) ? $field['id'] : '';
					$field_default = ( isset( $field['default'] ) ) ? $field['default'] : '';
					$field_value   = ( isset( $this->value[ $field_id ] ) ) ? $this->value[ $field_id ] : $field_default;
					$unique_id     = ( ! empty( $this->unique ) ) ? $this->unique : '';

					SP_WCS::field( $field, $field_value, $unique_id, 'field/tabbed' );
				}
				echo '</div>';
			}
			echo '</div>';
			echo wp_kses_post( $this->field_after() );
		}
	}
}
