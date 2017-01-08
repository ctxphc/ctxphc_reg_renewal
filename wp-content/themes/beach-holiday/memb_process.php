<?php
/*
Template Name: Reg_Processing
*/

require_once TEMPLATEPATH . '/includes/randPassGen.php';


$states = load_states_array();

$membership_type_table   = 'ctxphc_membership_types';
$relationship_type_table = 'ctxphc_member_relationships';
$status_type_table       = 'ctxphc_member_status';

$relationship_types = get_types( $relationship_type_table );
$membership_types   = get_types( $membership_type_table );
$memb_costs         = get_membership_pricing();

//Declare Global, local and Array variables before first use
global $wpdb, $memb_error;

$memb_error = new WP_Error();

//Change to false for production use
$debug = false;

if ( $debug ) {
	$wpdb->show_errors();
}

date_default_timezone_set( 'America/Chicago' );

$mb_id = '';

get_header();

$reg_action = $_POST[ 'reg_action' ];

if ( $_SERVER[ 'REQUEST_METHOD' ] === "POST" ) {
	$message = "Inside server request method POST";
	debug_log_message( $message, $debug );
	if ( $reg_action == 'renew' ) {
		$message = "Inside Registration Renewal Submit Post processing.";
		debug_log_message( $message, $debug );

		$renewal_data = process_update_metadata();

		$form_user_data   = $renewal_data[ 'userdata' ];
		$member_meta_data = $renewal_data[ 'metadata' ];
	} else if ( $reg_action == 'new' || isset( $_POST[ 'registration' ] ) ) {
		global $prime_members_id;
		$prime_members_id = null;

		$message = "Inside Registration Submit Post processing.";
		debug_log_message( $message, $debug );

		$registration_data = process_registration();

		$form_user_data   = $registration_data[ 'userdata' ];
		$member_meta_data = $registration_data[ 'metadata' ];

		$verified_data = membership_data_merge( $registration_data );
	} else if ( isset( $_POST[ 'update' ] ) ) {
		$message = "Inside Registration Review Update Page Post processing.";
		debug_log_message( $message, $debug );

		$updated_registration_data = process_update_metadata();

		$form_user_data   = $updated_registration_data[ 'userdata' ];
		$member_meta_data = $updated_registration_data[ 'metadata' ];
	}


	$mb_id        = $member_meta_data[ 'mb' ][ 'mb_id' ];
	$memb_type_id = $member_meta_data[ 'mb' ][ 'membership_type' ];

	if ( isset( $member_meta_data[ 'mb' ][ 'sp_id' ] ) ) {
		$sp_id = $member_meta_data[ 'mb' ][ 'sp_id' ];
	}
	if ( isset( $member_meta_data[ 'mb' ][ 'c1_id' ] ) ) {
		$c1_id = $member_meta_data[ 'mb' ][ 'c1_id' ];
	}
	if ( isset( $member_meta_data[ 'mb' ][ 'c2_id' ] ) ) {
		$c2_id = $member_meta_data[ 'mb' ][ 'c2_id' ];
	}
	if ( isset( $member_meta_data[ 'mb' ][ 'c3_id' ] ) ) {
		$c3_id = $member_meta_data[ 'mb' ][ 'c3_id' ];
	}
	if ( isset( $member_meta_data[ 'mb' ][ 'c4_id' ] ) ) {
		$c4_id = $member_meta_data[ 'mb' ][ 'c4_id' ];
	}


	switch ( $memb_type_id ) {
		case 1; //ID - Individual
			?>
            <script type="text/javascript">
                jQuery(document).ready(
                    function () {
                        jQuery("#spouse_info").css("display", "none");
                        jQuery("#spouse_spacer").css("display", "none");
                        jQuery("#family_info").css("display", "none");
                        jQuery("#family_spacer").css("display", "none");
                    }
                );
            </script>
			<?php
			break;

		case 2; //IC - Individual + child
			?>
            <script type="text/javascript">
                jQuery(document).ready(
                    function () {
                        jQuery("#spouse_info").css("display", "none");
                        jQuery("#spouse_spacer").css("display", "none");
                        jQuery("#family_info").css("display", "block");
                        jQuery("#family_spacer").css("display", "block");
                    }
                );
            </script>
			<?php
			break;

		case 3; //CO - Couple
			?>
            <script type="text/javascript">
                jQuery(document).ready(
                    function () {
                        jQuery("#spouse_info").css("display", "block");
                        jQuery("#spouse_spacer").css("display", "block");
                        jQuery("#family_info").css("display", "none");
                        jQuery("#family_spacer").css("display", "none");
                    }
                );
            </script>
			<?php
			break;
		case 4; //HH - Household
			?>
            <script type="text/javascript">
                jQuery(document).ready(
                    function () {
                        jQuery("#spouse_info").css("display", "block");
                        jQuery("#spouse_spacer").css("display", "block");
                        jQuery("#family_info").css("display", "block");
                        jQuery("#family_spacer").css("display", "block");
                    }
                );
            </script>
			<?php
			break;
	} //End of switch on memb_type_id
} //END of Server Method = POST

//TODO: See about moving the form to a display function or method. Passing the populate form data
//TODO: Passing the populate form data as a argument.
$message = "Begin display of member registration verification form.  ";
debug_log_message( $message, $debug ); ?>

    <div id="content" xmlns="http://www.w3.org/1999/html">
        <div class="spacer"></div>
		<?php if ( have_posts() ) : while ( have_posts() ) :
			the_post(); ?>
            <div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <div class="post_title">
                    <h1><a href="<?php the_permalink() ?>" rel="bookmark"
                           title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a>
                    </h1>
                    <span
                            class="post_author">Author: <?php the_author_posts_link( 'nickname' ); ?><?php edit_post_link( ' Edit ', ' &raquo;', '&laquo;' ); ?></span>
                    <span class="post_date_m"><?php the_time( 'M' ); ?></span>
                    <span class="post_date_d"><?php the_time( 'd' ); ?></span>
                </div>
                <!-- post_title -->
                <div class="clear"></div>
                <div class="entry">
					<?php the_content( 'more...' ); ?>
                    <div class="clear"></div>

                    <div class="print">Thank you for submitting your membership registration. Mail
                        your check
                        for <span
                                class="print check_pay">$<?php /** @noinspection PhpIllegalArrayKeyTypeInspection */
							echo $memb_costs[ intval( $memb_type_id ) ]->cost; ?></span> to:
                    </div>
                    <div class="print">
                        Central Texas Parrot Head Club<br/>
                        c/o Membership Director<br/>
                        700 Brown Dr. <br>
                        Pflugerville, TX 78660
                    </div>
                    <div class="screen" id="reg-verify-text">Thank you for submitting your
                        membership registration. Please review your
                        registration and click UPDATE after making any changes. If everything is
                        ready to go click your preferred
                        payment method below.
                    </div>

                    <div class="spacer"></div>

                    <form class="memb_reg_form" id="memb_proc_form" name="memb_proc_form"
                          method="post"
                          action="<?php bloginfo( 'url' ); ?>/registration-review/">
                        <input type="hidden" name="reg_action" value="<?php echo $reg_action ?>"/>
						<?php $message = "Begin display form.  ";
						debug_log_message( $message, $debug ); ?>

                        <fieldset class="reg_form" id="memb_type">
                            <legend><span class="memb_legend">Membership Types</span></legend>
                            <div class="reg_renewal_row" id="memb_type">
                                <input class="memb_type" id="individual" type="radio"
                                       name="memb_type" value="1"
								       <?php if ( $memb_type_id === 1 ) { ?>checked<?php }; ?>>
                                <label for="memb_type_1">Individual -
                                    $<?php echo $memb_costs[ 1 ]->cost; ?></label>
                                <input class="memb_type" id="individual-child" type="radio"
                                       name="memb_type" value="2"
								       <?php if ( $memb_type_id === 2 ) { ?>checked<?php }; ?>>
                                <label for="memb_type_2">Individual + Children -
                                    $<?php echo $memb_costs[ 2 ]->cost; ?></label>
                                <input class="memb_type" id="couple" type="radio" name="memb_type"
                                       value="3"
								       <?php if ( $memb_type_id === 3 ) { ?>checked<?php }; ?>>
                                <label for="memb_type_3">Couple -
                                    $<?php echo $memb_costs[ 3 ]->cost; ?></label>
                                <input class="memb_type" id="household" type="radio"
                                       name="memb_type" value="4"
								       <?php if ( $memb_type_id === 4 ) { ?>checked<?php }; ?>>
                                <label for="memb_type_4">Household -
                                    $<?php echo $memb_costs[ 4 ]->cost; ?></label>
                            </div>
                        </fieldset>

                        <div class="spacer"></div>

                        <fieldset class="reg_form" id="personal_info">
                            <input type="hidden"
                                   value="<?php echo isset( $member_meta_data[ 'mb' ][ 'mb_id' ] ) ? $member_meta_data[ 'mb' ][ 'mb_id' ] : ''; ?>"
                                   name="mb_id"/>
                            <input type="hidden" name="mb_relationship"
                                   value="<?php echo $member_meta_data[ 'mb' ][ 'relationship_id' ]; ?>"/>
                            <legend><span class="memb_legend">Your Information</span></legend>
                            <div class="reg_renewal_row" id="personal_info">
                                <label class="reg_first_name" id="lbl_mb_first_name"
                                       for="mb_first_name">First Name:</label>
                                <input class="reg_first_name validate[required, custom[onlyLetterSp]]"
                                       data-prompt-position="bottomLeft"
                                       id="mb_first_name"
                                       name="mb_first_name" type="text"
                                       value="<?php echo isset( $form_user_data[ 'mb' ][ 'first_name' ] ) ? $form_user_data[ 'mb' ][ 'first_name' ] : ''; ?>"

                                <label class="reg_last_name" id="lbl_mb_last_name"
                                       for="mb_last_name">Last Name:</label>
                                <input class="reg_last_name validate[required, custom[onlyLetterSp]]"
                                       data-prompt-position="bottomLeft"
                                       id="mb_last_name"
                                       name="mb_last_name" type="text"
                                       value="<?php echo isset( $form_user_data[ 'mb' ][ 'last_name' ] ) ? $form_user_data[ 'mb' ][ 'last_name' ] : ''; ?>"
                            </div>
                            <div class="reg_renewal_row">
                                <label class="cm_birthday" id="lbl_mb_birthday" for="mb_birthday">Birthdate:</label>
                                <input class="cm_birthday validate[required, custom[dateFormat]]"
                                       data-prompt-position="bottomLeft"
                                       id="mb_birthday" name="mb_birthday" type="date"
                                       value="<?php echo isset( $member_meta_data[ 'mb' ][ 'birthday' ] ) ? $member_meta_data[ 'mb' ][ 'birthday' ] : ''; ?>"

                                <label class="reg_email" id="lbl_mb_email"
                                       for="mb_email">Email:</label>
                                <input class="reg_email validate[required, custom[email]]"
                                       data-prompt-position="bottomLeft"
                                       id="mb_email" name="mb_email" type="email"
                                       value="<?php echo isset( $form_user_data[ 'mb' ][ 'email' ] ) ? $form_user_data[ 'mb' ][ 'email' ] : ''; ?>"
                            </div>
                            <div class="reg_renewal_row">
                                <label class="reg_phone" id="lbl_mb_phone"
                                       for="mb_phone">Phone:</label>
                                <input class="reg_phone validate[required, custom[onlyNumber]]"
                                       data-prompt-position="bottomLeft"
                                       id="mb_phone" name="mb_phone" type="tel"
                                       value="<?php echo $member_meta_data[ 'mb' ][ 'phone' ]; ?>">

                                <label id="lbl_mb_occupation"
                                       for="mb_occupation">Occupation:</label>
                                <input class="mb_proc_field validate[required, custom[onlyLetterSp]]"
                                       data-prompt-position="bottomLeft"
                                       id="mb_occupation" name="mb_occupation" type="text"
                                       value="<?php echo $member_meta_data[ 'mb' ][ 'occupation' ]; ?>"/>
                            </div>
                        </fieldset>
                        <!--  End Personal Information fieldset -->

                        <div class="spacer"></div>

                        <fieldset class="reg_form" id="memb_address">
                            <legend><span class="memb_legend">Address</span></legend>
                            <div class="reg_renewal_row">
                                <label id="lbl_mb_addr1" for="mb_addr1">Address:</label>
                                <input class="mb_proc_field validate[required, cstom[address]]"
                                       data-prompt-position="bottomLeft"
                                       id="mb_addr1" name="mb_addr1" type="text"
                                       value="<?php echo $member_meta_data[ 'mb' ][ 'addr1' ]; ?>"/>

                                <label id="lbl_mb_addr2" for="mb_addr2">Suite/Apt:</label>
                                <input class="mb_addr2 validate[custom[onlyLetterSp]]"
                                       data-prompt-position="bottomLeft"
                                       id="mb_addr2" name="mb_addr2" type="text"
                                       value="<?php echo $member_meta_data[ 'mb' ][ 'addr2' ]; ?>"/>
                            </div>
                            <div class="reg_renewal_row">
                                <label id="lbl_memb_city" for="mb_city">City:</label>
                                <input class="mb_proc_field validate[required, custom[onlyLetterSp]]"
                                       data-prompt-position="bottomLeft"
                                       id="mb_city" name="mb_city" type="text"
                                       value="<?php echo $member_meta_data[ 'mb' ][ 'city' ]; ?>"/>

                                <label id="lbl_mb_state" for="mb_state">State:</label>
                                <select id="mb_state" name="mb_state"
                                        class="memb_proc_field validate[required]">
									<?php $defSel = isset( $member_meta_data[ 'mb' ][ 'state' ] ) ? $member_meta_data[ 'mb' ][ 'state' ] : "TX"; ?>
									<?php echo showOptionsDrop( $states, $defSel, true ); ?>
                                </select>

                                <label id="lbl_mb_zip" for="mb_zip">Zip:</label>
                                <input class="mb_proc_field validate[required, custom[zip]]"
                                       data-prompt-position="bottomLeft"
                                       id="mb_zip" name="mb_zip" type="text"
                                       value="<?php echo $member_meta_data[ 'mb' ][ 'zip' ]; ?>"/>
                            </div>
                        </fieldset>
                        <!--  End Member's Address fieldset -->

                        <div class="spacer" id="spouse_spacer"></div>

                        <!--  Begin Spouse/Partner Info fieldset -->
                        <fieldset class="reg_form" id="spouse_info">
                            <input type="hidden"
                                   value="<?php echo isset( $member_meta_data[ 'mb' ][ 'sp_id' ] ) ? $member_meta_data[ 'mb' ][ 'sp_id' ] : ''; ?>"
                                   name="sp_id">
                            <legend><span class="memb_legend">Spouse/Partner</span></legend>
                            <div class="reg_renewal_row">
                                <label class="reg_first_name" id="lbl_sp_first_name"
                                       for="sp_first_name">First Name:</label>
                                <input class="memb_proc_field validate[custom[onlyLetterSp]]"
                                       data-prompt-position="bottomLeft"
                                       id="sp_first_name" name="sp_first_name" type="text"
                                       value="<?php echo isset( $form_user_data[ 'sp' ][ 'first_name' ] ) ? $form_user_data[ 'sp' ][ 'first_name' ] : ''; ?>"

                                <label class="reg_last_name" id="lbl_sp_last_name"
                                       for="sp_last_name">Last Name:</label>
                                <input class="reg_last_name validate[custom[onlyLetterSp]]"
                                       data-prompt-position="bottomLeft"
                                       id="sp_last_name" name="sp_last_name" type="text"
                                       value="<?php echo isset( $form_user_data[ 'sp' ][ 'last_name' ] ) ? $form_user_data[ 'sp' ][ 'last_name' ] : ''; ?>"
                            </div>
                            <div class="reg_renewal_row">
                                <label class="cm_birthday" id="lbl_sp_birthday" for="sp_birthday">Birthdate:</label>
                                <input class="cm_birthday" id="sp_birthday" name="sp_birthday"
                                       type="date"
                                       value="<?php echo isset( $member_meta_data[ 'sp' ][ 'birthday' ] ) ? $member_meta_data[ 'sp' ][ 'birthday' ] : ''; ?>"

                                <label id="lbl_sp_email" for="sp_email">Email:</label>
                                <input class="reg_email validate[custom[email]]"
                                       data-prompt-position="bottomLeft"
                                       id="sp_email" name="sp_email" type="email"
                                       value="<?php echo isset( $form_user_data[ 'sp' ][ 'email' ] ) ? $form_user_data[ 'sp' ][ 'email' ] : ''; ?>"
                            </div>
                            <div class="reg_renewal_row">
                                <label id="lbl_sp_phone" for="sp_phone">Phone:</label>
                                <input class="reg_phone validate[custom[onlyNumber]]" id="sp_phone"
                                       name="sp_phone"
                                       type="tel"
                                       value="<?php echo $member_meta_data[ 'sp' ][ 'phone' ]; ?>"/>

                                <label class="sp_relationship" id="lbl_sp_relationship"
                                       for="sp_relationship">Relationship:</label>
                                <select class="sp_relationship" id="sp_relationship"
                                        name="sp_relationship">
									<?php $defSel = isset( $member_meta_data[ 'sp' ][ 'sp_relationship' ] ) ? $member_meta_data[ 'sp' ][ 'sp_relationship' ] : 2; ?>
									<?php echo showOptionsDrop( $relationship_types, $defSel, true ); ?>
                                </select>
                            </div>
                        </fieldset>
                        <!--  End Spouse/Partner Information fieldset -->

                        <div class="spacer" id="family_spacer"></div>


                        <fieldset class="reg_form" id="family_info">
                            <legend><span class="memb_legend">Family Members</span></legend>
                            <section><!--  Begin 1st Child/Other Info fieldset -->
                                <input type="hidden"
                                       value="<?php echo isset( $member_meta_data[ 'mb' ][ 'c1_id' ] ) ? $member_meta_data[ 'mb' ][ 'c1_id' ] : ''; ?>"
                                       name="c1_id">

                                <div class="reg_renewal_row">
                                    <label class="reg_first_name" id="lbl_c1_first_name"
                                           for="c1_first_name">First Name:</label>
                                    <input class="reg_first_name validate[custom[onlyLetterSp]]"
                                           data-prompt-position="bottomLeft"
                                           id="c1_first_name" name="c1_first_name" type="text"
                                           value="<?php if ( isset( $form_user_data[ 'c1' ][ 'first_name' ] ) ) {
										       echo $form_user_data[ 'c1' ][ 'first_name' ];
									       } else if ( isset( $member_meta_data[ 'mb' ][ 'c1_first_name' ] ) ) {
										       echo $member_meta_data[ 'mb' ][ 'c1_first_name' ];
									       } else {
										       echo '';
									       }
									       ?>"

                                    <label class="reg_last_name" id="lbl_c1_last_name"
                                           for="c1_last_name">Last Name:</label>
                                    <input class="reg_last_name validate[condRequired[c1_first_name], custom[onlyLetterSp]]"
                                           data-prompt-position="bottomLeft"
                                           id="c1_last_name" name="c1_last_name"
                                           type="text"
                                           value="<?php if ( isset( $form_user_data[ 'c1' ][ 'last_name' ] ) ) {
	                                           echo $form_user_data[ 'c1' ][ 'last_name' ];
                                           } else if ( isset( $member_meta_data[ 'mb' ][ 'c1_last_name' ] ) ) {
	                                           echo $member_meta_data[ 'mb' ][ 'c1_last_name' ];
                                           } else {
	                                           echo '';
                                           }
                                           ?>"
                                </div>
                                <div class="reg_renewal_row">
                                    <label class="cm_birthday" id="lbl_c1_birthday"
                                           for="c1_birthday">Birthdate:</label>
                                    <input class="cm_birthday validate[condRequired[c1_first_name], custom[onlyNumber]]"
                                           data-prompt-position="bottomLeft"
                                           id="c1_birthday" name="c1_birthday" type="date"
                                           value="<?php if ( isset( $member_meta_data[ 'c1' ][ 'birthday' ] ) ) {
	                                           echo $member_meta_data[ 'c1' ][ 'birthday' ];
                                           } else if ( isset( $member_meta_data[ 'mb' ][ 'c1_birthday' ] ) ) {
	                                           echo $member_meta_data[ 'mb' ][ 'c1_birthday' ];
                                           } else {
	                                           echo '';
                                           }
                                           ?>"

                                    <label class="child_relationship" id="lbl_c1_relationship"
                                           for="c1_relationship">Relationship:</label>
                                    <select class="child_relationship" id="c1_relationship"
                                            name="c1_relationship">
										<?php if ( isset( $member_meta_data[ 'c1' ][ 'relationship' ] ) ){
											$defSel = $member_meta_data[ 'c1' ][ 'relationship' ];
                                        } else if ( isset( $member_meta_data[ 'mb' ][ 'c1_relationship' ] ) ) {
											$defSel = $member_meta_data[ 'mb' ][ 'c1_relationship' ];
										} else {
										    $defSel = 4;
										} ?>
										<?php echo showOptionsDrop( $relationship_types, $defSel, true ); ?>
                                    </select>
                                </div>
                                <div class="reg_renewal_row">
                                    <label class="child_email " id="lbl_c1_email" for="c1_email">Email:</label>
                                    <input class="child_email validate[condRequired[c1_first_name], custom[email]]"
                                           data-prompt-position="bottomLeft"
                                           id="c1_email" name="c1_email" type="email"
                                           value="<?php if ( isset( $form_user_data[ 'c1' ][ 'email' ] ) ) {
	                                           echo $form_user_data[ 'c1' ][ 'email' ];
                                           } else if ( isset( $member_meta_data[ 'mb' ][ 'c1_email' ] ) ) {
	                                           echo $member_meta_data[ 'mb' ][ 'c1_email' ];
                                           } else {
	                                           echo '';
                                           }
                                           ?>"
                                </div>
                            </section>
                            <!--  End of 1st Child/Other Info fieldset -->

                            <div class="spacer"></div>

                            <section><!-- Begin 2nd FAMILY MEMBER -->
                                <input type="hidden"
                                       value="<?php echo isset( $member_meta_data[ 'mb' ][ 'c2_id' ] ) ? $member_meta_data[ 'mb' ][ 'c2_id' ] : ''; ?>"
                                       name="c2_id"/>

                                <div class="reg_renewal_row">

                                    <label class="reg_first_name" id="lbl_c2_first_name"
                                           for="c2_first_name">First Name:</label>
                                    <input class="reg_first_name validate[custom[onlyLetterSp]]"
                                           data-prompt-position="bottomLeft"
                                           id="c2_first_name" name="c2_first_name" type="text"
                                           value="<?php if ( isset( $form_user_data[ 'c2' ][ 'first_name' ] ) ) {
	                                           echo $form_user_data[ 'c2' ][ 'first_name' ];
                                           } else if ( isset( $member_meta_data[ 'mb' ][ 'c2_first_name' ] ) ) {
	                                           echo $member_meta_data[ 'mb' ][ 'c2_first_name' ];
                                           } else {
	                                           echo '';
                                           }
                                           ?>"

                                    <label class="reg_last_name" id="lbl_c2_last_name"
                                           for="c2_last_name">Last Name:</label>
                                    <input class="reg_last_name validate[condRequired[c2_first_name], custom[onlyLetterSp]]"
                                           data-prompt-position="bottomLeft"
                                           id="c2_last_name" name="c2_last_name" type="text"
                                           value="<?php if ( isset( $form_user_data[ 'c2' ][ 'last_name' ] ) ) {
	                                           echo $form_user_data[ 'c2' ][ 'last_name' ];
                                           } else if ( isset( $member_meta_data[ 'mb' ][ 'c2_last_name' ] ) ) {
	                                           echo $member_meta_data[ 'mb' ][ 'c2_last_name' ];
                                           } else {
	                                           echo '';
                                           }
                                           ?>"
                                </div>
                                <div class="reg_renewal_row">
                                    <label class="cm_birthday" id="lbl_c2_birthday"
                                           for="c2_birthday">Birthdate:</label>
                                    <input class="cm_birthday validate[condRequired[c2_first_name], custom[onlyNumber]]"
                                           data-prompt-position="bottomLeft"
                                           id="c2_birthday" name="c2_birthday" type="date"
                                           value="<?php if ( isset( $member_meta_data[ 'c2' ][ 'birthday' ] ) ) {
	                                           echo $member_meta_data[ 'c2' ][ 'birthday' ];
                                           } else if ( isset( $member_meta_data[ 'mb' ][ 'c2_birthday' ] ) ) {
	                                           echo $member_meta_data[ 'mb' ][ 'c2_birthday' ];
                                           } else {
	                                           echo '';
                                           }
                                           ?>"

                                    <label class="child_relationship" id="lbl_c2_relationship"
                                           for="c2_relationship">Relationship:</label>
                                    <select class="child_relationship" id="c2_relationship"
                                            name="c2_relationship">
	                                    <?php if ( isset( $member_meta_data[ 'c2' ][ 'relationship' ] ) ){
		                                    $defSel = $member_meta_data[ 'c2' ][ 'relationship' ];
	                                    } else if ( isset( $member_meta_data[ 'mb' ][ 'c2_relationship' ] ) ) {
		                                    $defSel = $member_meta_data[ 'mb' ][ 'c2_relationship' ];
	                                    } else {
		                                    $defSel = 4;
	                                    } ?>
										<?php echo showOptionsDrop( $relationship_types, $defSel, true ); ?>
                                    </select>
                                </div>
                                <div class="reg_renewal_row">
                                    <label class="child_email" id="lbl_c2_emai"
                                           for="c2_email">Email:</label>
                                    <input class="child_email validate[condRequired[c2_first_name], custom[email]]"
                                           data-prompt-position="bottomLeft"
                                           id="c2_email" name="c2_email" type="email"
                                           value="<?php if ( isset( $form_user_data[ 'c2' ][ 'email' ] ) ) {
	                                           echo $form_user_data[ 'c2' ][ 'email' ];
                                           } else if ( isset( $member_meta_data[ 'mb' ][ 'c2_email' ] ) ) {
	                                           echo $member_meta_data[ 'mb' ][ 'c2_email' ];
                                           } else {
	                                           echo '';
                                           }
                                           ?>"
                                </div>
                            </section>
                            <!-- End 2nd FAMILY MEMBER -->

                            <div class="spacer"></div>

                            <section><!-- 3rd FAMILY MEMBER -->
                                <input type="hidden"
                                       value="<?php echo isset( $member_meta_data[ 'mb' ][ 'c3_id' ] ) ? $member_meta_data[ 'mb' ][ 'c3_id' ] : ''; ?>"
                                       name="c3_id"/>

                                <div class="reg_renewal_row">
                                    <label class="reg_first_name" id="lbl_c3_first_name"
                                           for="c3_first_name">First Name:</label>
                                    <input class="reg_first_name validate[custom[onlyLetterSp]]"
                                           data-prompt-position="bottomLeft"
                                           id="c3_first_name" name="c3_first_name" type="text"
                                           value="<?php if ( isset( $form_user_data[ 'c3' ][ 'first_name' ] ) ) {
	                                           echo $form_user_data[ 'c3' ][ 'first_name' ];
                                           } else if ( isset( $member_meta_data[ 'mb' ][ 'c3_first_name' ] ) ) {
	                                           echo $member_meta_data[ 'mb' ][ 'c3_first_name' ];
                                           } else {
	                                           echo '';
                                           }
                                           ?>"

                                    <label class="reg_last_name" id="lbl_c3_last_name"
                                           for="c3_last_name">Last Name:</label>
                                    <input class="reg_last_name validate[condRequired[c3_first_name], custom[onlyLetterSp]]"
                                           data-prompt-position="bottomLeft"
                                           id="c3_last_name" name="c3_last_name" type="text"
                                           value="<?php if ( isset( $form_user_data[ 'c3' ][ 'last_name' ] ) ) {
	                                           echo $form_user_data[ 'c3' ][ 'last_name' ];
                                           } else if ( isset( $member_meta_data[ 'mb' ][ 'c3_last_name' ] ) ) {
	                                           echo $member_meta_data[ 'mb' ][ 'c3_last_name' ];
                                           } else {
	                                           echo '';
                                           }
                                           ?>"
                                </div>
                                <div class="reg_renewal_row">
                                    <label class="cm_birthday" id="lbl_c3_birthday"
                                           for="c3_birthday">Birthdate:</label>
                                    <input class="cm_birthday validate[condRequired[c3_first_name], custom[onlyNumber]]"
                                           data-prompt-position="bottomLeft" id="c3_birthday"
                                           name="c3_birthday" type="date"
                                           value="<?php if ( isset( $member_meta_data[ 'c3' ][ 'birthday' ] ) ) {
	                                           echo $member_meta_data[ 'c3' ][ 'birthday' ];
                                           } else if ( isset( $member_meta_data[ 'mb' ][ 'c3_birthday' ] ) ) {
	                                           echo $member_meta_data[ 'mb' ][ 'c3_birthday' ];
                                           } else {
	                                           echo '';
                                           }
                                           ?>"

                                    <label class="child_relationship" id="lbl_c3_relationship"
                                           for="c3_relationship">Relationship:</label>
                                    <select class="child_relationship" id="c3_relationship"
                                            name="c3_relationship">
	                                    <?php if ( isset( $member_meta_data[ 'c3' ][ 'relationship' ] ) ){
		                                    $defSel = $member_meta_data[ 'c3' ][ 'relationship' ];
	                                    } else if ( isset( $member_meta_data[ 'mb' ][ 'c3_relationship' ] ) ) {
		                                    $defSel = $member_meta_data[ 'mb' ][ 'c3_relationship' ];
	                                    } else {
		                                    $defSel = 4;
	                                    } ?>
										<?php echo showOptionsDrop( $relationship_types, $defSel, true ); ?>
                                    </select>
                                </div>
                                <div class="reg_renewal_row">
                                    <label class="child_email" id="lbl_c3_email"
                                           for="c3_email">Email:</label>
                                    <input class="child_email validate[condRequired[c3_first_name], custom[email]]"
                                           data-prompt-position="bottomLeft"
                                           id="c3_email" name="c3_email" type="email"
                                           value="<?php if ( isset( $form_user_data[ 'c3' ][ 'email' ] ) ) {
	                                           echo $form_user_data[ 'c3' ][ 'email' ];
                                           } else if ( isset( $member_meta_data[ 'mb' ][ 'c3_email' ] ) ) {
	                                           echo $member_meta_data[ 'mb' ][ 'c3_email' ];
                                           } else {
	                                           echo '';
                                           }
                                           ?>"
                                </div>
                            </section>
                            <!-- End 3rd FAMILY MEMBER -->

                            <div class="spacer"></div>

                            <section><!-- 4th FAMILY MEMBER -->
                                <input type="hidden"
                                       value="<?php echo isset( $member_meta_data[ 'mb' ][ 'c4_id' ] ) ? $member_meta_data[ 'mb' ][ 'c4_id' ] : ''; ?>"
                                       name="c4_id"/>

                                <div class="reg_renewal_row">
                                    <label class="reg_first_name" id="lbl_c4_first_name"
                                           for="c4_first_name">First Name:</label>
                                    <input class="reg_first_name validate[custom[onlyLetterSp]]"
                                           data-prompt-position="bottomLeft"
                                           id="c4_first_name" name="c4_first_name" type="text"
                                           value="<?php if ( isset( $form_user_data[ 'c4' ][ 'first_name' ] ) ) {
	                                           echo $form_user_data[ 'c4' ][ 'first_name' ];
                                           } else if ( isset( $member_meta_data[ 'mb' ][ 'c4_first_name' ] ) ) {
	                                           echo $member_meta_data[ 'mb' ][ 'c4_first_name' ];
                                           } else {
	                                           echo '';
                                           }
                                           ?>"

                                    <label class="reg_last_name" id="lbl_c4_last_name"
                                           for="c4_last_name">Last
                                        Name:</label>
                                    <input class="reg_last_name validate[condRequired[c4_first_name], custom[onlyLetterSp]]"
                                           data-prompt-position="bottomLeft"
                                           id="c4_last_name" name="c4_last_name" type="text"
                                           value="<?php if ( isset( $form_user_data[ 'c4' ][ 'last_name' ] ) ) {
	                                           echo $form_user_data[ 'c4' ][ 'last_name' ];
                                           } else if ( isset( $member_meta_data[ 'mb' ][ 'c4_last_name' ] ) ) {
	                                           echo $member_meta_data[ 'mb' ][ 'c4_last_name' ];
                                           } else {
	                                           echo '';
                                           }
                                           ?>"
                                </div>
                                <div class="reg_renewal_row">
                                    <label class="cm_birthday" id="lbl_c4_birthday"
                                           for="c4_birthday">Birthdate:</label>
                                    <input class="cm_birthday validate[condRequired[c4_first_name], custom[onlyNumber]]"
                                           data-prompt-position="bottomLeft"
                                           id="c4_birthday" name="c4_birthday" type="date"
                                           value="<?php if ( isset( $member_meta_data[ 'c4' ][ 'birthday' ] ) ) {
	                                           echo $member_meta_data[ 'c4' ][ 'birthday' ];
                                           } else if ( isset( $member_meta_data[ 'mb' ][ 'c4_birthday' ] ) ) {
	                                           echo $member_meta_data[ 'mb' ][ 'c4_birthday' ];
                                           } else {
	                                           echo '';
                                           }
                                           ?>"

                                    <label class="child_relationship" id="lbl_c4_relationship"
                                           for="c4_relationship">Relationship:</label>
                                    <select class="child_relationship" id="c4_relationship"
                                            name="c4_relationship">
	                                    <?php if ( isset( $member_meta_data[ 'c4' ][ 'relationship' ] ) ){
		                                    $defSel = $member_meta_data[ 'c4' ][ 'relationship' ];
	                                    } else if ( isset( $member_meta_data[ 'mb' ][ 'c4_relationship' ] ) ) {
		                                    $defSel = $member_meta_data[ 'mb' ][ 'c4_relationship' ];
	                                    } else {
		                                    $defSel = 4;
	                                    } ?>
										<?php echo showOptionsDrop( $relationship_types, $defSel, true ); ?>
                                    </select>
                                </div>
                                <div class="reg_renewal_row">
                                    <label class="child_email" id="lbl_c4_email" for="c4_email">Email:</label>
                                    <input class="child_email validate[condRequired[c4_first_name], custom[email]]"
                                           data-prompt-position="bottomLeft"
                                           id="c4_email" name="c4_email" type="email"
                                           value="<?php if ( isset( $form_user_data[ 'c4' ][ 'email' ] ) ) {
	                                           echo $form_user_data[ 'c4' ][ 'email' ];
                                           } else if ( isset( $member_meta_data[ 'mb' ][ 'c4_email' ] ) ) {
	                                           echo $member_meta_data[ 'mb' ][ 'c4_email' ];
                                           } else {
	                                           echo '';
                                           }
                                           ?>"
                                </div>
                            </section>
                            <!-- End 4th FAMILY MEMBER -->
                        </fieldset>
                        <!--  End Children's Information fieldset -->

                        <div class="spacer"></div>

                        <div id="reg_update">
                            <fieldset class="screen">
                                <legend class="screen"><span class="memb_legend"> Update</span>
                                </legend>
                                <div class="screen">If you {
                                    have} made any changes to the form please
                                    click:
                                </div>
                                <div>
                                    <input class="ctxphc_button3 screen" id="reg_submit"
                                           type="submit" name="update"
                                           value="update" onclick=""/>
                                </div>
                            </fieldset>
                        </div>

						<?php $message = "End displaying form.  ";
						debug_log_message( $message, $debug ); ?>

                    </form>

                    <div class="spacer"></div>

                    <div id="payment">
                        <fieldset class="screen" id="payOptions">
                            <legend class="screen"><span class="memb_legend">Payment Options</span>
                            </legend>
                            <div class="cont_wrap">
                                <div class="screen">Once you are satisfied the information displayed
                                    is correct click your preferred
                                    payment option. PayPal will allow you to use most credit cards
                                    or your PayPal account.
                                </div>
                                <div class="spacer"></div>
                                <div class="cont_lf_col">
                                    <!--  <form action="https://www.sandbox.paypal.com/cgi-bin/webscr" method="post" target="_top">
									<input type="hidden" name="cmd" value="_s-xclick">
									<input type="hidden" name="hosted_button_id" value="9N5Z7PJKJMDA4">
									<table>
									<tr><td><input type="hidden" name="on0" value="Registration Type">Registration Type</td></tr><tr><td><select name="os0">
										<option value="Individual">Individual $25.00 USD</option>
										<option value="Individual + Child(ren)">Individual + Child(ren) $30.00 USD</option>
										<option value="Couple">Couple $40.00 USD</option>
										<option value="Household">Household $45.00 USD</option>
									</select> </td></tr>
									</table>
									<input type="hidden" name="currency_code" value="USD">
									<input type="image" src="https://www.sandbox.paypal.com/en_US/i/btn/btn_paynowCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
									<img alt="" border="0" src="https://www.sandbox.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1">
									</form>

									# sandbox ctxphc merchant  button
									<form action="https://www.sandbox.paypal.com/cgi-bin/webscr" method="post" target="_top">
									<input type="hidden" name="cmd" value="_s-xclick">
									<input type="hidden" name="hosted_button_id" value="FRVTBSA4QGDPG">
									<table>
									<tr><td><input type="hidden" name="on0" value="Registration Type">Registration Type</td></tr><tr><td><select name="os0">
										<option value="Individual">Individual $30.00 USD</option>
										<option value="Individual+Children">Individual+Children $35.00 USD</option>
										<option value="Couple">Couple $40.00 USD</option>
										<option value="Household">Household $45.00 USD</option>
									</select> </td></tr>
									</table>
									<input type="hidden" name="currency_code" value="USD">
									<input type="image" src="https://www.sandbox.paypal.com/en_US/i/btn/btn_paynowCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
									<img alt="" border="0" src="https://www.sandbox.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1">
									</form>

									 -->

                                    <div>
										<?php // SANDBOX PAYPAL TESTING
										$sandbox          = "";
										$iconURL          = "www.paypalobjects.com";
										$hosted_button_id = "64685RTC9LJEG";
										$target_return    = ( $reg_action == reg_type_new() ) ? "/membership-registration-confirmation/" : "/membership-renewal-confirmation/";
										$url              = home_url();
										if ( $debug || preg_match( "/localhost/", $url ) || preg_match( "/dev\.ctxphc/", $url ) ) {
											$sandbox = "sandbox.";
											$iconURL = "www.sandbox.paypal.com";
											//$hosted_button_id = "RKYWX7M3LFFMN";
											$hosted_button_id = "9N5Z7PJKJMDA4";
										}
										?>
                                        <form action="https://www.<?php echo( $sandbox ) ?>paypal.com/cgi-bin/webscr"
                                              method="post"
                                              target="_top">
                                            <input type="hidden" name="cmd" value="_s-xclick">
                                            <input type="hidden" name="hosted_button_id"
                                                   value="<?php echo( $hosted_button_id ) ?>">
                                            <table>
                                                <tr>
                                                    <td>
                                                        <input type="hidden" name="on0"
                                                               value="Registration Type">Registration
                                                        Type
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <select name="os0">
                                                            <option <?php if ( 1 == $member_meta_data[ 'mb' ][ 'membership_type' ] ) {
																echo 'selected="selected"';
															} ?> value="Individual">Individual
                                                                $25.00 USD
                                                            </option>
                                                            <option <?php if ( 2 == $member_meta_data[ 'mb' ][ 'membership_type' ] ) {
																echo 'selected="selected"';
															} ?> value="Individual+Children">
                                                                Individual+Children $30.00 USD
                                                            </option>
                                                            <option <?php if ( 3 == $member_meta_data[ 'mb' ][ 'membership_type' ] ) {
																echo 'selected="selected"';
															} ?> value="Couple">Couple $40.00 USD
                                                            </option>
                                                            <option <?php if ( 4 == $member_meta_data[ 'mb' ][ 'membership_type' ] ) {
																echo 'selected="selected"';
															} ?> value="Household">Household $45.00
                                                                USD
                                                            </option>
                                                        </select>
                                                    </td>
                                                </tr>
                                            </table>
                                            <input type="hidden" name="return"
                                                   value="<?php echo bloginfo( 'url' ), $target_return; ?>">
                                            <input type="hidden" name="currency_code" value="USD">
                                            <input type="image"
                                                   src="https://www.<?php echo( $sandbox ) ?>paypal.com/en_US/i/btn/btn_paynowCC_LG.gif"
                                                   name="submit"
                                                   alt="PayPal - The safer, easier way to pay online!">
                                            <img alt=""
                                                 src="https://www.<?php echo( $sandbox ) ?>paypal.com/en_US/i/scr/pixel.gif"
                                                 width="1"
                                                 height="1">
                                        </form>
                                    </div>
                                </div>


                                <div class="cont_rt_col">
                                    <div>
                                        <form id="regForm" name="regForm" method="post"
                                              action="<?php bloginfo( 'url' ); ?>?page_id=1572">
                                            <input type="hidden" name="membID"
                                                   value="<?php echo $mb_id; ?>">
                                            <input class="ctxphc_button3 screen" id="check"
                                                   type="submit" name="check" value="Check"
                                                   onClick="window.print()">
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                    <!-- end of css id PAYMENT -->

					<?php wp_link_pages( array(
						'before'         => '<div><strong>Pages: ',
						'after'          => '</strong></div>',
						'next_or_number' => 'number',
					) ); ?>
                    <div class="clear"></div>
                </div>
                <!-- entry -->
            </div>
            <!-- post -->
			<?php
		endwhile;
		endif; ?>
    </div>
    <!-- content -->
<?php get_sidebar(); ?>
<?php get_footer(); ?>
<?php
$message = "End display of member registration verification form.  ";
debug_log_message( $message, $debug );