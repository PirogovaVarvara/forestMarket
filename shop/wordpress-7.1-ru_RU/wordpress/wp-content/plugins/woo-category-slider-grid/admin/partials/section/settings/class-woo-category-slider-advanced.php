<?php
/**
 * Advanced settings section in settings page.
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
 * This class is responsible for Advanced Settings in Settings page.
 *
 * @since 1.1.0
 */
class SP_WCS_Advanced {
	/**
	 * Settings section.
	 *
	 * @param string $prefix advance section prefix.
	 * @return void
	 */
	public static function section( $prefix ) {

		SP_WCS::createSection(
			$prefix,
			array(
				'title'  => 'Advanced Controls',
				'icon'   => 'fa wcsp-icon-advanced',
				'fields' => array(
					array(
						'id'         => 'wcsp_delete_all_data',
						'type'       => 'checkbox',
						'title'      => esc_html__( 'Clean-up Data on Deletion', 'woo-category-slider-grid' ),
						'title_help' => esc_html__( 'Check this box if you would like Reno Product Category to completely remove all of its data when the plugin is deleted.', 'woo-category-slider-grid' ),
						'default'    => false,
					),
					array(
						'type'    => 'subheading',
						'content' => esc_html__( 'Control Assets (Styles & Scripts)', 'woo-category-slider-grid' ),
					),
					array(
						'id'         => 'wcsp_swiper_css',
						'type'       => 'switcher',
						'title'      => esc_html__( 'Swiper CSS', 'woo-category-slider-grid' ),
						'text_on'    => esc_html__( 'Enqueue', 'woo-category-slider-grid' ),
						'text_off'   => esc_html__( 'Dequeue', 'woo-category-slider-grid' ),
						'text_width' => 95,
						'default'    => true,
					),
					array(
						'id'         => 'wcsp_fa_css',
						'type'       => 'switcher',
						'title'      => esc_html__( 'Font Awesome CSS', 'woo-category-slider-grid' ),
						'text_on'    => esc_html__( 'Enqueue', 'woo-category-slider-grid' ),
						'text_off'   => esc_html__( 'Dequeue', 'woo-category-slider-grid' ),
						'text_width' => 95,
						'default'    => true,
					),
					array(
						'id'         => 'wcsp_swiper_js',
						'type'       => 'switcher',
						'title'      => esc_html__( 'Swiper JS', 'woo-category-slider-grid' ),
						'text_on'    => esc_html__( 'Enqueue', 'woo-category-slider-grid' ),
						'text_off'   => esc_html__( 'Dequeue', 'woo-category-slider-grid' ),
						'text_width' => 95,
						'default'    => true,
					),
				),
			)
		);
	}
}
