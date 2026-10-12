<?php
/**
 * Reno Product Category
 *
 * @link              https://shapedplugin.com/
 * @since             1.0.0
 * @package           Woo_Category_Slider
 *
 * Plugin Name:       Reno Product Category (formerly WooCategory)
 * Plugin URI:        https://shapedplugin.com/reno-product-category/?ref=115
 * Description:       Reno Product Category helps you display WooCommerce Categories aesthetically in a Slider, Grid, Hierarchy Grid, or Inline layouts. You can manage and show your product categories with thumbnail, child category (beside), description, shop now button with an easy to use shortcode generator interface with many handy options.
 * Version:           1.7.2
 * Author:            ShapedPlugin LLC
 * Author URI:        https://shapedplugin.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       woo-category-slider-grid
 * Domain Path:       /languages
 * Requires Plugins: woocommerce
 * Requires at least: 5.0
 * Requires PHP: 7.0
 * WC requires at least: 4.5
 * WC tested up to: 11.0.1
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 */
class Woo_Category_Slider {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Woo_Category_Slider_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name = 'woo-category-slider-grid';

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version = '1.7.2';

	/**
	 * Holds class object
	 *
	 * @var object
	 *
	 * @since 1.0.0
	 */
	private static $instance;

	/**
	 * Initialize the Woo_Category_Slider() class
	 *
	 * @since 1.0.0
	 * @return object
	 */
	public static function init() {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Woo_Category_Slider ) ) {
			self::$instance = new Woo_Category_Slider();
			self::$instance->setup();
		}
		return self::$instance;
	}

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function setup() {
		$this->define_constants();
		$this->load_dependencies();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	/**
	 * Define plugin constants.
	 *
	 * @since 1.0
	 */
	public function define_constants() {
		define( 'SP_WCS_VERSION', $this->version );
		define( 'SP_WCS_PLUGIN_NAME', $this->plugin_name );
		define( 'SP_WCS_PATH', plugin_dir_path( __FILE__ ) );
		define( 'SP_WCS_URL', plugin_dir_url( __FILE__ ) );
		define( 'SP_WCS_BASENAME', plugin_basename( __FILE__ ) );
		define( 'SP_WCS_INCLUDES', SP_WCS_PATH . '/includes' );
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Woo_Category_Slider_Loader. Orchestrates the hooks of the plugin.
	 * - Woo_Category_Slider_Admin. Defines all hooks for the admin area.
	 * - Woo_Category_Slider_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once SP_WCS_INCLUDES . '/class-woo-category-slider-loader.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once SP_WCS_PATH . 'admin/class-woo-category-slider-admin.php';

		/**
		* The class responsible for defining all actions that occur in the admin area.
		*/
		require_once SP_WCS_PATH . 'admin/class-category-slider-gutenberg-block.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once SP_WCS_PATH . 'public/class-woo-category-slider-public.php';
		require_once SP_WCS_INCLUDES . '/class-woo-category-slider-updates.php';
		require_once SP_WCS_INCLUDES . '/class-woo-category-slider-post-types.php';
		require_once SP_WCS_INCLUDES . '/class-woo-category-slider-shortcode.php';
		require_once SP_WCS_PATH . 'admin/help-page/help-page.php';
		require_once SP_WCS_INCLUDES . '/class-woo-category-slider-widget.php';
		require_once SP_WCS_INCLUDES . '/class-woo-category-slider-import-export.php';
		require_once SP_WCS_PATH . 'admin/partials/wcsp-framework/classes/setup.class.php';
		require_once SP_WCS_PATH . 'admin/partials/notices/review.php';
		require_once SP_WCS_PATH . 'admin/partials/notices/rebrand-notice.php';
		require_once SP_WCS_PATH . 'admin/preview/class-woo-category-slider-preview.php';

		$this->loader = new Woo_Category_Slider_Loader();
	}


	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin  = new Woo_Category_Slider_Admin( $this->get_plugin_name(), $this->get_version() );
		$plugin_cpt    = new Woo_Category_Slider_Post_Type( $this->get_plugin_name(), $this->get_version() );
		$import_export = new Woo_Category_Slider_Import_Export( $this->get_version(), $this->get_plugin_name() );

		$this->loader->add_action( 'wp_ajax_wcsp_export_shortcodes', $import_export, 'export_shortcodes' );
		$this->loader->add_action( 'wp_ajax_wcsp_import_shortcodes', $import_export, 'import_shortcodes' );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'admin_enqueue_scripts' );
		$this->loader->add_action( 'init', $plugin_cpt, 'register_post_type' );
		$this->loader->add_action( 'manage_sp_wcslider_posts_custom_column', $plugin_admin, 'add_shortcode_form', 10, 2 );
		$this->loader->add_filter( 'manage_sp_wcslider_posts_columns', $plugin_admin, 'add_shortcode_column' );
		$this->loader->add_filter( 'plugin_action_links', $plugin_admin, 'add_plugin_action_links', 10, 2 );
		$this->loader->add_filter( 'plugin_row_meta', $plugin_admin, 'plugin_row_meta', 10, 2 );
		$this->loader->add_filter( 'post_updated_messages', $plugin_admin, 'post_update_message' );
		$this->loader->add_filter( 'admin_footer_text', $plugin_admin, 'admin_footer', 1, 2 );
		$this->loader->add_filter( 'update_footer', $plugin_admin, 'admin_footer_version', 11 );

		// Redirect after active.
		$this->loader->add_action( 'activated_plugin', $plugin_admin, 'redirect_to' );

		// WooCommerce plugin is not installed notice.
		if ( empty( get_option( 'sp-wcsp-woo-notice-dismissed' ) ) ) {
			$this->loader->add_action( 'admin_notices', $plugin_admin, 'admin_notice' );
		}
		$this->loader->add_action( 'wp_ajax_dismiss_woo_notice', $plugin_admin, 'dismiss_woo_notice' );
		$this->loader->add_action( 'before_woocommerce_init', $plugin_admin, 'declare_compatibility_with_woo_hpos_feature' );

		if ( version_compare( $GLOBALS['wp_version'], '5.3', '>=' ) ) {
			// Gutenberg block.
			new Woo_Category_Slider_Gutenberg_Block();
		}

		// Elementor shortcode addons.
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
		if ( ( is_plugin_active( 'elementor/elementor.php' ) || is_plugin_active_for_network( 'elementor/elementor.php' ) ) ) {
			require_once SP_WCS_PATH . 'admin/class-woo-category-slider-element-shortcode-addons.php';
			require_once SP_WCS_PATH . 'admin/class-woo-category-slider-element-shortcode-addons-deprecated.php';
		}
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public    = new Woo_Category_Slider_Public( $this->get_plugin_name(), $this->get_version() );
		$plugin_shortcode = new Woo_Category_Slider_Shortcode( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'wp_loaded', $plugin_public, 'register_all_scripts' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'save_post', $plugin_public, 'delete_page_wcs_option_on_save' );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Woo_Category_Slider_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}
}


// End of the class.
if ( ! function_exists( 'woocatslider' ) ) {
	/**
	 * Shortcode converter function
	 *
	 * @param  int $post_id shortcode id.
	 * @return void
	 */
	function woocatslider( $post_id ) {
		echo do_shortcode( '[woocatslider id="' . $post_id . '"]' );
	}
}


/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_woo_category_slider() {

	$plugin = Woo_Category_Slider::init();
	$plugin->run();

	if ( ! defined( 'SHAPEDPLIUGIN_OFFER_BANNER_LOADED' ) ) {
		define( 'SHAPEDPLIUGIN_OFFER_BANNER_LOADED', true );

		/**
		 * The class responsible for generating admin offer banner.
		 */
		include_once plugin_dir_path( __FILE__ ) . '/admin/partials/notices/ShapedPlugin_Offer_Banner.php';
	}
}

require_once ABSPATH . 'wp-admin/includes/plugin.php';
if ( ! ( is_plugin_active( 'woo-category-slider-pro/woo-category-slider-pro.php' ) || is_plugin_active_for_network( 'woo-category-slider-pro/woo-category-slider-pro.php' ) ) ) {
	run_woo_category_slider();
}
