<?php

/**
 * Created by PhpStorm.
 * User: ken_kilgore1
 * Date: 2/21/2015
 * Time: 2:59 PM
 */
class MP_Member_Registration {

	public $states_array = array();
	public $pricing;
	private $rel_array;
	private $states_arr;

	public function construc() {

		$this->states_array = $this->get_states_array();
		$this->rel_array    = $this->get_rel_array();
		$this->pricing      = $this->get_membership_pricing();

	}

	private function get_states_array() {
		$states_array = array(
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

		return $states_array;
	}

	private function get_rel_array() {
		$rel_array = array( '2' => "Spouse", '3' => "Partner", '4' => "Child", '5' => "Other" );

		return $rel_array;
	}


	/**
	 * @param $rel_id
	 *
	 * @return wpdb
	 */
	private function get_membership_pricing() {
		/** @var wpdb $wpdb */
		global $wpdb;
		$cost       = $wpdb->get_results( "SELECT cost FROM ctxphc_membership_pricing" );
		$type_count = count( $cost );

		for ( $x = 1, $y = 0; $x <= $type_count; $x ++, $y ++ ) {
			$pricing[ $x ] = $cost[ $y ];
		}

		return $pricing;
	}


	/**
	 *
	 */
	private function reg_from() {


		?>
		<div class="spacer"></div>

		<div class="reg_form_row" id="mem_reg_types">
			<h4>Membership Types:</h4>
			<ul id="memb_reg_list">
				<li>Individual -
					$<?php echo $this->pricing[ 1 ]->cost; ?></li>
				<li>Individual + Child -
					$<?php echo $this->pricing[ 2 ]->cost; ?></li>
				<li>Couples -
					$<?php echo $this->pricing[ 3 ]->cost; ?></li>
				<li>Household -
					$<?php echo $this->pricing[ 4 ]->cost; ?></li>
			</ul>
		</div>
		<form class="memb_reg_form" id="regForm" name="memb_reg_form" method="post" action="<?php bloginfo( 'url' ); ?>/registration-review/">
			<input type="hidden" name="mb_relationship" value=<?php if ( isset( $form_meta_data[ 'mb' ][ 'relationship_id' ] ) ) { echo $form_meta_data['mb']['relationship_id']; } else { echo 1; } ?>/>
			<fieldset class="reg_form" id="memb_type">
				<legend><span class="memb_legend">Membership Options</span></legend>
				<div class="memb_type" id="memb_type_div">
					<!-- Individual Member Option -->
					<input class="memb_type" id="memb_type_1" type="radio" name="memb_type" value="1" checked/>
					<label class="memb_type" for="memb_type_1">Individual $<?php echo $this->pricing[ 1 ]->cost; ?></label>

					<!-- Individual + Child(ren) Member Option -->
					<input class="memb_type" id="memb_type_2" type="radio" name="memb_type" value="2"/>
					<label class="memb_type" for="memb_type_2">Individual + Children $<?php echo $this->pricing[ 2 ]->cost; ?></label>

					<!-- Couple Member Option -->
					<input class="memb_type" id="memb_type_3" type="radio" name="memb_type" value="3"/>
					<label class="memb_type" for="memb_type_3">Couple $<?php echo $this->pricing[ 3 ]->cost; ?></label>

					<!-- Household Member Option -->
					<input class="memb_type" id="memb_type_4" type="radio" name="memb_type" value="4"/>
					<label class="memb_type" for="memb_type_4">Household $<?php echo $this->pricing[ 4 ]->cost; ?></label>
				</div>
			</fieldset>

			<div class="spacer"></div>

			<fieldset class="reg_form" id="personal_info">
				<legend><span class="memb_legend">Your Information</span></legend>
				<div class="reg_form_row" id="personal_info_div">
					<label class="reg_first_name" id="lbl_mb_first_name" for="mb_first_name">First Name:</label>
					<input class="reg_first_name validate[required, custom[onlyLetterSp]]"
					       data-prompt-position="bottomLeft" id="mb_fast_name" name="mb_first_name" type="text"
					       value="<?php if ( isset( $form_meta_data[ 'mb' ][ 'first_name' ] ) ) { echo $form_meta_data['mb']['first_name']; } ?>" title="first_name"/>

					<label class="reg_last_name" id="lbl_mb_last_name" for="mb_last_name">Last Name:</label>
					<input class="reg_last_name validate[required, custom[onlyLetterSp]]"
					       data-prompt-position="bottomLeft" id="mb_last_name" name="mb_last_name" type="text"
					       value=""/>
				</div>
				<div class="reg_form_row">
					<label class="cm_birthday" id="lbl_mb_birthday" for="mb_birthday">Birthdate:</label>
					<input class="cm_birthday validate[required, custom[onlyNumber]]"
					       data-prompt-position="bottomLeft" id="mb_birthday" name="mb_birthday" type="date" value=""/>

					<label class="reg_email" id="lbl_mb_email" for="mb_email">Email:</label>
					<input class="reg_email validate[required, custom[email]]" data-prompt-position="bottomLeft"
					       id="mb_email" name="mb_email" type="email" value=""/>
				</div>
				<div class="reg_form_row">
					<label class="reg_phone" id="lbl_mb_phone" for="mb_phone">Phone:</label>
					<input class="reg_phone validate[required, custom[onlyNumber]]"
					       data-prompt-position="bottomLeft" id="mb_phone" name="mb_phone" type="tel"/>

					<label id="lbl_mb_occupation" for="mb_occupation">Occupation:</label>
					<input class="validate[required, custom[onlyLetterSp]]" data-prompt-position="bottomLeft"
					       id="mb_occupation" name="mb_occupation" type="text" value=""/>
				</div>
			</fieldset>

			<div class="spacer"></div>

			<fieldset class="reg_form" id="mb_address">
				<legend><span class="memb_legend">Address</span></legend>
				<div class="reg_form_row">
					<label id="lbl_mb_addr1" for="mb_addr1">Address:</label>
					<input class="validate[required, custom[address]]" data-prompt-position="bottomLeft"
					       id="mb_addr1" name="mb_addr1" type="text" value=""/>

					<label id="lbl_mb_addr2" for="mb_addr2">Suite/Apt:</label>
					<input class="validate[custom[onlyLetterNumber]]" data-prompt-position="bottomLeft"
					       id="mb_addr2" name="mb_addr2" type="text" value=""/>
				</div>
				<div class="reg_form_row">
					<label id="lbl_mb_city" for="mb_city">City:</label>
					<input class="validate[required, custom[onlyLetterSp]]" data-prompt-position="bottomLeft"
					       id="mb_city" name="mb_city" type="text" value=""/>

					<label id="lbl_mb_state" for="mb_state">State:</label>
					<select class="validate[required]" id="mb_state" name="mb_state">
						<?php $defSel = 'TX';
						echo showOptionsDrop( $this->states_arr, $defSel, true ); ?>
					</select>

					<label id="lbl_mb_zip" for="mb_zip">Zip:</label>
					<input id="mb_zip" class="validate[required, custom[zip-code]]"
					       data-prompt-position="bottomLeft" name="mb_zip" type="text" value=""/>
				</div>
			</fieldset>

			<div class="spacer" id="spouse_spacer"></div>

			<fieldset class="reg_form" id="spouse_info">
				<legend><span class="memb_legend">Spouse/Partner</span></legend>
				<div class="reg_form_row">
					<label class="reg_first_name" id="lbl_sp_first_name" for="sp_first_name">First Name:</label>
					<input class="reg_first_name validate[required, custom[onlyLetterSp]]"
					       data-prompt-position="bottomLeft" id="sp_first_name" name="sp_first_name" type="text"
					       value=""/>

					<label class="reg_last_name" id="lbl_sp_last_name" for="sp_last_name">Last Name:</label>
					<input class="reg_last_name validate[required, custom[onlyLetterSp]]"
					       data-prompt-position="bottomLeft" id="sp_last_name" name="sp_last_name" type="text"
					       value=""/>
				</div>
				<div class="reg_form_row">
					<label class="cm_birthday" id="lbl_sp_birthday" for="sp_birthday">Birthdate:</label>
					<input class="cm_birthday validate[required, custom[onlyNumber]]" id="sp_birthday"
					       data-prompt-position="bottomLeft" name="sp_birthday" type="date"/>

					<label class="reg_email" id="lbl_sp_email" for="sp_email">Email:</label>
					<input class="reg_email validate[custom[email]]" data-prompt-position="bottomLeft"
					       id="sp_email" name="sp_email" type="email" value=""/>
				</div>
				<div class="reg_form_row">
					<label class="reg_phone" id="lbl_sp_phone" for="sp_phone">Phone:</label>
					<input class="reg_phone validate[custom[onlyNumber]]" data-prompt-position="bottomLeft"
					       id="sp_phone" name="sp_phone" type="tel" value=""/>

					<label class="sp_relationship" id="lbl_sp_relationship"
					       for="sp_relationship">Relationship:</label>
					<select class="sp_relationship validate[required]"
					        id="sp_relationship" name="sp_relationship">
						<?php $defSel = 2 ?>
						<?php echo showOptionsDrop( $this->rel_array, $defSel, true ); ?>
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
						       value=""/>

						<label class="reg_last_name" for="c1_last_name">Last Name:</label>
						<input class="reg_last_name validate[custom[onlyLetterSp]]"
						       data-prompt-position="bottomLeft" id="c1_last_name" name="c1_last_name" type="text"
						       value=""/></div>
					<div class="reg_form_row">
						<label class="cm_birthday" id="lbl_c_birthday" for="c1_birthday">Birthdate:</label>
						<input class="reg_birth_month validate[custom[onlyNumber]]"
						       data-prompt-position="bottomLeft" id="c1_birthday" name="c1_birthday" type="date"/>

						<label class="child_relationship" id="lbl_c1_relationship"
						       for="c1_relationship">Relationship:</label>
						<select class="child_relationship" id="c1_relationship" name="c1_relationship">
							<?php $defSel = 4 ?>
							<?php echo showOptionsDrop( $this->rel_array, $defSel, true ); ?>
						</select>
					</div>
					<div class="reg_form_row">
						<label class="child_email" id="lbl_c1_email" for="c1_email">Email:</label>
						<input class="child_email validate[custom[email]]" data-prompt-position="bottomLeft"
						       id="c1_email" name="c1_email" type="email" value=""/>
					</div>
				</section>
				<!--  //END of CHILD1 -->

				<div class="spacer"></div>
				<!--    BEGIN 2ND FAMILY MEMBER  -->

				<section id="child2">
					<div class="reg_form_row">
						<label class="reg_first_name" id="lbl_c2_first_name" for="c2_first_name">First Name:</label>
						<input class="reg_first_name validate[custom[onlyLetterSp]]"
						       data-prompt-position="bottomLeft" id="c2_first_name" name="c2_first_name" type="text"
						       value=""/>

						<label class="reg_last_name" id="lbl_c2_last_name" for="c2_last_name">Last Name:</label>
						<input class="reg_last_name validate[custom[onlyLetterSp]]"
						       data-prompt-position="bottomLeft" id="c2_last_name" name="c2_last_name" type="text"
						       value=""/>
					</div>
					<div class="reg_form_row">
						<label class="cm_birthday" id="lbl_c2_birthday" for="c2_birthday">Birthdate:</label>
						<input class="cm_birthday" id="c2_birthday" name="c2_birthday" type="date"/>

						<label class="child_relationship" id="lbl_c2_relationship"
						       for="c2_relationship">Relationship:</label>
						<select class="child_relationship" id="c2_relationship" name="c2_relationship">
							<?php $defSel = 4; ?>
							<?php echo showOptionsDrop( $this->rel_array, $defSel, true ); ?>
						</select>
					</div>
					<div class="reg_form_row">
						<label class="child_email" id="lbl_c2_email" for="c2_email">Email:</label>
						<input class="child_email validate[custom[email]]" data-prompt-position="bottomLeft"
						       id="c2_email" name="c2_email" type="email" value=""/>
					</div>
				</section>
				<!--  //END CHILD2 -->

				<div class="spacer"></div>
				<!--    BEGIN 3RD FAMILY MEMBER  -->

				<section id="child3">
					<div class="reg_form_row">
						<label class="reg_first_name" id="lbl_c3_first_name" for="c3_first_name">First Name:</label>
						<input class="reg_first_name validate[custom[onlyLetterSp]]"
						       data-prompt-position="bottomLeft" id="c3_first_name" name="c3_first_name" type="text"
						       value=""/>

						<label class="reg_last_name" id="lbl_c3_last_name" for="c3_last_name">Last Name:</label>
						<input class="reg_last_name validate[custom[onlyLetterSp]]"
						       data-prompt-position="bottomLeft" id="c3_last_name" name="c3_last_name" type="text"
						       value=""/>
					</div>
					<div class="reg_form_row">
						<label class="cm_birthday" id="lbl_c3_birthday" for="c3_birthday">Birthdate:</label>
						<input class="cm_birthday" id="c3_birthday" name="c3_birthday" type="date"/>

						<label class="child_relationship" id="lbl_c3_relationship" for="c3_relationship">Relationship:</label>
						<select class="child_relationship" id="c3_relationship" name="c3_relationship">
							<?php $defSel = 4; ?>
							<?php echo showOptionsDrop( $this->rel_array, $defSel, true ); ?>
						</select>
					</div>
					<div class="reg_form_row">
						<label class="child_email" id="lbl_c3_email" for="c3_email">Email:</label>
						<input class="child_email validate[custom[email]]" data-prompt-position="bottomLeft"
						       id="c3_email" name="c3_email" type="email" value=""/>
					</div>
				</section>
				<!--  //END of CHILD3  -->

				<div class="spacer"></div>
				<!--    BEGIN 4th FAMILY MEMBER  -->

				<section id="child4">
					<div class="reg_form_row">
						<label class="reg_first_name" id="lbl_c4_first_name" for="c4_first_name">First Name:</label>
						<input class="reg_first_name validate[custom[onlyLetterSp]]"
						       data-prompt-position="bottomLeft" id="c4_first_name" name="c4_first_name" type="text"
						       value=""/>

						<label class="reg_last_name" id="lbl_c4_last_name" for="c4_last_name">Last Name:</label>
						<input class="reg_last_name validate[custom[onlyLetterSp]]"
						       data-prompt-position="bottomLeft" id="c4_last_name" name="c4_last_name" type="text"
						       value=""/>
					</div>
					<div class="reg_form_row">
						<label class="cm_birthday" id="lbl_c4_birthday" for="c4_birthday">Birthdate:</label>
						<input class="cm_birthday" id="c4_birthday" name="c4_birthday" type="date"/>

						<label class="child_relationship" id="lbl_c4_relationship"
						       for="c4_relationship">Relationship:</label>
						<select class="child_relationship" id="c4_relationship" name="c4_relationship">
							<?php $defSel = 4; ?>
							<?php echo showOptionsDrop( $this->rel_array, $defSel, true ); ?>
						</select>
					</div>
					<div class="reg_form_row">
						<label class="child_email" id="lbl_c4_email" for="c4_email">Email:</label>
						<input class="child_email validate[custom[email]]" data-prompt-position="bottomLeft"
						       id="c4_email" name="c4_email" type="email" value=""/>
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

}