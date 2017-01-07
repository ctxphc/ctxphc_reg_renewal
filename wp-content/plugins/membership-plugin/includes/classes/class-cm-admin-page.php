<?php
/**
 * Created by PhpStorm.
 * User: ken_kilgore1
 * Date: 1/26/2015
 * Time: 2:56 PM
 */
namespace Membership\classes;


class CM_Admin_Page {

	/**
	 *
	 */
	public function construct() {
		$screen = get_current_screen();


		//$this->membership_admin_contextual_help( $screen );

		/* Add the Membership menu to the WP Admin page. */
		//$this->add_cm_menu();
	}


	/**
	 * Sets up any functionality needed in the admin.
	 *
	 * @since 1.1.1
	 */
	function add_cm_menu() {

		$page_title                 = esc_attr__( 'Membership Dashboard', 'membership' );
		$menu_title                 = esc_attr__( 'Membership', 'membership' );
		$capability                 = esc_attr__( 'manage_options', 'membership' );
		$menu_slug                  = esc_attr__( 'membership_dashboard', 'membership' );
		$plugin_function            = esc_attr__( 'membership_dashboard', 'membership' );
		$icon_url                   = esc_attr__( 'dashicons-palmtree', 'membership' );
		$menu_position              = '4.1';
		$this->membership_dashboard = add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $plugin_function, $icon_url, $menu_position );

		// Current Members report menu option
		$submenu_page_title         = esc_attr__( 'Active Members', 'membership' );
		$submenu_title              = esc_attr__( 'Active', 'membership' );
		$submenu_slug               = esc_attr__( 'active_members', 'membership' );
		$submenu_function           = esc_attr__( 'active_members', 'membership' );
		$this->current_members_page = add_submenu_page( $menu_slug, $submenu_page_title, $submenu_title, $capability, $submenu_slug, $submenu_function );

		//Pending Members report menu option
		$submenu_page_title         = esc_attr__( 'Pending Members', 'membership' );
		$submenu_title              = esc_attr__( 'Pending', 'membership' );
		$submenu_slug               = esc_attr__( 'pending_members', 'membership' );
		$submenu_function           = esc_attr__( 'pending_members', 'membership' );
		$this->pending_members_page = add_submenu_page( $menu_slug, $submenu_page_title, $submenu_title, $capability, $submenu_slug, $submenu_function );

		//Archived Members report menu option
		$submenu_page_title          = esc_attr__( 'Archived Members', 'membership' );
		$submenu_title               = esc_attr__( 'Archived', 'membership' );
		$submenu_slug                = esc_attr__( 'archived_members', 'membership' );
		$submenu_function            = esc_attr__( 'archived_members', 'membership' );
		$this->archived_members_page = add_submenu_page( $menu_slug, $submenu_page_title, $submenu_title, $capability, $submenu_slug, $submenu_function );

		//All Members report menu option
		$submenu_page_title     = esc_attr__( 'All Members', 'membership' );
		$submenu_title          = esc_attr__( 'All', 'membership' );
		$submenu_slug           = esc_attr__( 'all_members', 'membership' );
		$submenu_function       = esc_attr__( 'all_members', 'membership' );
		$this->all_members_page = add_submenu_page( $menu_slug, $submenu_page_title, $submenu_title, $capability, $submenu_slug, $submenu_function );

		//Create New Member menu option
		$submenu_page_title     = esc_attr__( 'Add New', 'membership' );
		$submenu_title          = esc_attr__( 'Add New', 'membership' );
		$submenu_slug           = esc_attr__( 'new_members', 'membership' );
		$submenu_function       = esc_attr__( 'new_members', 'membership' );
		$this->new_members_page = add_submenu_page( $menu_slug, $submenu_page_title, $submenu_title, $capability, $submenu_slug, $submenu_function );

		//Will only use this once on production server
		/**
		$submenu_page_title = 'Migrate Members';
		$submenu_title      = 'Migrate';
		$submenu_slug       = 'migrate_members';
		$submenu_function   = 'migrate_members';
		add_submenu_page( $menu_slug, $submenu_page_title, $submenu_title, $capability, $submenu_slug, $submenu_function );
		 */

		//Will only use this once on production server
		/*
		$submenu_page_title = 'Insert Pending Members';
		$submenu_title      = 'InsertPending';
		$submenu_slug       = 'insert_pending_members';
		$submenu_function   = 'insert_pending_members';
		add_submenu_page( $menu_slug, $submenu_page_title, $submenu_title, $capability, $submenu_slug, $submenu_function );
		*/

		//For testing purposes only
		/*
		$submenu_page_title = 'WP_Meta_Query Testing';
		$submenu_title      = 'Meta Query Testing';
		$submenu_slug       = 'meta_query_testing';
		$submenu_function   = 'meta_query_testing';
		add_submenu_page( $menu_slug, $submenu_page_title, $submenu_title, $capability, $submenu_slug, $submenu_function );
		*/
	}

	/**
	 *
	 */
	function membership_dashboard() {
		echo "<div>testing the membership dashboard.</div>";
		echo '<div>From inside class-cm-admin-page file.</div>';
	}
}


