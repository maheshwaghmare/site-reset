<?php
/**
 * Site Reset WP CLI
 *
 * 1. Run `wp site-reset clean` Clean the site.
 *
 * @package Site Reset
 * @since 1.0.0
 */

if ( class_exists( 'WP_CLI_Command' ) && ! class_exists( 'Site_Reset_WP_CLI' ) ) :

	/**
	 * Site_Reset_WP_CLI
	 *
	 * @since 1.0.0
	 */
	class Site_Reset_WP_CLI extends WP_CLI_Command {

		/**
		 * Clean
		 *
		 * @since 1.0.0
		 * 
		 * @param  array $args       Arguments.
		 * @param  array $assoc_args Associated Arguments.
		 * @return void
		 */
		public function clean( $args, $assoc_args ) {

			WP_CLI::line( 'Started cleaning...' );

			// Site_Reset::instance()->fresh_setup();

			WP_CLI::success( 'Site reset successfully.' );
		}

	}

	/**
	 * Add Command
	 */
	WP_CLI::add_command( 'site-reset', 'Site_Reset_WP_CLI' );

endif;
