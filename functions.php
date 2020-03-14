<?php

// WordPress Admin Settings
require_once( 'inc/class.wp-bootstrap-admin.php' );
add_action( 'init', array( 'wp_bootstrap_admin', 'singleton' ) );

// WordPress Theme Settings
require_once( 'inc/class.wp-bootstrap-theme.php' );
add_action( 'init', array( 'wp_bootstrap_theme', 'singleton' ) );
add_action( 'after_setup_theme', array( 'wp_bootstrap_theme', 'setup_theme' ) );

// Accessible WordPress Menus
require_once( 'inc/class.wp-bootstrap-walker-nav-menu.php' );
