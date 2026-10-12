<?php
defined( 'ABSPATH' ) || exit;

class XS_Plugin {

	private static $instance = null;

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
			self::$instance->init();
		}
		return self::$instance;
	}

	private function init() {
		$this->load_helpers();

		if ( is_admin() ) {
			$this->load_admin();
		}

		$this->load_frontend();
	}

	private function load_helpers() {
		require_once XS_PLUGIN_PATH . 'inc/functions.php';
	}

	private function load_admin() {
		require_once XS_PLUGIN_PATH . 'inc/admin/class-admin.php';
		require_once XS_PLUGIN_PATH . 'inc/admin/class-admin-sliders.php';
		require_once XS_PLUGIN_PATH . 'inc/admin/class-admin-edit.php';
		require_once XS_PLUGIN_PATH . 'inc/admin/class-admin-settings.php';
		require_once XS_PLUGIN_PATH . 'inc/admin/class-dashboard-widget.php';
		new XS_Admin();
		new XS_Dashboard_Widget();
	}

	private function load_frontend() {
		require_once XS_PLUGIN_PATH . 'inc/front/class-shortcode.php';
		require_once XS_PLUGIN_PATH . 'inc/front/class-renderer.php';
		new XS_Shortcode();
	}
}
