<?php
/**
 * Rebrand announcement notice.
 *
 * Announces the WooCategory to Reno Product Category rename on the plugin's
 * own screens. Dismissing it hides the notice for the whole site, matching how
 * the offer banner behaves, so the dismissed state is kept in a site option.
 *
 * @since      1.6.7
 *
 * @package    Woo_Category_Slider
 * @subpackage Woo_Category_Slider/admin/partials/notices
 * @author     ShapedPlugin<support@shapedplugin.com>
 */

if ( ! defined( 'ABSPATH' ) ) {
	die; // Cannot access directly.
}

if ( ! class_exists( 'Woo_Category_Slider_Rebrand_Notice' ) ) {
	/**
	 * The class for the rebrand announcement notice.
	 */
	class Woo_Category_Slider_Rebrand_Notice {

		/**
		 * The single instance of the class.
		 *
		 * @var self
		 * @since 1.6.7
		 */
		private static $instance = null;

		/**
		 * Site option key holding the dismissed state.
		 *
		 * @var string
		 */
		const DISMISSED_OPTION_KEY = 'sp_wcsp_rebrand_notice_dismissed';

		/**
		 * Blog post explaining the rebrand.
		 *
		 * @var string
		 */
		const ANNOUNCEMENT_URL = 'https://shapedplugin.com/woocategory-is-now-reno-product-category/';

		/**
		 * Class constructor.
		 */
		private function __construct() {
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_rebrand_notice_style' ) );
			add_action( 'admin_notices', array( $this, 'render_rebrand_notice' ) );
			add_action( 'wp_ajax_sp_wcsp_dismiss_rebrand_notice', array( $this, 'dismiss_rebrand_notice' ) );
		}

		/**
		 * Retrieves the singleton instance of the class.
		 *
		 * This method ensures that only one instance of the class is created (singleton pattern).
		 *
		 * @return self The singleton instance of the class.
		 */
		public static function instance() {
			if ( null === self::$instance ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Whether the current screen belongs to this plugin.
		 *
		 * Every plugin screen — the slider list table, the slider editor, and the
		 * Settings and Get Help submenus — hangs off the slider post type, so the
		 * post type covers them all, the same way the admin assets are gated.
		 *
		 * @return bool
		 */
		private function is_plugin_screen() {
			if ( ! function_exists( 'get_current_screen' ) ) {
				return false;
			}

			$current_screen = get_current_screen();

			if ( ! is_object( $current_screen ) ) {
				return false;
			}

			return 'sp_wcslider' === $current_screen->post_type;
		}

		/**
		 * Determines whether the notice should be shown to the current user.
		 *
		 * Dismissing writes a site-wide option, so the same permission that
		 * guards the plugin UI guards the notice. The pro plugin keeps this one
		 * from loading at all when both are active, so no cross-plugin check is
		 * needed to keep the rename from being announced twice.
		 *
		 * @return bool
		 */
		public function should_render() {
			if ( ! $this->is_plugin_screen() || ! current_user_can( 'manage_options' ) ) {
				return false;
			}

			return ! get_option( self::DISMISSED_OPTION_KEY );
		}

		/**
		 * Loads the notice styles.
		 *
		 * Enqueued here rather than while rendering the notice, because styles
		 * enqueued during admin_notices are printed in the footer and the bar
		 * would flash unstyled first.
		 *
		 * @return void
		 */
		public function enqueue_rebrand_notice_style() {
			if ( ! $this->should_render() ) {
				return;
			}

			wp_enqueue_style( 'woo-category-slider-grid-notices', SP_WCS_URL . 'admin/partials/notices/notices.min.css', array(), SP_WCS_VERSION, 'all' );
		}

		/**
		 * Renders the rebrand notice on the page.
		 *
		 * @return void
		 */
		public function render_rebrand_notice() {
			if ( ! $this->should_render() ) {
				return;
			}

			$nonce = wp_create_nonce( 'sp_wcsp_rebrand_notice_dismiss' );
			?>
			<div id="wcs-rebrand-notice" class="wcs-rebrand-notice">
				<div class="wcs-rebrand-notice-content">
					<img class="wcs-rebrand-notice-icon" src="<?php echo esc_url( SP_WCS_URL . 'admin/img/rebrand-notice-icon.gif' ); ?>" alt="" width="18" height="18">
					<p class="wcs-rebrand-notice-text">
						<?php
						printf(
							/* translators: 1: old plugin name, 2: new plugin name. */
							esc_html__( '%1$s is now %2$s — Everything else remains the same. Why we rebranded', 'woo-category-slider-grid' ),
							'<strong>' . esc_html__( 'WooCategory', 'woo-category-slider-grid' ) . '</strong>',
							'<strong>' . esc_html__( 'Reno Product Category', 'woo-category-slider-grid' ) . '</strong>'
						);
						?>
					</p>
					<a class="wcs-rebrand-notice-link" href="<?php echo esc_url( self::ANNOUNCEMENT_URL ); ?>" target="_blank" rel="noopener noreferrer">
						<span class="screen-reader-text"><?php esc_html_e( 'Read more about the rebranding', 'woo-category-slider-grid' ); ?></span>
						<svg width="14" height="14" viewBox="0 0 14 14" fill="none" aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg">
							<path d="M5.25 2.625H2.625A1.167 1.167 0 0 0 1.458 3.79v7.583a1.167 1.167 0 0 0 1.167 1.167h7.583a1.167 1.167 0 0 0 1.167-1.167V8.75" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/>
							<path d="M8.75 1.458h3.792V5.25M6.125 7.875l6.417-6.417" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/>
						</svg>
					</a>
				</div>

				<button type="button" class="wcs-rebrand-notice-dismiss" data-nonce="<?php echo esc_attr( $nonce ); ?>">
					<span class="screen-reader-text"><?php esc_html_e( 'Dismiss this notice', 'woo-category-slider-grid' ); ?></span>
					<svg width="14" height="14" viewBox="0 0 14 14" fill="none" aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg">
						<path d="M10.5 3.5 3.5 10.5M3.5 3.5l7 7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
					</svg>
				</button>
			</div>

			<script type="text/javascript">
				(function($){
					$(document).on('click', '#wcs-rebrand-notice .wcs-rebrand-notice-dismiss', function(e){
						e.preventDefault();
						$.post(ajaxurl, {
							action: 'sp_wcsp_dismiss_rebrand_notice',
							nonce: $(this).data('nonce')
						});
						$('#wcs-rebrand-notice').fadeOut(300);
					});
				})(jQuery);
			</script>
			<?php
		}

		/**
		 * Handles the AJAX request to dismiss the rebrand notice.
		 *
		 * @return void
		 */
		public function dismiss_rebrand_notice() {
			check_ajax_referer( 'sp_wcsp_rebrand_notice_dismiss', 'nonce' );

			if ( ! current_user_can( 'manage_options' ) ) {
				wp_send_json_error( array( 'error' => esc_html__( 'Authorization failed!', 'woo-category-slider-grid' ) ), 401 );
			}

			// Not autoloaded: only ever read on this plugin's admin screens.
			update_option( self::DISMISSED_OPTION_KEY, 1, false );

			wp_send_json_success();
		}
	}

	// Initialize the rebrand notice.
	Woo_Category_Slider_Rebrand_Notice::instance();
}
