<?php
/**
 * Created by PhpStorm.
 * User: ken_kilgore1
 * Date: 10/24/2014
 * Time: 2:23 PM
 */

//include_once 'class-display-membership-list-table.php';
require_once( dirname( __FILE__ ) . '/class-display-membership-list-table.php' );

/** @var wpdb $wpdb */
global $memberReport, $wpdb;


// Check if an action needs to be taken on a member
if ( isset( $_GET[ 'action' ] ) ) {
	$member_action = esc_attr( $_GET[ 'action' ] );
	md_process_action( $member_action );

} else {
	// see if anything was entered into the search field.
	if ( isset( $_POST[ 's' ] ) ) {
		$search_request = esc_attr( $_POST[ 's' ] );
		md_member_search( $search_request );
	} else {
		if ( isset( $_REQUEST[ 'mdview' ] ) ) {
			$memb_rpt = sanitize_text_field( $_REQUEST[ 'mdview' ] );
			switch ( $memb_rpt ) {
				case 'active':
					render_members_report( 'active' );
					break;
				case 'pending':

			}
		}
		$rpt_type = ( ! empty( $_REQUEST[ 'mdview' ] ) ? $_REQUEST[ 'mdview' ] : 'active' );
		render_members_report( $rpt_type );
	}
}

/**
 * @param $member_action
 */
function md_process_action( $member_action ) {
	/** @var wpdb $wpdb */
	global $archived_result, $edit_result, $activated_result, $delete_result, $wpdb;

	switch ( $member_action ) {
		/**
		 * **********************************
		 * Archive Active Member
		 * **********************************
		 */
		case 'archive':
			$report        = 'active';
			$id_to_archive = esc_attr( $_GET[ 'member' ] );
			$member_table  = 'ctxphc_members';
			$arc_data      = array(
				'status_id' => 2,
			);
			$arc_where     = array(
				'ID' => $id_to_archive,
			);

			$args = array(
				'data'  => $arc_data,
				'table' => $member_table,
				'where' => $arc_where,
			);

			$result = md_update_member( $args );
			if ( $result ) {
				$archived_member = $wpdb->get_row( "SELECT * FROM ctxphc_members WHERE ID = $id_to_archive", object );

				$archived_result = "$archived_member->first_name $archived_member->last_name";
			} else {
				$archived_result = $result;
			}
			break;
		//  End of Archive Active Member

		/**
		 * *****************************
		 * Edit Member
		 * ****************************
		 */
		case 'edit':
			$edit_memb_id = esc_attr( $_GET[ 'member' ] );
			$member_table = 'ctxphc_members';
			$edit_where   = array(
				'ID' => $edit_memb_id,
			);

			$args = array(
				'table' => $member_table,
				'where' => $edit_where,
			);

			$result = md_edit_member( $args );

			if ( $result ) {
				$edited_data = $wpdb->get_row( "SELECT * FROM ctxphc_members WHERE ID = $edit_memb_id", object );

				$edit_result = $edited_data->first_name . ' ' . $edited_data->last_name;
			} else {
				$edit_result = $result;
			}
			break;
		//  End of Edit Member


		/**
		 * ****************************
		 * Activate Pending Member
		 * ****************************
		 */
		case 'activate':
			$report             = 'pending';
			$pend_memb_id       = esc_attr( $_GET[ 'member' ] );
			$member_table       = 'ctxphc_members';
			$update_memb_status = array(
				'status_id' => 1,
			);
			$update_where       = array(
				'ID' => $pend_memb_id,
			);

			$args = array(
				'data'  => $update_memb_status,
				'table' => $member_table,
				'where' => $update_where,
			);

			$result = md_update_member( $args );
			if ( $result ) {
				$activated_name = $wpdb->get_row( "SELECT * FROM ctxphc_members WHERE ID = $pend_memb_id", object );

				$activated_result = $activated_name->first_name . ' ' . $activated_name->last_name;
			} else {
				$activated_result = $result;
			}
			break;
		//  End of Activate Pending Member


		/**
		 * ****************************
		 * Delete Pending Member
		 * ****************************
		 */
		case 'delete':
			$report       = 'pending';
			$del_memb_id  = esc_attr( $_GET[ 'member' ] );
			$member_table = 'ctxphc_members';
			$del_where    = array(
				'ID' => $del_memb_id,
			);

			$args = array(
				'table' => $member_table,
				'where' => $del_where,
			);

			$result = md_delete_member( $args );

			if ( $result != 1 ) {
				$delete_result = $result;
			}
			break;
		// End of Delete Pending Member
	}

	$rpt_type = ( ! empty( $_REQUEST[ 'mdview' ] ) ? $_REQUEST[ 'mdview' ] : $report );
	render_members_report( $rpt_type );
}


/**
 * @param $rpt_type
 *
 * @return array
 */
function get_members_report_args( $rpt_type ) {
	switch ( $rpt_type ) {
		case 'all';
			$memb_args = array(
				'title'       => 'All Members',
				'orderby'     => 'last_name',
				'order_dir'   => 'asc',
			);
			break;
		case 'pending';
			$memb_args = array(
				'title'       => 'Pending Members',
				'report_type' => 0,
				'orderby'     => 'last_name',
				'order_dir'   => 'asc',
			);
			break;
		case 'archived';
			$memb_args = array(
				'title'       => 'Archived Members',
				'report_type' => 2,
				'orderby'     => 'last_name',
				'order_dir'   => 'asc',
			);
			break;
		default:
			$memb_args = array(
				'title'       => 'Active Members',
				'report_type' => 1,
				'orderby'     => 'last_name',
				'order_dir'   => 'asc',
			);
			break;
	}

	return $memb_args;
}

/**
 * @param $rpt
 * @param string $search_results
 */
function render_members_report( $rpt, $search_results = '0' ) {
	/** @var wpdb $wpdb */
	global $archived_result, $edit_result, $activated_result, $delete_result, $wpdb;

	$args = get_members_report_args( $rpt );

	$rpt_title   = $args[ 'title' ];
	$rpt_type    = $args[ 'report_type' ];
	$rpt_orderby = $args[ 'orderby' ];
	$rpt_order   = $args[ 'order_dir' ];

	$results = $wpdb->get_results( "SELECT * FROM ctxphc_members WHERE status_id = $rpt_type ORDER BY $rpt_orderby $rpt_order", object );

	$args = array(
		'report_type' => $rpt_type,
		'data'        => $results,
	);

	$memberReport = new CTXPHC_List_Table( $args );
	$memberReport->prepare_items();
	?>
	<div class="wrap">
		<h2>
			<?php
			echo esc_html( $rpt_title );
			if ( current_user_can( 'create_users' ) ) {
				?>
				<a href="?page=new-members.php" class="add-new-h2"><?php echo esc_html_x( 'Add New', 'member' ); ?></a>
			<?php
			}


			if ( $search_results ) {
				printf( '<span class="subtitle">' . __( 'Search results for &#8220;%s&#8221;' ) . '</span>', esc_html( $search_results ) );
			}

			if ( $archived_result ) {
				printf( '<span class="subtitle">' . __( '&#8220;%s&#8221; has been archived.;' ) . '</span>', esc_html( $archived_result ) );
			}

			if ( $edit_result ) {
				printf( '<span class="subtitle">' . __( '&#8220;%s&#8221; has been saved.;' ) . '</span>', esc_html( $edit_result ) );
			}

			if ( $activated_result ) {
				printf( '<span class="subtitle">' . __( '&#8220;%s&#8221; has been activated.;' ) . '</span>', esc_html( $activated_result ) );
			}

			if ( $delete_result ) {
				printf( '<span class="subtitle">' . __( '&#8220;%s&#8221; has been deleted.;' ) . '</span>', esc_html( $delete_result ) );
			}
			?>
		</h2>

		<form method="post"><input type="hidden" name="page" value="member_report">
			<?php
			$memberReport->views();
			$memberReport->search_box( __( 'Search Members' ), 'member' );
			$memberReport->display();
			?>
		</form>
	</div>
<?php
}