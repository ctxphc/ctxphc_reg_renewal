<?php
/**
 * Created by PhpStorm.
 * User: ken_kilgore1
 * Date: 12/30/2014
 * Time: 2:17 PM
 */

function display_report( $rpt_title ) {
	global $archived_result, $edit_result,$search_results, $activated_result,$delete_result, $memberReport;
	?>

	<div class="wrap">
		<h2>
			<?php
			echo esc_html( $rpt_title );
			if ( current_user_can( 'create_users' ) ) {
				?>
				<a href="?page=member-new.php" class="add-new-h2"><?php echo esc_html_x( 'Add New', 'member' ); ?></a>
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