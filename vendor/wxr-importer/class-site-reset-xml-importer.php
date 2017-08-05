<?php
/**
 * Site Reset XML Importer
 *
 * @since  1.0.0
 * @package Site Reset
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if( ! class_exists( 'Site_Reset_XML_Importer' ) ) :

	/**
	 * Site_Reset_XML_Importer
	 *
	 * @since 1.0.0
	 */
	class Site_Reset_XML_Importer {

		/**
		 * Instance
		 *
		 * @access private
		 * @since 1.0.0
		 */
		private static $instance;

		/**
		 * Initiator
		 *
		 * @since 1.0.0
		 * @return object initialized object of class.
		 */
		public static function instance(){
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
			
			$this->includes();

			// Add XML file support in MIME.
			add_filter( 'upload_mimes', array( $this, 'custom_upload_mimes' ) );

			// Add markup on admin page.
			add_action( 'site_reset_admin_page_left_section_bottom', array( $this, 'markup' ) );

			// Process XML import.
			add_action( 'site_reset_success', array( $this, 'process_xml' ) );
		}

		function markup() {
		?>
		<div class="postbox">
			<h2 class="hndle"><span> <?php _e( 'Import XML', 'site-reset' ); ?> </span></h2>
			<div class="inside">
				<select name="xml">
					<option value=""><?php _e( 'None', 'site-reset' ); ?></option>
					<option value="https://wpcom-themes.svn.automattic.com/demo/theme-unit-test-data.xml"><?php _e( 'Theme Unit Test Data', 'site-reset' ); ?></option>
				</select>
				<p>
					<label for="fetch-attachments">
						<input type="checkbox" id="fetch-attachments" value="1" name="fetch-attachments" />
						<?php _e( 'Download and import file attachments', 'site-reset' ); ?>
					</label>
				</p>
			</div>
		</div>
		<?php
	}

	/**
	 * Import XML
	 *
	 * Here $post === $_POST
	 * 
	 * @param  array  $post [description]
	 * @return [type]       [description]
	 */
	function process_xml( $post = array() ) {

		if( isset( $post['xml'] ) && ! empty( $post['xml'] ) ) {

			$url               = $post['xml'];
			$fetch_attachments = ( isset( $post['fetch-attachments'] ) ) ? $post['fetch-attachments'] : false;

			$options = array(
				'fetch_attachments' => $fetch_attachments, // true,
				'default_author'    => 0,
			);

			// Download XML into uploads directory.
			$xml_path = $this->download_xml( $url );

			// Import XML data from XML file.
			$this->import_xml( $xml_path['file'], $options );
		}

	}

	/**
	 * Add .xml files as supported format in the uploader.
	 *
	 * @param array $mimes Already supported mime types.
	 */
	public function custom_upload_mimes( $mimes ) {
		$mimes = array_merge(
			$mimes, array(
				'xml' => 'application/xml',
			)
		);

		return $mimes;
	}

	/**
	 * Include required files.
	 *
	 * @since  1.0.0
	 */
	private function includes() {
		if ( ! class_exists( 'WP_Importer' ) ) {
			defined( 'WP_LOAD_IMPORTERS' ) || define( 'WP_LOAD_IMPORTERS', true );
			require ABSPATH . '/wp-admin/includes/class-wp-importer.php';
		}
		require_once SITE_RESET_DIR . 'vendor/wxr-importer/class-wxr-importer.php';
		require_once SITE_RESET_DIR . 'vendor/wxr-importer/class-logger.php';
	}

	/**
	 * Start the xml import.
	 *
	 * @since  1.0.0
	 *
	 * @param  (String) $path Absolute path to the XML file.
	 */
	public function import_xml( $path, $options = array() ) {

		if( count( $options ) === 0 ) {
			$options = array(
				'fetch_attachments' => true,
				'default_author'    => 0,
			);
		}

		$logger   = new WP_Importer_Logger();
		$importer = new WXR_Importer( $options );
		$importer->set_logger( $logger );
		$result = $importer->import( $path );
		wp_die();
	}

	/**
	 * Download and save XML file to uploads directory.
	 *
	 * @since  1.0.0
	 *
	 * @param  (String) $url URL of the xml file.
	 *
	 * @return (Array)      Attachment array of the downloaded xml file.
	 */
	public function download_xml( $url ) {

		// Download XML file.
		$response = self::download_file( $url );

		// Is Success?
		if ( $response['success'] ) {
			return $response['data'];
		}

	}

	/**
	 * Download File Into Uploads Directory
	 *
	 * @param  string $file Download File URL.
	 * @return array        Downloaded file data.
	 */
	public static function download_file( $file = '' ) {

		// Gives us access to the download_url() and wp_handle_sideload() functions.
		require_once( ABSPATH . 'wp-admin/includes/file.php' );

		$timeout_seconds = 5;

		// Download file to temp dir.
		$temp_file = download_url( $file, $timeout_seconds );

		// WP Error.
		if ( is_wp_error( $temp_file ) ) {
			return array(
				'success' => false,
				'data'    => $temp_file->get_error_message(),
			);
		}

		// Array based on $_FILE as seen in PHP file uploads.
		$file_args = array(
			'name'     => basename( $file ),
			'tmp_name' => $temp_file,
			'error'    => 0,
			'size'     => filesize( $temp_file ),
		);

		$overrides = array(

			// Tells WordPress to not look for the POST form
			// fields that would normally be present as
			// we downloaded the file from a remote server, so there
			// will be no form fields
			// Default is true.
			'test_form' => false,

			// Setting this to false lets WordPress allow empty files, not recommended.
			// Default is true.
			'test_size' => true,

			// A properly uploaded file will pass this test. There should be no reason to override this one.
			'test_upload' => true,

		);

		// Move the temporary file into the uploads directory.
		$results = wp_handle_sideload( $file_args, $overrides );

		if ( isset( $results['error'] ) ) {
			return array(
				'success' => false,
				'data'    => $results,
			);
		}

		// Success!
		return array(
			'success' => true,
			'data'    => $results,
		);
	}

	}

	/**
	 * Kicking this off by calling 'instance()' method
	 */
	Site_Reset_XML_Importer::instance();

endif;
