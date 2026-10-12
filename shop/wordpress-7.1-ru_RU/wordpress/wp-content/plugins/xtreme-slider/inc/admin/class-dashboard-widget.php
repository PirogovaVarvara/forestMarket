<?php
/**
 * "Xtreme Slider — Overview" WordPress dashboard widget.
 *
 * Adds an at-a-glance panel to the wp-admin Dashboard: slider count, quick
 * actions, feature highlights, a Pro upsell (free tier only) and helpful links.
 *
 * @package XtremeSlider
 */

defined( 'ABSPATH' ) || exit;

class XS_Dashboard_Widget {

	/** Dashboard widget ID (also used to scope the widget CSS). */
	const WIDGET_ID = 'xs_overview_widget';

	public function __construct() {
		add_action( 'wp_dashboard_setup', array( $this, 'register_widget' ) );
	}

	/**
	 * Register the dashboard widget for administrators.
	 */
	public function register_widget() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		wp_add_dashboard_widget(
			self::WIDGET_ID,
			__( 'Xtreme Slider — Overview', 'xtreme-slider' ),
			array( $this, 'render' ),
			null,
			null,
			'normal',
			'high'
		);
	}

	/**
	 * Render the widget contents.
	 */
	public function render() {
		$total       = xs_count_all_sliders();
		$is_premium  = xs_is_premium_license_active();
		$create_url  = xs_can_create_slider()
			? admin_url( 'admin.php?page=xs-edit' )
			: admin_url( 'admin.php?page=xtreme-slider' );
		$list_url    = admin_url( 'admin.php?page=xtreme-slider' );
		$settings_url = admin_url( 'admin.php?page=xs-settings' );

		// xtremeplugins.com links (routes verified against the live Next.js app).
		$plugin_url  = 'https://xtremeplugins.com/plugins/xtreme-slider';
		$docs_url    = 'https://xtremeplugins.com/docs/xtreme-slider';
		$support_url = 'https://xtremeplugins.com/plugins/xtreme-slider/support';
		$blog_url    = 'https://xtremeplugins.com/blog';
		$buy_url     = 'https://xtremeplugins.com/plugins/xtreme-slider#pricing';

		$features = array(
			array( 'dashicons-images-alt2',   __( '3D Perspective Slider', 'xtreme-slider' ) ),
			array( 'dashicons-art',           __( 'Cool Gradient Layout', 'xtreme-slider' ) ),
			array( 'dashicons-controls-play', __( 'Autoplay & Fullscreen', 'xtreme-slider' ) ),
			array( 'dashicons-shortcode',     __( 'Elementor & Shortcode', 'xtreme-slider' ) ),
		);
		?>
		<style>
			#<?php echo esc_html( self::WIDGET_ID ); ?> .xso-stats { display:flex; gap:10px; margin:0 0 14px; }
			#<?php echo esc_html( self::WIDGET_ID ); ?> .xso-stat { flex:1; text-align:center; padding:14px 10px; border-radius:8px; background:linear-gradient(135deg,#eef2ff,#f5f3ff); border:1px solid #e0e7ff; }
			#<?php echo esc_html( self::WIDGET_ID ); ?> .xso-stat strong { display:block; font-size:24px; font-weight:700; color:#1e1b4b; line-height:1.1; }
			#<?php echo esc_html( self::WIDGET_ID ); ?> .xso-stat span { display:block; margin-top:4px; color:#4b5563; font-size:12px; }
			#<?php echo esc_html( self::WIDGET_ID ); ?> .xso-actions { display:flex; flex-wrap:wrap; gap:8px; margin:0 0 16px; }
			#<?php echo esc_html( self::WIDGET_ID ); ?> h4.xso-title { margin:0 0 10px; font-size:14px; color:#1d2327; }
			#<?php echo esc_html( self::WIDGET_ID ); ?> .xso-grid { display:grid; grid-template-columns:1fr 1fr; gap:8px; margin:0 0 16px; }
			#<?php echo esc_html( self::WIDGET_ID ); ?> .xso-feature { display:flex; align-items:center; gap:8px; padding:9px 10px; border-radius:8px; background:#f6f7f7; border:1px solid #e2e4e7; font-weight:500; transition:background .15s ease, transform .15s ease; }
			#<?php echo esc_html( self::WIDGET_ID ); ?> .xso-feature:hover { background:#eef4ff; transform:translateY(-1px); }
			#<?php echo esc_html( self::WIDGET_ID ); ?> .xso-feature .dashicons { color:#4f46e5; width:20px; height:20px; font-size:20px; }
			#<?php echo esc_html( self::WIDGET_ID ); ?> .xso-upsell { padding:14px; border-radius:10px; margin:0 0 16px; background:linear-gradient(135deg,#eff6ff,#faf5ff); border:1px solid #dbeafe; }
			#<?php echo esc_html( self::WIDGET_ID ); ?> .xso-upsell ul { margin:8px 0 12px; }
			#<?php echo esc_html( self::WIDGET_ID ); ?> .xso-upsell li { margin:0 0 4px; color:#374151; }
			#<?php echo esc_html( self::WIDGET_ID ); ?> .xso-gopro.button-primary { background:#6d28d9; border-color:#6d28d9; text-shadow:none; box-shadow:none; }
			#<?php echo esc_html( self::WIDGET_ID ); ?> .xso-gopro.button-primary:hover { background:#5b21b6; border-color:#5b21b6; }
			#<?php echo esc_html( self::WIDGET_ID ); ?> .xso-blog { margin:0 0 12px; }
			#<?php echo esc_html( self::WIDGET_ID ); ?> .xso-blog-list { margin:0; padding-left:18px; }
			#<?php echo esc_html( self::WIDGET_ID ); ?> .xso-blog-list li { margin:0 0 6px; list-style:disc; }
			#<?php echo esc_html( self::WIDGET_ID ); ?> .xso-badge { display:inline-block; background:#6d28d9; color:#fff; font-size:10px; font-weight:600; padding:1px 6px; border-radius:3px; margin-right:6px; vertical-align:1px; }
			#<?php echo esc_html( self::WIDGET_ID ); ?> .xso-footer { display:flex; flex-wrap:wrap; gap:14px; margin-top:6px; padding-top:12px; border-top:1px solid #dcdcde; font-size:13px; }
			#<?php echo esc_html( self::WIDGET_ID ); ?> .xso-footer a { text-decoration:none; color:#2271b1; display:inline-flex; align-items:center; gap:2px; }
			#<?php echo esc_html( self::WIDGET_ID ); ?> .xso-footer a.xso-gopro { color:#00a32a; font-weight:600; }
			#<?php echo esc_html( self::WIDGET_ID ); ?> .xso-footer .dashicons { font-size:15px; width:15px; height:15px; }
		</style>

		<div class="xso">
			<div class="xso-stats">
				<div class="xso-stat">
					<strong><?php echo esc_html( number_format_i18n( $total ) ); ?></strong>
					<span><?php esc_html_e( 'Total Sliders', 'xtreme-slider' ); ?></span>
				</div>
				<div class="xso-stat">
					<strong style="font-size:16px;line-height:1.6;color:<?php echo $is_premium ? '#7c3aed' : '#0f766e'; ?>;">
						<?php echo $is_premium ? esc_html__( 'Premium', 'xtreme-slider' ) : esc_html__( 'Free', 'xtreme-slider' ); ?>
					</strong>
					<span><?php esc_html_e( 'Current Plan', 'xtreme-slider' ); ?></span>
				</div>
			</div>

			<div class="xso-actions">
				<a href="<?php echo esc_url( $create_url ); ?>" class="button button-primary"><?php esc_html_e( 'Create Slider', 'xtreme-slider' ); ?></a>
				<a href="<?php echo esc_url( $list_url ); ?>" class="button"><?php esc_html_e( 'All Sliders', 'xtreme-slider' ); ?></a>
				<a href="<?php echo esc_url( $settings_url ); ?>" class="button"><?php esc_html_e( 'Settings', 'xtreme-slider' ); ?></a>
			</div>

			<h4 class="xso-title"><?php esc_html_e( 'Popular Features', 'xtreme-slider' ); ?></h4>
			<div class="xso-grid">
				<?php foreach ( $features as $feature ) : ?>
					<div class="xso-feature">
						<span class="dashicons <?php echo esc_attr( $feature[0] ); ?>" aria-hidden="true"></span>
						<span><?php echo esc_html( $feature[1] ); ?></span>
					</div>
				<?php endforeach; ?>
			</div>

			<?php if ( ! $is_premium ) : ?>
				<div class="xso-upsell">
					<h4 class="xso-title"><?php esc_html_e( 'Unlock Premium', 'xtreme-slider' ); ?></h4>
					<ul>
						<li><?php esc_html_e( '✔ Unlimited sliders', 'xtreme-slider' ); ?></li>
						<li><?php esc_html_e( '✔ Up to 50 images per slider', 'xtreme-slider' ); ?></li>
						<li><?php esc_html_e( '✔ Up to 15 visible slides at once', 'xtreme-slider' ); ?></li>
						<li><?php esc_html_e( '✔ Original-ratio (mixed portrait/landscape) images', 'xtreme-slider' ); ?></li>
					</ul>
					<a href="<?php echo esc_url( $buy_url ); ?>" target="_blank" rel="noopener noreferrer" class="button button-primary xso-gopro"><?php esc_html_e( 'Upgrade Now', 'xtreme-slider' ); ?></a>
				</div>
			<?php endif; ?>

			<div class="xso-blog">
				<h4 class="xso-title"><?php esc_html_e( 'Latest from the blog', 'xtreme-slider' ); ?></h4>
				<ul class="xso-blog-list">
					<li>
						<span class="xso-badge"><?php esc_html_e( 'NEW', 'xtreme-slider' ); ?></span>
						<a href="<?php echo esc_url( $plugin_url ); ?>" target="_blank" rel="noopener noreferrer">
							<?php
							printf(
								/* translators: %s: plugin version number. */
								esc_html__( 'What\'s new in Xtreme Slider %s', 'xtreme-slider' ),
								esc_html( XS_VERSION )
							);
							?>
						</a>
					</li>
					<li>
						<a href="<?php echo esc_url( $blog_url ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Tips & tutorials on the Xtreme Plugins blog', 'xtreme-slider' ); ?></a>
					</li>
				</ul>
			</div>

			<div class="xso-footer">
				<a href="<?php echo esc_url( $docs_url ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Docs', 'xtreme-slider' ); ?> <span class="dashicons dashicons-external" aria-hidden="true"></span></a>
				<a href="<?php echo esc_url( $support_url ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Support', 'xtreme-slider' ); ?> <span class="dashicons dashicons-external" aria-hidden="true"></span></a>
				<a href="<?php echo esc_url( $blog_url ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Blog', 'xtreme-slider' ); ?> <span class="dashicons dashicons-external" aria-hidden="true"></span></a>
				<a href="<?php echo esc_url( $buy_url ); ?>" target="_blank" rel="noopener noreferrer" class="xso-gopro"><?php esc_html_e( 'Buy Now', 'xtreme-slider' ); ?> <span class="dashicons dashicons-external" aria-hidden="true"></span></a>
			</div>
		</div>
		<?php
	}
}
