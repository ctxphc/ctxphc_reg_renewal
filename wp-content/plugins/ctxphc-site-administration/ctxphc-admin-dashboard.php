<?php
/** Load WordPress Bootstrap */
require_once('./admin.php');

/** Load CTXPHC dashboard API */
require_once(ABSPATH . 'wp-admin/includes/dashboard.php');

ctxphc_dashboard_setup();

wp_enqueue_script( 'dashboard' );

add_thickbox();

if ( wp_is_mobile() ) { wp_enqueue_script( 'jquery-touch-punch' ); }

$title = __( 'Central Texas Parrot Head Club Dashboard' );
$parent_file = 'ctxphc-index.php';

if ( is_user_admin() ) {
	add_screen_option( 'layout_columns', array( 'max' => 4, 'default' => 2 ) );
} else {
	add_screen_option( 'layout_columns', array( 'max' => 4, 'default' => 2 ) );
}

$today = current_time( 'mysql', 1 );
/**
 *
 */
function ctxphc_dashboard_setup() {
	if ( current_user_can( 'manage_options' ) ) {

		// Current Membership Widget
		wp_add_dashboard_widget( 'ctxphc_dashboard_current_members',
								__( 'Current Membership Counts' ),
								'ctxphc_dashboard_current_membership' );

		// Birthdays this month Widget
		wp_add_dashboard_widget( 'ctxphc_dashboard_member_bdays',
								__( 'Members With Birthdays in:' ),
								'ctxphc_dashboard_birthdays_next_month' );

		// Parrot Points dashboard widget
		/**
		 * wp_add_dashboard_widget( 'dashboard_top_10_parrot_points_now',
		 * 							__( "Top 10 Parrot Point's" ),
		 * 							'ctxphc_dashboard_top_10_parrot_points' );
		 **/
	}
}

function ctxphc_dashboard() {
	$screen = get_current_screen();
	$class = 'columns-2';

	echo '<div id="dashboard-widgets" class="metabox-holder ' . sanitize_text_field( $class ) . '">';
	echo '<div id="postbox-container-1" class="postbox-container">';

	do_meta_boxes( $screen->id, 'normal', '' );

	echo '</div><div id="postbox-container-2" class="postbox-container">';

	do_meta_boxes( $screen->id, 'side', '' );

	echo '</div></div>';

	wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false );
	wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false );
}

/**
 *
 */
function ctxphc_dashboard_current_membership() {
	//include_once( 'ctxphc_memb_tables.php' );
	include_once( 'includes/ctxphc-membership-functions.php' );

	$status_id = 1; //active members only.
	$indiv_members = 1; //Only Individual Members
	$indiv_and_children = 2; //Only Individuals and children
	$couples = 3; //Couples and Partners only
	$households = 4; //Household/Families only
	$act_indiv			= count_member( $indiv_members, $status_id );
	$act_indiv_child	= count_member( $indiv_and_children, $status_id );
	$act_couple			= count_member( $couples, $status_id );
	$act_family			= count_member( $households, $status_id );
	$act_members		= absint( $act_indiv + $act_indiv_child + $act_couple + $act_family );

	// Single Members
	display_member_counts( $act_indiv, $act_indiv_child, $act_couple,$act_family, $act_members );
}

/**
 *
 */
function ctxphc_dashboard_birthdays_next_month() {

	//include_once( 'ctxphc_memb_tables.php' );
	include_once( 'includes/ctxphc-membership-functions.php' );

	//Get next months name
	$curr_date = new DateTime( date( 'Y-m-d' ) );
	//error_log("!!!!!!  curr_date is:  $curr_date->format( 'Y-m-d' )  !!!!!!!");

	$next_month = $curr_date->modify( 'first day of next month' );
	//error_log("!!!!!!  Now curr_date is:  $curr_date->format( 'F' )  !!!!!!!");

	$month_name1 = $next_month->format( 'F' );
	//error_log("!!!!!!  The Month to display is:  $bday_month_1  !!!!!!!");
	$month_after_next = $next_month->modify( 'first day of next month' );
	//error_log("!!!!!!  Now curr_date is:  $curr_date->format( 'F' )  !!!!!!!");

	$month_name2 = $month_after_next->format( 'F' );
	//error_log("!!!!!!  The Month to display is:  $bday_month_2  !!!!!!!");

	$next_months_bdays = get_members_birthdays( 1 );
	If ( is_array( $next_months_bdays ) ){
		display_birthdays( $next_months_bdays, $month_name1 );
	} else {
		display_birthday_error( $month_name1 );
	}

	//get members who have birthdays next month.
	$next_months_bdays = get_members_birthdays( 2 );
	If ( is_array( $next_months_bdays ) ) {
		display_birthdays( $next_months_bdays, $month_name2 );
	} else {
		display_birthday_error( $month_name2 );
	}
}


//Initialize CTXPHC Dashboard
//ctxphc_dashboard_setup();

//wp_enqueue_script( 'dashboard' );  ?>

<div class="wrap">
	<h2><?php echo esc_html( $title ); ?></h2>
	<div id="dashbaord-widgets-wrap">
	<?php ctxphc_dashboard(); ?>
	<div class="clear"></div>
	</div><!-- dashboard-widgets-wrap -->
</div><!-- wrap -->

<?php //require(ABSPATH . 'wp-admin/admin-footer.php'); ?>