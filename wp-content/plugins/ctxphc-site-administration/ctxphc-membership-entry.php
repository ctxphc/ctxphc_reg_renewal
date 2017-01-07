<?php
/*
 * CTXPHC Manual Registration Entry
 * To be used by CTXPHC BOD or Support only.
*/
?>
<?php require_once TEMPLATEPATH.'/includes/randPassGen.php' ?>
<?php
$states_arr = array('AL'=>"Alabama",'AK'=>"Alaska",'AZ'=>"Arizona",'AR'=>"Arkansas",'CA'=>"California",'CO'=>"Colorado",'CT'=>"Connecticut",'DE'=>"Delaware",'DC'=>"District Of Columbia",'FL'=>"Florida",'GA'=>"Georgia",'HI'=>"Hawaii",'ID'=>"Idaho",'IL'=>"Illinois",'IN'=>"Indiana",'IA'=>"Iowa",'KS'=>"Kansas",'KY'=>"Kentucky",'LA'=>"Louisiana",'ME'=>"Maine",'MD'=>"Maryland",'MA'=>"Massachusetts",'MI'=>"Michigan",'MN'=>"Minnesota",'MS'=>"Mississippi",'MO'=>"Missouri",'MT'=>"Montana",'NE'=>"Nebraska",'NV'=>"Nevada",'NH'=>"New Hampshire",'NJ'=>"New Jersey",'NM'=>"New Mexico",'NY'=>"New York",'NC'=>"North Carolina",'ND'=>"North Dakota",'OH'=>"Ohio",'OK'=>"Oklahoma",'OR'=>"Oregon",'PA'=>"Pennsylvania",'RI'=>"Rhode Island",'SC'=>"South Carolina",'SD'=>"South Dakota",'TN'=>"Tennessee",'TX'=>"Texas",'UT'=>"Utah",'VT'=>"Vermont",'VA'=>"Virginia",'WA'=>"Washington",'WV'=>"West Virginia",'WI'=>"Wisconsin",'WY'=>"Wyoming");
$relationship_arr = array('S'=>"Spouse",'P'=>"Partner",'C'=>"Child",'O'=>"Other");

//Change to false for production use
$debug = TRUE;

//Declare Global, local and Array varables before first use
global $wpdb, $membID;

$memb_table = 'ctxphc_members';
$family_table = 'ctxphc_members_family';

date_default_timezone_set('America/Chicago');
$current_date = date('Y/m/d h:i:s');
?>

<script>
   jQuery(document).ready(function(){
      jQuery("#regForm").validationEngine('attach', {promptPosition : "centerRight"});
   });
</script>

<?php
/******************************************************************************************************/
function ctxphc_get_membership_types()
{
	$membership_types = array('ID','IC','CO','HH');

	return $givingLevels;
}

/******************************************************************************************************/
function ctxphc_membership_queue_stylesheet() {
	$styleurl = plugins_url('/css/styles.css', __FILE__);
	wp_register_style('ctxphc_membership_css', $styleurl);
	wp_enqueue_style('ctxphc_membership_css');
}
add_action('wp_enqueue_scripts', 'ctxphc_membership_queue_stylesheet');

/**********************************************************************************************************/
function ctxphc_membership_queue_admin_stylesheet() {
        $styleurl = plugins_url('/css/adminstyles.css', __FILE__);

        wp_register_style('ctxphc_membership_admin_css', $styleurl);
        wp_enqueue_style('ctxphc_membership_admin_css');
}
add_action('admin_print_styles', 'ctxphc_membership_queue_admin_stylesheet');

/******************************************************************************************************/
function ctxphc_membership_queue_scripts() {
	$load_in_footer = ( 'true' == get_option( 'ctxphc_membership_scripts_in_footer' ) );
	wp_enqueue_script( 'jquery' );
	$script_url = plugins_url( '/js/script.js', __FILE__ ); 
	wp_enqueue_script( 'ctxphc_membership_script', $script_url, array( 'jquery' ), false, $load_in_footer );

	$script_url = plugins_url( '/js/geo-selects.js', __FILE__ ); 
	wp_enqueue_script( 'ctxphc_membership_geo_selects_script', $script_url, array( 'jquery' ), false, $load_in_footer );

	// declare the URL to the file that handles the AJAX request (wp-admin/admin-ajax.php)
	wp_localize_script( 'ctxphc_membership_script', 'dgxDonateAjax',
		array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'nonce' => wp_create_nonce( 'dgx-donate-nonce' ),
			'postalCodeRequired' => ctxphc_membership_get_countries_requiring_postal_code()
		)
	);
}
add_action( 'wp_enqueue_scripts', 'ctxphc_membership_queue_scripts' );

/******************************************************************************************************/
function ctxphc_display_thank_you()
{
	$output = "<p>";
	$thankYouText = get_option('ctxphc_thanks_text');
	$thankYouText = nl2br($thankYouText);
	$output .= $thankYouText;
	$output .= "</p>";

	return $output;
}

if ( isset( $_POST['submit'] ) ) {
	extract($_POST,EXTR_PREFIX_ALL , "submit");
	if ( $submit_contact != 'Y') {
		$submit_contact = 'N';
	}

	switch ( $submit_memb_type ) {
		case "ID";
		    $memb_cost = "25.00";
		    break;
		case "IC";
		    $memb_cost = "30.00";
		    $process_child = TRUE;
		    break;
		case "CO";
		    $memb_cost = "40.00";
		    $process_spouse = TRUE;
		    break;
		case "HH";
		    $memb_cost = "45.00";
		    $process_spouse = TRUE;
		    $process_child = TRUE;
		    break;
		}

	//Load arrays for use in adding or updating records in database
	$member_data = array(
	    'first_name'		=> ucwords( strtolower( $submit_first_name ) ),
		'last_name'		=> ucwords( strtolower( $submit_last_name ) ),
		'email'			=> strtolower( $submit_email ),
		'phone'			=> $submit_phone1 . '-' . $submit_phone2 . '-' . $submit_phone3,
		'bday_month'		=> str_pad( $submit_month, 2, '0',STR_PAD_LEFT ),
		'bday_day'		=> str_pad( $submit_day, 2, '0',STR_PAD_LEFT ),
		'occupation'		=> ucwords( strtolower( $submit_occu ) ),
		'addr1'			=> ucwords( strtolower( $submit_addr1 ) ),
		'addr2'			=> ucwords( strtolower( $submit_addr2 ) ),
		'city'			=> ucwords( strtolower( $submit_city ) ),
		'state'			=> $submit_state,
		'zip'			=> $submit_zip,
		'membership_type'	=> $submit_memb_type,
		'contact'			=> $submit_contact,
		'reg_date'		=> $current_date,
	    );
    
	if ( $submit_membID ) {
		$update = TRUE;
		$member_data['ID'] = $membID = $submit_membID;
		$memb_update_results = $wpdb->update( $memb_table, $member_data, $where );
		if ( $memb_update_result ){
			//do something if this isn't a valid update.
		}
	} else {
		$update = FALSE;
		$memb_insert_results = $wpdb->insert( $memb_table, $member_data );
		if ( $memb_insert_result ){
			//do something if this isn't a valid insert.
		}
	}
    
	if ( $process_spouse === TRUE ){
		$spouse_data = array(
			'first_name'	=> ucwords( strtolower( $submit_sp_first_name ) ),
			'last_name'	=> ucwords( strtolower( $submit_sp_last_name ) ),
			'email'		=> strtolower( $submit_sp_email ),
			'phone'		=> $submit_sp_phone1 . '-' . $submit_sp_phone2 . '-' . $submit_sp_phone3,
			'bday_month'	=> str_pad( $submit_sp_month, 2, '0',STR_PAD_LEFT ),
			'bday_day'	=> str_pad( $submit_sp_day, 2, '0',STR_PAD_LEFT ),
			'relationship'	=> $submit_sp_relationship,
			);

		if ( $submit_spouseID ) {
			$where = array( 'memb_id' => $membID );
			$spouse_data['id'] = $submit_spouseID;
			$spouse_update_results = $wpdb->update( $family_table, $spouse_data, $where );
			if ( $spouse_update_results ){
				//do something if this isn't a valid update.
			}
		} else {
			$spouse_insert_results = $wpdb->insert( $family_table, $spouse_data );
			if ( $spouse_insert_result ){
				//do something if this isn't a valid insert.
			}
		}
	}


	if ( $process_child === TRUE ){
		 $child1_data = array(
			 'first_name'	=> ucwords( strtolower( $submit_c1_first_name ) ),
			 'last_name'	=> ucwords( strtolower( $submit_c1_last_name ) ),
			 'email'		=> $submit_c1_email,
			 'bday_month'	=> str_pad( $submit_c1_month, 2, '0',STR_PAD_LEFT ),
			 'bday_day'	=> str_pad( $submit_c1_day, 2, '0',STR_PAD_LEFT ),
			 'relationship'	=> $submit_c1_relationship,
			 );

		 if ( $submit_child1ID ) {
			 $where = array( 'memb_id' => $membID );
			 $child1_data['id'] = $submit_child1ID;
			 $family_update_results = $wpdb->update( $family_table, $child1_data, $where );
			 if ( $family_update_results ){
				 //do something if this isn't a valid update.
			 }
		 } else {
			 $family_insert_results = $wpdb->insert( $family_table, $child1_data );
			 if ( $family_insert_results ){
				 //do something if this isn't a valid insert.
			 }
		 }

		 $child2_data = array(
			 'first_name'	=> ucwords( strtolower( $submit_c2_first_name ) ),
			 'last_name'	=> ucwords( strtolower( $submit_c2_last_name ) ),
			 'email'		=> $submit_c2_email,
			 'bday_month'	=> str_pad( $submit_c2_month, 2, '0',STR_PAD_LEFT ),
			 'bday_day'	=> str_pad( $submit_c2_day, 2, '0',STR_PAD_LEFT ),
			 'relationship'	=> $submit_c2_relationship,
			 );

		 if ( $submit_child2ID ) {
			 $where = array( 'memb_id' => $membID );
			 $child2_data['id'] = $submit_child2ID;
			 $family_update_results = $wpdb->update( $family_table, $child2_data, $where );
			 if ( $family_update_results ){
				 //do something if this isn't a valid update.
			 }
		 } else {
			 $family_insert_results = $wpdb->insert( $family_table, $child2_data );
			 if ( $family_insert_results ){
				 //do something if this isn't a valid insert.
			 }
		 }

		 $child3_data = array(
			 'first_name'	=> ucwords( strtolower( $submit_c3_first_name ) ),
			 'last_name'	=> ucwords( strtolower( $submit_c3_last_name ) ),
			 'email'		=> $submit_c3_email,
			 'bday_month'	=> str_pad( $submit_c3_month, 2, '0',STR_PAD_LEFT ),
			 'bday_day'	=> str_pad( $submit_c3_day, 2, '0',STR_PAD_LEFT ),
			 'relationship'	=> $submit_c3_relationship
			 );

		 if ( $submit_child3ID ) {
			 $where = array( 'memb_id' => $membID );
			 $child3_data['id'] = $submit_child3ID;
			 $family_update_results = $wpdb->update( $family_table, $child3_data, $where );
			 if ( $family_update_results ){
				 //do something if this isn't a valid update.
			 }
		 } else {
			 $family_insert_results = $wpdb->insert( $family_table, $child3_data );
			 if ( $family_insert_results ){
				 //do something if this isn't a valid insert.
			 }
		 }
	}

	//Load Member data for display in member entry page.
	$first_name	= ucwords( strtolower( $member_data['first_name'] ) );
	$last_name	= ucwords( strtolower( $member_data['last_name'] ) );
	$email		= strtolower( $member_data['email'] );
	$phone1		= $member_data['phone1'];
	$phone2		= $member_data['phone2'];
	$phone3		= $member_data['phone3'];
	$phone		= $phone1 . '-' . $phone2 . '-' . $phone3;
	$month		= str_pad( $member_data['month'], 2, '0',STR_PAD_LEFT );
	$day			= str_pad( $member_data['day'], 2, '0',STR_PAD_LEFT );
	$occu		= ucwords( strtolower( $member_data['occu'] ) );
	$addr1		= ucwords( strtolower( $member_data['addr1'] ) );
	$addr2		= ucwords( strtolower( $member_data['addr2'] ) );
	$city		= ucwords( strtolower( $member_data['city'] ) );
	$state		= $member_data['state'];
	$zip			= $member_data['zip'];
	$username		= strtolower( substr($member_data['first_name'], 0, 3) . substr($member_data['last_name'], 0, 4) );
	$testPass		= new RandomPasswordGenerator();
	$pass		= $testPass->useNumbers(2)->generatePassword(8);

	if ($member_data['contact'] != "Y") { $contact = "N"; } else { $contact = "Y"; }

	//Load Spouse/Partner/Other data for display in member entry page.
	if ( $process_spouse === true || $process_family === true ) {
		if ( $update === true ) { $spouseID = $spouse_data['id']; }
		$sp_first_name = ucwords( strtolower( $spouse_data['first_name'] ) );
		$sp_last_name	= ucwords( strtolower( $spouse_data['last_name'] ) );
		$sp_email		= strtolower( $spouse_data['email'] );
		$sp_phone1	= $spouse_data['phone1'];
		$sp_phone2	= $spouse_data['phone2'];
		$sp_phone3	= $spouse_data['phone3'];
		$sp_phone		= $sp_phone1 . '-' . $sp_phone2 . '-' . $sp_phone3;
		$sp_month		= str_pad( $spouse_data['month'], 2, '0',STR_PAD_LEFT );
		$sp_day		= str_pad( $spouse_data['day'], 2, '0',STR_PAD_LEFT );
		$sp_rel		= $spouse_data['relationship'];
		$sp_username	= strtolower( substr($sp_first_name, 0, 3) . substr($sp_last_name, 0, 4) );
		$testPass		= new RandomPasswordGenerator();
		$sp_pass		= $testPass->useNumbers(2)->generatePassword(8);
	}

	if ( $memType === 'IC' || $memType === 'HH' ) {
		// 1st family member info
		if ( $update === TRUE ) { $child1ID = $child1_data['id']; }
		$c1_first_name	= ucwords( strtolower( $child1_data['first_name'] ) );
		$c1_last_name	= ucwords( strtolower( $child1_data['last_name'] ) );
		$c1_rel		= $child1_data['relationship'];
		$c1_month		= str_pad( $child1_data['month'], 2, '0',STR_PAD_LEFT );
		$c1_day		= str_pad( $child1_data['day'], 2, '0',STR_PAD_LEFT );
		$c1_email		= $child1_data['email'];

		// 2nd family member info
		if ( $update === TRUE ) { $child2ID = $child2_data['id']; }
		$c2_first_name	= ucwords( strtolower( $child2_data['first_name'] ) );
		$c2_last_name	= ucwords( strtolower( $child2_data['last_name'] ) );
		$c2_rel		= $child2_data['relationship'];
		$c2_month		= str_pad( $child2_data['month'], 2, '0',STR_PAD_LEFT );
		$c2_day		= str_pad( $child2_data['day'], 2, '0',STR_PAD_LEFT );
		$c2_email		= $child2_data['email'];

		// 3rd family member info
		if ( $update === TRUE ) { $child3ID = $child3_data['id']; }
		$c3_first_name	= ucwords( strtolower( $child3_data['first_name'] ) );
		$c3_last_name	= ucwords( strtolower( $child3_data['last_name'] ) );
		$c3_rel		= $child3_data['relationship'];
		$c3_month		= str_pad( $child3_data['month'], 2, '0',STR_PAD_LEFT );
		$c3_day		= str_pad( $child3_data['day'], 2, '0',STR_PAD_LEFT );
		$c3_email		= $child3_data['email'];
	}
}



/** WordPress Administration Bootstrap */
require_once( ABSPATH . 'wp-load.php' );
require_once( ABSPATH . 'wp-admin/admin.php' );
require_once( ABSPATH . 'wp-admin/admin-header.php' );
?>
<div class="wrap">
    <?php screen_icon(); ?>
<div class="wrap about-wrap">
    <h2><?php _e( "CTXPHC Membership Registration Manuel Entry" ); ?></h2>
    <h2 class="nav-tab-wrapper">
	<a href="<?php plugins_url( 'ctxphc-membership-entry.php', __FILE__ ) ?>" class="nav-tab nav-tab-active">
	    <?php _e( 'Manual Entry' ); ?>
	</a>
    </h2>

    <div class="changelog">
	<h3><?php _e( "Membership Manual Entry" ); ?></h3>

	<div class="spacer"></div>

	<form id="manRegForm" name="manRegForm" method="post" action="">
	    <input type=hidden <?php if (isset( $membID ) ) echo 'value=' . $membID; ?> name=membID>
	    <input type=hidden <?php if (isset( $spouseID ) ) echo 'value=' . $spouseID; ?> name=spID>
	    <input type=hidden <?php if (isset( $child1ID ) ) echo 'value=' . $child1ID; ?> name=child1ID>
	    <input type=hidden <?php if (isset( $child2ID ) ) echo 'value=' . $child2ID; ?> name=child2ID>
	    <input type=hidden <?php if (isset( $child3ID ) ) echo 'value=' . $child3ID; ?> name=child3ID>
	    <input type=hidden <?php if (isset( $username ) ) echo 'value=' . $username; ?> name=username>
	    <input type=hidden <?php if (isset( $pass ) ) echo 'value=' . $pass; ?> name=pass>
	    <input type=hidden <?php if (isset( $sp_username ) ) echo 'value=' . $sp_username; ?> name=sp_username>
	    <input type=hidden <?php if (isset( $sp_pass ) ) echo 'value=' . $sp_pass; ?> name=sp_pass>

	    <fieldset class="manRegForm" id="memb_type">
		<legend>Membership Options</legend>
		<div id="memb_type">
			<input class="memType" id=memb_type_1 type="radio" name="memb_type" value="ID" <?php if($memType === 'ID') { ?>checked<?php }; ?>>
			<label for=memb_type_1>Individual $25</label>
			<input class="memType" id=memb_type_1 type="radio" name="memb_type" value="IC" <?php if($memType === 'IC') { ?>checked<?php }; ?>>
			<label for=memb_type_1>Individual + Children $30</label>
			<input class="memType" id=memb_type_2 type="radio" name="memb_type" value="CO" <?php if($memType === 'CO') { ?>checked<?php }; ?>>
			<label for=memb_type_2>Couple $40</label>
			<input class="memType" id=memb_type_3 type="radio" name="memb_type" value="HH" <?php if($memType === 'HH') { ?>checked<?php }; ?>>
			<label for=memb_type_3>Household/Family $45</label>
		</div>
	    </fieldset>

	    <div class="spacer"></div>

	    <fieldset class="manRegForm"   id=personal_info>
		<legend>Your Information</legend>
		<div id="personal_info">
		    <label id="first_name" for="first_name">First Name:</label>
		        <input class="validate[required, custom[onlyLetterSp]]" data-prompt-position="bottomLeft" id="first_name" name="first_name" type=text value=<?php if (isset( $first_name ) ) echo  $first_name; ?>>
		    <label id="last_name" for="last_name">Last Name:</label>
			<input  class="validate[required, custom[onlyLetterSp]]" data-prompt-position="bottomLeft" id="last_name" name="last_name" type=text value=<?php if (isset( $last_name ) ) echo $last_name; ?>>
		</div>
		<div>
		    <label id="email" for=email>Email:</label>
		        <input class="validate[required, custom[email]]" data-prompt-position="bottomLeft" id="email" name="email" type="text" value=<?php if (isset( $email ) ) echo $email; ?>>
		    <label id="phone" for="phone">Phone:</label>
			<input class="validate[required, custom[onlyNumber]]" data-prompt-position="bottomLeft" id="phone1" name="phone1" maxlength="3" size="3" type="text" value=<?php if (isset( $phone1 ) ) echo $phone1; ?> >-
			<input class="validate[required, custom[onlyNumber]]" data-prompt-position="bottomLeft" id="phone2" name="phone2" maxlength="3" size="3" type="text" value=<?php if (isset( $phone2 ) ) echo $phone2; ?> >-
			<input class="validate[required, custom[onlyNumber]]" data-prompt-position="bottomLeft" id="phone3" name="phone3" type="text" maxlength="4" size="4" value=<?php if (isset( $phone3 ) ) echo $phone3; ?> >
	       </div>
	       <div>
		  <label id="b-day"  for=b-day>Birthdate:</label>
		    <select id=month name=month class="validate[required]" data-prompt-position="bottomLeft" >
		       <?php for( $m=1;$m<=12;$m++) {
			    $m = str_pad($m,2, '0',STR_PAD_LEFT );
			    if ( isset( $month ) ) {
				if ($m == $month) {
				   echo "<option selected value='$month'>$month</option>";
				}
			    } else {
				echo "<option value='$m'>$m</option>";
			    }
			} ?>
		    </select>/<select id=day name=day class="validate[required]" data-prompt-position="bottomLeft">
			<?php for( $d=1;$d<=31;$d++) {
			    $d = str_pad($d,2, '0',STR_PAD_LEFT );
			    if ( isset( $day ) ) {
				if ( $d == $day) {
				    echo "<option selected value='$day'>$day</option>";
				}
			    } else {
				echo "<option value='$d'>$d</option>";
			    }
			} ?>
		    </select>
		    <label id="occu" for=occupation>Occupation:</label>
		        <input  class="validate[required, custom[onlyLetterSp]]" data-prompt-position="bottomLeft" id=occu name=occu type=text value=<?php if (isset( $occu ) ) echo $occu; ?>>
		</div>
		<div>
		    <label id=addr1 for=addr1>Address 1:</label>
			<input class="validate[required, custom[onlyLetterNumberSp]]" data-prompt-position="bottomLeft" id=addr1 name=addr1 type=text size=35 value=<?php if (isset( $addr1 ) ) echo $addr1;?>>

		</div>
		<div>
		    <label for=addr2>Address 2:</label>
		    <input class="validate[required, custom[onlyLetterSp]]" id=addr2 name=addr2 type=text size=35" value=<?php if (isset( $addr2 ) ) echo $addr2;?>>
		</div>
		<div>
		    <label id=city for=city>City:</label>
		    <input class="validate[required, custom[onlyLetterSp]]" data-prompt-position="bottomLeft" id=city name=city type=text size=15 value=<?php if (isset( $city ) ) echo $city; ?>>
		    <label id=state for=state>State:</label>
		    <select id=state name=state class="validate[required]">
			<?php $defSel == 'TX';
			echo showOptionsDrop($states_arr, $defSel, true); ?>
		    </select>
		    <label id=zip for=zip>Zip:</label>
		    <input class="validate[required, custom[onlyNumberSp]]" data-prompt-position="bottomLeft" id=zip name=zip type=text size=5  value=<?php if (isset( $zip ) ) echo $zip; ?>>
		</div>
	    </fieldset>

	    <div class="spacer"></div>

	    <fieldset class="manRegForm" id=spouse_info>
		<legend>Spouse/Partner</legend>
		<div>
		    <label id=sfirst_name for=sfirst_name>First Name:</label>
		    <input class="validate[custom[onlyLetterSp]]" data-prompt-position="bottomLeft" id=sfirst_name name=sfirst_name type=text value=<?php if (isset( $sp_first_name ) ) echo $sp_first_name; ?>>
		    <label id=slast_name for=slast_name>Last Name:</label>
		    <input class="validate[custom[onlyLetterSp]]" data-prompt-position="bottomLeft" id=slast_name name=slast_name type=text value=<?php if (isset( $sp_last_name ) ) echo $sp_last_name; ?>>
		</div>
		<div>
		    <label id=semail for=semail>Email:</label>
		    <input class="validate[custom[email]]" data-prompt-position="bottomLeft" id=semail name=semail type=text value=<?php if (isset( $sp_email ) ) echo $sp_email; ?>>
		    <label id=sphone for=sphone>Phone:</label>
		    <input class="validate[custom[onlyNumber]]" id=sphone1 name=sphone1 type=text maxlength="3" size="3" value=<?php if (isset( $sp_phone1 ) ) echo $sp_phone1; ?> >-
		    <input class="validate[custom[onlyNumber]]" id=sphone2 name=sphone2 type=text maxlength="3" size="3" value=<?php if (isset( $sp_phone2 ) ) echo $sp_phone2; ?> >-
		    <input class="validate[custom[onlyNumber]]" id=sphone3 name=sphone3 type=text maxlength="4" size="4" value=<?php if (isset( $sp_phone3 ) ) echo $sp_phone3; ?> >
		</div>
		<div>
		    <label id=sb-day for=sb-day>Birthdate:</label>
		    <select id=smonth name=smonth>
			<?php for( $m=1;$m<=12;$m++) {
			    $sm = str_pad($m,2, '0',STR_PAD_LEFT );
			    if ( isset( $sp_month ) ) {
				if ($sm == $sp_month) {
				    echo "<option selected value='$sp_month'>$sp_month</option>";
				}
			    } else {
				echo "<option value='$sm'>$sm</option>";
			    }
			} ?>
		    </select>/<select id=day name=sday class="validate[required]" data-prompt-position="bottomLeft">
			<?php for( $d=1;$d<=31;$d++) {
			    $sd = str_pad($d,2, '0',STR_PAD_LEFT );
			    if ( isset( $sp_day ) ) {
				if ( $sd == $sp_day) {
				    echo "<option selected value='$sp_day'>$sp_day</option>";
				}
			    } else {
				echo "<option value='$sd'>$sd</option>";
			    }
			} ?>
		    </select>
		    <label id=srelationship for=srelationship>Relationship:</label>
		    <select id=srelationship name=srelationship>;
			<?php if ( isset( $sp_rel ) ) {
			    echo showOptionsDrop($relationship_arr, $sp_rel, true);
			} else {
			    $defSel = 'S';
			     showOptionsDrop($relationship_arr, $defSel, true);
			} ?>
		    </select>
		</div>
	    </fieldset>

	    <div class="spacer"></div>

            <fieldset class="manRegForm"   id=family_info>
		<legend>Family Members</legend>
		<div>
		    <label id=f1first_name for=f1first_name>First Name:</label>
		    <input class="validate[custom[onlyLetterSp]]" data-prompt-position="bottomLeft" id=f1first_name name=f1first_name type=text value=<?php if (isset( $c1_first_name ) ) echo $c1_first_name; ?>>
		    <label id=f1last_name for=f1last_name>Last Name:</label>
		    <input class="validate[custom[onlyLetterSp]]" data-prompt-position="bottomLeft" id=f1last_name name=f1last_name type=text value=<?php if (isset( $c1_last_name ) ) echo $c1_last_name; ?>>
		    <label id=f1relationship for=f1relationship>Relationship:</label>
		    <select id=f1relationship name=f1relationship>
			<?php if ( isset( $c1_rel ) ) {
			    echo showOptionsDrop($relationship_arr, $c1_rel, true);
			} else {
			    $defSel = 'C';
			     showOptionsDrop($relationship_arr, $defSel, true);
			} ?>
		    </select>
		</div>
		<div>
		    <label id=f1b-day for=b-day>Birthdate:</label>
		    <select id=f1month name=f1month>
			<?php for( $m=1;$m<=12;$m++) {
			    $m = str_pad($m,2, '0',STR_PAD_LEFT );
			    if ( isset( $c1_month ) ) {
				if ($m == $c1_month) {
				    echo "<option selected value='$c1_month'>$c1_month</option>";
				}
			    } else {
				echo "<option value='$m'>$m</option>";
			    }
			} ?>
		    </select>/<select id=day name=f1day class="validate[required]" data-prompt-position="bottomLeft">
			<?php for( $d=1;$d<=31;$d++) {
			    $d = str_pad($d,2, '0',STR_PAD_LEFT );
			    if ( isset( $c1_day ) ) {
				if ( $d == $c1_day) {
				    echo "<option selected value='$c1_day'>$c1_day</option>";
				}
			    } else {
				echo "<option value='$d'>$d</option>";
			    }
			} ?>
		    </select>
		    <label id=f1email for=f1email>Email:</label>
		    <input class="validate[custom[email]]" data-prompt-position="bottomLeft" id=f1email name=f1email type=text value=<?php if (isset( $c1_email ) ) echo $c1_email; ?>>
		</div>

		<div class="spacer"></div>  <!-- 2nd FAMILY MEMBER -->
		<div>
		    <label id=first_name for=c2_first_name>First Name:</label>
		    <input class="validate[custom[onlyLetterSp]]" data-prompt-position="bottomLeft" id=c2_first_name name=c2_first_name type=text value=<?php if (isset( $c2_first_name ) ) echo $c2_first_name; ?>>
		    <label id=c2_last_name for=c2_last_name>Last Name:</label>
		    <input class="validate[custom[onlyLetterSp]]" data-prompt-position="bottomLeft" id=c2_last_name name=c2_last_name type=text value=<?php if (isset( $c2_last_name ) ) echo $c2_last_name; ?>>
		    <label id=c2_relationship for=c2_relationship>Relationship:</label>
		    <select id=c2_relationship name=c2_relationship>
			<?php if ( isset( $c2_rel ) ) {
			    echo showOptionsDrop($relationship_arr, $c2_rel, true);
			} else {
			    $defSel = 'C';
			     showOptionsDrop($relationship_arr, $defSel, true);
			} ?>
		    </select>
		</div>
		<div>
		    <label id=c2_b-day for=b-day>Birthdate:</label>
		    <select id=c2_month name=c2_month>
			<?php for( $m=1;$m<=12;$m++) {
			    $m = str_pad($m,2, '0',STR_PAD_LEFT );
			    if ( isset( $c2_month ) ) {
				if ($m == $c2_month) {
				    echo "<option selected value='$c2_month'>$c2_month</option>";
				}
			    } else {
				echo "<option value='$m'>$m</option>";
			    }
			} ?>
		    </select>/<select id=c2_day name=c2_day class="validate[required]" data-prompt-position="bottomLeft">
			<?php for( $d=1;$d<=31;$d++) {
			    $c2_d = str_pad($d,2, '0',STR_PAD_LEFT );
			    if ( isset( $c1_day ) ) {
				if ( $c2_d == $c2_day) {
				    echo "<option selected value='$c2_day'>$c2_day</option>";
				}
			    } else {
				echo "<option value='$c2_d'>$c2_d</option>";
			    }
			} ?>
		    </select>
		    <label id=c2_email for=c2_email>Email:</label>
		    <input class="validate[custom[email]]" data-prompt-position="bottomLeft" id=c2_email name=c2_email type=text value=<?php if (isset( $c2_email ) ) echo $c2_email;?>>
		</div>

		<div class="spacer"></div>   <!-- 3rd FAMILY MEMBER -->

		<div>
		    <label id=c3_first_name for=c3_first_name>First Name:</label>
		    <input class="validate[custom[onlyLetterSp]]" data-prompt-position="bottomLeft" id=c3_first_name name=c3_first_name type=text value=<?php if (isset( $c3_first_name ) ) echo $c3_first_name; ?>>
		    <label id=c3_last_name for=c3_last_name>Last Name:</label>
		    <input class="validate[custom[onlyLetterSp]]" data-prompt-position="bottomLeft" id=c3_last_name name=c3_last_name type=text value=<?php if (isset( $c3_last_name ) ) echo $c3_last_name; ?>>
		    <label id=c3_relationship for=c3_relationship>Relationship:</label>
		    <select id=c3_relationship name=c3_relationship>
			<?php if ( isset( $c3_rel ) ) {
			    echo showOptionsDrop($relationship_arr, $c3_rel, true);
			} else {
			    $defSel = 'C';
			     showOptionsDrop($relationship_arr, $defSel, true);
			} ?>
		    </select>
		</div>
		<div>
		    <label id=c3_b-day for=b-day>Birthdate:</label>
		    <select id=c3_month name=c3_month>
			<?php for( $m=1;$m<=12;$m++) {
			    $m = str_pad($m,2, '0',STR_PAD_LEFT );
			    if ( isset( $c3_month ) ) {
				if ($m == $c3_month) {
				    echo "<option selected value='$c3_month'>$c3_month</option>";
				}
			    } else {
				echo "<option value='$m'>$m</option>";
			    }
			} ?>
		    </select>/<select id=c3_day name=c3_day class="validate[required]" data-prompt-position="bottomLeft">
			<?php for( $d=1;$d<=31;$d++) {
			    $d = str_pad($d,2, '0',STR_PAD_LEFT );
			    if ( isset( $c3_day ) ) {
				if ( $d == $c3_day) {
				    echo "<option selected value='$c3_day'>$c3_day</option>";
				}
			    } else {
				echo "<option value='$d'>$d</option>";
			    }
			} ?>
		    </select>
		    <label id=c3_email for=c3_email>Email:</label>
		    <input class="validate[custom[email]]" data-prompt-position="bottomLeft" id=c3_email name=c3_email type=text value=<?php if (isset( $c3_email ) ) echo $c3_email;?>>
		</div>
	    </fieldset>

	    <div class="spacer"></div>

	    <fieldset id=preferences>
		<legend>Preferences</legend>
		<div>
		    <input id=profile type=checkbox name="profile" value="<?php if (isset( $profile ) )echo $profile; ?>" <?php if( $profile != 'N' ) { echo "checked"; } ?>> <label for=profile>Check here if you want your contact information available in the SECURE online Membership Directory. (Childrens information will automatically be hidden).</label>
		</div>

		<div class="spacer"></div>

		<div>
		    <input id=contact type=checkbox name="contact" value="<?php if (isset( $contact ) ) echo $contact; ?>" <?php if($contact != 'N') { echo "checked"; } ?>><label for=contact>Check here if you are willing to help out with future events or event planing.</label>
		</div>
	    </fieldset>

	<?php if (isset( $_POST['submit'] ) || isset( $_POST['update']) ) { ?>

	    <div class="spacer"></div>

	    <div id=update>
		<fieldset class="screen">
		    <legend class="screen">Update</legend>
		    <div class=screen>If you have made any changes to the form please click:</div>
		    <div>
			<input class="button3 screen" id="update" type=submit name="update" value="update">
		    </div>
		</fieldset>
	    </div>

	<?php } else { ?>

	    <div class="spacer"></div>

	    <div id="submit">
		<fieldset class="screen pb_manRegForm">
		    <legend class="screen">Submit</legend>
		    <div class="center">
			<input class="button3 screen" id="submit" type=submit name="submit" value="submit">
		    </div>
		    <div class="spacer"></div>
		</fieldset>
	    </div>

	<?php } ?>
	</form>

    </div> <!-- end of the changelog div -->
</div> <!-- end of the wrap about_wrap div -->
    <br class="clear" />
</div>

<?php //include( ABSPATH . 'wp-admin/admin-footer.php' ); ?>