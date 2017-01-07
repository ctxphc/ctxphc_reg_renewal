<?php
/**
 * Created by PhpStorm.
 * User: ken_kilgore1
 * Date: 1/2/2015
 * Time: 1:45 PM
 */

require_once TEMPLATEPATH.'/includes/randPassGen.php';

$states_arr = array('AL'=>"Alabama",'AK'=>"Alaska",'AZ'=>"Arizona",'AR'=>"Arkansas",'CA'=>"California",'CO'=>"Colorado",'CT'=>"Connecticut",'DE'=>"Delaware",'DC'=>"District Of Columbia",'FL'=>"Florida",'GA'=>"Georgia",'HI'=>"Hawaii",'ID'=>"Idaho",'IL'=>"Illinois",'IN'=>"Indiana",'IA'=>"Iowa",'KS'=>"Kansas",'KY'=>"Kentucky",'LA'=>"Louisiana",'ME'=>"Maine",'MD'=>"Maryland",'MA'=>"Massachusetts",'MI'=>"Michigan",'MN'=>"Minnesota",'MS'=>"Mississippi",'MO'=>"Missouri",'MT'=>"Montana",'NE'=>"Nebraska",'NV'=>"Nevada",'NH'=>"New Hampshire",'NJ'=>"New Jersey",'NM'=>"New Mexico",'NY'=>"New York",'NC'=>"North Carolina",'ND'=>"North Dakota",'OH'=>"Ohio",'OK'=>"Oklahoma",'OR'=>"Oregon",'PA'=>"Pennsylvania",'RI'=>"Rhode Island",'SC'=>"South Carolina",'SD'=>"South Dakota",'TN'=>"Tennessee",'TX'=>"Texas",'UT'=>"Utah",'VT'=>"Vermont",'VA'=>"Virginia",'WA'=>"Washington",'WV'=>"West Virginia",'WI'=>"Wisconsin",'WY'=>"Wyoming");
$relationship_arr = array('S'=>"Spouse",'P'=>"Partner",'C'=>"Child",'O'=>"Other");

//Change to false for production use
$debug = FALSE;

//Declare Global, local and Array variables before first use
global $wpdb;
$current_date = date("m/d/Y");
$wpdb->show_errors();

$renewal_date = get_renewal_date();

date_default_timezone_set('America/Chicago');

//Define and assign default values to Variables
/** @var STRING $address_table */
$address_table = 'ctxphc_member_addresses';

/** @var STRING $members_table */
$members_table = 'ctxphc_members';

/** @var STRING $relationship_table */
$relationship_table = 'ctxphc_member_relationships';

/** @var STRING $membership_table */
$membership_table = 'ctxphc_membership_types';

/** @var STRING $ctxphc_status */
$status_table = 'ctxphc_member_status';

/** @var STRING $reg_table */
$reg_table = 'ctxphc_pending_registrations';

/** @var STRING $memb_type */
$mb_rel_id = $wpdb->get_var("SELECT ID FROM $relationship_table WHERE relationship_type = 'M'");
//if ( $debug ) {$wpdb->print_error();}

$status_id = $wpdb->get_var("SELECT ID FROM $status_table WHERE memb_status = 'Pending'");
// if ( $debug ) {$wpdb->print_error();}

if($_SERVER['REQUEST_METHOD'] === "POST") {
	$message = "Inside server request method POST";
	debug_log_message( $message, $debug );
}

if ( $_POST[ 'reg_submit' ] === 'Submit' ) {

}

