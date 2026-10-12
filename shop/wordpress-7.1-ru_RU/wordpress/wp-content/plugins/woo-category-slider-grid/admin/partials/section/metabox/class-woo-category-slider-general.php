<?php
/**
 * General settings tab.
 *
 * @link       https://shapedplugin.com/
 * @since      1.0.0
 *
 * @package    Woo_Category_Slider
 * @subpackage Woo_Category_Slider/admin/partials/section/metabox
 * @author     ShapedPlugin <support@shapedplugin.com>
 */

// Cannot access directly.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * This class is responsible for General settings tab.
 *
 * @since 1.0.0
 */
class SP_WCS_General {
	/**
	 * General section.
	 *
	 * @param string $prefix General section prefix.
	 * @return void
	 */
	public static function section( $prefix ) {

		SP_WCS::createSection(
			$prefix,
			array(
				'title'  => esc_html__( 'General Settings', 'woo-category-slider-grid' ),
				'icon'   => 'fa fa-cog',
				'class'  => 'active',
				'fields' => array(
					array(
						'id'         => 'wcsp_number_of_column',
						'type'       => 'column',
						'title'      => esc_html__( 'Columns', 'woo-category-slider-grid' ),
						'subtitle'   => esc_html__( 'Set number of columns in different responsive devices.', 'woo-category-slider-grid' ),
						'min'        => '1',
						'default'    => array(
							'large_desktop' => '4',
							'desktop'       => '3',
							'laptop'        => '2',
							'tablet'        => '2',
							'mobile'        => '1',
						),
						'help'       => '<i class="fa fa-television"></i> LARGE DESKTOP - Screens larger than 1280px.<br/>
							<i class="fa fa-desktop"></i> DESKTOP - Screens smaller than 1280px.<br/>
							<i class="fa fa-laptop"></i> LAPTOP - Screens smaller than 980px.<br/>
							<i class="fa fa-tablet"></i> TABLET - Screens smaller than 736px.<br/>
							<i class="fa fa-mobile"></i> MOBILE - Screens smaller than 480px.<br/>',
						'dependency' => array( 'wcsp_layout_presets', '!=', 'slider', true ),
					),

					array(
						'id'          => 'wcsp_space_between_cat',
						'type'        => 'spacing',
						'class'       => 'wcsp-space-between-cat',
						'title'       => esc_html__( 'Space Between Categories', 'woo-category-slider-grid' ),
						'subtitle'    => esc_html__( 'Set space between categories.', 'woo-category-slider-grid' ),
						'output_mode' => 'margin',
						'all'         => true,
						'all_text'    => false,
						'units'       => array(
							esc_html__( 'px', 'woo-category-slider-grid' ),
						),
						'default'     => array(
							'all'  => '20',
							'unit' => 'px',
						),
						'dependency'  => array( 'wcsp_layout_presets', '!=', 'slider', true ),
					),
					array(
						'id'       => 'wcsp_child_categories',
						'type'     => 'button_setf',
						'title'    => esc_html__( 'Category Type', 'woo-category-slider-grid' ),
						'subtitle' => esc_html__( 'Select a category type.', 'woo-category-slider-grid' ),
						'options'  => array(
							'hide'             => array(
								'text'     => esc_html__( 'Parent', 'woo-category-slider-grid' ),
								'pro_only' => false,
							),
							'parent_and_child' => array(
								'text'     => esc_html__( 'Parent and Child', 'woo-category-slider-grid' ),
								'pro_only' => false,
							),
						),
						'default'  => 'hide',
					),
					array(
						'id'         => 'wcsp_parent_and_child_categories',
						'class'      => 'wcsp_custom_select_parent_child_category',
						'only_pro'   => true,
						'type'       => 'custom_select',
						'title'      => esc_html__( 'Parent and Child', 'woo-category-slider-grid' ),
						'subtitle'   => esc_html__( 'Select category(s). Leave it empty to show all level of categories', 'woo-category-slider-grid' ),
						'desc'       => sprintf(
							/* translators: 1: start bold tag, 2: close bold tag, 3: start link and bold tag, 4: close link and bold tag. */
							esc_html__( 'To display %5$sParent with Child%6$s%1$s, Grand Child, Great-grand Child%2$s, %3$sUpgrade to Pro!%4$s', 'woo-category-slider-grid' ),
							'<strong>',
							'</strong>',
							'<a href="https://shapedplugin.com/reno-product-category/?ref=115#pricing" target="_blank"><b>',
							'</b></a>',
							'<a href="https://demo.shapedplugin.com/woocategory/carousel/#parent-and-child-both" target="_blank"><b>',
							'</b></a>'
						),
						'dependency' => array( 'wcsp_child_categories', '==', 'parent_and_child', true ),
					),
					array(
						'id'          => 'wcsp_parent_child_display_type',
						'type'        => 'image_select',
						'only_pro'    => true,
						'class'       => 'wcsp_layout_presets wcsp_parent_child_display_type',
						'title'       => esc_html__( 'Display Type', 'woo-category-slider-grid' ),
						'subtitle'    => esc_html__( 'Select an display type for parent and child categories.', 'woo-category-slider-grid' ),
						'option_name' => true,
						'options'     => array(
							'individualize_each' => array(
								'image'       => SP_WCS_URL . 'admin/img/individual-each.svg',
								'option_name' => esc_html__( 'Individualize Each', 'woo-category-slider-grid' ),
								'pro_only'    => true,
							),
							'under_parent'       => array(
								'image'       => SP_WCS_URL . 'admin/img/child-under-parent.svg',
								'option_name' => esc_html__( 'Child Under Parent', 'woo-category-slider-grid' ),
								'pro_only'    => true,
							),
						),
						'dependency'  => array( 'wcsp_child_categories', '==', 'parent_and_child', true ),
					),
					array(
						'id'         => 'wcsp_exclude_level',
						'class'      => 'wcsp_category_exclude_levels_checkbox',
						'type'       => 'checkbox',
						'only_pro'   => true,
						'inline'     => true,
						'title'      => esc_html__( 'Exclude Level(s)', 'woo-category-slider-grid' ),
						'subtitle'   => esc_html__( 'Exclude different level of categories.', 'woo-category-slider-grid' ),
						'options'    => array(
							'parent'           => esc_html__( 'Parent (Pro)', 'woo-category-slider-grid' ),
							'child'            => esc_html__( 'Child (Pro)', 'woo-category-slider-grid' ),
							'grandchild'       => esc_html__( 'Grand Child (Pro)', 'woo-category-slider-grid' ),
							'great_grandchild' => esc_html__( 'Great-grand Child (Pro)', 'woo-category-slider-grid' ),
						),
						'dependency' => array( 'wcsp_child_categories', '==', 'parent_and_child', true ),
					),
					array(
						'id'         => 'wcsp_filter_categories',
						'type'       => 'selectf',
						'title'      => esc_html__( 'Filter Categories', 'woo-category-slider-grid' ),
						'subtitle'   => esc_html__( 'Select an option to filter the categories.', 'woo-category-slider-grid' ),
						'options'    => array(
							'all'      => array(
								'text'     => esc_html__( 'All', 'woo-category-slider-grid' ),
								'pro_only' => false,
							),
							'specific' => array(
								'text'     => esc_html__( 'Specific', 'woo-category-slider-grid' ),
								'pro_only' => false,
							),
							'exclude'  => array( // phpcs:ignore
								'text'     => esc_html__( 'Exclude (Pro)', 'woo-category-slider-grid' ),
								'pro_only' => true,
							),
						),
						'default'    => 'all',
						'dependency' => array( 'wcsp_child_categories', '==', 'hide', true ),

					),
					array(
						'id'          => 'wcsp_categories_list',
						'type'        => 'select',
						'title'       => esc_html__( 'Choose Category(s)', 'woo-category-slider-grid' ),
						'subtitle'    => esc_html__( 'Choose the specific category(s) to show.', 'woo-category-slider-grid' ),
						'options'     => 'sp_wcsp_categories',
						'attributes'  => array(
							'style' => 'width: 280px;',
						),
						'multiple'    => 'multiple',
						'placeholder' => esc_html__( 'Select Category(s)', 'woo-category-slider-grid' ),
						'chosen'      => true,
						'dependency'  => array(
							'wcsp_filter_categories|wcsp_child_categories',
							'==|==',
							'specific|hide',
							true,
						),
					),
					array(
						'id'       => 'wcsp_hide_empty_categories',
						'type'     => 'checkbox',
						'title'    => esc_html__( 'Hide Empty Categories', 'woo-category-slider-grid' ),
						'subtitle' => esc_html__( 'Check to hide empty categories from the slider.', 'woo-category-slider-grid' ),
						'default'  => false,
					),
					array(
						'id'              => 'wcsp_number_of_total_categories',
						'class'           => 'wcsp-number-of-total-categories',
						'type'            => 'spacing',
						'title'           => esc_html__( 'Total Categories to Show', 'woo-category-slider-grid' ),
						'subtitle'        => esc_html__( 'Total number of categories to display.', 'woo-category-slider-grid' ),
						'all'             => true,
						'all_text'        => false,
						'all_placeholder' => false,
						'unit'            => false,
						'min'             => '1',
						'default'         => array(
							'all' => '12',
						),
					),
					array(
						'id'       => 'wcsp_order_by',
						'type'     => 'select',
						'title'    => esc_html__( 'Order By', 'woo-category-slider-grid' ),
						'subtitle' => esc_html__( 'Select an order by option.', 'woo-category-slider-grid' ),
						'options'  => array(
							'ID'         => esc_html__( 'ID', 'woo-category-slider-grid' ),
							'title'      => esc_html__( 'Name', 'woo-category-slider-grid' ),
							'date'       => esc_html__( 'Date', 'woo-category-slider-grid' ),
							'menu_order' => esc_html__( 'Drag & Drop', 'woo-category-slider-grid' ),
							'count'      => esc_html__( 'Count number of product', 'woo-category-slider-grid' ),
							'none'       => esc_html__( 'None', 'woo-category-slider-grid' ),
						),
						'default'  => 'date',
					),
					array(
						'id'       => 'wcsp_order',
						'type'     => 'select',
						'title'    => esc_html__( 'Order', 'woo-category-slider-grid' ),
						'subtitle' => esc_html__( 'Select an order option.', 'woo-category-slider-grid' ),
						'options'  => array(
							'ASC'  => esc_html__( 'Ascending', 'woo-category-slider-grid' ),
							'DESC' => esc_html__( 'Descending', 'woo-category-slider-grid' ),
						),
						'default'  => 'DESC',
					),
					array(
						'id'         => 'wcsp_randomize_categories',
						'type'       => 'switcher',
						'class'      => 'pro_only_field',
						'title'      => esc_html__( 'Randomize Categories', 'woo-category-slider-grid' ),
						'subtitle'   => esc_html__( 'Enable to randomize category order on each page load.', 'woo-category-slider-grid' ),
						'default'    => false,
						'text_on'    => esc_html__( 'Enabled', 'woo-category-slider-grid' ),
						'text_off'   => esc_html__( 'Disabled', 'woo-category-slider-grid' ),
						'text_width' => 100,
					),
					array(
						'id'         => 'wcsp_preloader',
						'type'       => 'switcher',
						'title'      => esc_html__( 'Preloader', 'woo-category-slider-grid' ),
						'subtitle'   => esc_html__( 'Slider will be hidden until page load completed.', 'woo-category-slider-grid' ),
						'text_on'    => esc_html__( 'Enabled', 'woo-category-slider-grid' ),
						'text_off'   => esc_html__( 'Disabled', 'woo-category-slider-grid' ),
						'text_width' => 100,
						'default'    => true,
					),

				), // Fields array end.
			)
		); // End of General section.
	}
}
