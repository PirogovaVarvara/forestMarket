<?php
defined( 'ABSPATH' ) || exit;

/**
 * Get a plugin setting value.
 *
 * @param string $group   Setting group key.
 * @param string $key     Setting key within the group.
 * @param mixed  $default Fallback if not set.
 * @return mixed
 */
function xs_setting( $group, $key, $default = null ) {
	$settings = get_option( 'xs_settings', array() );
	return isset( $settings[ $group ][ $key ] ) ? $settings[ $group ][ $key ] : $default;
}

/**
 * Get the host used to validate a site-bound premium license.
 *
 * @param string $site_url Optional URL override.
 * @return string
 */
function xs_get_license_host( $site_url = '' ) {
	$site_url = $site_url ? $site_url : home_url( '/' );
	$host     = wp_parse_url( $site_url, PHP_URL_HOST );
	$host     = is_string( $host ) ? strtolower( $host ) : '';

	return preg_replace( '/^www\./', '', $host );
}

/**
 * Normalize a license code for storage/validation.
 *
 * @param string $code Raw license code.
 * @return string
 */
function xs_normalize_license_code( $code ) {
	return strtoupper( preg_replace( '/[^A-Z0-9]/', '', (string) $code ) );
}

/**
 * Format a license code for display.
 *
 * @param string $code Raw or normalized license code.
 * @return string
 */
function xs_format_license_code( $code ) {
	$normalized = xs_normalize_license_code( $code );
	if ( '' === $normalized ) {
		return '';
	}

	$prefix = 'XSPRO';
	if ( 0 === strpos( $normalized, $prefix ) ) {
		$normalized = substr( $normalized, strlen( $prefix ) );
	}

	if ( '' === $normalized ) {
		return '';
	}

	$chunks = str_split( substr( $normalized, 0, 20 ), 4 );

	return $prefix . '-' . implode( '-', $chunks );
}

/**
 * Generate the valid premium license code for the current site host.
 *
 * @param string $site_url Optional URL override.
 * @return string
 */
function xs_generate_premium_license( $site_url = '' ) {
	$host = xs_get_license_host( $site_url );
	if ( '' === $host ) {
		return '';
	}

	$signature = strtoupper(
		substr(
			hash_hmac( 'sha256', $host . '|xtreme-slider|premium|50', 'xs-premium-2026-b91f4e7c2a5d' ),
			0,
			20
		)
	);

	return xs_format_license_code( 'XSPRO' . $signature );
}

/**
 * Check whether premium is active for the supplied or saved license code.
 *
 * @param string|null $code Optional license code override.
 * @return bool
 */
function xs_is_premium_license_active( $code = null ) {
	$code     = null === $code ? xs_setting( 'license', 'code', '' ) : $code;
	$expected = xs_normalize_license_code( xs_generate_premium_license() );
	$current  = xs_normalize_license_code( $code );

	return '' !== $expected && '' !== $current && hash_equals( $expected, $current );
}

/**
 * Get the slider-count limit for the current license tier.
 *
 * A value of 0 means unlimited.
 *
 * @return int
 */
function xs_get_max_slider_count() {
	return xs_is_premium_license_active() ? 0 : 2;
}

/**
 * Get a human-readable slider-count limit label.
 *
 * @param bool|null $is_premium Optional premium override.
 * @return string
 */
function xs_get_slider_limit_label( $is_premium = null ) {
	$is_premium = null === $is_premium ? xs_is_premium_license_active() : (bool) $is_premium;
	$limit      = $is_premium ? 0 : 2;

	if ( $limit <= 0 ) {
		return __( 'Unlimited sliders', 'xtreme-slider' );
	}

	return sprintf(
		/* translators: %d: slider count limit */
		_n( '%d slider', '%d sliders', $limit, 'xtreme-slider' ),
		$limit
	);
}

/**
 * Get the slide limit for the current license tier.
 *
 * @return int
 */
function xs_get_max_slides() {
	return xs_is_premium_license_active() ? 50 : 10;
}

/**
 * Get the visible-slide limit for the current license tier.
 *
 * @return int
 */
function xs_get_max_visible_slides() {
	return xs_is_premium_license_active() ? 15 : 6;
}

/**
 * Get available image ratio choices for a given admin context.
 *
 * The premium-only "default" ratio stays available for previously saved
 * sliders/settings so edits never silently downgrade an existing value.
 *
 * @param string    $context       editor or settings.
 * @param string    $current_ratio Current saved ratio.
 * @param bool|null $is_premium    Optional premium override.
 * @return array<string,string>
 */
function xs_get_image_ratio_options( $context = 'editor', $current_ratio = '16:10', $is_premium = null ) {
	$current_ratio = sanitize_text_field( (string) $current_ratio );
	$is_premium    = null === $is_premium ? xs_is_premium_license_active() : (bool) $is_premium;

	$options = array(
		'16:10' => __( '16:10 (Landscape)', 'xtreme-slider' ),
		'1:1'   => __( '1:1 (Square)', 'xtreme-slider' ),
	);

	if ( $is_premium || 'default' === $current_ratio ) {
		$options['default'] = __( 'Default (Original ratio)', 'xtreme-slider' );
	}

	if ( 'editor' === $context ) {
		$options['fixed'] = __( 'Fixed Height', 'xtreme-slider' );
	}

	return $options;
}

/**
 * Sanitize an image ratio against the allowed choices for the current context.
 *
 * @param string    $ratio         Submitted ratio.
 * @param string    $context       editor or settings.
 * @param string    $current_ratio Current saved ratio.
 * @param bool|null $is_premium    Optional premium override.
 * @return string
 */
function xs_sanitize_image_ratio( $ratio, $context = 'editor', $current_ratio = '16:10', $is_premium = null ) {
	$ratio         = sanitize_text_field( (string) $ratio );
	$current_ratio = sanitize_text_field( (string) $current_ratio );
	$options       = xs_get_image_ratio_options( $context, $current_ratio, $is_premium );

	if ( isset( $options[ $ratio ] ) ) {
		return $ratio;
	}

	if ( isset( $options[ $current_ratio ] ) ) {
		return $current_ratio;
	}

	return '16:10';
}

/**
 * Get a slider by ID.
 *
 * @param int $slider_id
 * @return object|null
 */
function xs_get_slider( $slider_id ) {
	global $wpdb;
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Custom table.
	return $wpdb->get_row( $wpdb->prepare(
		"SELECT * FROM {$wpdb->prefix}xs_sliders WHERE id = %d LIMIT 1",
		$slider_id
	) );
}

/**
 * Get all slides for a slider, ordered by sort_order.
 *
 * @param int $slider_id
 * @return array
 */
function xs_get_slides( $slider_id ) {
	global $wpdb;
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Custom table.
	return $wpdb->get_results( $wpdb->prepare(
		"SELECT * FROM {$wpdb->prefix}xs_slides WHERE slider_id = %d ORDER BY sort_order ASC",
		$slider_id
	) );
}

/**
 * Get all sliders.
 *
 * @param string $status Optional status filter.
 * @return array
 */
function xs_get_all_sliders( $status = '' ) {
	global $wpdb;
	if ( $status ) {
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Custom table.
		return $wpdb->get_results( $wpdb->prepare(
			"SELECT * FROM {$wpdb->prefix}xs_sliders WHERE status = %s ORDER BY created_at DESC",
			$status
		) );
	}
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Custom table.
	return $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}xs_sliders ORDER BY created_at DESC" );
}

/**
 * Count all sliders.
 *
 * @param string $status Optional status filter.
 * @return int
 */
function xs_count_all_sliders( $status = '' ) {
	global $wpdb;

	if ( $status ) {
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Custom table.
		return (int) $wpdb->get_var( $wpdb->prepare(
			"SELECT COUNT(*) FROM {$wpdb->prefix}xs_sliders WHERE status = %s",
			$status
		) );
	}

	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Custom table.
	return (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}xs_sliders" );
}

/**
 * Whether the current license tier can create another slider.
 *
 * @return bool
 */
function xs_can_create_slider() {
	$limit = xs_get_max_slider_count();

	return $limit <= 0 || xs_count_all_sliders() < $limit;
}

/**
 * Count slides for a slider.
 *
 * @param int $slider_id
 * @return int
 */
function xs_count_slides( $slider_id ) {
	global $wpdb;
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Custom table.
	return (int) $wpdb->get_var( $wpdb->prepare(
		"SELECT COUNT(*) FROM {$wpdb->prefix}xs_slides WHERE slider_id = %d",
		$slider_id
	) );
}

/**
 * Get the effective edit-time slide cap.
 * Existing sliders above the current tier limit keep their current slide count
 * so an edit never silently drops slides.
 *
 * @param int $slider_id Slider ID.
 * @return int
 */
function xs_get_edit_max_slides( $slider_id = 0 ) {
	$base_limit = xs_get_max_slides();
	$slider_id  = absint( $slider_id );

	if ( ! $slider_id ) {
		return $base_limit;
	}

	return max( $base_limit, xs_count_slides( $slider_id ) );
}

/**
 * Get the effective visible-slide cap for the current slider editor.
 * Existing premium sliders keep their saved count visible in the UI even if
 * the active license later drops back to the free limit.
 *
 * @param int $slider_id Slider ID.
 * @return int
 */
function xs_get_edit_max_visible_slides( $slider_id = 0 ) {
	$base_limit = xs_get_max_visible_slides();
	$slider_id  = absint( $slider_id );

	if ( ! $slider_id ) {
		return $base_limit;
	}

	$slider = xs_get_slider( $slider_id );
	if ( ! $slider ) {
		return $base_limit;
	}

	return max( $base_limit, absint( $slider->visible_count ?? 0 ) );
}

/**
 * Sanitize a hex color value.
 *
 * @param string $color
 * @return string
 */
function xs_sanitize_hex_color( $color ) {
	// Accept 6-digit hex.
	if ( preg_match( '/^#[a-fA-F0-9]{6}$/', $color ) ) {
		return $color;
	}
	// Accept 3-digit shorthand and expand to 6-digit.
	if ( preg_match( '/^#([a-fA-F0-9])([a-fA-F0-9])([a-fA-F0-9])$/', $color, $m ) ) {
		return '#' . $m[1] . $m[1] . $m[2] . $m[2] . $m[3] . $m[3];
	}
	return '';
}

/**
 * Sanitize a fixed height value (integer between 50 and 2000).
 *
 * @param mixed $value
 * @return int 0 if out of range, otherwise the validated integer.
 */
function xs_sanitize_fixed_height( $value ) {
	$val = absint( $value );
	return ( $val >= 50 && $val <= 2000 ) ? $val : 0;
}
