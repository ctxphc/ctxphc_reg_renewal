<?php
/**
 * Created by PhpStorm.
 * User: ken_kilgore1
 * Date: 12/30/2014
 * Time: 10:59 AM
 */

function membership_dashboard() {
	echo "testing membership dashboard display from admin.php file";
}

function active_members() {
	include_once( CLUB_MEMBERSHIP_INCLUDES . 'mp-members.php' );
}

function pending_members() {
	include_once( CLUB_MEMBERSHIP_INCLUDES . 'mp-member-pending.php' );
}

function archived_members() {
	include_once( CLUB_MEMBERSHIP_INCLUDES . 'mp-members-archived.php' );
}

function all_members() {
	include_once( CLUB_MEMBERSHIP_INCLUDES . 'mp-members-all.php' );
}

function new_members() {
	include_once( CLUB_MEMBERSHIP_INCLUDES . 'mp-member-new.php' );
}

function migrate_members() {
	include_once( CLUB_MEMBERSHIP_INCLUDES . 'db-merge.php' );
}

function insert_pending_members() {
	include_once( CLUB_MEMBERSHIP_INCLUDES . 'mp-process-pending-members.php' );
}


/**
 * Adds custom contextual help on the plugin's admin screens.  This is the text shown under the "Help" tab.
 *
 * @since 1.1.1
 */
function membership_admin_contextual_help( $text, $screen ) {

	/* Text shown on the "Members Settings" screen in the admin. */
	if ( 'settings_page_members-settings' == $screen ) {
		$text = '';

		$text .= '<p>' . __( '<strong>Role Manager:</strong> This feature allows you to manage roles on your site by giving you the ability to create, edit, and delete any role. Note that changes to roles do not change settings for the Members plugin. You are literally changing data in your WordPress database. This plugin feature merely provides an interface for you to make these changes.', 'members' ) . '</p>';
		$text .= '<p>' . __( "<strong>Content Permissions:</strong> This feature adds a meta box to the post edit screen that allows you to grant permissions for who can read the post content based on the user's role. Only users of roles with the <code>restrict_content</code> capability will be able to use this component.", 'members' ) . '</p>';
		$text .= '<p>' . __( "<strong>Sidebar Widgets:</strong> This feature creates additional widgets for use in your theme's sidebars. You can access them by clicking Widgets in the menu.", 'members' ) . '</p>';
		$text .= '<p>' . __( '<strong>Private Site:</strong> This feature allows you to redirect all users who are not logged into the site to the login page, creating an entirely private site. You may also replace your feed content with a custom error message.', 'members' ) . '</p>';

		$text .= '<p><strong>' . __( 'For more information:', 'members' ) . '</strong></p>';

		$text .= '<ul>';
		$text .= '<li><a href="' . MEMBERSHIP_URI . 'docs/readme.html">' . __( 'Documentation', 'members' ) . '</a></li>';
		$text .= '<li><a href="http://themehybrid.com/support">' . __( 'Support Forums', 'members' ) . '</a></li>';
		$text .= '</ul>';
	} /* Text shown on the "Roles" screens in the admin. */
	elseif ( 'users_page_users' == $screen ) {
		$text = '';

		/* Text for the "Edit Role" screen. */
		if ( isset( $_GET[ 'action' ] ) && 'edit' == $_GET[ 'action' ] ) {

			$text .= '<p>' . __( 'This screen allows you to edit the capabilities given to the role. You can tick the checkbox next to a capability to add the capability to the role. You can untick the checkbox next to a capability to remove a capability from the role. You can also add as many custom capabilities as you need in the Custom Capabilities section.', 'members' ) . '</p>';
			$text .= '<p>' . __( 'Capabilities are both powerful and dangerous tools. You should not add or remove a capability to a role unless you understand what permission you are granting or removing.', 'members' ) . '</p>';
		} /* Text shown on the main "Roles" screen. */
		else {
			$text .= '<p>' . __( 'This screen lists all the user roles available on this site. Roles are given to users as a way to "group" them. Roles are made up of capabilities (permissions), which decide what functions users of each role can perform on the site. From this screen, you can manage these roles and their capabilities.', 'members' ) . '</p>';
			$text .= '<p>' . __( 'To add a role to a user, click Users in the menu. To create a new role, click the Add New button at the top of the screen or Add New Role under the Users menu.', 'members' ) . '</p>';
		}

		/* Text shown for both the "Roles" and "Edit Role" screen. */
		$text .= '<p><strong>' . __( 'For more information:', 'members' ) . '</strong></p>';

		$text .= '<ul>';
		$text .= '<li><a href="http://justintadlock.com/archives/2009/08/30/users-roles-and-capabilities-in-wordpress">' . __( 'Users, Roles, and Capabilities', 'members' ) . '</a></li>';
		$text .= '<li><a href="' . MEMBERSHIP_URI . 'docs/readme.html">' . __( 'Documentation', 'members' ) . '</a></li>';
		$text .= '<li><a href="http://themehybrid.com/support">' . __( 'Support Forums', 'members' ) . '</a></li>';
		$text .= '</ul>';
	} /* Text to show on the "Add New Role" screen in the admin. */
	elseif ( 'users_page_role-new' == $screen || 'users_page_role' == $screen ) {
		$text = '';

		$text .= '<p>' . __( 'This screen allows you to create a new user role for your site. You must input a unique role name and role label. You can also grant capabilities (permissions) to the new role. Capabilities are both powerful and dangerous tools. You should not add a capability to a role unless you understand what permission you are granting.', 'members' ) . '</p>';
		$text .= '<p>' . __( 'To add a role to a user, click Users in the menu. To edit roles, click Roles under the Users menu.', 'members' ) . '</p>';

		$text .= '<p><strong>' . __( 'For more information:', 'members' ) . '</strong></p>';

		$text .= '<ul>';
		$text .= '<li><a href="http://justintadlock.com/archives/2009/08/30/users-roles-and-capabilities-in-wordpress">' . __( 'Users, Roles, and Capabilities', 'members' ) . '</a></li>';
		$text .= '<li><a href="' . MEMBERSHIP_URI . 'docs/readme.html">' . __( 'Documentation', 'members' ) . '</a></li>';
		$text .= '<li><a href="http://themehybrid.com/support">' . __( 'Support Forums', 'members' ) . '</a></li>';
		$text .= '</ul>';
	}

	/* Return the contextual help text. */

	return $text;
}

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
 *
 * @var string $return_value
 * @return string
 */
function membership_get_nonce( $action = '' ) {
	if ( $action ) {
		$return_value = "members-component-action_{$action}";
	} else {
		$return_value = "members-plugin";
	}


	return $return_value;
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