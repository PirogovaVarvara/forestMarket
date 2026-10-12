<?php
/**
 * Framework heading field file.
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

if ( ! class_exists( 'SP_WCS_Field_heading' ) ) {
	/**
	 *
	 * Field: heading
	 *
	 * @since 1.0.0
	 * @version 1.0.0
	 */
	class SP_WCS_Field_heading extends SP_WCS_Fields {
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
			echo '<div class="sp-category-slider-banner">';
			echo '<div class="sp-category-logo">';
			echo ( ! empty( $this->field['content'] ) ) ? wp_kses_post( $this->field['content'] ) : '';
			echo ( ! empty( $this->field['image'] ) ) ? '<img src="' . esc_url( $this->field['image'] ) . '">' : '';
			echo '<span class="sp-category-slider-version">' . esc_html( SP_WCS_VERSION ) . '</span>';
			echo '</div><div class="wcsp-submit-options">';
			echo '<div class="sp-category-short-links"><a href="https://shapedplugin.com/support/" target="_blank"><i class="fa fa-life-ring"></i>Support</a></div>
			<div class="spf-help-text spf-support"><div class="spf-info-label">Documentation</div>Check out our documentation and more information about what you can do with the Reno Product Category Pro.<a class="spf-open-docs browser-docs" href="https://docs.shapedplugin.com/docs/woocommerce-category-slider/introduction/" target="_blank">Browse Docs</a><div class="spf-info-label">Need Help or Missing a Feature?</div>Feel free to get help from our friendly support team or request a new feature if needed. We appreciate your suggestions to make the plugin better.<a class="spf-open-docs support" href="https://shapedplugin.com/create-new-ticket" target="_blank">Get Help</a><a class="spf-open-docs feature-request" href="https://shapedplugin.com/contact-us/" target="_blank">Request a Feature</a></div></div></div>';
		}
	}
}
