<?php
/**
 * The admin preview.
 *
 * @link        https://shapedplugin.com/
 * @since      1.3.0
 *
 * @package    Woo_Category_Slider
 * @subpackage Woo_Category_Slider/admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	die; // Cannot access directly.
}

/**
 * The admin preview.
 */
class Woo_Category_Slider_Preview {
	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.3.0
	 */
	public function __construct() {
		$this->woo_category_slider_preview_action();
	}

	/**
	 * Public Action
	 *
	 * @return void
	 */
	private function woo_category_slider_preview_action() {
		// admin Preview.
		add_action( 'wp_ajax_sp_wcsp_preview_meta_box', array( $this, 'sp_wcsp_preview_meta_box' ) );
	}

	/**
	 * Function Backed preview.
	 *
	 * @since 1.3.0
	 */
	public function sp_wcsp_preview_meta_box() {
		$nonce = isset( $_POST['ajax_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['ajax_nonce'] ) ) : '';
		if ( ! wp_verify_nonce( $nonce, 'spf_metabox_nonce' ) ) {
			return;
		}
		$capability = apply_filters( 'sp_wcslider_ui_permission', 'manage_options' );
		if ( ! current_user_can( $capability ) ) {
			wp_send_json_error( array( 'error' => esc_html__( 'Authorization failed!', 'woo-category-slider-grid' ) ), 401 );
		}

		$setting = array();
		// XSS ok.
		// No worries, This "POST" requests is sanitizing in the below array map.
		$data = ! empty( $_POST['data'] ) ? wp_unslash( $_POST['data'] )  : ''; // phpcs:ignore
		parse_str( $data, $setting );
		// Shortcode id.
		$post_id        = esc_attr( $setting['post_ID'] );
		$layout_meta    = $setting['sp_wcsp_layout_options'];
		$shortcode_meta = $setting['sp_wcsp_shortcode_options'];
		$layout_preset  = isset( $layout_meta['wcsp_layout_presets'] ) ? $layout_meta['wcsp_layout_presets'] : '';

		// If layout is not slider or carousel then show a pro notice in the preview.
		if ( 'slider' !== $layout_preset && 'carousel' !== $layout_preset ) {
			printf(
				/* translators: 1: start div tag, 2: start link and bold tag, 3: close bold and link tag. */
				esc_html__( '%1$sThis feature is only available to pro users. To access it, %2$sUpgrade to Pro!%3$s', 'woo-category-slider-grid' ),
				'<div class="wcsp-pro-notice-preview">',
				'<a href="https://shapedplugin.com/reno-product-category/?ref=115#pricing" target="_blank"><b>',
				'</b></a></div>'
			);
			die();
		}
		$title         = $setting['post_title'];
		$dynamic_style = Woo_Category_Slider_Public::load_dynamic_style( $post_id, $shortcode_meta, $layout_meta );

		echo '<style id="sp_category_dynamic_css' . $post_id . '">' . wp_strip_all_tags( $dynamic_style['dynamic_css'] ) . '</style>';//phpcs:ignore
		Woo_Category_Slider_Shortcode::sp_wcsp_html_show( $post_id, $shortcode_meta, $title, $layout_meta );
		?>
		<?php
		die();
	}
}
new Woo_Category_Slider_Preview();
