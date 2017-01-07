<?php
/**
 * All Members administration panel.
 *
 * @package Club Membership Plugin
 * @subpackage Administration
 */
use Membership\Classes\CM_Members_List_Table;


/** WordPress Administration Bootstrap */
require_once( WP_ADMIN_DIR . 'admin.php' );

if ( ! current_user_can( 'create_users' ) ) {
	wp_die( __( 'Cheatin&#8217; uh?' ), 403 );
}

if ( ! class_exists( 'CM_Members_List_Table' ) ) {
	require_once( CM_CLASSES . 'class-CMMembersListTable.php' );
}

global $wpdb, $memb_statuses, $membersearch;

/** @var STRING $ctxphc_status */
$status_table = 'ctxphc_member_status';

/** @var OBJECT $memb_statuses */
$args[ 'memb_statuses' ] = $wpdb->get_results( "SELECT * FROM $status_table" );

$args[ 'screen' ] = get_current_screen();


if ( isset( $_GET[ 'page' ] ) && '' !== $_GET[ 'page' ] ) {
	if ( isset( $_GET[ 'cm_status' ] ) && '' !== $_GET[ 'cm_status' ] ) {
		switch ( strtolower( $_GET[ 'cm_status' ] ) ) {
			case 'active':
				$args[ 'list_type' ] = 'Active';
				$title               = __( 'Active Members' );
				break;
			case 'archived':
				$args[ 'list_type' ] = 'Archived';
				$title               = __( 'Archived Members' );
				break;
			case 'pending':
				$args[ 'list_type' ] = 'Pending';
				$title               = __( 'Pending Members' );
				break;
			case 'all':
				$args[ 'list_type' ] = 'All';
				$title               = __( 'All Members' );
				break;
		}
	} else {
		switch ( $_GET[ 'page' ] ) {
			case 'active_members':
				$args[ 'list_type' ] = 'Active';
				$title               = __( 'Active Members' );
				break;
			case 'archived_members':
				$args[ 'list_type' ] = 'Archived';
				$title               = __( 'Archived Members' );
				break;
			case 'pending_members':
				$args[ 'list_type' ] = 'Pending';
				$title               = __( 'Pending Members' );
				break;
			case 'all_members':
				$args[ 'list_type' ] = 'All';
				$title               = __( 'All Members' );
				break;
			case 'new_members':
				$args[ 'list_type' ] = 'New';
				$title               = __( 'New Member Entry' );
				break;
			case 'edit_member':
				$args[ 'list_type' ] = 'Edit';
				$user_id             = $_GET[ "user_id" ];
				$title               = __( 'Edit Member ' );
				break;
		}
	}
	if ( isset( $_GET[ 'action' ] ) && '' != $_GET[ 'action' ] ) {
		$args[ 'action' ] = $_GET[ 'action' ];
	}
} else {

	$args[ 'list_type' ] = 'Active';
	$title               = __( 'Active Members' );
}
/** @var OBJECT $cm_list_table */
//$cm_list_table = _get_cm_list_table( $list_type, $args );
$cm_list_table = new CM_Members_List_Table( $args );

/* Set filter used to get column headers for member listings */
//add_filter( 'membership_page_active_members', array( $cm_list_table, 'get_column_info' ) );
//add_filter( 'manage_membership_page_active_members_columns', array( $cm_list_table, 'get_column_info' ) );

/** @var STRING $pagenum */
$pagenum = $cm_list_table->get_pagenum();

/** @var STRING $parent_file */
if ( ( ! isset( $parent_file ) ) && empty( $parent_file ) ) {
	$parent_file = 'admin.php';
}


//$hook = add_menu_page('My Plugin List Table', 'My List Table Example', 'activate_plugins', 'my_list_test',
//'my_render_list_page');
//add_action( "load-$hook", 'add_options' );


add_screen_option( 'per_page', array( 'label' => _x( 'Members', 'members per page (screen options)' ) ) );

// contextual help - choose Help on the top right of admin panel to preview this.
get_current_screen()->add_help_tab( array(
		'id'      => 'overview',
		'title'   => __( 'Overview' ),
		'content' => '<p>' . __( 'This screen lists all the existing members for your site. Each member has two actions that can be taken, Archive or Edit.' ) . '</p>' .
		             '<p>' . __( 'To add a new member to your site, click the Add New button at the top of the screen or Add New in the Members
		             menu
		             section.' ) . '</p>',
	)
);

get_current_screen()->add_help_tab( array(
		'id'      => 'screen-display',
		'title'   => __( 'Screen Display' ),
		'content' => '<p>' . __( 'You can customize the display of this screen in a number of ways:' ) . '</p>' .
		             '<ul>' .
		             '<li>' . __( 'You can hide/display columns based on your needs and decide how many members to list
		              per screen using the Screen Options tab.' ) . '</li>' .
		             '<li>' . __( 'You can filter the list of members by using the text links in the upper left to show
		             All members, Active members, Pending Members or All members. The default view is to show active
		             members.' ) . '</li>' .
		             '<li>' . '</ul>',
	)
);

$help = '<p>' . __( 'Hovering over a row in the members list will display action links that allow you to manage members. You can perform the following actions:' ) . '</p>' .
        '<ul>' .
        '<li>' . __( 'Edit takes you to the editable profile screen for that member.' ) . '</li>' . '<li>' . __( 'Edit takes you to the editable profile screen for that member.' ) .
        '</li>' .
        '</ul>';

get_current_screen()->add_help_tab( array(
	'id'      => 'actions',
	'title'   => __( 'Actions' ),
	'content' => $help,
) );
unset( $help );

get_current_screen()->set_help_sidebar(
	'<p><strong>' . __( 'For more information:' ) . '</strong></p>' .
	'<p>' . __( '<a href="http://codex.wordpress.org/Users_Screen" target="_blank">Documentation on Managing Users</a>' ) . '</p>' .
	'<p>' . __( '<a href="http://codex.wordpress.org/Roles_and_Capabilities" target="_blank">Descriptions of Roles and Capabilities</a>' ) . '</p>' .
	'<p>' . __( '<a href="https://wordpress.org/support/" target="_blank">Support Forums</a>' ) . '</p>'
);

if ( empty( $_REQUEST ) ) {
	$referer = '<input type="hidden" name="wp_http_referer" value="' . esc_attr( wp_unslash( $_SERVER[ 'REQUEST_URI' ] ) ) . '" />';
} elseif ( isset( $_REQUEST[ 'wp_http_referer' ] ) ) {
	$redirect = remove_query_arg( array(
		'wp_http_referer',
		'updated',
		'delete_count',
	), wp_unslash( $_REQUEST[ 'wp_http_referer' ] ) );
	$referer  = '<input type="hidden" name="wp_http_referer" value="' . esc_attr( $redirect ) . '" />';
} else {
	$redirect = 'admin.php';
	$referer  = '';
}

$update = '';

switch ( $cm_list_table->current_action() ) {

	case 'doarchive':
		check_admin_referer( 'edit-users' );

		if ( ! is_multisite() ) {
			wp_die( __( 'You can&#8217;t archive members.' ) );
		}

		if ( empty( $_REQUEST[ 'users' ] ) ) {
			wp_redirect( $redirect );
			exit;
		}

		if ( ! current_user_can( 'edit_users' ) ) {
			wp_die( __( 'You can&#8217;t archive members.' ) );
		}

		$userids = $_REQUEST[ 'users' ];

		$update = 'archive';
		foreach ( $userids as $id ) {
			$id = (int) $id;
			if ( $id == $current_user->ID && ! is_super_admin() ) {
				$update = 'err_admin_remove';
				continue;
			}
			if ( ! current_user_can( 'edit_user', $id ) ) {
				$update = 'err_admin_remove';
				continue;
			}
			archive_member( $id );
		}

		$redirect = add_query_arg( array( 'update' => $update ), $redirect );
		wp_redirect( $redirect );
		exit;

	case 'archive':

		check_admin_referer( 'bulk-users' );

		if ( empty( $_REQUEST[ 'users' ] ) && empty( $_REQUEST[ 'user' ] ) ) {
			wp_redirect( $redirect );
			exit();
		}

		if ( ! current_user_can( 'edit_users' ) ) {
			$error = new WP_Error( 'edit_users', __( 'You can&#8217;t archive users.' ) );
		}

		if ( empty( $_REQUEST[ 'users' ] ) ) {
			$userids = array( intval( $_REQUEST[ 'user' ] ) );
		} else {
			$userids = $_REQUEST[ 'users' ];
		}
		?>
		<form method="post" name="archivememberss" id="archivemembers">
			<?php wp_nonce_field( 'archive-users' ) ?>
			<?php echo $referer; ?>

			<div class="wrap">
				<h2><?php _e( 'Archive Members' ); ?></h2>

				<p><?php _e( 'You have specified these members for archival:' ); ?></p>
				<ul>
					<?php
					$go_archive = false;
					foreach ( $userids as $id ) {
						$id   = (int) $id;
						$user = get_userdata( $id );
						if ( $id == $current_user->ID && ! is_super_admin() ) {
							echo "<li>" . sprintf( __( 'ID #%1$s: %2$s <strong>The current user will not be archived.</strong>' ), $id, $user->user_login ) . "</li>\n";
						} elseif ( ! current_user_can( 'remove_user', $id ) ) {
							echo "<li>" . sprintf( __( 'ID #%1$s: %2$s <strong>You don\'t have permission to archive this
							user.</strong>' ), $id, $user->user_login ) . "</li>\n";
						} else {
							echo "<li><input type=\"hidden\" name=\"users[]\" value=\"{$id}\" />" . sprintf( __( 'ID #%1$s: %2$s' ), $id, $user->user_login ) . "</li>\n";
							$go_archive = true;
						}
					}
					?>
				</ul>
				<?php if ( $go_archive ) : ?>
					<input type="hidden" name="action" value="doarchive"/>
					<?php submit_button( __( 'Confirm Archival' ), 'secondary' ); ?>
				<?php else : ?>
					<p><?php _e( 'There are no valid members selected for archival.' ); ?></p>
				<?php endif; ?>
			</div>
		</form>
		<?php
		Break;

	case 'edit':

		check_admin_referer( 'bulk-users' );

		if ( empty( $_REQUEST[ 'users' ] ) && empty( $_REQUEST[ 'user' ] ) ) {
			wp_redirect( $redirect );
			exit();
		}

		if ( ! current_user_can( 'edit_users' ) ) {
			$error = new WP_Error( 'edit_users', __( 'You can&#8217;t create new members.' ) );
		}

		if ( empty( $_REQUEST[ 'users' ] ) ) {
			$userids = array( intval( $_REQUEST[ 'user' ] ) );
		} else {
			$userids = $_REQUEST[ 'users' ];
		}
		?>
		<form method="post" name="newmemberss" id="newmembers">
			<?php wp_nonce_field( 'new-users' ) ?>
			<?php echo $referer; ?>

			<div class="wrap">
				<h2><?php _e( 'Add New Member' ); ?></h2>

				<p><?php _e( 'You have specified these members for archival:' ); ?></p>
				<ul>
					<?php
					$go_archive = false;
					foreach ( $userids as $id ) {
						$id   = (int) $id;
						$user = get_userdata( $id );
						if ( $id == $current_user->ID && ! is_super_admin() ) {
							echo "<li>" . sprintf( __( 'ID #%1$s: %2$s <strong>The current user will not be archived.</strong>' ), $id, $user->user_login ) . "</li>\n";
						} elseif ( ! current_user_can( 'remove_user', $id ) ) {
							echo "<li>" . sprintf( __( 'ID #%1$s: %2$s <strong>You don\'t have permission to archive this
							user.</strong>' ), $id, $user->user_login ) . "</li>\n";
						} else {
							echo "<li><input type=\"hidden\" name=\"users[]\" value=\"{$id}\" />" . sprintf( __( 'ID #%1$s: %2$s' ), $id, $user->user_login ) . "</li>\n";
							$go_archive = true;
						}
					}
					?>
				</ul>
				<?php if ( $go_archive ) : ?>
					<input type="hidden" name="action" value="donewmember"/>
					<?php submit_button( __( 'Add Member' ), 'secondary' ); ?>
				<?php else : ?>
					<p><?php _e( 'Something went horribly wrong!' ); ?></p>
				<?php endif; ?>
			</div>
		</form>
		<?php
		Break;

	default:

		if ( ! empty( $_GET[ '_wp_http_referer' ] ) ) {
			wp_redirect( remove_query_arg( array( '_wp_http_referer', '_wpnonce' ), wp_unslash( $_SERVER[ 'REQUEST_URI' ] ) ) );
			exit;
		}

		$cm_list_table->prepare_items( $args );
		$total_pages = $cm_list_table->get_pagination_arg( 'total_pages' );
		if ( $pagenum > $total_pages && $total_pages > 0 ) {
			wp_redirect( add_query_arg( 'paged', $total_pages ) );
			exit;
		}

		//include( ABSPATH . 'wp-admin/admin-header.php' );

		$messages = array();
		if ( isset( $_GET[ 'update' ] ) ) :
			switch ( $_GET[ 'update' ] ) {
				case 'del':
				case 'del_many':
					$delete_count = isset( $_GET[ 'delete_count' ] ) ? (int) $_GET[ 'delete_count' ] : 0;
					if ( 1 == $delete_count ) {
						$message = __( 'User deleted.' );
					} else {
						$message = _n( '%s user deleted.', '%s users deleted.', $delete_count );
					}
					$messages[] = '<div id="message" class="updated notice is-dismissible"><p>' . sprintf( $message, number_format_i18n( $delete_count ) ) . '</p></div>';
					break;
				case 'add':
					if ( isset( $_GET[ 'id' ] ) && ( $user_id = $_GET[ 'id' ] ) && current_user_can( 'edit_user', $user_id ) ) {
						$messages[] = '<div id="message" class="updated notice is-dismissible"><p>' . sprintf( __( 'New user created. <a href="%s">Edit user</a>' ),
								esc_url( add_query_arg( 'wp_http_referer', urlencode( wp_unslash( $_SERVER[ 'REQUEST_URI' ] ) ),
									self_admin_url( 'user-edit.php?user_id=' . $user_id ) ) ) ) . '</p></div>';
					} else {
						$messages[] = '<div id="message" class="updated notice is-dismissible"><p>' . __( 'New user created.' ) . '</p></div>';
					}
					break;
				case 'promote':
					$messages[] = '<div id="message" class="updated notice is-dismissible"><p>' . __( 'Changed roles.' ) . '</p></div>';
					break;
				case 'err_admin_role':
					$messages[] = '<div id="message" class="error notice is-dismissible"><p>' . __( 'The current user&#8217;s role must have user editing capabilities.' ) . '</p></div>';
					$messages[] = '<div id="message" class="updated notice is-dismissible"><p>' . __( 'Other user roles have been changed.' ) . '</p></div>';
					break;
				case 'err_admin_del':
					$messages[] = '<div id="message" class="error notice is-dismissible"><p>' . __( 'You can&#8217;t delete the current user.' ) . '</p></div>';
					$messages[] = '<div id="message" class="updated notice is-dismissible"><p>' . __( 'Other users have been deleted.' ) . '</p></div>';
					break;
				case 'remove':
					$messages[] = '<div id="message" class="updated notice is-dismissible fade"><p>' . __( 'User removed from this site.' ) . '</p></div>';
					break;
				case 'err_admin_remove':
					$messages[] = '<div id="message" class="error notice is-dismissible"><p>' . __( "You can't remove the current user." ) . '</p></div>';
					$messages[] = '<div id="message" class="updated notice is-dismissible fade"><p>' . __( 'Other users have been removed.' ) . '</p></div>';
					break;
			}
		endif; ?>

		<?php if ( isset( $errors ) && is_wp_error( $errors ) ) : ?>
		<div class="error">
			<ul>
				<?php
				foreach ( $errors->get_error_messages() as $err ) {
					echo "<li>$err</li>\n";
				}
				?>
			</ul>
		</div>
	<?php endif;

		if ( ! empty( $messages ) ) {
			foreach ( $messages as $msg ) {
				echo $msg;
			}
		} ?>

		<div class="wrap">
			<h2>
				<?php
				echo esc_html( $title );
				if ( current_user_can( 'create_users' ) ) { ?>
					<a href="admin.php?page=new_members" class="add-new-h2"><?php echo esc_html_x( 'Add New', 'member' ); ?></a>
				<?php }

				if ( $membersearch ) {
					printf( '<span class="subtitle">' . __( 'Search results for &#8220;%s&#8221;' ) . '</span>', esc_html( $membersearch ) );
				} ?>
			</h2>

			<?php $cm_list_table->views(); ?>

			<form method="get">

				<?php $cm_list_table->search_box( __( 'Search Users' ), 'user' ); ?>

				<?php $cm_list_table->display(); ?>
			</form>

			<br class="clear"/>
		</div>
		<?php
		break;

} // end of the $doaction switch

include( ABSPATH . 'wp-admin/admin-footer.php' );
