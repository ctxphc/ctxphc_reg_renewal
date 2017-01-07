<?php
/**
 * Created by PhpStorm.
 * User: ken_kilgore1
 * Date: 12/29/2014
 * Time: 8:45 PM
 */

require_once( WP_ADMIN_DIR . 'admin.php' );

global $defSel, $wpdb;

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
?>

	<script type="text/javascript">
		jQuery(document).ready(function ($) {
			var $formID = $('#regForm');
			$($formID).validationEngine({promptPosition: "centerRight"});
			$($formID).validationEngine('attach');
		});
	</script>

	<script type="text/javascript">
		jQuery(document).ready(function ($) {
			$("#spouse_info").hide();
			$("#spouse_spacer").hide();
			$("#family_info").hide();
			$("#family_spacer").hide();
			$("input:radio[name$='memb_type']").click(function () {

				if ($("input[name$='memb_type']:checked").val() == 1) {
					$("#spouse_info").hide();
					$("#spouse_spacer").hide();
					$("#family_info").hide();
					$("#family_spacer").hide();
				} else if ($("input[name$='memb_type']:checked").val() == 2) {
					$("#spouse_info").hide();
					$("#spouse_spacer").hide();
					$("#family_info").show();
					$("#family_spacer").show();
				} else if ($("input[name$='memb_type']:checked").val() == 3) {
					$("#spouse_spacer").show();
					$("#spouse_info").show();
					$("#family_info").hide();
					$("#family_spacer").hide();
				} else if ($("input[name$='memb_type']:checked").val() == 4) {
					$("#spouse_info").show();
					$("#spouse_spacer").show();
					$("#family_info").show();
					$("#family_spacer").show();
				}
			});
		});
	</script>

	<h2>New Member</h2>

	<div class="spacer"></div>

	<form class="memb_reg_form" id="regForm" name="regForm" method="post" action="<?php plugins_url( 'add-new-member.php', __FILE__ ); ?>">
		<fieldset class="reg_form" id="memb_type">
			<legend><span class="memb_legend">Membership Options</span></legend>
			<div class="memb_type" id="memb_type">
				<!-- Individual Member Option -->
				<input class="memb_type" id=memb_type_1 type="radio" name="memb_type" value=1 checked/>
				<label class=memb_type for=memb_type_1>Individual $25</label>

				<!-- Individual + Child(ren) Member Option -->
				<input class="memb_type" id=memb_type_2 type="radio" name="memb_type" value=2/>
				<label class=memb_type for=memb_type_2>Individual + Children $30</label>

				<!-- Couple Member Option -->
				<input class="memb_type" id=memb_type_3 type="radio" name="memb_type" value=3/>
				<label class=memb_type for=memb_type_3>Couple $40</label>

				<!-- Household Member Option -->
				<input class="memb_type" id=memb_type_4 type="radio" name="memb_type" value=4/>
				<label class=memb_type for=memb_type_4>Household $45</label>
			</div>
		</fieldset>

		<div class="spacer"></div>

		<fieldset class="reg_form" id=personal_info>
			<legend><span class="memb_legend">Your Information</span></legend>
			<div class="reg_renewal_row" id="personal_info">
				<label class="reg_first_name" id="memb_first_name" for="fist_name">First Name:</label>
				<input class="reg_first_name validate[required, custom[onlyLetterSp]]"
				       data-prompt-position="bottomLeft" id="memb_fast_name" name="first_name" type=text
				       value="" title="first_name"/>

				<label class="reg_last_name" id="memb_last_name" for="memb_last_name">Last Name:</label>
				<input class="reg_last_name validate[required, custom[onlyLetterSp]]"
				       data-prompt-position="bottomLeft" id="memb_last_name" name="last_name" type=text
				       value=""/>
			</div>
			<div class="reg_renewal_row">
				<label class="reg_birthday" id="memb_birthday" for="b-day">Birthdate:</label>
				<select class="reg_birthday validate[required, custom[onlyNumber]]" id="memb_month"
				        data-prompt-position="bottomLeft" name="mb_month">
					<option value="MM" selected="selected">MM</option>
					<?php for ( $m = 1; $m <= 12; $m ++ ) {
						$m = str_pad( $m, 2, '0', STR_PAD_LEFT );
						echo "<option value='$m'>$m</option>";
					} ?>
				</select>
				<b>/</b>
				<select class="reg_birth_day validate[required, custom[onlyNumber]]" id="memb_day" data-prompt-position="bottomLeft"
				        name=mb_day>
					<option value="DD" selected="selected">DD</option>
					<?php for ( $d = 1; $d <= 31; $d ++ ) {
						$d = str_pad( $d, 2, '0', STR_PAD_LEFT );
						echo "<option value='$d'>$d</option>";
					} ?>
				</select>

				<label class="reg_email" id="memb_email" for=memb_email>Email:</label>
				<input class="reg_email validate[required, custom[email]]" data-prompt-position="bottomLeft"
				       id="memb_email" name="email" type="email"/>
			</div>
			<div class="reg_renewal_row">
				<label class="reg_phone" id="memb_phone" for="phone">Phone:</label>
				<input class="reg_phone validate[required, custom[onlyNumber]]"
				       data-prompt-position="bottomLeft"
				       id="memb_phone" name="phone1" type="text" maxlength=3/>-
				<input class="reg_phone2 validate[required, custom[onlyNumber]]"
				       data-prompt-position="bottomLeft"
				       id="memb_phone2" name="phone2" type="text" maxlength=3/>-
				<input class="reg_phone3 validate[required, custom[onlyNumber]]"
				       data-prompt-position="bottomLeft"
				       id="memb_phone3" name="phone3" type="text" maxlength=4/>

				<label id="memb_occu" for=memb_occu>Occupation:</label>
				<input id="memb_occu" class="validate[required,
					    custom[onlyLetterSp]]" data-prompt-position="bottomLeft" name=occu type=text value=""/>
			</div>
		</fieldset>

		<div class="spacer"></div>

		<fieldset class="reg_form" id="memb_address">
			<legend><span class="memb_legend">Address</span></legend>
			<div class="reg_renewal_row">
				<label id="memb_addr1" for=memb_addr1>Address:</label>
				<input id="memb_addr1" class="validate[required, custom[address]]" data-prompt-position="bottomLeft" name=addr1
				       type=text value=""/>

				<label id="memb_addr2" for=memb_addr2>Suite/Apt:</label>
				<input id="memb_addr2" name=addr2 type=text value="" class="validate[custom[onlyLetterNumber]]"
				       data-prompt-position="bottomLeft"/>
			</div>
			<div class="reg_renewal_row">
				<label id="memb_city" for=memb_city>City:</label>
				<input id="memb_city" class="validate[required, custom[onlyLetterSp]]" data-prompt-position="bottomLeft" name=city
				       type=text value=""/>

				<label id="memb_state" for=memb_state>State:</label>
				<select id="memb_state" name=state class="validate[required]">
					<?php $defSel = 'TX';
					echo showOptionsDrop( $states_arr, $defSel, true ); ?>
				</select>

				<label id="memb_zip" for=memb_zip>Zip:</label>
				<input id="memb_zip" class="validate[required, custom[zip-code]]" data-prompt-position="bottomLeft" name=zip type=text
				       value=""/>
			</div>
		</fieldset>

		<div class="spacer" ID="spouse_spacer"></div>

		<fieldset class="reg_form" id="spouse_info">
			<legend><span class="memb_legend">Spouse/Partner</span></legend>
			<div class="reg_renewal_row">
				<label class="reg_first_name" id="sp_first_name" for="sp_first_name">First Name:</label>
				<input class="reg_first_name validate[required,
                        custom[onlyLetterSp]]" data-prompt-position="bottomLeft" id="sp_first_name"
				       name="sp_first_name" type=text value=""/>
				<label class="reg_last_name" id="sp_last_name" for=sp_last_name>Last Name:</label>
				<input class="reg_last_name validate[required,
                        custom[onlyLetterSp]]" data-prompt-position="bottomLeft"
				       id="sp_last_name" name=sp_last_name type=text value=""/>
			</div>
			<div class="reg_renewal_row">
				<label class="reg_birthday" id="sp_birthday" for="spouseb-day">Birthdate:</label>
				<select class="reg_birthday validate[required, custom[onlyNumber]]" id="sp_month"
				        data-prompt-position="bottomLeft" name="sp_month">
					<option value="MM" selected="selected">MM</option>
					<?php for ( $m = 1; $m <= 12; $m ++ ) {
						$sm = str_pad( $m, 2, '0', STR_PAD_LEFT );
						echo "<option value='$sm'>$sm</option>";
					} ?>
				</select>
				<b>/</b>
				<select id="sp_day" class="reg_birth_day validate[required,
                    custom[onlyNumber]]" data-prompt-position="bottomLeft" name="sp_day">
					<option value="DD" selected="selected">DD</option>
					<?php for ( $d = 1; $d <= 31; $d ++ ) {
						$sd = str_pad( $d, 2, '0', STR_PAD_LEFT );
						echo "<option value='$sd'>$sd</option>";
					} ?>
				</select>

				<label class="reg_email" id="sp_email" for="sp_email">Email:</label>
				<input class="reg_email validate[custom[email]]" data-prompt-position="bottomLeft"
				       id="sp_email" name="sp_email" type="email" value=""/>
			</div>
			<div class="reg_renewal_row">
				<label class="reg_phone" id="sp_phone" for="sp_phone">Phone:</label>
				<input class="reg_phone validate[custom[onlyNumber]]" id="sp_phone" name="sp_phone1" type=text
				       value="" maxlength="3" data-prompt-position="bottomLeft"/>-
				<input class="reg_phone2 validate[custom[onlyNumber]]" id="sp_phone2" name="sp_phone2"
				       type=text value="" maxlength="3" data-prompt-position="bottomLeft"/>-
				<input class="reg_phone3 validate[custom[onlyNumber]]" id="sp_phone3" name="sp_phone3"
				       type=text value="" maxlength="4" data-prompt-position="bottomLeft"/>
				<label class="sp_relationship" id="sp_relationship" for="sp_relationship">Relationship:</label>
				<select id="sp_relationship" class="sp_relationship validate[required]" name="sp_relationship">
					<?php echo showOptionsDrop( $relationship_arr, null, true ); ?>
				</select>

			</div>
		</fieldset>

		<div class="spacer" id="family_spacer"></div>
		<!--    BEGIN 1ST FAMILY MEMBER  -->

		<fieldset class="reg_form" id=family_info>
			<legend><span class="memb_legend">Family Members</span></legend>
			<section id="child1">
				<div class="reg_renewal_row">
					<label class="reg_first_name" for=c1_first_name>First Name:</label>
					<input id="c1_first_name" class="reg_first_name validate[custom[onlyLetterSp]]" data-prompt-position="bottomLeft"
					       name=c1_first_name type=text value=""/>
					<label class=reg_last_name for=c1_last_name>Last Name:</label>
					<input class="reg_last_name validate[custom[onlyLetterSp]]" data-prompt-position="bottomLeft" id=c1_last_name
					       name=c1_last_name type=text value=""/></div>
				<div class="reg_renewal_row">
					<label class="reg_birthday" id=child1b-day for=child1b-day>Birthdate:</label>
					<select class="reg_birthday validate[custom[onlyNumber]]" data-prompt-position="bottomLeft"
					        id=c1_month name=c1_month>
						<option value="MM" selected="selected">MM</option>
						<?php for ( $m = 1; $m <= 12; $m ++ ) {
							$child1m = str_pad( $m, 2, '0', STR_PAD_LEFT );
							echo "<option value='$child1m'>$child1m</option>";
						} ?>
					</select>
					<b>/</b>
					<select class="reg_birth_day validate[custom[onlyNumber]]" data-prompt-position="bottomLef" t
					        id="c1_day" name="c1_day">
						<option value="DD" selected="selected">DD</option>
						<?php for ( $d = 1; $d <= 31; $d ++ ) {
							$child1d = str_pad( $d, 2, '0', STR_PAD_LEFT );
							echo "<option value='$child1d'>$child1d</option>";
						} ?>
					</select>

					<label class="child_relationship" id=c1_relationship for=c1_relationship>Relationship:</label>
					<select class="child_relationship" id=c1_relationship name=c1_relationship>
						<?php $defSel = 'C'; ?>
						<?php echo showOptionsDrop( $relationship_arr, "C", true ); ?>
					</select>
				</div>
				<div class="reg_renewal_row">
					<label class="child_email" id=c1_email for=c1_email>Email:</label>
					<input class="child_email validate[custom[email]]" data-prompt-position="bottomLeft" id=c1_email name=c1_email
					       type="email" value=""/>
				</div>
			</section>
			<!--  //END of CHILD1 -->

			<div class="spacer"></div>
			<!--    BEGIN 2ND FAMILY MEMBER  -->

			<section id="child2">
				<div class="reg_renewal_row">
					<label class="reg_first_name" id=c2_first_name for=c2_first_name>First Name:</label>
					<input class="reg_first_name validate[custom[onlyLetterSp]]" data-prompt-position="bottomLeft" id=c2_first_name
					       name=c2_first_name type=text value=""/>
					<label class="reg_last_name" id=c2_last_name for=c2_last_name>Last Name:</label>
					<input class="reg_last_name validate[custom[onlyLetterSp]]" data-prompt-position="bottomLeft" id=c2_last_name
					       name=c2_last_name type=text value=""/>
				</div>
				<div class="reg_renewal_row">
					<label class="reg_birthday" id=child2b-day for=child2b-day>Birthdate:</label>
					<select class="reg_birthday" id=c2_month name=c2_month>
						<option value="MM" selected="selected">MM</option>
						<?php for ( $m = 1; $m <= 12; $m ++ ) {
							$child2m = str_pad( $m, 2, '0', STR_PAD_LEFT );
							echo "<option value='$child2m'>$child2m</option>";
						} ?>
					</select>
					<b>/</b>
					<select class="reg_birth_day" id=c2_day name=c2_day>
						<option value="DD" selected="selected">DD</option>
						<?php for ( $d = 1; $d <= 31; $d ++ ) {
							$child2d = str_pad( $d, 2, '0', STR_PAD_LEFT );
							echo "<option value='$child2d'>$child2d</option>";
						} ?>
					</select>
					<label class="child_relationship" id=c2_relationship for=c2_relationship>Relationship:</label>
					<select class="child_relationship" id=c2_relationship name=c2_relationship>
						<?php $defSel = 'C'; ?>
						<?php echo showOptionsDrop( $relationship_arr, "C", true ); ?>
					</select>
				</div>
				<div class="reg_renewal_row">
					<label class="child_email" id=c2_email for=c2_email>Email:</label>
					<input class="child_email validate[custom[email]]" data-prompt-position="bottomLeft" id=c2_email name=c2_email
					       type="email" value=""/>
				</div>
			</section>
			<!--  //END CHILD2 -->

			<div class="spacer"></div>
			<!--    BEGIN 3RD FAMILY MEMBER  -->

			<section id="child3">
				<div class="reg_renewal_row">
					<label class="reg_first_name" id=c3_first_name for=c3_first_name>First Name:</label>
					<input class="reg_first_name validate[custom[onlyLetterSp]]" data-prompt-position="bottomLeft" id=c3_first_name
					       name=c3_first_name type=text value=""/>
					<label class="reg_last_name" id=c3_last_name for=c3_last_name>Last Name:</label>
					<input class="reg_last_name validate[custom[onlyLetterSp]]" data-prompt-position="bottomLeft" id=c3_last_name
					       name=c3_last_name type=text value=""/>
				</div>
				<div class="reg_renewal_row">
					<label class="reg_birthday" id=child3b-day for=child3b-day>Birthdate:</label>
					<select class="reg_birthday" id=c3_month name=c3_month>
						<option value="MM" selected="selected">MM</option>
						<?php for ( $m = 1; $m <= 12; $m ++ ) {
							$child3m = str_pad( $m, 2, '0', STR_PAD_LEFT );
							echo "<option value='$child3m'>$child3m</option>";
						} ?>
					</select>
					<b>/</b>
					<select class="reg_birth_day" id=c3_day name=c3_day>
						<option value="DD" selected="selected">DD</option>
						<?php for ( $d = 1; $d <= 31; $d ++ ) {
							$child3d = str_pad( $d, 2, '0', STR_PAD_LEFT );
							echo "<option value='$child3d'>$child3d</option>";
						} ?>
					</select>
					<label class="child_relationship" id=c3_relationship for=c3_relationship>Relationship:</label>
					<select class="child_relationship" id=c3_relationship name=c3_relationship>
						<?php $defSel = 'C'; ?>
						<?php echo showOptionsDrop( $relationship_arr, "C", true ); ?>
					</select>
				</div>
				<div class="reg_renewal_row">
					<label class="child_email" id=c3_email for=c3_email>Email:</label>
					<input class="child_email validate[custom[email]]" data-prompt-position="bottomLeft" id=c3_email name=c3_email
					       type="email" value=""/>
				</div>
			</section>
			<!--  //END of CHILD3  -->

			<div class="spacer"></div>
			<!--    BEGIN 4th FAMILY MEMBER  -->

			<section id="child4">
				<div class="reg_renewal_row">
					<label class="reg_first_name" id=c4_first_name for=c4_first_name>First Name:</label>
					<input class="reg_first_name validate[custom[onlyLetterSp]]" data-prompt-position="bottomLeft" id=c4_first_name
					       name=c4_first_name type=text value=""/>
					<label class="reg_last_name" id=c4_last_name for=c4_last_name>Last Name:</label>
					<input class="reg_last_name validate[custom[onlyLetterSp]]" data-prompt-position="bottomLeft" id=c4_last_name
					       name=c4_last_name type=text value=""/>
				</div>
				<div class="reg_renewal_row">
					<label class="reg_birthday" id=child4b-day for=child4b-day>Birthdate:</label>
					<select class="reg_birthday" id=c4_month name=c4_month>
						<option value="MM" selected="selected">MM</option>
						<?php for ( $m = 1; $m <= 12; $m ++ ) {
							$child4m = str_pad( $m, 2, '0', STR_PAD_LEFT );
							echo "<option value='$child4m'>$child4m</option>";
						} ?>
					</select>
					<b>/</b>
					<select class="reg_birth_day" id=c4_day name=c4_day>
						<option value="DD" selected="selected">DD</option>
						<?php for ( $d = 1; $d <= 31; $d ++ ) {
							$child4d = str_pad( $d, 2, '0', STR_PAD_LEFT );
							echo "<option value='$child4d'>$child4d</option>";
						} ?>
					</select>
					<label class="child_relationship" id=c4_relationship for=c4_relationship>Relationship:</label>
					<select class="child_relationship" id=c4_relationship name=c4_relationship>
						<?php $defSel = 'C'; ?>
						<?php echo showOptionsDrop( $relationship_arr, "C", true ); ?>
					</select>
				</div>
				<div class="reg_renewal_row">
					<label class="child_email" id=c4_email for=c4_email>Email:</label>
					<input class="child_email validate[custom[email]]" data-prompt-position="bottomLeft" id=c4_email name=c4_email
					       type="email" value=""/>
				</div>
			</section>
			<!--  //END of CHILD3  -->
		</fieldset>

		<div class="spacer"></div>
		<!--    BEGIN PREFERENCES  -->

		<div>
			<input class="ctxphc_button3 screen" id="reg_submit" type="submit" name="reg_submit" value="Submit"/>
		</div>

	</form>
<?php