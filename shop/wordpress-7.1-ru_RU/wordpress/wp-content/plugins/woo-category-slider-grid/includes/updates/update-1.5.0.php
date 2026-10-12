<?php

/**
 * Update page.
 *
 * @link       https://shapedplugin.com/
 * @since      1.5.0
 *
 * @package    Woo_Category_Slider
 * @subpackage Woo_Category_Slider/includes
 * @author     ShapedPlugin <support@shapedplugin.com>
 */

if ( ! defined( 'ABSPATH' ) ) {
	die; // Cannot access directly.
}

// Update version.
update_option( 'woo_category_slider_version', '1.5.0' );
update_option( 'woo_category_slider_db_version', '1.5.0' );

/**
 * Category slider query for id.
 */
$args       = new WP_Query(
	array(
		'post_type'      => 'sp_wcslider',
		'post_status'    => 'any',
		'posts_per_page' => '3000',
	)
);
$slider_ids = wp_list_pluck( $args->posts, 'ID' );

/**
 * Update metabox data along with previous data.
 */
if ( count( $slider_ids ) > 0 ) {
	foreach ( $slider_ids as $slider_id ) {
		$shortcode_meta = get_post_meta( $slider_id, 'sp_wcsp_shortcode_options', true );
		if ( ! is_array( $shortcode_meta ) ) {
			continue;
		}

		$old_layout_preset = isset( $shortcode_meta['wcsp_layout_presets'] ) ? $shortcode_meta['wcsp_layout_presets'] : '';
		if ( 'slider' === $old_layout_preset ) {
			$shortcode_meta['wcsp_layout_presets'] = 'carousel';
		}

		$make_it_card_style = isset( $shortcode_meta['wcsp_make_it_card_style'] ) ? $shortcode_meta['wcsp_make_it_card_style'] : '';
		if ( $make_it_card_style ) {
			$old_cat_border                          = isset( $shortcode_meta['wcsp_cat_border']['style'] ) ? $shortcode_meta['wcsp_cat_border'] : '';
			$shortcode_meta['wcsp_cat_border_style'] = array(
				'style'       => $old_cat_border['style'],
				'color'       => $old_cat_border['color'],
				'hover_color' => $old_cat_border['hover_color'],
			);
		}

		// Use database updater for thumbnail border & box-shadow options.
		$old_border_and_shadow                        = isset( $shortcode_meta['wcsp_cat_border_box_shadow'] ) ? $shortcode_meta['wcsp_cat_border_box_shadow'] : '';
		$old_thumb_border                             = isset( $shortcode_meta['wcsp_cat_thumb_border'] ) ? $shortcode_meta['wcsp_cat_thumb_border'] : '';
		$shortcode_meta['wcsp_category_thumb_border'] = false;
		if ( is_array( $old_border_and_shadow ) && in_array( 'border', $old_border_and_shadow, true ) ) {
			$shortcode_meta['wcsp_category_thumb_border']  = true;
			$shortcode_meta['wcsp_cat_thumb_border_style'] = array(
				'style'       => $old_thumb_border['style'],
				'color'       => $old_thumb_border['color'],
				'hover_color' => $old_thumb_border['hover_color'],
			);
		}
		$shortcode_meta['wcsp_cat_grayscale'] = 'normal';

		$auto_play_speed                              = isset( $shortcode_meta['wcsp_auto_play_speed']['all'] ) ? $shortcode_meta['wcsp_auto_play_speed']['all'] : 3000;
		$shortcode_meta['wcsp_auto_play_speed']       = $auto_play_speed;
		$standard_scroll_speed                        = isset( $shortcode_meta['wcsp_standard_scroll_speed']['all'] ) ? $shortcode_meta['wcsp_standard_scroll_speed']['all'] : 600;
		$shortcode_meta['wcsp_standard_scroll_speed'] = $standard_scroll_speed;


		// Use database updater for the "carousel navigation" and "hide on mobile" options.
		$old_navigation_data = isset( $shortcode_meta['wcsp_navigation'] ) ? $shortcode_meta['wcsp_navigation'] : 'true';
		switch ( $old_navigation_data ) {
			case 'show':
				$shortcode_meta['wcsp_carousel_navigation']['navigation']                = '1';
				$shortcode_meta['wcsp_carousel_navigation']['navigation_hide_on_mobile'] = '0';
				break;
			case 'hide':
				$shortcode_meta['wcsp_carousel_navigation']['navigation']                = '0';
				$shortcode_meta['wcsp_carousel_navigation']['navigation_hide_on_mobile'] = '0';
				break;
			case 'hide_mobile':
				$shortcode_meta['wcsp_carousel_navigation']['navigation']                = '1';
				$shortcode_meta['wcsp_carousel_navigation']['navigation_hide_on_mobile'] = '1';
				break;
		}

		// Use database updater for the "carousel pagination" and "hide on mobile" options.
		$old_pagination_data = isset( $shortcode_meta['wcsp_pagination'] ) ? $shortcode_meta['wcsp_pagination'] : 'hide_mobile';
		switch ( $old_pagination_data ) {
			case 'show':
				$shortcode_meta['wcsp_carousel_pagination']['pagination']                = '1';
				$shortcode_meta['wcsp_carousel_pagination']['pagination_hide_on_mobile'] = '0';
				break;
			case 'hide':
				$shortcode_meta['wcsp_carousel_pagination']['pagination']                = '0';
				$shortcode_meta['wcsp_carousel_pagination']['pagination_hide_on_mobile'] = '0';
				break;
			case 'hide_mobile':
				$shortcode_meta['wcsp_carousel_pagination']['pagination']                = '1';
				$shortcode_meta['wcsp_carousel_pagination']['pagination_hide_on_mobile'] = '1';
				break;
		}

		update_post_meta( $slider_id, 'sp_wcsp_shortcode_options', $shortcode_meta );
	}
}
