<?php
/*
Plugin Name: CTXPHC Site Administration
Plugin URI: http://kaptkaos.com/ctxphc_admin_plugin
Description: The CTXPHC Site Administation Plug-in
Version: 1.2
Author: Ken Kilgore
Author URI: http://kaptkaos.com
License: GPL2  - most WordPress plugins are released under GPL2 license terms
*/

date_default_timezone_set('America/Chicago');

function ctxphc_admin_dashboard_init() {
       /* Register our stylesheet. */
       wp_register_style( 'ctxphc_admin_stylesheet', plugins_url('includes/css/ctxphc-admin-styles.css', __FILE__) );
       wp_enqueue_style( 'ctxphc_admin_stylesheet' );
}
register_activation_hook( __FILE__, 'ctxphc_admin_dashboard_init' );

//Adding custom roles and capablilities for BOD member and Administrator roles.
//add_role( $role_name, $display_name, $capabilities );
function create_ctxphc_bod_role(){
     $bod_cap = array(
          'archive_members'        => true,
          'renew_members'          => true,
          'view_ctxphc_dashboard'  => true,
          'create_users'           => true,
          'edit_users'             => true,
          'moderate_comments'      => true,
          'manage_categories'      => true,
          'manage_links'           => true,
          'manage_options'         => true,
          'edit_others_posts'      => true,
          'edit_pages'             => true,
          'edit_others_pages'      => true,
          'edit_published_pages'   => true,
          'publish_pages'          => true,
          'delete_pages'           => true,
          'delete_others_pages'    => true,
          'delete_published_pages' => true,
          'delete_others_posts'    => true,
          'delete_private_posts'   => true,
          'edit_private_posts'     => true,
          'read_private_posts'     => true,
          'delete_private_pages'   => true,
          'edit_private_pages'     => true,
          'read_private_pages'     => true,
          'edit_published_posts'   => true,
          'upload_files'           => true,
          'create_product'         => true,
          'publish_posts'          => true,
          'delete_published_posts' => true,
          'edit_posts'             => true,
          'delete_posts'           => true,
          'read'                   => true
     );

     add_role( 'bod_member', 'BOD Member', $bod_cap);
}
register_activation_hook(__FILE__, 'create_ctxphc_bod_role');

function add_ctxphc_bod_role(){
     $userid = wp_get_current_user();
     $user = new WP_User( $userid );
     if ( $user->has_cap( 'edit_users' ) && ! $user->has_cap( 'archive_members')){
          $user->add_cap( 'archive_members');
     }
     
     if ( $user->has_cap( 'edit_users' ) && ! $user->has_cap( 'renew_members')){
          $user->add_cap( 'renew_members');
     }
     
     if ( $user->has_cap( 'edit_users' ) && ! $user->has_cap( 'view_ctxphc_dashboard')){
          $user->add_cap( 'view_ctxphc_dashboard');
     }
          
}
add_action('admin_menu','add_ctxphc_bod_role');

function ctxphc_admin_menus() {
    // Main CTXPHC Site Administation Dashboard Menu
    add_menu_page('CTXPHC BOD Dashboard', 'CTXPHC Dashboard', 'manage_options', 'ctxphc_admin_dashboard', 'ctxphc_admin_dashboard','','4.1');
    add_submenu_page('ctxphc_admin_dashboard','Member List','Member List','manage_options','ctxphc_membership_listing', 'ctxphc_members');
    add_submenu_page('ctxphc_admin_dashboard',"Member Entry", "New Member",'manage_options','ctxphc_membership_entry', 'ctxphc_membership_entry_dashboard');
    add_submenu_page('ctxphc_admin_dashboard','Pending Members','List Pending Members','manage_options','ctxphc_pending_members_listing', 'ctxphc_pending_members_dashboard');
    //add_submenu_page('ctxphc_admin_dashboard','Renewal List','Renewal List','manage_options','ctxphc_renewal_listing', 'ctxphc_renewal_dashboard');
    //add_submenu_page('ctxphc_admin_dashboard','Parrot Points', 'Parrot Points','manage_options','ctxphc_parrot_points', 'ctxphc_parrot_points_dashboard');
    //add_submenu_page('ctxphc_admin_dashboard',"Pirate's Ball", "Pirate's Ball",'manage_options','ctxphc_pirates_ball', 'ctxphc_piretes_ball_dashboard');
    //add_submenu_page('ctxphc_admin_dashboard',"Pirate's Ball Entry", "Pirate's Ball Entry",'manage_options','ctxphc_pirates_ball_entry', 'ctxphc_piretes_ball_entry_dashboard');
    //add_menu_page('CTXPHC Family Table Update', 'CTXPHC Family Table Update', 'manage_options', 'ctxphc_family_table_update', 'ctxphc_family_table_update','','4.2');
}
add_action('admin_menu','ctxphc_admin_menus');


function ctxphc_admin_dashboard() {
    include_once( 'ctxphc-admin-dashboard.php' );
}

function ctxphc_membership_dashboard() {
    include_once( 'ctxphc-membership-dashboard.php' );
}

function ctxphc_members() {
    include_once( 'ctxphc_members.php' );
}

function ctxphc_membership_entry_dashboard() {
    include_once( 'ctxphc-membership-entry.php' );
}


function ctxphc_renewal_dashboard() {
    include_once( 'ctxphc-renewal-dashboard.php' );
}

function ctxphc_pending_members_dashboard() {
     include_once 'ctxphc_pending_members_dashboard.php';
}

function ctxphc_parrot_points_dashboard() {
    include_once( 'ctxphc-parrot-points-dashboard.php' );
}

function ctxphc_piretes_ball_dashboard() {
    include_once( 'ctxphc-pirates-ball-dashboard.php' );
}

function ctxphc_piretes_ball_entry_dashboard() {
    include_once( 'ctxphc-pirates-ball-entry-dashboard.php' );
}

function ctxphc_family_table_update(){
    include_once( 'ctxphc_family_table_update.php');
}