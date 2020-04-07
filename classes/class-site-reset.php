<?php
/**
 * Site Reset
 *
 * @since  1.0.0
 * @package Site Reset
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Site_Reset' ) ) :

	/**
	 * Site Reset
	 *
	 * @since 1.0.0
	 */
	class Site_Reset {

		/**
		 * Instance
		 *
		 * @access private
		 * @var object Class object.
		 * @since 1.0.0
		 */
		private static $instance;

		/**
		 * Initiator
		 *
		 * @since 1.0.0
		 * @return object initialized object of class.
		 */
		public static function instance() {
			if ( ! isset( self::$instance ) ) {
				self::$instance = new self;
			}
			return self::$instance;
		}

		/**
		 * Constructor
		 *
		 * @since 1.0.0
		 */
		public function __construct() {
			add_action( 'admin_menu', array( $this, 'add_page' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );

			add_action( 'admin_init', array( $this, 'reset_process' ) );
			add_action( 'wp_before_admin_bar_render', array( $this, 'admin_bar_link' ) );

			add_action( 'load-tools_page_site-reset', array( $this, 'add_help' ) );

			add_action( 'plugin_action_links_' . SITE_RESET_BASE, array( $this, 'action_links' ) );
		}

		/**
		 * Show action links on the plugin screen.
		 *
		 * @param   mixed $links Plugin Action links.
		 * @return  array
		 */
		public function action_links( $links ) {

			$arguments = array(
				'page' => 'site-reset',
			);

			$url = add_query_arg( $arguments, admin_url( 'tools.php' ) );

			$action_links = array(
				'settings' => '<a href="' . esc_url( $url ) . '" aria-label="' . esc_attr__( 'Settings', 'site-reset' ) . '">' . esc_html__( 'Settings', 'site-reset' ) . '</a>',
			);

			return array_merge( $action_links, $links );
		}

		/**
		 * Add Help Tab
		 *
		 * @since 1.0.0
		 */
		function add_help() {
			$screen = get_current_screen();

			$screen->add_help_tab(
				array(
					'id'      => 'site-reset-default',
					'title'   => __( 'Default', 'site-reset' ),

					/* translators: %1$s is URL parameter.  */
					'content' => '<p>' . sprintf( __( 'Selected theme and active plugins data is stored in option %1$s. <br/> If you want to delete current stored data then add %2$s in URL to and press enter. We clear current selected theme and active plugins data.', 'site-reset' ), '<code>site_reset</code>', '<code>&amp;author=true</code>' ) . '</p>',
				)
			);

			// Help sidebars are optional.
			$screen->set_help_sidebar(
				'<p><strong>' . __( 'For more information:', 'site-reset' ) . '</strong></p>' .
				'<p><a href="https://wordpress.org/support/plugin/site-reset/" target="_blank">' . __( 'Support Forums', 'site-reset' ) . '</a></p>'
			);
		}

		/**
		 * Add Admin Page
		 *
		 * @see https://codex.wordpress.org/User_Levels#User_Levels_9_and_10
		 * @since 1.0.0
		 */
		public function add_page() {
			if ( current_user_can( 'level_10' ) ) {
				add_management_page( __( 'Reset Complete Site', 'site-reset' ), __( 'Reset Complete Site', 'site-reset' ), 'level_10', 'site-reset', array( $this, 'admin_page' ) );
			}
		}

		/**
		 * Admin Page View
		 *
		 * @since 1.0.0
		 */
		function admin_page() {

			$defaults = apply_filters(
				'site_reset_default_settings',
				array(
					'theme'   => '',
					'plugins' => array(),
				)
			);

			$stored_data = get_option( 'site_reset', $defaults );
			$reset_data  = wp_parse_args( $stored_data, $defaults );

			require_once SITE_RESET_DIR . 'includes/view-admin-page.php';
		}

		/**
		 * Enqueue Admin Scripts
		 *
		 * @since 1.0.0
		 * @param  string $hook Current page slug.
		 * @return void
		 */
		function admin_scripts( $hook = '' ) {

			if ( 'tools_page_site-reset' === $hook ) {
				wp_enqueue_script( 'site-reset-admin', SITE_RESET_URI . 'assets/admin.js', array( 'jquery' ), SITE_RESET_VER, true );
				$site_reset_js_obj = array(
					'warning' => __( "Warning! Your current data will lost.\n\nWe recommend to take a backup before process the site reset.\n\nClick on 'ok' button to reset the site.", 'site-reset' ),
					'invalid' => __( 'Invalid input! Please type \'reset\' to reset the site.', 'site-reset' ),
				);
				wp_localize_script( 'site-reset-admin', 'siteReset', $site_reset_js_obj );
			}

		}

		/**
		 * Add Admin Bar Link
		 *
		 * @since 1.0.0
		 */
		public function admin_bar_link() {
			global $wp_admin_bar;
			$wp_admin_bar->add_menu(
				array(
					'parent' => 'site-name',
					'id'     => 'site-reset',
					'title'  => __( 'Reset Complete Site', 'site-reset' ),
					'href'   => admin_url( 'tools.php?page=site-reset' ),
				)
			);
		}

		/**
		 * Reset Process
		 *
		 * @since 1.0.0
		 */
		public function reset_process() {

			/**
			 * Delete activate plugin and switch theme data
			 * If found 'author=true' in $_GET
			 */
			if ( isset( $_GET['author'] ) && 'true' === $_GET['author'] ) {
				delete_option( 'site_reset' );
			}

			$valid_nonce   = ( isset( $_POST['_wpnonce'] ) && wp_verify_nonce( $_POST['_wpnonce'], 'site-reset' ) ) ? true : false;
			$reset         = ( isset( $_POST['site-reset'] ) && 'true' == $_POST['site-reset'] ) ? true : false;
			$reset_confirm = ( isset( $_POST['site-reset-confirm'] ) && 'reset' == $_POST['site-reset-confirm'] ) ? true : false;

			if ( $reset && $reset_confirm && $valid_nonce ) {

				// Fresh setup.
				$this->fresh_setup();

			}
		}

		/**
		 * Fresh Setup Site
		 *
		 * @since 1.0.0
		 * @return void
		 */
		function fresh_setup() {

			global $current_user, $wpdb;

			require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );

			$blogname    = get_option( 'blogname' );
			$admin_email = get_option( 'admin_email' );
			$blog_public = get_option( 'blog_public' );

			if ( 'admin' != $current_user->user_login ) {
				$user = get_user_by( 'login', 'admin' );
			}

			// Check user Levels.
			// @see https://codex.wordpress.org/User_Levels#User_Levels_9_and_10.
			if ( empty( $user->user_level ) || 10 > $user->user_level ) {
				$user = $current_user;
			}

			$prefix = str_replace( '_', '\_', $wpdb->prefix );
			$tables = $wpdb->get_col( "SHOW TABLES LIKE '{$prefix}%'" ); // WPCS: unprepared SQL OK.
			foreach ( $tables as $table ) {
				$wpdb->query( "DROP TABLE $table" ); // WPCS: unprepared SQL OK.
			}

			// Install WordPress.
			$result = wp_install( $blogname, $user->user_login, $user->user_email, $blog_public );

			$url              = $result['url'];
			$user_id          = $result['user_id'];
			$password         = $result['password'];
			$password_message = $result['password_message'];

			// Set current user password.
			$query = $wpdb->prepare( "UPDATE $wpdb->users SET user_pass = %s, user_activation_key = '' WHERE ID = %d", $user->user_pass, $user_id );
			$wpdb->query( $query ); // WPCS: unprepared SQL OK.

			$get_user_meta    = function_exists( 'get_user_meta' ) ? 'get_user_meta' : 'get_usermeta';
			$update_user_meta = function_exists( 'update_user_meta' ) ? 'update_user_meta' : 'update_usermeta';

			// Do not generated password for the current user.
			if ( $get_user_meta( $user_id, 'default_password_nag' ) ) {
				$update_user_meta( $user_id, 'default_password_nag', false );
			}

			if ( $get_user_meta( $user_id, $wpdb->prefix . 'default_password_nag' ) ) {
				$update_user_meta( $user_id, $wpdb->prefix . 'default_password_nag', false );
			}

			do_action( 'site_reset_after', $_POST );

			/**
			 * Activate Plugins.
			 */
			$reset_data = apply_filters(
				'site_reset_default_settings',
				array(
					'theme'   => '',
					'plugins' => array(),
				)
			);
			if ( ! empty( $_POST['activate-plugins'] ) ) {

				$reset_data['plugins'] = $_POST['activate-plugins'];

				foreach ( $_POST['activate-plugins'] as $plugin ) {
					$plugin = plugin_basename( $plugin );
					if ( ! is_wp_error( validate_plugin( $plugin ) ) ) {
						activate_plugin( $plugin );
					}
				}
			}

			/**
			 * Switch Theme.
			 */
			if ( isset( $_POST['switch-theme'] ) ) {
				$theme_slug          = sanitize_text_field( $_POST['switch-theme'] );
				$reset_data['theme'] = $theme_slug;
				switch_theme( $theme_slug );
			}

			// Update options.
			update_option( 'site_reset', $reset_data );

			// Clear current auth cookies.
			wp_clear_auth_cookie();

			// Set current user auth cookies.
			wp_set_auth_cookie( $user_id );

			// Redirect to /wp-admin/.
			wp_redirect( admin_url() );
		}
	}

	/**
	 * Kicking this off by calling 'instance()' method
	 */
	Site_Reset::instance();

endif;
