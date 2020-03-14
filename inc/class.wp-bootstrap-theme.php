<?php

class WP_Bootstrap_Theme {
	static $instance = false;

	public function __construct() {
		$this->_add_actions();
		$this->_remove_actions();
		$this->_remove_filters();
	}

	/**
	 * Disable Feeds
	 *
	 * Disables all WordPress generated feeds.
	 */
	public function disable_feeds() {
		wp_die( __( 'No feed available, please visit the <a href="'. esc_url( home_url( '/' ) ) .'">homepage</a>!' ) );
	}

	/**
	 * Add Security Headers
	 *
	 * Makes our site super safe
	 */
	public function security_headers() {
		header( 'Strict-Transport-Security: max-age=31536000; includeSubDomains' );
		header( 'X-Frame-Options: SAMEORIGIN' );
		header( 'X-XSS-Protection: 1; mode=block' );
		header( 'X-Content-Type-Options: nosniff' );
		header( 'Referrer-Policy: no-referrer-when-downgrade' );
	}

	/**
	 * Enqueue Assets
	 *
	 * Enqueues the necessary css and js files when the theme is loaded.
	 */
	public function enqueue_assets() {
		wp_enqueue_style(
			'wp-bootstrap-theme',
			get_template_directory_uri() .'/dist/theme.min.css',
			array(),
			filemtime( get_template_directory() . '/dist/theme.min.css' )
		);

		wp_enqueue_script(
			'wp-bootstrap-theme',
			get_template_directory_uri() .'/dist/theme.min.js',
			array(),
			filemtime( get_template_directory() . '/dist/theme.min.js' ),
			true
		);
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
	 * Set Theme Options
	 *
	 * Configures the necessary WordPress theme options once the theme is activated.
	 */
	public static function setup_theme() {
		add_theme_support( 'title-tag' );
		add_theme_support( 'custom-background' );
		add_theme_support( 'custom-logo' );
		add_theme_support( 'customize-selective-refresh-widgets' );
		add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption' ) );
		add_theme_support( 'post-formats', array( 'aside', 'gallery' ) );
		add_theme_support( 'post-thumbnails' );

		register_nav_menus(
			array(
				'primary' => __( 'Primary Navigation', '_starter' ),
			)
		);
	}

	/**
	 * Add Actions
	 *
	 * Defines all the WordPress actions and filters used by this theme.
	 */
	protected function _add_actions() {
		add_action( 'do_feed', array( $this, 'disable_feeds' ), 1 );
		add_action( 'do_feed_rdf', array( $this, 'disable_feeds' ), 1 );
		add_action( 'do_feed_rss', array( $this, 'disable_feeds' ), 1 );
		add_action( 'do_feed_rss2', array( $this, 'disable_feeds' ), 1 );
		add_action( 'do_feed_atom', array( $this, 'disable_feeds' ), 1 );
		add_action( 'do_feed_rss2_comments', array( $this, 'disable_feeds' ), 1 );
		add_action( 'do_feed_atom_comments', array( $this, 'disable_feeds' ), 1 );
		add_action( 'send_headers', array( $this, 'security_headers' ), 1 );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
	}

	/**
	 * Remove Actions
	 *
	 * Defines all the WordPress actions and filters we don't want.
	 */
	protected function _remove_actions() {
		remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10 );
		remove_action( 'wp_head', 'feed_links_extra', 3 );
		remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
		remove_action( 'wp_head', 'rest_output_link_wp_head', 10 );
		remove_action( 'wp_head', 'rsd_link' );
		remove_action( 'wp_head', 'wlwmanifest_link' );
		remove_action( 'wp_head', 'wp_generator' );
		remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
		remove_action( 'wp_head', 'wp_oembed_add_host_js' );
		remove_action( 'wp_head', 'wp_shortlink_wp_head', 10 );
		remove_action( 'wp_print_styles', 'print_emoji_styles' );
	}

	/**
	 * Remove Filters
	 *
	 * Removes all the WordPress filters not required by this theme.
	 */
	protected function _remove_filters() {
		remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
		remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
	}
}
