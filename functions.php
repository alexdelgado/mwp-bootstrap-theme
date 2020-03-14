<?php

// WordPress Admin Settings
include 'inc/class.wp-bootstrap-admin.php';
add_action( 'init', array( 'wp_bootstrap_admin', 'singleton' ) );

// WordPress Theme Settings
include 'inc/class.wp-bootstrap-theme.php';
add_action( 'init', array( 'wp_bootstrap_theme', 'singleton' ) );
add_action( 'after_setup_theme', array( 'wp_bootstrap_theme', 'setup_theme' ) );

// Accessible WordPress Menus
include 'inc/class.wp-bootstrap-walker-nav-menu.php';
