<?php

/**
 *
 * @param $rpt_type
 * @param $order_by
 * @param $order
 *
 * @return OBJECT $members
 */
function cm_get_members( $args ) {
	/** @var wpdb $wpdb */
	global $wpdb;

	/**
	 * blog_id - The current blog's ID, unless multisite is enabled and another ID is provided
	 * role - Limit the returned users to the role specified.
	 * include - An array of IDs. Only users matching these IDs will be returned. Note that at present the include and exclude (below) arguments cannot be used together. See ticket #23228
	 * exclude - An array of IDs. Users matching these IDs will not be returned, regardless of the other arguments. It will be ignored if the include argument is set.
	 * meta_key - The meta_key in the wp_usermeta table for the meta_value to be returned. See get_userdata() for the possible meta keys.
	 * meta_value - The value of the meta key.
	 * meta_compare - Operator to test the 'meta_value'. Possible values are '!=', '>', '>=', '<', or '<='. Default value is '='.
	 * meta_query - A WP_Meta_Query.
	 * orderby - Sort by 'ID', 'login', 'nicename', 'email', 'url', 'registered', 'display_name', 'post_count', or 'meta_value' (query must also contain a 'meta_key' - see WP_User_Query).
	 * order - ASC (ascending) or DESC (descending).
	 * offset - The first n users to be skipped in the returned array.
	 * search - Use this argument to search users by email address, URL, ID or username (this does not currently include display name).
	 * number - Limit the total number of users returned.
	 * count_total - This is always false. To get a total count, call WP_User_Query directly instead. See here for usage.
	 * fields - Which fields to include in the returned array. Default is 'all'. Pass an array of wp_users table fields to return an array of stdClass objects with only those fields.
	 * who - If set to 'authors', only authors (user level greater than 0) will be returned.
	 *
	 *
	 * Returns (Array)
	 * An array of IDs, stdClass objects, or WP_User objects, depending on the value of the 'fields' parameter.
	 * If 'fields' is set to 'all' (default), or 'all_with_meta', it will return an array of WP_User objects.
	 * If 'fields' is set to an array of wp_users table fields, it will return an array of stdClass objects with only those fields.
	 * If 'fields' is set to any individual wp_users table field, an array of IDs will be returned.
	 *
	 */
	/**
	 * $args = array(
	 * 'blog_id'      => $GLOBALS['blog_id'],
	 * 'role'         => '',
	 * 'meta_key'     => '',
	 * 'meta_value'   => '',
	 * 'meta_compare' => '',
	 * 'meta_query'   => array(),
	 * 'include'      => array(),
	 * 'exclude'      => array(),
	 * 'orderby'      => 'login',
	 * 'order'        => 'ASC',
	 * 'offset'       => '',
	 * 'search'       => '',
	 * 'number'       => '',
	 * 'count_total'  => false,
	 * 'fields'       => 'all',
	 * 'who'          => ''
	 * );
	 */

	$members = get_users( $args );
	//print_r( $members );
	//$wpdb->print_error();

	return $members;
}

function count_members( $type ) {
	/** @var wpdb $wpdb */
	global $wpdb;
	$rows = new stdClass;

	$query   = 'SELECT mtype, COUNT(*) AS memb_count FROM
		(SELECT b.memb_type AS mtype FROM ctxphc_members a JOIN ctxphc_membership_types b WHERE a.membership_type = b.ID)
		AS mt WHERE mtype = %s GROUP BY mtype';
	$results = $wpdb->get_results( $wpdb->prepare( $query, $type ) );
	foreach ( $results as $row ) {
		foreach ( $row as $key => $value ) {
			$rows->$key = $value;
		}
	}

	return $rows;
}

function count_memb_status( $type ) {

	$member_statuses = array(
		'Pending'  => 0,
		'Active'   => 1,
		'Archived' => 2,
		'All'      => 3,
	);


	if ( 3 > $member_statuses[ $type ] ) {
		$query_args = array(
			'fields'     => 'all_with_meta',
			'meta_query' => array(
				'meta_key'   => 'status_id',
				'meta_value' => $member_statuses[ $type ],
			),
		);
		$cm_members = new WP_User_Query( $query_args );

		return $cm_members->get_total();
	} else {
		return count_users();
	}
}

/**
 * Gets next months name for use in birthday metabox.
 *
 * @param $date_format
 *
 * @return string
 */
function get_next_months_name( $date_format ) {
	$now = new datetime();
	$now->modify( 'first day of next month' );

	return $now->format( $date_format );
}


/**
 * @param $args
 *
 * @return false|int
 */
function cm_update_member( $args ) {
	//todo: update metadata or userdata for the userid passed.
	$update_results = null;

	return $update_results;
}


/**
 * @param $args
 *
 * @return false|int
 */
function cm_delete_member( $userid, $reassign = '' ) {
	require_once( ABSPATH . 'wp-admin/includes/user.php' );

	//todo: determine if user has not posts.
	$user_post_count = count_user_posts( $userid );
	if ( 0 < $user_post_count ) {
		$delete_results = wp_delete_user( $userid );
	} else {
		//todo: some kind of interface to select who to associate posts with.
	}


	return $delete_results;
}

function cm_edit_member( $args ) {
	/** @var wpdb $wpdb */
	global $wpdb;

	$existing_member_data = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM %s WHERE ID = %d", $args[ 'table' ], $args[ 'ID' ] ) );

	if ( $existing_member_data ) {
		//display edit form with existing data in-place.
	}

	return 'result of edit';
}


function cm_member_search( $search ) {
	global $wpdb;
	//figure out how to deal with the search parameters.
	$search_results = '';
	if ( $search ) {
		echo "need to figure out how to process the search info and return it so it only gets the info needed.";

		$search_results = $wpdb->get_results( $wpdb->prepare( "SELECT ID FROM ctxphc_members WHERE first_name LIKE %s OR WHERE last_name LIKE %s", $search, $search ) );

		if ( ! $search_results ) {
			$search_results = "No members matched your search results.  Try again.";
		}
	}
	$rpt_type = ( ! empty( $_REQUEST[ 'cm-view' ] ) ? $_REQUEST[ 'cm-view' ] : 'active' );

	render_members_report( $rpt_type, $search_results );
}


/**
 * Resets global variables based on $_GET and $_POST
 *
 * This function resets global variables based on the names passed
 * in the $vars array to the value of $_POST[$var] or $_GET[$var] or ''
 * if neither is defined.
 *
 * @since 1.0.0
 *
 * @param array $vars An array of globals to reset.
 */
function cm_reset_vars( $vars ) {
	foreach ( $vars as $var ) {
		if ( empty( $_POST[ $var ] ) ) {
			if ( empty( $_GET[ $var ] ) ) {
				$GLOBALS[ $var ] = '';
			} else {
				$GLOBALS[ $var ] = $_GET[ $var ];
			}
		} else {
			$GLOBALS[ $var ] = $_POST[ $var ];
		}
	}
}

/**
 * Gets a setting from from the plugin settings in the database.
 *
 * @since 1.1.1
 */
function membership_get_setting( $option = '' ) {
	global $membership;

	if ( ! $option ) {
		return false;
	}

	if ( ! isset( $membership->settings ) ) {
		$membership->settings = get_option( 'membership_settings' );
	}

	if ( ! is_array( $membership->settings ) || empty( $membership->settings[ $option ] ) ) {
		return false;
	}

	return $membership->settings[ $option ];
}

/**
 * Retrieve edit member link
 *
 * @since 1.1.1
 *
 * @param int $member_id Optional. User ID. Defaults to the current user.
 *
 * @return string URL to edit user page or empty string.
 */
function get_edit_member_link( $member_id = null ) {
	if ( ! $member_id ) {
		$member_id = get_current_user_id();
	}

	if ( empty( $member_id ) || ! current_user_can( 'edit_user', $member_id ) ) {
		return '';
	}

	$member = get_memberdata( $member_id );

	if ( ! $member ) {
		return '';
	}

	if ( get_current_user_id() == $member->ID ) {
		$link = get_edit_member_profile_url( $member->ID );
	} else {
		$link = add_query_arg( 'user_id', $member->ID, self_admin_url( "admin.php?page=edit_member" ) );
	}

	/**
	 * Filter the member edit link.
	 *
	 * @since 1.1.1
	 *
	 * @param string $link The edit link.
	 * @param int $member_id Member ID.
	 */

	return apply_filters( 'get_edit_member_link', $link, $member->ID );
}


/**
 * @param $member_id
 *
 * @return mixed|string|void
 */
function get_archive_member_link( $member_id ) {
	if ( ! $member_id ) {
		$member_id = get_current_user_id();
	}

	if ( empty( $member_id ) || ! current_user_can( 'edit_user', $member_id ) ) {
		return '';
	}

	$member = get_memberdata( $member_id );

	if ( ! $member ) {
		return '';
	}

	$link = add_query_arg( array(
		'user_id' => $member->ID,
		'action'  => 'archive',
	), wp_unslash( $_SERVER[ 'REQUEST_URI' ] ) );

	/**
	 * Filter the member edit link.
	 *
	 * @since 1.1.1
	 *
	 * @param string $link The edit link.
	 * @param int $member_id Member ID.
	 */

	return $link;    //apply_filters( 'get_archive_member_link', $link, $member->ID );
}


/**
 * Get the URL to the user's profile editor.
 *
 * @since 3.1.0
 *
 * @param int $user_id Optional. User ID. Defaults to current user.
 * @param string $scheme The scheme to use. Default is 'admin', which obeys force_ssl_admin() and is_ssl().
 *                        'http' or 'https' can be passed to force those schemes.
 *
 * @return string Dashboard url link with optional path appended.
 */
function get_edit_member_profile_url( $user_id = 0, $scheme = 'admin' ) {
	$user_id = $user_id ? (int) $user_id : get_current_user_id();

	if ( is_user_admin() ) {
		$url = user_admin_url( 'admin.php?page=edit_member', $scheme );
	} elseif ( is_network_admin() ) {
		$url = network_admin_url( 'admin.php?page=edit_member', $scheme );
	} else {
		$url = get_dashboard_url( $user_id, 'admin.php?page=edit_member', $scheme );
	}

	/**
	 * Filter the URL for a user's profile editor.
	 *
	 * @since 3.1.0
	 *
	 * @param string $url The complete URL including scheme and path.
	 * @param int $user_id The user ID.
	 * @param string $scheme Scheme to give the URL context. Accepts 'http', 'https', 'login',
	 *                        'login_post', 'admin', 'relative' or null.
	 */

	return apply_filters( 'edit_profile_url', $url, $user_id, $scheme );
}

function get_states_array() {
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
		'WY' => "Wyoming",
	);

	return $states_arr;
}

function get_relationship_array() {
	$relationship_arr = array(
		'2' => "Spouse",
		'3' => "Partner",
		'4' => "Child",
		'5' => "Other",
	);

	return $relationship_arr;
}


/**
 * Display Club Membership Profile page
 *
 * displays for the current user or any user that can edit the user
 * as well as for New Members/Users.
 */
//add_action( 'show_user_profile', 'cm_add_membership_profile_fields' );  // Current User
//add_action( 'edit_user_profile', 'cm_add_membership_profile_fields' );  //  Any User
//add_action( 'user_new_form', 'cm_add_membership_profile_fields' );  //  New User page

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

	<?php cm_display_user_pass_fields(); ?>

	<div class="cm-admin-spacer"></div>
	<div class="cm-admin-spacer"></div>

	<legend class="cm-legend"><span class="cm-legend">Membership Type</span></legend>
	<div class="cm-admin-spacer"></div>

	<?php cm_display_membership_types(); ?>

	<div class="cm-admin-spacer"></div>
	<div class="cm-admin-spacer"></div>

	<legend class="cm-legend"><span class="cm-legend">Personal Information</span></legend>
	<div class="cm-admin-spacer"></div>

	<?php cm_display_primary_member_fields(); ?>

	<div class="cm-admin-spacer"></div>

	<legend class="cm-legend"><span class="cm-legend">Address</span></legend>
	<div class="cm-admin-spacer"></div>

	<?php cm_display_address_fields(); ?>

	<div class="cm-admin-spacer" id="spouse_spacer"></div>
	<div class="cm-admin-spacer"></div>

	<legend class="cm-legend"><span class="cm-legend">Spouse/Partner</span></legend>
	<div class="cm-admin-spacer"></div>

	<?php cm_display_spouse_fields(); ?>

	<div class="cm-admin-spacer" id="family_spacer"></div>
	<div class="cm-admin-spacer"></div>

	<legend class="cm-legend"><span class="cm-legend">Family Members</span></legend>
	<div class="cm-admin-spacer"></div>

	<?php cm_display_child1_fields(); ?>

	<div class="cm-admin-spacer"></div>

	<?php cm_display_child2_fields(); ?>

	<div class="cm-admin-spacer"></div>

	<?php cm_display_child3_fields(); ?>

	<div class="cm-admin-spacer"></div>

	<?php cm_display_child4_fields(); ?>

	<div class="cm-admin-spacer"></div>
	<?php
}


//add_action( 'personal_options_update', 'save_membership_profile_fields' );
//add_action( 'edit_user_profile_update', 'save_membership_profile_fields' );
/**
 *
 * Save New and/or Updated profile information
 *
 * @param $user_id
 *
 * @return bool
 */
function save_membership_profile_fields( $user_id ) {

	if ( ! current_user_can( 'edit_user', $user_id ) ) {
		return false;
	}

	$members_data = cm_separate_post_data( $user_id );

	foreach ( $members_data as $memb_key => $memb_obj ) {
		switch ( $memb_key ) {
			case 'primary':
				cm_save_primary_member_fields( $user_id, $memb_obj );
				break;
			case 'address':
				cm_save_address_fields( $user_id, $memb_obj );
				break;
			case 'spouse':
				cm_save_spouse_fields( $user_id, $memb_obj );
				break;
			case 'child1':
				cm_save_child1_fields( $user_id, $memb_obj );
				break;
			case 'child2':
				cm_save_child2_fields( $user_id, $memb_obj );
				break;
			case 'child3':
				cm_save_child3_fields( $user_id, $memb_obj );
				break;
			case 'child4':
				cm_save_child4_fields( $user_id, $memb_obj );
				break;
		}
	}

	return true;
}

function cm_display_user_pass_fields() {
	?>
	<table class="cm-form-table">
		<tr class="cm-profile-labels">
			<td class="cm-profile-col1">
				<label id="cm-mb-username-lbl" for="cm-mb-username">Username:</label>
			</td>
		</tr>
		<tr class="cm-profile-inputs">
			<td class="cm-profile-col2">
				<input class="validate[required, funcCall[cm_does_username_exist]]"
				       data-prompt-position="bottomLeft"
				       id="cm-mb-username" name="mb_username" type="text"/>
			</td>
		</tr>
		<tr class="cm-profile-labels">
			<td class="cm-profile-col1">
				<label id="cm-mb-pass1-lbl" for="cm-mb-pass1">Password</label>
			</td>
			<td class="cm-profile-col2">
				<label id="cm-mb-pass2-lbl" for="cm-mb-pass2">Repeat Password</label>
			</td>
		</tr>
		<tr class="cm-profile-inputs">
			<td class="cm-profile-col1">
				<input class="validate[condRequired[mb_username]], custom[password]]"
				       data-prompt-position="bottomLeft"
				       id="cm-mb-pass1" name="mb_pass1" type="password"/>
			</td>
			<td class="cm-profile-col2">
				<input
					class="validate[condRequired[mb_pass1], equals[mb_pass1]], custom[password]]"
					data-prompt-position="bottomLeft"
					id="cm-mb-pass2" name="mb_pass2" type="password"/>
			</td>
		</tr>
	</table>
	<?php
}

function cm_display_membership_types() {

	/** @var int $cost */
	$memb_costs = get_membership_pricing(); ?>

	<table class="cm-form-table">
		<tr class="cm-profile-labels">
			<!-- Individual Member Option -->
			<td class="cm-form-table-td">
				<input class="cm-memb-type" id="memb_type_1" type="radio" name="memb_type"
				       value="1" checked/>
				<label class="cm-memb-type" for="memb_type_1">Individual
					$<?php echo $memb_costs[ 1 ]->cost; ?></label>

				<!-- Individual + Child(ren) Member Option -->
				<input class="cm-memb-type" id="memb_type_2" type="radio" name="memb_type"
				       value="2"/>
				<label class="cm-memb-type" for="memb_type_2">Individual + Children
					$<?php echo $memb_costs[ 2 ]->cost; ?></label>

				<!-- Couple Member Option -->
				<input class="cm-memb-type" id="memb_type_3" type="radio" name="memb_type"
				       value="3"/>
				<label class="cm-memb-type" for="memb_type_3">Couple
					$<?php echo $memb_costs[ 3 ]->cost; ?></label>

				<!-- Household Member Option -->
				<input class="cm-memb-type" id="memb_type_4" type="radio" name="memb_type"
				       value="4"/>
				<label class="cm-memb-type" for="memb_type_4">Household
					$<?php echo $memb_costs[ 4 ]->cost; ?></label>
			</td>
		</tr>
	</table>
	<?php
}

function cm_display_primary_member_fields() {
	?>

	<input type="hidden" name="mb_relationship" value="1"/>
	<input type="hidden" name="primary" value="primary"/>

	<table class="cm-form-table">
		<tr class="cm-profile-labels">
			<td class="cm-profile-col1 ">
				<label class="cm-form-table" id="cm-mb-first-name-lbl"
				       for="cm-mb-first-name">First Name:</label>
			</td>
			<td class="cm-profile-col2 ">
				<label class="cm-form-table" id="cm-mb-last-name-lbl"
				       for="cm-mb-last-name">Last Name:</label>
			</td>
		</tr>
		<tr class="cm-profile-inputs">
			<td class="cm-profile-col1">
				<input class="cm-form-table primary" id="cm-mb-first-name"
				       name="mb_first_name" type="text"/>
			</td>
			<td class="cm-profile-col2">
				<input class="cm-form-table primary" id="cm-mb-last-name"
				       name="mb_last_name" type="text"/>
			</td>
		</tr>
		<tr class="cm-profile-labels">
			<td class="cm-profile-col1 ">
				<label class="cm-form-table" id="cm-mb-birthday-lbl" for="cm-mb-birthday">Birthday:</label>
			</td>
			<td class="cm-profile-col2">
				<label class="cm-form-table" id="cm-mb-email-lbl"
				       for="cm-mb-email">Email</label>
			</td>
		</tr>
		<tr class="cm-profile-inputs">
			<td class="cm-profile-col1 ">
				<input class="cm_birthday datepicker" id="cm-mb-birthday"
				       name="mb_birthday" type="date" value=""/>
			</td>
			<td class="cm-profile-col2 ">
				<input class="cm-form-table" id="cm-mb-email" name="mb_email"
				       type="email"/>
			</td>
		</tr>
		<tr class="cm-profile-labels">
			<td class="cm-profile-col1">
				<label class="cm-form-table" id="cm-mb-phone-lbl"
				       for="cm-mb-phone">Phone:</label>
			</td>
			<td class="cm-profile-col2">
				<label class="cm-form-table" id="cm-mb-occupation-lbl"
				       for="cm-mb-occupation">Occupation:</label>
			</td>
		</tr>
		<tr class="cm-profile-inputs">
			<td class="cm-profile-col1">
				<input class="cm-form-table validate[required, custom[onlyLetterSp]]"
				       data-prompt-position="bottomLeft"
				       id="cm-mb-phone" name="mb_phone" type="tel" value=""/>
			</td>
			<td class="cm-profile-col2">
				<input class="validate[required, custom[onlyLetterSp]]"
				       data-prompt-position="bottomLeft"
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
	<table>
		<tr class="cm-profile-labels">
			<td class="cm-profile-col1">
				<label id="lbl_mb_addr1" for="mb_addr1">Address:</label>
			</td>
			<td class="cm-profile-col2">
				<label id="lbl_mb_addr2" for="mb_addr2">Suite/Apt:</label>
			</td>
		</tr>
		<tr class="cm-profile-inputs">
			<td class="cm-profile-col1">
				<input class="validate[required, custom[address]]"
				       data-prompt-position="bottomLeft"
				       id="mb_addr1" name="mb_addr1" type="text" value=""/>
			</td>
			<td class="cm-profile-col2">
				<input class="validate[custom[onlyLetterNumber]]"
				       data-prompt-position="bottomLeft"
				       id="mb_addr2" name="mb_addr2" type="text" value=""/>
			</td>
		</tr>
		<tr class="cm-profile-labels">
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
		<tr class="cm-profile-inputs">
			<td class="cm-profile-col1">
				<input class="validate[required, custom[onlyLetterSp]]"
				       data-prompt-position="bottomLeft"
				       id="mb_city" name="mb_city" type="text" value=""/>
			</td>
			<td class="cm-profile-col2">
				<select class="validate[required]" id="mb_state" name="mb_state">
					<?php $defSel = 'TX';
					echo showOptionsDrop( $states_array, $defSel, true ); ?>
				</select>
			</td>
			<td class="cm-profile-col3">
				<input class="validate[required, custom[zip-code]]"
				       data-prompt-position="bottomLeft"
				       id="mb_zip" name="mb_zip" type="text" value=""/>
			</td>
		</tr>
	</table>
	<?php
}


function cm_display_spouse_fields() {
	$rel_array = get_relationship_array();
	?>
	<table class="cm-form-table">
		<tr class="cm-profile-labels">
			<td class="cm-profile-col1">
				<input type="hidden" name="spouse" value="spouse"/>
				<label class="reg_first_name" id="lbl_sp_first_name" for="sp_first_name">First
					Name:</label>
			</td>
			<td class="cm-profile-col2">
				<label class="reg_last_name" id="lbl_sp_last_name" for="sp_last_name">Last
					Name:</label>
			</td>
		</tr>
		<tr class="cm-profile-inputs">
			<td class="cm-profile-col1">
				<input class="reg_first_name validate[required, custom[onlyLetterSp]]"
				       data-prompt-position="bottomLeft"
				       id="sp_first_name" name="sp_first_name" type="text" value=""/>
			</td>
			<td class="cm-profile-col2">
				<input class="reg_last_name validate[required, custom[onlyLetterSp]]"
				       data-prompt-position="bottomLeft"
				       id="sp_last_name" name="sp_last_name" type="text" value=""/>
			</td>
		</tr>
		<tr class="cm-profile-labels">
			<td class="cm-profile-col1">
				<label class="cm_birthday" id="lbl_sp_birthday" for="sp_birthday">Birthdate:</label>
			</td>
			<td class="cm-profile-col2">
				<label class="reg_email" id="lbl_sp_email" for="sp_email">Email:</label>
			</td>
		</tr>
		<tr class="cm-profile-inputs">
			<td class="cm-profile-col1">
				<input class="cm_birthday validate[required, custom[onlyNumber]]"
				       id="sp_birthday"
				       data-prompt-position="bottomLeft" name="sp_birthday" type="date"/>
			</td>
			<td class="cm-profile-col2">
				<input class="reg_email validate[custom[email]]"
				       data-prompt-position="bottomLeft"
				       id="sp_email" name="sp_email" type="email" value=""/>
			</td>
		</tr>
		<tr class="cm-profile-labels">
			<td class="cm-profile-col1">
				<label class="reg_phone" id="lbl_sp_phone" for="sp_phone">Phone:</label>
			</td>
			<td class="cm-profile-col2">
				<label class="sp_relationship" id="lbl_sp_relationship"
				       for="sp_relationship">Relationship:</label>
			</td>
		</tr>
		<tr class="cm-profile-inputs">
			<td class="cm-profile-col1">
				<input class="reg_phone validate[custom[onlyNumber]]"
				       data-prompt-position="bottomLeft"
				       id="sp_phone" name="sp_phone" type="tel" value=""/>
			</td>
			<td class="cm-profile-col2">
				<select class="sp_relationship validate[required]" id="sp_relationship"
				        name="sp_relationship">
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
	<table class="cm-form-table">
		<tr class="cm-profile-labels">
			<td class="cm-profile-col1">
				<label class="reg_first_name" for="c1_first_name">First Name:</label>
			</td>
			<td class="cm-profile-col2">
				<label class="reg_last_name" for="c1_last_name">Last Name:</label>
			</td>
		</tr>
		<tr class="cm-profile-inputs">
			<td class="cm-profile-col1">
				<input type="hidden" name="child1" value="child1"/>
				<input class="reg_first_name validate[custom[onlyLetterSp]]"
				       data-prompt-position="bottomLeft"
				       id="c1_first_name" name="c1_first_name" type="text" value=""/>
			</td>
			<td class="cm-profile-col2">
				<input class="reg_last_name validate[custom[onlyLetterSp]]"
				       data-prompt-position="bottomLeft"
				       id="c1_last_name" name="c1_last_name" type="text" value=""/>
			</td>
		</tr>
		<tr class="cm-profile-labels">
			<td class="cm-profile-col1">
				<label class="cm_birthday" id="lbl_c1_birthday" for="c1_birthday">Birthdate:</label>
			</td>
			<td class="cm-profile-col2">
				<label class="child_relationship" id="lbl_c1_relationship"
				       for="c1_relationship">Relationship:</label>
			</td>
		</tr>
		<tr class="cm-profile-inputs">
			<td class="cm-profile-col1">
				<input class="reg_birth_month validate[custom[onlyNumber]]"
				       data-prompt-position="bottomLeft"
				       id="c1_birthday" name="c1_birthday" type="date"/>
			</td>
			<td class="cm-profile-col2">
				<select class="child_relationship" id="c1_relationship"
				        name="c1_relationship">
					<?php $defSel = 4 ?>
					<?php echo showOptionsDrop( $rel_array, $defSel, true ); ?>
				</select>
			</td>
		</tr>
		<tr class="cm-profile-labels">
			<td class="cm-profile-col1">
				<label class="child_email" id="lbl_c1_email" for="c1_email">Email:</label>
			</td>
		</tr>
		<tr class="cm-profile-inputs">
			<td class="cm-profile-col1" rowspan="2">
				<input class="child_email validate[custom[email]]"
				       data-prompt-position="bottomLeft"
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
	<table class="cm-form-table">
		<tr class="cm-profile-labels">
			<td class="cm-profile-col1">
				<label class="reg_first_name" for="c2_first_name">First Name:</label>
			</td>
			<td class="cm-profile-col2">
				<label class="reg_last_name" for="c2_last_name">Last Name:</label>
			</td>
		</tr>
		<tr class="cm-profile-inputs">
			<td class="cm-profile-col1">
				<input type="hidden" name="child2" value="child2"/>
				<input class="reg_first_name validate[custom[onlyLetterSp]]"
				       data-prompt-position="bottomLeft"
				       id="c2_first_name" name="c2_first_name" type="text" value=""/>
			</td>
			<td class="cm-profile-col2">
				<input class="reg_last_name validate[custom[onlyLetterSp]]"
				       data-prompt-position="bottomLeft"
				       id="c2_last_name" name="c2_last_name" type="text" value=""/>
			</td>
		</tr>
		<tr class="cm-profile-labels">
			<td class="cm-profile-col1">
				<label class="cm_birthday" id="lbl_c2_birthday" for="c1_birthday">Birthdate:</label>
			</td>
			<td class="cm-profile-col2">
				<label class="child_relationship" id="lbl_c2_relationship"
				       for="c1_relationship">Relationship:</label>
			</td>
		</tr>
		<tr class="cm-profile-inputs">
			<td class="cm-profile-col1">
				<input class="reg_birth_month validate[custom[onlyNumber]]"
				       data-prompt-position="bottomLeft"
				       id="c1_birthday" name="c2_birthday" type="date"/>
			</td>
			<td class="cm-profile-col2">
				<select class="child_relationship" id="c2_relationship"
				        name="c2_relationship">
					<?php $defSel = 4 ?>
					<?php echo showOptionsDrop( $rel_array, $defSel, true ); ?>
				</select>
			</td>
		</tr>
		<tr class="cm-profile-labels">
			<td class="cm-profile-col1">
				<label class="child_email" id="lbl_c2_email" for="c2_email">Email:</label>
			</td>
		</tr>
		<tr class="cm-profile-inputs">
			<td class="cm-profile-col1" rowspan="2">
				<input class="child_email validate[custom[email]]"
				       data-prompt-position="bottomLeft"
				       id="c2_email" name="c2_email" type="email" value=""/>
			</td>
		</tr>
	</table>
	<!--  //END of CHILD2 -->
	<?php
}

function cm_display_child3_fields() {
	$rel_array = get_relationship_array();
	?>
	<table class="cm-form-table">
		<tr class="cm-profile-labels">
			<td class="cm-profile-col1">
				<label class="reg_first_name" for="c3_first_name">First Name:</label>
			</td>
			<td class="cm-profile-col2">
				<label class="reg_last_name" for="c3_last_name">Last Name:</label>
			</td>
		</tr>
		<tr class="cm-profile-inputs">
			<td class="cm-profile-col1">
				<input type="hidden" name="child3" value="child3"/>
				<input class="reg_first_name validate[custom[onlyLetterSp]]"
				       data-prompt-position="bottomLeft"
				       id="c3_first_name" name="c3_first_name" type="text" value=""/>
			</td>
			<td class="cm-profile-col2">
				<input class="reg_last_name validate[custom[onlyLetterSp]]"
				       data-prompt-position="bottomLeft"
				       id="c3_last_name" name="c3_last_name" type="text" value=""/>
			</td>
		</tr>
		<tr class="cm-profile-labels">
			<td class="cm-profile-col1">
				<label class="cm_birthday" id="lbl_c3_birthday" for="c3_birthday">Birthdate:</label>
			</td>
			<td class="cm-profile-col2">
				<label class="child_relationship" id="lbl_c3_relationship"
				       for="c3_relationship">Relationship:</label>
			</td>
		</tr>
		<tr class="cm-profile-inputs">
			<td class="cm-profile-col1">
				<input class="reg_birth_month validate[custom[onlyNumber]]"
				       data-prompt-position="bottomLeft"
				       id="c3_birthday" name="c3_birthday" type="date"/>
			</td>
			<td class="cm-profile-col2">
				<select class="child_relationship" id="c3_relationship"
				        name="c3_relationship">
					<?php $defSel = 4 ?>
					<?php echo showOptionsDrop( $rel_array, $defSel, true ); ?>
				</select>
			</td>
		</tr>
		<tr class="cm-profile-labels">
			<td class="cm-profile-col1">
				<label class="child_email" id="lbl_c3_email" for="c3_email">Email:</label>
			</td>
		</tr>
		<tr class="cm-profile-inputs">
			<td class="cm-profile-col1" rowspan="2">
				<input class="child_email validate[custom[email]]"
				       data-prompt-position="bottomLeft"
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
	<table class="cm-form-table" id="cm-child4-table">
		<tr class="cm-profile-labels">
			<td class="cm-profile-col1">
				<label class="reg_first_name" for="c4_first_name">First Name:</label>
			</td>
			<td class="cm-profile-col2">
				<label class="reg_last_name" for="c4_last_name">Last Name:</label>
			</td>
		</tr>
		<tr class="cm-profile-inputs">
			<td class="cm-profile-col1">
				<input type="hidden" name="child4" value="child4"/>
				<input class="reg_first_name validate[custom[onlyLetterSp]]"
				       data-prompt-position="bottomLeft"
				       id="c4_first_name" name="c4_first_name" type="text" value=""/>
			</td>
			<td class="cm-profile-col2">
				<input class="reg_last_name validate[custom[onlyLetterSp]]"
				       data-prompt-position="bottomLeft"
				       id="c4_last_name" name="c4_last_name" type="text" value=""/>
			</td>
		</tr>
		<tr class="cm-profile-labels">
			<td class="cm-profile-col1">
				<label class="cm_birthday" id="lbl_c4_birthday" for="c4_birthday">Birthdate:</label>
			</td>
			<td class="cm-profile-col2">
				<label class="child_relationship" id="lbl_c4_relationship"
				       for="c4_relationship">Relationship:</label>
			</td>
		</tr>
		<tr class="cm-profile-inputs">
			<td class="cm-profile-col1">
				<input class="reg_birth_month validate[custom[onlyNumber]]"
				       data-prompt-position="bottomLeft"
				       id="c4_birthday" name="c4_birthday" type="date"/>
			</td>
			<td class="cm-profile-col2">
				<select class="child_relationship" id="c4_relationship"
				        name="c4_relationship">
					<?php $defSel = 4 ?>
					<?php echo showOptionsDrop( $rel_array, $defSel, true ); ?>
				</select>
			</td>
		</tr>
		<tr class="cm-profile-labels">
			<td class="cm-profile-col1">
				<label class="child_email" id="lbl_c4_email" for="c4_email">Email:</label>
			</td>
		</tr>
		<tr class="cm-profile-inputs">
			<td class="cm-profile-col1" rowspan="2">
				<input class="child_email validate[custom[email]]"
				       data-prompt-position="bottomLeft"
				       id="c4_email" name="c4_email" type="email" value=""/>
			</td>
		</tr>
	</table>
	<!--  //END of CHILD4 -->
	<?php
}


function cm_does_username_exist( $username ) {
	$error = null;
	if ( username_exists( $username ) ) {
		$error = "Username already exists! Try again.";
	}

	return $error;
}

/**
 * @return mixed
 */
function cm_separate_post_data() {
	global $errors;

	//settype( $raw_post_data, 'array' );

	$children_ary = array(
		'c1_first_name',
		'c2_first_name',
		'c3_first_name',
		'c4_first_name',
	);

	$errors = new WP_Error();

	$child_proc = false;

	foreach ( $_POST as $chk_key => $chk_value ) {
		if ( stripos( $chk_key, 'mb_' ) !== false ) {
			$chk_key                     = preg_replace( '@^mb_@', '', $chk_key );
			$prime_memb_data[ $chk_key ] = $chk_value;
		} elseif ( stripos( $chk_key, 'sp_' ) !== false ) {
			$chk_key                  = preg_replace( '@^sp_@', '', $chk_key );
			$sp_memb_data[ $chk_key ] = $chk_value;
		} elseif ( preg_match( '@^c[1-4]_@i', $chk_key, $match ) ) {
			if ( in_array( $chk_key, $children_ary ) && '' !== $chk_value ) {
				switch ( $chk_key ) {
					case 'c1_first_name':
						$child_proc = true;
						break;
					case 'c2_first_name':
						$child_proc = true;
						break;
					case 'c3_first_name':
						$child_proc = true;
						break;
					case 'c4_first_name':
						$child_proc = true;
						break;
				}
				$child_memb_data[ $match[ 0 ] ][ 'proc' ] = $child_proc;
				$child_proc                               = false;
			}
			$chk_key                                    = preg_replace( '@^c[1-4]_@', '', $chk_key );
			$child_memb_data[ $match[ 0 ] ][ $chk_key ] = $chk_value;
		}
	}

	if ( is_array( $prime_memb_data ) ) {
		$memb_data[ 'prime' ] = $prime_memb_data;
	}

	if ( is_array( $sp_memb_data ) ) {
		$memb_data[ 'spouse' ] = $sp_memb_data;
	}

	if ( is_array( $child_memb_data ) ) {
		$memb_data[ 'child' ] = $child_memb_data;
	}

	return $memb_data;
}

/**
 * Clean up input data for insertion into the Wordpress User Metadata table.
 *
 * @var ARRAY $member_arr
 * @var OBJECT $memb_obj
 *
 **/
function cm_clean_member_data( $args ) {
	global $errors;
	if ( is_array( $args[ 'member_arr' ] ) ) {
		$errors      = $args[ 'errors' ];
		$member_data = $args[ 'member_arr' ];

		${$args[ 'member_key' ]} = $member_data;

		${$args[ 'member_key' ]}[ 'first_name' ]      = ( isset( $member_data[ 'first_name' ] ) ? ucwords( strtolower( sanitize_text_field( $member_data[ 'first_name' ] ) ) ) : $errors->add( 'empty', __( 'First Name can not be left blank!', 'membership' ) ) );
		${$args[ 'member_key' ]}[ 'last_name' ]       = ( isset( $member_data[ 'last_name' ] ) ? ucwords( strtolower( sanitize_text_field( $member_data[ 'last_name' ] ) ) ) : $errors->add( 'empty', __( 'Last Name can not be left blank!', 'membership' ) ) );
		${$args[ 'member_key' ]}[ 'email' ]           = ( isset( $member_data[ 'email' ] ) ? is_email( strtolower( sanitize_email( wp_unslash( $member_data[ 'email' ] ) ) ) ) : '' );
		${$args[ 'member_key' ]}[ 'phone' ]           = ( isset( $member_data[ 'phone' ] ) ? cm_validate_phone( $member_data[ 'phone' ] ) : '' );
		${$args[ 'member_key' ]}[ 'birthday' ]        = ( isset( $member_data[ 'birthday' ] ) ? cm_validate_birthday( $member_data[ 'birthday' ] ) : '' );
		${$args[ 'member_key' ]}[ 'occupation' ]      = ( isset( $member_data[ 'occupation' ] ) ? ucwords( strtolower( sanitize_text_field( $member_data[ 'occupation' ] ) ) ) : '' );
		${$args[ 'member_key' ]}[ 'relationship_id' ] = ( isset( $member_data[ 'relationship' ] ) ? intval( $member_data[ 'relationship' ] ) : 1 );
		${$args[ 'member_key' ]}[ 'membership_type' ] = ( isset( $member_data[ 'memb_type' ] ) ? intval( $member_data[ 'memb_type' ] ) : '' );
		${$args[ 'member_key' ]}[ 'addr1' ]           = ( isset( $member_data[ 'addr1' ] ) ? sanitize_text_field( $member_data[ 'addr1' ] ) : '' );
		${$args[ 'member_key' ]}[ 'addr2' ]           = ( isset( $member_data[ 'addr2' ] ) ? sanitize_text_field( $member_data[ 'addr2' ] ) : '' );
		${$args[ 'member_key' ]}[ 'city' ]            = ( isset( $member_data[ 'city' ] ) ? sanitize_text_field( $member_data[ 'city' ] ) : '' );
		${$args[ 'member_key' ]}[ 'state' ]           = ( isset( $member_data[ 'state' ] ) ? sanitize_text_field( $member_data[ 'state' ] ) : '' );
		${$args[ 'member_key' ]}[ 'zip' ]             = ( isset( $member_data[ 'zip' ] ) ? sanitize_text_field( $member_data[ 'zip' ] ) : '' );

	} elseif ( is_object( $args[ 'member_arr' ] ) ) {
		$errors                  = $args[ 'errors' ];
		$member_obj              = $args[ 'member_obj' ];
		${$args[ 'member_key' ]} = $member_obj;

		${$args[ 'member_key' ]}->first_name      = ( isset( $member_obj->first_name ) ? ucwords( strtolower( sanitize_text_field( $member_obj->first_name ) ) ) : $errors->add( 'empty', __( 'First Name can not be left blank!', 'membership' ) ) );
		${$args[ 'member_key' ]}->last_name       = ( isset( $member_obj->last_name ) ? ucwords( strtolower( sanitize_text_field( $member_obj->last_name ) ) ) : $errors->add( 'empty', __( 'Last Name can not be left blank!', 'membership' ) ) );
		${$args[ 'member_key' ]}->email           = ( isset( $member_obj->email ) ? is_email( strtolower( sanitize_email( wp_unslash( $member_obj->email ) ) ) ) : '' );
		${$args[ 'member_key' ]}->phone           = ( isset( $member_obj->phone ) ? cm_validate_phone( $member_obj->phone ) : '' );
		${$args[ 'member_key' ]}->birthday        = ( isset( $member_obj->birthday ) ? cm_validate_birthday( $member_obj->birthday ) : '' );
		${$args[ 'member_key' ]}->occupation      = ( isset( $member_obj->occupation ) ? ucwords( strtolower( sanitize_text_field( $member_obj->occupation ) ) ) : '' );
		${$args[ 'member_key' ]}->relationship_id = ( isset( $member_obj->relationship ) ? intval( $member_obj->relationship ) : 1 );
		${$args[ 'member_key' ]}->membership_type = ( isset( $member_obj->memb_type ) ? intval( $member_obj->memb_type ) : '' );
		${$args[ 'member_key' ]}->addr1           = ( isset( $member_obj->addr1 ) ? sanitize_text_field( $member_obj->addr1 ) : '' );
		${$args[ 'member_key' ]}->addr2           = ( isset( $member_obj->addr2 ) ? sanitize_text_field( $member_obj->addr2 ) : '' );
		${$args[ 'member_key' ]}->city            = ( isset( $member_obj->city ) ? sanitize_text_field( $member_obj->city ) : '' );
		${$args[ 'member_key' ]}->state           = ( isset( $member_obj->state ) ? sanitize_text_field( $member_obj->state ) : '' );
		${$args[ 'member_key' ]}->zip             = ( isset( $member_obj->zip ) ? sanitize_text_field( $member_obj->zip ) : '' );
	}

	return ${$args[ 'member_key' ]};
}

/**
 * Clean up input data for insertion into the Wordpress User Metadata table.
 *
 * @var OBJECT $member_obj
 *
 **/
function cm_clean_spouse_data( $args ) {
	global $errors;
	if ( is_array( $args[ 'member_arr' ] ) ) {
		$errors                  = $args[ 'errors' ];
		$member_data             = $args[ 'member_arr' ];
		${$args[ 'spouse_key' ]} = $member_data;

		${$args[ 'spouse_key' ]}[ 'first_name' ]      = ( isset( $member_data[ 'first_name' ] ) ? ucwords( strtolower( sanitize_text_field( $member_data[ 'first_name' ] ) ) ) : $errors->add( 'empty', __( 'First Name can not be left blank!', 'membership' ) ) );
		${$args[ 'spouse_key' ]}[ 'last_name' ]       = ( isset( $member_data[ 'last_name' ] ) ? ucwords( strtolower( sanitize_text_field( $member_data[ 'last_name' ] ) ) ) : $errors->add( 'empty', __( 'Last Name can not be left blank!', 'membership' ) ) );
		${$args[ 'spouse_key' ]}[ 'email' ]           = ( isset( $member_data[ 'email' ] ) ? is_email( strtolower( sanitize_email( wp_unslash( $member_data[ 'email' ] ) ) ) ) : '' );
		${$args[ 'spouse_key' ]}[ 'phone' ]           = ( isset( $member_data[ 'phone' ] ) ? cm_validate_phone( $member_data[ 'phone' ] ) : '' );
		${$args[ 'spouse_key' ]}[ 'birthday' ]        = ( isset( $member_data[ 'birthday' ] ) ? cm_validate_birthday( $member_data[ 'birthday' ] ) : '' );
		${$args[ 'spouse_key' ]}[ 'relationship_id' ] = ( isset( $member_data[ 'relationship' ] ) ? intval( $member_data[ 'relationship' ] ) : 1 );

	} elseif ( is_object( $args[ 'member_obj' ] ) ) {
		$errors                  = $args[ 'errors' ];
		$member_obj              = $args[ 'member_obj' ];
		${$args[ 'spouse_key' ]} = $member_obj;

		${$args[ 'spouse_key' ]}->first_name      = ( isset( $member_obj->first_name ) ? ucwords( strtolower( sanitize_text_field( $member_obj->first_name ) ) ) : $errors->add( 'empty', __( 'First Name can not be left blank!', 'membership' ) ) );
		${$args[ 'spouse_key' ]}->last_name       = ( isset( $member_obj->last_name ) ? ucwords( strtolower( sanitize_text_field( $member_obj->last_name ) ) ) : $errors->add( 'empty', __( 'Last Name can not be left blank!', 'membership' ) ) );
		${$args[ 'spouse_key' ]}->email           = ( isset( $member_obj->email ) ? is_email( strtolower( sanitize_email( wp_unslash( $member_obj->email ) ) ) ) : '' );
		${$args[ 'spouse_key' ]}->phone           = ( isset( $member_obj->phone ) ? cm_validate_phone( $member_obj->phone ) : '' );
		${$args[ 'spouse_key' ]}->birthday        = ( isset( $member_obj->birthday ) ? cm_validate_birthday( $member_obj->birthday ) : '' );
		${$args[ 'spouse_key' ]}->relationship_id = ( isset( $member_obj->relationship ) ? intval( $member_obj->relationship ) : 1 );
	}

	return ${$args[ 'spouse_key' ]};
}

/**
 * Clean up input data for insertion into the Wordpress User Metadata table.
 *
 * @var OBJECT $member_obj
 * @var ARRAY $memb_arr
 *
 *
 **/
function cm_clean_child_data( $args ) {
	global $errors;

	if ( is_array( $args[ 'member_arr' ] ) ) {
		$errors                 = $args[ 'errors' ];
		$member_data            = $args[ 'member_arr' ];
		${$args[ 'child_key' ]} = $member_data;

		${$args[ 'child_key' ]}[ 'first_name' ]      = ( isset( $member_data[ 'first_name' ] ) ? ucwords( strtolower( sanitize_text_field( $member_data[ 'first_name' ] ) ) ) : $errors->add( 'empty', __( 'First Name can not be left blank!', 'membership' ) ) );
		${$args[ 'child_key' ]}[ 'last_name' ]       = ( isset( $member_data[ 'last_name' ] ) ? ucwords( strtolower( sanitize_text_field( $member_data[ 'last_name' ] ) ) ) : $errors->add( 'empty', __( 'Last Name can not be left blank!', 'membership' ) ) );
		${$args[ 'child_key' ]}[ 'email' ]           = ( isset( $member_data[ 'email' ] ) ? is_email( strtolower( sanitize_email( wp_unslash( $member_data[ 'email' ] ) ) ) ) : '' );
		${$args[ 'child_key' ]}[ 'phone' ]           = ( isset( $member_data[ 'phone' ] ) ? cm_validate_phone( $member_data[ 'phone' ] ) : '' );
		${$args[ 'child_key' ]}[ 'birthday' ]        = ( isset( $member_data[ 'birthday' ] ) ? cm_validate_birthday( $member_data[ 'birthday' ] ) : '' );
		${$args[ 'child_key' ]}[ 'relationship_id' ] = ( isset( $member_data[ 'relationship' ] ) ? intval( $member_data[ 'relationship' ] ) : 1 );

	} elseif ( is_object( $args[ 'member_arr' ] ) ) {
		$errors                 = $args[ 'errors' ];
		$member_obj             = $args[ 'member_arr' ];
		${$args[ 'child_key' ]} = $member_obj;

		${$args[ 'child_key' ]}->first_name      = ( isset( $member_obj->first_name ) ? ucwords( strtolower( sanitize_text_field( $member_obj->first_name ) ) ) : $errors->add( 'empty', __( 'First Name can not be left blank!', 'membership' ) ) );
		${$args[ 'child_key' ]}->last_name       = ( isset( $member_obj->last_name ) ? ucwords( strtolower( sanitize_text_field( $member_obj->last_name ) ) ) : $errors->add( 'empty', __( 'Last Name can not be left blank!', 'membership' ) ) );
		${$args[ 'child_key' ]}->email           = ( isset( $member_obj->email ) ? is_email( strtolower( sanitize_email( wp_unslash( $member_obj->email ) ) ) ) : '' );
		${$args[ 'child_key' ]}->phone           = ( isset( $member_obj->phone ) ? cm_validate_phone( $member_obj->phone ) : '' );
		${$args[ 'child_key' ]}->birthday        = ( isset( $member_obj->birthday ) ? cm_validate_birthday( $member_obj->birthday ) : '' );
		${$args[ 'child_key' ]}->relationship_id = ( isset( $member_obj->relationship ) ? intval( $member_obj->relationship ) : 1 );
	}

	return ${$args[ 'child_key' ]};
}

/**
 * @param $phone
 * @param $errors
 *
 * @return int
 */
function cm_validate_phone( $phone ) {
	global $errors;
	settype( $valid_phone, "int" );
	$phone_elements = explode( '-', $phone );

	foreach ( $phone_elements as $element ) {
		if ( preg_match( '/[0-9]{3}/', $element ) || preg_match( '/[0-9]{4}/', $element ) || preg_match( '/[0-9]{10}/', $element ) ) {
			$valid_phone = intval( $element );
		} else {
			$errors->add( 'phone_error', __( '<strong>ERROR</strong>: Enter a valid phone number( 5125551234)!.', 'membership-plugin' ) );
		}
	}

	return $valid_phone;
}

/**
 * @param $bdate
 *
 * @return string
 */
function cm_validate_birthday( $bdate ) {
	global $memb_error;

	$date        = new DateTime( $bdate );
	$fixed_date  = $date->format( 'Y-m-d' );
	$date_fields = explode( '-', $fixed_date );

	$safe_date = checkdate( $date_fields[ 1 ], $date_fields[ 2 ], $date_fields[ 0 ] );
	if ( $safe_date ) {
		$fixed_safe_date = sprintf( "%s-%02s-%02s", $date_fields[ 0 ], $date_fields[ 1 ], $date_fields[ 2 ] );
	} else {
		$memb_error->add( 'date', "The date was not valid.  This needs to be checked out.  The data will be stored for further review in the failed_registration table." );
		$fixed_safe_date = $memb_error->get_error_message( 'date' );
	}

	return $fixed_safe_date;
}

/**
 * @param $mbdata
 *
 * @return null
 */
function cm_process_record( $mbdata ) {
	global $errors, $messages;
	$result          = null;
	$member_metadata = array();

	if ( isset( $mbdata[ 'email' ] ) && '' !== $mbdata[ 'email' ] ) {
		$member_id = insert_wordpress_user( $mbdata );

		if ( is_wp_error( $member_id ) ) {
			$errors->add( 'cm_create_wp_user', $member_id->get_error_message() );

			return $errors;  //return if insert results in failure.
		}

		$member_metadata[ 'user_id' ] = $member_id;
		//Add data not used in WP User insert to WP User's metadata.
		foreach ( $mbdata as $m_key => $m_value ) {
			$existing_data = get_user_meta( $member_id, $m_key, true );

			if ( $m_value != $existing_data ) {
				$member_metadata[ $m_key ] = $m_value;
				$meta_update_result        = update_user_meta( $member_id, $m_key, $m_value );

				if ( $meta_update_result === false ) {

					$result = $errors->add( 'meta_update', __( 'Something went wrong', 'membership' ) );

					return $result;  //if adding metadata fails return with error message.
				}
			}
		}
		$result = $member_metadata;

	} else {
		//Add secondary members data to the primary members metadata
		foreach ( $mbdata as $mb_key => $mb_value ) {
			if ( '' !== $mb_value ) {
				$member_metadata[ $mb_key ] = $mb_value;
			}
		}

		$result = $member_metadata;
	}

	return $result;
}


/**
 * @param $membdata
 *
 * @return int|string
 */
function insert_wordpress_user( $member ) {


	if ( is_object( $member ) ) {
		$insert_wp_user_result = insert_wp_user_obj( $member );

	} else {
		$insert_wp_user_result = insert_wp_user_arr( $member );
	}

	return $insert_wp_user_result;
}

function insert_wp_user_obj( $member ) {
	global $memb_error, $messages;
	if ( ! empty( $member->email ) ) {
		$userdata = array(
			'first_name'      => $member->first_name,
			'last_name'       => $member->last_name,
			'user_email'      => $member->email,
			'user_login'      => ( isset( $member->username ) ? $member->username : mb_strtolower( substr( $member->first_name, 0, 3 ) . substr( $member->last_name, 0, 4 ) ) ),
			'user_pass'       => ( isset( $member->pass1 ) ? $member->pass1 : wp_generate_password( $length = 12, $include_standard_special_chars = false ) ),
			'nickname'        => $member->first_name . ' ' . $member->last_name,
			'display_name'    => $member->first_name . ' ' . $member->last_name,
			'user_nicename'   => $member->first_name . '-' . $member->last_name,
			'user_registered' => $member->reg_date,
			'birthday'        => ( isset( $member->birthday ) ? $member->birthday : null ),
			'memb_type'       => ( isset( $member->memb_type ) ? $member->memb_type : null ),
			'relationship_id' => ( isset( $member->relationship_id ) ? $member->relationship_id : null ),
			'occupation'      => ( isset( $member->occupation ) ? $member->occupation : null ),
			'phone'           => ( isset( $member->phone ) ? $member->phone : null ),
			'membership_type' => ( isset( $member->membership_type ) ? $member->membership_type : null ),
		);

		$memb_id = wp_insert_user( $userdata );
	} else {
		$memb_error->add( 'no_email', 'Without an email address we cannot create a wordpress user account.' );
		$memb_id = $memb_error;
	}

	$user       = get_user_by( 'id', $memb_id );
	$messages[] = __( 'Membership for ' . $user->first_name . ' ' . $user->last_name . ' is complete!' );

	return $memb_id;
}

/**
 * @param $member
 *
 * @return int|WP_Error
 */
function insert_wp_user_arr( $member ) {
	global $memb_error, $messages;
	if ( ! empty( $member[ 'email' ] ) ) {
		$userdata = array(
			'first_name'      => $member[ 'first_name' ],
			'last_name'       => $member[ 'last_name' ],
			'user_email'      => $member[ 'email' ],
			'user_login'      => ( isset( $member[ 'username' ] ) ? $member[ 'username' ] : mb_strtolower( substr( $member[ 'first_name' ], 0, 3 ) . substr( $member[ 'last_name' ], 0, 4 ) ) ),
			'user_pass'       => ( isset( $member[ 'pass1' ] ) ? $member[ 'pass1' ] : wp_generate_password( $length = 12,
				$include_standard_special_chars = false ) ),
			'nickname'        => $member[ 'first_name' ] . ' ' . $member[ 'last_name' ],
			'display_name'    => $member[ 'first_name' ] . ' ' . $member[ 'last_name' ],
			'user_nicename'   => $member[ 'first_name' ] . '-' . $member[ 'last_name' ],
			'user_registered' => $member[ 'reg_date' ],
			'birthday'        => ( isset( $member[ 'birthday' ] ) ? $member[ 'birthday' ] : null ),
			'memb_type'       => ( isset( $member[ 'memb_type' ] ) ? $member[ 'memb_type' ] : null ),
			'relationship_id' => ( isset( $member[ 'relationship_id' ] ) ? $member[ 'relationship_id' ] : null ),
			'occupation'      => ( isset( $member[ 'occupation' ] ) ? $member[ 'occupation' ] : null ),
			'phone'           => ( isset( $member[ 'phone' ] ) ? $member[ 'phone' ] : null ),
			'membership_type' => ( isset( $member[ 'membership_type' ] ) ? $member[ 'membership_type' ] : null ),
		);

		$memb_id = wp_insert_user( $userdata );
	} else {
		$memb_error->add( 'no_email', 'Without an email address we cannot create a wordpress user account.' );
		$memb_id = $memb_error;
	}

	$user       = get_user_by( 'id', $memb_id );
	$messages[] = __( 'Membership for ' . $user->first_name . ' ' . $user->last_name . ' is complete!' );

	return $memb_id;
}

function cm_unset_unused_values( $data_obj ) {
	unset( $data_obj->pass1 );
	unset( $data_obj->pass2 );
	unset( $data_obj->_wp_http_referer );
	unset( $data_obj->_wpnonce_create{- member} );
	unset( $data_obj->action );
	unset( $data_obj->createmember );
	unset( $data_obj->page );
	unset( $data_obj->primary );
}

function cm_save_primary_member_fields( $user_id, $member_data ) {
	foreach ( $member_data as $memb_key => $memb_value ) {
		/* Copy and paste this line for additional fields. Make sure to change 'twitter' to the field ID. */
		update_user_meta( absint( $user_id ), $memb_key, $memb_value );
	}
}

function cm_save_spouse_fields( $user_id, $member_data ) {
	$spouse = $member_data;
	if ( ! empty( $spouse->email ) ) {
		$userdata = array(
			'first_name'      => $spouse->sp_first_name,
			'last_name'       => $spouse->sp_last_name,
			'user_email'      => $spouse->sp_email,
			'user_login'      => mb_strtolower( substr( $spouse->first_name, 0, 3 ) . substr( $spouse->last_name, 0, 4 ) ),
			'user_pass'       => wp_generate_password( $length = 12, $include_standard_special_chars = false ),
			'nickname'        => $spouse->first_name . ' ' . $spouse->last_name,
			'display_name'    => $spouse->first_name . ' ' . $spouse->last_name,
			'user_nicename'   => $spouse->first_name . ' ' . $spouse->last_name,
			'user_registered' => getdate(),
		);

		$sp_id = wp_insert_user( $userdata );

		update_user_meta( absint( $user_id ), 'sp_id', $sp_id );
		update_user_meta( $sp_id, 'user_id', absint( $user_id ) );

		foreach ( $spouse as $sp_key => $sp_value ) {
			update_user_meta( absint( $sp_id ), $sp_key, $sp_value );
		}
	} elseif ( ! empty( $spouse->first_name ) ) {
		foreach ( $spouse as $sp_key => $sp_value ) {
			update_user_meta( absint( $user_id ), $sp_key, $sp_value );
		}
	}
}

function cm_save_child1_fields( $user_id, $member_data ) {
	$child1 = $member_data[ 'child1' ];
	if ( $child1->email ) {
		$userdata = array(
			'first_name'      => $child1->sp_first_name,
			'last_name'       => $child1->sp_last_name,
			'user_email'      => $child1->sp_email,
			'user_login'      => mb_strtolower( substr( $child1->first_name, 0, 3 ) . substr( $child1->last_name, 0, 4 ) ),
			'user_pass'       => wp_generate_password( $length = 12, $include_standard_special_chars = false ),
			'nickname'        => $child1->first_name . ' ' . $child1->last_name,
			'display_name'    => $child1->first_name . ' ' . $child1->last_name,
			'user_nicename'   => $child1->first_name . ' ' . $child1->last_name,
			'user_registered' => getdate(),
		);

		$c1_id = wp_insert_user( $userdata );

		update_user_meta( $user_id, 'c1_id', $c1_id );
		update_user_meta( $c1_id, 'user_id', $user_id );

		foreach ( $child1 as $c1_key => $c1_value ) {
			/* Copy and paste this line for additional fields. Make sure to change 'twitter' to the field ID. */
			update_user_meta( absint( $c1_id ), $c1_key, wp_kses_post( $c1_value ) );
		}
	} elseif ( ! empty( $child1->first_name ) ) {
		foreach ( $child1 as $c1_key => $c1_value ) {
			update_user_meta( absint( $user_id ), $c1_key, $c1_value );
		}
	}
}

function cm_save_child2_fields( $user_id, $member_data ) {
	$child2 = $member_data[ 'child2' ];
	if ( $child2->email ) {
		$userdata = array(
			'first_name'      => $child2->sp_first_name,
			'last_name'       => $child2->sp_last_name,
			'user_email'      => $child2->sp_email,
			'user_login'      => mb_strtolower( substr( $child2->first_name, 0, 3 ) . substr( $child2->last_name, 0, 4 ) ),
			'user_pass'       => wp_generate_password( $length = 12, $include_standard_special_chars = false ),
			'nickname'        => $child2->first_name . ' ' . $child2->last_name,
			'display_name'    => $child2->first_name . ' ' . $child2->last_name,
			'user_nicename'   => $child2->first_name . ' ' . $child2->last_name,
			'user_registered' => getdate(),
		);

		$c2_id = wp_insert_user( $userdata );

		update_user_meta( $user_id, 'c1_id', $c2_id );
		update_user_meta( $c2_id, 'user_id', $user_id );

		foreach ( $child2 as $c2_key => $c2_value ) {
			/* Copy and paste this line for additional fields. Make sure to change 'twitter' to the field ID. */
			update_user_meta( absint( $c2_id ), $c2_key, $c2_value );
		}
	} elseif ( ! empty( $child2->first_name ) ) {
		foreach ( $child2 as $c2_key => $c2_value ) {
			update_user_meta( absint( $user_id ), $c2_key, $c2_value );
		}
	}
}

function cm_save_child3_fields( $user_id, $member_data ) {
	$child3 = $member_data[ 'child3' ];
	if ( $child3->email ) {
		$userdata = array(
			'first_name'      => $child3->sp_first_name,
			'last_name'       => $child3->sp_last_name,
			'user_email'      => $child3->sp_email,
			'user_login'      => mb_strtolower( substr( $child3->first_name, 0, 3 ) . substr( $child3->last_name, 0, 4 ) ),
			'user_pass'       => wp_generate_password( $length = 12, $include_standard_special_chars = false ),
			'nickname'        => $child3->first_name . ' ' . $child3->last_name,
			'display_name'    => $child3->first_name . ' ' . $child3->last_name,
			'user_nicename'   => $child3->first_name . ' ' . $child3->last_name,
			'user_registered' => getdate(),
		);

		$c3_id = wp_insert_user( $userdata );

		update_user_meta( $user_id, 'c1_id', $c3_id );
		update_user_meta( $c3_id, 'user_id', $user_id );

		foreach ( $child3 as $c3_key => $c3_value ) {
			/* Copy and paste this line for additional fields. Make sure to change 'twitter' to the field ID. */
			update_user_meta( absint( $c3_id ), $c3_key, $c3_value );
		}
	} elseif ( ! empty( $child3->first_name ) ) {
		foreach ( $child3 as $c3_key => $c3_value ) {
			update_user_meta( absint( $user_id ), $c3_key, $c3_value );
		}
	}
}

function cm_save_child4_fields( $user_id, $member_data ) {
	$child4 = $member_data[ 'child4' ];
	if ( $child4->email ) {
		$userdata = array(
			'first_name'      => $child4->sp_first_name,
			'last_name'       => $child4->sp_last_name,
			'user_email'      => $child4->sp_email,
			'user_login'      => mb_strtolower( substr( $child4->first_name, 0, 3 ) . substr( $child4->last_name, 0, 4 ) ),
			'user_pass'       => wp_generate_password( $length = 12, $include_standard_special_chars = false ),
			'nickname'        => $child4->first_name . ' ' . $child4->last_name,
			'display_name'    => $child4->first_name . ' ' . $child4->last_name,
			'user_nicename'   => $child4->first_name . ' ' . $child4->last_name,
			'user_registered' => getdate(),
		);

		$c4_id = wp_insert_user( $userdata );

		update_user_meta( $user_id, 'c1_id', $c4_id );
		update_user_meta( $c4_id, 'user_id', $user_id );

		foreach ( $child4 as $c4_key => $c4_value ) {
			/* Copy and paste this line for additional fields. Make sure to change 'twitter' to the field ID. */
			update_user_meta( absint( $c4_id ), $c4_key, $c4_value );
		}
	} elseif ( ! empty( $child4->first_name ) ) {
		foreach ( $child4 as $c4_key => $c4_value ) {
			update_user_meta( absint( $user_id ), $c4_key, $c4_value );
		}
	}
}


/**
 * Example for adding new user registration fields.
 */
// 1. Add a new form element...
//add_action( 'register_form', 'cm_register_form_example' );
function cm_register_form_example() {

	$first_name = ( ! empty( $_POST[ 'first_name' ] ) ) ? trim( $_POST[ 'first_name' ] ) : '';

	?>

	<?php _e( 'First Name', 'membership-plugin' ) ?>
	<?php echo esc_attr( wp_unslash( $first_name ) ); ?>


	<?php
}

//2. Add validation. In this case, we make sure first_name is required.
//add_filter( 'registration_errors', 'cm_registration_errors_example', 10, 3 );
function myplugin_registration_errors_example( $errors, $sanitized_user_login, $user_email ) {

	if ( empty( $_POST[ 'first_name' ] ) || ! empty( $_POST[ 'first_name' ] ) && trim( $_POST[ 'first_name' ] ) == '' ) {
		$errors->add( 'first_name_error', __( '<strong>ERROR</strong>: You must include a first name.', 'membership-plugin' ) );
	}

	return $errors;
}

//3. Finally, save our extra registration user meta.
//add_action( 'user_register', 'cm_register_member_example' );
function cm_register_member_example( $user_id ) {
	if ( ! empty( $_POST[ 'first_name' ] ) ) {
		update_user_meta( $user_id, 'first_name', trim( $_POST[ 'first_name' ] ) );
	}
}


/**
 *
 * EXAMPLE: Validating a Custom Field
 *
 *
 * @param $errors
 * @param $sanitized_user_login
 * @param $user_email
 *
 * @return mixed
 */

function myplugin_check_fields( $errors, $sanitized_user_login, $user_email ) {

	if ( ! preg_match( '/[0-9]{5}/', $_POST[ 'zipcode' ] ) ) {
		$errors->add( 'zipcode_error', __( '<strong>ERROR</strong>: Invalid ZIP code.', 'my_textdomain' ) );
	}

	return $errors;
}

add_filter( 'registration_errors', 'myplugin_check_fields', 10, 3 );


function cm_get_clean_post_data() {
	foreach ( $_POST as $p_key => $p_value ) {
		if ( ! empty( $p_value ) ) {
			switch ( $p_key ) {
				case 'mb_email':
				case 'sp_email':
				case 'c1_email':
				case 'c2_email':
				case 'c3_email':
				case 'c4_email':
					$clean_post_data[ sanitize_key( $p_key ) ] = sanitize_email( $p_value );
					break;
				case 'memb_type':
				case 'mb_id':
				case 'mb_relationship_id':
				case 'sp_id':
				case 'sp_relationship':
				case 'c1_id':
				case 'c1_relationship':
				case 'c2_id':
				case 'c2_relationship':
				case 'c3_id':
				case 'c3_relationship':
				case 'c4_id':
				case 'c4_relationship':
					$clean_post_data[ sanitize_key( $p_key ) ] = intval( $p_value );
					break;
				case 'mb_phone':
				case 'sp_phone':
					unset( $numb_grps );
					unset( $clean_numb_grps );
					unset( $clean_phone );
					unset( $numbers );

					$numb_grps = explode( '-', $p_value );

					foreach ( $numb_grps as $numbers ) {
						$clean_numb_grps[] = intval( $numbers );
					}
					if ( count( $numb_grps ) > 1 ) {
						$clean_phone = $clean_numb_grps[ 0 ] . '-' . $clean_numb_grps[ 1 ] . '-' . $clean_numb_grps[ 2 ];
					} else {
						$clean_phone = substr( $clean_numb_grps[ 0 ], 0, 3 ) . '-' . substr( $clean_numb_grps[ 0 ], 3, 3 ) . '-' . substr( $clean_numb_grps[ 0 ], 6, 4 );
					}

					$clean_post_data[ $p_key ] = $clean_phone;
					break;
				default:
					$clean_post_data[ sanitize_key( $p_key ) ] = sanitize_text_field( $p_value );
					break;
			}
		}
	}
	unset( $numb_grps );
	unset( $clean_numb_grps );
	unset( $clean_phone );
	unset( $numbers );

	return $clean_post_data;
}

function get_date_ready_for_display() {

}


add_action( 'contextual_help', 'cm_screen_help', 10, 3 );
function cm_screen_help( $contextual_help, $screen_id, $screen ) {

	// The add_help_tab function for screen was introduced in WordPress 3.3.
	if ( ! method_exists( $screen, 'add_help_tab' ) ) {
		return $contextual_help;
	}

	global $hook_suffix;

	// List screen properties
	$variables = '<ul style="width:50%;float:left;"> <strong>Screen variables </strong>'
	             . sprintf( '<li> Screen id : %s</li>', $screen_id )
	             . sprintf( '<li> Screen base : %s</li>', $screen->base )
	             . sprintf( '<li>Parent base : %s</li>', $screen->parent_base )
	             . sprintf( '<li> Parent file : %s</li>', $screen->parent_file )
	             . sprintf( '<li> Hook suffix : %s</li>', $hook_suffix )
	             . '</ul>';

	// Append global $hook_suffix to the hook stems
	$hooks = array(
		"load-$hook_suffix",
		"admin_print_styles-$hook_suffix",
		"admin_print_scripts-$hook_suffix",
		"admin_head-$hook_suffix",
		"admin_footer-$hook_suffix",
	);

	// If add_meta_boxes or add_meta_boxes_{screen_id} is used, list these too
	if ( did_action( 'add_meta_boxes_' . $screen_id ) ) {
		$hooks[] = 'add_meta_boxes_' . $screen_id;
	}

	if ( did_action( 'add_meta_boxes' ) ) {
		$hooks[] = 'add_meta_boxes';
	}

	// Get List HTML for the hooks
	$hooks = '<ul style="width:50%;float:left;"> <strong>Hooks </strong> <li>' . implode( '</li><li>', $hooks ) . '</li></ul>';

	// Combine $variables list with $hooks list.
	$help_content = $variables . $hooks;

	// Add help panel
	$screen->add_help_tab( array(
		'id'      => $screen_id,
		'title'   => 'Screen Information',
		'content' => $help_content,
	) );

	return $contextual_help;
}

function list_hooked_functions( $tag = false ) {
	global $wp_filter, $debug;
	if ( $debug ) {
		if ( $tag ) {
			$hook[ $tag ] = $wp_filter[ $tag ];
			if ( ! is_array( $hook[ $tag ] ) ) {
				trigger_error( "Nothing found for '$tag' hook", E_USER_WARNING );

				return;
			}
		} else {
			$hook = $wp_filter;
			ksort( $hook );
		}
		echo '<pre>';
		foreach ( $hook as $tag => $priority ) {
			echo "<br />&gt;&gt;&gt;&gt;&gt;t<strong>$tag</strong><br />";
			ksort( $priority );
			foreach ( $priority as $priority => $function ) {
				echo $priority;
				foreach ( $function as $name => $properties ) {
					echo "t$name<br />";
				}
			}
		}
		echo '</pre>';

		return;
	}


}

function archive_member( $id ) {
	$archive_result = update_user_meta( $id, 'status_id', 2 );

	return $archive_result;
}

function generateCallTrace() {
	$e     = new Exception();
	$trace = explode( "\n", $e->getTraceAsString() );
	// reverse array to make steps line up chronologically
	$trace = array_reverse( $trace );
	array_shift( $trace ); // remove {main}
	array_pop( $trace ); // remove call to this method
	$length = count( $trace );
	$result = array();

	for ( $i = 0; $i < $length; $i ++ ) {
		$result[] = ( $i + 1 ) . ')' . substr( $trace[ $i ], strpos( $trace[ $i ], ' ' ) ); // replace '#someNum' with '$i)', set the right ordering
	}

	return "\t" . implode( "\n\t", $result );
}