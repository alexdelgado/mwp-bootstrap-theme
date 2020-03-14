<?php

class WP_Bootstrap_Admin {
	static $instance = false;

	public function __construct() {
		$this->_add_actions();
		$this->_remove_actions();
		$this->_remove_filters();
	}

	/**
	 * Add Editor Styles
	 *
	 * Adds a stylesheet to the WordPress Page/Post editor.
	 */
	public function add_editor_styles() {
		add_editor_style( 'dist/editor.css' );
	}

	/**
	 * Enqueue Assets
	 *
	 * Enqueues the necessary css and js files when the theme is loaded.
	 */
	public function enqueue_assets() {

		wp_enqueue_style(
			'wp-bootstrap-theme',
			get_template_directory_uri() .'/dist/wp-admin.min.css',
			array(),
			false
		);
	}

	/**
	 * Add Mime Types
	 *
	 * Allows svg and zip file uploads.
	 *
	 * @param array $mimes Array containing allowed Mime Types
	 */
	public function add_mime_types( $mimes ) {
		$mimes['svg'] = 'image/svg';
		$mimes['zip'] = 'application/zip';

		return $mimes;
	}

	/**
	 * Update Attachment Metadata
	 *
	 * Show SVG preview image in media gallery
	 *
	 * @param array $data Array containing attachment metadata.
	 * @param int $id ID of the given attachment.
	 */
	public function update_attachment_metadata( $data, $id ) {
		$attachment = get_post( $id );
		$mime_type = $attachment->post_mime_type;

		if ( $mime_type == 'image/svg+xml' ) {

			if ( empty( $data ) || empty( $data['width'] ) || empty( $data['height'] ) ) {
				$xml = simplexml_load_file( wp_get_attachment_url( $id ) );
				$attr = $xml->attributes();
				$viewbox = explode( ' ', $attr->viewBox );

				if ( isset( $attr->width ) && preg_match( '/\d+/', $attr->width, $value ) ) {
					$data['width'] = (int) $value[0];
				} else {
					$data['width'] = ( count( $viewbox ) == 4 ? (int) $viewbox[2] : null );
				}

				if ( isset( $attr->height ) && preg_match( '/\d+/', $attr->height, $value ) ) {
					$data['height'] = (int) $value[0];
				} else {
					$data['height'] = ( count( $viewbox ) == 4 ? (int) $viewbox[3] : null );
				}
			}
		}

		return $data;
	}

	/**
	 * Singleton
	 *
	 * Returns a single instance of the current class.
	 */
	public static function singleton() {

		if ( ! self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Add Actions
	 *
	 * Defines all the WordPress actions and filters used by this theme.
	 */
	private function _add_actions() {
		add_action( 'admin_init', array( $this, 'add_editor_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
	}

	/**
	 * Add Filters
	 *
	 * Defines all the WordPress filters and filters used by this theme.
	 */
	private function _add_filters() {
		add_filter( 'upload_mimes', array( $this, 'add_mime_types' ) );
		add_filter( 'wp_update_attachment_metadata', array( $this, 'update_attachment_metadata' ), 10, 2 );
	}

	/**
	 * Remove Actions
	 *
	 * Defines all the WordPress actions and filters we don't want.
	 */
	private function _remove_actions() {
		remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
		remove_action( 'admin_print_styles', 'print_emoji_styles' );
	}

	/**
	 * Remove Filters
	 *
	 * Removes all the WordPress filters not required by this theme.
	 */
	private function _remove_filters() {
		remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
	}
}
