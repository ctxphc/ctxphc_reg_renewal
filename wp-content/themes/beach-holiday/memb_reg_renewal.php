<?php
/*
Template Name: Memb_Registration
*/

global $defSel, $wpdb, $memb_error;

?>

<?php // if (!is_user_logged_in()) { auth_redirect(); } //User must be logged in to access this page!?>
<?php //$wpdb->show_errors(); ?>

<?php

if ( ! is_user_logged_in() ) {
	auth_redirect();
}

$possible_associated_member_id_keys = array(
	'sp_id',
	'c1_id',
	'membUserID',
	'prim_memb_id',
);

$prime_member_id       = null;
$cur_user_relationship = null;
$populate_form         = array();
$report                = "<ul>";

$orig_user_data = array();
$orig_user_meta = array();

$reg_action = reg_type_renew();

$display_reg_warning = false;

$states_arr = load_states_array();

$membership_type_table   = 'ctxphc_membership_types';
$relationship_type_table = 'ctxphc_member_relationships';
$status_type_table       = 'ctxphc_member_status';

$relationship_types = get_types( $relationship_type_table );
$membership_types   = get_types( $membership_type_table );
$memb_costs         = get_membership_pricing();

//$form_data_map = load_form_data_map();
$relationship_id_map = member_relationship_value_map();
$membership_type_map = member_membership_type_value_map();

#retrieve current user_data
$cur_user_info = wp_get_current_user();
$cur_user_id   = get_current_user_id();
$cur_user_data = get_userdata( $cur_user_id );
$cur_user_meta = get_user_meta( $cur_user_id );
$usr_metadata  = $cur_user_meta;

get_header();
/*
$user1_count = 0;
foreach ( $possible_associated_member_id_keys as $check_key ) {
	$user1_count ++;
	$u1_id = "user_id_{$user1_count}";
	if ( $cur_user_info->__isset( $check_key ) ) {
		${$u1_id} = $cur_user_info->__get( $check_key );
		if ( $cur_user_info->__get( 'ID' ) <> ${$u1_id} ) {
			$user_info[] = get_userdata( ${$u1_id} );
			$report .= "<li>" . $check_key . " value is " . ${$u1_id};
		}
	}
}

$user2_count = 0;
foreach ( $possible_associated_member_id_keys as $check_key ) {
	$user2_count ++;
	$u2_id = "user_id_{$user2_count}";
	foreach ( $user_info as $user_data ) {
		if ( $user_data->__isset( $check_key ) ) {
			${$u2_id} = $user_data->__get( $check_key );
			if ( $user_data->__get( 'ID' ) <> ${$u2_id} ) {
				$user_info[] = get_userdata( ${$u2_id} );
				$report .= "<li>" . $check_key . " value is " . ${$u2_id};
			}
		}
	}
}
$report .= "</ul>";
echo $report;


foreach ( $associated_member_ids as $associated_member_id ) {
	$account_info[] = get_userdata( $associated_member_id );
}

foreach ( $account_info as $member_info ) {
	if ( $member_info->__isset() ) {

	}
}
*/

$associated_member_types = get_associated_member_types();

foreach ( $associated_member_types as $associated_member_type ) {

	$associated_member_id_keys = get_associated_member_id_keys( $associated_member_type );

	if ( $associated_member_type == 'mb' ) {
		continue; // Do not need to test for mb_id.
	}

	$associated_member_id_key = $associated_member_type . "_id";

	$metadata_field_map = map_metadata_to_form_fields( $associated_member_type );

	if ( $cur_user_info->__isset( $associated_member_id_key ) ) {
		foreach ( get_relationship_keys() as $rel_key ) {
			if ( ! isset( $cur_user_relationship ) ) {
				if ( $cur_user_info->__isset( $rel_key ) ) {
					if ( ! isset( $populate_form[ $metadata_field_map[ $rel_key ] ] ) ) {
						foreach ( member_relationship_value_map() as $mr_key => $mr_value ) {
							if ( $cur_user_info->__get( $rel_key ) == $mr_value ) {
								if ( ! isset( $cur_user_relationship ) ) {
									$cur_user_relationship = $mr_value;
								}
							} else if ( $cur_user_info->__get( $rel_key ) == $mr_key ) {
								$cur_user_relationship = $mr_key;
							}
						}
					}
				}
			}
		}


		if ( $cur_user_relationship == 1 || $cur_user_relationship == 'M' ) {
			$cur_user_relationship = null;
			$prime_member_id       = $cur_user_info->__get( 'ID' );

			// load primary members orig-info
			$account_info[ 'mb' ]   = get_member_data( $prime_member_id );
			$orig_user_data[ 'mb' ] = get_member_data( $prime_member_id );
			$orig_user_meta[ 'mb' ] = get_member_metadata( $prime_member_id );
		} else {
			// user is an associated member
			if ( ! isset( $prime_member_id ) ) {
				// get primary members info
				$prime_member_id = get_primary_member_id( $cur_user_meta );

				// load primary members orig-info
				$account_info[ 'mb' ]   = get_member_data( $prime_member_id );
				$orig_user_data[ 'mb' ] = get_member_data( $prime_member_id );
				$orig_user_meta[ 'mb' ] = get_member_metadata( $prime_member_id );
			}
		}

		if ( $cur_user_info->__isset( $associated_member_id_key ) ) {
			//load associated member's info
			$account_info[ $associated_member_type ]   = get_userdata( $cur_user_info->__get( $associated_member_id_key ) );
			$orig_user_data[ $associated_member_type ] = get_member_data( $cur_user_info->__get( $associated_member_id_key ) );
			$orig_user_meta[ $associated_member_type ] = get_member_metadata( $cur_user_info->__get( $associated_member_id_key ) );
		}

		foreach ( $account_info as $member_key => $member_info ) {
			$metadata_field_map = map_metadata_to_form_fields( $member_key );
			foreach ( $metadata_field_map as $meta_field_key => $form_field_key ) {
				if ( $member_info->__isset( $meta_field_key ) && ! empty( $member_info->__get( $meta_field_key ) ) ) {
					$prepared_meta_data = prepare_member_data( $member_info->__get( $meta_field_key ), $meta_field_key );

					if ( $prepared_meta_data <> $populate_form[ $form_field_key ] ) {
						$populate_form[ $form_field_key ] = $prepared_meta_data;
					}
				}
			}
		}
	}
}

// capture original user info in session variable
// for reference and comparison
$_SESSION[ 'orig_user_data' ] = $orig_user_data;
$_SESSION[ 'orig_user_meta' ] = $orig_user_meta;

?>

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
                    <input type="hidden" name="mb_relationship" value=1/>
                    <input type="hidden" name="reg_action" value="<?php echo $reg_action ?>"/>
                    <input type="hidden" name="mb_id"
                           value="<?php echo( isset( $populate_form[ "mb_id" ] ) ? $populate_form[ "mb_id" ] : "" ); ?>"/>
                    <input type="hidden" name="sp_id"
                           value="<?php echo( isset( $populate_form[ "sp_id" ] ) ? $populate_form[ "sp_id" ] : "" ); ?>"/>
                    <input type="hidden" name="c1_id"
                           value="<?php echo( isset( $populate_form[ "c1_id" ] ) ? $populate_form[ "c1_id" ] : "" ); ?>"/>
                    <input type="hidden" name="c2_id"
                           value="<?php echo( isset( $populate_form[ "c2_id" ] ) ? $populate_form[ "c2_id" ] : "" ); ?>"/>
                    <input type="hidden" name="c3_id"
                           value="<?php echo( isset( $populate_form[ "c3_id" ] ) ? $populate_form[ "c3_id" ] : "" ); ?>"/>
                    <input type="hidden" name="c4_id"
                           value="<?php echo( isset( $populate_form[ "c4_id" ] ) ? $populate_form[ "c4_id" ] : "" ); ?>"/>
                    <fieldset class="reg_form" id="memb_type">
                        <legend><span class="memb_legend">Membership Options</span></legend>
                        <div class="memb_type" id="memb_type_div">
                            <!-- Individual Member Option current member-->
                            <input class="memb_type" id="memb_type_ID" type="radio" name="memb_type"
                                   value="1" <?php echo ( $populate_form[ 'memb_type' ] == 1 ) ? "checked=\"checked\"" : ""; ?> />
                            <label class="memb_type" for="memb_type_ID">Individual
                                $<?php echo $memb_costs[ 1 ]->cost; ?></label>

                            <!-- Individual + Child(ren) Member Option -->
                            <input class="memb_type" id="memb_type_IC" type="radio" name="memb_type"
                                   value="2" <?php echo ( $populate_form[ 'memb_type' ] == 2 ) ? "checked=\"checked\"" : ""; ?> />
                            <label class="memb_type" for="memb_type_IC">Individual + Children
                                $<?php echo $memb_costs[ 2 ]->cost; ?></label>

                            <!-- Couple Member Option -->
                            <input class="memb_type" id="memb_type_CO" type="radio" name="memb_type"
                                   value="3" <?php echo ( $populate_form[ 'memb_type' ] == 3 ) ? "checked=\"checked\"" : ""; ?>/>
                            <label class="memb_type" for="memb_type_CO">Couple
                                $<?php echo $memb_costs[ 3 ]->cost; ?></label>

                            <!-- Household Member Option -->
                            <input class="memb_type" id="memb_type_HH" type="radio" name="memb_type"
                                   value="4" <?php echo ( $populate_form[ 'memb_type' ] == 4 ) ? "checked=\"checked\"" : ""; ?> />
                            <label class="memb_type" for="memb_type_HH">Household
                                $<?php echo $memb_costs[ 4 ]->cost; ?></label>
                        </div>
                    </fieldset>

                    <div class="spacer"></div>

                    <fieldset class="reg_form" id="personal_info">
                        <legend><span class="memb_legend">Your Information</span></legend>
                        <div class="reg_form_row" id="personal_info_div">
                            <label class="reg_first_name" id="lbl_mb_first_name"
                                   for="mb_first_name">First Name:</label>
                            <input class="reg_first_name validate[required, custom[onlyLetterSp]]"
                                   data-prompt-position="bottomLeft" id="mb_first_name"
                                   name="mb_first_name" type="text"
                                   value="<?php echo( isset( $populate_form[ "mb_first_name" ] ) ? $populate_form[ "mb_first_name" ] : "" ); ?>"
                                   title="first_name"/>

                            <label class="reg_last_name" id="lbl_mb_last_name" for="mb_last_name">Last
                                Name:</label>
                            <input class="reg_last_name validate[required, custom[onlyLetterSp]]"
                                   data-prompt-position="bottomLeft" id="mb_last_name"
                                   name="mb_last_name" type="text"
                                   value="<?php echo( isset( $populate_form[ "mb_last_name" ] ) ? $populate_form[ "mb_last_name" ] : "" ); ?>"/>
                        </div>
                        <div class="reg_form_row">
                            <label class="cm_birthday" id="lbl_mb_birthday" for="mb_birthday">Birthdate:</label>
                            <input class="cm_birthday validate[required, custom[date]]"
                                   data-prompt-position="bottomLeft" id="mb_birthday"
                                   name="mb_birthday" type="date"
                                   value="<?php echo( isset( $populate_form[ "mb_birthday" ] ) ? $populate_form[ "mb_birthday" ] : "" ); ?>"/>

                            <label class="reg_email" id="lbl_mb_email" for="mb_email">Email:</label>
                            <input class="reg_email validate[required, custom[email]]"
                                   data-prompt-position="bottomLeft"
                                   id="mb_email" name="mb_email" type="email"
                                   value="<?php echo( isset( $populate_form[ "mb_email" ] ) ? $populate_form[ "mb_email" ] : "" ); ?>"/>
                        </div>
                        <div class="reg_form_row">
                            <label class="reg_phone" id="lbl_mb_phone" for="mb_phone">Phone:</label>
                            <input class="reg_phone validate[required, custom[phone]]"
                                   data-prompt-position="bottomLeft" id="mb_phone" name="mb_phone"
                                   type="tel"
                                   value="<?php echo( isset( $populate_form[ "mb_phone" ] ) ? $populate_form[ "mb_phone" ] : "" ); ?>"/>

                            <label id="lbl_mb_occupation" for="mb_occupation">Occupation:</label>
                            <input class="validate[required, custom[onlyLetterSp]]"
                                   data-prompt-position="bottomLeft"
                                   id="mb_occupation" name="mb_occupation" type="text"
                                   value="<?php echo( isset( $populate_form[ "mb_occupation" ] ) ? $populate_form[ "mb_occupation" ] : "" ); ?>"/>
                        </div>
                    </fieldset>

                    <div class="spacer"></div>

                    <fieldset class="reg_form" id="mb_address">
                        <legend><span class="memb_legend">Address</span></legend>
                        <div class="reg_form_row">
                            <label id="lbl_mb_addr1" for="mb_addr1">Address:</label>
                            <input class="validate[required, custom[address]]"
                                   data-prompt-position="bottomLeft"
                                   id="mb_addr1" name="mb_addr1" type="text"
                                   value="<?php echo( isset( $populate_form[ "mb_addr1" ] ) ? $populate_form[ "mb_addr1" ] : "" ); ?>"/>

                            <label id="lbl_mb_addr2" for="mb_addr2">Suite/Apt:</label>
                            <input class="validate[custom[onlyLetterNumber]]"
                                   data-prompt-position="bottomLeft"
                                   id="mb_addr2" name="mb_addr2" type="text"
                                   value="<?php echo( isset( $populate_form[ "mb_addr2" ] ) ? $populate_form[ "mb_addr2" ] : "" ); ?>"/>
                        </div>
                        <div class="reg_form_row">
                            <label id="lbl_mb_city" for="mb_city">City:</label>
                            <input class="validate[required, custom[onlyLetterSp]]"
                                   data-prompt-position="bottomLeft"
                                   id="mb_city" name="mb_city" type="text"
                                   value="<?php echo( isset( $populate_form[ "mb_city" ] ) ? $populate_form[ "mb_city" ] : "" ); ?>"/>

                            <label id="lbl_mb_state" for="mb_state">State:</label>
                            <select class="validate[required]" id="mb_state" name="mb_state">
								<?php $defSel = isset( $populate_form[ "mb_state" ] ) ? $populate_form[ "mb_state" ] : "TX";
								echo showOptionsDrop( $states_arr, $defSel, true ); ?>
                            </select>

                            <label id="lbl_mb_zip" for="mb_zip">Zip:</label>
                            <input id="mb_zip" class="validate[required, custom[zip]]"
                                   data-prompt-position="bottomLeft" name="mb_zip" type="text"
                                   value="<?php echo( isset( $populate_form[ "mb_zip" ] ) ? $populate_form[ "mb_zip" ] : "" ); ?>"/>
                        </div>
                    </fieldset>

                    <div class="spacer" id="spouse_spacer"></div>

                    <fieldset class="reg_form" id="spouse_info">
                        <legend><span class="memb_legend">Spouse/Partner</span></legend>
                        <div class="reg_form_row">
                            <label class="reg_first_name" id="lbl_sp_first_name"
                                   for="sp_first_name">First Name:</label>
                            <input class="reg_first_name validate[custom[onlyLetterSp]]"
                                   data-prompt-position="bottomLeft" id="sp_first_name"
                                   name="sp_first_name" type="text"
                                   value="<?php echo( isset( $populate_form[ "sp_first_name" ] ) ? $populate_form[ "sp_first_name" ] : "" ); ?>"/>

                            <label class="reg_last_name" id="lbl_sp_last_name" for="sp_last_name">Last
                                Name:</label>
                            <input class="reg_last_name validate[condRequired[sp_first_name], custom[onlyLetterSp]]"
                                   data-prompt-position="bottomLeft" id="sp_last_name"
                                   name="sp_last_name" type="text"
                                   value="<?php echo( isset( $populate_form[ "sp_last_name" ] ) ? $populate_form[ "sp_last_name" ] : "" ); ?>"/>
                        </div>
                        <div class="reg_form_row">
                            <label class="cm_birthday" id="lbl_sp_birthday" for="sp_birthday">Birthdate:</label>
                            <input class="cm_birthday validate[condRequired[sp_first_name], custom[date]]"
                                   id="sp_birthday"
                                   data-prompt-position="bottomLeft" name="sp_birthday" type="date"
                                   value="<?php echo( isset( $populate_form[ "sp_birthday" ] ) ? $populate_form[ "sp_birthday" ] : "" ); ?>"/>

                            <label class="reg_email" id="lbl_sp_email" for="sp_email">Email:</label>
                            <input class="reg_email validate[condRequired[sp_first_name], custom[email]]"
                                   data-prompt-position="bottomLeft"
                                   id="sp_email" name="sp_email" type="email"
                                   value="<?php echo( isset( $populate_form[ "sp_email" ] ) ? $populate_form[ "sp_email" ] : "" ); ?>"/>
                        </div>
                        <div class="reg_form_row">
                            <label class="reg_phone" id="lbl_sp_phone" for="sp_phone">Phone:</label>
                            <input class="reg_phone validate[condRequired[sp_first_name], custom[phone]]"
                                   data-prompt-position="bottomLeft"
                                   id="sp_phone" name="sp_phone" type="tel"
                                   value="<?php echo( isset( $populate_form[ "sp_phone" ] ) ? $populate_form[ "sp_phone" ] : "" ); ?>"/>

                            <label class="sp_relationship" id="lbl_sp_relationship"
                                   for="sp_relationship">Relationship:</label>
                            <select class="sp_relationship validate[condRequired[sp_first_name]]"
                                    id="sp_relationship" name="sp_relationship">
								<?php $defSel = isset( $populate_form[ "sp_relationship" ] ) ? $populate_form[ "sp_relationship" ] : 2 ?>
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
                                <label class="reg_first_name" for="c1_first_name">First
                                    Name:</label>
                                <input class="reg_first_name validate[custom[onlyLetterSp]]"
                                       data-prompt-position="bottomLeft" id="c1_first_name"
                                       name="c1_first_name"
                                       type="text"
                                       value="<?php echo( isset( $populate_form[ "c1_first_name" ] ) ? $populate_form[ "c1_first_name" ] : "" ); ?>"/>

                                <label class="reg_last_name" for="c1_last_name">Last Name:</label>
                                <input class="reg_last_name validate[condRequired[c1_first_name], custom[onlyLetterSp]]"
                                       data-prompt-position="bottomLeft" id="c1_last_name"
                                       name="c1_last_name"
                                       type="text"
                                       value="<?php echo( isset( $populate_form[ "c1_last_name" ] ) ? $populate_form[ "c1_last_name" ] : "" ); ?>"/>
                            </div>
                            <div class="reg_form_row">
                                <label class="cm_birthday" id="lbl_c_birthday" for="c1_birthday">Birthdate:</label>
                                <input class="cm_birthday validate[condRequired[c1_first_name], custom[date]]"
                                       data-prompt-position="bottomLeft" id="c1_birthday"
                                       name="c1_birthday" type="date"
                                       value="<?php echo( isset( $populate_form[ "c1_birthday" ] ) ? $populate_form[ "c1_birthday" ] : "" ); ?>"/>


                                <label class="child_relationship" id="lbl_c1_relationship"
                                       for="c1_relationship">Relationship:</label>
                                <select class="child_relationship validate[condRequired[c1_first_name]]"
                                        id="c1_relationship" name="c1_relationship">
									<?php $defSel = isset( $populate_form[ "c1_relationship" ] ) ? $populate_form[ "c1_relationship" ] : 4; ?>
									<?php echo showOptionsDrop( $relationship_types, $defSel, true ); ?>
                                </select>
                            </div>
                            <div class="reg_form_row">
                                <label class="child_email" id="lbl_c1_email"
                                       for="c1_email">Email:</label>
                                <input class="child_email validate[condRequired[c1_first_name], custom[email]]"
                                       data-prompt-position="bottomLeft"
                                       id="c1_email" name="c1_email" type="email"
                                       value="<?php echo( isset( $populate_form[ "c1_email" ] ) ? $populate_form[ "c1_email" ] : "" ); ?>"/>
                            </div>
                        </section>
                        <!--  //END of CHILD1 -->

                        <div class="spacer"></div>
                        <!--    BEGIN 2ND FAMILY MEMBER  -->

                        <section id="child2">
                            <div class="reg_form_row">
                                <label class="reg_first_name" id="lbl_c2_first_name"
                                       for="c2_first_name">First
                                    Name:</label>
                                <input class="reg_first_name validate[custom[onlyLetterSp]]"
                                       data-prompt-position="bottomLeft" id="c2_first_name"
                                       name="c2_first_name"
                                       type="text"
                                       value="<?php echo( isset( $populate_form[ "c2_first_name" ] ) ? $populate_form[ "c2_first_name" ] : "" ); ?>"/>

                                <label class="reg_last_name" id="lbl_c2_last_name"
                                       for="c2_last_name">Last Name:</label>
                                <input class="reg_last_name validate[condRequired[c2_first_name],custom[onlyLetterSp]]"
                                       data-prompt-position="bottomLeft" id="c2_last_name"
                                       name="c2_last_name"
                                       type="text"
                                       value="<?php echo( isset( $populate_form[ "c2_last_name" ] ) ? $populate_form[ "c2_last_name" ] : "" ); ?>"/>
                            </div>
                            <div class="reg_form_row">
                                <label class="cm_birthday" id="lbl_c2_birthday" for="c2_birthday">Birthdate:</label>
                                <input class="cm_birthday validate[condRequired[c2_first_name],custom[date]]"
                                       id="c2_birthday" name="c2_birthday"
                                       type="date"
                                       value="<?php echo( isset( $populate_form[ "c2_birthday" ] ) ? $populate_form[ "c2_birthday" ] : "" ); ?>"/>

                                <label class="child_relationship" id="lbl_c2_relationship"
                                       for="c2_relationship">Relationship:</label>
                                <select class="child_relationship validate[condRequired[c2_first_name]]"
                                        id="c2_relationship" name="c2_relationship">
									<?php $defSel = isset( $populate_form[ "c2_relationship" ] ) ? $populate_form[ "c2_relationship" ] : 4; ?>
									<?php echo showOptionsDrop( $relationship_types, $defSel, true ); ?>
                                </select>
                            </div>
                            <div class="reg_form_row">
                                <label class="child_email" id="lbl_c2_email"
                                       for="c2_email">Email:</label>
                                <input class="child_email validate[condRequired[c2_first_name], custom[email]]"
                                       data-prompt-position="bottomLeft"
                                       id="c2_email" name="c2_email" type="email"
                                       value="<?php echo( isset( $populate_form[ "c2_email" ] ) ? $populate_form[ "c2_email" ] : "" ); ?>"/>
                            </div>
                        </section>
                        <!--  //END CHILD2 -->

                        <div class="spacer"></div>
                        <!--    BEGIN 3RD FAMILY MEMBER  -->

                        <section id="child3">
                            <div class="reg_form_row">
                                <label class="reg_first_name" id="lbl_c3_first_name"
                                       for="c3_first_name">First
                                    Name:</label>
                                <input class="reg_first_name validate[custom[onlyLetterSp]]"
                                       data-prompt-position="bottomLeft" id="c3_first_name"
                                       name="c3_first_name"
                                       type="text"
                                       value="<?php echo( isset( $populate_form[ "c3_first_name" ] ) ? $populate_form[ "c3_first_name" ] : "" ); ?>"/>

                                <label class="reg_last_name" id="lbl_c3_last_name"
                                       for="c3_last_name">Last Name:</label>
                                <input class="reg_last_name validate[condRequired[c3_first_name], custom[onlyLetterSp]]"
                                       data-prompt-position="bottomLeft" id="c3_last_name"
                                       name="c3_last_name"
                                       type="text"
                                       value="<?php echo( isset( $populate_form[ "c3_last_name" ] ) ? $populate_form[ "c3_last_name" ] : "" ); ?>"/>
                            </div>
                            <div class="reg_form_row">
                                <label class="cm_birthday" id="lbl_c3_birthday" for="c3_birthday">Birthdate:</label>
                                <input class="cm_birthday validate[condRequired[c3_first_name] custom[date]]"
                                       id="c3_birthday" name="c3_birthday"
                                       type="date"
                                       value="<?php echo( isset( $populate_form[ "c3_birthday" ] ) ? $populate_form[ "c3_birthday" ] : "" ); ?>"/>

                                <label class="child_relationship" id="lbl_c3_relationship"
                                       for="c3_relationship">Relationship:</label>
                                <select class="child_relationship validate[condRequired[c3_first_name]]"
                                        id="c3_relationship" name="c3_relationship">
									<?php $defSel = isset( $populate_form[ "c3_relationship" ] ) ? $populate_form[ "c3_relationship" ] : 4; ?>
									<?php echo showOptionsDrop( $relationship_types, $defSel, true ); ?>
                                </select>
                            </div>
                            <div class="reg_form_row">
                                <label class="child_email" id="lbl_c3_email"
                                       for="c3_email">Email:</label>
                                <input class="child_email validate[condRequired[c3_first_name],custom[email]]"
                                       data-prompt-position="bottomLeft"
                                       id="c3_email" name="c3_email" type="email"
                                       value="<?php echo( isset( $populate_form[ "c3_email" ] ) ? $populate_form[ "c3_email" ] : "" ); ?>"/>
                            </div>
                        </section>
                        <!--  //END of CHILD3  -->

                        <div class="spacer"></div>
                        <!--    BEGIN 4th FAMILY MEMBER  -->

                        <section id="child4">
                            <div class="reg_form_row">
                                <label class="reg_first_name" id="lbl_c4_first_name"
                                       for="c4_first_name">First
                                    Name:</label>
                                <input class="reg_first_name validate[custom[onlyLetterSp]]"
                                       data-prompt-position="bottomLeft" id="c4_first_name"
                                       name="c4_first_name"
                                       type="text"
                                       value="<?php echo( isset( $populate_form[ "c4_first_name" ] ) ? $populate_form[ "c4_first_name" ] : "" ); ?>"/>

                                <label class="reg_last_name" id="lbl_c4_last_name"
                                       for="c4_last_name">Last Name:</label>
                                <input class="reg_last_name validate[condRequired[c4_first_name], custom[onlyLetterSp]]"
                                       data-prompt-position="bottomLeft" id="c4_last_name"
                                       name="c4_last_name"
                                       type="text"
                                       value="<?php echo( isset( $populate_form[ "c4_last_name" ] ) ? $populate_form[ "c4_last_name" ] : "" ); ?>"/>
                            </div>
                            <div class="reg_form_row">
                                <label class="cm_birthday" id="lbl_c4_birthday" for="c4_birthday">Birthdate:</label>
                                <input class="cm_birthday validate[condRequired[c4_first_name] custom[date]]"
                                       id="c4_birthday" name="c4_birthday"
                                       type="date"
                                       value="<?php echo( isset( $populate_form[ "c4_birthday" ] ) ? $populate_form[ "c4_birthday" ] : "" ); ?>"/>

                                <label class="child_relationship" id="lbl_c4_relationship"
                                       for="c4_relationship">Relationship:</label>
                                <select class="child_relationship validate[condRequired[c4_first_name]]"
                                        id="c4_relationship" name="c4_relationship">
									<?php $defSel = isset( $populate_form[ "c4_relationship" ] ) ? $populate_form[ "c4_relationship" ] : 4; ?>
									<?php echo showOptionsDrop( $relationship_types, $defSel, true ); ?>
                                </select>
                            </div>
                            <div class="reg_form_row">
                                <label class="child_email" id="lbl_c4_email"
                                       for="c4_email">Email:</label>
                                <input class="child_email validate[condRequired[c4_first_name], custom[email]]"
                                       data-prompt-position="bottomLeft"
                                       id="c4_email" name="c4_email" type="email"
                                       value="<?php echo( isset( $populate_form[ "c4_email" ] ) ? $populate_form[ "c4_email" ] : "" ); ?>"/>
                            </div>
                        </section>
                        <!--  //END of CHILD4  -->
                    </fieldset>

                    <div class="spacer"></div>

                    <div>
                        <input class="ctxphc_button3 screen" id="reg_submit" type="submit"
                               name="registration"
                               value="Submit"/>
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
	