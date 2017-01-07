<?php

/**
 * Created by PhpStorm.
 * User: ken_kilgore1
 * Date: 12/11/2014
 * Time: 10:27 AM
 */
class Membership_Bulk_Actions
{

	public function __construct()
	{

		if ( is_admin() ) {
			// admin actions/filters
			//add_action('admin_footer-edit.php', array(&$this, 'custom_bulk_admin_footer'));
			add_action( 'cm-membership-reports.php', array( &$this, 'membership_process_bulk_action' ) );
			add_action( 'admin_notices', array( &$this, 'membership_bulk_admin_notices' ) );
		}
	}


	/**
	 * Step 2: handle the custom Bulk Action
	 *
	 * Based on the post http://wordpress.stackexchange.com/questions/29822/custom-bulk-action
	 */
	function membership_process_bulk_action()
	{
		global $typenow;
		$post_type = $typenow;



			// get the action
			$memb_rtp_list_table = _get_list_table( 'CTXPHC_List_Table' );  // depending on your resource type this could be WP_Users_List_Table, WP_Comments_List_Table, etc
			$action = $memb_rtp_list_table->current_action();

			$allowed_actions = array( "activate", "archive", "delete" );
			if ( ! in_array( $action, $allowed_actions ) ) return;

			// security check
			check_admin_referer( 'bulk-posts' );

			// make sure ids are submitted.  depending on the resource type, this may be 'media' or 'ids'
			if ( isset( $_REQUEST[ 'ID' ] ) ) {
				$member_ids = array_map( 'intval', $_REQUEST[ 'ID' ] );
			}

			if ( empty( $member_ids ) ) return;

			// this is based on wp-admin/edit.php
			$sendback = remove_query_arg( array( 'activated', 'untrashed', 'deleted', 'ids' ), wp_get_referer() );
			if ( ! $sendback )
				$sendback = admin_url( ".php?post_type=$post_type" );

			$pagenum = $memb_rtp_list_table->get_pagenum();
			$sendback = add_query_arg( 'paged', $pagenum, $sendback );

			switch ( $action ) {
				case 'activate':

					// if we set up user permissions/capabilities, the code might look like:
					//if ( !current_user_can($post_type_object->cap->export_post, $post_id) )
					//	wp_die( __('You are not allowed to export this post.') );

					$activated = 0;
					foreach ( $member_ids as $member_id ) {

						$args = array(
							'ID' => $member_id,
							'table' => 'ctxphc_members',
						);

						if ( ! $this->perform_activate_pending( $args ) )
							wp_die( __( 'Error exporting post.' ) );

						$activated++;
					}

					$sendback = add_query_arg( array( 'activated' => $activated, 'ids' => join( ',', $member_ids ) ), $sendback );
					break;

				case 'archive':

					// if we set up user permissions/capabilities, the code might look like:
					//if ( !current_user_can($post_type_object->cap->export_post, $post_id) )
					//	wp_die( __('You are not allowed to export this post.') );

					$archived = 0;
					foreach ( $member_ids as $member_id ) {

						if ( ! $this->perform_archive_member( $member_id ) )
							wp_die( __( 'Error exporting post.' ) );

						$archived++;
					}

					$sendback = add_query_arg( array( 'archived' => $archived, 'ids' => join( ',', $member_ids ) ), $sendback );
					break;

				case 'delete':

					// if we set up user permissions/capabilities, the code might look like:
					//if ( !current_user_can($post_type_object->cap->export_post, $post_id) )
					//	wp_die( __('You are not allowed to export this post.') );

					$deleted = 0;
					foreach ( $member_ids as $member_id ) {

						if ( ! $this->preform_delete_pending( $member_id ) ) {
							wp_die( __( 'Error deleting member!!' ) );
						}
						$deleted++;
					}

					$sendback = add_query_arg( array( 'deleted' => $deleted, 'ids' => join( ',', $member_ids ) ), $sendback );
					break;

				default:
					return;
			}

			$sendback = remove_query_arg( array( 'action', 'action2', 'tags_input', 'post_author', 'comment_status', 'ping_status', '_status', 'post', 'bulk_edit', 'post_view' ), $sendback );

			wp_redirect( $sendback );
			exit();
		}



	/**
	 * Step 3: display an admin notice on the Membership Reports page after processing
	 * the bulk action.
	 */
	function membership_bulk_admin_notices()
	{
		global $pagenow;

		if ( $pagenow == 'membership_report.php' && isset( $_REQUEST[ 'activated' ] ) && (int)$_REQUEST[ 'activated' ] ) {
			$message = sprintf( _n( 'Member activated.', '%s Members activated.', $_REQUEST[ 'activated' ] ), number_format_i18n( $_REQUEST[ 'activated' ] ) );
			echo "<div class=\"updated\"><p>{$message}</p></div>";
		} else if ( $pagenow == 'membership_report.php' && isset( $_REQUEST[ 'archived' ] ) && (int)$_REQUEST[ 'archived' ] ) {
			$message = sprintf( _n( 'Member archived.', '%s Members archived.', $_REQUEST[ 'archived' ] ), number_format_i18n( $_REQUEST[ 'archived' ] ) );
			echo "<div class=\"updated\"><p>{$message}</p></div>";
		} else if ( $pagenow == 'membership_report.php' && isset( $_REQUEST[ 'deleted' ] ) && (int)$_REQUEST[ 'deleted' ] ) {
			$message = sprintf( _n( 'Member deleted.', '%s Members deleted.', $_REQUEST[ 'deleted' ] ), number_format_i18n( $_REQUEST[ 'deleted' ] ) );
			echo "<div class=\"updated\"><p>{$message}</p></div>";
		}
	}

	function perform_activate_pending( $args )
	{
		/** @var wpdb $wpdb */
		global $wpdb;

		$table = $args['table'];
		$data = array( 'status_id' => 1 );
		$where = array( 'ID' => $args['ID'] );

		//$wpdb->show_errors();
		$update_results = $wpdb->update( $table, $data, $where );

		//$wpdb->print_error();
		if ( ! $update_results ){
			wp_die( __('Error activating member!') );
		}
		return true;
	}

	function perform_archive_member( $args ){
		/** @var wpdb $wpdb */
		global $wpdb;

		$table = $args['table'];
		$data = array( 'status_id' => 2 );
		$where = array( 'ID' => $args['member_id'] );

		//$wpdb->show_errors();
		$update_results = $wpdb->update( $table, $data, $where );

		//$wpdb->print_error();
		//return $update_results;
		if ( ! $update_results ){
			wp_die( __('Error archiving member!') );
		}
		return true;
	}

	function preform_delete_pending( $args ){
		/** @var wpdb $wpdb */
		global $wpdb;

		$table = $args['table'];
		$where = $args['where'];

		//$wpdb->show_errors();
		$delete_results = $wpdb->delete( $table, $where );

		//$wpdb->print_error();
		//return $update_results;
		if ( ! $delete_results ){
			wp_die( __('Error deleting member!') );
		}
		return true;
	}

	function perform_edit_member( $args )
	{
		/** @var wpdb $wpdb */
		global $wpdb;

		$existing_member_data = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM %s WHERE ID = %d", $args[ 'table' ], $args['ID'] ) );

		if ( $existing_member_data ) {
			//display edit form with existing data in-place.
		}


		return 'result of edit';
	}

}  // Class End

new Membership_Bulk_Actions();