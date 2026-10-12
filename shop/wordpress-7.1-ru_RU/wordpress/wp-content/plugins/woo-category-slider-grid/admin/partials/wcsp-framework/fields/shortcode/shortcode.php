<?php
/**
 * Framework shortcode field file.
 *
 * @link       https://shapedplugin.com/
 * @since      1.0.0
 *
 * @package    Woo_Category_Slider
 * @subpackage Woo_Category_Slider/admin/partials/section/settings
 * @author     ShapedPlugin <support@shapedplugin.com>
 */

if ( ! defined( 'ABSPATH' ) ) {
	die;
} // Cannot access directly.

if ( ! class_exists( 'SP_WCS_Field_shortcode' ) ) {
	/**
	 *
	 * Field: shortcode
	 *
	 * @since 1.0.0
	 * @version 1.0.0
	 */
	class SP_WCS_Field_shortcode extends SP_WCS_Fields {
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

			// Get the Post ID.
			$post_id = get_the_ID();

			if ( empty( $post_id ) ) {
				return '';
			}

			echo '<div class="wcsp-scode-wrap">';
			if ( isset( $this->field['shortcode'] ) && 'manage_view' === $this->field['shortcode'] ) {

				echo '
				<div class="wcsp-after-copy-text woo-cat-pagination-not-work"><i class="fa fa-check-circle"></i>  ' . esc_html__( 'The pagination will work in the frontend well.', 'woo-category-slider-grid' ) . '</div>
				<div class="wcsp-scode-content">
					<p>' . sprintf(
						/* translators: 1: start link tag, 2: close link tag. */
					esc_html__( 'To display your product category view, add the following shortcode into your post, custom post types, page, widget or block editor. If adding the category view to your theme files, additionally include the surrounding PHP code, %1$ssee how%2$s.', 'woo-category-slider-grid' ),
					'<a href="https://docs.shapedplugin.com/docs/woocommerce-category-slider-pro/faq/#template-include" target="_blank">',
					'</a>'
				) . '</p>
					<div class="shortcode-wrap">
					<div class="selectable">[renocatslider id="' . esc_attr( $post_id ) . '"]</div></div>
					<div class="wcsp-after-copy-text"><i class="fa fa-check-circle"></i>  ' . esc_html__( 'Shortcode  Copied to Clipboard!', 'woo-category-slider-grid' ) . '</div>
				</div>';

			} elseif ( ! empty( $this->field['shortcode'] ) && 'pro_notice' === $this->field['shortcode'] ) {
				if ( ! empty( $post_id ) ) {
					echo '<div class="sp_wcsp_shortcode-area sp_wcsp-notice-wrapper">';
					echo '<div class="sp_wcsp-notice-heading">' . sprintf(
						/* translators: 1: start span tag, 2: close tag. */
						esc_html__( 'Unlock Full Potential with %1$sPRO%2$s', 'woo-category-slider-grid' ),
						'<span>',
						'</span>'
					) . '</div>';

					echo '<p class="sp_wcsp-notice-desc">' . sprintf(
						/* translators: 1: start bold tag, 2: close tag. */
						esc_html__( 'Enhance the shopping experience with Stunning Product Category Displays!', 'woo-category-slider-grid' ),
						'<b>',
						'</b>'
					) . '</p>';

					echo '<ul>';
					echo '<li><i class="wcsp-icon-check-icon"></i> ' . esc_html__( '11+ Category Layouts', 'woo-category-slider-grid' ) . '</li>';
					echo '<li><i class="wcsp-icon-check-icon"></i> ' . esc_html__( 'A Lot of Templates & Styles', 'woo-category-slider-grid' ) . '</li>';
					echo '<li><i class="wcsp-icon-check-icon"></i> ' . esc_html__( 'Multi-level (up to 4) Categories', 'woo-category-slider-grid' ) . '</li>';
					echo '<li><i class="wcsp-icon-check-icon"></i> ' . esc_html__( 'Highlight Special Categories', 'woo-category-slider-grid' ) . '</li>';
					echo '<li><i class="wcsp-icon-check-icon"></i> ' . esc_html__( 'Random Order & Item Counter', 'woo-category-slider-grid' ) . '</li>';
					echo '<li><i class="wcsp-icon-check-icon"></i> ' . esc_html__( 'Show Childs Independently', 'woo-category-slider-grid' ) . '</li>';
					echo '<li><i class="wcsp-icon-check-icon"></i> ' . esc_html__( 'Auto Hide Empty Categories', 'woo-category-slider-grid' ) . '</li>';
					echo '<li><i class="wcsp-icon-check-icon"></i> ' . esc_html__( '130+ Customizations and More', 'woo-category-slider-grid' ) . '</li>';
					echo '</ul>';

					echo '<div class="sp_wcsp-notice-button">';
					echo '<a class="sp_wcsp-open-live-demo" href="https://shapedplugin.com/reno-product-category/#pricing" target="_blank">';
					echo esc_html__( 'Upgrade to Pro Now', 'woo-category-slider-grid' ) . ' <i class="wcsp-icon-shuttle_2285485-1"></i>';
					echo '</a>';
					echo '</div>';
					echo '</div>';
				}
			} else {

				echo '
				<div class="wcsp-scode-content">
					<p>' .
					sprintf(
						/* translators: 1: start strong tag, 2: close tag. */
						esc_html__( 'Reno Product Category has seamless integration with Gutenberg, Classic Editor, %1$sElementor%2$s, Divi, Bricks, Beaver, Oxygen, WPBakery Builder, etc.', 'woo-category-slider-grid' ),
						'<strong>',
						'</strong>'
					) . '
					</p>
				</div>';
			}
			echo '</div>';
		}
	}
}
