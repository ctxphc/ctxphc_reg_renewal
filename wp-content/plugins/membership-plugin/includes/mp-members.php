<?php
/**
 * All Members administration panel.
 *
 * @package Club Membership Plugin
 * @subpackage Administration
 */

/** WordPress Administration Bootstrap */
require_once( WP_ADMIN_DIR . 'admin.php' );

if ( ! current_user_can( 'list_users' ) ) {
	wp_die( __( 'Cheatin&#8217; uh?' ), 403 );
}

if ( ! class_exists( '_MP_List_Table_Compat' ) ) {
	require_once( CLUB_MEMBERSHIP_INCLUDES . 'mp-list-table.php' );
}

global $wpdb, $memb_statuses, $membersearch;

/** @var STRING $ctxphc_status */
$status_table  = 'ctxphc_member_status';
$memb_statuses = $wpdb->get_results( "SELECT * FROM $status_table" );
$mp_list_table = _get_mp_list_table( 'MP_Active_Members_List_Table' );
$pagenum       = $mp_list_table->get_pagenum();
$title         = __( 'Active Members' );
$parent_file   = 'admin.php';


add_screen_option( 'per_page', array( 'label' => _x( 'Members', 'members per page (screen options)' ) ) );

// contextual help - choose Help on the top right of admin panel to preview this.
get_current_screen()->add_help_tab( array(
	'id'      => 'overview',
	'title'   => __( 'Overview' ),
	'content' => '<p>' . __( 'This screen lists all the active members for your site.' ) . '</p>' .
	             '<p>' . __( 'To add a new member to your site, click the Add New button at the top of the screen or Add New in the Membership menu section.' ) . '</p>'
) );

get_current_screen()->add_help_tab( array(
	'id'      => 'screen-display',
	'title'   => __( 'Screen Display' ),
	'content' => '<p>' . __( 'You can customize the display of this screen in a number of ways:' ) . '</p>' .
	             '<ul>' .
	             '<li>' . __( 'You can hide/display columns based on your needs and decide how many members to list per screen using the Screen Options tab.' ) . '</li>' .
	             '<li>' . __( 'You can filter the list of members by Member Status using the text links in the upper left to show Active, Archived, Pending or All members. The default view is to show Active members.' ) . '</li>' .
	             '<li>' . __( 'You can view all posts made by a member by clicking on the number under the Posts column.' ) . '</li>' .
	             '</ul>'
) );

$help = '<p>' . __( 'Hovering over a row in the members list will display action links that allow you to manage members. You can perform the following actions:' ) . '</p>' .
        '<ul>' .
        '<li>' . __( 'Edit takes you to the editable profile screen for that member. You can also reach that screen by clicking on the first name.' ) . '</li>';


$help .= '</ul>';

get_current_screen()->add_help_tab( array(
	'id'      => 'actions',
	'title'   => __( 'Actions' ),
	'content' => $help,
) );
unset( $help );

if ( empty( $_REQUEST ) ) {
	$referer = '<input type="hidden" name="wp_http_referer" value="' . esc_attr( wp_unslash( $_SERVER[ 'REQUEST_URI' ] ) ) . '" />';
} elseif ( isset( $_REQUEST[ 'wp_http_referer' ] ) ) {
	$redirect = remove_query_arg( array( 'wp_http_referer', 'updated', 'delete_count' ), wp_unslash( $_REQUEST[ 'wp_http_referer' ] ) );
	$referer  = '<input type="hidden" name="wp_http_referer" value="' . esc_attr( $redirect ) . '" />';
} else {
	$redirect = 'admin.php';
	$referer  = '';
}

$update = '';


switch ( $mp_list_table->current_action() ) {

	/* Bulk Dropdown menu handler */
	case 'archive':
		check_admin_referer( 'bulk-users' );

		if ( ! current_user_can( 'membership_manager' ) ) {
			wp_die( __( 'You can&#8217;t activate that user.' ) );
		}

		if ( empty( $_REQUEST[ 'members' ] ) ) {
			wp_redirect( $redirect );
			exit();
		}

		$memberids = $_REQUEST[ 'members' ];
		$update    = 'archive';
		foreach ( $memberids as $id ) {
			$id = (int) $id;

			// If the user doesn't already belong to the blog, bail.
			if ( is_multisite() && ! is_user_member_of_blog( $id ) ) {
				wp_die( __( 'Cheatin&#8217; uh?' ), 403 );
			}

			$member = get_memberdata( $id );
			$member->set_status( $_REQUEST[ 'status' ] );
		}

		wp_redirect( add_query_arg( 'update', $update, $redirect ) );
		exit();

	default:

		if ( ! empty( $_GET[ '_wp_http_referer' ] ) ) {
			wp_redirect( remove_query_arg( array( '_wp_http_referer', '_wpnonce' ), wp_unslash( $_SERVER[ 'REQUEST_URI' ] ) ) );
			exit;
		}

		$mp_list_table->prepare_items();
		$total_pages = $mp_list_table->get_pagination_arg( 'total_pages' );
		if ( $pagenum > $total_pages && $total_pages > 0 ) {
			wp_redirect( add_query_arg( 'paged', $total_pages ) );
			exit;
		}

		include_once( ABSPATH . 'wp-admin/admin-header.php' );

		$messages = array();
		if ( isset( $_GET[ 'update' ] ) ) :
			switch ( $_GET[ 'update' ] ) {
				case 'archive':
					$messages[ ] = '<div id="message" class="updated"><p>' . __( 'Archived.' ) . '</p></div>';
					break;
				case 'err_admin_role':
					$messages[ ] = '<div id="message" class="error"><p>' . __( 'The current user&#8217;s role must have user editing capabilities.' ) . '</p></div>';
					$messages[ ] = '<div id="message" class="updated"><p>' . __( 'Other user roles have been changed.' ) . '</p></div>';
					break;
				case 'err_admin_del':
					$messages[ ] = '<div id="message" class="error"><p>' . __( 'You can&#8217;t delete the current user.' ) . '</p></div>';
					$messages[ ] = '<div id="message" class="updated"><p>' . __( 'Other users have been deleted.' ) . '</p></div>';
					break;
				case 'remove':
					$messages[ ] = '<div id="message" class="updated fade"><p>' . __( 'User removed from this site.' ) . '</p></div>';
					break;
				case 'err_admin_remove':
					$messages[ ] = '<div id="message" class="error"><p>' . __( "You can't remove the current user." ) . '</p></div>';
					$messages[ ] = '<div id="message" class="updated fade"><p>' . __( 'Other users have been removed.' ) . '</p></div>';
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

				//Adds New Member link next to page title.
				if ( current_user_can( 'create_users' ) ) {
					sprintf( '<a href="?page=%s&action=%s&member=%s" class="add-new-h2">echo esc_html_x( "Add New", "member" )</a>', $_REQUEST[ 'page' ], 'new', '' );
				} elseif ( is_multisite() && current_user_can( 'create_users' ) ) {
					sprintf( '<a href="?page=%s&action=%s&member=%s" class="add-new-h2">echo esc_html_x( "Add New", "member" )</a>', $_REQUEST[ 'page' ], 'new', '' );
				}

				if ( $membersearch ) {
					printf( '<span class="subtitle">' . __( 'Search results for &#8220;%s&#8221;' ) . '</span>', esc_html( $membersearch ) );
				} ?>
			</h2>

			<?php $mp_list_table->views(); ?>

			<form action="" method="get">

				<?php $mp_list_table->search_box( __( 'Search Members' ), 'member' ); ?>

				<?php $mp_list_table->display(); ?>
			</form>

			<br class="clear"/>
		</div>
		<?php
		break;

} // end of the $doaction switch

include( ABSPATH . 'wp-admin/admin-footer.php' );
