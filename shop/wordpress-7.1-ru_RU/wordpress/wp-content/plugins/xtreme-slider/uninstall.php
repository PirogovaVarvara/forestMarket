<?php
// Only run when WP triggers uninstall.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

global $wpdb;

// Drop custom tables.
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.SchemaChange -- Uninstall cleanup.
$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}xs_slides" );
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.SchemaChange -- Uninstall cleanup.
$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}xs_sliders" );

// Remove options.
delete_option( 'xs_settings' );
delete_option( 'xs_db_version' );
