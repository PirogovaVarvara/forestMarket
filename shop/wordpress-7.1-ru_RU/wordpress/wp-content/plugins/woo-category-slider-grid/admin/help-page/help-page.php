<?php
/**
 * The help page for the Reno Product Category
 *
 * @package WooCategory
 * @subpackage woo-category-slider-grid/admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}  // if direct access.

/**
 * The help class for the Reno Product Category
 */
class Woo_Category_Slider_Help {

	/**
	 * Single instance of the class
	 *
	 * @var null
	 */
	protected static $instance = null;

	/**
	 * Plugins Path variable.
	 *
	 * @var array
	 */
	protected static $plugins = array(
		'woo-product-slider'             => 'main.php',
		'gallery-slider-for-woocommerce' => 'woo-gallery-slider.php',
		'post-carousel'                  => 'main.php',
		'easy-accordion-free'            => 'plugin-main.php',
		'logo-carousel-free'             => 'main.php',
		'location-weather'               => 'main.php',
		'woo-quickview'                  => 'woo-quick-view.php',
		'wp-expand-tabs-free'            => 'plugin-main.php',

	);

	/**
	 * Welcome pages
	 *
	 * @var array
	 */
	public $pages = array(
		'wcsp_help',
	);


	/**
	 * Not show this plugin list.
	 *
	 * @var array
	 */
	protected static $not_show_plugin_list = array( 'aitasi-coming-soon', 'latest-posts', 'widget-post-slider', 'easy-lightbox-wp' );

	/**
	 * Help page construct function.
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'help_admin_menu' ), 80 );

        $page   = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '';// @codingStandardsIgnoreLine
		if ( 'wcsp_help' !== $page ) {
			return;
		}
		add_action( 'admin_print_scripts', array( $this, 'disable_admin_notices' ) );
		add_action( 'spf_enqueue', array( $this, 'help_page_enqueue_scripts' ) );
	}

	/**
	 * Main Help page Instance
	 *
	 * @static
	 * @return self Main instance
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Help_page_enqueue_scripts function.
	 *
	 * @return void
	 */
	public function help_page_enqueue_scripts() {
		wp_enqueue_style( 'sp-woo-cat-slider-help', SP_WCS_URL . 'admin/help-page/css/help-page.min.css', array(), SP_WCS_VERSION );
		wp_enqueue_style( 'sp-woo-cat-slider-fontello', SP_WCS_URL . 'admin/help-page/css/fontello.min.css', array(), SP_WCS_VERSION );

		wp_enqueue_script( 'sp-woo-cat-slider-help', SP_WCS_URL . 'admin/help-page/js/help-page.min.js', array(), SP_WCS_VERSION, true );
	}

	/**
	 * Add admin menu.
	 *
	 * @return void
	 */
	public function help_admin_menu() {
		add_submenu_page(
			'edit.php?post_type=sp_wcslider',
			esc_html__( 'Reno Product Category', 'woo-category-slider-grid' ),
			esc_html__( 'Lite vs Pro', 'woo-category-slider-grid' ),
			'manage_options',
			'edit.php?post_type=sp_wcslider&page=wcsp_help#lite-to-pro'
		);
		add_submenu_page(
			'edit.php?post_type=sp_wcslider',
			esc_html__( 'Reno Product Category Help', 'woo-category-slider-grid' ),
			esc_html__( 'Get Started', 'woo-category-slider-grid' ),
			'manage_options',
			'wcsp_help',
			array(
				$this,
				'help_page_callback',
			)
		);

		$this->add_submenu_classes();
	}

	/**
	 * Tag the plugin submenu items with dedicated CSS classes.
	 *
	 * WordPress has no argument for this on add_submenu_page(), so the class
	 * slot (index 4) of the registered items is filled in directly. It keeps
	 * the admin menu styling off brittle :nth-child() selectors.
	 *
	 * @return void
	 */
	private function add_submenu_classes() {
		global $submenu;

		$parent = 'edit.php?post_type=sp_wcslider';

		if ( empty( $submenu[ $parent ] ) ) {
			return;
		}

		$classes = array(
			'edit.php?post_type=sp_wcslider&page=wcsp_help#lite-to-pro' => 'spwoocs-menu-lite-vs-pro',
			'wcsp_help' => 'spwoocs-menu-get-started',
		);

		foreach ( $submenu[ $parent ] as $index => $item ) {
			if ( isset( $item[2], $classes[ $item[2] ] ) ) {
				$submenu[ $parent ][ $index ][4] = isset( $item[4] ) ? trim( $item[4] . ' ' . $classes[ $item[2] ] ) : $classes[ $item[2] ]; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
			}
		}
	}

	/**
	 * Build the URL of a help page tab.
	 *
	 * @param string $hash Tab hash without the leading "#".
	 * @return string
	 */
	private function tab_url( $hash = '' ) {
		$url = admin_url( 'edit.php?post_type=sp_wcslider&page=wcsp_help' );

		return $hash ? $url . '#' . $hash : $url;
	}

	/**
	 * Spwoocs_ajax_help_page function.
	 *
	 * @return void
	 */
	public function spwoocs_plugins_info_api_help_page() {
		$plugins_arr = get_transient( 'spwoocs_plugins' );
		if ( false === $plugins_arr ) {
			$args = array(
				'author'   => 'shapedplugin',
				'per_page' => '120',
				'page'     => '1',
				'fields'   => array(
					'slug',
					'name',
					'version',
					'downloaded',
					'active_installs',
					'last_updated',
					'rating',
					'num_ratings',
					'short_description',
					'author',
					'icons',
				),
			);

			if ( ! function_exists( 'plugins_api' ) ) {
				require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
			}

			$plugin_info = plugins_api( 'query_plugins', $args );

			if ( ! is_wp_error( $plugin_info ) ) {

				$plugins_arr = array();
				if ( isset( $plugin_info->plugins ) && ( count( $plugin_info->plugins ) > 0 ) ) {
					foreach ( $plugin_info->plugins as $pl ) {
						if ( ! in_array( $pl['slug'], self::$not_show_plugin_list, true ) ) {
							$plugins_arr[] = array(
								'slug'              => $pl['slug'],
								'name'              => $pl['name'],
								'version'           => $pl['version'],
								'downloaded'        => $pl['downloaded'],
								'active_installs'   => $pl['active_installs'],
								'last_updated'      => strtotime( $pl['last_updated'] ),
								'rating'            => $pl['rating'],
								'num_ratings'       => $pl['num_ratings'],
								'short_description' => $pl['short_description'],
								'icons'             => $pl['icons']['2x'],
							);
						}
					}
				}

				set_transient( 'spwoocs_plugins', $plugins_arr, 24 * HOUR_IN_SECONDS );
			}
		}

		if ( is_array( $plugins_arr ) && ( count( $plugins_arr ) > 0 ) ) {
			$active_installs = array_column( $plugins_arr, 'active_installs' );
			array_multisort( $active_installs, SORT_DESC, $plugins_arr );

			foreach ( $plugins_arr as $plugin ) {
				$plugin_slug = $plugin['slug'];
				$plugin_icon = $plugin['icons'];
				// Skip the current plugin.
				if ( 'woo-category-slider-grid' === $plugin_slug ) {
					continue;
				}

				if ( isset( self::$plugins[ $plugin_slug ] ) ) {
					$plugin_file = self::$plugins[ $plugin_slug ];
				} else {
					$plugin_file = $plugin_slug . '.php';
				}

				$details_link = network_admin_url( 'plugin-install.php?tab=plugin-information&amp;plugin=' . $plugin['slug'] . '&amp;TB_iframe=true&amp;width=745&amp;height=550' );
				?>
				<div class="plugin-card <?php echo esc_attr( $plugin_slug ); ?>" id="<?php echo esc_attr( $plugin_slug ); ?>">
					<div class="plugin-card-top">
						<div class="name column-name">
							<h3>
								<a class="thickbox" title="<?php echo esc_attr( $plugin['name'] ); ?>" href="<?php echo esc_url( $details_link ); ?>">
						<?php echo esc_html( $plugin['name'] ); ?>
									<img src="<?php echo esc_url( $plugin_icon ); ?>" class="plugin-icon"/>
								</a>
							</h3>
						</div>
						<div class="action-links">
							<ul class="plugin-action-buttons">
								<li>
						<?php
						if ( $this->is_plugin_installed( $plugin_slug, $plugin_file ) ) {
							if ( $this->is_plugin_active( $plugin_slug, $plugin_file ) ) {
								?>
										<button type="button" class="button button-disabled" disabled="disabled">Active</button>
									<?php
							} else {
								?>
											<a href="<?php echo esc_url( $this->activate_plugin_link( $plugin_slug, $plugin_file ) ); ?>" class="button button-primary activate-now">
									<?php esc_html_e( 'Activate', 'woo-category-slider-grid' ); ?>
											</a>
									<?php
							}
						} else {
							?>
										<a href="<?php echo esc_url( $this->install_plugin_link( $plugin_slug ) ); ?>" class="button install-now">
								<?php esc_html_e( 'Install Now', 'woo-category-slider-grid' ); ?>
										</a>
								<?php } ?>
								</li>
								<li>
									<a href="<?php echo esc_url( $details_link ); ?>" class="thickbox open-plugin-details-modal" aria-label="<?php echo esc_attr( 'More information about ' . $plugin['name'] ); ?>" title="<?php echo esc_attr( $plugin['name'] ); ?>">
								<?php esc_html_e( 'More Details', 'woo-category-slider-grid' ); ?>
									</a>
								</li>
							</ul>
						</div>
						<div class="desc column-description">
							<p><?php echo esc_html( isset( $plugin['short_description'] ) ? $plugin['short_description'] : '' ); ?></p>
							<p class="authors"> <cite>By <a href="https://shapedplugin.com/">ShapedPlugin LLC</a></cite></p>
						</div>
					</div>
					<?php
					echo '<div class="plugin-card-bottom">';

					if ( isset( $plugin['rating'], $plugin['num_ratings'] ) ) {
						?>
						<div class="vers column-rating">
							<?php
							wp_star_rating(
								array(
									'rating' => $plugin['rating'],
									'type'   => 'percent',
									'number' => $plugin['num_ratings'],
								)
							);
							?>
							<span class="num-ratings">(<?php echo esc_html( number_format_i18n( $plugin['num_ratings'] ) ); ?>)</span>
						</div>
						<?php
					}
					if ( isset( $plugin['version'] ) ) {
						?>
						<div class="column-updated">
							<strong><?php esc_html_e( 'Version:', 'woo-category-slider-grid' ); ?></strong>
							<span><?php echo esc_html( $plugin['version'] ); ?></span>
						</div>
							<?php
					}

					if ( isset( $plugin['active_installs'] ) ) {
						?>
						<div class="column-downloaded">
						<?php echo esc_html( number_format_i18n( $plugin['active_installs'] ) ) . esc_html__( '+ Active Installations', 'woo-category-slider-grid' ); ?>
						</div>
									<?php
					}

					if ( isset( $plugin['last_updated'] ) ) {
						?>
						<div class="column-compatibility">
							<strong><?php esc_html_e( 'Last Updated:', 'woo-category-slider-grid' ); ?></strong>
							<span><?php echo esc_html( human_time_diff( $plugin['last_updated'] ) ) . ' ' . esc_html__( 'ago', 'woo-category-slider-grid' ); ?></span>
						</div>
									<?php
					}

					echo '</div>';
					?>
				</div>
				<?php
			}
		}
	}

	/**
	 * Check plugins installed function.
	 *
	 * @param string $plugin_slug Plugin slug.
	 * @param string $plugin_file Plugin file.
	 * @return boolean
	 */
	public function is_plugin_installed( $plugin_slug, $plugin_file ) {
		return file_exists( WP_PLUGIN_DIR . '/' . $plugin_slug . '/' . $plugin_file );
	}

	/**
	 * Check active plugin function
	 *
	 * @param string $plugin_slug Plugin slug.
	 * @param string $plugin_file Plugin file.
	 * @return boolean
	 */
	public function is_plugin_active( $plugin_slug, $plugin_file ) {
		return is_plugin_active( $plugin_slug . '/' . $plugin_file );
	}

	/**
	 * Install plugin link.
	 *
	 * @param string $plugin_slug Plugin slug.
	 * @return string
	 */
	public function install_plugin_link( $plugin_slug ) {
		return wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=' . $plugin_slug ), 'install-plugin_' . $plugin_slug );
	}

	/**
	 * Active Plugin Link function
	 *
	 * @param string $plugin_slug Plugin slug.
	 * @param string $plugin_file Plugin file.
	 * @return string
	 */
	public function activate_plugin_link( $plugin_slug, $plugin_file ) {
		return wp_nonce_url( admin_url( 'edit.php?post_type=sp_wcslider&page=wcsp_help&action=activate&plugin=' . $plugin_slug . '/' . $plugin_file . '#recommended' ), 'activate-plugin_' . $plugin_slug . '/' . $plugin_file );
	}

	/**
	 * Making page as clean as possible
	 */
	public function disable_admin_notices() {

		global $wp_filter;

		if ( isset( $_GET['post_type'] ) && isset( $_GET['page'] ) && 'sp_wcslider' === wp_unslash( $_GET['post_type'] ) && in_array( wp_unslash( $_GET['page'] ), $this->pages ) ) { // @codingStandardsIgnoreLine

			if ( isset( $wp_filter['user_admin_notices'] ) ) {
				unset( $wp_filter['user_admin_notices'] );
			}
			if ( isset( $wp_filter['admin_notices'] ) ) {
				unset( $wp_filter['admin_notices'] );
			}
			if ( isset( $wp_filter['all_admin_notices'] ) ) {
				unset( $wp_filter['all_admin_notices'] );
			}

			// The purge above drops every notice, third-party ones included. Put the
			// rebrand announcement back: it is a one-off, site-wide dismissible bar
			// that has to reach people who land straight on this page. Its own
			// stylesheet is already enqueued by then, because this runs on
			// admin_print_scripts, after admin_enqueue_scripts.
			if ( class_exists( 'Woo_Category_Slider_Rebrand_Notice' ) ) {
				add_action( 'admin_notices', array( Woo_Category_Slider_Rebrand_Notice::instance(), 'render_rebrand_notice' ) );
			}
		}
	}

	/**
	 * The Reno Product Category Help Callback.
	 *
	 * @return void
	 */
	public function help_page_callback() {
		add_thickbox();

		$action   = isset( $_GET['action'] ) ? sanitize_text_field( wp_unslash( $_GET['action'] ) ) : '';
		$plugin   = isset( $_GET['plugin'] ) ? sanitize_text_field( wp_unslash( $_GET['plugin'] ) ) : '';
		$_wpnonce = isset( $_GET['_wpnonce'] ) ? sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ) : '';

		if ( isset( $action, $plugin ) && ( 'activate' === $action ) && wp_verify_nonce( $_wpnonce, 'activate-plugin_' . $plugin ) && current_user_can( 'activate_plugins' ) ) {
			activate_plugin( $plugin, '', false, true );
		}

		if ( isset( $action, $plugin ) && ( 'deactivate' === $action ) && wp_verify_nonce( $_wpnonce, 'deactivate-plugin_' . $plugin ) && current_user_can( 'deactivate_plugins' ) ) {
			deactivate_plugins( $plugin, '', false );
		}

		$img         = SP_WCS_URL . 'admin/help-page/img/';
		$review_url  = 'https://wordpress.org/support/plugin/woo-category-slider-grid/reviews/';
		$support_url = 'https://shapedplugin.com/create-new-ticket/';
		?>
		<div class="sp-woo-cat-slider-help">
			<div class="spwoocs-topbar">
				<div class="spwoocs-topbar__inner">
					<div class="spwoocs-topbar__start">
						<div class="spwoocs-topbar__brand">
							<img class="spwoocs-topbar__logo" src="<?php echo esc_url( $img . 'reno-product-cat-logo.svg' ); ?>" alt="<?php esc_attr_e( 'Reno Product Category', 'woo-category-slider-grid' ); ?>" width="128" height="24">
							<span class="spwoocs-topbar__version">
								<img src="<?php echo esc_url( $img . 'version-icon.svg' ); ?>" alt="" width="12" height="12">
								<?php echo esc_html( SP_WCS_VERSION ); ?>
							</span>
						</div>
						<nav class="spwoocs-header-nav-menu" aria-label="<?php esc_attr_e( 'Reno Product Category help navigation', 'woo-category-slider-grid' ); ?>">
							<ul>
								<li><a class="active" data-id="get-start-tab" href="<?php echo esc_url( $this->tab_url( 'get-start' ) ); ?>"><?php esc_html_e( 'Get Started', 'woo-category-slider-grid' ); ?></a></li>
								<li class="spwoocs-header-nav-menu__separator" aria-hidden="true"></li>
								<li><a data-id="lite-to-pro-tab" href="<?php echo esc_url( $this->tab_url( 'lite-to-pro' ) ); ?>"><?php esc_html_e( 'Lite vs Pro', 'woo-category-slider-grid' ); ?></a></li>
								<li><a data-id="recommended-tab" href="<?php echo esc_url( $this->tab_url( 'recommended' ) ); ?>"><i class="spwoocs-nav-icon spwoocs-nav-icon--plugins" aria-hidden="true"></i><?php esc_html_e( 'Our Plugins', 'woo-category-slider-grid' ); ?></a></li>
								<li><a data-id="about-us-tab" href="<?php echo esc_url( $this->tab_url( 'about-us' ) ); ?>"><?php esc_html_e( 'About Us', 'woo-category-slider-grid' ); ?></a></li>
							</ul>
						</nav>
					</div>
					<a class="spwoocs-topbar__support" target="_blank" href="<?php echo esc_url( $support_url ); ?>">
						<img src="<?php echo esc_url( $img . 'get-help-icon.svg' ); ?>" alt="" width="15" height="15">
						<?php esc_html_e( 'Get Help', 'woo-category-slider-grid' ); ?>
					</a>
				</div>
			</div>
			<!-- Header section end -->

			<div class="spwoocs-help-content">
			<!-- Start Page -->
			<section class="spwoocs__help start-page" id="get-start-tab">
				<div class="spwoocs-start-page-wrap">
					<div class="spwoocs-welcome-card">
						<div class="spwoocs-welcome-card__intro">
							<h2 class="spwoocs-section-title-help"><?php esc_html_e( 'Welcome to Reno Product Category!', 'woo-category-slider-grid' ); ?></h2>
							<p><?php esc_html_e( 'Thank you for installing Reno Product Category! This video will help you get started with the plugin. Enjoy!', 'woo-category-slider-grid' ); ?></p>
						</div>
						<div class="spwoocs-welcome-card__actions">
							<a class="spwoocs-btn spwoocs-btn--primary" href="<?php echo esc_url( admin_url( 'post-new.php?post_type=sp_wcslider' ) ); ?>">
								<img src="<?php echo esc_url( $img . 'plus-icon.svg' ); ?>" alt="" width="14" height="14">
								<?php esc_html_e( 'Create a Category View', 'woo-category-slider-grid' ); ?>
							</a>
							<a class="spwoocs-btn spwoocs-btn--outline" target="_blank" href="https://demo.shapedplugin.com/woocategory/"><?php esc_html_e( 'Live Demo', 'woo-category-slider-grid' ); ?></a>
						</div>
						<div class="spwoocs-welcome-card__video">
							<iframe width="804" height="452" loading="lazy" src="https://www.youtube.com/embed/X_Czmx3ndjU?si=FG32mVzfhkC-3WEA" title="<?php esc_attr_e( 'Getting started with Reno Product Category', 'woo-category-slider-grid' ); ?>" frameborder="0" allowfullscreen></iframe>
						</div>
					</div>
					<div class="spwoocs-cta-cards">
						<?php
						$this->render_cta_card(
							$img . 'card-doc-icon.svg',
							__( 'Documentation', 'woo-category-slider-grid' ),
							__( 'Explore clear, well-organized documentation to understand features, settings, and get the most out of the plugin.', 'woo-category-slider-grid' ),
							__( 'Browse Now', 'woo-category-slider-grid' ),
							'https://docs.shapedplugin.com/docs/woocommerce-category-slider/introduction/'
						);
						$this->render_cta_card(
							$img . 'card-support-icon.svg',
							__( 'Technical Support', 'woo-category-slider-grid' ),
							__( 'Need assistance? Reach out to our expert support team for fast, reliable help with any issues or questions.', 'woo-category-slider-grid' ),
							__( 'Ask Now', 'woo-category-slider-grid' ),
							$support_url
						);
						$this->render_cta_card(
							$img . 'card-community-icon.svg',
							__( 'Show Your Love', 'woo-category-slider-grid' ),
							__( 'Join the official ShapedPlugin community to connect with other users, share ideas, and stay updated with the latest news.', 'woo-category-slider-grid' ),
							__( 'Rate Us', 'woo-category-slider-grid' ),
							$review_url
						);
						?>
					</div>
				</div>
			</section>

			<!-- Lite To Pro Page -->
			<section class="spwoocs__help lite-to-pro-page" id="lite-to-pro-tab">
				<div class="spwoocs-container">
					<div class="spwoocs-call-to-action-top">
						<h2 class="spwoocs-section-title-help">Lite vs Pro Comparison</h2>
						<a target="_blank" href="https://shapedplugin.com/reno-product-category/?ref=115#pricing" class='spwoocs-big-btn'>Upgrade to Pro Now!</a>
					</div>
					<div class="spwoocs-lite-to-pro-wrap">
						<div class="spwoocs-features">
							<ul>
								<li class='spwoocs-header'>
									<span class='spwoocs-title'>FEATURES</span>
									<span class='spwoocs-free'>Lite</span>
									<span class='spwoocs-pro'><i class='spwoocs-icon-pro'></i> PRO</span>
								</li>
								<li class='spwoocs-body'>
									<span class='spwoocs-title'>All Free Version Features</span>
									<span class='spwoocs-free spwoocs-check-icon'></span>
									<span class='spwoocs-pro spwoocs-check-icon'></span>
								</li>
								<li class='spwoocs-body'>
									<span class='spwoocs-title'>Category Layouts (Carousel, Slider, Grid, Hierarchy Grid, Inline, etc.)</span>
									<span class='spwoocs-free'><b>2</b></span>
									<span class='spwoocs-pro'><b>10+</b></span>
								</li>
								<li class='spwoocs-body'>
									<span class='spwoocs-title'>Ticker Mode Carousel</span>
									<span class='spwoocs-free spwoocs-close-icon'></span>
									<span class='spwoocs-pro spwoocs-check-icon'></span>
								</li>
								<li class='spwoocs-body'>
									<span class='spwoocs-title'>Show Child Category on Archive Page <i class="spwoocs-new">New</i></span>
									<span class='spwoocs-free spwoocs-close-icon'></span>
									<span class='spwoocs-pro spwoocs-check-icon'></span>
								</li>
								<li class='spwoocs-body'>
									<span class='spwoocs-title'>Display Parent, Child, Grand Child, and Great-grand Child Individually (Parent/All) <i class="spwoocs-hot">Hot</i></span>
									<span class='spwoocs-free spwoocs-close-icon'></span>
									<span class='spwoocs-pro spwoocs-check-icon'></span>
								</li>
								<li class='spwoocs-body'>
									<span class='spwoocs-title'>Parent and First-Level Child Selection Option <i class="spwoocs-new">New</i><i class="spwoocs-hot">Hot</i></span>
									<span class='spwoocs-free spwoocs-close-icon'></span>
									<span class='spwoocs-pro spwoocs-check-icon'></span>
								</li>
								<li class='spwoocs-body'>
									<span class='spwoocs-title'>Display Child Categories (Beside parent, below parent) <i class="spwoocs-hot">Hot</i></span>
									<span class='spwoocs-free spwoocs-close-icon'></span>
									<span class='spwoocs-pro spwoocs-check-icon'></span>
								</li>
								<li class='spwoocs-body'>
									<span class='spwoocs-title'>Filtering WooCommerce Categories, you want to show</span>
									<span class='spwoocs-free spwoocs-close-icon'></span>
									<span class='spwoocs-pro spwoocs-check-icon'></span>
								</li>
								<li class='spwoocs-body'>
									<span class='spwoocs-title'>Child Categories Product Count</span>
									<span class='spwoocs-free spwoocs-close-icon'></span>
									<span class='spwoocs-pro spwoocs-check-icon'></span>
								</li>
								<li class='spwoocs-body'>
									<span class='spwoocs-title'>Hide Empty Categories</span>
									<span class='spwoocs-free spwoocs-check-icon'></span>
									<span class='spwoocs-pro spwoocs-check-icon'></span>
								</li>
								<li class='spwoocs-body'>
									<span class='spwoocs-title'>Hide Category without Thumbnail</span>
									<span class='spwoocs-free spwoocs-close-icon'></span>
									<span class='spwoocs-pro spwoocs-check-icon'></span>
								</li>
								<li class='spwoocs-body'>
									<span class='spwoocs-title'>Display Categories Randomly</span>
									<span class='spwoocs-free spwoocs-close-icon'></span>
									<span class='spwoocs-pro spwoocs-check-icon'></span>
								</li>
								<li class='spwoocs-body'>
									<span class='spwoocs-title'>Category Content Position <i class="spwoocs-new">New</i></span>
									<span class='spwoocs-free'><b>1</b></span>
									<span class='spwoocs-pro'><b>5</b></span>
								</li>
								<li class='spwoocs-body'>
									<span class='spwoocs-title'>Display Card Style Category Showcase</span>
									<span class='spwoocs-free spwoocs-close-icon'></span>
									<span class='spwoocs-pro spwoocs-check-icon'></span>
								</li>
								<li class='spwoocs-body'>
									<span class='spwoocs-title'>Stylize Overlay Visibility, Content Color Type, Content Type (Fully Covered and Caption Style)</span>
									<span class='spwoocs-free spwoocs-close-icon'></span>
									<span class='spwoocs-pro spwoocs-check-icon'></span>
								</li>
								<li class='spwoocs-body'>
									<span class='spwoocs-title'>Equalize Categories/Items Height</span>
									<span class='spwoocs-free spwoocs-close-icon'></span>
									<span class='spwoocs-pro spwoocs-check-icon'></span>
								</li>
								<li class='spwoocs-body'>
									<span class='spwoocs-title'>Manage Category Content (Category Name, Product Count, Description, Name and Description Margin)</span>
									<span class='spwoocs-free spwoocs-check-icon'></span>
									<span class='spwoocs-pro spwoocs-check-icon'></span>
								</li>
								<li class='spwoocs-body'>
									<span class='spwoocs-title'>Display Category Icon, Icon Size, Custom Category Text</span>
									<span class='spwoocs-free spwoocs-close-icon'></span>
									<span class='spwoocs-pro spwoocs-check-icon'></span>
								</li>
								<li class='spwoocs-body'>
									<span class='spwoocs-title'>Add Category Icon from Icon Library <i class="spwoocs-hot">Hot</i></span>
									<span class='spwoocs-free spwoocs-close-icon'></span>
									<span class='spwoocs-pro spwoocs-check-icon'></span>
								</li>
								<li class='spwoocs-body'>
									<span class='spwoocs-title'>Add Thumbnail for Category Archive Pages</span>
									<span class='spwoocs-free spwoocs-close-icon'></span>
									<span class='spwoocs-pro spwoocs-check-icon'></span>
								</li>
								<li class='spwoocs-body'>
									<span class='spwoocs-title'>Stylize the Shop Now Button Label, Color, Border, Margin, etc.</span>
									<span class='spwoocs-free spwoocs-close-icon'></span>
									<span class='spwoocs-pro spwoocs-check-icon'></span>
								</li>
								<li class='spwoocs-body'>
									<span class='spwoocs-title'>Multiple Ajax Paginations (Load More, Number, and Infinite Scroll)</span>
									<span class='spwoocs-free spwoocs-close-icon'></span>
									<span class='spwoocs-pro spwoocs-check-icon'></span>
								</li>
								<li class='spwoocs-body'>
									<span class='spwoocs-title'>Category Items to Show Per Page/Click</span>
									<span class='spwoocs-free spwoocs-close-icon'></span>
									<span class='spwoocs-pro spwoocs-check-icon'></span>
								</li>
								<li class='spwoocs-body'>
									<span class='spwoocs-title'>Category Thumbnail Custom Dimension and Retina Ready Supported</span>
									<span class='spwoocs-free spwoocs-close-icon'></span>
									<span class='spwoocs-pro spwoocs-check-icon'></span>
								</li>
								<li class='spwoocs-body'>
									<span class='spwoocs-title'>Add Custom Thumbnail used as Placeholder.</span>
									<span class='spwoocs-free spwoocs-close-icon'></span>
									<span class='spwoocs-pro spwoocs-check-icon'></span>
								</li>
								<li class='spwoocs-body'>
									<span class='spwoocs-title'>Category Thumbnail Shapes (Square, Rounded, Circle, Custom) </span>
									<span class='spwoocs-free'><b>1</b></span>
									<span class='spwoocs-pro'><b>4</b></span>
								</li>
								<li class='spwoocs-body'>
									<span class='spwoocs-title'>Category Thumbnail Border, Box-shadow, Inner Padding, etc.</span>
									<span class='spwoocs-free spwoocs-close-icon'></span>
									<span class='spwoocs-pro spwoocs-check-icon'></span>
								</li>
								<li class='spwoocs-body'>
									<span class='spwoocs-title'>Category Thumbnail Zoom and Grayscale Modes</span>
									<span class='spwoocs-free spwoocs-close-icon'></span>
									<span class='spwoocs-pro spwoocs-check-icon'></span>
								</li>
								<li class='spwoocs-body'>
									<span class='spwoocs-title'>Powerful Slider Controls (Autoplay, Autoplay Speed, Pause on hover, Slide to Scroll, Navigation, Pagination, etc.) </span>
									<span class='spwoocs-free spwoocs-check-icon'></span>
									<span class='spwoocs-pro spwoocs-check-icon'></span>
								</li>
								<li class='spwoocs-body'>
									<span class='spwoocs-title'>Fade Effect and Multi-row Category Sliders</span>
									<span class='spwoocs-free spwoocs-close-icon'></span>
									<span class='spwoocs-pro spwoocs-check-icon'></span>
								</li>
								<li class='spwoocs-body'>
									<span class='spwoocs-title'>Export or Import Category Showcases/Views</span>
									<span class='spwoocs-free spwoocs-check-icon'></span>
									<span class='spwoocs-pro spwoocs-check-icon'></span>
								</li>
								<li class='spwoocs-body'>
									<span class='spwoocs-title'>Stylize your Category Showcase Typography with 1500+ Google Fonts</span>
									<span class='spwoocs-free spwoocs-close-icon'></span>
									<span class='spwoocs-pro spwoocs-check-icon'></span>
								</li>
								<li class='spwoocs-body'>
									<span class='spwoocs-title'>All Premium Features, Security Enhancements, and Compatibility</span>
									<span class='spwoocs-free spwoocs-close-icon'></span>
									<span class='spwoocs-pro spwoocs-check-icon'></span>
								</li>
								<li class='spwoocs-body'>
									<span class='spwoocs-title'>Priority Top-notch Support</span>
									<span class='spwoocs-free spwoocs-close-icon'></span>
									<span class='spwoocs-pro spwoocs-check-icon'></span>
								</li>
							</ul>
						</div>
						<div class="spwoocs-upgrade-to-pro">
							<h2 class='spwoocs-section-title-help'>Upgrade to PRO & Enjoy Advanced Features!</h2>
							<span class='spwoocs-section-subtitle'>Already, <b>15000+</b> people are using Reno Product Category on their websites to create beautiful showcase, why won’t you!</span>
							<div class="spwoocs-upgrade-to-pro-btn">
								<div class="spwoocs-action-btn">
									<a target="_blank" href="https://shapedplugin.com/reno-product-category/?ref=115#pricing" class='spwoocs-big-btn'>Upgrade to Pro Now!</a>
									<span class='spwoocs-small-paragraph'>14-Day No-Questions-Asked <a target="_blank" href="https://shapedplugin.com/refund-policy/">Refund Policy</a></span>
								</div>
								<a target="_blank" href="https://shapedplugin.com/reno-product-category/?ref=115" class='spwoocs-big-btn-border'>See All Features</a>
								<a target="_blank" href="https://shapedplugin.com/reno-product-category/" class='spwoocs-big-btn-border spwoocs-live-pro-demo'>Pro Live Demo</a>
							</div>
						</div>
					</div>
					<div class="spwoocs-testimonial">
						<div class="spwoocs-testimonial-title-section">
							<span class='spwoocs-testimonial-subtitle'>NO NEED TO TAKE OUR WORD FOR IT</span>
							<h2 class="spwoocs-section-title-help">Our Users Love Reno Product Category Pro!</h2>
						</div>
						<div class="spwoocs-testimonial-wrap">
							<div class="spwoocs-testimonial-area">
								<div class="spwoocs-testimonial-content">
									<p>This is the best plugin for managing categories, i was having some issues and starting looking at others and was very disappointed at whas was available, they are not even close. Thankfully they were...</p>
								</div>
								<div class="spwoocs-testimonial-info">
									<div class="spwoocs-img">
										<img src="<?php echo esc_url( $img . 'green.png' ); ?>" alt="">
									</div>
									<div class="spwoocs-info">
										<h3>Green Rep Exchange</h3>
										<div class="spwoocs-star">
											<i>★★★★★</i>
										</div>
									</div>
								</div>
							</div>
							<div class="spwoocs-testimonial-area">
								<div class="spwoocs-testimonial-content">
									<p>It’s taken me years to find a plugin to do this what this plugin does.It allows us to simplify our static menu structure and show the sub-categories above each category page...</p>
								</div>
								<div class="spwoocs-testimonial-info">
									<div class="spwoocs-img">
										<img src="<?php echo esc_url( $img . 'testimonial.svg' ); ?>" alt="">
									</div>
									<div class="spwoocs-info">
										<h3>Martynsaunders</h3>
										<div class="spwoocs-star">
											<i>★★★★★</i>
										</div>
									</div>
								</div>
							</div>
							<div class="spwoocs-testimonial-area">
								<div class="spwoocs-testimonial-content">
									<p>The problem was a conflict with the theme, but he solved my problem with a kind consultation.Exactly the problem was the conflict with the theme of delay load.I’m satisfied with your service. Tha...</p>
								</div>
								<div class="spwoocs-testimonial-info">
									<div class="spwoocs-img">
										<img src="<?php echo esc_url( $img . 'ncia.png' ); ?>" alt="">
									</div>
									<div class="spwoocs-info">
										<h3>Ncia</h3>
										<div class="spwoocs-star">
											<i>★★★★★</i>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</section>

			<!-- Recommended Page -->
			<section id="recommended-tab" class="spwoocs-recommended-page">
				<div class="spwoocs-container">
					<h2 class="spwoocs-section-title-help">Enhance your Website with our Free Robust Plugins</h2>
					<div class="spwoocs-wp-list-table plugin-install-php">
						<div class="spwoocs-recommended-plugins" id="the-list">
							<?php
								$this->spwoocs_plugins_info_api_help_page();
							?>
						</div>
					</div>
				</div>
			</section>

			<!-- About Page -->
			<section id="about-us-tab" class="spwoocs__help about-page">
				<div class="spwoocs-container">
					<div class="spwoocs-about-box">
						<div class="spwoocs-about-info">
							<h3>The Best WooCommerce Category Showcase plugin by the Reno Product Category Team, ShapedPlugin, LLC</h3>
							<p>At <b>ShapedPlugin LLC</b>, we always want to help WooCommerce store owners boost sales with different easy sales booster plugins. However, we have yet to find a plugin that effectively displays product categories when it's vital for customers to know what products you offer.</p>
							<p>Hence, we have created a plugin that beautifully displays WooCommerce categories. You can easily filter categories, including parent, child, grandchild, great-grandchild, and more. Check it out now, and you'll love it!</p>
							<div class="spwoocs-about-btn">
								<a target="_blank" href="https://shapedplugin.com/reno-product-category/?ref=115" class='spwoocs-medium-btn'>Explore Reno Product Category</a>
								<a target="_blank" href="https://shapedplugin.com/about-us/" class='spwoocs-medium-btn spwoocs-arrow-btn'>More About Us <i class="spwoocs-icon-button-arrow-icon"></i></a>
							</div>
						</div>
						<div class="spwoocs-about-img">
							<img src="<?php echo esc_url( $img . 'shapedplugin-team.jpg' ); ?>" alt="ShapedPlugin Team">
							<span>Team ShapedPlugin LLC at WordCamp Sylhet</span>
						</div>
					</div>
					<?php
					$plugins_arr = get_transient( 'spwoocs_plugins' );
					$plugin_icon = array();
					if ( is_array( $plugins_arr ) && ( count( $plugins_arr ) > 0 ) ) {
						foreach ( $plugins_arr as $plugin ) {
							$plugin_icon[ $plugin['slug'] ] = $plugin['icons'];
						}
					}

					// Smart Post ships an animated icon that the query_plugins payload
					// has been seen to omit; fall back to the WordPress.org asset
					// (unpinned, so it tracks the current revision) rather than
					// hardcoding the entry out of the transient-backed lookup.
					$smart_post_icon = isset( $plugin_icon['post-carousel'] )
						? $plugin_icon['post-carousel']
						: 'https://ps.w.org/post-carousel/assets/icon-256x256.gif';

					if ( isset( $plugin_icon['wp-carousel-free'] ) ) :
						?>
					<div class="spwoocs-our-plugin-list">
						<h3 class="spwoocs-section-title-help">Upgrade your Website with our High-quality Plugins!</h3>
						<div class="spwoocs-our-plugin-list-wrap">
							<a target="_blank" class="spwoocs-our-plugin-list-box" href="https://wpcarousel.io/?ref=1">
							<i class="spwoocs-icon-button-arrow-icon"></i>
								<img src="<?php echo esc_url( $plugin_icon['wp-carousel-free'] ); ?>" alt="WP Carousel">
								<h4>WP Carousel</h4>
								<p>The most powerful and user-friendly multi-purpose carousel, slider, & gallery plugin for WordPress.</p>
							</a>
							<a target="_blank" class="spwoocs-our-plugin-list-box" href="https://realtestimonials.io/?ref=1">
							<i class="spwoocs-icon-button-arrow-icon"></i>
								<img src="<?php echo esc_url( $plugin_icon['testimonial-free'] ); ?>" alt="Real Testimonials">
								<h4>Real Testimonials</h4>
								<p>Simply collect, manage, and display Testimonials on your website and boost conversions.</p>
							</a>
							<a target="_blank" class="spwoocs-our-plugin-list-box" href="https://smartpostshow.com/?ref=1">
								<i class="spwoocs-icon-button-arrow-icon"></i>
								<img src="<?php echo esc_url( $smart_post_icon ); ?>" alt="Smart Post">
								<h4>Smart Post</h4>
								<p>Filter and display posts (any post types), pages, taxonomy, custom taxonomy, and custom field, in beautiful layouts.</p>
							</a>
							<a target="_blank" href="https://wooproductslider.io/?ref=1" class="spwoocs-our-plugin-list-box">
								<i class="spwoocs-icon-button-arrow-icon"></i>
								<img src="<?php echo esc_url( $plugin_icon['woo-product-slider'] ); ?>" alt="Reno Product Slider">
								<h4>Reno Product Slider</h4>
								<p>Boost sales by interactive product Slider, Grid, and Table in your WooCommerce website or store.</p>
							</a>
							<a target="_blank" class="spwoocs-our-plugin-list-box" href="https://renoproductgallery.com/?ref=1">
								<i class="spwoocs-icon-button-arrow-icon"></i>
								<img src="<?php echo esc_url( $plugin_icon['gallery-slider-for-woocommerce'] ); ?>" alt="Reno Product Gallery">
								<h4>Reno Product Gallery</h4>
								<p>Product gallery slider and additional variation images gallery for WooCommerce and boost your sales.</p>
							</a>
							<a target="_blank" class="spwoocs-our-plugin-list-box" href="https://getwpteam.com/?ref=1">
								<i class="spwoocs-icon-button-arrow-icon"></i>
								<img src="<?php echo esc_url( $plugin_icon['team-free'] ); ?>" alt="Smart Team">
								<h4>Smart Team</h4>
								<p>Display your team members smartly who are at the heart of your company or organization!</p>
							</a>
							<a target="_blank" class="spwoocs-our-plugin-list-box" href="https://logocarousel.com/?ref=1">
								<i class="spwoocs-icon-button-arrow-icon"></i>
								<img src="<?php echo esc_url( $plugin_icon['logo-carousel-free'] ); ?>" alt="Logo Carousel">
								<h4>Logo Carousel</h4>
								<p>Showcase a group of logo images with Title, Description, Tooltips, Links, and Popup as a grid or in a carousel.</p>
							</a>
							<a target="_blank" class="spwoocs-our-plugin-list-box" href="https://easyaccordion.io/?ref=1">
								<i class="spwoocs-icon-button-arrow-icon"></i>
								<img src="<?php echo esc_url( $plugin_icon['easy-accordion-free'] ); ?>" alt="Easy Accordion">
								<h4>Easy Accordion</h4>
								<p>Minimize customer support by offering comprehensive FAQs and increasing conversions.</p>
							</a>
							<a target="_blank" class="spwoocs-our-plugin-list-box" href="https://shapedplugin.com/smart-swatches-for-woocommerce/?ref=1">
								<i class="spwoocs-icon-button-arrow-icon"></i>
								<img src="<?php echo esc_url( $plugin_icon['smart-swatches'] ); ?>" alt="Smart Swatches for WooCommerce">
								<h4>Smart Swatches for WooCommerce</h4>
								<p>Appealing color, image, and button variation swatches on your WooCommerce Shop and Product pages in minutes to increase sales.</p>
							</a>
							<a target="_blank" class="spwoocs-our-plugin-list-box" href="https://wptabs.com/?ref=1">
								<i class="spwoocs-icon-button-arrow-icon"></i>
								<img src="<?php echo esc_url( $plugin_icon['wp-expand-tabs-free'] ); ?>" alt="Smart Tabs">
								<h4>Smart Tabs</h4>
								<p>Display tabbed content smartly & quickly on your WordPress site without coding skills.</p>
							</a>
							<a target="_blank" class="spwoocs-our-plugin-list-box" href="https://shapedplugin.com/reno-quick-view?ref=1">
								<i class="spwoocs-icon-button-arrow-icon"></i>
								<img src="<?php echo esc_url( $plugin_icon['woo-quickview'] ); ?>" alt="Reno Quick View">
								<h4>Reno Quick View</h4>
								<p>Quickly view product information with smooth animation via AJAX in a nice Modal without opening the product page.</p>
							</a>
							<a target="_blank" class="spwoocs-our-plugin-list-box" href="https://shapedplugin.com/smart-brands/?ref=1">
								<i class="spwoocs-icon-button-arrow-icon"></i>
								<img src="<?php echo esc_url( $plugin_icon['smart-brands-for-woocommerce'] ); ?>" alt="Smart Brands for WooCommerce">
								<h4>Smart Brands for WooCommerce</h4>
								<p>Smart Brands for WooCommerce Pro helps you display product brands in an attractive way on your online store.</p>
							</a>
						</div>
					</div>
					<?php endif; ?>
				</div>
			</section>
			</div><!-- /.spwoocs-help-content -->

			<!-- Footer Section -->
			<footer class="spwoocs-footer">
				<div class="spwoocs-footer__inner">
					<div class="spwoocs-footer__credit">
						<span><?php esc_html_e( 'Made with', 'woo-category-slider-grid' ); ?></span>
						<img src="<?php echo esc_url( $img . 'footer-heart.svg' ); ?>" alt="" width="16" height="16">
						<span><?php esc_html_e( 'by the', 'woo-category-slider-grid' ); ?></span>
						<a target="_blank" href="https://shapedplugin.com/about-us/"><?php esc_html_e( 'ShapedPlugin LLC Team', 'woo-category-slider-grid' ); ?></a>
					</div>
					<div class="spwoocs-footer__social">
						<span><?php esc_html_e( 'Get Connected with', 'woo-category-slider-grid' ); ?></span>
						<ul>
							<li><a target="_blank" href="https://www.linkedin.com/company/shapedplugin/" aria-label="<?php esc_attr_e( 'LinkedIn', 'woo-category-slider-grid' ); ?>"><i class="spwoocs-icon-linkedin"></i></a></li>
							<li><a target="_blank" href="https://twitter.com/intent/follow?screen_name=ShapedPlugin" aria-label="<?php esc_attr_e( 'X (Twitter)', 'woo-category-slider-grid' ); ?>"><i class="spwoocs-icon-x"></i></a></li>
							<li><a target="_blank" href="https://profiles.wordpress.org/shapedplugin/#content-plugins" aria-label="<?php esc_attr_e( 'WordPress.org', 'woo-category-slider-grid' ); ?>"><i class="spwoocs-icon-wp-icon"></i></a></li>
							<li><a target="_blank" href="https://www.facebook.com/ShapedPlugin/" aria-label="<?php esc_attr_e( 'Facebook', 'woo-category-slider-grid' ); ?>"><i class="spwoocs-icon-fb"></i></a></li>
							<li><a target="_blank" href="https://youtube.com/@ShapedPlugin?sub_confirmation=1" aria-label="<?php esc_attr_e( 'YouTube', 'woo-category-slider-grid' ); ?>"><i class="spwoocs-icon-youtube-play"></i></a></li>
						</ul>
					</div>
					<div class="spwoocs-footer__rating">
						<span>
							<?php
							printf(
								/* translators: %s: plugin name. */
								esc_html__( 'Enjoying %s?', 'woo-category-slider-grid' ),
								'<strong>' . esc_html__( 'Reno Product Category', 'woo-category-slider-grid' ) . '</strong>'
							);
							?>
							<a target="_blank" href="<?php echo esc_url( $review_url ); ?>"><?php esc_html_e( 'Rate us!', 'woo-category-slider-grid' ); ?></a>
						</span>
						<a class="spwoocs-footer__stars" target="_blank" href="<?php echo esc_url( $review_url ); ?>" aria-label="<?php esc_attr_e( 'Rate Reno Product Category five stars on WordPress.org', 'woo-category-slider-grid' ); ?>">
							<img src="<?php echo esc_url( $img . 'footer-rating-stars.svg' ); ?>" alt="" width="74" height="14">
						</a>
					</div>
				</div>
			</footer>
		</div>
		<?php
	}

	/**
	 * Render a help page CTA card.
	 *
	 * @param string $icon        Absolute URL of the card icon.
	 * @param string $title       Card title.
	 * @param string $description Card description.
	 * @param string $button_text Button label.
	 * @param string $button_url  Button URL.
	 * @return void
	 */
	private function render_cta_card( $icon, $title, $description, $button_text, $button_url ) {
		?>
		<div class="spwoocs-cta-card">
			<div class="spwoocs-cta-card__body">
				<div class="spwoocs-cta-card__title">
					<img src="<?php echo esc_url( $icon ); ?>" alt="" width="24" height="24">
					<h3><?php echo esc_html( $title ); ?></h3>
				</div>
				<p><?php echo esc_html( $description ); ?></p>
			</div>
			<a class="spwoocs-btn spwoocs-btn--ghost" target="_blank" href="<?php echo esc_url( $button_url ); ?>"><?php echo esc_html( $button_text ); ?></a>
		</div>
		<?php
	}
}

Woo_Category_Slider_Help::instance();
