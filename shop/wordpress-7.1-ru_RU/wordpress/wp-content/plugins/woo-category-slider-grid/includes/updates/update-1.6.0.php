<?php
/**
 * Update page.
 *
 * @link       https://shapedplugin.com/
 * @since      1.6.0
 *
 * @package    Woo_Category_Slider
 * @subpackage Woo_Category_Slider/includes
 * @author     ShapedPlugin <support@shapedplugin.com>
 */

if ( ! defined( 'ABSPATH' ) ) {
	die; // Cannot access directly.
}

update_option( 'woo_category_slider_version', '1.6.0' );
update_option( 'woo_category_slider_db_version', '1.6.0' );

/**
 * Category slider query for id.
 */
$args = new WP_Query(
	array(
		'post_type'      => 'sp_wcslider',
		'post_status'    => 'any',
		'posts_per_page' => 500, // phpcs:ignore
	)
);

$slider_ids = wp_list_pluck( $args->posts, 'ID' );

/**
 * Update metabox data along with previous data.
 */
if ( count( $slider_ids ) > 0 ) {
	foreach ( $slider_ids as $slider_key => $slider_id ) {

		$layout_data    = get_post_meta( $slider_id, 'sp_wcsp_layout_options', true );
		$shortcode_meta = get_post_meta( $slider_id, 'sp_wcsp_shortcode_options', true );

		if ( ! is_array( $shortcode_meta ) ) {
			continue;
		}

		$wcsp_layout_presets     = isset( $layout_data['wcsp_layout_presets'] ) ? $layout_data['wcsp_layout_presets'] : 'carousel';
		$wcsp_slider_orientation = isset( $layout_data['wcsp_slider_orientation'] ) ? $layout_data['wcsp_slider_orientation'] : 'horizontal';

		// Update slide layout.
		if ( 'slider' === $wcsp_layout_presets && in_array( $wcsp_slider_orientation, array( 'horizontal', 'vertical' ), true ) ) {
			$shortcode_meta['wcsp_slider_orientation'] = $wcsp_slider_orientation;
		}

		if ( empty( $shortcode_meta['wcsp_make_it_card_style'] ) ) {
			$shortcode_meta['wcsp_cat_background'] = array(
				'background'       => 'transparent',
				'hover_background' => 'transparent',
			);
		}
		$shortcode_meta['wcsp_slide_border'] = array(
			'all'    => 0,
			'style'  => 'solid',
			'color'  => '#dddddd',
			'radius' => 0,
		);

		update_post_meta( $slider_id, 'sp_wcsp_shortcode_options', $shortcode_meta );
	} // End of foreach.
}

// Clear transient cache for the data of rcommended plugins.
if ( get_transient( 'spwoocs_plugins' ) ) {
	delete_transient( 'spwoocs_plugins' );
}
