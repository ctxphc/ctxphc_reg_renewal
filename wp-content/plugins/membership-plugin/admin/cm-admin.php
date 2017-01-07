<?php
/**
 * Created by PhpStorm.
 * User: ken_kilgore1
 * Date: 12/30/2014
 * Time: 10:59 AM
 */

/**
 *
 * Calls cm_membership_init from the includes/functions.php file
 * which will enqueue javascript and css files for use in all Club Membership Admin pages.
 *
 */
function cm_member_admin_css( $hook ) {
if ( 'load-membership_page_new_members' != $hook ){
		return;
	}

	//Register Membership Admin Page Custom CSS Stylesheet
	// wp_register_style( 'cm-member-style', plugin_dir_url(  __FILE__ ) . '/css/cm-admin-style.css' );
	wp_enqueue_style( 'cm-member-style', plugin_dir_url(  __FILE__ ) . '/css/cm-admin-style.css' );
}
add_action( 'admin_enqueue_scripts', 'cm_member_admin_css' );
/**
 * Create Club Membership admin menu.
 */
function membership_dashboard() {
	echo "testing membership dashboard display from cm-admin.php file";

	add_action( 'add_meta_boxes_post', 'adding_custom_meta_boxes' );
}

function adding_custom_meta_boxes( $post ) {
	add_meta_box(
		'my-meta-box',
		__( 'My Meta Box' ),
		'render_my_meta_box',
		'post',
		'normal',
		'default'
	);
}

function cm_members() {
	include_once( CM_INCLUDES . 'cm-members.php' );
}

function active_members() {
	include_once( CM_INCLUDES . 'cm-members.php' );
}

function pending_members() {
	include_once( CM_INCLUDES . 'cm-members.php' );
}

function archived_members() {
	include_once( CM_INCLUDES . 'cm-members.php' );
}

function all_members() {
	include_once( CM_INCLUDES . 'cm-members.php' );
}

function new_members() {
	include_once( CM_INCLUDES . 'cm-new-member.php' );
}

function edit_member() {
	include_once( CM_INCLUDES . 'cm-edit-member.php' );
}

/**
 * function migrate_members() {
 * include_once( CM_INCLUDES . 'db-merge.php' );
 * }
 **/

/**
 *
 * function insert_pending_members() {
 *  include_once( CM_INCLUDES . 'cm-process-pending-members.php' );
 * }
 *
 */


/**
 * Message to show when a single member has been deleted.
 *
 * @since 1.1.1
 */
function membership_message_member_deleted() {
	membership_admin_message( '', __( 'Member deleted.', 'members' ) );
}

/**
 * Message to show when multiple members have been deleted (bulk delete).
 *
 * @since 1.1.1
 */
function membership_message_members_deleted() {
	membership_admin_message( '', __( 'Selected members deleted.', 'members' ) );
}

/**
 * Message to show when a single member has been archived.
 *
 * @since 1.1.1
 */
function membership_message_member_archived() {
	membership_admin_message( '', __( 'Member archived.', 'members' ) );
}

/**
 * Message to show when multiple members have been archived (bulk delete).
 *
 * @since 1.1.1
 */
function membership_message_members_archived() {
	membership_admin_message( '', __( 'Selected members archived.', 'members' ) );
}

/**
 * A function for displaying messages in the admin.  It will wrap the message in the appropriate <div> with the
 * custom class entered.  The updated class will be added if no $class is given.
 *
 * @since 0.1.0
 *
 * @param $class string Class the <div> should have.
 * @param $message string The text that should be displayed.
 */
function membership_admin_message( $class = 'updated', $message = '' ) {
	echo '<div class="' . ( ! empty( $class ) ? esc_attr( $class ) : 'updated' ) . '"><p><strong>' . $message . '</strong></p></div>';
}

/**
 * Members plugin nonce function.  This is to help with securely making sure forms have been processed
 * from the correct place.
 *
 * @since 0.1.0
 *
 * @param $action string Additional action to add to the nonce.
 */
function membership_get_nonce( $action = '' ) {
	if ( $action ) {
		return "cm_action_{$action}";
	} else {
		return "cm_plugin";
	}
}

/**
 * Function for safely deleting a member and transferring that members posts to the default poster.
 *
 * @since 1.1.1
 *
 * @param string $member_id The ID of the member to delete.
 */
function membership_delete_member( $member_id ) {

	/* Get all the members to be deleted. */
	$members = get_members( array( 'ID' => $member_id ) );

	/* Remove the member. */
	// TODO: create delete for the member based on ID.
}