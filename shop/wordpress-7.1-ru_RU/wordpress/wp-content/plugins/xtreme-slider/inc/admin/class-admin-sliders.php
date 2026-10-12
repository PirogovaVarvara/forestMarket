<?php
defined( 'ABSPATH' ) || exit;

class XS_Admin_Sliders {

	public static function render() {
		$sliders           = xs_get_all_sliders();
		$is_premium        = xs_is_premium_license_active();
		$slider_limit      = xs_get_max_slider_count();
		$slider_limit_label = xs_get_slider_limit_label( $is_premium );
		$total_sliders     = xs_count_all_sliders();
		$can_create_slider = xs_can_create_slider();
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only display flag, no data processing.
		$limit_error       = isset( $_GET['xs_error'] ) && 'slider_limit' === sanitize_text_field( wp_unslash( $_GET['xs_error'] ) );
		?>
		<div class="wrap xs-admin-wrap">
			<div class="xs-admin-header">
				<div class="xs-admin-logo">
					<a href="https://xtremeplugins.com/plugins/xtreme-slider" target="_blank" rel="noopener noreferrer">
						<img src="<?php echo esc_url( XS_PLUGIN_URL . 'assets/img/xtreme-slider.svg' ); ?>" alt="Xtreme Slider">
					</a>
				</div>
				<?php if ( $can_create_slider ) : ?>
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=xs-edit' ) ); ?>" class="xs-btn xs-btn-primary"><?php echo '+ ' . esc_html__( 'Add New Slider', 'xtreme-slider' ); ?></a>
				<?php else : ?>
					<span class="xs-btn xs-btn-primary xs-btn-disabled" aria-disabled="true"><?php echo '+ ' . esc_html__( 'Add New Slider', 'xtreme-slider' ); ?></span>
				<?php endif; ?>
			</div>

			<?php if ( ! $is_premium ) : ?>
				<div class="xs-license-meta xs-slider-limit-meta">
					<span class="xs-plan-badge is-free"><?php esc_html_e( 'Free Mode', 'xtreme-slider' ); ?></span>
					<span class="xs-license-note">
						<?php
						printf(
							/* translators: 1: slider limit label, 2: used slider count */
							esc_html__( '%1$s available in free mode. %2$d used.', 'xtreme-slider' ),
							esc_html( $slider_limit_label ),
							(int) $total_sliders
						);
						?>
					</span>
					<?php if ( $limit_error || ! $can_create_slider ) : ?>
						<span class="xs-save-feedback is-error" title="<?php esc_attr_e( 'Free mode is limited to 2 sliders. Delete one or activate premium to create another.', 'xtreme-slider' ); ?>" role="status" aria-live="polite">
							<span class="xs-save-feedback-text"><?php esc_html_e( 'Limit reached', 'xtreme-slider' ); ?></span>
						</span>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<?php if ( empty( $sliders ) ) : ?>
				<div class="xs-empty-state">
					<div class="xs-empty-icon">
						<svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="#555" stroke-width="1.5"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="M5 15l3.5-4.5 2.5 3 3.5-4.5L19 15"/><circle cx="8.5" cy="8.5" r="1.5"/></svg>
					</div>
					<h2><?php esc_html_e( 'No sliders yet', 'xtreme-slider' ); ?></h2>
					<p><?php esc_html_e( 'Create your first slider to get started.', 'xtreme-slider' ); ?></p>
					<?php if ( $can_create_slider ) : ?>
						<a href="<?php echo esc_url( admin_url( 'admin.php?page=xs-edit' ) ); ?>" class="xs-btn xs-btn-primary xs-btn-lg"><?php esc_html_e( 'Create Slider', 'xtreme-slider' ); ?></a>
					<?php else : ?>
						<span class="xs-btn xs-btn-primary xs-btn-lg xs-btn-disabled" aria-disabled="true"><?php esc_html_e( 'Create Slider', 'xtreme-slider' ); ?></span>
					<?php endif; ?>
				</div>
			<?php else : ?>
				<table class="xs-table">
					<thead>
						<tr>
							<th><?php esc_html_e( 'Title', 'xtreme-slider' ); ?></th>
							<th><?php esc_html_e( 'Layout', 'xtreme-slider' ); ?></th>
							<th><?php esc_html_e( 'Slides', 'xtreme-slider' ); ?></th>
							<th><?php esc_html_e( 'Shortcode', 'xtreme-slider' ); ?></th>
							<th><?php esc_html_e( 'Status', 'xtreme-slider' ); ?></th>
							<th><?php esc_html_e( 'Date', 'xtreme-slider' ); ?></th>
							<th><?php esc_html_e( 'Actions', 'xtreme-slider' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ( $sliders as $slider ) :
							$count    = xs_count_slides( $slider->id );
							$edit_url = admin_url( 'admin.php?page=xs-edit&slider_id=' . $slider->id );
							$del_url  = wp_nonce_url( admin_url( 'admin.php?page=xtreme-slider&action=delete&slider_id=' . $slider->id ), 'xs_delete_' . $slider->id );
						?>
							<tr>
								<td><a href="<?php echo esc_url( $edit_url ); ?>" class="xs-link"><?php echo esc_html( $slider->title ? $slider->title : __( '(Untitled)', 'xtreme-slider' ) ); ?></a></td>
								<td><span class="xs-badge xs-badge-<?php echo esc_attr( $slider->layout ); ?>"><?php echo esc_html( ucfirst( $slider->layout ) ); ?></span></td>
								<td><?php echo esc_html( $count ); ?></td>
								<td>
									<code class="xs-shortcode-display">[xtreme_slider id="<?php echo esc_attr( $slider->id ); ?>"]</code>
									<button type="button" class="xs-copy-btn" data-copy='[xtreme_slider id="<?php echo esc_attr( $slider->id ); ?>"]' title="<?php esc_attr_e( 'Copy shortcode', 'xtreme-slider' ); ?>">
										<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/></svg>
									</button>
								</td>
								<td><span class="xs-status xs-status-<?php echo esc_attr( $slider->status ); ?>"><?php echo esc_html( ucfirst( $slider->status ) ); ?></span></td>
								<td><?php echo esc_html( date_i18n( 'M j, Y', strtotime( $slider->created_at ) ) ); ?></td>
								<td>
									<a href="<?php echo esc_url( $edit_url ); ?>" class="xs-action-link"><?php esc_html_e( 'Edit', 'xtreme-slider' ); ?></a>
									<a href="<?php echo esc_url( $del_url ); ?>" class="xs-action-link xs-action-delete" onclick="return confirm('<?php echo esc_js( __( 'Delete this slider and all its slides?', 'xtreme-slider' ) ); ?>');"><?php esc_html_e( 'Delete', 'xtreme-slider' ); ?></a>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			<?php endif; ?>
		</div>
		<?php
	}

	public static function process_delete() {
		if ( ! isset( $_GET['slider_id'], $_GET['_wpnonce'] ) ) {
			wp_die( esc_html__( 'Invalid request.', 'xtreme-slider' ) );
		}

		$slider_id = absint( wp_unslash( $_GET['slider_id'] ) );

		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'xs_delete_' . $slider_id ) ) {
			wp_die( esc_html__( 'Security check failed.', 'xtreme-slider' ) );
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Unauthorized.', 'xtreme-slider' ) );
		}

		global $wpdb;
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Custom table, delete operation.
		$wpdb->delete( $wpdb->prefix . 'xs_slides', array( 'slider_id' => $slider_id ), array( '%d' ) );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Custom table, delete operation.
		$wpdb->delete( $wpdb->prefix . 'xs_sliders', array( 'id' => $slider_id ), array( '%d' ) );

		wp_safe_redirect( admin_url( 'admin.php?page=xtreme-slider&deleted=1' ) );
		exit;
	}
}
