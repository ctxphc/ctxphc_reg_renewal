<?php

/**
 * Created by PhpStorm.
 * User: ken_kilgore1
 * Date: 10/27/2014R
 * Time: 7:21 PM
 */

//debug settings
$debug = true;

if ( $debug ) {
	ini_set( 'display_errors', 'off' );
	ini_set( 'xdebug.collect_vars', 'on' );
	ini_set( 'xdebug.collect_params', '4' );
	ini_set( 'xdebug.dump_globals', 'on' );
	ini_set( 'xdebug.dump.SERVER', 'REQUEST_URI' );
	ini_set( 'xdebug.show_local_vars', 'on' );
	error_reporting( E_ALL | E_STRICT );
} else {
	ini_set( 'display_errors', 'off' );
	error_reporting( E_ERROR | E_WARNING | E_PARSE | E_NOTICE );
}


/**
 * ************************************************
 * Get member records from old table
 * ************************************************
 */
function get_memb_records() {
	/** @var wpdb $wpdb */
	global $wpdb;

	//return $wpdb->get_results( "SELECT memb_id, memb_fname, memb_lname, memb_email, memb_phone, memb_occup, memb_bday_day, memb_bday_month, memb_user, memb_type, memb_hatch_date, memb_tag, memb_addr, memb_addr2, memb_city, memb_state, memb_zip FROM  ctxphc_ctxphc_members", object );
	return $wpdb->get_results( "SELECT * FROM ctxphc_members", object );
}


/**
 * ************************************
 * link to class dbMerge if not already loaded
 * ************************************
 */
if ( ! class_exists( 'dbMerge' ) ) {
	require_once( dirname( __FILE__ ) . '/class-dbmerge.php' );
}

//require_once( plugin_dir_path(__FILE__) . '../vender/apache/log4php/src/Logger.php');

// get member records from previous tables
// for use in the dbMerge class
$member_records = get_memb_records();

/**
 * Create new instance of dbMerge class.
 * Pass database arguments.
 */
$moveMembers = new dbMerge( $debug );

?>
<div class="wrap">
	<h2>
		<?php
		echo 'Database Merge';
		?>
	</h2>

	<form method="post">
		<input type="hidden" name="page" value="db_merge">

		<div>
			<ul>
				<?php
				// Convert merge member records object to a single members
				// record object. Pass this object to the dbMerge class
				foreach ( $member_records as $member_record ) {
					$moveMembers->prepare_members( $member_record );
					$moveMembers->display();
				}
				?>
			</ul>
		</div>
	</form>
</div>
