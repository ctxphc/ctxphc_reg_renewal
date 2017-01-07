<?php
/**
 * Created by PhpStorm.
 * User: ken_kilgore1
 * Date: 2/25/2015
 * Time: 1:23 PM
 */

namespace Membership\includes\classes;


class CM_Membership_Profile {

	public function __construct( ){
		/**
		 * Display Membership Registration Fields
		 */
		add_action( 'register_form', array( $this, 'cm_register_form' ) );

		/**
		 * Validate Membership Registration Fields
		 */
		add_filter( 'registration_errors', array( $this, 'cm_registration_validation', 10, 3 ) );

		/**
		 * Save Membership Registration Fields
		 */
		add_action( 'user_register', array( $this, 'cm_register_member' ) );

		/**
		 * Display Club Membership Profile page
		 *
		 * displays for the current user or any user that can edit the user
		 * as well as for New Members/Users.
		 */
		add_action( 'show_user_profile', array( $this, 'cm_add_membership_profile_fields' ) );  // Current User
		add_action( 'edit_user_profile', array( $this, 'cm_add_membership_profile_fields' ) );  //  Any User
		add_action( 'user_new_form', array( $this, 'cm_add_membership_profile_fields' ) );  //  New User page


		/**
		 * Save Membership Profile Page Updates
		 */
		add_action( 'personal_options_update', array( $this, 'cm_save_membership_profile_fields' ) );
		add_action( 'edit_user_profile_update', array( $this, 'cm_save_membership_profile_fields' ) );
	}


	/**
	 * Club Membership Custom Registration Fields
	 */
	function cm_register_form() {

		$first_name = ( ! empty( $_POST['first_name'] ) ) ? trim( $_POST['first_name'] ) : '';

		?>
		<p>
			<label for="first_name"><?php _e( 'First Name', 'mydomain' ) ?><br />
				<input type="text" name="first_name" id="first_name" class="input" value="<?php echo esc_attr( wp_unslash( $first_name ) ); ?>" size="25" /></label>
		</p>

		<h2 xmlns="http://www.w3.org/1999/html">Membership Profile Information</h2>

		<div class="cm-admin-spacer"></div>
		<div class="cm-admin-spacer"></div>

		<legend class="cm-legend"><span class="cm-legend">Membership Type</span></legend>
		<div class="cm-admin-spacer"></div>

		<?php $this->cm_display_membership_types(); ?>

		<div class="cm-admin-spacer"></div>
		<div class="cm-admin-spacer"></div>

		<legend class="cm-legend"><span class="cm-legend">Personal Information</span></legend>
		<div class="cm-admin-spacer"></div>

		<?php $this->cm_display_primary_member_fields(); ?>

		<div class="cm-admin-spacer"></div>

		<legend class="cm-legend"><span class="cm-legend">Address</span></legend>
		<div class="cm-admin-spacer"></div>

		<?php $this->cm_display_address_fields(); ?>

		<div class="cm-admin-spacer" id="spouse_spacer"></div>
		<div class="cm-admin-spacer"></div>

		<legend class="cm-legend"><span class="cm-legend">Spouse/Partner</span></legend>
		<div class="cm-admin-spacer"></div>

		<?php $this->cm_display_spouse_fields(); ?>

		<div class="cm-admin-spacer" id="family_spacer"></div>
		<div class="cm-admin-spacer"></div>

		<legend class="cm-legend"><span class="cm-legend">Family Members</span></legend>
		<div class="cm-admin-spacer"></div>

		<?php $this->cm_display_child1_fields(); ?>

		<div class="cm-admin-spacer"></div>

		<?php $this->cm_display_child2_fields(); ?>

		<div class="cm-admin-spacer"></div>

		<?php $this->cm_display_child3_fields(); ?>

		<div class="cm-admin-spacer"></div>

		<?php $this->cm_display_child4_fields(); ?>

		<div class="cm-admin-spacer"></div>

	<?php
	}


	/**
	 * Add validation for the Club Membership Custom Registration Fields
	 *
	 * @param $errors
	 * @param $sanitized_user_login
	 * @param $user_email
	 *
	 * @return mixed
	 */
	function cm_registration_validation( $errors, $sanitized_user_login, $user_email ) {

		if ( empty( $_POST['first_name'] ) || ! empty( $_POST['first_name'] ) && trim( $_POST['first_name'] ) == '' ) {
			$errors->add( 'first_name_error', __( '<strong>ERROR</strong>: You must include a first name.', 'mydomain' ) );
		}

		if ( ! preg_match( '/[0-9]{5}/', $_POST[ 'zipcode' ] ) || ! preg_match( '/[0-9]{5}-[0-9]{4}/', $_POST['zipcode' ]) ) {
			$errors->add( 'zipcode_error', __( '<strong>ERROR</strong>: Invalid ZIP code.', 'my_textdomain' ) );
		}


		return $errors;
	}


	/**
	 * Saving Club Membership Custom Registration Field Data
	 *
	 * @param $user_id
	 */
	function cm_register_member( $user_id ) {
		if ( ! empty( $_POST['first_name'] ) ) {
			update_user_meta( $user_id, 'first_name', trim( $_POST['first_name'] ) );
		}
	}




	/**
	 * Display Profile Fields
	 *
	 * @param $user
	 */
	function cm_add_membership_profile_fields( $user ) {
		?>

		<h2 xmlns="http://www.w3.org/1999/html">Membership Profile Information</h2>

		<div class="cm-admin-spacer"></div>
		<div class="cm-admin-spacer"></div>

		<legend class="cm-legend"><span class="cm-legend">Membership Type</span></legend>
		<div class="cm-admin-spacer"></div>

		<?php $this->cm_display_membership_types(); ?>

		<div class="cm-admin-spacer"></div>
		<div class="cm-admin-spacer"></div>

		<legend class="cm-legend"><span class="cm-legend">Personal Information</span></legend>
		<div class="cm-admin-spacer"></div>

		<?php $this->cm_display_primary_member_fields(); ?>

		<div class="cm-admin-spacer"></div>

		<legend class="cm-legend"><span class="cm-legend">Address</span></legend>
		<div class="cm-admin-spacer"></div>

		<?php $this->cm_display_address_fields(); ?>

		<div class="cm-admin-spacer" id="spouse_spacer"></div>
		<div class="cm-admin-spacer"></div>

		<legend class="cm-legend"><span class="cm-legend">Spouse/Partner</span></legend>
		<div class="cm-admin-spacer"></div>

		<?php $this->cm_display_spouse_fields(); ?>

		<div class="cm-admin-spacer" id="family_spacer"></div>
		<div class="cm-admin-spacer"></div>

		<legend class="cm-legend"><span class="cm-legend">Family Members</span></legend>
		<div class="cm-admin-spacer"></div>

		<?php $this->cm_display_child1_fields(); ?>

		<div class="cm-admin-spacer"></div>

		<?php $this->cm_display_child2_fields(); ?>

		<div class="cm-admin-spacer"></div>

		<?php $this->cm_display_child3_fields(); ?>

		<div class="cm-admin-spacer"></div>

		<?php $this->cm_display_child4_fields(); ?>

		<div class="cm-admin-spacer"></div>
	<?php
	}



	/**
	 *
	 * Save New and/or Updated profile information
	 *
	 * @param $user_id
	 *
	 * @return bool
	 */
	function cm_save_membership_profile_fields( $user_id ) {

		if ( ! current_user_can( 'edit_user', $user_id ) ) {
			return false;
		}

		$members_data = $this->cm_separate_post_data( $user_id );

		foreach ( $members_data as $member ){
			switch ( $member->data_rec_type ){
				case 'memb_type':
					$this->cm_save_membership_types( $user_id, $member );
					break;
				case 'primary':
					$this->cm_save_primary_member_fields( $user_id, $member );
					break;
				case 'address':
					$this->cm_save_address_fields( $user_id, $member );
					break;
				case 'spouse':
					$this->cm_save_spouse_fields( $user_id, $member );
					break;
				case 'child1':
					$this->cm_save_child1_fields( $user_id, $member );
					break;
				case 'child2':
					$this->cm_save_child2_fields( $user_id, $member );
					break;
				case 'child3':
					$this->cm_save_child3_fields( $user_id, $member );
					break;
				case 'child4':
					$this->cm_save_child4_fields( $user_id, $member );
					break;

			}
		}
		return true;
	}


	function cm_display_membership_types() {

		/** @var int $cost */
		$memb_costs = get_membership_pricing();
		?>
		<input type="hidden" name="data_rec_type" value="memb_type" />
		<table class="cm-form-table">
			<tr>
				<!-- Individual Member Option -->
				<td>
					<input class="cm-memb-type" id="memb_type_1" type="radio" name="memb_type" value="1" checked/>
					<label class="cm-memb-type" for="memb_type_1">Individual $<?php echo $memb_costs[ 1 ]->cost; ?></label>

					<!-- Individual + Child(ren) Member Option -->
					<input class="cm-memb-type" id="memb_type_2" type="radio" name="memb_type" value="2"/>
					<label class="cm-memb-type" for="memb_type_2">Individual + Children $<?php echo $memb_costs[ 2 ]->cost; ?></label>

					<!-- Couple Member Option -->
					<input class="cm-memb-type" id="memb_type_3" type="radio" name="memb_type" value="3"/>
					<label class="cm-memb-type" for="memb_type_3">Couple $<?php echo $memb_costs[ 3 ]->cost; ?></label>

					<!-- Household Member Option -->
					<input class="cm-memb-type" id="memb_type_4" type="radio" name="memb_type" value="4"/>
					<label class="cm-memb-type" for="memb_type_4">Household $<?php echo $memb_costs[ 4 ]->cost; ?></label>
				</td>
			</tr>
		</table>
	<?php
	}

	function cm_display_primary_member_fields() {
		?>
		<input type="hidden" name="sp_relationship" value="1"/>
		<input type="hidden" name="data_rec_type" value="primary" />
		<table class="cm-form-table">
			<tr class="cm_profile_labels">
				<td class="cm-profile-col1">
					<label class="cm_birthday form-required" id="lbl_mb_birthday" for="mb_birthday">Birthdate:</label>
				</td>
				<td class="cm-profile-col2">
					<label class="reg_phone form-required" id="lbl_mb_phone" for="mb_phone">Phone:</label>
				</td>
			</tr>
			<tr class="cm_profile_inputs" >
				<td class="cm-profile-col1 form-required">
					<input class="cm_birthday validate[required, custom[onlyNumber]]" data-prompt-position="bottomLeft"
					       id="mb_birthday" name="mb_birthday" type="date" value=""/>
				</td>
				<td class="cm-profile-col2 form-required">
					<input class="reg_phone validate[required, custom[onlyNumber]]" data-prompt-position="bottomLeft"
					       id="mb_phone" name="mb_phone" type="tel"/>
				</td>
			</tr>
			<tr class="cm_profile_labels">
				<td class="cm-profile-col1">
					<label id="lbl_mb_occupation" for="mb_occupation">Occupation:</label>
				</td>
			</tr>
			<tr>
				<td class="cm-profile-col1">
					<input class="validate[required, custom[onlyLetterSp]]" data-prompt-position="bottomLeft"
					       id="mb_occupation" name="mb_occupation" type="text" value=""/>
				</td>
			</tr>
		</table>
	<?php
	}

	function cm_display_address_fields() {
		global $defSel;
		$states_array = get_states_array();
		?>
		<input type="hidden" name="data_rec_type" value="address" />
		<table>
			<tr class="cm_profile_labels">
				<td class="cm-profile-col1">
					<label id="lbl_mb_addr1" for="mb_addr1">Address:</label>
				</td>
				<td class="cm-profile-col2">
					<label id="lbl_mb_addr2" for="mb_addr2">Suite/Apt:</label>
				</td>
			</tr>
			<tr class="cm_profile_inputs">
				<td class="cm-profile-col1">
					<input class="validate[required, custom[address]]" data-prompt-position="bottomLeft"
					       id="mb_addr1" name="mb_addr1" type="text" value=""/>
				</td>
				<td class="cm-profile-col2">
					<input class="validate[custom[onlyLetterNumber]]" data-prompt-position="bottomLeft"
					       id="mb_addr2" name="mb_addr2" type="text" value=""/>
				</td>
			</tr>
			<tr class="cm_profile_labesl">
				<td class="cm-profile-col1">
					<label id="lbl_mb_city" for="mb_city">City:</label>
				</td>
				<td class="cm-profile-col2">
					<label id="lbl_mb_state" for="mb_state">State:</label>
				</td>
				<td class="cm-profile-col3">
					<label id="lbl_mb_zip" for="mb_zip">Zip:</label>
				</td>
			</tr>
			<tr class="cm_profile_inputs">
				<td class="cm-profile-col1">
					<input class="validate[required, custom[onlyLetterSp]]" data-prompt-position="bottomLeft"
					       id="mb_city" name="mb_city" type="text" value=""/>
				</td>
				<td class="cm-profile-col2">
					<select class="validate[required]" id="mb_state" name="mb_state">
						<?php $defSel = 'TX';
						echo showOptionsDrop( $states_array, $defSel, true ); ?>
					</select>
				</td>
				<td class="cm-profile-col3">
					<input class="validate[required, custom[zip-code]]" data-prompt-position="bottomLeft"
					       id="mb_zip" name="mb_zip" type="text" value=""/>
				</td>
			</tr>
		</table>
	<?php
	}


	function cm_display_spouse_fields() {
		$rel_array = get_relationship_array();
		?>
		<input type="hidden" name="data_rec_type" value="spouse" />
		<table class="cm-form-table">
			<tr>
				<td class="cm-profile-col1">
					<label class="reg_first_name" id="lbl_sp_first_name" for="sp_first_name">First Name:</label>
				</td>
				<td class="cm-profile-col2">
					<label class="reg_last_name" id="lbl_sp_last_name" for="sp_last_name">Last Name:</label>
				</td>
			</tr>
			<tr>
				<td class="cm-profile-col1">
					<input class="reg_first_name validate[required, custom[onlyLetterSp]]" data-prompt-position="bottomLeft"
					       id="sp_first_name" name="sp_first_name" type="text" value=""/>
				</td>
				<td class="cm-profile-col2">
					<input class="reg_last_name validate[required, custom[onlyLetterSp]]" data-prompt-position="bottomLeft"
					       id="sp_last_name" name="sp_last_name" type="text" value=""/>
				</td>
			</tr>
			<tr>
				<td class="cm-profile-col1">
					<label class="cm_birthday" id="lbl_sp_birthday" for="sp_birthday">Birthdate:</label>
				</td>
				<td class="cm-profile-col2">
					<label class="reg_email" id="lbl_sp_email" for="sp_email">Email:</label>
				</td>
			</tr>
			<tr>
				<td class="cm-profile-col1">
					<input class="cm_birthday validate[required, custom[onlyNumber]]" id="sp_birthday"
					       data-prompt-position="bottomLeft" name="sp_birthday" type="date"/>
				</td>
				<td class="cm-profile-col2">
					<input class="reg_email validate[custom[email]]" data-prompt-position="bottomLeft"
					       id="sp_email" name="sp_email" type="email" value=""/>
				</td>
			</tr>
			<tr>
				<td class="cm-profile-col1">
					<label class="reg_phone" id="lbl_sp_phone" for="sp_phone">Phone:</label>
				</td>
				<td class="cm-profile-col2">
					<label class="sp_relationship" id="lbl_sp_relationship" for="sp_relationship">Relationship:</label>
				</td>
			</tr>
			<tr>
				<td class="cm-profile-col1">
					<input class="reg_phone validate[custom[onlyNumber]]" data-prompt-position="bottomLeft"
					       id="sp_phone" name="sp_phone" type="tel" value=""/>
				</td>
				<td class="cm-profile-col2">
					<select class="sp_relationship validate[required]" id="sp_relationship" name="sp_relationship">
						<?php $defSel = 2 ?>
						<?php echo showOptionsDrop( $rel_array, $defSel, true ); ?>
					</select>
				</td>
			</tr>
		</table>
	<?php
	}

	function cm_display_child1_fields() {
		$rel_array = get_relationship_array();
		?>
		<input type="hidden" name="data_rec_type" value="child1" />
		<table class="cm-form-table">
			<tr>
				<td class="cm-profile-col1">
					<label class="reg_first_name" for="c1_first_name">First Name:</label>
				</td>
				<td class="cm-profile-col2">
					<label class="reg_last_name" for="c1_last_name">Last Name:</label>
				</td>
			</tr>
			<tr>
				<td class="cm-profile-col1">
					<input class="reg_first_name validate[custom[onlyLetterSp]]" data-prompt-position="bottomLeft"
					       id="c1_first_name" name="c1_first_name" type="text" value=""/>
				</td>
				<td class="cm-profile-col2">
					<input class="reg_last_name validate[custom[onlyLetterSp]]" data-prompt-position="bottomLeft"
					       id="c1_last_name" name="c1_last_name" type="text" value=""/>
				</td>
			</tr>
			<tr>
				<td class="cm-profile-col1">
					<label class="cm_birthday" id="lbl_c1_birthday" for="c1_birthday">Birthdate:</label>
				</td>
				<td class="cm-profile-col2">
					<label class="child_relationship" id="lbl_c1_relationship" for="c1_relationship">Relationship:</label>
				</td>
			</tr>
			<tr>
				<td class="cm-profile-col1">
					<input class="reg_birth_month validate[custom[onlyNumber]]" data-prompt-position="bottomLeft"
					       id="c1_birthday" name="c1_birthday" type="date"/>
				</td>
				<td class="cm-profile-col2">
					<select class="child_relationship" id="c1_relationship" name="c1_relationship">
						<?php $defSel = 4 ?>
						<?php echo showOptionsDrop( $rel_array, $defSel, true ); ?>
					</select>
				</td>
			</tr>
			<tr>
				<td class="cm-profile-col1">
					<label class="child_email" id="lbl_c1_email" for="c1_email">Email:</label>
				</td>
			</tr>
			<tr>
				<td class="cm-profile-col1" rowspan="2">
					<input class="child_email validate[custom[email]]" data-prompt-position="bottomLeft"
					       id="c1_email" name="c1_email" type="email" value=""/>
				</td>
			</tr>
		</table>
		<!--  //END of CHILD1 -->
	<?php
	}

	function cm_display_child2_fields() {
		$rel_array = get_relationship_array();
		?>
		<input type="hidden" name="data_rec_type" value="child2" />
		<table class="cm-form-table">
			<tr>
				<td class="cm-profile-col1">
					<label class="reg_first_name" for="c1_first_name">First Name:</label>
				</td>
				<td class="cm-profile-col2">
					<label class="reg_last_name" for="c1_last_name">Last Name:</label>
				</td>
			</tr>
			<tr>
				<td class="cm-profile-col1">
					<input class="reg_first_name validate[custom[onlyLetterSp]]" data-prompt-position="bottomLeft"
					       id="c1_first_name" name="c1_first_name" type="text" value=""/>
				</td>
				<td class="cm-profile-col2">
					<input class="reg_last_name validate[custom[onlyLetterSp]]" data-prompt-position="bottomLeft"
					       id="c1_last_name" name="c1_last_name" type="text" value=""/>
				</td>
			</tr>
			<tr>
				<td class="cm-profile-col1">
					<label class="cm_birthday" id="lbl_c2_birthday" for="c1_birthday">Birthdate:</label>
				</td>
				<td class="cm-profile-col2">
					<label class="child_relationship" id="lbl_c1_relationship" for="c1_relationship">Relationship:</label>
				</td>
			</tr>
			<tr>
				<td class="cm-profile-col1">
					<input class="reg_birth_month validate[custom[onlyNumber]]" data-prompt-position="bottomLeft"
					       id="c1_birthday" name="c1_birthday" type="date"/>
				</td>
				<td class="cm-profile-col2">
					<select class="child_relationship" id="c1_relationship" name="c1_relationship">
						<?php $defSel = 4 ?>
						<?php echo showOptionsDrop( $rel_array, $defSel, true ); ?>
					</select>
				</td>
			</tr>
			<tr>
				<td class="cm-profile-col1">
					<label class="child_email" id="lbl_c1_email" for="c1_email">Email:</label>
				</td>
			</tr>
			<tr>
				<td class="cm-profile-col1" rowspan="2">
					<input class="child_email validate[custom[email]]" data-prompt-position="bottomLeft"
					       id="c1_email" name="c1_email" type="email" value=""/>
				</td>
			</tr>
		</table>
		<!--  //END of CHILD2 -->
	<?php
	}

	function cm_display_child3_fields() {
		$rel_array = get_relationship_array();
		?>
		<input type="hidden" name="data_rec_type" value="child3" />
		<table class="cm-form-table">
			<tr>
				<td class="cm-profile-col1">
					<label class="reg_first_name" for="c3_first_name">First Name:</label>
				</td>
				<td class="cm-profile-col2">
					<label class="reg_last_name" for="c3_last_name">Last Name:</label>
				</td>
			</tr>
			<tr>
				<td class="cm-profile-col1">
					<input class="reg_first_name validate[custom[onlyLetterSp]]" data-prompt-position="bottomLeft"
					       id="c3_first_name" name="c3_first_name" type="text" value=""/>
				</td>
				<td class="cm-profile-col2">
					<input class="reg_last_name validate[custom[onlyLetterSp]]" data-prompt-position="bottomLeft"
					       id="c3_last_name" name="c3_last_name" type="text" value=""/>
				</td>
			</tr>
			<tr>
				<td class="cm-profile-col1">
					<label class="cm_birthday" id="lbl_c3_birthday" for="c3_birthday">Birthdate:</label>
				</td>
				<td class="cm-profile-col2">
					<label class="child_relationship" id="lbl_c3_relationship" for="c3_relationship">Relationship:</label>
				</td>
			</tr>
			<tr>
				<td class="cm-profile-col1">
					<input class="reg_birth_month validate[custom[onlyNumber]]" data-prompt-position="bottomLeft"
					       id="c3_birthday" name="c3_birthday" type="date"/>
				</td>
				<td class="cm-profile-col2">
					<select class="child_relationship" id="c3_relationship" name="c3_relationship">
						<?php $defSel = 4 ?>
						<?php echo showOptionsDrop( $rel_array, $defSel, true ); ?>
					</select>
				</td>
			</tr>
			<tr>
				<td class="cm-profile-col1">
					<label class="child_email" id="lbl_c3_email" for="c3_email">Email:</label>
				</td>
			</tr>
			<tr>
				<td class="cm-profile-col1" rowspan="2">
					<input class="child_email validate[custom[email]]" data-prompt-position="bottomLeft"
					       id="c3_email" name="c3_email" type="email" value=""/>
				</td>
			</tr>
		</table>
		<!--  //END of CHILD3 -->
	<?php
	}

	function cm_display_child4_fields() {
		$rel_array = get_relationship_array();
		?>
		<input type="hidden" name="data_rec_type" value="child4" />
		<table class="cm-form-table">
			<tr>
				<td class="cm-profile-col1">
					<label class="reg_first_name" for="c4_first_name">First Name:</label>
				</td>
				<td class="cm-profile-col2">
					<label class="reg_last_name" for="c4_last_name">Last Name:</label>
				</td>
			</tr>
			<tr>
				<td class="cm-profile-col1">
					<input class="reg_first_name validate[custom[onlyLetterSp]]" data-prompt-position="bottomLeft"
					       id="c4_first_name" name="c4_first_name" type="text" value=""/>
				</td>
				<td class="cm-profile-col2">
					<input class="reg_last_name validate[custom[onlyLetterSp]]" data-prompt-position="bottomLeft"
					       id="c4_last_name" name="c4_last_name" type="text" value=""/>
				</td>
			</tr>
			<tr>
				<td class="cm-profile-col1">
					<label class="cm_birthday" id="lbl_c4_birthday" for="c4_birthday">Birthdate:</label>
				</td>
				<td class="cm-profile-col2">
					<label class="child_relationship" id="lbl_c4_relationship" for="c4_relationship">Relationship:</label>
				</td>
			</tr>
			<tr>
				<td class="cm-profile-col1">
					<input class="reg_birth_month validate[custom[onlyNumber]]" data-prompt-position="bottomLeft"
					       id="c4_birthday" name="c4_birthday" type="date"/>
				</td>
				<td class="cm-profile-col2">
					<select class="child_relationship" id="c4_relationship" name="c4_relationship">
						<?php $defSel = 4 ?>
						<?php echo showOptionsDrop( $rel_array, $defSel, true ); ?>
					</select>
				</td>
			</tr>
			<tr>
				<td class="cm-profile-col1">
					<label class="child_email" id="lbl_c4_email" for="c4_email">Email:</label>
				</td>
			</tr>
			<tr>
				<td class="cm-profile-col1" rowspan="2">
					<input class="child_email validate[custom[email]]" data-prompt-position="bottomLeft"
					       id="c4_email" name="c4_email" type="email" value=""/>
				</td>
			</tr>
		</table>
		<!--  //END of CHILD4 -->
	<?php
	}

	function cm_separate_post_data( $user_id ){
		global $primary, $spouse, $child1, $child2, $child3, $child4;
		$primary = new stdClass();
		$spouse = new stdClass();
		$child1 = new stdClass();
		$child2 = new stdClass();
		$child3 = new stdClass();
		$child4 = new stdClass();

		$valid_post_data = $this->cm_validate_membership_profile_data();

		switch ( $valid_post_data['data_rec_type']){
			case 'memb_type':
				$primary->memb_type = intval( $_POST['memb_type'] );
				break;
			case 'primary':
				$primary->member_id = intval( $user_id );
			$primary->birthday = $valid_post_data['mb_birthday'];
			$primary->phone = $valid_post_data['mb_phone'];
	}
		foreach ( $_POST as $post_key => $post_value ) {

			switch ( $post_value ){

			}

		}
	}

	function cm_validate_membership_profile_data(){

	}

	function cm_save_primary_member_fields( $user_id, $member_data ){
		foreach ( $_POST as $post_key => $post_value ) {
			/* Copy and paste this line for additional fields. Make sure to change 'twitter' to the field ID. */
			update_user_meta( absint( $user_id ), $post_key, wp_kses_post( $post_value ) );
		}
	}

	function cm_save_address_fields( $user_id, $member_data ){

	}

	function cm_save_spouse_fields( $user_id, $member_data ){

	}

	function cm_save_child1_fields( $user_id, $member_data ){

	}

	function cm_save_child2_fields( $user_id, $member_data ){

	}

	function cm_save_child3_fields( $user_id, $member_data ){

	}

	function cm_save_child4_fields( $user_id, $member_data ){

	}

}