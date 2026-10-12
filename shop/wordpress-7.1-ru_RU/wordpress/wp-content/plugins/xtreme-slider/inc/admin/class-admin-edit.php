<?php
defined( 'ABSPATH' ) || exit;

class XS_Admin_Edit {

	public static function render() {
		if ( class_exists( 'XS_Activator' ) ) {
			XS_Activator::maybe_upgrade();
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only display parameter, no data processing.
		$slider_id = isset( $_GET['slider_id'] ) ? absint( wp_unslash( $_GET['slider_id'] ) ) : 0;
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only display flag, no data processing.
		$saved     = isset( $_GET['saved'] ) && '1' === sanitize_text_field( wp_unslash( $_GET['saved'] ) );
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only display flag, no data processing.
		$save_error = isset( $_GET['xs_error'] ) ? sanitize_text_field( wp_unslash( $_GET['xs_error'] ) ) : '';
		$save_feedback_text  = '';
		$save_feedback_class = '';
		$save_feedback_title = '';

		if ( $saved ) {
			$save_feedback_text  = __( 'Saved', 'xtreme-slider' );
			$save_feedback_class = 'is-success';
			$save_feedback_title = __( 'Slider saved.', 'xtreme-slider' );
		} elseif ( 'save_failed' === $save_error ) {
			$save_feedback_text  = __( 'Save failed', 'xtreme-slider' );
			$save_feedback_class = 'is-error';
			$save_feedback_title = __( 'Slider could not be saved. The plugin storage was repaired, so please try again.', 'xtreme-slider' );
		}

		$slider = $slider_id ? xs_get_slider( $slider_id ) : null;
		$slides = $slider_id ? xs_get_slides( $slider_id ) : array();
		$is_new = ! $slider;

		// Defaults for new slider.
		if ( ! $slider ) {
			$slider = (object) array(
				'id'             => 0,
				'title'          => '',
				'layout'         => xs_setting( 'defaults', 'layout', 'default' ),
				'visible_count'  => xs_setting( 'defaults', 'visible_count', 3 ),
				'autoplay'       => xs_setting( 'defaults', 'autoplay', 0 ),
				'autoplay_speed' => xs_setting( 'defaults', 'autoplay_speed', 4000 ),
				'fullscreen'     => xs_setting( 'defaults', 'fullscreen', 0 ),
				'image_ratio'    => xs_setting( 'defaults', 'image_ratio', '16:10' ),
				'fixed_height'   => 0,
				'square_corners' => 0,
				'black_arrows'   => 0,
				'link_hover_color' => '#ee212b',
				'gradient_start' => xs_setting( 'defaults', 'gradient_start', '#ec38bc' ),
				'gradient_end'   => xs_setting( 'defaults', 'gradient_end', '#7303c0' ),
				'bg_color_3d'    => xs_setting( 'defaults', 'bg_color_3d', '#0a0a0a' ),
				'status'         => 'active',
			);
		}

		$max_slides          = xs_get_edit_max_slides( $slider_id );
		$max_visible_slides  = xs_get_edit_max_visible_slides( $slider_id );
		$is_premium          = xs_is_premium_license_active();
		$slider_limit        = xs_get_max_slider_count();
		$can_create_slider   = ! $is_new || xs_can_create_slider();
		$image_ratio_options = xs_get_image_ratio_options( 'editor', $slider->image_ratio ?? '16:10', $is_premium );

		$speed_seconds = intval( $slider->autoplay_speed ) / 1000;
		?>
		<div class="wrap xs-admin-wrap">
			<div class="xs-admin-header">
				<div class="xs-admin-logo">
					<a href="https://xtremeplugins.com/plugins/xtreme-slider" target="_blank" rel="noopener noreferrer">
						<img src="<?php echo esc_url( XS_PLUGIN_URL . 'assets/img/xtreme-slider.svg' ); ?>" alt="Xtreme Slider">
					</a>
				</div>
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=xtreme-slider' ) ); ?>" class="xs-btn xs-btn-secondary"><?php echo '&larr; ' . esc_html__( 'All Sliders', 'xtreme-slider' ); ?></a>
			</div>

			<?php if ( $is_new && ! $can_create_slider ) : ?>
				<div class="xs-empty-state">
					<div class="xs-empty-icon">
						<svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="#555" stroke-width="1.5"><circle cx="12" cy="12" r="9"/><path d="M12 7v6"/><circle cx="12" cy="16.5" r="1"/></svg>
					</div>
					<h2><?php esc_html_e( 'Free slider limit reached', 'xtreme-slider' ); ?></h2>
					<p>
						<?php
						printf(
							/* translators: %d: free slider count limit */
							esc_html__( 'Free mode allows up to %d sliders. Delete one of your existing sliders or activate premium to create another.', 'xtreme-slider' ),
							(int) $slider_limit
						);
						?>
					</p>
					<div class="xs-form-actions">
						<a href="<?php echo esc_url( admin_url( 'admin.php?page=xtreme-slider' ) ); ?>" class="xs-btn xs-btn-primary"><?php esc_html_e( 'View Sliders', 'xtreme-slider' ); ?></a>
						<a href="<?php echo esc_url( admin_url( 'admin.php?page=xs-settings' ) ); ?>" class="xs-btn xs-btn-secondary"><?php esc_html_e( 'Open Settings', 'xtreme-slider' ); ?></a>
					</div>
				</div>
			</div>
				<?php
				return;
			endif;
			?>

			<form method="post" id="xs-edit-form">
				<?php wp_nonce_field( 'xs_save_slider', 'xs_edit_nonce' ); ?>
				<input type="hidden" name="slider_id" value="<?php echo esc_attr( $slider->id ); ?>">

				<div class="xs-edit-layout">

					<!-- Main Column -->
					<div class="xs-edit-main">

						<!-- Title + Status -->
						<div class="xs-form-section">
							<div class="xs-title-row">
								<div class="xs-title-field">
									<input type="text" name="title" class="xs-input-full" value="<?php echo esc_attr( $slider->title ); ?>" placeholder="<?php esc_attr_e( 'Enter slider name...', 'xtreme-slider' ); ?>">
								</div>
								<div class="xs-title-status">
									<label class="xs-status-toggle">
										<input type="hidden" name="status" value="draft">
										<input type="checkbox" name="status" value="active" <?php checked( $slider->status, 'active' ); ?>>
										<span class="xs-toggle-track"></span>
										<span class="xs-toggle-text"></span>
									</label>
								</div>
							</div>
						</div>

						<!-- Slides -->
						<div class="xs-form-section">
							<h2><?php esc_html_e( 'Slides', 'xtreme-slider' ); ?> <span class="xs-slide-counter">(<span id="xs-slide-count"><?php echo count( $slides ); ?></span> / <?php echo esc_html( $max_slides ); ?>)</span></h2>
							<p class="xs-form-desc">
								<?php
								printf(
									/* translators: %d: max slide count */
									esc_html__( 'Add up to %d images. Drag to reorder. The number of visible slides per view is configured in the sidebar.', 'xtreme-slider' ),
									(int) $max_slides
								);
								?>
								<?php if ( $is_premium ) : ?>
									<span class="xs-inline-plan xs-inline-plan-premium"><?php esc_html_e( 'Premium 50-image limit active.', 'xtreme-slider' ); ?></span>
								<?php endif; ?>
							</p>

							<div id="xs-slides-grid" class="xs-slides-grid">
								<?php foreach ( $slides as $slide ) : ?>
									<?php
									$image_path     = wp_parse_url( $slide->image_url, PHP_URL_PATH );
									$image_filename = $image_path ? rawurldecode( wp_basename( $image_path ) ) : '';
									?>
									<div class="xs-slide-card" data-image-id="<?php echo esc_attr( $slide->image_id ); ?>" data-image-filename="<?php echo esc_attr( $image_filename ); ?>">
										<div class="xs-slide-img">
											<img src="<?php echo esc_url( $slide->image_url ); ?>" alt="">
										</div>
										<div class="xs-slide-fields">
											<input type="hidden" name="slides[image_id][]" value="<?php echo esc_attr( $slide->image_id ); ?>">
											<input type="hidden" name="slides[image_url][]" value="<?php echo esc_url( $slide->image_url ); ?>">
											<input type="text" name="slides[title][]" value="<?php echo esc_attr( $slide->title ); ?>" placeholder="<?php esc_attr_e( 'Title (on image)', 'xtreme-slider' ); ?>">
											<input type="text" name="slides[caption][]" value="<?php echo esc_attr( $slide->caption ?? '' ); ?>" placeholder="<?php esc_attr_e( 'Caption (below image)', 'xtreme-slider' ); ?>">
											<input type="text" class="xs-field-non-options" name="slides[description][]" value="<?php echo esc_attr( $slide->description ?? '' ); ?>" placeholder="<?php esc_attr_e( 'Description (optional)', 'xtreme-slider' ); ?>">
											<input type="text" class="xs-field-non-options" name="slides[link_url][]" value="<?php echo esc_attr( $slide->link_url ); ?>" placeholder="<?php esc_attr_e( 'Link URL (optional)', 'xtreme-slider' ); ?>">
											<div class="xs-field-options-only">
												<div class="xs-html-toolbar">
													<label class="xs-html-label"><?php esc_html_e( 'HTML Content', 'xtreme-slider' ); ?></label>
													<div class="xs-html-tabs">
														<button type="button" class="xs-html-tab active" data-mode="text"><?php esc_html_e( 'Code', 'xtreme-slider' ); ?></button>
														<button type="button" class="xs-html-tab" data-mode="preview"><?php esc_html_e( 'Preview', 'xtreme-slider' ); ?></button>
													</div>
												</div>
												<?php $html_editor_id = 'xs-html-editor-' . uniqid(); ?>
												<textarea id="<?php echo esc_attr( $html_editor_id ); ?>" class="xs-slide-html" name="slides[html_content][]" rows="12" placeholder="<?php esc_attr_e( 'Paste HTML here. It will be shown when this option is clicked on the frontend.', 'xtreme-slider' ); ?>"><?php echo esc_textarea( $slide->html_content ?? '' ); ?></textarea>
												<iframe class="xs-html-preview" style="display:none;"></iframe>
											</div>
										</div>
										<button type="button" class="xs-slide-remove" title="<?php esc_attr_e( 'Remove slide', 'xtreme-slider' ); ?>">&times;</button>
									</div>
								<?php endforeach; ?>
							</div>

							<div class="xs-slides-actions">
								<div class="xs-slides-action-buttons">
									<button type="button" id="xs-add-slides" class="xs-btn xs-btn-secondary xs-btn-lg" <?php echo count( $slides ) >= $max_slides ? 'disabled' : ''; ?>>
										<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
										<?php esc_html_e( 'Add Images', 'xtreme-slider' ); ?>
									</button>
									<button type="button" id="xs-load-titles" class="xs-btn xs-btn-secondary xs-btn-lg" <?php echo empty( $slides ) ? 'disabled' : ''; ?>>
										<?php esc_html_e( 'Load Titles', 'xtreme-slider' ); ?>
									</button>
								</div>
								<span id="xs-load-titles-feedback" class="xs-action-feedback" role="status" aria-live="polite" hidden></span>
							</div>
						</div>

						<!-- Shortcode (only shown for saved sliders) -->
						<?php if ( ! $is_new ) : ?>
						<div class="xs-form-section">
							<h2><?php esc_html_e( 'Shortcode', 'xtreme-slider' ); ?></h2>
							<p class="xs-form-desc"><?php esc_html_e( 'Copy this shortcode and paste it into any page, post, or Elementor Shortcode widget.', 'xtreme-slider' ); ?></p>
							<div class="xs-shortcode-box">
								<code id="xs-shortcode-value">[xtreme_slider id="<?php echo esc_attr( $slider->id ); ?>"]</code>
								<button type="button" class="xs-copy-btn xs-copy-btn-lg" data-copy='[xtreme_slider id="<?php echo esc_attr( $slider->id ); ?>"]'><?php esc_html_e( 'Copy', 'xtreme-slider' ); ?></button>
							</div>
						</div>
						<?php endif; ?>

					</div>

					<!-- Sidebar -->
					<div class="xs-edit-sidebar">

						<div class="xs-form-section">
							<h2><?php esc_html_e( 'Layout', 'xtreme-slider' ); ?></h2>
							<div class="xs-radio-cards xs-radio-cards-4">
								<label class="xs-radio-card <?php echo 'default' === $slider->layout ? 'active' : ''; ?>">
									<input type="radio" name="layout" value="default" <?php checked( $slider->layout, 'default' ); ?>>
									<span class="xs-radio-card-icon">
										<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="2" y="4" width="20" height="16"/><line x1="9" y1="4" x2="9" y2="20"/><line x1="15" y1="4" x2="15" y2="20"/></svg>
									</span>
									<span class="xs-radio-card-label"><?php esc_html_e( 'Default', 'xtreme-slider' ); ?></span>
								</label>
								<label class="xs-radio-card <?php echo 'cool' === $slider->layout ? 'active' : ''; ?>">
									<input type="radio" name="layout" value="cool" <?php checked( $slider->layout, 'cool' ); ?>>
									<span class="xs-radio-card-icon">
										<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="2" y="6" width="20" height="12" rx="2"/><line x1="8" y1="6" x2="8" y2="18"/><line x1="16" y1="6" x2="16" y2="18"/></svg>
									</span>
									<span class="xs-radio-card-label"><?php esc_html_e( 'Cool', 'xtreme-slider' ); ?></span>
								</label>
								<label class="xs-radio-card <?php echo '3d' === $slider->layout ? 'active' : ''; ?>">
									<input type="radio" name="layout" value="3d" <?php checked( $slider->layout, '3d' ); ?>>
									<span class="xs-radio-card-icon">
										<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg>
									</span>
									<span class="xs-radio-card-label"><?php esc_html_e( '3D', 'xtreme-slider' ); ?></span>
								</label>
								<label class="xs-radio-card <?php echo 'options' === $slider->layout ? 'active' : ''; ?>">
									<input type="radio" name="layout" value="options" <?php checked( $slider->layout, 'options' ); ?>>
									<span class="xs-radio-card-icon">
										<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="18" height="7" rx="1.5"/></svg>
									</span>
									<span class="xs-radio-card-label"><?php esc_html_e( 'Options', 'xtreme-slider' ); ?></span>
								</label>
							</div>
						</div>

						<div class="xs-form-section">
							<h2><?php esc_html_e( 'Display Options', 'xtreme-slider' ); ?></h2>
							<div class="xs-form-row">
								<label>
									<?php
									printf(
										/* translators: %d: max visible slides */
										esc_html__( 'Visible Slides (1–%d)', 'xtreme-slider' ),
										(int) $max_visible_slides
									);
									?>
								</label>
								<input type="number" name="visible_count" value="<?php echo esc_attr( $slider->visible_count ); ?>" min="1" max="<?php echo esc_attr( $max_visible_slides ); ?>" class="xs-input-sm">
								<?php if ( $is_premium ) : ?>
									<div class="xs-form-hint"><?php esc_html_e( 'Premium lets you show up to 15 slides at once on large screens.', 'xtreme-slider' ); ?></div>
								<?php endif; ?>
							</div>
							<div class="xs-form-row">
								<label class="xs-toggle-label">
									<input type="checkbox" name="fullscreen" value="1" <?php checked( $slider->fullscreen, 1 ); ?>>
									<?php esc_html_e( 'Fullscreen (edge-to-edge)', 'xtreme-slider' ); ?>
								</label>
							</div>
							<div class="xs-form-row">
								<label class="xs-toggle-label">
									<input type="checkbox" name="title_shadow" value="1" <?php checked( $slider->title_shadow ?? 0, 1 ); ?>>
									<?php esc_html_e( 'Add shadow to titles', 'xtreme-slider' ); ?>
								</label>
							</div>
							<div class="xs-form-row">
								<label class="xs-toggle-label">
									<input type="checkbox" name="square_corners" value="1" <?php checked( $slider->square_corners ?? 0, 1 ); ?>>
									<?php esc_html_e( 'Square corners', 'xtreme-slider' ); ?>
								</label>
							</div>
							<div class="xs-form-row xs-hide-on-options">
								<label class="xs-toggle-label">
									<input type="checkbox" name="black_arrows" value="1" <?php checked( $slider->black_arrows ?? 0, 1 ); ?>>
									<?php esc_html_e( 'Black arrows', 'xtreme-slider' ); ?>
								</label>
							</div>
							<div class="xs-form-row xs-ratio-row">
								<label><?php esc_html_e( 'Image Ratio', 'xtreme-slider' ); ?></label>
								<div class="xs-ratio-inline">
									<select name="image_ratio">
										<?php foreach ( $image_ratio_options as $ratio_value => $ratio_label ) : ?>
											<option value="<?php echo esc_attr( $ratio_value ); ?>" <?php selected( $slider->image_ratio ?? '16:10', $ratio_value ); ?>><?php echo esc_html( $ratio_label ); ?></option>
										<?php endforeach; ?>
									</select>
									<div id="xs-fixed-height-row" class="xs-fixed-height-inline" style="<?php echo ( ( $slider->image_ratio ?? '' ) === 'fixed' ) ? '' : 'display:none;'; ?>">
										<input type="number" name="fixed_height" value="<?php echo esc_attr( $slider->fixed_height ?? 400 ); ?>" min="50" max="2000" class="xs-input-sm" placeholder="400"<?php echo ( ( $slider->image_ratio ?? '' ) !== 'fixed' ) ? ' disabled' : ''; ?>>
										<span class="xs-unit-label">px</span>
									</div>
								</div>
								<?php if ( $is_premium || 'default' === ( $slider->image_ratio ?? '' ) ) : ?>
									<div class="xs-form-hint"><?php esc_html_e( 'Premium Default keeps each image at its original aspect ratio.', 'xtreme-slider' ); ?></div>
								<?php endif; ?>
							</div>
							<div class="xs-form-row xs-hide-on-options">
								<label><?php esc_html_e( 'Link Hover Color', 'xtreme-slider' ); ?></label>
								<input type="text" name="link_hover_color" value="<?php echo esc_attr( $slider->link_hover_color ?? '#ee212b' ); ?>" class="xs-color-picker">
							</div>
						</div>

						<div class="xs-form-section xs-hide-on-options">
							<h2><?php esc_html_e( 'Autoplay', 'xtreme-slider' ); ?></h2>
							<div class="xs-form-row">
								<label class="xs-toggle-label">
									<input type="checkbox" name="autoplay" value="1" id="xs-autoplay-toggle" <?php checked( $slider->autoplay, 1 ); ?>>
									<?php esc_html_e( 'Enable auto-scrolling', 'xtreme-slider' ); ?>
								</label>
							</div>
							<div class="xs-form-row xs-autoplay-speed" style="<?php echo $slider->autoplay ? '' : 'display:none;'; ?>">
								<label><?php
									/* translators: %s: speed in seconds */
									printf( esc_html__( 'Speed: %ss', 'xtreme-slider' ), '<span id="xs-speed-label">' . esc_html( $speed_seconds ) . '</span>' );
								?></label>
								<input type="range" name="autoplay_speed" min="2000" max="10000" step="500" value="<?php echo esc_attr( $slider->autoplay_speed ); ?>" id="xs-speed-range">
							</div>
						</div>

						<div class="xs-form-section xs-gradient-section" id="xs-gradient-section" style="<?php echo 'cool' !== $slider->layout ? 'display:none;' : ''; ?>">
							<h2><?php esc_html_e( 'Background Gradient', 'xtreme-slider' ); ?></h2>
							<p class="xs-form-desc"><?php esc_html_e( 'Visible behind the slider in Cool layout.', 'xtreme-slider' ); ?></p>
							<div class="xs-form-row">
								<label><?php esc_html_e( 'Start Color', 'xtreme-slider' ); ?></label>
								<input type="text" name="gradient_start" value="<?php echo esc_attr( $slider->gradient_start ?? '' ); ?>" class="xs-color-picker">
							</div>
							<div class="xs-form-row">
								<label><?php esc_html_e( 'End Color', 'xtreme-slider' ); ?></label>
								<input type="text" name="gradient_end" value="<?php echo esc_attr( $slider->gradient_end ?? '' ); ?>" class="xs-color-picker">
							</div>
							<?php
							$gs = $slider->gradient_start ?? '';
							$ge = $slider->gradient_end   ?? '';
							$preview_style = ( $gs || $ge )
								? 'background: linear-gradient(135deg, ' . esc_attr( $gs ?: $ge ) . ', ' . esc_attr( $ge ?: $gs ) . ');'
								: '';
						?>
						<div class="xs-gradient-preview" id="xs-gradient-preview" style="<?php echo esc_attr( $preview_style ); ?>"></div>
						</div>

						<div class="xs-form-section" id="xs-3d-bg-section" style="<?php echo '3d' !== $slider->layout ? 'display:none;' : ''; ?>">
							<h2><?php esc_html_e( 'Background Color', 'xtreme-slider' ); ?></h2>
							<p class="xs-form-desc"><?php esc_html_e( 'Background color behind the 3D slider.', 'xtreme-slider' ); ?></p>
							<div class="xs-form-row">
								<label><?php esc_html_e( 'Color', 'xtreme-slider' ); ?></label>
								<input type="text" name="bg_color_3d" value="<?php echo esc_attr( $slider->bg_color_3d ?? '' ); ?>" class="xs-color-picker">
							</div>
						</div>

						<div class="xs-form-section" id="xs-options-bg-section" style="<?php echo 'options' !== $slider->layout ? 'display:none;' : ''; ?>">
							<h2><?php esc_html_e( 'Background Color', 'xtreme-slider' ); ?></h2>
							<p class="xs-form-desc"><?php esc_html_e( 'Background color behind the Options slider. Leave empty to use the default light/dark theme.', 'xtreme-slider' ); ?></p>
							<div class="xs-form-row">
								<label><?php esc_html_e( 'Color', 'xtreme-slider' ); ?></label>
								<input type="text" name="bg_color_options" value="<?php echo esc_attr( $slider->bg_color_options ?? '' ); ?>" class="xs-color-picker">
							</div>
						</div>

						<div class="xs-form-actions">
							<button type="submit" class="xs-btn xs-btn-primary xs-btn-lg xs-btn-full" id="xs-save-btn"><span class="xs-spinner"></span> <?php echo $is_new ? esc_html__( 'Create Slider', 'xtreme-slider' ) : esc_html__( 'Save Changes', 'xtreme-slider' ); ?></button>
							<?php if ( $save_feedback_text ) : ?>
								<span class="xs-save-feedback <?php echo esc_attr( $save_feedback_class ); ?>" title="<?php echo esc_attr( $save_feedback_title ); ?>" role="status" aria-live="polite">
									<span class="xs-save-feedback-text"><?php echo esc_html( $save_feedback_text ); ?></span>
								</span>
							<?php endif; ?>
						</div>
					</div>

				</div>
			</form>
		</div>
		<?php
	}

	public static function process_save() {
		if ( ! isset( $_POST['xs_edit_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['xs_edit_nonce'] ) ), 'xs_save_slider' ) ) {
			wp_die( esc_html__( 'Security check failed.', 'xtreme-slider' ) );
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Unauthorized.', 'xtreme-slider' ) );
		}

		global $wpdb;

		if ( class_exists( 'XS_Activator' ) ) {
			XS_Activator::maybe_upgrade();
			if ( method_exists( 'XS_Activator', 'storage_ready' ) && ! XS_Activator::storage_ready() ) {
				self::rollback_and_fail( 0, 'Slider storage is not ready after upgrade.' );
			}
		}

		$slider_id      = absint( wp_unslash( $_POST['slider_id'] ?? 0 ) );
		if ( ! $slider_id && ! xs_can_create_slider() ) {
			self::redirect_to_slider_limit();
		}

		$current_slider = $slider_id ? xs_get_slider( $slider_id ) : null;
		$redirect_id    = $slider_id;
		$slide_limit    = xs_get_edit_max_slides( $slider_id );
		$visible_limit  = xs_get_edit_max_visible_slides( $slider_id );
		$title          = sanitize_text_field( wp_unslash( $_POST['title'] ?? '' ) );
		$layout         = sanitize_text_field( wp_unslash( $_POST['layout'] ?? 'default' ) );
		$layout         = in_array( $layout, array( 'default', 'cool', '3d', 'options' ), true ) ? $layout : 'default';
		$visible_count  = max( 1, min( $visible_limit, absint( wp_unslash( $_POST['visible_count'] ?? 3 ) ) ) );
		$autoplay       = isset( $_POST['autoplay'] ) ? 1 : 0;
		$autoplay_speed = max( 2000, min( 10000, absint( wp_unslash( $_POST['autoplay_speed'] ?? 4000 ) ) ) );
		$fullscreen     = isset( $_POST['fullscreen'] ) ? 1 : 0;
		$title_shadow   = isset( $_POST['title_shadow'] ) ? 1 : 0;
		$square_corners = isset( $_POST['square_corners'] ) ? 1 : 0;
		$black_arrows   = isset( $_POST['black_arrows'] ) ? 1 : 0;
		$current_ratio  = $current_slider->image_ratio ?? xs_setting( 'defaults', 'image_ratio', '16:10' );
		$image_ratio    = xs_sanitize_image_ratio( sanitize_text_field( wp_unslash( $_POST['image_ratio'] ?? '16:10' ) ), 'editor', $current_ratio );
		$fixed_height   = xs_sanitize_fixed_height( absint( wp_unslash( $_POST['fixed_height'] ?? 0 ) ) );
		$link_hover_color = xs_sanitize_hex_color( sanitize_text_field( wp_unslash( $_POST['link_hover_color'] ?? '' ) ) ) ?: '#ee212b';
		$gradient_start   = xs_sanitize_hex_color( sanitize_text_field( wp_unslash( $_POST['gradient_start'] ?? '' ) ) );
		$gradient_end     = xs_sanitize_hex_color( sanitize_text_field( wp_unslash( $_POST['gradient_end'] ?? '' ) ) );
		$bg_color_3d      = xs_sanitize_hex_color( sanitize_text_field( wp_unslash( $_POST['bg_color_3d'] ?? '' ) ) );
		$bg_color_options = xs_sanitize_hex_color( sanitize_text_field( wp_unslash( $_POST['bg_color_options'] ?? '' ) ) );
		$status         = sanitize_text_field( wp_unslash( $_POST['status'] ?? 'active' ) );
		$status         = in_array( $status, array( 'active', 'draft' ), true ) ? $status : 'active';
		$now            = current_time( 'mysql' );

		$data = array(
			'title'          => $title,
			'layout'         => $layout,
			'visible_count'  => $visible_count,
			'autoplay'       => $autoplay,
			'autoplay_speed' => $autoplay_speed,
			'fullscreen'     => $fullscreen,
			'image_ratio'      => $image_ratio,
			'fixed_height'     => $fixed_height,
			'title_shadow'     => $title_shadow,
			'square_corners'   => $square_corners,
			'black_arrows'     => $black_arrows,
			'link_hover_color' => $link_hover_color,
			'gradient_start'   => $gradient_start,
			'gradient_end'   => $gradient_end,
			'bg_color_3d'      => $bg_color_3d,
			'bg_color_options' => $bg_color_options,
			'status'         => $status,
			'updated_at'     => $now,
		);

		$format = array( '%s', '%s', '%d', '%d', '%d', '%d', '%s', '%d', '%d', '%d', '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s' );

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching -- Transaction protects slider/slides from partial writes.
		$wpdb->query( 'START TRANSACTION' );

		if ( $slider_id ) {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Custom table, update operation.
			$result = $wpdb->update( $wpdb->prefix . 'xs_sliders', $data, array( 'id' => $slider_id ), $format, array( '%d' ) );
			if ( false === $result ) {
				self::rollback_and_fail( $redirect_id, $wpdb->last_error );
			}
		} else {
			$data['created_at'] = $now;
			$format[]           = '%s';
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery -- Custom table, insert operation.
			$result = $wpdb->insert( $wpdb->prefix . 'xs_sliders', $data, $format );
			if ( false === $result || ! $wpdb->insert_id ) {
				self::rollback_and_fail( 0, $wpdb->last_error );
			}
			$slider_id   = (int) $wpdb->insert_id;
			$redirect_id = $slider_id;
		}

		// Save slides.
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Custom table, delete before re-insert.
		$deleted = $wpdb->delete( $wpdb->prefix . 'xs_slides', array( 'slider_id' => $slider_id ), array( '%d' ) );
		if ( false === $deleted ) {
			self::rollback_and_fail( $redirect_id, $wpdb->last_error );
		}

		if ( ! empty( $_POST['slides']['image_id'] ) && is_array( $_POST['slides']['image_id'] ) ) {
			$image_ids    = array_map( 'absint', wp_unslash( $_POST['slides']['image_id'] ) );
			$image_urls   = array_map( 'esc_url_raw', wp_unslash( $_POST['slides']['image_url'] ?? array() ) );
			$titles       = array_map( 'sanitize_text_field', wp_unslash( $_POST['slides']['title'] ?? array() ) );
			$captions     = array_map( 'sanitize_text_field', wp_unslash( $_POST['slides']['caption'] ?? array() ) );
			$descriptions = array_map( 'sanitize_text_field', wp_unslash( $_POST['slides']['description'] ?? array() ) );
			$link_urls    = array_map( 'sanitize_text_field', wp_unslash( $_POST['slides']['link_url'] ?? array() ) );
			// HTML content is saved raw for users with unfiltered_html; otherwise sanitized via wp_kses_post in the loop below.
			// phpcs:disable WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Per-item sanitization happens in the foreach below; admins with unfiltered_html may intentionally save raw HTML.
			$raw_html     = isset( $_POST['slides']['html_content'] ) && is_array( $_POST['slides']['html_content'] )
				? wp_unslash( $_POST['slides']['html_content'] )
				: array();
			// phpcs:enable WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			$html_contents = array();
			foreach ( $raw_html as $html ) {
				$html_contents[] = current_user_can( 'unfiltered_html' ) ? (string) $html : wp_kses_post( (string) $html );
			}

			$max = min( count( $image_ids ), $slide_limit );
			for ( $i = 0; $i < $max; $i++ ) {
				if ( ! $image_ids[ $i ] ) {
					continue;
				}
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery -- Custom table, insert operation.
				$inserted = $wpdb->insert(
					$wpdb->prefix . 'xs_slides',
					array(
						'slider_id'    => $slider_id,
						'image_id'     => $image_ids[ $i ],
						'image_url'    => $image_urls[ $i ] ?? '',
						'title'        => $titles[ $i ] ?? '',
						'caption'      => $captions[ $i ] ?? '',
						'description'  => $descriptions[ $i ] ?? '',
						'link_url'     => $link_urls[ $i ] ?? '',
						'html_content' => $html_contents[ $i ] ?? '',
						'sort_order'   => $i,
						'created_at'   => $now,
					),
					array( '%d', '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%s' )
				);

				if ( false === $inserted ) {
					self::rollback_and_fail( $redirect_id, $wpdb->last_error );
				}
			}
		}

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching -- Commit completes the atomic save.
		$wpdb->query( 'COMMIT' );

		// Redirect to edit page with success message.
		wp_safe_redirect( admin_url( 'admin.php?page=xs-edit&slider_id=' . $slider_id . '&saved=1' ) );
		exit;
	}

	private static function rollback_and_fail( $slider_id, $error = '' ) {
		global $wpdb;

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching -- Best-effort rollback for partial writes.
		$wpdb->query( 'ROLLBACK' );

		if ( $error && defined( 'WP_DEBUG' ) && WP_DEBUG && defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG ) {
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- Gated by WP_DEBUG + WP_DEBUG_LOG; only logs failed DB writes during a transaction rollback.
			error_log( 'Xtreme Slider save failed: ' . $error );
		}

		$url = admin_url( 'admin.php?page=xs-edit&xs_error=save_failed' );
		if ( $slider_id ) {
			$url = add_query_arg( 'slider_id', (int) $slider_id, $url );
		}

		wp_safe_redirect( $url );
		exit;
	}

	private static function redirect_to_slider_limit() {
		wp_safe_redirect( admin_url( 'admin.php?page=xtreme-slider&xs_error=slider_limit' ) );
		exit;
	}
}
