<?php
/**
 * The metabox of the plugin.
 *
 * @link       https://shapedplugin.com/
 * @since      1.0.0
 * @package    Woo_Category_Slider
 * @subpackage Woo_Category_Slider/admin/partials
 * @author     ShapedPlugin <support@shapedplugin.com>
 */

if ( ! defined( 'ABSPATH' ) ) {
	die;
} // Cannot access directly.

/**
 * The metabox main class.
 */
class SP_WCS_Metaboxs {

	/**
	 * Preview metabox.
	 *
	 * @param string $prefix The metabox main Key.
	 * @return void
	 */
	public static function preview_metabox( $prefix ) {
		SP_WCS::createMetabox(
			$prefix,
			array(
				'title'        => esc_html__( 'Live Preview', 'woo-category-slider-grid' ),
				'post_type'    => 'sp_wcslider',
				'show_restore' => false,
				'context'      => 'normal',
			)
		);
		SP_WCS::createSection(
			$prefix,
			array(
				'fields' => array(
					array(
						'type' => 'preview',
					),
				),
			)
		);
	}

	/**
	 * Side metabox.
	 *
	 * @return void
	 */
	public static function side_metabox() {
		SP_WCS::createMetabox(
			'sp_wcsp_copy_shortcode',
			array(
				'title'             => esc_html__( 'How To Use', 'woo-category-slider-grid' ),
				'post_type'         => 'sp_wcslider',
				'context'           => 'side',
				'show_restore'      => false,
				'sp_wcsp_shortcode' => false,
			)
		);

		SP_WCS::createSection(
			'sp_wcsp_copy_shortcode',
			array(
				'fields' => array(
					array(
						'type'      => 'shortcode',
						'shortcode' => 'manage_view',
						'class'     => 'sp_tpro-admin-sidebar',
					),
				),
			)
		);

		/**
		 * Page builder supported metabox.
		 *
		 * @param string 'sp_wcsp_promotion_section' The metabox main Key.
		 * @return void
		 */
		SP_WCS::createMetabox(
			'sp_wcsp_promotion_section',
			array(
				'title'             => esc_html__( 'Page Builders', 'woo-category-slider-grid' ),
				'post_type'         => 'sp_wcslider',
				'context'           => 'side',
				'show_restore'      => false,
				'sp_wcsp_shortcode' => false,
			)
		);

		SP_WCS::createSection(
			'sp_wcsp_promotion_section',
			array(
				'fields' => array(
					array(
						'type'      => 'shortcode',
						'shortcode' => false,
						'class'     => 'sp_tpro-admin-sidebar',
					),
				),
			)
		);

		SP_WCS::createMetabox(
			'sp_wcsp_notice',
			array(
				'title'        => esc_html__( 'Unlock Pro Feature', 'woo-category-slider-grid' ),
				'post_type'    => 'sp_wcslider',
				'context'      => 'side',
				'show_restore' => false,
			)
		);

		SP_WCS::createSection(
			'sp_wcsp_notice',
			array(
				'fields' => array(
					array(
						'type'      => 'shortcode',
						'shortcode' => 'pro_notice',
						'class'     => 'spwps-admin-sidebar',
					),
				),
			)
		);
	}

	/**
	 * Metabox banner.
	 *
	 * @param string $prefix metabox.
	 * @return void
	 */
	public static function metabox_layout( $prefix ) {

		// Create a metabox.
		SP_WCS::createMetabox(
			$prefix,
			array(
				'title'        => esc_html__( 'Reno Product Category', 'woo-category-slider-grid' ),
				'post_type'    => 'sp_wcslider',
				'show_restore' => false,
				'context'      => 'normal',
				'priority'     => 'default',
			)
		);

		// Create a section.
		SP_WCS::createSection(
			$prefix,
			array(
				'fields' => array(
					array(
						'type'  => 'heading',
						'image' => plugin_dir_url( __DIR__ ) . 'img/reno-category-slider-white-logo.svg',
						'after' => '<i class="fa fa-life-ring"></i> Support',
						'link'  => 'https://shapedplugin.com/support/',
						'class' => 'wcsp-admin-header',
					),
					array(
						'id'          => 'wcsp_layout_presets',
						'type'        => 'image_select',
						'title'       => esc_html__( 'Layout Preset', 'woo-category-slider-grid' ),
						'class'       => 'wcsp_layout_presets',
						'option_name' => true,
						'options'     => array(
							'carousel'  => array(
								'image'           => SP_WCS_URL . 'admin/img/layout-presets/carousel.svg',
								'option_name'     => esc_html__( 'Carousel', 'woo-category-slider-grid' ),
								'option_demo_url' => 'https://demo.shapedplugin.com/woocategory/carousel/',
							),
							'slider'    => array(
								'image'           => SP_WCS_URL . 'admin/img/layout-presets/slider.svg',
								'option_name'     => esc_html__( 'Slider', 'woo-category-slider-grid' ),
								'option_demo_url' => 'https://demo.shapedplugin.com/woocategory/slider/',
							),
							'multi_row' => array(
								'image'           => SP_WCS_URL . 'admin/img/layout-presets/multi_row_carousel.svg',
								'option_name'     => esc_html__( 'Multi-row', 'woo-category-slider-grid' ),
								'option_demo_url' => 'https://demo.shapedplugin.com/woocategory/multi-row-carousel/',
								'class'           => 'wcsp-pro-feature',
								'pro_only'        => true,
							),
							'grid'      => array(
								'image'           => SP_WCS_URL . 'admin/img/layout-presets/grid.svg',
								'option_name'     => esc_html__( 'Grid', 'woo-category-slider-grid' ),
								'option_demo_url' => 'https://demo.shapedplugin.com/woocategory/grid/',
								'class'           => 'wcsp-pro-feature',
								'pro_only'        => true,
							),
							'block-1'   => array(
								'image'           => SP_WCS_URL . 'admin/img/layout-presets/hierarchical_grid.svg',
								'option_name'     => esc_html__( 'Hierarchy Grid', 'woo-category-slider-grid' ),
								'option_demo_url' => 'https://demo.shapedplugin.com/woocategory/hierarchy-grid/',
								'class'           => 'wcsp-pro-feature',
								'pro_only'        => true,
							),
							'block-2'   => array(
								'image'           => SP_WCS_URL . 'admin/img/layout-presets/hierarchical_1.svg',
								'option_name'     => esc_html__( 'Hierarchy One', 'woo-category-slider-grid' ),
								'option_demo_url' => 'https://demo.shapedplugin.com/woocategory/hierarchy-one/',
								'class'           => 'wcsp-pro-feature',
								'pro_only'        => true,
							),
							'block-3'   => array(
								'image'           => SP_WCS_URL . 'admin/img/layout-presets/hierarchical_2.svg',
								'option_name'     => esc_html__( 'Hierarchy Two', 'woo-category-slider-grid' ),
								'option_demo_url' => 'https://demo.shapedplugin.com/woocategory/hierarchy-two/',
								'class'           => 'wcsp-pro-feature',
								'pro_only'        => true,
							),
							'block-4'   => array(
								'image'           => SP_WCS_URL . 'admin/img/layout-presets/hierarchical_3.svg',
								'option_name'     => esc_html__( 'Hierarchy Three', 'woo-category-slider-grid' ),
								'option_demo_url' => 'https://demo.shapedplugin.com/woocategory/heirarchy-three/',
								'class'           => 'wcsp-pro-feature',
								'pro_only'        => true,
							),
							'block-5'   => array(
								'image'           => SP_WCS_URL . 'admin/img/layout-presets/hierarchical_4.svg',
								'option_name'     => esc_html__( 'Hierarchy Four', 'woo-category-slider-grid' ),
								'option_demo_url' => 'https://demo.shapedplugin.com/woocategory/hierarchy-four/',
								'class'           => 'wcsp-pro-feature',
								'pro_only'        => true,
							),
							'inline'    => array(
								'image'           => SP_WCS_URL . 'admin/img/layout-presets/inline.svg',
								'option_name'     => esc_html__( 'Inline', 'woo-category-slider-grid' ),
								'option_demo_url' => 'https://demo.shapedplugin.com/woocategory/inline/',
								'class'           => 'wcsp-pro-feature',
								'pro_only'        => true,
							),
						),
						'default'     => 'carousel',
					),
					array(
						'id'          => 'wcsp_carousel_style',
						'type'        => 'image_select',
						'title'       => esc_html__( 'Carousel Style', 'woo-category-slider-grid' ),
						'class'       => 'wcsp_layout_presets carousel-style sp-no-selected-icon',
						'option_name' => true,
						'options'     => array(
							'standard'      => array(
								'image'       => SP_WCS_URL . 'admin/img/carousel-style/standard.svg',
								'option_name' => esc_html__( 'Standard', 'woo-category-slider-grid' ),
							),
							'ticker'        => array(
								'image'       => SP_WCS_URL . 'admin/img/carousel-style/ticker.svg',
								'option_name' => esc_html__( 'Ticker', 'woo-category-slider-grid' ),
								'pro_only'    => true,
								'class'       => 'wcsp-pro-feature',
							),
							'standard_fade' => array(
								'image'       => SP_WCS_URL . 'admin/img/carousel-style/fade.svg',
								'option_name' => esc_html__( 'Fade', 'woo-category-slider-grid' ),
								'pro_only'    => true,
								'class'       => 'wcsp-pro-feature',
							),
						),
						'default'     => 'standard',
						'dependency'  => array(
							'wcsp_layout_presets',
							'==',
							'carousel',
						),
					),
					array(
						'id'          => 'wcsp_slider_styles',
						'type'        => 'image_select',
						'title'       => esc_html__( 'Slider Styles', 'woo-category-slider-grid' ),
						'class'       => 'wcsp_layout_presets carousel-style sp-no-selected-icon wcsp-slider-styles',
						'option_name' => true,
						'options'     => array(
							'slide'     => array(
								'image'       => SP_WCS_URL . 'admin/img/effects/slide.svg',
								'option_name' => esc_html__( 'Slide', 'woo-category-slider-grid' ),
							),
							'fade'      => array(
								'image'       => SP_WCS_URL . 'admin/img/effects/fadeslide.png',
								'option_name' => esc_html__( 'Fade', 'woo-category-slider-grid' ),
								'pro_only'    => true,
								'class'       => 'wcsp-pro-feature',
							),
							'coverflow' => array(
								'image'       => SP_WCS_URL . 'admin/img/effects/coverflow.svg',
								'option_name' => esc_html__( 'Coverflow', 'woo-category-slider-grid' ),
								'pro_only'    => true,
								'class'       => 'wcsp-pro-feature',
							),
							'flip'      => array(
								'image'       => SP_WCS_URL . 'admin/img/effects/flip.svg',
								'option_name' => esc_html__( 'Flip', 'woo-category-slider-grid' ),
								'pro_only'    => true,
								'class'       => 'wcsp-pro-feature',
							),
							'cube'      => array(
								'image'       => SP_WCS_URL . 'admin/img/effects/cube.svg',
								'option_name' => esc_html__( 'Cube', 'woo-category-slider-grid' ),
								'pro_only'    => true,
								'class'       => 'wcsp-pro-feature',
							),
							'kenburn'   => array(
								'image'       => SP_WCS_URL . 'admin/img/effects/kenburns.svg',
								'option_name' => esc_html__( 'Ken Burns', 'woo-category-slider-grid' ),
								'pro_only'    => true,
								'class'       => 'wcsp-pro-feature',
							),
						),
						'default'     => 'slide',
						'attributes'  => array(
							'data-depend-id' => 'slider_animation',
						),
						'dependency'  => array(
							'wcsp_layout_presets',
							'==',
							'slider',
							true,
						),
					),
					array(
						'id'          => 'wcsp_block_orientation',
						'type'        => 'image_select',
						'title'       => esc_html__( 'Hierarchical Grid Style', 'woo-category-slider-grid' ),
						'class'       => 'wcsp_layout_presets wcsp_block_orientation sp-no-selected-icon',
						'only_pro'    => true,
						'option_name' => true,
						'options'     => array(
							'block-1' => array(
								'image'       => SP_WCS_URL . 'admin/img/hierarchical-grid-style/block-1.svg',
								'option_name' => esc_html__( 'Hierarchical Grid', 'woo-category-slider-grid' ),
								'pro_only'    => true,
							),
							'block-2' => array(
								'image'       => SP_WCS_URL . 'admin/img/hierarchical-grid-style/block-2.svg',
								'option_name' => esc_html__( 'Hierarchical 1', 'woo-category-slider-grid' ),
								'pro_only'    => true,
							),
							'block-3' => array(
								'image'       => SP_WCS_URL . 'admin/img/hierarchical-grid-style/block-3.svg',
								'option_name' => esc_html__( 'Hierarchical 2', 'woo-category-slider-grid' ),
								'pro_only'    => true,
							),
							'block-4' => array(
								'image'       => SP_WCS_URL . 'admin/img/hierarchical-grid-style/block-4.svg',
								'option_name' => esc_html__( 'Hierarchial 3', 'woo-category-slider-grid' ),
								'pro_only'    => true,
							),
							'block-5' => array(
								'image'       => SP_WCS_URL . 'admin/img/hierarchical-grid-style/block-5.svg',
								'option_name' => esc_html__( 'Hierarchical 4', 'woo-category-slider-grid' ),
								'pro_only'    => true,
							),
						),
						'default'     => 'block-1',
						'dependency'  => array(
							'wcsp_layout_presets',
							'==',
							'block',
						),
					),
					array(
						'type'    => 'notice',
						'style'   => 'normal',
						'content' => sprintf(
							/* translators: 1: start link and bold tag, 2: close tag. */
							esc_html__( 'To enhance your store with beautiful Ticker, Multi-row, Grid, Hierarchy Grid, Inline layouts, and more to boost sales, %1$sUpgrade to Pro!%2$s', 'woo-category-slider-grid' ),
							'<a href="https://shapedplugin.com/reno-product-category/?ref=115#pricing" target="_blank"><b>',
							'</b></a>'
						),
					),
				), // End of fields array.
			)
		);
	}

	/**
	 * Metabox.
	 *
	 * @param string $prefix metabox prefix.
	 * @return void
	 */
	public static function metabox( $prefix ) {

		//
		// Create a metabox.
		//
		SP_WCS::createMetabox(
			$prefix,
			array(
				'title'        => esc_html__( 'Shortcode Section', 'woo-category-slider-grid' ),
				'post_type'    => 'sp_wcslider',
				'show_restore' => false,
				'theme'        => 'light',
				'context'      => 'normal',
				'priority'     => 'default',
				'class'        => 'sp_wcsp_shortcode_generator',
			)
		);
		SP_WCS_General::section( $prefix );
		SP_WCS_Display::section( $prefix );
		SP_WCS_Thumbnail::section( $prefix );
		SP_WCS_Slider::section( $prefix );
	}
}
