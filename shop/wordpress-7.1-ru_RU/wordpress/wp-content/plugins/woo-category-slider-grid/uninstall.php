<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * When populating this file, consider the following flow
 * of control:
 *
 * - This method should be static
 * - Check if the $_REQUEST content actually is the plugin name
 * - Run an admin referrer check to make sure it goes through authentication
 * - Verify the output of $_GET makes sense
 * - Repeat with other user roles. Best directly by using the links/query string parameters.
 * - Repeat things for multisite. Once for a single site in the network, once sitewide.
 *
 * This file may be updated more in future version of the Boilerplate; however, this is the
 * general skeleton and outline for how the file should work.
 *
 * For more information, see the following discussion:
 * https://github.com/tommcfarlin/WordPress-Plugin-Boilerplate/pull/123#issuecomment-28541913
 *
 * @link       https://shapedplugin.com/
 * @since      1.1.0
 *
 * @package    Woo_Category_Slider
 * @author     ShapedPlugin <support@shapedplugin.com>
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

/**
 * Delete plugin data function.
 *
 * @return void
 */
function sp_wcsp_delete_plugin_data() {

	// Delete plugin option settings.
	$option_name = 'sp_wcsp_settings';
	delete_option( $option_name );
	delete_site_option( $option_name ); // For site options in Multisite.

	// Delete slider post type.
	$slider_posts = get_posts(
		array(
			'numberposts' => -1,
			'post_type'   => 'sp_wcslider',
			'post_status' => 'any',
		)
	);
	foreach ( $slider_posts as $post ) {
		wp_delete_post( $post->ID, true );
	}

	// Delete slider post meta.
	delete_post_meta_by_key( 'sp_wcsp_layout_options' );
	delete_post_meta_by_key( 'sp_wcsp_shortcode_options' );

	// Delete the rebrand notice dismissed state.
	delete_option( 'sp_wcsp_rebrand_notice_dismissed' );

	// Delete offer banner related option keys.
	delete_option( 'shapedplugin_offer_banner_dismissed_black_friday_2025' );
	delete_option( 'shapedplugin_offer_banner_dismissed_new_year_2026' );
}

// Load WCS file.
require plugin_dir_path( __FILE__ ) . '/woo-category-slider-grid.php';
$wcsp_options     = get_option( 'sp_wcsp_settings' );
$wcsp_plugin_data = isset( $wcsp_options['wcsp_delete_all_data'] ) ? $wcsp_options['wcsp_delete_all_data'] : false;

if ( $wcsp_plugin_data ) {
	sp_wcsp_delete_plugin_data();
}
