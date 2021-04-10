<?php
/**
 * Plugin Name: Reset Complete Site
 * Description: Select your favorite theme and active plugin's which you want to see after site reset. Note: Plugin NOT working for multisite. Also, Use it for testing purpose.
 * Plugin URI: https://github.com/maheshwaghmare/site-reset/
 * Author: Mahesh M. Waghmare
 * Author URI: https://maheshwaghmare.com/
 * Version: 1.2.1
 * License: GPL2
 * Text Domain: site-reset
 *
 * @package Site Reset
 */

/**
 * Set constants.
 */
define( 'SITE_RESET_VER', '1.2.1' );
define( 'SITE_RESET_FILE', __FILE__ );
define( 'SITE_RESET_BASE', plugin_basename( SITE_RESET_FILE ) );
define( 'SITE_RESET_DIR', plugin_dir_path( SITE_RESET_FILE ) );
define( 'SITE_RESET_URI', plugins_url( '/', SITE_RESET_FILE ) );

if ( is_admin() ) {
	require_once SITE_RESET_DIR . 'classes/class-site-reset.php';
}
