<?php
/**
 * ******************************************
 * * Prepare member data from previous membership tables
 * * for inserting into the new membership table.
 * ******************************************
 *
 * @param $memb_data
 */
function mp_validate_post_data( $memb_data ) {
	foreach ( $memb_data as $mkey => $mvalue ) {
		switch ( $mkey ) {
			case 'mb_email':
			case 'sp_email':
			case 'c1_email':
			case 'c2_email':
			case 'c3_email':
			case 'c4_email':
				$safe_email           = sanitize_email( $mvalue );
				$member_data[ $mkey ] = $safe_email;
				break;
			case 'phone':
				$member_data[ $mkey ] = $mvalue;
				break;
			case 'mb_birthday':
				$member_data[ $mkey ] = $mvalue;
				break;
			case 'state':
				$member_data[ $mkey ] = $mvalue;
				break;
			case 'relationship_id':
				$member_data[ $mkey ] = $mvalue;
				break;
			default:
				$clean_value          = sanitize_text_field( $mvalue );
				$member_data[ $mkey ] = $clean_value;
				break;
		}
	}

	return $member_data;
}


/**
 *
 */
function mp_registration_form() {


	global $defSel, $mp_valid_mdata;

	$states_arr = array(
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

	$relationship_arr = array( '2' => "Spouse", '3' => "Partner", '4' => "Child", '5' => "Other" );

	/** @var int $cost */
	$memb_costs = get_membership_pricing();


	$mb_first_name = ( ! empty( $mp_valid_mdata[ 'mb_first_name' ] ) ) ? trim( $mp_valid_mdata[ 'mb_first_name' ] ) : '';
	$mb_last_name  = ( ! empty( $mp_valid_mdata[ 'mb_last_name' ] ) ) ? trim( $mp_valid_mdata[ 'mb_last_name' ] ) : '';
	$mb_username   = ( ! empty( $mp_valid_mdata[ 'mb_username' ] ) ) ? trim( $mp_valid_mdata[ 'mb_username' ] ) : '';
	$mb_password   = ( ! empty( $mp_valid_mdata[ 'mb_password' ] ) ) ? trim( $mp_valid_mdata[ 'mb_password' ] ) : '';
	$mb_birthday   = ( ! empty( $mp_valid_mdata[ 'mb_birthday' ] ) ) ? trim( $mp_valid_mdata[ 'mb_birthday' ] ) : '';
	$mb_email      = ( ! empty( $mp_valid_mdata[ 'mb_email' ] ) ) ? trim( $mp_valid_mdata[ 'mb_email' ] ) : '';
	$mb_phone      = ( ! empty( $mp_valid_mdata[ 'mb_phone' ] ) ) ? trim( $mp_valid_mdata[ 'mb_phone' ] ) : '';
	$mb_occupation = ( ! empty( $mp_valid_mdata[ 'mb_occupation' ] ) ) ? trim( $mp_valid_mdata[ 'mb_occupation' ] ) : '';
	$mb_addr1      = ( ! empty( $mp_valid_mdata[ 'mb_addr1' ] ) ) ? trim( $mp_valid_mdata[ 'mb_addr1' ] ) : '';
	$mb_addr2      = ( ! empty( $mp_valid_mdata[ 'mb_addr2' ] ) ) ? trim( $mp_valid_mdata[ 'mb_addr2' ] ) : '';
	$mb_city       = ( ! empty( $mp_valid_mdata[ 'mb_city' ] ) ) ? trim( $mp_valid_mdata[ 'mb_city' ] ) : '';
	$mb_state      = ( ! empty( $mp_valid_mdata[ 'mb_state' ] ) ) ? trim( $mp_valid_mdata[ 'mb_state' ] ) : '';
	$mb_zip        = ( ! empty( $mp_valid_mdata[ 'mb_zip' ] ) ) ? trim( $mp_valid_mdata[ 'mb_zip' ] ) : '';

	$sp_first_name   = ( ! empty( $mp_valid_mdata[ 'sp_first_name' ] ) ) ? trim( $mp_valid_mdata[ 'sp_first_name' ] ) : '';
	$sp_last_name    = ( ! empty( $mp_valid_mdata[ 'sp_last_name' ] ) ) ? trim( $mp_valid_mdata[ 'sp_last_name' ] ) : '';
	$sp_birthday     = ( ! empty( $mp_valid_mdata[ 'sp_birthday' ] ) ) ? trim( $mp_valid_mdata[ 'sp_birthday' ] ) : '';
	$sp_email        = ( ! empty( $mp_valid_mdata[ 'sp_email' ] ) ) ? trim( $mp_valid_mdata[ 'sp_email' ] ) : '';
	$sp_phone        = ( ! empty( $mp_valid_mdata[ 'sp_phone' ] ) ) ? trim( $mp_valid_mdata[ 'sp_phone' ] ) : '';
	$sp_relationship = ( ! empty( $mp_valid_mdata[ 'sp_relationship' ] ) ) ? trim( $mp_valid_mdata[ 'sp_relationship' ] ) : '';

	$c1_first_name   = ( ! empty( $mp_valid_mdata[ 'c1_first_name' ] ) ) ? trim( $mp_valid_mdata[ 'c1_first_name' ] ) : '';
	$c1_last_name    = ( ! empty( $mp_valid_mdata[ 'c1_last_name' ] ) ) ? trim( $mp_valid_mdata[ 'c1_last_name' ] ) : '';
	$c1_birthday     = ( ! empty( $mp_valid_mdata[ 'c1_birthday' ] ) ) ? trim( $mp_valid_mdata[ 'c1_birthday' ] ) : '';
	$c1_email        = ( ! empty( $mp_valid_mdata[ 'c1_email' ] ) ) ? trim( $mp_valid_mdata[ 'c1_email' ] ) : '';
	$c1_relationship = ( ! empty( $mp_valid_mdata[ 'c1_relationship' ] ) ) ? trim( $mp_valid_mdata[ 'c1_relationship' ] ) : '';

	$c2_first_name   = ( ! empty( $mp_valid_mdata[ 'c2_first_name' ] ) ) ? trim( $mp_valid_mdata[ 'c2_first_name' ] ) : '';
	$c2_last_name    = ( ! empty( $mp_valid_mdata[ 'c2_last_name' ] ) ) ? trim( $mp_valid_mdata[ 'c2_last_name' ] ) : '';
	$c2_birthday     = ( ! empty( $mp_valid_mdata[ 'c2_birthday' ] ) ) ? trim( $mp_valid_mdata[ 'c2_birthday' ] ) : '';
	$c2_email        = ( ! empty( $mp_valid_mdata[ 'c2_email' ] ) ) ? trim( $mp_valid_mdata[ 'c2_email' ] ) : '';
	$c2_relationship = ( ! empty( $mp_valid_mdata[ 'c2_relationship' ] ) ) ? trim( $mp_valid_mdata[ 'c2_relationship' ] ) : '';

	$c3_first_name   = ( ! empty( $mp_valid_mdata[ 'c3_first_name' ] ) ) ? trim( $mp_valid_mdata[ 'c3_first_name' ] ) : '';
	$c3_last_name    = ( ! empty( $mp_valid_mdata[ 'c3_last_name' ] ) ) ? trim( $mp_valid_mdata[ 'c3_last_name' ] ) : '';
	$c3_birthday     = ( ! empty( $mp_valid_mdata[ 'c3_birthday' ] ) ) ? trim( $mp_valid_mdata[ 'c3_birthday' ] ) : '';
	$c3_email        = ( ! empty( $mp_valid_mdata[ 'c3_email' ] ) ) ? trim( $mp_valid_mdata[ 'c3_email' ] ) : '';
	$c3_relationship = ( ! empty( $mp_valid_mdata[ 'c3_relationship' ] ) ) ? trim( $mp_valid_mdata[ 'c3_relationship' ] ) : '';

	$c4_first_name   = ( ! empty( $mp_valid_mdata[ 'c4_first_name' ] ) ) ? trim( $mp_valid_mdata[ 'c4_first_name' ] ) : '';
	$c4_last_name    = ( ! empty( $mp_valid_mdata[ 'c4_last_name' ] ) ) ? trim( $mp_valid_mdata[ 'c4_last_name' ] ) : '';
	$c4_birthday     = ( ! empty( $mp_valid_mdata[ 'c4_birthday' ] ) ) ? trim( $mp_valid_mdata[ 'c4_birthday' ] ) : '';
	$c4_email        = ( ! empty( $mp_valid_mdata[ 'c4_email' ] ) ) ? trim( $mp_valid_mdata[ 'c4_email' ] ) : '';
	$c4_relationship = ( ! empty( $mp_valid_mdata[ 'c4_relationship' ] ) ) ? trim( $mp_valid_mdata[ 'c4_relationship' ] ) : '';

	?>
	<div>
		<h2 class="ctxphc_center">Central Texas Parrot Head Club</h2>
	</div>
	<p>So, you've decided you want to join in our mission to Party with a Purpose,
		have PHun and help the community? If so, <img class="alignright wp-image-7" title="membership-image"
		                                              src="<?php echo get_template_directory_uri(); ?>/includes/images/membership-image.jpg"
		                                              alt="ctxphc membership image"/>you
		can complete the application below and make a payment using PayPal or print
		out the application and mail a check to:</p>

	<p>
		Central Texas Parrot Head Club<br/>
		c/o Membership Director<br/>
		P.O. Box 1074<br/>
		Austin, TX 78767-1074
	</p>

	<p>Membership entitles you to attend our numerous monthly events; an official club badge; access to the monthly CTXPHC newsletter and ParrotHead-related electronic bulletins. The newsletter and bulletins keep you up-to-date regarding local, regional and statewide PHlockings (which you would be eligible to attend); community events; special discounts; concert news; VIP passes and much more!</p>

	<p>If you have any questions, contact our <a
			href="mailto:<?php echo antispambot( 'membership@ctxphc.com' ); ?>">Membership
			Director</a>.</p>

	<p>If you are ready to join in the PHun, scroll down and fill out our registration form!</p>

	<p><strong>NOTE: If this is a renewal, login to your profile and click on the
			"Renew Membership" button. If you don't know how to login, send email to
			our <a href="mailto:<?php echo antispambot( 'support@ctxphc.com' ); ?>">Support
				Staff</a>.</strong></p>

	<div class="spacer"></div>

	<div class="reg_form_row" id="mem_reg_types">
		<h4>Membership Types:</h4>
		<ul id="memb_reg_list">
			<li>Individual -
				$<?php echo $memb_costs[ 1 ]->cost; ?></li>
			<li>Individual + Child -
				$<?php echo $memb_costs[ 2 ]->cost; ?></li>
			<li>Couples -
				$<?php echo $memb_costs[ 3 ]->cost; ?></li>
			<li>Household -
				$<?php echo $memb_costs[ 4 ]->cost; ?></li>
		</ul>
	</div>


	<form class="memb_reg_form" id="regForm" name="regForm" action="<?php echo $_SERVER[ 'REQUEST_URI' ]; ?>" method="post">
		<?php do_action( 'user_edit_form_tag' ); ?>
		<fieldset class="reg_form" id="memb_type">
			<legend><span class="memb_legend">Membership Options</span></legend>
			<div class="memb_type" id="memb_type_div">
				<!-- Individual Member Option -->
				<input class="memb_type" id="memb_type_1" type="radio" name="memb_type" value="1" checked/>
				<label class="memb_type" for="memb_type_1"><?php _e( 'Individual' ) ?> $<?php echo $memb_costs[ 1 ]->cost; ?></label>

				<!-- Individual + Child(ren) Member Option -->
				<input class="memb_type" id="memb_type_2" type="radio" name="memb_type" value="2"/>
				<label class="memb_type" for="memb_type_2"><?php _e( 'Individual + Children' ) ?> $<?php echo $memb_costs[ 2 ]->cost; ?></label>

				<!-- Couple Member Option -->
				<input class="memb_type" id="memb_type_3" type="radio" name="memb_type" value="3"/>
				<label class="memb_type" for="memb_type_3"><?php _e( 'Couple' ) ?> $<?php echo $memb_costs[ 3 ]->cost; ?></label>

				<!-- Household Member Option -->
				<input class="memb_type" id="memb_type_4" type="radio" name="memb_type" value="4"/>
				<label class="memb_type" for="memb_type_4"><?php _e( 'Household' ) ?> $<?php echo $memb_costs[ 4 ]->cost; ?></label>
			</div>
		</fieldset>

		<div class="spacer"></div>

		<fieldset class="reg_form" id="personal_info">
			<legend><span class="memb_legend">Your Information</span></legend>
			<div class="reg_form_row">
				<label class="reg_birthday" id="lbl_mb_birthday" for="mb_birthday">Birthdate:</label>
				<input class="reg_birthday validate[required, custom[onlyNumber]]"
				       data-prompt-position="bottomLeft" id="mb_birthday" name="mb_birthday" type="date"
				       value="<?php ( isset( $mp_valid_mdata[ 'mb_birthday' ] ) ? $mb_birthday : null ); ?>"/>

				<label id="lbl_mb_occupation" for="mb_occupation">Occupation:</label>
				<input class="validate[required, custom[onlyLetterSp]]" data-prompt-position="bottomLeft"
				       id="mb_occupation" name="mb_occupation" type="text"
				       value="<?php ( isset( $mp_valid_mdata[ 'mb_occupation' ] ) ? $mb_occupation : null ); ?>"/>
			</div>
		</fieldset>

		<div class="spacer"></div>

		<fieldset class="reg_form" id="mb_address">
			<legend><span class="memb_legend">Address</span></legend>
			<div class="reg_form_row">
				<label id="lbl_mb_addr1" for="mb_addr1">Address:</label>
				<input class="validate[required, custom[address]]" data-prompt-position="bottomLeft"
				       id="mb_addr1" name="mb_addr1" type="text"
				       value="<?php ( isset( $mp_valid_mdata[ 'mb_addr1' ] ) ? $mb_addr1 : null ); ?>"/>

				<label id="lbl_mb_addr2" for="mb_addr2">Suite/Apt:</label>
				<input class="validate[custom[onlyLetterNumber]]" data-prompt-position="bottomLeft"
				       id="mb_addr2" name="mb_addr2" type="text"
				       value="<?php ( isset( $mp_valid_mdata[ 'mb_addr2' ] ) ? $mb_addr2 : null ); ?>"/>
			</div>
			<div class="reg_form_row">
				<label id="lbl_mb_city" for="mb_city">City:</label>
				<input class="validate[required, custom[onlyLetterSp]]" data-prompt-position="bottomLeft"
				       id="mb_city" name="mb_city" type="text"
				       value="<?php ( isset( $mp_valid_mdata[ 'mb_city' ] ) ? $mb_city : null ); ?>"/>

				<label id="lbl_mb_state" for="mb_state">State:</label>
				<select class="validate[required]" id="mb_state" name="mb_state">
					<?php ( isset( $mp_valid_mdata[ 'mb_state' ] ) ? $defSel = $mb_state : $defSel = 'TX' ); ?>
					<?php echo showOptionsDrop( $states_arr, $defSel, true ); ?>
				</select>

				<label id="lbl_mb_zip" for="mb_zip">Zip:</label>
				<input id="mb_zip" class="validate[required, custom[zip-code]]"
				       data-prompt-position="bottomLeft" name="mb_zip" type="text"
				       value="<?php ( isset( $mp_valid_mdata[ 'mb_zip' ] ) ? $mb_zip : null ); ?>"/>
			</div>
		</fieldset>

		<div class="spacer" id="spouse_spacer"></div>

		<fieldset class="reg_form" id="spouse_info">
			<legend><span class="memb_legend">Spouse/Partner</span></legend>
			<div class="reg_form_row">
				<label class="reg_first_name" id="lbl_sp_first_name" for="sp_first_name">First Name:</label>
				<input class="reg_first_name validate[required, custom[onlyLetterSp]]"
				       data-prompt-position="bottomLeft" id="sp_first_name" name="sp_first_name" type="text"
				       value="<?php ( isset( $mp_valid_mdata[ 'sp_first_name' ] ) ? $sp_first_name : null ); ?>"/>

				<label class="reg_last_name" id="lbl_sp_last_name" for="sp_last_name">Last Name:</label>
				<input class="reg_last_name validate[required, custom[onlyLetterSp]]"
				       data-prompt-position="bottomLeft" id="sp_last_name" name="sp_last_name" type="text"
				       value="<?php ( isset( $mp_valid_mdata[ 'sp_last_name' ] ) ? $sp_last_name : null ); ?>"/>
			</div>
			<div class="reg_form_row">
				<label class="reg_birthday" id="lbl_sp_birthday" for="sp_birthday">Birthdate:</label>
				<input class="reg_birthday validate[required, custom[onlyNumber]]" id="sp_birthday"
				       data-prompt-position="bottomLeft" name="sp_birthday" type="date"
				       value="<?php ( isset( $mp_valid_mdata[ 'sp_birthday' ] ) ? $sp_birthday : null ); ?>"/>

				<label class="reg_email" id="lbl_sp_email" for="sp_email">Email:</label>
				<input class="reg_email validate[custom[email]]" data-prompt-position="bottomLeft"
				       id="sp_email" name="sp_email" type="email"
				       value="<?php ( isset( $mp_valid_mdata[ 'sp_email' ] ) ? $sp_email : null ); ?>"/>
			</div>
			<div class="reg_form_row">
				<label class="reg_phone" id="lbl_sp_phone" for="sp_phone">Phone:</label>
				<input class="reg_phone validate[custom[onlyNumber]]" data-prompt-position="bottomLeft"
				       id="sp_phone" name="sp_phone" type="tel"
				       value="<?php ( isset( $mp_valid_mdata[ 'sp_phone' ] ) ? $sp_phone : null ); ?>"/>

				<label class="sp_relationship" id="lbl_sp_relationship"
				       for="sp_relationship">Relationship:</label>
				<select class="sp_relationship validate[required]"
				        id="sp_relationship" name="sp_relationship">
					<?php ( isset( $mp_valid_mdata[ 'sp_relationship' ] ) ? $defSel = $sp_relationship : $defSel = 2 ); ?>
					<?php echo showOptionsDrop( $relationship_arr, $defSel, true ); ?>
				</select>

			</div>
		</fieldset>

		<div class="spacer" id="family_spacer"></div>
		<!--    BEGIN 1ST FAMILY MEMBER  -->

		<fieldset class="reg_form" id="family_info">
			<legend><span class="memb_legend">Family Members</span></legend>
			<section id="child1">
				<div class="reg_form_row">
					<label class="reg_first_name" for="c1_first_name">First Name:</label>
					<input class="reg_first_name validate[custom[onlyLetterSp]]"
					       data-prompt-position="bottomLeft" id="c1_first_name" name="c1_first_name" type="text"
					       value="<?php ( isset( $mp_valid_mdata[ 'c1_first_name' ] ) ? $c1_first_name : null ); ?>"/>

					<label class="reg_last_name" for="c1_last_name">Last Name:</label>
					<input class="reg_last_name validate[custom[onlyLetterSp]]"
					       data-prompt-position="bottomLeft" id="c1_last_name" name="c1_last_name" type="text"
					       value="<?php ( isset( $mp_valid_mdata[ 'c1_last_name' ] ) ? $c1_last_name : null ); ?>"/></div>
				<div class="reg_form_row">
					<label class="reg_birthday" id="lbl_c1_birthday" for="c1_birthday">Birthdate:</label>
					<input class="reg_birth_month validate[custom[onlyNumber]]"
					       data-prompt-position="bottomLeft" id="c1_birthday" name="c1_birthday" type="date"
					       value="<?php ( isset( $mp_valid_mdata[ 'c1_birthday' ] ) ? $c1_birthday : null ); ?>"/>

					<label class="child_relationship" id="lbl_c1_relationship"
					       for="c1_relationship">Relationship:</label>
					<select class="child_relationship" id="c1_relationship" name="c1_relationship">
						<?php ( isset( $mp_valid_mdata[ 'c1_relationship' ] ) ? $defSel = $c1_relationship : $defSel = 4 ); ?>
						<?php echo showOptionsDrop( $relationship_arr, $defSel, true ); ?>
					</select>
				</div>
				<div class="reg_form_row">
					<label class="child_email" id="lbl_c1_email" for="c1_email">Email:</label>
					<input class="child_email validate[custom[email]]" data-prompt-position="bottomLeft"
					       id="c1_email" name="c1_email" type="email"
					       value="<?php ( isset( $mp_valid_mdata[ 'c1_email' ] ) ? $c1_email : null ); ?>"/>
				</div>
			</section>
			<!--  //END of CHILD1 -->

			<div class="spacer"></div>
			<!--    BEGIN 2ND FAMILY MEMBER  -->

			<section id="child2">
				<div class="reg_form_row">
					<label class="reg_first_name" for="c2_first_name">First Name:</label>
					<input class="reg_first_name validate[custom[onlyLetterSp]]"
					       data-prompt-position="bottomLeft" id="c2_first_name" name="c2_first_name" type="text"
					       value="<?php ( isset( $mp_valid_mdata[ 'c2_first_name' ] ) ? $c2_first_name : null ); ?>"/>

					<label class="reg_last_name" for="c2_last_name">Last Name:</label>
					<input class="reg_last_name validate[custom[onlyLetterSp]]"
					       data-prompt-position="bottomLeft" id="c2_last_name" name="c2_last_name" type="text"
					       value="<?php ( isset( $mp_valid_mdata[ 'c2_last_name' ] ) ? $c2_last_name : null ); ?>"/></div>
				<div class="reg_form_row">
					<label class="reg_birthday" id="lbl_c2_birthday" for="c2_birthday">Birthdate:</label>
					<input class="reg_birth_month validate[custom[onlyNumber]]"
					       data-prompt-position="bottomLeft" id="c2_birthday" name="c2_birthday" type="date"
					       value="<?php ( isset( $mp_valid_mdata[ 'c2_birthday' ] ) ? $c2_birthday : null ); ?>"/>

					<label class="child_relationship" id="lbl_c2_relationship"
					       for="c2_relationship">Relationship:</label>
					<select class="child_relationship" id="c2_relationship" name="c2_relationship">
						<?php ( isset( $mp_valid_mdata[ 'c2_relationship' ] ) ? $defSel = $c2_relationship : $defSel = 4 ); ?>
						<?php echo showOptionsDrop( $relationship_arr, $defSel, true ); ?>
					</select>
				</div>
				<div class="reg_form_row">
					<label class="child_email" id="lbl_c2_email" for="c2_email">Email:</label>
					<input class="child_email validate[custom[email]]" data-prompt-position="bottomLeft"
					       id="c2_email" name="c2_email" type="email"
					       value="<?php ( isset( $mp_valid_mdata[ 'c2_email' ] ) ? $c2_email : null ); ?>"/>
				</div>
			</section>
			<!--  //END CHILD2 -->

			<div class="spacer"></div>
			<!--    BEGIN 3RD FAMILY MEMBER  -->

			<section id="child3">
				<div class="reg_form_row">
					<label class="reg_first_name" for="c3_first_name">First Name:</label>
					<input class="reg_first_name validate[custom[onlyLetterSp]]"
					       data-prompt-position="bottomLeft" id="c3_first_name" name="c3_first_name" type="text"
					       value="<?php ( isset( $mp_valid_mdata[ 'c3_first_name' ] ) ? $c3_first_name : null ); ?>"/>

					<label class="reg_last_name" for="c3_last_name">Last Name:</label>
					<input class="reg_last_name validate[custom[onlyLetterSp]]"
					       data-prompt-position="bottomLeft" id="c3_last_name" name="c3_last_name" type="text"
					       value="<?php ( isset( $mp_valid_mdata[ 'c3_last_name' ] ) ? $c3_last_name : null ); ?>"/></div>
				<div class="reg_form_row">
					<label class="reg_birthday" id="lbl_c3_birthday" for="c3_birthday">Birthdate:</label>
					<input class="reg_birth_month validate[custom[onlyNumber]]"
					       data-prompt-position="bottomLeft" id="c3_birthday" name="c3_birthday" type="date"
					       value="<?php ( isset( $mp_valid_mdata[ 'c3_birthday' ] ) ? $c3_birthday : null ); ?>"/>

					<label class="child_relationship" id="lbl_c3_relationship"
					       for="c3_relationship">Relationship:</label>
					<select class="child_relationship" id="c3_relationship" name="c3_relationship">
						<?php ( isset( $mp_valid_mdata[ 'c3_relationship' ] ) ? $defSel = $c3_relationship : $defSel = 4 ); ?>
						<?php echo showOptionsDrop( $relationship_arr, $defSel, true ); ?>
					</select>
				</div>
				<div class="reg_form_row">
					<label class="child_email" id="lbl_c3_email" for="c3_email">Email:</label>
					<input class="child_email validate[custom[email]]" data-prompt-position="bottomLeft"
					       id="c3_email" name="c3_email" type="email"
					       value="<?php ( isset( $mp_valid_mdata[ 'c3_email' ] ) ? $c3_email : null ); ?>"/>
				</div>
			</section>
			<!--  //END of CHILD3  -->

			<div class="spacer"></div>
			<!--    BEGIN 4th FAMILY MEMBER  -->

			<section id="child4">
				<div class="reg_form_row">
					<label class="reg_first_name" for="c4_first_name">First Name:</label>
					<input class="reg_first_name validate[custom[onlyLetterSp]]"
					       data-prompt-position="bottomLeft" id="c4_first_name" name="c4_first_name" type="text"
					       value="<?php ( isset( $mp_valid_mdata[ 'c4_first_name' ] ) ? $c4_first_name : null ); ?>"/>

					<label class="reg_last_name" for="c4_last_name">Last Name:</label>
					<input class="reg_last_name validate[custom[onlyLetterSp]]"
					       data-prompt-position="bottomLeft" id="c4_last_name" name="c4_last_name" type="text"
					       value="<?php ( isset( $mp_valid_mdata[ 'c4_last_name' ] ) ? $c4_last_name : null ); ?>"/></div>
				<div class="reg_form_row">
					<label class="reg_birthday" id="lbl_c4_birthday" for="c4_birthday">Birthdate:</label>
					<input class="reg_birth_month validate[custom[onlyNumber]]"
					       data-prompt-position="bottomLeft" id="c4_birthday" name="c4_birthday" type="date"
					       value="<?php ( isset( $mp_valid_mdata[ 'c4_birthday' ] ) ? $c4_birthday : null ); ?>"/>

					<label class="child_relationship" id="lbl_c4_relationship"
					       for="c4_relationship">Relationship:</label>
					<select class="child_relationship" id="c4_relationship" name="c4_relationship">
						<?php ( isset( $mp_valid_mdata[ 'c4_relationship' ] ) ? $defSel = $c4_relationship : $defSel = 4 ); ?>
						<?php echo showOptionsDrop( $relationship_arr, $defSel, true ); ?>
					</select>
				</div>
				<div class="reg_form_row">
					<label class="child_email" id="lbl_c4_email" for="c4_email">Email:</label>
					<input class="child_email validate[custom[email]]" data-prompt-position="bottomLeft"
					       id="c4_email" name="c4_email" type="email"
					       value="<?php ( isset( $mp_valid_mdata[ 'c4_email' ] ) ? $c4_email : null ); ?>"/>
				</div>
			</section>
			<!--  //END of CHILD3  -->
		</fieldset>

		<div class="spacer"></div>

		<div>
			<input class="ctxphc_button3 screen" id="reg_submit" type="submit" name="registration"
			       value="Submit"/>
		</div>

	</form>

<?php
}


add_filter( 'registration_errors', 'mp_registration_errors', 10, 3 );
function mp_registration_errors( $errors, $sanitized_user_login, $user_email ) {
	global $mp_valid_mdata;

	if ( empty( $_POST['first_name'] ) || ! empty( $_POST['first_name'] ) && trim( $_POST['first_name'] ) == '' ) {
		$errors->add( 'first_name_error', __( '<strong>ERROR</strong>: You must include a first name.', 'mydomain' ) );
	}

	return $errors;
}



add_action( 'user_register', 'complete_registration' );

function complete_registration() {
	global $mp_valid_mdata, $mp_reg_errors;
	if ( 1 > count( $mp_reg_errors->get_error_messages() ) ) {
		$userdata = array(
			'mb_first_name'   => trim( $mp_valid_mdata[ 'mb_first_name' ] ),
			'mb_last_name'    => trim( $mp_valid_mdata[ 'mb_last_name' ] ),
			'mb_username'     => trim( $mp_valid_mdata[ 'mb_username' ] ),
			'mb_password'     => trim( $mp_valid_mdata[ 'mb_password' ] ),
			'mb_birthday'     => trim( $mp_valid_mdata[ 'mb_birthday' ] ),
			'mb_email'        => trim( $mp_valid_mdata[ 'mb_email' ] ),
			'mb_phone'        => trim( $mp_valid_mdata[ 'mb_phone' ] ),
			'mb_occupation'   => trim( $mp_valid_mdata[ 'mb_occupation' ] ),
			'mb_addr1'        => trim( $mp_valid_mdata[ 'mb_addr1' ] ),
			'mb_addr2'        => trim( $mp_valid_mdata[ 'mb_addr2' ] ),
			'mb_city'         => trim( $mp_valid_mdata[ 'mb_city' ] ),
			'mb_state'        => trim( $mp_valid_mdata[ 'mb_state' ] ),
			'mb_zip'          => trim( $mp_valid_mdata[ 'mb_zip' ] ),
			'sp_first_name'   => trim( $mp_valid_mdata[ 'sp_first_name' ] ),
			'sp_last_name'    => trim( $mp_valid_mdata[ 'sp_last_name' ] ),
			'sp_birthday'     => trim( $mp_valid_mdata[ 'sp_birthday' ] ),
			'sp_email'        => trim( $mp_valid_mdata[ 'sp_email' ] ),
			'sp_phone'        => trim( $mp_valid_mdata[ 'sp_phone' ] ),
			'sp_relationship' => trim( $mp_valid_mdata[ 'sp_relationship' ] ),
			'c1_first_name'   => trim( $mp_valid_mdata[ 'c1_first_name' ] ),
			'c1_last_name'    => trim( $mp_valid_mdata[ 'c1_last_name' ] ),
			'c1_birthday'     => trim( $mp_valid_mdata[ 'c1_birthday' ] ),
			'c1_email'        => trim( $mp_valid_mdata[ 'c1_email' ] ),
			'c1_relationship' => trim( $mp_valid_mdata[ 'c1_relationship' ] ),
			'c2_first_name'   => trim( $mp_valid_mdata[ 'c2_first_name' ] ),
			'c2_last_name'    => trim( $mp_valid_mdata[ 'c2_last_name' ] ),
			'c2_birthday'     => trim( $mp_valid_mdata[ 'c2_birthday' ] ),
			'c2_email'        => trim( $mp_valid_mdata[ 'c2_email' ] ),
			'c2_relationship' => trim( $mp_valid_mdata[ 'c2_relationship' ] ),
			'c3_first_name'   => trim( $mp_valid_mdata[ 'c3_first_name' ] ),
			'c3_last_name'    => trim( $mp_valid_mdata[ 'c3_last_name' ] ),
			'c3_birthday'     => trim( $mp_valid_mdata[ 'c3_birthday' ] ),
			'c3_email'        => trim( $mp_valid_mdata[ 'c3_email' ] ),
			'c3_relationship' => trim( $mp_valid_mdata[ 'c3_relationship' ] ),
			'c4_first_name'   => trim( $mp_valid_mdata[ 'c4_first_name' ] ),
			'c4_last_name'    => trim( $mp_valid_mdata[ 'c4_last_name' ] ),
			'c4_birthday'     => trim( $mp_valid_mdata[ 'c4_birthday' ] ),
			'c4_email'        => trim( $mp_valid_mdata[ 'c4_email' ] ),
			'c4_relationship' => trim( $mp_valid_mdata[ 'c4_relationship' ] ),
		);

		foreach ( $userdata as $meta_key => $meta_value ){
			update_user_meta( $user_id, $meta_key, $meta_value );
		}
		$user = wp_insert_user( $userdata );

		if ( is_wp_error( $user ) ) {
			$mp_reg_errors = $user;
			foreach ( $mp_reg_errors->get_error_messages() as $error ) {
				echo '<div>';
				echo '<strong>ERROR</strong>:';
				echo $error . '<br />';
				echo '</div>';
			}
		} else {
			$member = get_userdata( $user );
			echo 'Registration complete for ' . $member->mb_first_name . ' ' . $member->mb_last_name . '. Goto <a href="' . get_site_url() . '/wp-login.php">login page</a>.';
		}
	}
}

function custom_registration_function() {
	global $mp_valid_mdata, $mp_reg_errors;

	$mp_reg_errors = new WP_Error();

	if ( isset( $_POST[ 'submit' ] ) ) {

		$mp_valid_mdata = mp_validate_post_data( $_POST );

		// call @function complete_registration to create the user
		// only when no WP_error is found
		complete_registration();
	}

	mp_registration_form();
}

// Register the membership registration form


do_action( 'show_user_profile', $profileuser );
do_action( 'edit_user_profile', $profileuser );
do_action( 'user_edit_form_tag' );