<?php
/**
 * Created by PhpStorm.
 * User: ken_kilgore1
 * Date: 1/4/2015
 * Time: 11:36 AM
 *
 * @param $new_query
 */

function render_member_registration( $new_query ) {

	?>
	<div class="tabdivouter">

		<fieldset class="udashboard">
			<legend class="udashboard">User Registration</legend>
			<div class="udashboard tabdivinner-left">
				<div id="registration">

					<p class="message"><br>
					</p>

					<form method="post" action="<?php echo get_permalink( $new_query ); ?>" id="registrationform" name="registrationform">
						<p>
							<label for="user_login"><?php _e( 'Username:' ); ?><br>
								<input type="text" size="20"
								       value="<?php echo ( isset( $_REQUEST[ 'username' ] ) ) ? $_REQUEST[ 'username' ] : ''; ?>"
								       class="input" id="user_login" name="username"></label>
						</p>

						<p>
							<label for="user_email"><?php _e( 'Email:' ); ?><br>
								<input type="text" size="20"
								       value="<?php echo ( isset( $_REQUEST[ 'email' ] ) ) ? $_REQUEST[ 'email' ] : ''; ?>"
								       class="input" id="user_email" name="email"></label>
						</p>

						<p class="submit" style="margin-top: 15px;">
							<input type="submit" value="SignUp" class="button button-primary button-large" id="wp-submit"
							       name="wp-submit">
							<input type="button" value="Back" class="button button-primary button-large" name="back"
							       onclick="window.history.back()">
						</p>
					</form>
				</div>
			</div>
			<div class="tabdivinner-right" style="float:left; margin: 0 auto; width: 50%;">
				<img class="udashboard" height="187px" src="<?php echo IMGPATH; ?>palm-tree-icon-1.png" alt="Registration" title="Registration">
			</div>
		</fieldset>
	</div>
<?php }

function render_ud_profile( $new_query ) {
	global $current_user;

	$states = get_states_array();

	$relationships = get_relationship_array();

	$memb_costs = get_membership_pricing();

	$cost_count = 0;
	foreach ( $memb_costs as $memb_cost ) {
		$cost_count ++;
		$memb_price[ $cost_count ] = $memb_cost->cost;
	}

	$debug = false;
	$error = null;
	?>

	<div class="entry-content entry">
	<?php if ( ! is_user_logged_in() ) : ?>
		<p class="warning">
			<?php _e( 'You must be logged in to edit your profile.', 'profile' ); ?>
		</p><!-- .warning -->
	<?php else : ?>
		<?php if ( count( $error ) > 0 ) {
			echo '<p class="error">' . implode( "<br />", $error ) . '</p>';
		} ?>
		<form class="udashbaord" method="post" id="adduser" action="<?php echo get_permalink( $new_query ); ?>">
			<p class="renewal-row">
				<label class="ud_renewal_label_col2" id="ud-mb-first-name-lbl" for="ud-mb-first-name"><?php _e( 'First Name', 'renewal' ); ?></label>:
				<input class="ud-first-name" id="ud-mb-first-name" type="text" name="mb_first_name"
				       value="<?php _e( get_user_meta( $current_user->id, 'first_name' ), 'renewal' ); ?>">

				<label class="ud_renewal_label_col2" id="ud-mb-last-name-lbl" for="ud-mb-first-name"><?php _e( 'Last Name', 'renewal' ); ?></label>:
				<input class="ud-last-name" id="ud-mb-last-name" name="mb_first_name"
				       value="<?php _e( get_user_meta( $current_user->id, 'last_name' ), 'renewal' ); ?>"/>
			</p><!-- .form-username -->
			<p class="renewal-row">
				<label class="ud_renewal_label_col2" id="ud-mb-email" for="ud-mb-email"><?php _e( 'E-mail', 'renewal' ); ?></label>:
				<input class="ud-email" id="ud-mb-email" type="email" name="mb_email"
				       value="<?php _e( get_user_meta( $current_user->id, 'email' ), 'renewal' ); ?>"/>

				<label for="ud-mb-phone"><?php _e( 'Phone', 'renewal' ); ?></label>:
				<input class="ud-phone" id="ud-mb-phone" type="tel" <?php echo get_user_meta( $current_user->id, 'phone', true ); ?>
			</p><!-- .form-url -->
			<p class="renewal-row">
				<input class="memb_type" id="renew_memb_type_1" type="radio" name="memb_type" value=1
				       <?php if (1 == get_user_meta( $current_user->id, 'membership_id', true )) { ?>checked<?php }; ?>>
				<label class="mb-type-lbl" for="renew_memb_type_1">Individual - <?php echo "$" . $memb_price[ 1 ]; ?></label>

				<input class="memb_type" id="renew_memb_type_2" type="radio" name="memb_type" value=2
				       <?php if (2 == get_user_meta( $current_user->id, 'membership_id', true )) { ?>checked<?php }; ?>>
				<label class="mb-type-lbl" for="renew_memb_type_2">Individual + Children - <?php echo "$" . $memb_price[ 2 ]; ?></label>

				<input class="memb_type" id="renew_memb_type_3" type="radio" name="memb_type" value=3
				       <?php if (3 == get_user_meta( $current_user->id, 'membership_id', true )) { ?>checked<?php }; ?>>
				<label class="mb-type-lbl" for="renew_memb_type_3">Couple - <?php echo "$" . $memb_price[ 3 ]; ?></label>

				<input class="memb_type" id="renew_memb_type_4" type="radio" name="memb_type" value=4
				       <?php if (4 == get_user_meta( $current_user->id, 'membership_id', true )) { ?>checked<?php }; ?>>
				<label class="mb-type-lbl" for="renew_memb_type_4">Household - <?php echo "$" . $memb_price[ 4 ]; ?></label>
			</p>
		</form><!-- #adduser -->
		</div>

	<?php endif; ?>
	<!-- </div><!-- .entry-content -->
	<?php get_sidebar(); ?>
	<!-- start footer -->
	<?php get_footer(); ?>
	<!-- end footer -->
<?php
}

/**
 * @param $new_query
 * @param $mem_recs
 */
function render_membership_renewal( $new_query, $member_ids ) {

	$debug         = false;
	$error         = null;
	$proc_spouse   = false;
	$proc_children = false;

	$states = get_states_array();

	$relationships = get_relationship_array();

	$memb_costs = get_membership_pricing();

	$child_counter = 0;
	foreach ( $member_ids as $memb_id ) {
		$renewal_date = get_renewal_date();
		$rel_id = get_user_meta( $memb_id, 'relationship_id', true );

		switch ( $rel_id ) {
			case 1:
				$mb_id = $memb_id;
				break;
			case 2:
			case 3:
				$proc_spouse = true;
				$sp_id       = $memb_id;
				break;
			case 4:
			case 5:
				$child_counter ++;
				$child[ $child_counter ] = $memb_id;
				$proc_children           = true;
				break;
		}
	}


	if ( isset( $child[ 1 ] ) ) {
		$c1_id = $child[ 1 ];
	}
	if ( isset( $child[ 2 ] ) ) {
		$c2_id = $child[ 2 ];
	}
	if ( isset( $child[ 3 ] ) ) {
		$c3_id = $child[ 3 ];
	}
	if ( isset( $child[ 4 ] ) ) {
		$c4_id = $child[ 4 ];
	}

	$cost_count = 0;
	foreach ( $memb_costs as $memb_cost ) {
		$cost_count ++;
		$memb_price[ $cost_count ] = $memb_cost->cost;
	}

	?>

	<div class="entry-content entry">
		<?php if ( ! is_user_logged_in() ) { ?>
			<p class="warning">You must be logged in to renew your membership.</p><!-- .warning -->
		<?php } elseif ( count( $error ) > 0 ) {
			echo '<p class="error">' . implode( "<br />", $error ) . '</p>';
		} ?>

		<form method="post" id="renew_membership" action="<?php echo get_permalink( $new_query ); ?>">

			<fieldset class="renewal_form" id="renew_memb_type">
				<legend><span class="memb_legend">Membership Types</span></legend>
				<div class="ud_renewal_row" id="renew_memb_type">
					<input class="memb_type" id="renew_memb_type_1" type="radio" name="memb_type" value=1
					       <?php if (1 == get_user_meta( $mb_id, 'membership_id', true )) { ?>checked<?php }; ?>>
					<label for="renew_memb_type_1">Individual - <?php echo "$" . $memb_price[ 1 ]; ?></label>
					<input class="memb_type" id="renew_memb_type_2" type="radio" name="memb_type" value=2
					       <?php if (2 == get_user_meta( $mb_id, 'membership_id', true )) { ?>checked<?php }; ?>>
					<label for="renew_memb_type_2">Individual + Children - <?php echo "$" . $memb_price[ 2 ]; ?></label>
					<input class="memb_type" id="renew_memb_type_3" type="radio" name="memb_type" value=3
					       <?php if (3 == get_user_meta( $mb_id, 'membership_id', true )) { ?>checked<?php }; ?>>
					<label for="renew_memb_type_3">Couple - <?php echo "$" . $memb_price[ 3 ]; ?></label>
					<input class="memb_type" id="renew_memb_type_4" type="radio" name="memb_type" value=4
					       <?php if (4 == get_user_meta( $mb_id, 'membership_id', true )) { ?>checked<?php }; ?>>
					<label for="renew_memb_type_4">Household -
						<?php echo "$" . $memb_price[ 4 ]; ?></label>
				</div>
			</fieldset>

			<div class="spacer"></div>

			<fieldset class="ud-renewal_form" id="ud-personal_info">
				<legend><span class="memb_legend">Your Information</span></legend>
				<div class="ud_renewal_row" id="personal_info">
					<label class="ud_renewal_label_col1" id="ud-mb-first-name-lbl" for="ud-mb-first-name">First Name: </label>
					<input class="ud_renewal_input_col1" id="ud-mb-first-name" type="text" name="mb_first_name"
					       value="<?php if ( isset( $mb_id ) ) {
						       echo get_user_meta( $mb_id, 'first_name', true );
					       } ?>"/>

					<label class="ud_renewal_labe_col2l" id="ud-mb-last-name-lbl" for="ud-mb-last-name">Last Name: </label>
					<input class="ud_renewal_input_col2" id="ud-mb-last-name" type="text" name="mb_last_name"
					       value="<?php if ( isset( $mb_id ) ) {
						       echo get_user_meta( $mb_id, 'last_name', true );
					       } ?>"/>

				</div>
				<div class="ud_renewal_row">
					<label class="ud_renewal_label_col1" id="ud-mb-birthday-lbl" for="ud-mb-birthday">Birthdate: </label>
					<input class="ud_renewal_input_col1" id="ud-mb-birthday" type="text" name="mb_birthday"
					       value="<?php if ( isset( $mb_id ) ) {
						       echo get_user_meta( $mb_id, 'birthday', true );
					       } ?>"/>

					<label class="ud_renewal_label_col2" id="ud-mb-email-lbl" for="ud-mb-email">Email: </label>
					<input class="ud_renewal_input_col2" id="ud-mb-email" type="email" name="mb_email"
					       value="<?php if ( isset( $mb_id ) ) {
						       echo get_user_meta( $mb_id, 'email', true );
					       } ?>"/>
				</div>
				<div class="ud_renewal_row">
					<label class="ud-mb-phone" id="ud-mb-phone-lbl" for="ud-mb-phone">Phone: </label>
					<input class="ud-mb-phone" id="ud-mb-phone" type="tel" name="mb_phone"
					       value="<?php if ( isset( $mb_id ) ) {
						       echo get_user_meta( $mb_id, 'user_phone', true );
					       } ?>"/>

					<label class="ud_renewal_label_col2" id="ud-mb-occupation-label" for="ud-mb-occupation">Occupation: </label>
					<input class="ud_renewal_input_col2" id="ud-mb-occupation" type="text" name="mb_occupation"
					       value="<?php if ( isset( $mb_id ) ) {
						       echo get_user_meta( $mb_id, 'occupation', true );
					       } ?>"/>
				</div>
				<input type="hidden" value="<?php if ( isset( $mb_id ) ) {
					echo get_user_meta( $mb_id, 'user_id', true );
				} ?>" name="mb_id">
				<input type="hidden" value="<?php echo get_user_meta( $mb_id, 'relationship_id', true ) ?>" name="mb_relationship_id"/>
				<input type="hidden" value="<?php echo $renewal_date; ?>" name="mb_renewal_date" />
			</fieldset>

			<div class="spacer"></div>

			<fieldset class="renewal_form" id="renew_memb_address">
				<legend><span class="memb_legend">Address</span></legend>
				<div class="ud_renewal_row">
					<label class="ud_renewal_label_col1" id="ud-mb-addr1-lbl" for="ud-mb-addr1">Address: </label>
					<input class="ud_renewal_input_col2" id="ud-mb-addr1" type="text" name="mb_addr1"
					       value="<?php echo get_user_meta( $mb_id, 'addr1', true ); ?>"/>

					<label class="ud_renewal_label_col2" id="ud-mb-addr2-lbl" for="ud-mb-addr2">Suite/Apt: </label>
					<input class="ud_renewal_input_col2" id="ud-mb-addr2" type="text" name="mb_addr2"
					       value="<?php echo get_user_meta( $mb_id, 'addr2', true ); ?>"/>
				</div>
				<div class="ud_renewal_row">
					<label class="ud_renewal_label_col1" id="ud-mb-city-lbl" for="ud-mb-city">City: </label>
					<input class="ud_renewal_input_col1" id="ud-mb-city" type="text" name="mb_city"
					       value="<?php if ( isset( $mb_id ) ) {
						       echo get_user_meta( $mb_id, 'city', true );
					       } ?>"/>

					<label class="ud_renewal_label_col2" id="ud-mb-state-lbl" for="ud-mb-state">State: </label>
					<select class="ud-state" id="ud-mb-state" name="mb_state">
						<?php $defSel = 'TX';
						echo showOptionsDrop( $states, $defSel, true ); ?>
					</select>

					<label class="ud_renewal_label_col3" id="ud-mp-zip-lbl" for="ud-mb-zip">Zip: </label>
					<input class="ud_renewal_input_col3" id="ud-mb-zip" type="text" name="mb_zip"
					       value="<?php if ( isset( $mb_id ) ) {
						       echo get_user_meta( $mb_id, 'zip', true );
					       } ?>"/>
				</div>
			</fieldset>

			<div class="spacer" id="ud_spouse_spacer"></div>
			<?php if ( $proc_spouse ){ ?>
			<fieldset class="renewal_form" id="ud_spouse_info">
				<legend><span class="memb_legend">Spouse/Partner</span></legend>
				<input type="hidden" name="sp_id" value="<?php echo $sp_id; ?>"/>
				<input type="hidden" value="<?php echo $renewal_date; ?>" name="sp_renewal_date" />

				<div class="ud_renewal_row">
					<label class="ud_renewal_label_col1" id="sp-first-name-lbl" for="sp-first-name"><?php _e( 'First Name', 'renewal' ); ?>:</label>
					<input class="ud_renewal_input_col1" id="sp-first-name" name="sp_first_name"
					       value="<?php _e( get_user_meta( $sp_id, 'first_name', true ), 'renewal' ); ?>"/>

					<label class="ud_renewal_label_col2" id="sp-last-name-lbl" for="sp-last-name">Last Name:</label>
					<input class="ud_renewal_input_col2" id="sp-last-name" name="sp_last_name"
					       value=" <?php _e( get_user_meta( $sp_id, 'last_name', true ), 'renewal' ); ?>"/>
				</div>
				<div class="ud_renewal_row">
					<label class="ud_renewal_label_col1" id="sp-birthday-lbl" for="sp-birthday">Birthdate:</label>
					<input class="ud_renewal_input_col1" id="sp-birthday" name="sp_birthday"
					       value="<?php if ( isset( $sp_id ) ) {
						       echo get_user_meta( $sp_id, 'birthday', true );
					       } ?>">

					<label class="ud_renewal_label_col2" id="sp-email-lbl" for="sp-email">Email:</label>
					<input class="ud_renewal_input_col2" id="sp-email" name="sp_email" value="<?php if ( isset( $sp_id ) ) {
						echo get_user_meta( $sp_id, 'email', true );
					} ?>">
				</div>
				<div class="ud_renewal_row">
					<label class="ud_renewal_label_col1" id="sp-phone-lbl" for="sp-phone">Phone: </label>
					<input class="ud_renewal_input_col1" id="sp-phone" name="sp_phone" value="<?php if ( isset( $sp_id ) ) {
						echo get_user_meta( $sp_id, 'phone', true );
					} ?>"/>

					<label class="ud_renewal_label_col2" id="sp-relationship-lbl" for="sp-relationship">Relationship: </label>
					<select class="ud-renewal-select" id="sp-relationship" name="sp_relationship">
						<?php ( get_user_meta( $sp_id, 'relationship_id', true ) ) ? $defSel = get_user_meta( $sp_id, 'relationship_id', true ) : $defSel = 2;
						echo showOptionsDrop( $relationships, $defSel, true ); ?>
					</select>
				</div>
			</fieldset>
			<?php }

			if ( $proc_children ) { ?>

			<div class="spacer" id="ud_family_spacer"></div>

			<fieldset class="renewal_form" id="ud_family_info">
				<legend><span class="memb_legend">Family Members</span></legend>

				<input type="hidden" value="<?php if ( isset( $c1_id ) ) {
					echo get_user_meta( $c1_id, 'user_id', true );
				}; ?>" name="c1_id">
				<input type="hidden" value="<?php echo $renewal_date; ?>" name="c1_renewal_date" />

				<div class="ud_renewal_row">
					<label class="ud_renewal_label_col1" id="c1-first-name-lbl" for="c1-first-name">First Name: </label>
					<input class="ud_renewal_input_col1" id="c1-first-name" name="c1_first_name"
					       value="<?php if ( isset( $c1_id ) ) {
						       echo get_user_meta( $c1_id, 'first_name', true );
					       } ?>"/>

					<label class="ud_renewal_label_col2" id="c1-last-name-lbl" for="c1-last-name">Last Name:</label>
					<input class="ud_renewal_input_col2" id="c1-last-name" name="c1_last_name"
					       value="<?php if ( isset( $c1_id ) ) {
						       echo get_user_meta( $c1_id, 'last_name', true );
					       } ?>"/>
				</div>
				<div class="ud_renewal_row">
					<label class="ud_renewal_label_col1" id="c1-birthday-lbl" for="c1-birthday">Birthdate:</label>
					<input class="ud_renewal_input_col1" id="c1-birthday" name="c1_birthday"
					       value="<?php if ( isset( $c1_id ) ) {
						       echo get_user_meta( $c1_id, 'birthday', true );
					       } ?>"/>

					<label class="ud_renewal_label_col2" id="c1-relationship-lbl" for="c1-relationship">Relationship:</label>
					<select class="ud-renewal-select" id="c1-relationship" name="c1_relationship">
						<?php ( isset( $c1_id ) ) ? $defSel = get_user_meta( $c1_id, 'relationship_id', true ) : $defSel = 4;
						echo showOptionsDrop( $relationships, $defSel, true ); ?>
					</select>
				</div>
				<div class="ud_renewal_row">
					<label class="ud_renewal_label_col1" id="c1-email-lbl" for="c1-email">Email:</label>
					<input class="ud_renewal_input_col1" id="c1-email" name="c1_email" value="<?php if ( isset( $c1_id ) ) {
						echo get_user_meta( $c1_id, 'email', true );
					} ?>"/>
				</div>

				<div class="spacer"></div>

				<input type="hidden" value="<?php if ( isset( $c2_id ) ) {
					echo get_user_meta( $c2_id, 'user_id', true );
				} ?>" name="c2_id">
				<input type="hidden" value="<?php echo $renewal_date; ?>" name="c2_renewal_date" />

				<div class="ud_renewal_row">
					<label class="ud_renewal_label_col1" id="c2-first-name-lbl" for="c2-first-name">First Name: </label>
					<input class="ud_renewal_input_col1" id="c2-first-name" name="c2_first_name"
					       value="<?php if ( isset( $c2_id ) ) {
						       echo get_user_meta( $c2_id, 'first_name', true );
					       } ?>"/>

					<label class="ud_renewal_label_col2" id="c2-last-name-lbl" for="c2-last-name">Last Name:</label>
					<input class="ud_renewal_input_col2" id="c2-last-name" name="c2_last_name"
					       value="<?php if ( isset( $c2_id ) ) {
						       echo get_user_meta( $c2_id, 'last_name', true );
					       } ?>"/>
				</div>
				<div class="ud_renewal_row">
					<label class="ud_renewal_label_col1" id="c2-birthday-lbl" for="c2-birthday">Birthdate:</label>
					<input class="ud_renewal_input_col1" id="c2-birthday" name="c2_birthday"
					       value="<?php if ( isset( $c2_id ) ) {
						       echo get_user_meta( $c2_id, 'birthday', true );
					       } ?>"/>

					<label class="ud_renewal_label_col2" id="c2-relationship-lbl" for="c2-relationship">Relationship:</label>
					<select class="ud-renewal-select" id="c2-relationship" name="c2_relationship">
						<?php ( isset( $c2_id ) ) ? $defSel = get_user_meta( $c2_id, 'relationship_id', true ) : $defSel = 4;
						echo showOptionsDrop( $relationships, $defSel, true ); ?>
					</select>
				</div>
				<div class="ud_renewal_row">
					<label class="ud_renewal_label_col1" id="c2-email-lbl" for="c2-email">Email:</label>
					<input class="ud_renewal_input_col1" id="c2-email" name="c2_email" value="<?php if ( isset( $c2_id ) ) {
						echo get_user_meta( $c2_id, 'email', true );
					} ?>"/>
				</div>

				<div class="spacer"></div>

				<input type="hidden" value="<?php if ( isset( $c3_id ) ) {
					echo get_user_meta( $c3_id, 'user_id', true );
				} ?>" name="c3_id">
				<input type="hidden" value="<?php echo $renewal_date; ?>" name="c3_renewal_date" />

				<div class="ud_renewal_row">
					<label class="ud_renewal_label_col1" id="c3-first-name-lbl" for="c3-first-name">First Name: </label>
					<input class="ud_renewal_input_col1" id="c3-first-name" name="c3_first_name"
					       value="<?php if ( isset( $c3_id ) ) {
						       echo get_user_meta( $c3_id, 'first_name', true );
					       } ?>"/>

					<label class="ud_renewal_label_col2" id="c3-last-name-lbl" for="c3-last-name">Last Name:</label>
					<input class="ud_renewal_input_col2" id="c3-last-name" name="c3_last_name"
					       value="<?php if ( isset( $c3_id ) ) {
						       echo get_user_meta( $c3_id, 'last_name', true );
					       } ?>"/>
				</div>
				<div class="ud_renewal_row">
					<label class="ud_renewal_label_col1" id="c3-birthday-lbl" for="c3-birthday">Birthdate:</label>
					<input class="ud_renewal_input_col1" id="c3-birthday" name="c3_birthday"
					       value="<?php if ( isset( $c3_id ) ) {
						       echo get_user_meta( $c3_id, 'birthday', true );
					       } ?>"/>

					<label class="ud_renewal_label_col2" id="c3-relationship-lbl" for="c3-relationship">Relationship:</label>
					<select class="ud-renewal-select" id="c3-relationship" name="c3_relationship">
						<?php ( isset( $c3_id ) ) ? $defSel = get_user_meta( $c3_id, 'relationship_id', true ) : $defSel = 4;
						echo showOptionsDrop( $relationships, $defSel, true ); ?>
					</select>
				</div>
				<div class="ud_renewal_row">
					<label class="ud_renewal_label_col1" id="c3-email-lbl" for="c3-email">Email:</label>
					<input class="ud_renewal_input_col1" id="c3-email" name="c3_email" value="<?php if ( isset( $c3_id ) ) {
						echo get_user_meta( $c3_id, 'email', true );
					} ?>"/>
				</div>

				<div class="spacer"></div>

				<input type="hidden" value="<?php if ( isset( $c4_id ) ) {
					echo get_user_meta( $c4_id, 'user_id', true );
				} ?>" name="c4_id">
				<input type="hidden" value="<?php echo $renewal_date; ?>" name="c4_renewal_date" />

				<div class="ud_renewal_row">
					<label class="ud_renewal_label_col1" id="c4-first-name-lbl" for="c4-first-name">First Name: </label>
					<input class="ud_renewal_input_col1" id="c4-first-name" name="c4_first_name"
					       value="<?php if ( isset( $c4_id ) ) {
						       echo get_user_meta( $c4_id, 'first_name', true );
					       } ?>"/>

					<label class="ud_renewal_label_col2" id="c4-last-name-lbl" for="c4-last-name">Last Name:</label>
					<input class="ud_renewal_input_col2" id="c4-last-name" name="c4_last_name"
					       value="<?php if ( isset( $c4_id ) ) {
						       echo get_user_meta( $c4_id, 'last_name', true );
					       } ?>"/>
				</div>
				<div class="ud_renewal_row">
					<label class="ud_renewal_label_col1" id="c4-birthday-lbl" for="c4-birthday">Birthdate:</label>
					<input class="ud_renewal_input_col1" id="c4-birthday" name="c4_birthday"
					       value="<?php if ( isset( $c4_id ) ) {
						       echo get_user_meta( $c4_id, 'birthday', true );
					       } ?>"/>

					<label class="ud_renewal_label_col2" id="c4-relationship-lbl" for="c4-relationship">Relationship:</label>
					<select class="ud-renewal-select" id="c4-relationship" name="c4_relationship">
						<?php ( isset($c4_id) ) ? $defSel = get_user_meta( $c4_id, 'relationship_id', true ) : $defSel = 4;
						echo showOptionsDrop( $relationships, $defSel, true ); ?>
					</select>
				</div>
				<div class="ud_renewal_row">
					<label class="ud_renewal_label_col1" id="c4-email-lbl" for="c4-email">Email:</label>
					<input class="ud_renewal_input_col1" id="c4-email" name="c4_email" value="<?php if ( isset( $c4_id ) ) {
						echo get_user_meta( $c4_id, 'email', true );
					} ?>"/>
				</div>
			</fieldset>
				<?php } ?>

			<div class="spacer"></div>

			<div class="spacer"></div>

			<div id="reg_update">
				<fieldset class="screen">
					<legend class="screen"><span class="memb_legend">Update</span></legend>
					<div class="screen">
						If you {have} made any changes to the form please click:
					</div>
					<div>
						<input class="ctxphc_button3 screen" id="renewal-update" type="submit" name="renewal-update" value="update" onclick=""/>
					</div>
				</fieldset>
			</div>
		</form>

		<div class="paypal">
			<?php // SANDBOX PAYPAL TESTING
			?>
			<?php if ($debug === "true") {  // Using PayPal's SANDBOX server for payment testing ?>
				<form action="https://www.sandbox.paypal.com/cgi-bin/webscr" method="post" target="_top">
					<input type="hidden" name="cmd" value="_s-xclick">
					<input type="hidden" name="hosted_button_id" value="LGBNWGLFHB8XY">
					<input type="hidden" name="item_name" value="RENEWAL"/>
					<input type="hidden" name="item-number" value="<?php echo "CTXPHC-" . get_user_meta( $mb_id, 'membership_type', true ); ?>"/>
					<input type="hidden" name="amount" value="<?php echo $memb_price[ get_user_meta( $mb_id, 'membership_id', true ) ]; ?>"/>
					<input type="hidden" name="custom" value="<?php if ( isset( $mb_id ) ) {
						echo get_user_meta( $mb_id, 'user_id', true );
					} ?>"/>
					<input type="image" src="https://www.sandbox.paypal.com/en_US/i/btn/btn_buynowCC_LG.gif"
					       name="submit" alt="PayPal - The safer, easier way to pay online!">
					<img alt="" src="https://www.sandbox.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1" align="center">
				</form>
			<?php } else { ?>
			<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
				<input type="hidden" name="cmd" value="_s-xclick">
				<input type="hidden" name="hosted_button_id" value="DBXH8A467Z58E">
				<table>
					<tr>
						<td><input type="hidden" name="on0" value="Membership Options">Membership Options</td>
					</tr>
					<tr>
						<td><!--suppress HtmlFormInputWithoutLabel -->
							<select name="os0">
								<option value="Individual" <?php if ( 25 == $memb_price[ get_user_meta( $mb_id, 'membership_id', true ) ] ) {
									echo 'selected';
								} ?>>Individual $25.00 USD
								</option>
								<option
									value="Individual + Child(ren)" <?php if ( 30 == $memb_price[ get_user_meta( $mb_id, 'membership_id', true ) ] ) {
									echo 'selected';
								} ?>>Individual + Child(ren) $30.00 USD
								</option>
								<option value="Couple" <?php if ( 40 == $memb_price[ get_user_meta( $mb_id, 'membership_id', true ) ] ) {
									echo 'selected';
								} ?>>Couple $40.00 USD
								</option>
								<option value="Household" <?php if ( 45 == $memb_price[ get_user_meta( $mb_id, 'membership_id', true ) ] ) {
									echo 'selected';
								} ?>>Household $45.00 USD
								</option>
							</select></td>
					</tr>
				</table>
				<input type="hidden" name="currency_code" value="USD">
				<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_paynowCC_LG.gif" name="submit"
				       alt="PayPal - The safer, easier way to pay online!">
				<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1" align="center">
			</form>
		</div>
	<?php } ?>
	</div><!-- .entry-content -->
<?php
}
