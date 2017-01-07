<?php

/*
 * Plugin Name: Membership Plugin
 * Text Domain: membership-plugin
 * Plugin URI: http://kaosoft.com/membershi-plugin
 * Description: Create Membership Dashboard with meta boxes.
 *              Insert membership report menus.
 * Version: 1.1.1
 * Author: Kapt Kaos
 * Author URI: http://kaptkaos.com
 * License: GPL2
 */

//debug settings
use Membership\Classes\Club_Membership_Initialization;
global $debug_errors;

$debug = false;

if ( $debug ) {
	ini_set( 'display_errors', 'on' );
	error_reporting( E_ALL | E_STRICT );
} else {
	ini_set( 'display_errors', 'off' );
	error_reporting( E_ERROR | E_WARNING | E_PARSE | E_NOTICE );
}

/* Set plugin directory constant. */
define( 'CM_DIR', trailingslashit( plugin_dir_path( __FILE__ ) ) );

/* Set plugin name constant. */
define( 'CM_NAME', trim( dirname( plugin_basename( __FILE__ ) ), '/' ) );

if ( ! class_exists( 'CM_Admin_Page' ) ) {
	require_once( CM_DIR . 'includes/classes/class-club-membership-initialization.php' );
}

$cm_init = new Club_Membership_Initialization();
add_action( 'admin_enqueue_scripts', array( $cm_init, 'cm_membership_init' ));
//add_action( 'admin_enqueue_scripts', array( $cm_init, 'cm_member_admin_css') );

$debug_errors[] = wp_debug_backtrace_summary();