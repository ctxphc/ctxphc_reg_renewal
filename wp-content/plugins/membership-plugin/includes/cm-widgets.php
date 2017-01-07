<?php

require_once( dirname( __FILE__ ) . '/functions.php' );

// Admin footer modification
function remove_footer_admin() {
	echo '<span id="footer-thankyou">Developed by <a href="http://www.kaptkaos.com" target="_blank">Kapt Kaos</a></span>';
}

add_filter( 'admin_footer_text', 'remove_footer_admin' );

function mp_membership_dashboard_widgets() {
	$date_format    = 'F';
	$safe_next_month = sanitize_text_field( get_next_months_name( $date_format ) );

	wp_add_dashboard_widget( 'mp_membership_status', 'Membership Status', 'mp_membership_status_widget' );
	wp_add_dashboard_widget( 'mp_next_months_birthdays', 'Birthdays in ' . $safe_next_month, 'mp_next_months_birthdays' );
	wp_add_dashboard_widget( 'mp_newest_members', 'Newest Members', 'mp_newest_members' );
}
add_action( 'wp_dashboard_setup', 'mp_membership_dashboard_widgets' );

function mp_membership_status_widget() {
	require_once( dirname( __FILE__ ) . 'includes/cm-membership-count-widget.php' );
}

function mp_membership_reports() {
	require_once( dirname( __FILE__ ) . 'includes/cm-membership-reports-widget.php' );
}

function mp_newest_members() {
	require_once( dirname( __FILE__ ) . 'includes/cm-newest-members-widget.php' );
} // end add Newest Members widget

function mp_next_months_birthdays() {
	require_once( dirname( __FILE__ ) . 'includes/cm-next-months-birthdays-widget.php' );
}