<?php
/**
 * Admin Page View
 *
 * @since  1.0.0
 * @package Site Reset
 */

?>
<div class="wrap">

	<h1><?php esc_html_e( 'Site Reset', 'site-reset' ); ?></h1>

	<form id="site-reset-form" action="" method="post">
	
		<p>
			<?php
			/* translators: %1$s is URL parameter.  */
			printf( __( 'Set default theme and active plugin\'s before site reset. Add %1$s in URL to and press enter to delete current selected default theme & plugins.', 'site-reset' ) , '<code>&amp;author=true</code>' );
			?>
		</p>
		

		<?php do_action( 'site_reset_page_top' ); ?>

		<div id="dashboard-widgets-wrap">
			<div id="dashboard-widgets" class="metabox-holder">

				<div id="postbox-container-1" class="postbox-container">
					<div class="meta-box-sortables">

						<?php do_action( 'site_reset_admin_page_left_section_top' ); ?>

						<div class="postbox">
							<h2 class="hndle"><span> <?php _e( 'Themes', 'site-reset' ); ?> </span></h2>
							<div class="inside">
								<p class="description"> <?php _e( 'Select the theme which you want to activate after site reset.', 'site-reset' ); ?> </p>
								<?php
								/**
								 * Themes
								 */
								foreach ( wp_get_themes() as $theme_key => $theme_info ) {
									?>
									<p>
										<label <?php echo esc_attr( $theme_key ); ?>>
											<input type="radio" <?php checked( $reset_data['theme'], $theme_key, ' checked="checked"' ); ?> name="switch-theme" value="<?php echo esc_attr( $theme_key ); ?>" /><?php echo esc_html( $theme_info->Name ); ?>
										</label>
									<p>
								<?php } ?>
							</div>
						</div>
						
						<?php do_action( 'site_reset_admin_page_left_section_bottom' ); ?>

					</div>
				</div>

				<div id="postbox-container-2" class="postbox-container">
					<div class="meta-box-sortables">

						<?php do_action( 'site_reset_admin_page_right_section_top' ); ?>

						<div class="postbox">
							<h2 class="hndle"><span> <?php _e( 'Plugins', 'site-reset' ); ?> </span></h2>
							<div class="inside">
								<p class="description"> <?php _e( 'Select the plugin\'s which you want to activate after site reset.', 'site-reset' ); ?> </p>
								<?php
								/**
								 * Get All Plugins
								 */
								$plugins = get_plugins();
								foreach ( $plugins as $plugin_init => $plugin ) {

									// Mark checked for stored plugins.
									$checked = '';
									if ( in_array( $plugin_init, $reset_data['plugins'] ) ) {
										$checked = ' checked="checked" ';
									}

									$plugin_slug = strtok( $plugin_init, '/' );
									?>
									<p>
										<label for="<?php echo esc_attr( $plugin_slug ); ?>">
											<input type="checkbox" <?php echo esc_attr( $checked ); ?>
												id="<?php echo esc_attr( $plugin_slug ); ?>"
												class="activate-plugins"
												value="<?php echo esc_attr( $plugin_init ); ?>"
												name="activate-plugins[]">
											<?php echo esc_attr( $plugin['Name'] ); ?>
										</label>
									</p>
								<?php } ?>
							</div>
						</div>

						<?php do_action( 'site_reset_admin_page_right_section_bottom' ); ?>

					</div>
				</div>

			</div>
		</div>

		<?php do_action( 'site_reset_page_bottom' ); ?>

		<hr />

		<h3><?php esc_html_e( 'Reset', 'site-reset' ); ?></h3>
		<p>
			<?php
			/* translators: %s is HTML code.  */
			printf( esc_html__( 'Type %s to reset the site.', 'site-reset' ), '<strong>reset</strong>' );
			?>
		</p>
		<?php wp_nonce_field( 'site-reset' ); ?>
		<input id="site-reset" type="hidden" name="site-reset" value="true" />
		<input id="site-reset-confirm" type="text" name="site-reset-confirm" value="" /><br/>
		<p class="submit">
			<input type="submit" name="button-site-reset" class="button-primary" id="button-site-reset" value="<?php _e( 'Reset', 'site-reset' ); ?>" />
		</p>


	</form>
</div><!-- .wrap -->
