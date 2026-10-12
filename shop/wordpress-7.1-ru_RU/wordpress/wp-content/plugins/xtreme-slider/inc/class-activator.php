<?php
defined( 'ABSPATH' ) || exit;

class XS_Activator {

	public static function activate() {
		self::create_tables();
		self::set_defaults();
		flush_rewrite_rules();
		update_option( 'xs_db_version', XS_DB_VERSION );
	}

	private static function create_tables() {
		global $wpdb;
		$c = $wpdb->get_charset_collate();
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		dbDelta( "CREATE TABLE {$wpdb->prefix}xs_sliders (
			id               BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			title            VARCHAR(255) NOT NULL DEFAULT '',
			layout           ENUM('default','cool','3d','options') NOT NULL DEFAULT 'default',
			visible_count    TINYINT(3) UNSIGNED NOT NULL DEFAULT 3,
			autoplay         TINYINT(1) NOT NULL DEFAULT 0,
			autoplay_speed   INT UNSIGNED NOT NULL DEFAULT 4000,
			fullscreen       TINYINT(1) NOT NULL DEFAULT 0,
			image_ratio      VARCHAR(10) NOT NULL DEFAULT '16:10',
			fixed_height     SMALLINT UNSIGNED NOT NULL DEFAULT 0,
			title_shadow     TINYINT(1) NOT NULL DEFAULT 0,
			square_corners   TINYINT(1) NOT NULL DEFAULT 0,
			black_arrows     TINYINT(1) NOT NULL DEFAULT 0,
			link_hover_color VARCHAR(7) NOT NULL DEFAULT '#ee212b',
			gradient_start   VARCHAR(7) NOT NULL DEFAULT '#ec38bc',
			gradient_end     VARCHAR(7) NOT NULL DEFAULT '#7303c0',
			bg_color_3d      VARCHAR(7) NOT NULL DEFAULT '#0a0a0a',
			bg_color_options VARCHAR(7) NOT NULL DEFAULT '',
			status           ENUM('active','draft') NOT NULL DEFAULT 'active',
			created_at       DATETIME NOT NULL,
			updated_at       DATETIME NOT NULL,
			PRIMARY KEY  (id),
			KEY status (status)
		) $c;" );

		dbDelta( "CREATE TABLE {$wpdb->prefix}xs_slides (
			id           BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			slider_id    BIGINT(20) UNSIGNED NOT NULL,
			image_id     BIGINT(20) UNSIGNED NOT NULL DEFAULT 0,
			image_url    TEXT DEFAULT NULL,
			title        VARCHAR(255) NOT NULL DEFAULT '',
			caption      VARCHAR(255) NOT NULL DEFAULT '',
			description  TEXT DEFAULT NULL,
			link_url     TEXT DEFAULT NULL,
			html_content LONGTEXT DEFAULT NULL,
			sort_order   INT NOT NULL DEFAULT 0,
			created_at   DATETIME NOT NULL,
			PRIMARY KEY  (id),
			KEY slider_id (slider_id),
			KEY sort_order (sort_order)
		) $c;" );
	}

	private static function set_defaults() {
		if ( ! get_option( 'xs_settings' ) ) {
			update_option( 'xs_settings', array(
				'defaults' => array(
					'layout'         => 'default',
					'visible_count'  => 3,
					'autoplay'       => 0,
					'autoplay_speed' => 4000,
					'fullscreen'     => 0,
					'image_ratio'    => '16:10',
					'gradient_start' => '#ec38bc',
					'gradient_end'   => '#7303c0',
					'bg_color_3d'    => '#0a0a0a',
				),
				'license' => array(
					'code' => '',
				),
			) );
		}
	}

	public static function maybe_upgrade() {
		if ( ! self::storage_needs_upgrade() ) {
			return;
		}

		self::create_tables();
		self::ensure_schema();
		update_option( 'xs_db_version', XS_DB_VERSION );
	}

	public static function storage_ready() {
		global $wpdb;

		$sliders_table = $wpdb->prefix . 'xs_sliders';
		$slides_table  = $wpdb->prefix . 'xs_slides';

		$slider_columns = array(
			'id',
			'title',
			'layout',
			'visible_count',
			'autoplay',
			'autoplay_speed',
			'fullscreen',
			'image_ratio',
			'fixed_height',
			'title_shadow',
			'square_corners',
			'black_arrows',
			'link_hover_color',
			'gradient_start',
			'gradient_end',
			'bg_color_3d',
			'bg_color_options',
			'status',
			'created_at',
			'updated_at',
		);

		$slide_columns = array(
			'id',
			'slider_id',
			'image_id',
			'image_url',
			'title',
			'caption',
			'description',
			'link_url',
			'html_content',
			'sort_order',
			'created_at',
		);

		if ( ! self::table_exists( $sliders_table ) || ! self::table_exists( $slides_table ) ) {
			return false;
		}

		foreach ( $slider_columns as $column ) {
			if ( ! self::column_exists( $sliders_table, $column ) ) {
				return false;
			}
		}

		foreach ( $slide_columns as $column ) {
			if ( ! self::column_exists( $slides_table, $column ) ) {
				return false;
			}
		}

		return true;
	}

	private static function storage_needs_upgrade() {
		return get_option( 'xs_db_version' ) !== XS_DB_VERSION || ! self::storage_ready();
	}

	private static function ensure_schema() {
		global $wpdb;

		$sliders_table = $wpdb->prefix . 'xs_sliders';
		$slides_table  = $wpdb->prefix . 'xs_slides';

		$slider_alters = array(
			'fixed_height'     => "ADD COLUMN `fixed_height` SMALLINT UNSIGNED NOT NULL DEFAULT 0 AFTER `image_ratio`",
			'title_shadow'     => "ADD COLUMN `title_shadow` TINYINT(1) NOT NULL DEFAULT 0 AFTER `fixed_height`",
			'square_corners'   => "ADD COLUMN `square_corners` TINYINT(1) NOT NULL DEFAULT 0 AFTER `title_shadow`",
			'black_arrows'     => "ADD COLUMN `black_arrows` TINYINT(1) NOT NULL DEFAULT 0 AFTER `square_corners`",
			'link_hover_color' => "ADD COLUMN `link_hover_color` VARCHAR(7) NOT NULL DEFAULT '#ee212b' AFTER `black_arrows`",
			'gradient_start'   => "ADD COLUMN `gradient_start` VARCHAR(7) NOT NULL DEFAULT '#ec38bc' AFTER `link_hover_color`",
			'gradient_end'     => "ADD COLUMN `gradient_end` VARCHAR(7) NOT NULL DEFAULT '#7303c0' AFTER `gradient_start`",
			'bg_color_3d'      => "ADD COLUMN `bg_color_3d` VARCHAR(7) NOT NULL DEFAULT '#0a0a0a' AFTER `gradient_end`",
			'bg_color_options' => "ADD COLUMN `bg_color_options` VARCHAR(7) NOT NULL DEFAULT '' AFTER `bg_color_3d`",
			'updated_at'       => "ADD COLUMN `updated_at` DATETIME NOT NULL AFTER `created_at`",
		);

		foreach ( $slider_alters as $column => $sql ) {
			if ( ! self::column_exists( $sliders_table, $column ) ) {
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.DirectDatabaseQuery.SchemaChange,WordPress.DB.PreparedSQL.InterpolatedNotPrepared,PluginCheck.Security.DirectDB.UnescapedDBParameter
				$wpdb->query( "ALTER TABLE `{$sliders_table}` {$sql}" );
			}
		}

		$slide_alters = array(
			'caption'      => "ADD COLUMN `caption` VARCHAR(255) NOT NULL DEFAULT '' AFTER `title`",
			'description'  => "ADD COLUMN `description` TEXT DEFAULT NULL AFTER `caption`",
			'html_content' => "ADD COLUMN `html_content` LONGTEXT DEFAULT NULL AFTER `link_url`",
		);

		// Expand layout ENUM to include 'options'.
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.DirectDatabaseQuery.SchemaChange,WordPress.DB.PreparedSQL.InterpolatedNotPrepared,PluginCheck.Security.DirectDB.UnescapedDBParameter
		$wpdb->query( "ALTER TABLE `{$sliders_table}` MODIFY COLUMN `layout` ENUM('default','cool','3d','options') NOT NULL DEFAULT 'default'" );

		foreach ( $slide_alters as $column => $sql ) {
			if ( ! self::column_exists( $slides_table, $column ) ) {
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.DirectDatabaseQuery.SchemaChange,WordPress.DB.PreparedSQL.InterpolatedNotPrepared,PluginCheck.Security.DirectDB.UnescapedDBParameter
				$wpdb->query( "ALTER TABLE `{$slides_table}` {$sql}" );
			}
		}
	}

	private static function table_exists( $table ) {
		global $wpdb;

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
		$result = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table ) );

		return $result === $table;
	}

	private static function column_exists( $table, $column ) {
		global $wpdb;

		if ( ! self::table_exists( $table ) ) {
			return false;
		}

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.PreparedSQL.InterpolatedNotPrepared,PluginCheck.Security.DirectDB.UnescapedDBParameter
		$result = $wpdb->get_var( $wpdb->prepare( "SHOW COLUMNS FROM `{$table}` LIKE %s", $column ) );

		return $result === $column;
	}
}
