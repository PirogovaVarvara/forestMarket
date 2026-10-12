<?php
defined( 'ABSPATH' ) || exit;

class XS_Admin {

	public function __construct() {
		add_action( 'admin_menu',            array( $this, 'register_menus' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		add_filter( 'admin_body_class',      array( $this, 'add_body_class' ) );
		add_action( 'admin_init',            array( $this, 'handle_form_submissions' ) );
	}

	/**
	 * Process form submissions before headers are sent.
	 * wp_safe_redirect() must run before WordPress outputs the page.
	 */
	public function handle_form_submissions() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only routing; nonce verified inside each handler.
		$page   = isset( $_GET['page'] )   ? sanitize_text_field( wp_unslash( $_GET['page'] ) )   : '';
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only routing; nonce verified inside each handler.
		$action = isset( $_GET['action'] ) ? sanitize_text_field( wp_unslash( $_GET['action'] ) ) : '';

		// Delete action (GET-based) — must run before the POST check.
		if (
			'xtreme-slider' === $page
			&& 'delete' === $action
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Nonce verified inside process_delete().
			&& isset( $_GET['slider_id'], $_GET['_wpnonce'] )
		) {
			XS_Admin_Sliders::process_delete();
		}

		// POST form submissions.
		if ( ! isset( $_SERVER['REQUEST_METHOD'] ) || 'POST' !== strtoupper( sanitize_text_field( wp_unslash( $_SERVER['REQUEST_METHOD'] ) ) ) ) {
			return;
		}

		// Edit page save.
		// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce verified inside process_save().
		if ( 'xs-edit' === $page && isset( $_POST['xs_edit_nonce'] ) ) {
			XS_Admin_Edit::process_save();
		}

		// Settings page save.
		// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce verified inside process_save().
		if ( 'xs-settings' === $page && isset( $_POST['xs_settings_nonce'] ) ) {
			XS_Admin_Settings::process_save();
		}
	}

	public function register_menus() {
		add_menu_page(
			'XtremeSlider',
			'Xtreme Slider',
			'manage_options',
			'xtreme-slider',
			array( 'XS_Admin_Sliders', 'render' ),
			'data:image/svg+xml;base64,' . base64_encode( '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#00d4ff"><path d="M21 3H3c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h18c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H3V5h18v14zM5 15l3.5-4.5 2.5 3.01L14.5 9l4.5 6H5z"/></svg>' ),
			56
		);

		add_submenu_page( 'xtreme-slider', __( 'All Sliders', 'xtreme-slider' ), __( 'All Sliders', 'xtreme-slider' ), 'manage_options', 'xtreme-slider',    array( 'XS_Admin_Sliders',  'render' ) );
		add_submenu_page( 'xtreme-slider', __( 'Settings', 'xtreme-slider' ),    __( 'Settings', 'xtreme-slider' ),    'manage_options', 'xs-settings',      array( 'XS_Admin_Settings', 'render' ) );

		if ( xs_can_create_slider() ) {
			add_submenu_page( 'xtreme-slider', __( 'Add New', 'xtreme-slider' ), __( 'Add New', 'xtreme-slider' ), 'manage_options', 'xs-edit', array( 'XS_Admin_Edit', 'render' ) );
		}
	}

	public function add_body_class( $classes ) {
		$screen = get_current_screen();
		if ( $screen && $this->is_xs_page( $screen->id ) ) {
			$classes .= ' xs-admin-page';
		}
		return $classes;
	}

	public function enqueue_assets( $hook ) {
		if ( ! $this->is_xs_page( $hook ) ) {
			return;
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only route parameter used to calculate the current editor limit.
		$page      = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '';
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only route parameter used to calculate the current editor limit.
		$slider_id = isset( $_GET['slider_id'] ) ? absint( wp_unslash( $_GET['slider_id'] ) ) : 0;
		$max_slides = 'xs-edit' === $page ? xs_get_edit_max_slides( $slider_id ) : xs_get_max_slides();

		wp_enqueue_media();
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_style( 'xs-admin', XS_PLUGIN_URL . 'assets/css/admin.css', array(), XS_VERSION );
		wp_enqueue_script( 'xs-admin', XS_PLUGIN_URL . 'assets/js/admin.js', array( 'jquery', 'jquery-ui-sortable', 'wp-color-picker' ), XS_VERSION, true );
		wp_localize_script( 'xs-admin', 'xsAdmin', array(
			'ajaxUrl'     => admin_url( 'admin-ajax.php' ),
			'nonce'       => wp_create_nonce( 'xs_admin_nonce' ),
			'maxSlides'   => $max_slides,
			'isPremium'   => xs_is_premium_license_active(),
			'pluginUrl'   => XS_PLUGIN_URL,
			'loadTitles'  => array(
				'empty'   => __( 'Add images first.', 'xtreme-slider' ),
				'single'  => __( '1 title loaded', 'xtreme-slider' ),
				/* translators: %d: number of slide titles that were filled in */
				'multiple'=> __( '%d titles loaded', 'xtreme-slider' ),
				'none'    => __( 'No filenames found', 'xtreme-slider' ),
			),
		) );
	}

	private function is_xs_page( $hook ) {
		$xs_hooks = array(
			'toplevel_page_xtreme-slider',
			'xtreme-slider_page_xs-edit',
			'xtreme-slider_page_xs-settings',
		);
		return in_array( $hook, $xs_hooks, true );
	}
}
