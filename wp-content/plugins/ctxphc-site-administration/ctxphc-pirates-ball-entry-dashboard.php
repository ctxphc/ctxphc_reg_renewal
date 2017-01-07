<?php
/*
* Pirate's Ball Manual Registration Entry
 * To be used by CTXPHC BOD or Support only.
*/
?>

<?php
  //global $defSel, $wpdb;
  $cost = 50;
  $pbRegQuantity = 1;
 ?>

<?php $states_arr = array('AL'=>'Alabama','AK'=>'Alaska','AZ'=>'Arizona','AR'=>'Arkansas','CA'=>'California','CO'=>'Colorado','CT'=>'Connecticut','DE'=>'Delaware','DC'=>'District Of Columbia','FL'=>'Florida','GA'=>'Georgia','HI'=>'Hawaii','ID'=>'Idaho','IL'=>'Illinois','IN'=>'Indiana','IA'=>'Iowa','KS'=>'Kansas','KY'=>'Kentucky','LA'=>'Louisiana','ME'=>'Maine','MD'=>'Maryland','MA'=>'Massachusetts','MI'=>'Michigan','MN'=>'Minnesota','MS'=>'Mississippi','MO'=>'Missouri','MT'=>'Montana','NE'=>'Nebraska','NV'=>'Nevada','NH'=>'New Hampshire','NJ'=>'New Jersey','NM'=>'New Mexico','NY'=>'New York','NC'=>'North Carolina','ND'=>'North Dakota','OH'=>'Ohio','OK'=>'Oklahoma','OR'=>'Oregon','PA'=>'Pennsylvania','RI'=>'Rhode Island','SC'=>'South Carolina','SD'=>'South Dakota','TN'=>'Tennessee','TX'=>'Texas','UT'=>'Utah','VT'=>'Vermont','VA'=>'Virginia','WA'=>'Washington','WV'=>'West Virginia','WI'=>'Wisconsin','WY'=>'Wyoming'); ?>


<script type="text/javascript">
   jQuery(document).ready(function(){
      jQuery("#pb_reg_form").validationEngine('attach', {promptPosition : "bottomLeft"});
   });
</script>

<?php
If (isset($_POST['submit'])) {
    //Load Primary Member data from POST data coming from PB registration page.
    $fname			 = $_POST['fname'];
    $lname			 = $_POST['lname'];
    $email			 = $_POST['email'];
    $addr1			 = $_POST['addr1'];
    $addr2			 = $_POST['addr2'];
    $city			 = $_POST['city'];
    $state			 = $_POST['state'];
    $zip			 = $_POST['zip'];
    $club_affiliation	 = $_POST['club_affiliation'];
    $attendee_count		= $_POST['attendee_count'];
    $pbRegQuantity		 = (int)$attendee_count;
    $pb_attendee_fname_1 = $_POST['pb_attendee_fname_1'];
    $pb_attendee_lname_1 = $_POST['pb_attendee_lname_1'];

    if (isset($_POST['pb_attendee_fname_2'])) {
      $pb_attendee_fname_2 = $_POST['pb_attendee_fname_2'];
      $pb_attendee_lname_2 = $_POST['pb_attendee_lname_2'];
    }

    if (isset($_POST['pb_attendee_fname_3'])) {
      $pb_attendee_fname_3 = $_POST['pb_attendee_fname_3'];
      $pb_attendee_lname_3 = $_POST['pb_attendee_lname_3'];
    }

    if (isset($_POST['pb_attendee_fname_4'])) {
      $pb_attendee_fname_4 = $_POST['pb_attendee_fname_4'];
      $pb_attendee_lname_4 = $_POST['pb_attendee_lname_4'];
    }

    $pbTable = 'ctxphc_pb_reg';

    //insert into DB
    $pb_userdata = array(
	'first_name' => $fname,
	'last_name'  => $lname,
	'email'	  => $email,
	'addr1' => $addr1,
	'addr2' => $addr2,
	'city'  => $city,
	'state' => $state,
	'zip'	  => $zip,
	'club_aff'	  => $club_affiliation,
	'quantity'	  => $pbRegQuantity,
	'amount'	  => $pbRegQuantity * $cost,
	'reg_date'	  => current_time('mysql'),
	'attendee_1' => $pb_attendee_fname_1 . ' ' . $pb_attendee_lname_1,
	'attendee_2' => $pb_attendee_fname_2 . ' ' . $pb_attendee_lname_2,
	'attendee_3' => $pb_attendee_fname_3 . ' ' . $pb_attendee_lname_3,
	'attendee_4' => $pb_attendee_fname_4 . ' ' . $pb_attendee_lname_4
       );

    $inserted = pb_insert_registration_data($pbTable, $pb_userdata);

    If ( $inserted === "FAILED" ) {

      //Display error message to user with CTXPHC SUPPORT contact info
       ?>
	<div class="wrap about-wrap">
	    <h2><?php _e( "CTXPHC Pirate's Ball Registration Entry" ); ?></h2>

	    <h2 class="nav-tab-wrapper">
		<a href="<?php plugins_url( 'ctxphc-pirates-ball-entry-dashboard.php', __FILE__ ) ?>" class="nav-tab nav-tab-active">
		    <?php _e( 'Entry' ); ?>
		</a>
	    </h2>

	    <div class="changelog">
		<h3><?php _e( "Pirates Ball Entry" ); ?></h3>

		<div>
		     <h2 style="text-align: center;">There was a failure when processing this entry.</h2>
		</div>
		<div><p />Please let your CTXPHC technical support know this has occurred. Email them at support@ctxphc.com</div>
		 <div class="spacer"></div>
		<div>Thank you,</div>
		<div>CTXPHC Technical Support</div>
	    </div>
	</div>
    <?php  exit;
   }
}

/** WordPress Administration Bootstrap */
require_once( ABSPATH . 'wp-load.php' );
require_once( ABSPATH . 'wp-admin/admin.php' );
require_once( ABSPATH . 'wp-admin/admin-header.php' );
?>

<div class="wrap about-wrap">

    <h2><?php _e( "CTXPHC Pirate's Ball Registration Entry" ); ?></h2>

    <h2 class="nav-tab-wrapper">
	<a href="<?php plugins_url( 'ctxphc-pirates-ball-entry-dashboard.php', __FILE__ ) ?>" class="nav-tab nav-tab-active">
	    <?php _e( 'Manual Entry' ); ?>
	</a>
    </h2>

    <div class="changelog">
	<h3><?php _e( "Pirates Ball Manual Entry" ); ?></h3>

	<form id="pb_reg_form" name="registration_conformation_form" method="post" action="">
	    <fieldset class="pb_reg_form"   id=personal_info>
		<legend>Your Information</legend>
		    <div id="personal_info">
			<label id="pb_fname" for="fname">First Name:</label>
			    <input type=text value='<?php echo $fname; ?>' class="validate[required, custom[onlyLetterSp]]" data-prompt-position="bottomLeft" id="pb_fname" name="fname" />
			<label id="pb_lname" for="lname">Last Name:</label>
			    <input type=text value='<?php echo $lname; ?>' class="validate[required, custom[onlyLetterSp]]" data-prompt-position="bottomLeft" id="pb_lname" name="lname" />
		    </div>

		    <div id="pb_email">
			<label id="pb_email" for=email>Email:</label>
			    <input class="validate[required, custom[email]]" data-prompt-position="bottomLeft" id="pb_email" name="email" type="text" value='<?php echo $email; ?>' />
			<label id="pb_email_verify" for=email>Verify Email:</label>
			    <input class="validate[required, custom[email]]" data-prompt-position="bottomLeft" id="pb_email-verify" name="email-verify" type="text" value='<?php echo $email; ?>' />
		    </div>
		    <div id='pb_club_affiliation'>
		      <label id="pb_club_affiliation" for=club_affiliation>PHC Affiliation or None:</label>
			    <input class="validate[required, custom[ClubAffiliation]]" data-prompt-position="bottomLeft" id="club_affiliation" name="club_affiliation" type="text" value='<?php echo $club_affiliation; ?>' />
		    </div>
		    <div class='spacer'></div>
	    </fieldset>

	    <div class='spacer'></div>

	    <fieldset class="pb_reg_form" id=address>
		    <legend>Address</legend>
			    <div>
				    <label id="pb_addr1" for="addr1">Address:</label>
					    <input id="pb_addr1" name="addr1" type="text" size=35 value='<?php echo $addr1; ?>' class="validate[required, custom[address]]" data-prompt-position="bottomLeft" />
				    <label for=addr2>Apt/Suite:</label>
					    <input id=pb_addr2 name=addr2 type=text size=35 value='<?php echo $addr2; ?>' class="validate[custom[address-2]]" data-prompt-position="bottomLeft" />
			    </div>
			    <div>
				    <label id=pb_city for=city>City:</label>
					    <input id=pb_city name=city type=text size=15 value='<?php echo $city; ?>' class="validate[required, custom[onlyLetterSp]]" data-prompt-position="bottomLeft" />
				    <label id=pb_state for=state>State:</label>
					    <select id=pb_state name=state class="validate[required]">
						    <?php echo showOptionsDrop($states_arr, $state, FALSE); ?>
					    </select>
				    <label id=pb_ip for=zip>Zip:</label>
					    <input id=pb_zip name=zip type=text size=5 value='<?php echo $zip; ?>' class="validate[required, custom[postcodeUS]]" data-prompt-position="bottomLeft" />
			    </div>
			    <div class='spacer'></div>
	    </fieldset>

	    <div class="spacer"></div>

	    <fieldset class="pb_reg_form"   id="pb_Attend_Info">
		<legend>Attendees</legend>
		<div id="pb_attendee_count">
		   <input class="pb_attendeeCount" id=pb_attendee_count_1 type="radio" name="attendee_count" value="1" <?php if($attendee_count === '1') { ?>checked<?php }; ?> />
		   <label class=pb_attendeeCount for=pb_attendee_count_1>1 Attendee $50</label>
		   <input class="pb_attendeeCount" id=pb_attendee_count_2 type="radio" name="attendee_count" value="2" <?php if($attendee_count === '2') { ?>checked<?php }; ?>/>
		   <label class=pb_attendeeCount for=pb_attendee_count_2>2 Attendees $100</label>
		   <input class="pb_attendeeCount" id=pb_attendee_count_3 type="radio" name="attendee_count" value="3" <?php if($attendee_count === '3') { ?>checked<?php }; ?>/>
		   <label class=pb_attendeeCount for=pb_attendee_count_3>3 Attendees $150</label>
		   <input class="pb_attendeeCount" id=pb_attendee_count_4 type="radio" name="attendee_count" value="4" <?php if($attendee_count === '4') { ?>checked<?php }; ?>/>
		   <label class=pb_attendeeCount for=pb_attendee_count_4>4 Attendees: $200</label>
		</div>

		<div class="spacer"></div>

		<div id='pb_attendee_1'>
		  <label id=pb_attendee_fname for=pb_attendee_fname_1>First Name:</label>
			<input id=pb_attendee_fname name=pb_attendee_fname_1 type=text value='<?php echo $pb_attendee_fname_1; ?>' class="validate[custom[onlyLetterSp]]" data-prompt-position="bottomLeft" />
		  <label id=pb_attendee_lname for=pb_attendee_lname_1>Last Name:</label>
			<input id=pb_attendee_lname name=pb_attendee_lname_1 type=text value='<?php echo $pb_attendee_lname_1; ?>' class="validate[custom[onlyLetterSp]]" data-prompt-position="bottomLeft" />
		 </div>
		 <div id='pb_attendee_2'>
		    <label id=pb_attendee_fname for=pb_attendee_fname_2>First Name:</label>
			<input id=pb_attendee_fname name=pb_attendee_fname_2 type=text value='<?php echo $pb_attendee_fname_2; ?>' class="validate[custom[onlyLetterSp]]" data-prompt-position="bottomLeft" />
		    <label id=pb_attendee_lname for=pb_attendee_lname_2>Last Name:</label>
			<input id=pb_attendee_lname name=pb_attendee_lname_2 type=text value='<?php echo $pb_attendee_lname_2; ?>' class="validate[custom[onlyLetterSp]]" data-prompt-position="bottomLeft" />
		 </div>
		 <div id='pb_attendee_3'>
		    <label id=pb_attendee_fname for=pb_attendee_fname_2>First Name:</label>
			<input id=pb_attendee_fname name=pb_attendee_fname_3 type=text value='<?php echo $pb_attendee_fname_3; ?>' class="validate[custom[onlyLetterSp]]" data-prompt-position="bottomLeft" />
		    <label id=pb_attendee_lname for=pb_attendee_lname_2>Last Name:</label>
			<input id=pb_attendee_lname name=pb_attendee_lname_3 type=text value='<?php echo $pb_attendee_lname_3; ?>' class="validate[custom[onlyLetterSp]]" data-prompt-position="bottomLeft" />
		 </div>
		 <div id='pb_attendee_4'>
		    <label id=pb_attendee_fname for=pb_attendee_fname_4>First Name:</label>
			<input id=pb_attendee_fname name=pb_attendee_fname_4 type=text value='<?php echo $pb_attendee_fname_4; ?>' class="validate[custom[onlyLetterSp]]" data-prompt-position="bottomLeft" />
		    <label id=pb_attendee_lname for=pb_attendee_lname_4>Last Name:</label>
			<input id=pb_attendee_lname name=pb_attendee_lname_4 type=text value='<?php echo $pb_attendee_lname_4; ?>' class="validate[custom[onlyLetterSp]]" data-prompt-position="bottomLeft" />
		 </div>
		 <div class='spacer'></div>
	    </fieldset>

	    <div class='spacer'></div>

	    <fieldset class="screen pb_reg_form">
	      <legend class="screen">Submit</legend>
		<div class="center">
		    <input class="button3 screen" id="update" type=submit name="submit" value="submit">
		</div>
	    </fieldset>
	</form>
    </div>
</div>

<?php include( ABSPATH . 'wp-admin/admin-footer.php' );
?>