<?php
/**
 * The plugin gutenberg block Initializer.
 *
 * @link       https://shapedplugin.com/
 * @since      1.4.4
 *
 * @package    Woo_Category_Slider
 * @subpackage Woo_Category_Slider/admin
 * @author     ShapedPlugin <support@shapedplugin.com>
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Woo_Category_Slider_Gutenberg_Block_Init' ) ) {
	/**
	 * Woo_Category_Slider_Gutenberg_Block_Init class.
	 */
	class Woo_Category_Slider_Gutenberg_Block_Init {
		/**
		 * Script and style suffix
		 *
		 * @since 1.4.4
		 * @access protected
		 * @var string
		 */
		protected $suffix;

		/**
		 * Custom Gutenberg Block Initializer.
		 */
		public function __construct() {
			add_action( 'init', array( $this, 'woo_category_slider_gutenberg_shortcode_block' ) );
			add_action( 'enqueue_block_assets', array( $this, 'woo_category_slider_block_assets' ) );
		}

		/**
		 * Register block assets.
		 *
		 * Assets are enqueued in both the editor and the frontend, but the
		 * is_admin() guard ensures that the front end only loads them when
		 * a shortcode is actually present on the page.
		 *
		 * WordPress 7.1 uses an iframed editor for the post content. The
		 * iframe document is built only from the handles collected on
		 * enqueue_block_assets, so all assets that the block needs at
		 * runtime must be registered here.
		 */
		public function woo_category_slider_block_assets() {
			if ( ! is_admin() ) {
				return;
			}

			$assets_url = SP_WCS_URL . 'public/js/';
			$version    = defined( 'SP_WCS_VERSION' ) ? SP_WCS_VERSION : '1.0.0';
			$suffix     = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

			// Register scripts first.
			wp_register_script(
				'sp-wcs-swiper-js',
				$assets_url . 'swiper' . $suffix . '.js',
				array(),
				$version,
				true
			);

			wp_register_script(
				'sp-wcs-swiper-config',
				$assets_url . 'swiper-config' . $suffix . '.js',
				array( 'sp-wcs-swiper-js', 'jquery' ),
				$version,
				true
			);

			wp_register_script(
				'sp-wcs-preloader',
				$assets_url . 'preloader' . $suffix . '.js',
				array( 'jquery' ),
				$version,
				true
			);

			// Enqueue Swiper, Font Awesome, and plugin styles.
			wp_enqueue_style( 'wcsp-fontello', SP_WCS_URL . 'admin/css/fontello.min.css', array(), SP_WCS_VERSION, 'all' );
			wp_enqueue_style( 'woo-category-slider-grid-admin' );
			wp_enqueue_style( 'sp-wcs-swiper' );
			wp_enqueue_style( 'sp-wcs-font-awesome' );
			wp_enqueue_style( 'woo-category-slider-grid' );

			// Enqueue Swiper JS and plugin scripts in correct order.
			// Swiper library must be loaded before the config script.
			wp_enqueue_script( 'sp-wcs-swiper-js' );
			wp_enqueue_script( 'sp-wcs-swiper-config' );
			wp_enqueue_script( 'sp-wcs-preloader' );
		}

		/**
		 * Shortcode list.
		 *
		 * @return array
		 */
		public function woo_category_slider_pro_post_list() {
			$shortcodes = get_posts(
				array(
					'post_type'      => 'sp_wcslider',
					'post_status'    => 'publish',
					'posts_per_page' => 9999,
				)
			);

			if ( count( $shortcodes ) < 1 ) {
				return array();
			}

			return array_map(
				function ( $shortcode ) {
						return (object) array(
							'id'    => absint( $shortcode->ID ),
							'title' => esc_html( $shortcode->post_title ),
						);
				},
				$shortcodes
			);
		}

		/**
		 * Register Gutenberg shortcode block.
		 */
		public function woo_category_slider_gutenberg_shortcode_block() {

			$asset_file = include plugin_dir_path( __FILE__ ) . 'build/index.asset.php';

			/**
			 * Register block editor js file enqueue for backend.
			 */
			wp_register_script(
				'woo-category-slider-shortcode-block',
				plugins_url( '/GutenbergBlock/build/index.js', __DIR__ ),
				$asset_file['dependencies'],
				$asset_file['version'],
				true
			);

			wp_localize_script(
				'woo-category-slider-shortcode-block',
				'sp_woo_category_slider_load_script',
				array(
					'ajax_url'      => admin_url( 'admin-ajax.php' ),
					'path'          => SP_WCS_URL,
					'url'           => admin_url( 'post-new.php?post_type=sp_wcslider' ),
					'shortCodeList' => $this->woo_category_slider_pro_post_list(),
				)
			);

			/**
			 * Register Gutenberg block on server-side.
			 */
			register_block_type(
				'woo-category-slider/shortcode',
				array(
					'attributes'      => array(
						'shortcode'          => array(
							'type'    => 'string',
							'default' => '',
						),
						'showInputShortcode' => array(
							'type'    => 'boolean',
							'default' => true,
						),
						'preview'            => array(
							'type'    => 'boolean',
							'default' => false,
						),
						'is_admin'           => array(
							'type'    => 'boolean',
							'default' => is_admin(),
						),
					),
					'example'         => array(
						'attributes' => array(
							'preview' => true,
						),
					),
					'editor_script'   => 'woo-category-slider-shortcode-block',
					'render_callback' => array( $this, 'woo_category_slider_pro_render_shortcode' ),
				)
			);
		}

		/**
		 * Render callback.
		 *
		 * @param string $attributes Shortcode.
		 * @return string
		 */
		public function woo_category_slider_pro_render_shortcode( $attributes ) {

			if ( $attributes['preview'] ) {
				return '<div class="sp_wcs_shortcode_block_preview_image"><img src="' . SP_WCS_URL . 'src/admin/GutenbergBlock/src/wp-team-block-preview.svg"/></div>';
			}

			if ( is_null( $attributes['shortcode'] ) || '' === $attributes['shortcode'] ) {
				return '<i></i>';
			}

			if ( ! $attributes['is_admin'] ) {
				return do_shortcode( '[woocatslider id="' . sanitize_text_field( $attributes['shortcode'] ) . '"]' );
			}
			$edit_page_link = get_edit_post_link( sanitize_text_field( $attributes['shortcode'] ) );

			return '<div id="' . uniqid() . '"><a href="' . esc_url( $edit_page_link ) . '" target="_blank" class="wcsp-block-edit-button">' . esc_html__( 'Edit Category View', 'woo-category-slider-grid' ) . '</a>' . do_shortcode( '[woocatslider id="' . sanitize_text_field( $attributes['shortcode'] ) . '"]' ) . '</div>';
		}
	}
}
