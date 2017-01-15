<?php
/*
Template Name: Registration
*/

global $defSel, $wpdb;

$display_reg_warning = false;

$states = load_states_array();

$membership_type_table   = 'ctxphc_membership_types';
$relationship_type_table = 'ctxphc_member_relationships';
$status_type_table       = 'ctxphc_member_status';

$relationship_types = get_types( $relationship_type_table );
$membership_types   = get_types( $membership_type_table );
$memb_costs         = get_membership_pricing();

$reg_action = reg_type_new();

get_header(); ?>

	<div id="content">
		<div class="spacer"></div>
		<?php if ( have_posts() ) : while ( have_posts() ) :
			the_post(); ?>
			<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<div id="post_title" class="post_title">
					<h1>
						<a href="<?php the_permalink() ?>" rel="bookmark"
						   title="Permanent Link to <?php the_title_attribute(); ?>">
							<?php the_title(); ?>
						</a>
					</h1>
				</div>
				<!-- Post_title -->
				<div class="clear"></div>
				<div class="entry">
					<?php the_content( 'more...' ); ?>
					<div class="clear"></div>
					<div class="spacer"></div>
					<div>
						<h2 class="ctxphc_center">Central Texas Parrot Head Club</h2>
					</div>

					<?php if ( $display_reg_warning ) { ?>
						<div class="warning">Currently our registration form is not working correctly. We are aware of this issue and are actively working to resolve this as quick as possible. In the mean time please contact our <a href="mailto:<?php echo antispambot( 'membership@ctxphc.com' ); ?> ">Membership Director</a> and let them know you are interested in joining.
						</div>
					<?php } ?>
					<p class="ctxphc_reg_txt">So, you've decided you want to join in our mission to Party with a Purpose,
						have PHun and help the community? If so, <img class="alignright wp-image-7" title="membership-image"
						                                              src="<?php echo get_template_directory_uri();
						                                              ?>/includes/images/membership-image.jpg"
						                                              alt="ctxphc membership image"/>you can complete the application below and make a payment using PayPal or print out the application and mail a check to:</p>

					<p class="ctxphc_reg_txt">
						Central Texas Parrot Head Club<br/>
						c/o Membership Director<br/>
						P.O. Box 82655<br/>
						Austin, TX 78708
					</p>

					<p class="ctxphc_reg_txt">Membership entitles you to attend our numerous monthly events; an official club badge; access to the monthly CTXPHC newsletter and ParrotHead-related electronic bulletins. The newsletter and bulletins keep you up-to-date regarding local, regional and statewide PHlockings (which you would be eligible to attend); community events; special discounts; concert news; VIP passes and much more!</p>

					<p class="ctxphc_reg_txt">If you have any questions, contact our <a
							href="mailto:<?php echo antispambot( 'membership@ctxphc.com' ); ?>">Membership
							Director</a>.</p>

					<p class="ctxphc_reg_txt">If you are ready to join in the PHun, scroll down and fill out our registration form!</p>

					<p class="ctxphc_reg_txt"><strong>NOTE: If this is a renewal, login to your profile and click on the "Renew Membership" button. If you don't know how to login, send email to the <a href="mailto:<?php echo antispambot( 'support@ctxphc.com' ); ?>">CTXPHC Support Staff</a>.</strong></p>

					<div class="spacer"></div>

					<div class="reg_form_row" id="mem_reg_types">
						<h4>Membership Types:</h4>
						<ul class="ctxphc_reg_type" id="memb_reg_list">
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

					<form class="memb_reg_form" id="regForm" name="regForm" method="post"
					      action="<?php bloginfo( 'url' ); ?>/registration-review/">
						<input type="hidden" name="mb_relationship" value=1 />
						<input type="hidden" name="reg_action" value="<?php echo $reg_action; ?>" />
						<fieldset class="reg_form" id="memb_type">
							<legend><span class="memb_legend">Membership Options</span></legend>
							<div class="memb_type" id="memb_type_div">
								<!-- Individual Member Option -->
								<input class="memb_type" id="memb_type_ID" type="radio" name="memb_type" value="1" checked/>
								<label class="memb_type" for="memb_type_ID">Individual $<?php echo $memb_costs[ 1 ]->cost; ?></label>

								<!-- Individual + Child(ren) Member Option -->
								<input class="memb_type" id="memb_type_IC" type="radio" name="memb_type" value="2"/>
								<label class="memb_type" for="memb_type_IC">Individual + Children
									$<?php echo $memb_costs[ 2 ]->cost; ?></label>

								<!-- Couple Member Option -->
								<input class="memb_type" id="memb_type_CO" type="radio" name="memb_type" value="3"/>
								<label class="memb_type" for="memb_type_CO">Couple $<?php echo $memb_costs[ 3 ]->cost; ?></label>

								<!-- Household Member Option -->
								<input class="memb_type" id="memb_type_HH" type="radio" name="memb_type" value="4"/>
								<label class="memb_type" for="memb_type_HH">Household $<?php echo $memb_costs[ 4 ]->cost; ?></label>
							</div>
						</fieldset>

						<div class="spacer"></div>

						<fieldset class="reg_form" id="personal_info">
							<legend><span class="memb_legend">Your Information</span></legend>
							<div class="reg_form_row" id="personal_info_div">
								<label class="reg_first_name" id="lbl_mb_first_name" for="mb_first_name">First Name:</label>
								<input class="reg_first_name validate[required, custom[onlyLetterSp]]"
								       data-prompt-position="bottomLeft" id="mb_fast_name" name="mb_first_name" type="text"
								       value="" title="first_name"/>

								<label class="reg_last_name" id="lbl_mb_last_name" for="mb_last_name">Last Name:</label>
								<input class="reg_last_name validate[required, custom[onlyLetterSp]]"
								       data-prompt-position="bottomLeft" id="mb_last_name" name="mb_last_name" type="text"
								       value=""/>
							</div>
							<div class="reg_form_row">
								<label class="cm_birthday" id="lbl_mb_birthday" for="mb_birthday">Birthdate:</label>
								<input class="cm_birthday validate[required, custom[dateFormat]]"
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
									echo showOptionsDrop( $states, $defSel, true ); ?>
								</select>

								<label id="lbl_mb_zip" for="mb_zip">Zip:</label>
								<input id="mb_zip" class="validate[required, custom[zip]]"
								       data-prompt-position="bottomLeft" name="mb_zip" type="text" value=""/>
							</div>
						</fieldset>

						<div class="spacer" id="spouse_spacer"></div>

						<fieldset class="reg_form" id="spouse_info">
							<legend><span class="memb_legend">Spouse/Partner</span></legend>

							<div class="reg_form_row">
								<label class="reg_first_name" id="lbl_sp_first_name" for="sp_first_name">First Name:</label>
								<input class="reg_first_name validate[condRequired[memb_type]], custom[onlyLetterSp]]"
								       data-prompt-position="bottomLeft" id="sp_first_name" name="sp_first_name" type="text"
								       value=""/>

								<label class="reg_last_name" id="lbl_sp_last_name" for="sp_last_name">Last Name:</label>
								<input class="reg_last_name validate[condRequired[memb_type]], custom[onlyLetterSp]]"
								       data-prompt-position="bottomLeft" id="sp_last_name" name="sp_last_name" type="text"
								       value=""/>
							</div>

							<div class="reg_form_row">
								<label class="cm_birthday" id="lbl_sp_birthday" for="sp_birthday">Birthdate:</label>
								<input class="cm_birthday validate[condRequired[memb_type]] text-input datepicker" id="sp_birthday"
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
								<select class="sp_relationship validate[condRequired[memb_type]]]"
								        id="sp_relationship" name="sp_relationship">
									<?php $defSel = 2 ?>
									<?php echo showOptionsDrop( $relationship_types, $defSel, true ); ?>
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
									<input class="cm_birthday validate[custom[dateFormat]]"
									       data-prompt-position="bottomLeft" id="c1_birthday" name="c1_birthday" type="date"/>

									<label class="child_relationship" id="lbl_c1_relationship"
									       for="c1_relationship">Relationship:</label>
									<select class="child_relationship" id="c1_relationship" name="c1_relationship">
										<?php $defSel = 4 ?>
										<?php echo showOptionsDrop( $relationship_types, $defSel, true ); ?>
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
									<input class="cm_birthday validate[custom[dateFormat]]" id="c2_birthday" name="c2_birthday" type="date"/>

									<label class="child_relationship" id="lbl_c2_relationship"
									       for="c2_relationship">Relationship:</label>
									<select class="child_relationship" id="c2_relationship" name="c2_relationship">
										<?php $defSel = 4; ?>
										<?php echo showOptionsDrop( $relationship_types, $defSel, true ); ?>
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
									<input class="cm_birthday validate[custom[dateFormat]]" id="c3_birthday" name="c3_birthday" type="date"/>

									<label class="child_relationship" id="lbl_c3_relationship" for="c3_relationship">Relationship:</label>
									<select class="child_relationship" id="c3_relationship" name="c3_relationship">
										<?php $defSel = 4; ?>
										<?php echo showOptionsDrop( $relationship_types, $defSel, true ); ?>
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
									<input class="cm_birthday validate[custom[dateFormat]]" id="c4_birthday" name="c4_birthday" type="date"/>

									<label class="child_relationship" id="lbl_c4_relationship"
									       for="c4_relationship">Relationship:</label>
									<select class="child_relationship" id="c4_relationship" name="c4_relationship">
										<?php $defSel = 4; ?>
										<?php echo showOptionsDrop( $relationship_types, $defSel, true ); ?>
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
							       value="new"/>
						</div>

					</form>
				</div>
				<!-- entry -->
			</div>
			<!-- post -->

			<?php
		endwhile;
		endif;
		?>
	</div>
	<!-- content -->
<?php get_sidebar(); ?>
<?php get_footer(); ?>
