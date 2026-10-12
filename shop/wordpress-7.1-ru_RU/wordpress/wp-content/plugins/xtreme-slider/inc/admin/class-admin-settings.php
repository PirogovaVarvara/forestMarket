<?php
defined( 'ABSPATH' ) || exit;

class XS_Admin_Settings {

	public static function render() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only display flag, no data processing.
		$saved = isset( $_GET['saved'] ) && '1' === sanitize_text_field( wp_unslash( $_GET['saved'] ) );

		$s = get_option( 'xs_settings', array() );
		$d = $s['defaults'] ?? array();
		$l = $s['license'] ?? array();
		$license_code = $l['code'] ?? '';
		$is_premium   = xs_is_premium_license_active( $license_code );
		$plan_label   = $is_premium ? __( 'Premium Active', 'xtreme-slider' ) : __( 'Free Mode', 'xtreme-slider' );
		$plan_class   = $is_premium ? 'is-premium' : 'is-free';
		$slider_limit_label = xs_get_slider_limit_label( $is_premium );
		$max_slides   = $is_premium ? 50 : 10;
		$max_visible  = xs_get_max_visible_slides();
		$license_host = xs_get_license_host();
		$image_ratio_options = xs_get_image_ratio_options( 'settings', $d['image_ratio'] ?? '16:10', $is_premium );

		?>
		<div class="wrap xs-admin-wrap">
			<div class="xs-admin-header">
				<div class="xs-admin-logo">
					<a href="https://xtremeplugins.com/plugins/xtreme-slider" target="_blank" rel="noopener noreferrer">
						<img src="<?php echo esc_url( XS_PLUGIN_URL . 'assets/img/xtreme-slider.svg' ); ?>" alt="Xtreme Slider">
					</a>
				</div>
			</div>

			<form method="post">
				<?php wp_nonce_field( 'xs_save_settings', 'xs_settings_nonce' ); ?>

				<div class="xs-settings-layout">

					<div class="xs-form-section">
						<h2><?php esc_html_e( 'Default Slider Settings', 'xtreme-slider' ); ?></h2>
						<p class="xs-form-desc"><?php esc_html_e( 'These defaults are applied when creating a new slider. Can be overridden per slider.', 'xtreme-slider' ); ?></p>
						<div class="xs-form-grid">
							<div class="xs-form-row">
								<label><?php esc_html_e( 'Default Layout', 'xtreme-slider' ); ?></label>
								<select name="settings[defaults][layout]">
									<option value="default" <?php selected( $d['layout'] ?? 'default', 'default' ); ?>><?php esc_html_e( 'Default', 'xtreme-slider' ); ?></option>
									<option value="cool" <?php selected( $d['layout'] ?? 'default', 'cool' ); ?>><?php esc_html_e( 'Cool', 'xtreme-slider' ); ?></option>
									<option value="3d" <?php selected( $d['layout'] ?? 'default', '3d' ); ?>><?php esc_html_e( '3D', 'xtreme-slider' ); ?></option>
									<option value="options" <?php selected( $d['layout'] ?? 'default', 'options' ); ?>><?php esc_html_e( 'Options', 'xtreme-slider' ); ?></option>
								</select>
							</div>
							<div class="xs-form-row">
								<label>
									<?php
									printf(
										/* translators: %d: max visible slides */
										esc_html__( 'Default Visible Slides (1–%d)', 'xtreme-slider' ),
										(int) $max_visible
									);
									?>
								</label>
								<input type="number" name="settings[defaults][visible_count]" value="<?php echo esc_attr( $d['visible_count'] ?? 3 ); ?>" min="1" max="<?php echo esc_attr( $max_visible ); ?>">
							</div>
							<div class="xs-form-row">
								<label class="xs-toggle-label">
									<input type="checkbox" name="settings[defaults][autoplay]" value="1" <?php checked( $d['autoplay'] ?? 0 ); ?>>
									<?php esc_html_e( 'Enable autoplay by default', 'xtreme-slider' ); ?>
								</label>
							</div>
							<div class="xs-form-row">
								<label><?php esc_html_e( 'Default Autoplay Speed (ms)', 'xtreme-slider' ); ?></label>
								<input type="number" name="settings[defaults][autoplay_speed]" value="<?php echo esc_attr( $d['autoplay_speed'] ?? 4000 ); ?>" min="2000" max="10000" step="500">
							</div>
							<div class="xs-form-row">
								<label class="xs-toggle-label">
									<input type="checkbox" name="settings[defaults][fullscreen]" value="1" <?php checked( $d['fullscreen'] ?? 0 ); ?>>
									<?php esc_html_e( 'Fullscreen by default', 'xtreme-slider' ); ?>
								</label>
							</div>
							<div class="xs-form-row">
								<label><?php esc_html_e( 'Default Image Ratio', 'xtreme-slider' ); ?></label>
								<select name="settings[defaults][image_ratio]">
									<?php foreach ( $image_ratio_options as $ratio_value => $ratio_label ) : ?>
										<option value="<?php echo esc_attr( $ratio_value ); ?>" <?php selected( $d['image_ratio'] ?? '16:10', $ratio_value ); ?>><?php echo esc_html( $ratio_label ); ?></option>
									<?php endforeach; ?>
								</select>
								<?php if ( $is_premium || 'default' === ( $d['image_ratio'] ?? '' ) ) : ?>
									<div class="xs-form-hint"><?php esc_html_e( 'Premium Default keeps uploaded images at their original aspect ratio.', 'xtreme-slider' ); ?></div>
								<?php endif; ?>
							</div>
						</div>
					</div>

					<div class="xs-form-section">
						<h2><?php esc_html_e( 'Default Background Color (3D Layout)', 'xtreme-slider' ); ?></h2>
						<div class="xs-form-grid">
							<div class="xs-form-row">
								<label><?php esc_html_e( 'Background Color', 'xtreme-slider' ); ?></label>
								<input type="text" name="settings[defaults][bg_color_3d]" value="<?php echo esc_attr( $d['bg_color_3d'] ?? '#0a0a0a' ); ?>" class="xs-color-picker">
							</div>
						</div>
					</div>

					<div class="xs-form-section">
						<h2><?php esc_html_e( 'Default Gradient (Cool Layout)', 'xtreme-slider' ); ?></h2>
						<div class="xs-form-grid">
							<div class="xs-form-row">
								<label><?php esc_html_e( 'Start Color', 'xtreme-slider' ); ?></label>
								<input type="text" name="settings[defaults][gradient_start]" value="<?php echo esc_attr( $d['gradient_start'] ?? '#ec38bc' ); ?>" class="xs-color-picker">
							</div>
							<div class="xs-form-row">
								<label><?php esc_html_e( 'End Color', 'xtreme-slider' ); ?></label>
								<input type="text" name="settings[defaults][gradient_end]" value="<?php echo esc_attr( $d['gradient_end'] ?? '#7303c0' ); ?>" class="xs-color-picker">
							</div>
						</div>
					</div>

					<div class="xs-form-section xs-license-section">
						<h2><?php esc_html_e( 'Premium License', 'xtreme-slider' ); ?></h2>
						<p class="xs-form-desc">
							<?php
							printf(
								/* translators: 1: site host, 2: slide limit */
								esc_html__( 'Enter the site license for %1$s to unlock unlimited sliders, up to %2$d images per slider, and more premium options.', 'xtreme-slider' ),
								esc_html( $license_host ),
								50
							);
							?>
						</p>

						<div class="xs-form-row">
							<label><?php esc_html_e( 'License Code', 'xtreme-slider' ); ?></label>
							<input type="text" name="settings[license][code]" value="<?php echo esc_attr( $license_code ); ?>" placeholder="XSPRO-XXXX-XXXX-XXXX-XXXX-XXXX" class="xs-license-input">
						</div>

						<div class="xs-license-meta">
							<span class="xs-plan-badge <?php echo esc_attr( $plan_class ); ?>"><?php echo esc_html( $plan_label ); ?></span>
							<span class="xs-license-note">
								<?php
								printf(
									/* translators: 1: slider count limit, 2: image limit, 3: visible slide limit */
									esc_html__( 'Current limits: %1$s, %2$d images, %3$d visible slides', 'xtreme-slider' ),
									esc_html( $slider_limit_label ),
									(int) $max_slides,
									(int) $max_visible
								);
								?>
							</span>
						</div>
					</div>

					<div class="xs-form-actions">
						<button type="submit" class="xs-btn xs-btn-primary xs-btn-lg" id="xs-save-btn"><span class="xs-spinner"></span> <?php esc_html_e( 'Save Settings', 'xtreme-slider' ); ?></button>
						<?php if ( $saved ) : ?>
							<span class="xs-save-feedback is-success" title="<?php esc_attr_e( 'Settings saved.', 'xtreme-slider' ); ?>" role="status" aria-live="polite">
								<span class="xs-save-feedback-text"><?php esc_html_e( 'Saved', 'xtreme-slider' ); ?></span>
							</span>
						<?php endif; ?>
					</div>

				</div>
			</form>
		</div>
		<?php
	}

	public static function process_save() {
		if ( ! isset( $_POST['xs_settings_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['xs_settings_nonce'] ) ), 'xs_save_settings' ) ) {
			wp_die( esc_html__( 'Security check failed.', 'xtreme-slider' ) );
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Unauthorized.', 'xtreme-slider' ) );
		}

		$raw               = isset( $_POST['settings'] ) ? map_deep( wp_unslash( $_POST['settings'] ), 'sanitize_text_field' ) : array();
		$license_code      = xs_format_license_code( sanitize_text_field( $raw['license']['code'] ?? '' ) );
		$license_premium   = xs_is_premium_license_active( $license_code );
		$current_settings  = get_option( 'xs_settings', array() );
		$current_defaults  = $current_settings['defaults'] ?? array();
		$current_ratio     = $current_defaults['image_ratio'] ?? '16:10';

		$settings = array(
			'defaults' => array(
				'layout'         => in_array( $raw['defaults']['layout'] ?? '', array( 'default', 'cool', '3d', 'options' ), true ) ? sanitize_text_field( $raw['defaults']['layout'] ) : 'default',
				'visible_count'  => max( 1, min( xs_get_max_visible_slides(), absint( $raw['defaults']['visible_count'] ?? 3 ) ) ),
				'autoplay'       => isset( $raw['defaults']['autoplay'] ) ? 1 : 0,
				'autoplay_speed' => max( 2000, min( 10000, absint( $raw['defaults']['autoplay_speed'] ?? 4000 ) ) ),
				'fullscreen'     => isset( $raw['defaults']['fullscreen'] ) ? 1 : 0,
				'image_ratio'    => xs_sanitize_image_ratio( $raw['defaults']['image_ratio'] ?? '16:10', 'settings', $current_ratio, $license_premium ),
				'gradient_start' => xs_sanitize_hex_color( sanitize_text_field( $raw['defaults']['gradient_start'] ?? '#ec38bc' ) ),
				'gradient_end'   => xs_sanitize_hex_color( sanitize_text_field( $raw['defaults']['gradient_end'] ?? '#7303c0' ) ),
				'bg_color_3d'    => xs_sanitize_hex_color( sanitize_text_field( $raw['defaults']['bg_color_3d'] ?? '' ) ),
			),
			'license' => array(
				'code' => $license_code,
			),
		);

		update_option( 'xs_settings', $settings );

		wp_safe_redirect( admin_url( 'admin.php?page=xs-settings&saved=1' ) );
		exit;
	}
}
