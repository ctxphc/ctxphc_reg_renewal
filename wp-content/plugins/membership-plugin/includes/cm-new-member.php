<?php
/**
 * Created by PhpStorm.
 * User: ken_kilgore1
 * Date: 2/26/2015
 * Time: 9:23 AM
 */

/** WordPress Administration Bootstrap */
require_once( ABSPATH . 'wp-admin/admin.php' );

if ( ! current_user_can( 'create_users' ) ) {
	wp_die( __( 'Cheatin&#8217; uh?' ), 403 );
}
$messages = array();
global $messages;

/**
 * @return bool
 */
function cm_validate_passwords() {
	$pass1 = $_POST[ 'pass1' ];
	$pass2 = $_POST[ 'pass2' ];

	$hash1 = wp_hash_password( $pass1 );
	$hash2 = wp_hash_password( $pass2 );

	return ( $hash1 === $hash2 ? true : false );
}

/**
 * @param $user_id
 */
function cm_update_extra_profile_fields( $user_id ) {
	global $errors;

	if ( current_user_can( 'edit_user' ) ) {
		process_registration();
		echo $errors;
	}
}

if ( $_SERVER[ 'REQUEST_METHOD' ] === "POST" ) {
	global $errors, $messages;

	/*
	$result = cm_validate_passwords(); skipping for now as this is only used from the admin screen.

	if ( false === $result ){
		//reload page with passwords whipped.
	}
	*/

	$primary_id  = false;
	$proc_spouse = false;
	$proc_child  = false;

	/** @var int $memb_costs */
	$memb_costs = get_membership_pricing();

	//Take POST data and separate it member relationship: primary, spouse, child1, child2...
	$raw_members_data = cm_separate_post_data();  //returns array of objects


	$kid_count = 0;
	foreach ( $raw_members_data as $member_key => $member_data ) {
		if ( 'prime' == $member_key ) {
			$args = array(
				'member_key' => $member_key,
				'member_arr' => $member_data,
				'errors'     => $errors,
			);

			$clean_members_data[ $member_key ] = cm_clean_member_data( $args );
		} elseif ( 'spouse' == $member_key ) {
			$args = array(
				'spouse_key' => $member_key,
				'member_arr' => $member_data,
				'errors'     => $errors,
			);

			if ( '' !== $member_data[ 'first_name' ] ){
				$clean_members_data[ $member_key ] = cm_clean_spouse_data( $args );
			}

		} elseif ( 'child' == $member_key ) {
			foreach ( $member_data as $child_key => $child_data ) {
				$kid_count ++;
				if ( '' !== $child_data[ 'first_name' ] ) {
					$args = array(
						'child_key'  => $child_key,
						'member_arr' => $child_data,
						'errors'     => $errors,
					);

					$clean_members_data[ $member_key . '_' . $kid_count ] = cm_clean_child_data( $args );
				}
			}
		}
	}


	foreach ( $clean_members_data as $mb_key => $mb_data ) {
		if ( 'child' == $mb_key ) {
			foreach ( $member as $ch_key => $ch_data ) {
				if ( isset( $ch_data[ 'proc' ] ) && true == $ch_data ) {
					$cm_processed[ $ch_key ] = cm_process_record( $ch_data, $ch_key );
				}
			}
		} else {
			$cm_processed[ $mb_key] = cm_process_record( $mb_data );
		}
	}


/*
	$memb_cost    = $memb_costs[ $cm_processed[ 'metadata' ]->membership_type ];
	$mb_id        = $cm_processed[ 'userdata' ][ 'mb_id' ];
	$memb_type_id = $cm_processed[ 'mb' ][ 'membership_type' ];

	if ( isset( $cm_processed[ 'mb' ][ 'sp_id' ] ) ) {
		$sp_id = $cm_processed[ 'mb' ][ 'sp_id' ];
	}
	if ( isset( $cm_processed[ 'mb' ][ 'c1_id' ] ) ) {
		$c1_id = $cm_processed[ 'mb' ][ 'c1_id' ];
	}
	if ( isset( $cm_processed[ 'mb' ][ 'c2_id' ] ) ) {
		$c2_id = $cm_processed[ 'mb' ][ 'c2_id' ];
	}
	if ( isset( $cm_processed[ 'mb' ][ 'c3_id' ] ) ) {
		$c3_id = $cm_processed[ 'mb' ][ 'c3_id' ];
	}
	if ( isset( $cm_processed[ 'mb' ][ 'c4_id' ] ) ) {
		$c4_id = $cm_processed[ 'mb' ][ 'c4_id' ];
	}

	switch ( $memb_type_id ) {
		case 1: //ID - Individual
			?>
			<script type="text/javascript">
				jQuery(document).ready(
					function () {
						jQuery("#cm_spouse").css("display", "none");
						jQuery("#spouse_spacer").css("display", "none");
						jQuery("#cm_family").css("display", "none");
						jQuery("#family_spacer").css("display", "none");
					}
				);
			</script>
			<?php
			break;

		case 2: //IC - Individual + child
			$proc_child = true;
			?>
			<script type="text/javascript">
				jQuery(document).ready(
					function () {
						jQuery("#cm_spouse").css("display", "none");
						jQuery("#spouse_spacer").css("display", "none");
						jQuery("#cm_family").css("display", "block");
						jQuery("#family_spacer").css("display", "block");
					}
				);
			</script>
			<?php
			break;

		case 3: //CO - Couple
			$proc_spouse = true;
			?>
			<script type="text/javascript">
				jQuery(document).ready(
					function () {
						jQuery("#cm_spouse").css("display", "block");
						jQuery("#spouse_spacer").css("display", "block");
						jQuery("#cm_family").css("display", "none");
						jQuery("#family_spacer").css("display", "none");
					}
				);
			</script>
			<?php
			break;
		case 4: //HH - Household
			$proc_spouse = true;
			?>
			<script type="text/javascript">
				jQuery(document).ready(
					function () {
						jQuery("#cm_spouse").css("display", "block");
						jQuery("#spouse_spacer").css("display", "block");
						jQuery("#cm_family").css("display", "block");
						jQuery("#family_spacer").css("display", "block");
					}
				);
			</script>
			<?php
			break;
	} //End of switch on memb_type_id
*/
} //END of Server Method = POST


//add_action( 'edit_user_profile_update', 'cm_update_extra_profile_fields' );


$title       = __( 'Add New Member' );
$parent_file = 'admin.php?page=membership_dashboard';

$help = '<p>' . __( 'To add a new member to your site, fill in the form on this screen and click the Add New Member button at the bottom.' ) . '</p>';

$help .= '<p>' . __( 'You must assign a password to the new member, which they can change after logging in. The username, however, cannot be changed.' ) . '</p>' .
         '<p>' . __( 'New member will receive an email letting them know they&#8217;ve been added as a member for your site. By default, this email will also contain their password. Uncheck the box if you don&#8217;t want the password to be included in the welcome email.' ) . '</p>';


$help .= '<p>' . __( 'Remember to click the Add New Member button at the bottom of this screen when you are finished.' ) . '</p>';

$screen = get_current_screen();
$screen->add_help_tab( array(
		'id'      => 'overview',
		'title'   => __( 'Overview' ),
		'content' => $help,
	)
);

$help_member_roles = '<p>' . __( 'Here is a basic overview of the different Member roles and the permissions associated with each one:' ) . '</p>' .
                     '<ul>' .
                     '<li>' . __( 'Subscribers can read comments/comment/receive newsletters, etc. but cannot create regular site content.' ) . '</li>' .
                     '<li>' . __( 'Contributors can write and manage their posts but not publish posts or upload media files.' ) . '</li>' .
                     '<li>' . __( 'Authors can publish and manage their own posts, and are able to upload files.' ) . '</li>' .
                     '<li>' . __( 'Editors can publish posts, manage posts as well as manage other people&#8217;s posts, etc.' ) . '</li>' .
                     '<li>' . __( 'Board Members can perform all functions below as well as manage members, including add, edit and archive members.' ) . '</li>' .
                     '<li>' . __( 'Administrators have access to all the administration features.' ) . '</li>' .
                     '</ul>';

$screen->add_help_tab( array(
		'id'      => 'user-roles',
		'title'   => __( 'Member Roles' ),
		'content' => $help_member_roles,
	)
);


require_once( ABSPATH . 'wp-admin/admin-header.php' ); ?>

	<div class="wrap">
		<h2 id="add_new_user"> <?php
			if ( current_user_can( 'create_users' ) ) {
				echo _x( 'Add New Member', 'membership' );
			} ?>
		</h2>

		<?php if ( isset( $errors ) && ! empty( $errors->errors ) && is_wp_error( $errors ) ) { ?>
			<div class="error">
				<ul>
					<?php
					foreach ( $errors->get_error_messages() as $err ) {
						echo "<li>$err</li>\n";
					}
					?>
				</ul>
			</div>
		<?php }

		if ( ! empty( $messages ) ) {
			foreach ( $messages as $msg ) {
				echo '<div class="updated notice is-dismissible" id="message"><p>' . $msg . '</p></div>';
			}
		}

		if ( isset( $add_user_errors ) && is_wp_error( $add_user_errors ) ) { ?>
			<div class="error">
				<?php
				foreach ( $add_user_errors->get_error_messages() as $message ) {
					echo "<p>$message</p>";
				}
				?>
			</div>
		<?php }

		if ( current_user_can( 'create_users' ) ) { ?>
			<p><?php _e( 'Create a brand new member and add them to this site.' ); ?></p>
			<form action="" method="post" name="createmember" id="createmember"<?php
			do_action( 'user_new_form_tag', 'createmember' );
			?>>
				<input name="action" type="hidden" value="createmember"/>
				<?php wp_nonce_field( 'create-member', '_wpnonce_create-member' ); ?>


				<!--
				<div class="cm_admin_spacer"></div>

				<fieldset class="cm_form_section_header">
					<legend class="cm_section_header"><span class="cm_section_header">Username/Password</span></legend>
					<?php //cm_display_user_pass_fields(); ?>
				</fieldset>
				-->

				<div class="cm_admin_spacer"></div>
				<div class="cm_admin_spacer"></div>

				<fieldset class="cm_form_section_header" id="cm_membership_types">
					<legend class="cm_section_header"><span class="cm_section_header">Membership Type</span></legend>
					<div class="cm_admin_spacer"></div>
					<?php cm_display_membership_types(); ?>
				</fieldset>

				<div class="cm_admin_spacer"></div>
				<div class="cm_admin_spacer"></div>

				<fieldset class="cm_form_section_header" id="cm_primary">
					<legend class="cm_section_header"><span class="cm_section_header">Personal Information</span></legend>
					<div class="cm_admin_spacer"></div>
					<?php cm_display_primary_member_fields(); ?>
				</fieldset>

				<div class="cm_admin_spacer"></div>

				<fieldset class="cm_form_section_header" id="cm_address">
					<legend class="cm_section_header"><span class="cm_section_header">Address</span></legend>
					<div class="cm_admin_spacer"></div>
					<?php cm_display_address_fields(); ?>
				</fieldset>

				<div class="cm_admin_spacer" id="spouse_spacer"></div>

				<fieldset class="cm_form_section_header" id="cm_spouse">
					<legend class="cm_section_header"><span class="cm_section_header">Spouse/Partner</span></legend>
					<div class="cm_admin_spacer"></div>
					<?php cm_display_spouse_fields(); ?>
				</fieldset>

				<div class="cm_admin_spacer" id="family_spacer"></div>

				<fieldset class="cm_form_section_header" id="cm_family">
					<legend class="cm_section_header"><span class="cm_section_header">Family Members</span></legend>
					<div class="cm_admin_spacer" id="cm_c1_family_spacer"></div>
					<?php cm_display_child1_fields(); ?>

					<div class="cm_admin_spacer" id="cm_c2_family_spacer"></div>

					<?php cm_display_child2_fields(); ?>

					<div class="cm_admin_spacer" id="cm_c3_family_spacer"></div>

					<?php cm_display_child3_fields(); ?>

					<div class="cm_admin_spacer" id="cm_c4_family_spacer"></div>

					<?php cm_display_child4_fields(); ?>
				</fieldset>

				<div class="cm_admin_spacer"></div>

				<?php
				/** This action is documented in wp-admin/user-new.php */
				do_action( 'user_new_form', 'add-new-member' );
				?>

				<?php submit_button( __( 'Add New Member ' ), 'primary', 'createmember', true, array( 'id' => 'createmembersub' ) ); ?>

			</form>

		<?php } // current_user_can('create_users') ?>
	</div>
<?php

