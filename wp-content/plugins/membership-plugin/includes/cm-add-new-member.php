<?php
/**
 * Created by PhpStorm.
 * User: ken_kilgore1
 * Date: 1/2/2015
 * Time: 1:45 PM
 */

require_once TEMPLATEPATH . '/includes/randPassGen.php';

$states_arr       = array(
	'AL' => "Alabama",
	'AK' => "Alaska",
	'AZ' => "Arizona",
	'AR' => "Arkansas",
	'CA' => "California",
	'CO' => "Colorado",
	'CT' => "Connecticut",
	'DE' => "Delaware",
	'DC' => "District Of Columbia",
	'FL' => "Florida",
	'GA' => "Georgia",
	'HI' => "Hawaii",
	'ID' => "Idaho",
	'IL' => "Illinois",
	'IN' => "Indiana",
	'IA' => "Iowa",
	'KS' => "Kansas",
	'KY' => "Kentucky",
	'LA' => "Louisiana",
	'ME' => "Maine",
	'MD' => "Maryland",
	'MA' => "Massachusetts",
	'MI' => "Michigan",
	'MN' => "Minnesota",
	'MS' => "Mississippi",
	'MO' => "Missouri",
	'MT' => "Montana",
	'NE' => "Nebraska",
	'NV' => "Nevada",
	'NH' => "New Hampshire",
	'NJ' => "New Jersey",
	'NM' => "New Mexico",
	'NY' => "New York",
	'NC' => "North Carolina",
	'ND' => "North Dakota",
	'OH' => "Ohio",
	'OK' => "Oklahoma",
	'OR' => "Oregon",
	'PA' => "Pennsylvania",
	'RI' => "Rhode Island",
	'SC' => "South Carolina",
	'SD' => "South Dakota",
	'TN' => "Tennessee",
	'TX' => "Texas",
	'UT' => "Utah",
	'VT' => "Vermont",
	'VA' => "Virginia",
	'WA' => "Washington",
	'WV' => "West Virginia",
	'WI' => "Wisconsin",
	'WY' => "Wyoming"
);
$relationship_arr = array( 'S' => "Spouse", 'P' => "Partner", 'C' => "Child", 'O' => "Other" );

//Change to false for production use
$debug = false;

//Declare Global, local and Array variables before first use
global $wpdb;
$current_date = date( "m/d/Y" );
$wpdb->show_errors();

$renewal_date = get_renewal_date();

date_default_timezone_set( 'America/Chicago' );

//Define and assign default values to Variables

/** @var STRING $relationship_table */
$relationship_table   = 'ctxphc_member_relationships';
$relationship_options = $wpdb->get_results( "SELECT * FROM $relationship_table" );

/** @var STRING $membership_table */
$membership_table   = 'ctxphc_membership_types';
$membership_options = $wpdb->get_results( "SELECT * FROM $membership_table" );

/** @var STRING $ctxphc_status */
$status_table   = 'ctxphc_member_status';
$status_options = $wpdb->get_results( "SELECT * FROM $status_table" );

/** @var STRING $reg_table */
$reg_table = 'ctxphc_pending_registrations';

function get_membership_registration_form() {
	//Start output buffering
	ob_start();

	// Call action hook that builds the registration page
	do_action( 'membership_registration' );


}


function membership_registration() {

	get_membership_type_options();

	get_primary_members_info();

	get_spouses_info();

	get_childrens_options();
}

function get_membership_type_options(){ ?>
<table class=''>
	<tr>
		<td><input class="memb_type" id="memb_type_1" type="radio" name="memb_type" value="1"
		           <?php if ($memb_type_id === 1) { ?>checked<?php }; ?>></td>
		<td><label for="memb_type_1">Individual - $<?php echo $memb_costs[ 1 ]->cost; ?></label></td>
		<td><input class="memb_type" id="memb_type_2" type="radio" name="memb_type" value="2"
		       <?php if ($memb_type_id === 2) { ?>checked<?php }; ?>></td>
		<td><label for="memb_type_2">Individual + Children -
			$<?php echo $memb_costs[ 2 ]->cost; ?></label></td>
		<td><input class="memb_type" id="memb_type_3" type="radio" name="memb_type" value="3"
		       <?php if ($memb_type_id === 3) { ?>checked<?php }; ?>></td>
		<td><label for="memb_type_3">Couple - $<?php echo $memb_costs[ 3 ]->cost; ?></label></td>
		<td><input class="memb_type" id="memb_type_4" type="radio" name="memb_type" value="4"
		       <?php if ($memb_type_id === 4) { ?>checked<?php }; ?>></td>
		<td><label for="memb_type_4">Household - $<?php echo $memb_costs[ 4 ]->cost; ?></label></td>
	</tr>
</table>
<?php
}