<?php
/**
 * Style settings section in settings page.
 *
 * @link       https://shapedplugin.com/
 * @since      1.0.0
 *
 * @package    Woo_Category_Slider
 * @subpackage Woo_Category_Slider/admin/partials/section/settings
 * @author     ShapedPlugin <support@shapedplugin.com>
 */

// Cannot access directly.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * This class is responsible for Style Settings in Settings page.
 *
 * @since 1.1.0
 */
class SP_WCS_Style {
	/**
	 * Settings section.
	 *
	 * @param string $prefix Settings section prefix.
	 * @return void
	 */
	public static function section( $prefix ) {

		SP_WCS::createSection(
			$prefix,
			array(
				'id'     => 'custom_css_section',
				'title'  => esc_html__( 'Additional CSS & JS', 'woo-category-slider-grid' ),
				'icon'   => 'fa wcsp-icon-code',
				'fields' => array(
					array(
						'id'       => 'wcsp_custom_css',
						'type'     => 'code_editor',
						'title'    => esc_html__( 'Custom CSS', 'woo-category-slider-grid' ),
						'settings' => array(
							'icon'  => 'fa fa-sliders',
							'theme' => 'default',
							'mode'  => 'css',
						),
					),
					array(
						'id'       => 'custom_js',
						'type'     => 'code_editor',
						'title'    => esc_html__( 'Custom JS', 'woo-category-slider-grid' ),
						'settings' => array(
							'theme' => 'default',
							'mode'  => 'javascript',
						),
					),
				),
			)
		);
	}
}
