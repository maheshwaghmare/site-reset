<?php
/**
 * Plugin Name: Site Reset
 * Description: Set active plugins and default theme before site reset.
 * Plugin URI: https://github.com/maheshwaghmare/site-reset
 * Author: Mahesh M. Waghmare
 * Author URI: https://maheshwaghmare.wordpress.com/
 * Version: 1.0.0
 * License: GPL2
 * Text Domain: site-reset
 *
 * @package Site Reset
 */

/**
 * Set constants.
 */
define( 'SITE_RESET_VER',  '1.0.0' );
define( 'SITE_RESET_FILE', __FILE__ );
define( 'SITE_RESET_BASE', plugin_basename( SITE_RESET_FILE ) );
define( 'SITE_RESET_DIR',  plugin_dir_path( SITE_RESET_FILE ) );
define( 'SITE_RESET_URI',  plugins_url( '/', SITE_RESET_FILE ) );

if ( is_admin() ) {
	require_once SITE_RESET_DIR . 'classes/class-site-reset.php';
}
