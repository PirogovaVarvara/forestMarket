<?php

/**
 * Framework border field file.
 *
 * @link       https://shapedplugin.com/
 * @since      1.0.0
 *
 * @package    Woo_Category_Slider
 * @subpackage Woo_Category_Slider/admin/partials/section/settings
 * @author     ShapedPlugin <support@shapedplugin.com>
 */

if ( ! defined( 'ABSPATH' ) ) {
	die; } // Cannot access directly.

if ( ! class_exists( 'SP_WCS_Field_border' ) ) {
	/**
	 *
	 * Field: border
	 *
	 * @since 1.0.0
	 * @version 1.0.0
	 */
	class SP_WCS_Field_border extends SP_WCS_Fields {
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

			$args = wp_parse_args(
				$this->field,
				array(
					'top_icon'           => '<i class="fa fa-long-arrow-up"></i>',
					'left_icon'          => '<i class="fa fa-long-arrow-left"></i>',
					'bottom_icon'        => '<i class="fa fa-long-arrow-down"></i>',
					'right_icon'         => '<i class="fa fa-long-arrow-right"></i>',
					'all_icon'           => '<i class="fa fa-arrows"></i>',
					'radius_icon'        => '<i class="wcsp-icon-radius-01"></i>',
					'top_placeholder'    => esc_html__( 'top', 'woo-category-slider-grid' ),
					'right_placeholder'  => esc_html__( 'right', 'woo-category-slider-grid' ),
					'bottom_placeholder' => esc_html__( 'bottom', 'woo-category-slider-grid' ),
					'left_placeholder'   => esc_html__( 'left', 'woo-category-slider-grid' ),
					'all_placeholder'    => esc_html__( 'all', 'woo-category-slider-grid' ),
					'top'                => true,
					'left'               => true,
					'bottom'             => true,
					'right'              => true,
					'all'                => false,
					'color'              => true,
					'hover_color'        => false,
					'style'              => true,
					'radius'             => false,
					'unit'               => 'px',
				)
			);

			$default_value = array(
				'top'         => '',
				'right'       => '',
				'bottom'      => '',
				'left'        => '',
				'color'       => '',
				'hover_color' => '',
				'style'       => 'solid',
				'all'         => '',
			);

			$border_props = array(
				'solid'  => esc_html__( 'Solid', 'woo-category-slider-grid' ),
				'dashed' => esc_html__( 'Dashed', 'woo-category-slider-grid' ),
				'dotted' => esc_html__( 'Dotted', 'woo-category-slider-grid' ),
				'double' => esc_html__( 'Double', 'woo-category-slider-grid' ),
				'inset'  => esc_html__( 'Inset', 'woo-category-slider-grid' ),
				'outset' => esc_html__( 'Outset', 'woo-category-slider-grid' ),
				'groove' => esc_html__( 'Groove', 'woo-category-slider-grid' ),
				'ridge'  => esc_html__( 'ridge', 'woo-category-slider-grid' ),
				'none'   => esc_html__( 'None', 'woo-category-slider-grid' ),
			);

			$default_value = ( ! empty( $this->field['default'] ) ) ? wp_parse_args( $this->field['default'], $default_value ) : $default_value;
			$value         = wp_parse_args( $this->value, $default_value );
			echo wp_kses_post( $this->field_before() );

			if ( ! empty( $args['all'] ) ) {
				$placeholder = ( ! empty( $args['all_placeholder'] ) ) ? ' placeholder="' . esc_attr( $args['all_placeholder'] ) . '"' : '';
				echo '<div class="spf--left spf--input">';
				echo '<div class="spf--title">' . esc_html( __( 'Width', 'woo-category-slider-grid' ) ) . '</div>';
				echo ( ! empty( $args['all_icon'] ) ) ? '<span class="spf--label spf--label-icon">' . wp_kses_post( $args['all_icon'] ) . '</span>' : '';
				echo '<input type="number" name="' . esc_attr( $this->field_name( '[all]' ) ) . '" value="' . esc_attr( $value['all'] ) . '"' . $placeholder . ' class="spf-number" />'; // phpcs:ignore
				echo ( ! empty( $args['unit'] ) ) ? '<span class="spf--label spf--label-unit">' . esc_html( $args['unit'] ) . '</span>' : '';
				echo '</div>';
			} else {
				$properties = array();
				foreach ( array( 'top', 'right', 'bottom', 'left' ) as $prop ) {
					if ( ! empty( $args[ $prop ] ) ) {
						$properties[] = $prop;
					}
				}

				$properties = ( array( 'right', 'left' ) === $properties ) ? array_reverse( $properties ) : $properties;
				foreach ( $properties as $property ) {

					$placeholder = ( ! empty( $args[ $property . '_placeholder' ] ) ) ? ' placeholder="' . $args[ $property . '_placeholder' ] . '"' : '';
					echo '<div class="spf--left spf--input">';
					echo '<div class="spf--title">' . esc_html( __( 'Width', 'woo-category-slider-grid' ) ) . '</div>';
					echo ( ! empty( $args[ $property . '_icon' ] ) ) ? '<span class="spf--label spf--label-icon">' . wp_kses_post( $args[ $property . '_icon' ] ) . '</span>' : '';
					echo '<input type="number" name="' . esc_attr( $this->field_name( '[' . $property . ']' ) ) . '" value="' . esc_attr( $value[ $property ] ) . '"' . $placeholder . ' class="spf-number" />'; // phpcs:ignore
					echo ( ! empty( $args['unit'] ) ) ? '<span class="spf--label spf--label-unit">' . esc_html( $args['unit'] ) . '</span>' : '';
					echo '</div>';
				}
			}

			if ( ! empty( $args['style'] ) ) {
				echo '<div class="spf--left spf--input">';
				echo '<div class="spf--title">' . esc_html( __( 'Style', 'woo-category-slider-grid' ) ) . '</div>';
				echo '<select name="' . esc_attr( $this->field_name( '[style]' ) ) . '">';
				foreach ( $border_props as $border_prop_key => $border_prop_value ) {
					$selected = ( $value['style'] === $border_prop_key ) ? ' selected' : '';
					echo '<option value="' . esc_attr( $border_prop_key ) . '"' . esc_attr( $selected ) . '>' . wp_kses_post( $border_prop_value ) . '</option>';
				}
				echo '</select>';
				echo '</div>';
			}

			if ( ! empty( $args['color'] ) ) {
				$default_color_attr = ( ! empty( $default_value['color'] ) ) ? ' data-default-color="' . esc_attr( $default_value['color'] ) . '"' : '';
				echo '<div class="spf--left spf-field-color">';
				echo '<div class="spf--title">Color</div>';
				echo '<input type="text" name="' . esc_attr( $this->field_name( '[color]' ) ) . '" value="' . esc_attr( $value['color'] ) . '" class="spf-color"' . $default_color_attr . ' />';// phpcs:ignore
				echo '</div>';
			}

			if ( ! empty( $args['hover_color'] ) ) {
				$default_hover_color_attr = ( ! empty( $default_value['hover_color'] ) ) ? ' data-default-color="' . esc_attr( $default_value['hover_color'] ) . '"' : '';
				echo '<div class="spf--left spf-field-color">';
				echo '<div class="spf--title">Hover Color</div>';
				echo '<input type="text" name="' . esc_attr( $this->field_name( '[hover_color]' ) ) . '" value="' . esc_attr( $value['hover_color'] ) . '" class="spf-color"' . $default_hover_color_attr . ' />';// phpcs:ignore
				echo '</div>';
			}

			if ( ! empty( $args['radius'] ) ) {
				$placeholder = ( ! empty( $args['all_placeholder'] ) ) ? ' placeholder="' . $args['all_placeholder'] . '"' : '';

				echo '<div class="spf--left spf--input">';
				echo '<div class="spf--title">Radius</div>';
				echo ( ! empty( $args['radius_icon'] ) ) ? '<span class="spf--label spf--label-icon">' . wp_kses_post( $args['radius_icon'] ) . '</span>' : '';
				echo '<input type="number" name="' . esc_attr( $this->field_name( '[radius]' ) ) . '" value="' . esc_attr( $value['radius'] ) . '"' . wp_kses_post( $placeholder ) . $this->field_attributes() . ' class="spf-number" />'; // phpcs:ignore -- outputs safely 
				echo ( ! empty( $args['radius'] ) ) ? '<span class="spf--label spf--label-unit">' . esc_attr( $args['unit'] ) . '</span>' : '';
				echo '</div>';
			}

			echo '<div class="clear"></div>';
			echo wp_kses_post( $this->field_after() );
		}
	}
}
