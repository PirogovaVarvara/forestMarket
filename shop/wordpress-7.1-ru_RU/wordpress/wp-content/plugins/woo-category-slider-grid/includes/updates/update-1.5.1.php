<?php
/**
 * Update page.
 *
 * @link       https://shapedplugin.com/
 * @since      1.5.1
 *
 * @package    Woo_Category_Slider
 * @subpackage Woo_Category_Slider/includes
 * @author     ShapedPlugin <support@shapedplugin.com>
 */

if ( ! defined( 'ABSPATH' ) ) {
	die; // Cannot access directly.
}

update_option( 'woo_category_slider_version', '1.5.1' );
update_option( 'woo_category_slider_db_version', '1.5.1' );

/**
 * Category slider query for id.
 */
$args = new WP_Query(
	array(
		'post_type'      => 'sp_wcslider',
		'post_status'    => 'any',
		'posts_per_page' => 300, // phpcs:ignore
	)
);

$slider_ids = wp_list_pluck( $args->posts, 'ID' );

/**
 * Update metabox data along with previous data.
 */
if ( count( $slider_ids ) > 0 ) {
	foreach ( $slider_ids as $slider_key => $slider_id ) {

		$shortcode_data = get_post_meta( $slider_id, 'sp_wcsp_shortcode_options', true );

		if ( ! is_array( $shortcode_data ) ) {
			continue;
		}

		/**
		 * Multi rows option updater.
		 */
		$wcsp_layout_presets     = isset( $shortcode_data['wcsp_layout_presets'] ) ? $shortcode_data['wcsp_layout_presets'] : 'carousel';
		$wcsp_carousel_style     = isset( $shortcode_data['wcsp_carousel_style'] ) ? $shortcode_data['wcsp_carousel_style'] : 'standard';
		$wcsp_slider_orientation = isset( $shortcode_data['wcsp_slider_orientation'] ) ? $shortcode_data['wcsp_slider_orientation'] : 'horizontal';

		// Declare empty array to store layout data.
		$layout_data = array();

		// Update main layouts.
		if ( $wcsp_layout_presets ) {
			$layout_data['wcsp_layout_presets'] = $wcsp_layout_presets;
		}

		// Update carousel layout.
		if ( 'carousel' === $wcsp_layout_presets ) {
			$layout_data['wcsp_carousel_style'] = $wcsp_carousel_style;
		}

		// Update slide layout.
		if ( 'slider' === $wcsp_layout_presets && in_array( $wcsp_slider_orientation, array( 'horizontal', 'vertical' ), true ) ) {
			$layout_data['wcsp_slider_orientation'] = $wcsp_slider_orientation;
		}

		update_post_meta( $slider_id, 'sp_wcsp_layout_options', $layout_data );
	} // End of foreach.
}
