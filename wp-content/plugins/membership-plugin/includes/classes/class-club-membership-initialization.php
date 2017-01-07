<?php
/**
 * Created by PhpStorm.
 * User: ken_kilgore1
 * Date: 4/13/2015
 * Time: 11:49 AM
 */

namespace Membership\classes;
global $debug_errors;


class Club_Membership_Initialization {

	/**
	 *
	 */
	function __construct() {
		/* Forces all Membership Usernames to be lowercase. */
		add_filter( 'sanitize_user', 'strtolower' );

		/* Set the constants needed by the plugin. */
		add_action( 'plugins_loaded', array( &$this, 'constants' ), 1 );

		/* Load the functions files. */
		add_action( 'plugins_loaded', array( &$this, 'includes' ), 3 );

		/* Load the admin files. */
		add_action( 'plugins_loaded', array( &$this, 'admin' ), 4 );

		/* Add the Membership menu to the WP Admin page. */
		add_action( 'admin_menu', array( &$this, 'cm_load_admin_menu' ), 2 );
	}

	function cm_membership_init(){

		/* Register activation hook. */
		register_activation_hook( __FILE__, array( &$this, 'activation' ) );
	}

	/**
	 *
	 */
	function constants() {

		/* Set WP wp-admin directory constant. */
		define( 'WP_ADMIN_DIR', trailingslashit( ABSPATH . 'wp-admin' ) );

		/* Set plugin URL path constant. */
		define( 'CM_URI', trailingslashit( plugin_dir_url( __FILE__ ) ) );

		/* Set plugin includes directory constant. */
		define( 'CM_INCLUDES', CM_DIR . trailingslashit( 'includes' ) );

		/* Set plugin classes directory constant */
		define( 'CM_CLASSES', CM_INCLUDES . trailingslashit( 'classes' ) );

		/* Set plugin admin directory constant. */
		define( 'CM_ADMIN_DIR', CM_DIR . trailingslashit( 'admin' ) );

		/* Set plugin version key constant */
		define( 'CM_VERSION_KEY', 'membership_version' );

		/* Set plugin version constant */
		define( 'CM_VERSION', '1.1.1' );

		/* set plugin database version key constant*/
		define( 'CM_DB_VERSION_KEY', 'membership_db_version' );

		/* Set plugin database version constant. */
		define( 'CM_DB_VERSION', 1.2 );

		add_option( CM_VERSION_KEY, CM_VERSION );
		add_option( CM_DB_VERSION_KEY, CM_DB_VERSION );
	}

	/**
	 * Loads the initial files needed by the plugin.
	 *
	 * @since 0.2.0
	 */
	function includes() {

		/* Load the plugin functions file. */
		require_once( CM_INCLUDES . '/functions.php' );

		/* Load the CM_Member class file */
		require_once( CM_CLASSES . '/class-cm-member.php' );

		/* Load the CM_List_Table class file */
		require_once( CM_CLASSES . '/class-cm-list-table.php' );
	}

	/**
	 * Loads the admin functions and files.
	 *
	 * @since 0.2.0
	 */
	function admin() {

		/* Only load files if in the WordPress admin. */
		if ( is_admin() ) {

			/* Load the main admin file. */
			require_once( CM_ADMIN_DIR . 'cm-admin.php' );
			//require_once( CM_CLASSES . 'class-cm-admin-page.php' );

			/* Load the plugin settings. */
			//require_once( CM_ADMIN_DIR . 'cm-settings.php' );
		}
	}


	/**
	 *
	 */
	function cm_load_admin_menu() {
		if ( ! class_exists( 'CM_Admin_Page' ) ) {
			require_once( CM_CLASSES . 'class-cm-admin-page.php' );
		}

		$cm_admin_menu = new CM_Admin_Page();
		$cm_admin_menu->add_cm_menu();
	}

	/**
	 * Method that runs only when the plugin is activated.
	 * Creates Membership_Manager role and set capabilities.
	 *
	 * @since 1.1.1
	 */
	function activation() {

		/* Get the membership manager role. */
		$role = get_role( 'manager_members' );

		/* If the membership manager role DOES NOT exists, create it. */
		if ( empty( $role ) ) {

			add_role(
				'manage_members',
				__( 'Manager Members' ),
				array(
					'read'          => true,
					'list_users'    => true,
					'create_users'  => true,
					'add_users'     => true,
					'delete_users'  => true,
					'edit_users'    => true,
					'remove_users'  => true,
					'promote_users' => true,
				)
			);

			//add new role to users that need this level of access.
			$role = get_role( 'manage_members' );
			$editor_role = get_role('editor');

			foreach ( $editor_role->capabilities as $cap_key => $cap_value ){
				$role->add_cap( $cap_key );
			}

			$member_managers = array( 'president', 'vice-president', 'membership' );
			foreach ( $member_managers as $memb_manager ){
				$user = get_user_by('login', $memb_manager );
				$user->add_role( 'manage_members');
			}


		}
		//TODO:  Run create_tables.
		//TODO:  Create Setup/Settings page.  (membership levels/types, costs per level/type etc.
	}
}

//$debug_errors[] = wp_debug_backtrace_summary();