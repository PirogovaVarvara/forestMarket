<?php
/**
 * Framework box-shadow field.
 *
 * @link       https://shapedplugin.com/
 * @since      1.0.0
 *
 * @package    Woo_Category_Slider
 * @subpackage Woo_Category_Slider/admin/partials/section/settings
 * @author     ShapedPlugin <support@shapedplugin.com>
 */

if ( ! defined( 'ABSPATH' ) ) {
	die;
} // Cannot access directly.

if ( ! class_exists( 'SP_WCS_Field_box_shadow' ) ) {
	/**
	 *
	 * Field: Box Shadow
	 *
	 * @since 2.0
	 * @version 2.0
	 */
	class SP_WCS_Field_box_shadow extends SP_WCS_Fields {

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
		 * Render
		 *
		 * @return void
		 */
		public function render() {

			$args = wp_parse_args(
				$this->field,
				array(
					'vertical_icon'          => '<i class="fa fa-arrows-v"></i>',
					'horizontal_icon'        => '<i class="fa fa-arrows-h"></i>',
					'vertical_placeholder'   => 'v-offset',
					'horizontal_placeholder' => 'h-offset',
					'blur_placeholder'       => 'blur',
					'spread_placeholder'     => 'spread',
					'vertical_offset'        => esc_html__( 'X offset', 'woo-category-slider-grid' ),
					'horizontal_offset'      => esc_html__( 'Y offset', 'woo-category-slider-grid' ),
					'blur_offset'            => 'Blur',
					'spread_offset'          => 'Spread',
					'vertical'               => true,
					'horizontal'             => true,
					'blur'                   => true,
					'spread'                 => true,
					'color'                  => true,
					'hover_color'            => false,
					'style'                  => true,
					'unit'                   => 'px',
				)
			);

			$default_value = array(
				'vertical'    => '0',
				'horizontal'  => '0',
				'blur'        => '0',
				'spread'      => '0',
				'color'       => '',
				'hover_color' => '',
				'style'       => 'outset',
			);

			$default_value = ( ! empty( $this->field['default'] ) ) ? wp_parse_args( $this->field['default'], $default_value ) : $default_value;
			$value         = wp_parse_args( $this->value, $default_value );
			echo wp_kses_post( $this->field_before() );

			$properties = array();
			foreach ( array( 'vertical', 'horizontal', 'blur', 'spread' ) as $prop ) {
				if ( ! empty( $args[ $prop ] ) ) {
					$properties[] = $prop;
				}
			}

			foreach ( $properties as $property ) {

				$placeholder = ( ! empty( $args[ $property . '_placeholder' ] ) ) ? ' placeholder="' . $args[ $property . '_placeholder' ] . '"' : '';

				echo '<div class="spf--left spf--input">';
				echo ( ! empty( $args[ $property . '_offset' ] ) ) ? '<div class="spf--title">' . wp_kses_post( $args[ $property . '_offset' ] ) . '</div>' : '';
				echo ( ! empty( $args[ $property . '_icon' ] ) ) ? '<span class="spf--label spf--label-icon">' . wp_kses_post( $args[ $property . '_icon' ] ) . '</span>' : '';
				echo '<input type="number" name="' . esc_attr( $this->field_name( '[' . $property . ']' ) ) . '" value="' . esc_attr( $value[ $property ] ) . '"' . wp_kses_post( $placeholder ) . ' class="spf-number" />';
				echo ( ! empty( $args['unit'] ) ) ? '<span class="spf--label spf--label-unit">' . esc_attr( $args['unit'] ) . '</span>' : '';
				echo '</div>';
			}

			if ( ! empty( $args['style'] ) ) {
				echo '<div class="spf--left spf--input">';
				echo '<div class="spf--title">Type</div>';
				echo '<select name="' . esc_attr( $this->field_name( '[style]' ) ) . '">';
				foreach ( array( 'inset', 'outset' ) as $style ) {
					$selected = ( $value['style'] === $style ) ? ' selected' : '';
					echo '<option value="' . esc_attr( $style ) . '"' . esc_attr( $selected ) . '>' . esc_attr( ucfirst( $style ) ) . '</option>';
				}
				echo '</select>';
				echo '</div>';
			}

			if ( ! empty( $args['color'] ) ) {
				$default_color_attr = ( ! empty( $default_value['color'] ) ) ? ' data-default-color="' . $default_value['color'] . '"' : '';
				echo '<div class="spf--left spf-field-color">';
				echo '<div class="spf--title">Color</div>';
				echo '<input type="text" name="' . esc_attr( $this->field_name( '[color]' ) ). '" value="' . esc_attr( $value['color'] ) . '" class="spf-color"' . $default_color_attr . ' />';//phpcs:ignore 
				// default_hover_color_attr hover already escaping in the above.
				echo '</div>';
			}

			if ( ! empty( $args['hover_color'] ) ) {
				$default_hover_color_attr = ( ! empty( $default_value['hover_color'] ) ) ? ' data-default-color="' . esc_attr( $default_value['hover_color'] ) . '"' : '';
				echo '<div class="spf--left spf-field-color">';
				echo '<div class="spf--title">Hover Color</div>';
				echo '<input type="text" name="' . esc_attr( $this->field_name( '[hover_color]' ) ) . '" value="' . esc_attr( $value['hover_color'] ) . '" class="spf-color"' . $default_hover_color_attr . ' />';//phpcs:ignore 
				// default_hover_color_attr hover already escaping in the above.
				echo '</div>';
			}
			echo '<div class="clear"></div>';
			echo wp_kses_post( $this->field_after() );
		}
	}
}
