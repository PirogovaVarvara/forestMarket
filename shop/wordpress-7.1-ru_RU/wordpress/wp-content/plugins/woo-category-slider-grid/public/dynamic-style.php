<?php
/**
 * Dynamic style for the plugin
 *
 * @link       https://shapedplugin.com/
 * @since      1.0.0
 *
 * @package    Woo_Category_Slider
 * @subpackage Woo_Category_Slider/public
 */

if ( ! defined( 'ABSPATH' ) ) {
	die; // Cannot access directly.
}

$cat_padding  = isset( $shortcode_meta['wcsp_cat_padding'] ) ? $shortcode_meta['wcsp_cat_padding'] : '';
$wcsp_options = get_option( 'sp_wcsp_settings' );

$layout_preset   = isset( $layout_meta['wcsp_layout_presets'] ) ? $layout_meta['wcsp_layout_presets'] : '';
$section_title   = isset( $shortcode_meta['wcsp_section_title'] ) && $shortcode_meta['wcsp_section_title'] ? $shortcode_meta['wcsp_section_title'] : false;
$cat_item        = isset( $shortcode_meta['wcsp_slide_border'] ) ? $shortcode_meta['wcsp_slide_border'] : array();
$cat_item_border = isset( $cat_item['all'] ) ? $cat_item['all'] : 0;
$cat_item_style  = isset( $cat_item['style'] ) ? $cat_item['style'] : 'solid';
$cat_item_color  = isset( $cat_item['color'] ) ? $cat_item['color'] : '#dddddd';
$cat_item_radius = isset( $cat_item['radius'] ) ? $cat_item['radius'] : 0;

$cat_description            = isset( $shortcode_meta['wcsp_cat_description'] ) ? $shortcode_meta['wcsp_cat_description'] : '';
$cat_name                   = isset( $shortcode_meta['wcsp_cat_name'] ) ? $shortcode_meta['wcsp_cat_name'] : '';
$cat_product_count          = isset( $shortcode_meta['wcsp_cat_product_count'] ) ? $shortcode_meta['wcsp_cat_product_count'] : '';
$navigation                 = isset( $shortcode_meta['wcsp_navigation'] ) ? $shortcode_meta['wcsp_navigation'] : '';
$preloader                  = isset( $shortcode_meta['wcsp_preloader'] ) ? $shortcode_meta['wcsp_preloader'] : '';
$cat_product_count_position = isset( $shortcode_meta['wcsp_cat_product_count_position'] ) ? $shortcode_meta['wcsp_cat_product_count_position'] : '';
$cat_shop_now_button        = isset( $shortcode_meta['wcsp_cat_shop_now_button'] ) ? $shortcode_meta['wcsp_cat_shop_now_button'] : '';
$cat_shop_button_color      = isset( $shortcode_meta['wcsp_cat_shop_button_color'] ) ? $shortcode_meta['wcsp_cat_shop_button_color'] : '';
$border_box_shadow          = isset( $shortcode_meta['wcsp_cat_border_box_shadow'] ) ? $shortcode_meta['wcsp_cat_border_box_shadow'] : '';

$show_thumb_border    = isset( $shortcode_meta['wcsp_category_thumb_border'] ) ? $shortcode_meta['wcsp_category_thumb_border'] : false;
$thumb_border         = isset( $shortcode_meta['wcsp_cat_thumb_border'] ) ? $shortcode_meta['wcsp_cat_thumb_border'] : '';
$thumb_border_style   = isset( $shortcode_meta['wcsp_cat_thumb_border_style'] ) ? $shortcode_meta['wcsp_cat_thumb_border_style'] : '';
$section_title_margin = isset( $shortcode_meta['wcsp_section_title_margin'] ) ? $shortcode_meta['wcsp_section_title_margin'] : '';
$thumb_margin         = isset( $shortcode_meta['wcsp_thumb_margin'] ) ? $shortcode_meta['wcsp_thumb_margin'] : '';

// Shadow style and values.
$item_box_shadow_style   = $shortcode_meta['wcsp_box_shadow_style'] ?? '';
$item_box_shadow         = $shortcode_meta['wcsp_box_shadow'] ?? array();
$item_shadow_vertical    = $item_box_shadow['vertical'] ?? 0;
$item_shadow_horizontal  = $item_box_shadow['horizontal'] ?? 0;
$item_shadow_blur        = $item_box_shadow['blur'] ?? 0;
$item_shadow_spread      = $item_box_shadow['spread'] ?? 0;
$item_shadow_color       = $item_box_shadow['color'] ?? '#dddddd';
$item_shadow_hover_color = $item_box_shadow['hover_color'] ?? '#dddddd';
$cat_background          = $shortcode_meta['wcsp_cat_background'] ?? array(
	'background'       => 'transparent',
	'hover_background' => 'transparent',
);
// Determine box shadow value.
$shadow_value                = 'inset' === $item_box_shadow_style ? 'inset ' : '';
$item_box_shadow_value       = $shadow_value . "{$item_shadow_vertical}px {$item_shadow_horizontal}px {$item_shadow_blur}px {$item_shadow_spread}px $item_shadow_color";
$item_box_shadow_hover_value = $shadow_value . "{$item_shadow_vertical}px {$item_shadow_horizontal}px {$item_shadow_blur}px {$item_shadow_spread}px $item_shadow_hover_color";

// If box-shadow is none.
if ( 'none' === $item_box_shadow_style ) {
	$item_box_shadow_value       = 'none';
	$item_box_shadow_hover_value = 'none';
}

/**
 * Category item style.
 */
$dynamic_style .= '
	.sp-wcsp-slider-area #sp-wcsp-slider-section-' . $post_id . ' .sp-wcsp-cat-item .sp-wcsp-cat-item-thumb-content {
		border: ' . $cat_item_border . 'px ' . $cat_item_style . ' ' . $cat_item_color . ';
		border-radius: ' . $cat_item_radius . 'px;
	}
	.sp-wcsp-slider-area #sp-wcsp-slider-section-' . $post_id . ' .sp-wcsp-cat-item {
		box-shadow: ' . $item_box_shadow_value . ';
		background: ' . $cat_background['background'] . ';
	}
	.sp-wcsp-slider-area #sp-wcsp-slider-section-' . $post_id . ' .sp-wcsp-cat-item:hover{
		box-shadow: ' . $item_box_shadow_hover_value . ';
		background: ' . $cat_background['hover_background'] . ';
	}
';

// Item shadow extra margin.
if ( 'outset' === $item_box_shadow_style ) {
	$item_box_shadow_margin = isset( $item_shadow_spread ) && $item_shadow_spread > 0 ? $item_shadow_spread : $item_shadow_blur;
	$dynamic_style         .= '
		.sp-wcsp-slider-area #sp-wcsp-slider-section-' . $post_id . ' {
			padding-left: ' . $item_box_shadow_margin . 'px;
			padding-right: ' . $item_box_shadow_margin . 'px;
		}
	';
}

if ( $section_title ) {
	$section_title_color = isset( $shortcode_meta['wpsp_section_title_typography']['color'] ) ? $shortcode_meta['wpsp_section_title_typography']['color'] : '#444444';

	$section_title_margin_bottom = $section_title_margin['bottom'];
	if ( 'show' === $navigation && ( 'carousel' === $layout_preset || 'slider' === $layout_preset ) ) {
		$section_title_margin_bottom = $section_title_margin['bottom'] - 50;
	}
	$dynamic_style .= '.sp-wcsp-slider-area.sp-wcsp-slider-area-' . $post_id . ' .sp-wcsp-section-title {
		margin: ' . $section_title_margin['top'] . $section_title_margin['unit'] . ' ' . $section_title_margin['right'] . $section_title_margin['unit'] . ' ' . $section_title_margin_bottom . $section_title_margin['unit'] . ' ' . $section_title_margin['left'] . $section_title_margin['unit'] . ';
		color: ' . $section_title_color . ';
		font-size: 20px;
		line-height: 20px;
		letter-spacing: 0;
		text-transform: none;
		text-align: left;
		font-weight: 600;
	}';
}

if ( $cat_description ) {
	$description_margin = isset( $shortcode_meta['wcsp_description_margin'] ) ? $shortcode_meta['wcsp_description_margin'] : '';
	$description_color  = isset( $shortcode_meta['wcsp_description_typography']['color'] ) ? $shortcode_meta['wcsp_description_typography']['color'] : '#444444';
	$dynamic_style     .= '.sp-wcsp-slider-area #sp-wcsp-slider-section-' . $post_id . ' .sp-wcsp-cat-item .sp-wcsp-cat-details .sp-wcsp-cat-desc {
		margin: ' . $description_margin['top'] . $description_margin['unit'] . ' ' . $description_margin['right'] . $description_margin['unit'] . ' ' . $description_margin['bottom'] . $description_margin['unit'] . ' ' . $description_margin['left'] . $description_margin['unit'] . ';
		color: ' . $description_color . ';
		font-size: 14px;
		line-height: 18px;
		letter-spacing: 0;
		text-transform: none;
		text-align: center;
		padding: 0;
		font-weight: 400;
		font-style: normal;
	}';
}

if ( $cat_name ) {
	$cat_name_margin      = isset( $shortcode_meta['wcsp_cat_name_margin'] ) ? $shortcode_meta['wcsp_cat_name_margin'] : '';
	$product_count_margin = isset( $shortcode_meta['wcsp_product_count_margin'] ) ? $shortcode_meta['wcsp_product_count_margin'] : '';
	$cat_name_color       = isset( $shortcode_meta['wcsp_cat_name_typography']['color'] ) ? $shortcode_meta['wcsp_cat_name_typography']['color'] : '#444444';
	$cat_name_hover_color = isset( $shortcode_meta['wcsp_cat_name_typography']['hover-color'] ) ? $shortcode_meta['wcsp_cat_name_typography']['hover-color'] : '#444444';

	$dynamic_style .= '.sp-wcsp-slider-area #sp-wcsp-slider-section-' . $post_id . ' .sp-wcsp-cat-item .sp-wcsp-cat-details .sp-wcsp-cat-details-content .sp-wcsp-cat-name {
		text-align: center;
	}
	.sp-wcsp-slider-area #sp-wcsp-slider-section-' . $post_id . ' .sp-wcsp-cat-item .sp-wcsp-cat-details .sp-wcsp-cat-details-content .sp-wcsp-cat-name a {
		margin: ' . $cat_name_margin['top'] . $cat_name_margin['unit'] . ' ' . $cat_name_margin['right'] . $cat_name_margin['unit'] . ' ' . $cat_name_margin['bottom'] . $cat_name_margin['unit'] . ' ' . $cat_name_margin['left'] . $cat_name_margin['unit'] . ';
		color: ' . $cat_name_color . ';
		font-size: 16px;
		line-height: 18px;
		letter-spacing: 0;
		text-transform: none;
		text-align: center;
		font-weight: 700;
		font-style: normal;
	}
	.sp-wcsp-slider-area #sp-wcsp-slider-section-' . $post_id . ' .sp-wcsp-cat-item .sp-wcsp-cat-details .sp-wcsp-cat-details-content .sp-wcsp-cat-name a:hover{
		color: ' . $cat_name_hover_color . ';
	}';
	if ( $cat_product_count && 'under_cat' === $cat_product_count_position ) {
		$product_count_color = isset( $shortcode_meta['wcsp_product_count_typography']['color'] ) ? $shortcode_meta['wcsp_product_count_typography']['color'] : '#777777';

		$dynamic_style .= '.sp-wcsp-slider-area #sp-wcsp-slider-section-' . $post_id . ' .sp-wcsp-cat-item .sp-wcsp-cat-details .sp-wcsp-cat-details-content .sp-wcsp-product-count {
			margin: ' . $product_count_margin['top'] . $product_count_margin['unit'] . ' ' . $product_count_margin['right'] . $product_count_margin['unit'] . ' ' . $product_count_margin['bottom'] . $product_count_margin['unit'] . ' ' . $product_count_margin['left'] . $product_count_margin['unit'] . ';
			color: ' . $product_count_color . ';
			font-size: 14px;
			line-height: 20px;
			letter-spacing: 0;
			text-transform: none;
			text-align: center;
			font-weight: 400;
			font-style: normal;
		}';
	}
}

$make_it_card_style = isset( $shortcode_meta['wcsp_make_it_card_style'] ) ? $shortcode_meta['wcsp_make_it_card_style'] : '';
if ( $make_it_card_style ) {
	$cat_border            = isset( $shortcode_meta['wcsp_cat_border'] ) ? $shortcode_meta['wcsp_cat_border'] : array(
		'top'    => '0',
		'left'   => '0',
		'right'  => '0',
		'bottom' => '0',
	);
	$wcsp_cat_border_style = isset( $shortcode_meta['wcsp_cat_border_style'] ) ? $shortcode_meta['wcsp_cat_border_style'] : array(
		'style'       => 'solid',
		'color'       => '#e2e2e2',
		'hover_color' => '#e2e2e2',
	);
	$dynamic_style        .= '
		.sp-wcsp-slider-area #sp-wcsp-slider-section-' . $post_id . ' .sp-wcsp-cat-item .sp-wcsp-cat-details .sp-wcsp-cat-details-content {
			border-top: ' . $cat_border['top'] . 'px;
			border-left: ' . $cat_border['left'] . 'px;
			border-right: ' . $cat_border['right'] . 'px;
			border-bottom: ' . $cat_border['bottom'] . 'px;
			border-style: ' . $wcsp_cat_border_style['style'] . ';
			border-color: ' . $wcsp_cat_border_style['color'] . ';
		}
		.sp-wcsp-slider-area #sp-wcsp-slider-section-' . $post_id . ' .sp-wcsp-cat-item:hover .sp-wcsp-cat-details .sp-wcsp-cat-details-content {
			border-color: ' . $wcsp_cat_border_style['hover_color'] . ';
		}';
}

$cat_padding_top    = isset( $cat_padding['top'] ) ? $cat_padding['top'] : '';
$cat_padding_right  = isset( $cat_padding['right'] ) ? $cat_padding['right'] : '';
$cat_padding_bottom = isset( $cat_padding['bottom'] ) ? $cat_padding['bottom'] : '';
$cat_padding_left   = isset( $cat_padding['left'] ) ? $cat_padding['left'] : '';
$dynamic_style     .= '.sp-wcsp-slider-area #sp-wcsp-slider-section-' . $post_id . ' .sp-wcsp-cat-item .sp-wcsp-cat-details .sp-wcsp-cat-details-content {
	padding: ' . $cat_padding_top . 'px ' . $cat_padding_right . 'px ' . $cat_padding_bottom . 'px ' . $cat_padding_left . 'px;
}';

$thumb_margin_unit   = isset( $thumb_margin['unit'] ) ? $thumb_margin['unit'] : '';
$thumb_margin_top    = isset( $thumb_margin['top'] ) ? $thumb_margin['top'] : '';
$thumb_margin_right  = isset( $thumb_margin['right'] ) ? $thumb_margin['right'] : '';
$thumb_margin_bottom = isset( $thumb_margin['bottom'] ) ? $thumb_margin['bottom'] : '';
$thumb_margin_left   = isset( $thumb_margin['left'] ) ? $thumb_margin['left'] : '';
$dynamic_style      .= '.sp-wcsp-slider-area #sp-wcsp-slider-section-' . $post_id . ' .sp-wcsp-cat-item .sp-wcsp-cat-thumbnail {
	margin: ' . $thumb_margin_top . $thumb_margin_unit . ' ' . $thumb_margin_right . $thumb_margin_unit . ' ' . $thumb_margin_bottom . $thumb_margin_unit . ' ' . $thumb_margin_left . $thumb_margin_unit . ';
}';

// Navigation.
$nav_hide_on_mobile = isset( $shortcode_meta['wcsp_carousel_navigation']['navigation_hide_on_mobile'] ) ? $shortcode_meta['wcsp_carousel_navigation']['navigation_hide_on_mobile'] : false;
if ( $nav_hide_on_mobile ) {
	$dynamic_style .= '@media (max-width: 480px) {
		.sp-wcsp-slider-area-' . $post_id . ' .sp-wcsp-button {
			display: none;
		}
	}';
}
$nav_colors                  = isset( $shortcode_meta['wcsp_nav_colors'] ) ? $shortcode_meta['wcsp_nav_colors'] : '';
$nav_border                  = isset( $shortcode_meta['wcsp_nav_border'] ) ? $shortcode_meta['wcsp_nav_border'] : '';
$nav_border_all              = isset( $nav_border['all'] ) ? $nav_border['all'] : '';
$nav_border_style            = isset( $nav_border['style'] ) ? $nav_border['style'] : '';
$nav_border_color            = isset( $nav_border['color'] ) ? $nav_border['color'] : '';
$nav_colors_color            = isset( $nav_colors['color'] ) ? $nav_colors['color'] : '';
$nav_colors_background       = isset( $nav_colors['background'] ) ? $nav_colors['background'] : '';
$nav_border_hover_color      = isset( $nav_border['hover_color'] ) ? $nav_border['hover_color'] : '';
$nav_colors_hover_color      = isset( $nav_colors['hover_color'] ) ? $nav_colors['hover_color'] : '';
$nav_colors_hover_background = isset( $nav_colors['hover_background'] ) ? $nav_colors['hover_background'] : '';
$dynamic_style              .= '.sp-wcsp-slider-area-' . $post_id . ' .sp-wcsp-button-prev, .sp-wcsp-slider-area-' . $post_id . ' .sp-wcsp-button-next{
	border: ' . $nav_border_all . 'px ' . $nav_border_style . ' ' . $nav_border_color . ';
	color: ' . $nav_colors_color . ';
	background: ' . $nav_colors_background . ';
	height: 30px;
	line-height: 28px;
	font-size: 20px;
	width: 30px;
}
.sp-wcsp-slider-area-' . $post_id . ' .sp-wcsp-button-prev:hover,
.sp-wcsp-slider-area-' . $post_id . ' .sp-wcsp-button-next:hover{
	border-color: ' . $nav_border_hover_color . ';
	color: ' . $nav_colors_hover_color . ';
	background: ' . $nav_colors_hover_background . ';
}';

$pagination_hide_on_mobile      = isset( $shortcode_meta['wcsp_carousel_pagination']['pagination_hide_on_mobile'] ) ? $shortcode_meta['wcsp_carousel_pagination']['pagination_hide_on_mobile'] : false;
$pagination_colors              = isset( $shortcode_meta['wcsp_pagination_colors'] ) ? $shortcode_meta['wcsp_pagination_colors'] : '';
$pagination_number_colors       = isset( $shortcode_meta['wcsp_pagination_number_colors'] ) ? $shortcode_meta['wcsp_pagination_number_colors'] : '';
$pagination_colors_color        = isset( $pagination_colors['color'] ) ? $pagination_colors['color'] : '';
$pagination_colors_active_color = isset( $pagination_colors['active_color'] ) ? $pagination_colors['active_color'] : '';
// Pagination.
$dynamic_style .= '#sp-wcsp-slider-section-' . $post_id . ' .sp-wcsp-pagination span {
	margin: 0 3px;
	width: 12px;
	height: 12px;
	background: ' . $pagination_colors_color . ';
	opacity: 1;
	font-size: 14px;
	text-indent: -999px;
	overflow: hidden;
}
#sp-wcsp-slider-section-' . $post_id . ' .sp-wcsp-pagination span.swiper-pagination-bullet-active {
	background: ' . $pagination_colors_active_color . ';
}';
if ( $pagination_hide_on_mobile ) {
	$dynamic_style .= '@media (max-width: 480px) {
		.sp-wcsp-slider-area-' . $post_id . ' .sp-wcsp-pagination {
			display: none;
		}
	}';
}

// Shop Now button.
if ( $cat_shop_now_button ) {
	$cat_shop_button_border             = isset( $shortcode_meta['wcsp_cat_shop_button_border'] ) ? $shortcode_meta['wcsp_cat_shop_button_border'] : '';
	$cat_button_margin                  = isset( $shortcode_meta['wcsp_cat_button_margin'] ) ? $shortcode_meta['wcsp_cat_button_margin'] : '';
	$cat_button_margin_unit             = isset( $cat_button_margin['unit'] ) ? $cat_button_margin['unit'] : '';
	$cat_button_margin_top              = isset( $cat_button_margin['top'] ) ? $cat_button_margin['top'] : '';
	$cat_button_margin_right            = isset( $cat_button_margin['right'] ) ? $cat_button_margin['right'] : '';
	$cat_button_margin_bottom           = isset( $cat_button_margin['bottom'] ) ? $cat_button_margin['bottom'] : '';
	$cat_button_margin_left             = isset( $cat_button_margin['left'] ) ? $cat_button_margin['left'] : '';
	$cat_shop_button_border_all         = isset( $cat_shop_button_border['all'] ) ? $cat_shop_button_border['all'] : '';
	$cat_shop_button_border_style       = isset( $cat_shop_button_border['style'] ) ? $cat_shop_button_border['style'] : '';
	$cat_shop_button_border_color       = isset( $cat_shop_button_border['color'] ) ? $cat_shop_button_border['color'] : '';
	$cat_shop_button_border_hover_color = isset( $cat_shop_button_border['hover_color'] ) ? $cat_shop_button_border['hover_color'] : '';

	$cat_shop_button_typo             = isset( $shortcode_meta['wcsp_shop_now_typography'] ) ? $shortcode_meta['wcsp_shop_now_typography'] : '';
	$cat_shop_typo_button_color       = isset( $cat_shop_button_typo['color'] ) ? $cat_shop_button_typo['color'] : '#ffffff';
	$cat_shop_typo_button_hover_color = isset( $cat_shop_button_typo['hover-color'] ) ? $cat_shop_button_typo['hover-color'] : '#ffffff';

	$cat_shop_button_color_background       = isset( $cat_shop_button_color['background'] ) ? $cat_shop_button_color['background'] : '';
	$cat_shop_button_color_hover_background = isset( $cat_shop_button_color['hover_background'] ) ? $cat_shop_button_color['hover_background'] : '';

	$dynamic_style .= '.sp-wcsp-slider-area #sp-wcsp-slider-section-' . $post_id . ' .sp-wcsp-cat-item .sp-wcsp-shop-now {
		margin: ' . $cat_button_margin_top . $cat_button_margin_unit . ' ' . $cat_button_margin_right . $cat_button_margin_unit . ' ' . $cat_button_margin_bottom . $cat_button_margin_unit . ' ' . $cat_button_margin_left . $cat_button_margin_unit . ';
		border-width: ' . $cat_shop_button_border_all . 'px;
		border-style: ' . $cat_shop_button_border_style . ';
		border-color: ' . $cat_shop_button_border_color . ';
		color: ' . $cat_shop_typo_button_color . ';
		font-size: 15px;
		line-height: 20px;
		letter-spacing: 0;
		text-transform: none;
		text-align: center;
		font-weight: 700;
		font-style: normal;
		background: ' . $cat_shop_button_color['background'] . ';
		z-index: 99;
		position: relative;
	}
	.sp-wcsp-slider-area #sp-wcsp-slider-section-' . $post_id . ' .sp-wcsp-cat-item .sp-wcsp-shop-now:hover {
		border-color: ' . $cat_shop_button_border_hover_color . ';
		color: ' . $cat_shop_typo_button_hover_color . ';
		background: ' . $cat_shop_button_color_hover_background . ';
	}';
}

// Preloader.
if ( $preloader ) {
	$dynamic_style .= '
	.sp-wcsp-slider-area-' . $post_id . '{
		position: relative;
	}
	#sp-wcsp-slider-section-' . $post_id . ' {
		opacity: 0;
	}
	#wcsp-preloader-' . $post_id . '{
		position: absolute;
		left: 0;
		top: 0;
		height: 100%;
		width: 100%;
		text-align: center;
		display: flex;
		align-items: center;
		justify-content: center;
	}
	';
}

// Border.
if ( $show_thumb_border ) {
	$dynamic_style .= '.sp-wcsp-slider-area #sp-wcsp-slider-section-' . $post_id . ' .sp-wcsp-cat-item .sp-wcsp-cat-thumbnail {
		border-top: ' . $thumb_border['top'] . 'px;
		border-right: ' . $thumb_border['right'] . 'px;
		border-bottom: ' . $thumb_border['bottom'] . 'px;
		border-left: ' . $thumb_border['left'] . 'px;
		border-style: ' . $thumb_border_style['style'] . ';
		border-color: ' . $thumb_border_style['color'] . ';
	}.sp-wcsp-slider-area #sp-wcsp-slider-section-' . $post_id . ' .sp-wcsp-cat-item:hover .sp-wcsp-cat-thumbnail {
		border-color: ' . $thumb_border_style['hover_color'] . ';
	}';
}
