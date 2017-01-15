<?php
/* preset FGC setting*/
if ( file_exists( TEMPLATEPATH . "/includes/options-init.php" ) ) {
	require_once TEMPLATEPATH . "/includes/options-init.php";
}

if ( ! session_id() ) {
	session_start();
}

/* Designed by TemplateLite.com */
$tpinfo[ 'themename' ] = 'Beach Holiday';
$tpinfo[ 'prefix' ]    = 'templatelite';        //for options. e.g. all templatelite themes should use "templatelite" for general options (feed url, twitter id, analytics)
$tpinfo[ 'tb_prefix' ] = 'templatelite_beachholiday';//for options. theme base prefix


if ( function_exists( 'register_sidebar' ) ) {
	register_sidebar( array(
		'before_widget' => '<li><div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div></li>',
		'before_title'  => '<h4>',
		'after_title'   => '</h4>',
	) );
}

include( TEMPLATEPATH . '/includes/theme-options.php' );
include( TEMPLATEPATH . '/includes/theme-setup.php' );
include( TEMPLATEPATH . '/includes/theme-functions.php' );
include( TEMPLATEPATH . '/includes/ctxphc-functions.php' );
include( TEMPLATEPATH . '/template.php' );

$max_children = 4;

/**
 *  Load custom scripts:
 */
function reg_custom_scripts_and_styles() {

	wp_register_script( 'mp-validation-script', get_template_directory_uri() . '/includes/js/mp-validation-script.js', array( 'jquery' ), '', true );
	wp_enqueue_script( 'mp-validation-script' );

	//Register JQuery Input Validation Rules in English
	wp_register_script( 'validation-local', get_template_directory_uri() . '/includes/js/languages/jquery.validationEngine-en.js', '', true );
	wp_enqueue_script( 'validation-local' );

	//Register jQuery Input Validation Engine
	wp_register_script( 'validation-engine', get_template_directory_uri() . '/includes/js/jquery.validationEngine.js', '', true );
	wp_enqueue_script( 'validation-engine' );

	//Register jQuery Input Validation CSS Stylesheet
	wp_register_style( 'validation-style', get_template_directory_uri() . '/includes/css/validationEngine.jquery.css' );

	wp_register_script( 'ctxphc-scripts', get_template_directory_uri() . '/includes/js/ctxphc-scripts.js', array( 'jquery' ), '', true );
	if ( is_page( 'membership' ) ) {
		wp_enqueue_script( 'ctxphc-scripts' );
		wp_enqueue_style( 'validation-style' );
	}

	wp_register_script( 'ctxphc-pb-script', get_template_directory_uri() . '/includes/js/ctxphc-pb-script.js', array( 'jquery' ), '', true );

	if ( is_page( 'pirates-ball-members-only-early-registration' ) || is_page( 'pirates-ball-early-registration' ) || is_page( 'pirates-ball-registration' ) || is_page( 'pirates-ball-private-registration' ) ) {
		wp_enqueue_script( 'ctxphc-pb-script' );
		wp_enqueue_style( 'validation-style' );
	}

	//Register CTXPHC Custom CSS Stylesheet
	wp_register_style( 'ctxphc-custom-style', get_template_directory_uri() . '/includes/css/ctxphc-style.css' );
	wp_enqueue_style( 'ctxphc-custom-style' );

	//Register CTXPHC CSS Print Stylesheet
	wp_register_style( 'ctxphc-print-style', get_template_directory_uri() . '/includes/css/ctxphc-print-style.css', '', '', "print" );
	wp_enqueue_style( 'ctxphc-print-style' );

	//Register CTXPHC Pirates Ball Registration Custom CSS Stylesheet
	wp_register_style( 'pb_reg_styles', get_stylesheet_directory_uri() . '/includes/css/pb_reg_styles.css', array(), '1.0' );
	if ( is_page( 'pirates-ball-members-only-early-registration' ) || is_page( 'pirates-ball-early-registration' ) || is_page( 'pirates-ball-registration' ) || is_page( 'pirates-ball-private-registration' ) ) {
		wp_enqueue_style( 'pb_reg_styles' );
	}
}

add_action( 'wp_enqueue_scripts', 'reg_custom_scripts_and_styles' );

/**
 * Redirect non-admins to the current page and Administrators to the dashboard
 * after logging into the site.
 *
 * @since    1.1.1
 */
/*
function memb_login_redirect( $redirect_to, $request, $user ) {
	return ( $user->has_cap( 'delete_users' ) ) ? admin_url() : $request;
}

add_filter( 'login_redirect', 'memb_login_redirect', 10, 3 );
*/

//TODO:  Look into registration_redirect

//TODO:  Look into lostpassword_redirect


function redirect_after_logout() {
	wp_logout_url( home_url() );
}

add_filter( 'allowed_redirect_hosts', 'redirect_after_logout' );

//Displays options for drop down boxes in forms.
/**
 * @param $array
 * @param $selected
 * @param $echo
 *
 * @return string
 */
function showOptionsDrop( $array, $selected, $echo ) {
	$string = '';
	foreach ( $array as $key => $value ) {
		if ( $key === $selected ) {
			$string .= "<option value='$key' selected>$value</option>\n";
		} else {
			$string .= "<option value='$key'>$value</option>\n";
		}
	}

	if ( $echo ) {
		echo $string;
	} else {
		return $string;
	}
}


/**
 * @param $name
 *
 * @return string
 */
function memb_lower_case_user_name( $name ) {
	// might be turned off
	if ( function_exists( 'sp_strtolower' ) ) {
		return mb_strtolower( $name );
	}

	return strtolower( $name );
}

add_filter( 'sanitize_user', 'memb_lower_case_user_name' );


//TODO:  Create IPN Web Accept handler Class

//Main IPN Web Accept Txn Type processing calls Membership Paypal processing class
/**
 * @param $data
 */
function cm_ipn_web_accept_payment_processing( $data ) {
	$payment_processing = new membershipPayPalPaymentProcessing( $data );
}

add_action( 'paypal-paypal_ipn_for_wordpress_txn_type_web_accept', 'cm_ipn_web_accept_payment_processing' );

function my_ipn_web_failed_payment_processing( $data ) {
	$payment_processing = new membershipPayPalPaymentProcessing( $data );
}

add_action( 'paypal-web_accept_failed', 'my_ipn_web_failed_payment_processing' );

/**
 *
 */
function get_renewal_date( $renewing = false ) {
	$current_date          = make_date_safe( date( "Y-m-d" ) );
	$curr_year             = intval( date( 'Y' ) );
	$renewal_year          = $curr_year + 1;
	$extended_renewal_year = $renewal_year + 1;
	$extend_renewal_date   = $curr_year . '-09-01';

	//todo: update this to use unix timestamps
	if ( $current_date > $extend_renewal_date ) {
		$renewal_date = $extended_renewal_year . '-01-01';
	} else {
		$renewal_date = $renewal_year . '-01-01';
	}

	return $renewal_date;
}

/**
 * @param $reg_id
 *
 * @return mixed
 */
function activate_new_member( $reg_id ) {
	global $wpdb;
	$wpdb->print_error();

}

/**
 * @param $recid
 *
 * @return array
 */
function activate_renewing_member( $recid ) {
	Global $wpdb;
	//process renewing membership record.
	//return $renewing_memb_ids array

}


/**
 * @param $rel_id
 *
 * @return wpdb
 */
function get_membership_pricing() {
	/** @var wpdb $wpdb */
	global $wpdb;
	$cost       = $wpdb->get_results( "SELECT cost FROM ctxphc_membership_pricing" );
	$type_count = count( $cost );

	for ( $x = 1, $y = 0; $x <= $type_count; $x ++, $y ++ ) {
		$pricing[ $x ] = $cost[ $y ];
	}

	return $pricing;
}

function format_save_phone( $phone_number ) {
	return preg_replace( '/[^0-9]/', '', $phone_number );
}

function formatPhoneNumber( $phoneNumber ) {
	$phoneNumber = preg_replace( '/[^0-9]/', '', $phoneNumber );

	if ( strlen( $phoneNumber ) > 10 ) {
		$countryCode = substr( $phoneNumber, 0, strlen( $phoneNumber ) - 10 );
		$areaCode    = substr( $phoneNumber, - 10, 3 );
		$nextThree   = substr( $phoneNumber, - 7, 3 );
		$lastFour    = substr( $phoneNumber, - 4, 4 );

		$phoneNumber = '+' . $countryCode . ' (' . $areaCode . ') ' . $nextThree . '-' . $lastFour;
	} else if ( strlen( $phoneNumber ) == 10 ) {
		$areaCode  = substr( $phoneNumber, 0, 3 );
		$nextThree = substr( $phoneNumber, 3, 3 );
		$lastFour  = substr( $phoneNumber, 6, 4 );

		$phoneNumber = '(' . $areaCode . ') ' . $nextThree . '-' . $lastFour;
	} else if ( strlen( $phoneNumber ) == 7 ) {
		$nextThree = substr( $phoneNumber, 0, 3 );
		$lastFour  = substr( $phoneNumber, 3, 4 );

		$phoneNumber = $nextThree . '-' . $lastFour;
	}

	return $phoneNumber;
}

function make_date_safe( $date ) {
	global $memb_error;

	$date       = new DateTime( $date );
	$fixed_date = $date->format( 'Y-m-d' );
	list( $year, $month, $day ) = explode( '-', $fixed_date );

	$safe_date = checkdate( $month, $day, $year );
	if ( $safe_date ) {
		$fixed_safe_date = sprintf( "%s-%02s-%02s", $year, $month, $day );
	} else {
		$code      = 'make_date_safe_' . $date;
		$error_msg = "The date was not valid.  This needs to be checked out.  The data will be stored for further review in the failed_registration table.";
		$memb_error->add( $code, $error_msg );
		$fixed_safe_date = $memb_error->get_error_message( $code );
		unset( $code, $error_msg );
	}

	return $fixed_safe_date;
}

function load_reg_types() {
	global $reg_types;
	$reg_types = array(
		"new"   => "new",
		"renew" => "renew",
	);
}

function reg_type_new() {
	global $reg_types;

	if ( ! isset( $reg_types ) ) {
		load_reg_types();
	}

	return $reg_types[ 'new' ];

}

function reg_type_renew() {
	global $reg_types;

	if ( ! isset( $reg_types ) ) {
		load_reg_types();
	}

	return $reg_types[ 'renew' ];
}

function get_associated_member_types() {
	global $max_children;

	$id_names = array( "mb", "sp" );

	for ( $i = 1; $i <= $max_children; ++ $i ) {
		$id_names[] = "c$i";
	}

	return ( $id_names );
}

function get_associated_member_id_keys( $mtype ) {
	switch ( $mtype ) {
		case 'mb':
			$id_keys = array(
				'wp_user_id',
				'sp_id',
				'c1_id',
				'c2_id',
				'c3_id',
				'c4_id',
				'fam_memb_id_1',
				'fam_memb_id_2',
				'fam_memb_id_3',
				'fam_memb_id_4',
			);
			break;
		case 'sp':
			$id_keys = array( 'wp_user_id', 'mb_id', 'membUserID', 'prim_memb_id', );
			break;
		default:
			$id_keys = array( 'wp_user_id', 'mb_id', 'membUserID', 'prim_memb_id', );
			break;
	}

	return $id_keys;
}

function is_prime_member( $curusr_id, $assoc_usr_id, $assoc_memb_type ) {


	if ( $assoc_usr_id <> $curusr_id && $assoc_memb_type <> 'mb_id' ) {
		$is_primary = $curusr_id;
	} else {
		$is_primary = false;
	}

	return $is_primary;
}

function get_primary_member_id( $cur_metadata ) {

	foreach ( get_associated_member_keys() as $key ) {
		if ( $key == 'prim_memb_id' || ( $cur_metadata->__isset( $key ) && ! empty( $cur_metadata->__get( $key ) ) ) ) {

			$prime_memb_id = $cur_metadata->__get( $key );
			break;
		}
	}

	return $prime_memb_id;
}

function get_associated_member_keys() {
	$keys = array(
		"mb_id",
		"sp_id",
		"membUserID",
		"prim_memb_id",
		"fam_memb_id_1",
		"fam_memb_id_2",
		"fam_memb_id_3",
		"fam_memb_id_4",
		"c1_id",
		"c2_id",
		"c3_id",
		"c4_id",
	);

	return $keys;
}

function get_member_data( $usrid ) {
	$usrdata = get_userdata( $usrid );

	return $usrdata;
}

function get_member_metadata( $usrid ) {
	$usrmeta = get_user_meta( $usrid );

	return $usrmeta;
}

function get_associated_user_ids( $user_id ) {
	global $wpdb;
	$query = $wpdb->prepare( " SELECT user_id, meta_key, meta_value FROM ctxphc_usermeta WHERE meta_value = %d AND user_id <> %d", $user_id, $user_id );

	$results = $wpdb->get_results( $query );

	return $results;
}

/**
 * @param $meta_value
 * @param $meta_key
 *
 * @return mixed
 */
function prepare_member_data( $meta_value, $meta_key ) {

	switch ( $meta_key ) {
		case 'birthday':
			$prepared_metadata = make_date_safe( $meta_value );
			break;
		case 'fam_1_birthday':
			$prepared_metadata = make_date_safe( $meta_value );
			break;
		case 'fam_2_birthday':
			$prepared_metadata = make_date_safe( $meta_value );
			break;
		case 'phone':
			$prepared_metadata = formatPhoneNumber( $meta_value );
			break;
		case 'user_phone':
			$prepared_metadata = formatPhoneNumber( $meta_value );
			break;
		case 'state':
			$prepared_metadata = map_state_values_to_form_values( $meta_value );
			break;
		case 'user_state':
			$prepared_metadata = map_state_values_to_form_values( $meta_value );
			break;
		case 'relationship_id':
			$prepared_metadata = map_rel_values_to_form_values( $meta_value );
			break;
		case 'mb_relationship_id':
			$prepared_metadata = map_rel_values_to_form_values( $meta_value );
			break;
		case 'relationship':
			$prepared_metadata = map_rel_values_to_form_values( $meta_value );
			break;
		case 'membership':
			$prepared_metadata = map_memb_values_to_form_values( $meta_value );
			break;
		case 'mb_membership_type':
			$prepared_metadata = map_memb_values_to_form_values( $meta_value );
			break;
		case 'membership_type':
			$prepared_metadata = map_memb_values_to_form_values( $meta_value );
			break;
		case 'membership_id':
			$prepared_metadata = map_memb_values_to_form_values( $meta_value );
			break;
		case 'user_memb_type':
			$prepared_metadata = map_memb_values_to_form_values( $meta_value );
			break;
		case 'memb_type':
			$prepared_metadata = map_memb_values_to_form_values( $meta_value );
			break;
		default:
			$prepared_metadata = $meta_value;
	}

	return $prepared_metadata;
}


/**
 * @param $reg_type
 *
 * @return mixed
 */
function process_registration( $reg_type ) {
	global $prime_members_id, $memb_error;

	$current_date = date( "Y-m-d" );
	$renewal_date = get_renewal_date();

	$clean_form_data      = get_clean_form_data( $reg_type );
	$clean_users_data     = $clean_form_data[ 'userdata' ];
	$clean_users_metadata = $clean_form_data[ 'metadata' ];

	foreach ( $clean_users_data as $memb_type_key => $memb_data ) {

		if ( empty( $memb_data[ 'email' ] ) ) {
			$code      = 'email_missing';
			$error_msg = "Email must be filled in to create a wordpress user account..";
			$memb_error->add( $code, $error_msg );

			error_log_message( $memb_error->get_error_message( $code, $error_msg ) );
			unset( $code, $error_msg );

			// Added each associated member's data to the primary member's
			// metadata if the associated member does not have an email address.

			// associated member's user data added to primary member's metadata
			foreach ( $memb_data as $mb_meta_key => $mb_meta_value ) {

				$associated_memb_data_key = $memb_type_key . '_' . $mb_meta_key;

				$clean_users_metadata[ 'mb' ][ $associated_memb_data_key ] = $mb_meta_value;

			}

			// associated member's user metadata added to primary member's metadata
			foreach ( $clean_users_metadata[ $memb_type_key ] as $mb_meta_key => $mb_meta_value ) {
				$associated_memb_data_key = $memb_type_key . '_' . $mb_meta_key;

				$clean_users_metadata[ 'mb' ][ $associated_memb_data_key ] = $mb_meta_value;
			}

		} else if ( email_exists( $memb_data[ 'email' ] ) ) {
			$code      = 'email_exists';
			$error_msg = "Email address is already registered.";
			$memb_error->add( $code, $error_msg );

			error_log_message( $memb_error->get_error_message( $code, $error_msg ) );
			unset( $code, $error_msg );
			//TODO: Display message to user that email is already registered.

			// Added each associated member's data to the primary member's
			// metadata if the associated member's email address is already registered.

			// associated member's user data added to primary member's metadata
			foreach ( $memb_data as $mb_meta_key => $mb_meta_value ) {
				$associated_memb_data_key = $memb_type_key . '_' . $mb_meta_key;

				$clean_users_metadata[ 'mb' ][ $associated_memb_data_key ] = $mb_meta_value;
			}

			// associated member's user metadata added to primary member's metadata
			foreach ( $clean_users_metadata[ $memb_type_key ] as $mb_meta_key => $mb_meta_value ) {
				$associated_memb_data_key = $memb_type_key . '_' . $mb_meta_key;

				$clean_users_metadata[ 'mb' ][ $associated_memb_data_key ] = $mb_meta_value;
			}

		} else {
			// Create wordpress user.
			// Will happen for every primary user
			// but is optional for all others
			$memb_id = create_wordpress_user( $memb_data );

			if ( is_wp_error( $memb_id ) ) {

				// error message in set in create wordpress user function and returned
				// as memb_id value is there was an error.
				error_log_message( $memb_id->get_error_message() );

			} else {

				// Otherwise memb_id will contain the new members wordpress user id.
				// We will add this user id to the metadata for the user as
				// the users member type (mb_id, sp_id, etc)

				$member_ids[] = $memb_id;  // may be able to delete this.

				$clean_users_data[ $memb_type_key ][ 'ID' ] = $memb_id;  // not sure this is needed.

				// Added each wordpress member's id to the primary members metadata
				// sp_id, c1_id, c2_id, etc
				$clean_users_metadata[ 'mb' ][ $memb_type_key . '_id' ] = $memb_id;

				// Added associated member's id to their own metadata.
				// Needed when doing membership renewal.
				// sp_id, c1_id, c2_id, etc.
				$clean_users_metadata[ $memb_type_key ][ $memb_type_key . '_id' ] = $memb_id;

				if ( 'mb' == $memb_type_key ) {

					// Set primary members user id
					if ( is_null( $prime_members_id ) ) {

						$prime_members_id = $memb_id;
					}

				} else {
					// Add primary Member's Wordpress user id to each family member with a Wordpress user id.
					$clean_users_metadata[ $memb_type_key ][ 'mb_id' ] = $prime_members_id;
				}
			}

			$clean_users_metadata[ $memb_type_key ][ 'email' ] = $clean_users_data[ $memb_type_key ][ 'email' ];

			$clean_users_metadata[ $memb_type_key ][ 'hatch_date' ]   = make_date_safe( $current_date );
			$clean_users_metadata[ $memb_type_key ][ 'renewal_date' ] = make_date_safe( $renewal_date );
			$clean_users_metadata[ $memb_type_key ][ 'status_id' ]    = 0; // new member registration defaults to pending

		}

		$clean_form_data[ 'userdata' ] = $clean_users_data;
		$clean_form_data[ 'metadata' ] = $clean_users_metadata;
	}

	return $clean_form_data;
}


/**
 * @param $current_memb_id
 *
 * @return int|WP_Error
 */
function process_update_metadata( $reg_action ) {

	global $memb_error;

	$clean_form_data      = get_clean_form_data( $reg_action );
	$clean_users_data     = $clean_form_data[ 'userdata' ];
	$clean_users_metadata = $clean_form_data[ 'metadata' ];

	//check existing metadata against the form data to determine if data needs to be updated
	foreach ( $clean_users_metadata as $member_key => $member_meta_array ) {

		if ( isset( $_POST[ $member_key . '_id' ] ) && ! empty( $_POST[ $member_key . '_id' ] ) ) {

			$cur_memb_id = $_POST[ $member_key . '_id' ];

		}

		foreach ( $member_meta_array as $member_meta_key => $member_meta_value ) {

			if ( is_array( $member_meta_value ) ) {

				foreach ( $member_meta_value as $metadata_value ) {

					$compare_result = compare_form_data_to_usermeta( $cur_memb_id, $member_meta_key, $metadata_value );
				}
			} else {

				$compare_result = compare_form_data_to_usermeta( $cur_memb_id, $member_meta_key, $member_meta_value );
			}

			if ( $compare_result == false ) {

				$prev_value = get_user_meta( $cur_memb_id, $member_meta_key, true );

				$result = update_members_metadata( $cur_memb_id, $member_meta_key, $member_meta_value, $prev_value );

				if ( is_wp_error( $result ) ) {
					//Do something with this
				}
			}
		}
	}

	//check existing user_data against the form data to determine if data needs to be updated
	foreach ( $clean_users_data as $member_key => $member_user_array ) {

		$cur_memb_id = $_POST[ $member_key . '_id' ];

		//Get existing Wordpress user data
		$user_info = get_userdata( $cur_memb_id );

		if ( ! $user_info ) {

			$code      = 'get_userdata_' . $cur_memb_id;
			$error_msg = "get_userdata failed for ID : $cur_memb_id";
			$memb_error->add( $code, $error_msg );
			error_log_message( $memb_error->get_error_message( $code ) );
			unset( $code, $error_msg );

		} else {

			foreach ( $member_user_array as $user_data_key => $user_data_value ) {

				compare_form_userdata( $user_data_key, $user_data_value, $user_info, $cur_memb_id );

			}

			return $clean_form_data;
		}
	}
}

function compare_form_userdata( $user_data_key, $user_data_value, $user_info, $current_memb_id ) {
	global $memb_error;

	$keyval     = null;
	$key_fields = explode( '_', $user_data_key );

	if ( is_array( $key_fields ) ) {
		$i = 0;

		while ( $i < count( $key_fields ) ) {
			$keyval .= $key_fields[ $i ++ ];
		}
	}

	if ( $keyval == 'ID' || $keyval == 'display_name' ) {

		$user_key = $keyval;

	} else {

		$user_key = 'user_' . $keyval;
	}

	if ( $user_data_value != $user_info->$user_key ) {

		$updated_user_id = wp_update_user( array(
			'ID'      => $current_memb_id,
			$user_key => $user_data_value,
		) );

		if ( is_wp_error( $updated_user_id ) ) {

			$code      = 'wp_update_user_error_' . $current_memb_id;
			$error_msg = "wp_user_update failed for : $current_memb_id, $user_key, $user_data_value";
			$memb_error->add( $code, $error_msg );
			error_log_message( $memb_error->get_error_message( $code ) );
			unset( $code, $error_msg );

		} else {

			$code      = 'wp_update_user_success_' . $current_memb_id;
			$error_msg = "wp_user_update completed for : $current_memb_id, $user_key, $user_data_value";
			$memb_error->add( $code, $error_msg );
			error_log_message( $memb_error->get_error_message( $code ) );
			unset( $code, $error_msg );

		}
	}
}

function make_pretty( $ugly_data ) {

	foreach ( $ugly_data as $ugly_key => $ugly_value ) {

		foreach ( $ugly_value as $ukey => $uvalue ) {

			switch ( $ukey ) {
				case 'phone':
					$pretty_value = formatPhoneNumber( $uvalue );
					break;
				default:
					$pretty_value = $uvalue;
					break;
			}

			$pretty_data[ $ugly_key ][ $ukey ] = $pretty_value;
		}
	}

	return $pretty_data;
}

/**
 * @param $type
 *
 * @return mixed
 */
function get_clean_form_data( $type ) {

	global $max_children;


	if ( $type == 'renew' ) {

		$cleaned_input_form_data = clean_renewal_form_input();

	} else if ( $type == 'new' ) {

		$cleaned_input_form_data = clean_new_reg_form_input();

	}

	return $cleaned_input_form_data;
}

function clean_new_reg_form_input() {
	global $max_children;

	$reg_action = ucwords( strtolower( sanitize_text_field( $_POST[ 'reg_action' ] ) ) );

	$mb_userdata = array(
		'first_name' => ucwords( strtolower( sanitize_text_field( $_POST[ 'mb_first_name' ] ) ) ),
		'last_name'  => ucwords( strtolower( sanitize_text_field( $_POST[ 'mb_last_name' ] ) ) ),
		'email'      => is_email( strtolower( sanitize_email( wp_unslash( $_POST[ 'mb_email' ] ) ) ) ),
	);

	$mb_metadata = array(
		'phone'           => format_save_phone( $_POST[ 'mb_phone' ] ),
		'birthday'        => make_date_safe( $_POST[ 'mb_birthday' ] ),
		'occupation'      => ucwords( strtolower( sanitize_text_field( $_POST[ 'mb_occupation' ] ) ) ),
		'relationship_id' => ( isset( $_POST[ 'mb_relationship' ] ) ? intval( $_POST[ 'mb_relationship' ] ) : 1 ),
		'membership_type' => intval( $_POST[ 'memb_type' ] ),
		'addr1'           => sanitize_text_field( $_POST[ 'mb_addr1' ] ),
		'addr2'           => sanitize_text_field( $_POST[ 'mb_addr2' ] ),
		'address'         => sanitize_text_field( $_POST[ 'mb_addr1' ] ) . ' ' . sanitize_text_field( $_POST[ 'mb_addr2' ] ),
		'city'            => sanitize_text_field( $_POST[ 'mb_city' ] ),
		'state'           => sanitize_text_field( $_POST[ 'mb_state' ] ),
		'zip'             => sanitize_text_field( $_POST[ 'mb_zip' ] ),
	);

	$cleaned_new_reg_form_input[ 'reg_action' ]       = $reg_action;
	$cleaned_new_reg_form_input[ 'userdata' ][ 'mb' ] = $mb_userdata;
	$cleaned_new_reg_form_input[ 'metadata' ][ 'mb' ] = $mb_metadata;

	if ( isset( $_POST[ 'sp_first_name' ] ) && ! empty( $_POST[ 'sp_first_name' ] ) ) {

		$sp_userdata = array(
			'first_name' => ucwords( strtolower( sanitize_text_field( $_POST[ 'sp_first_name' ] ) ) ),
			'last_name'  => ucwords( strtolower( sanitize_text_field( $_POST[ 'sp_last_name' ] ) ) ),
			'email'      => is_email( strtolower( sanitize_email( $_POST[ 'sp_email' ] ) ) ),
		);

		$sp_metadata = array(
			'phone'        => format_save_phone( $_POST[ 'sp_phone' ] ),
			'birthday'     => make_date_safe( $_POST[ 'sp_birthday' ] ),
			'relationship' => intval( $_POST[ 'sp_relationship' ] ),
		);

		$cleaned_new_reg_form_input[ 'userdata' ][ 'sp' ] = $sp_userdata;
		$cleaned_new_reg_form_input[ 'metadata' ][ 'sp' ] = $sp_metadata;
	}

	// Begin child data loop
	for ( $c = 1; $c <= $max_children; $c ++ ) {
		$ctag       = "c{$c}";
		$udata_name = $ctag . "_userdata";  #runtime array name
		$mdata_name = $ctag . "_metadata";  #runtime array name

		if ( isset( $_POST[ $ctag . '_first_name' ] ) && ! empty( $_POST[ $ctag . '_first_name' ] ) ) {

			${$udata_name} = array(
				'first_name' => ucwords( strtolower( sanitize_text_field( $_POST[ $ctag . '_first_name' ] ) ) ),
				'last_name'  => ucwords( strtolower( sanitize_text_field( $_POST[ $ctag . '_last_name' ] ) ) ),
				'email'      => is_email( strtolower( sanitize_email( $_POST[ $ctag . '_email' ] ) ) ),
			);

			${$mdata_name} = array(
				'birthday'     => make_date_safe( $_POST[ $ctag . '_birthday' ] ),
				'relationship' => intval( $_POST[ $ctag . '_relationship' ] ),
			);

			$cleaned_new_reg_form_input[ 'userdata' ][ $ctag ] = ${$udata_name};
			$cleaned_new_reg_form_input[ 'metadata' ][ $ctag ] = ${$mdata_name};
		}
	} #end child_data loop

	return $cleaned_new_reg_form_input;
}

function clean_renewal_form_input() {
	global $max_children;

	$orig_meta = $_SESSION[ 'orig_user_meta' ];
	$orig_user = $_SESSION[ 'orig_user_data' ];

	$reg_action = ucwords( strtolower( sanitize_text_field( $_POST[ 'reg_action' ] ) ) );

	$mb_userdata = array(
		'ID'         => intval( $_POST[ 'mb_id' ] ),
		'first_name' => ucwords( strtolower( sanitize_text_field( $_POST[ 'mb_first_name' ] ) ) ),
		'last_name'  => ucwords( strtolower( sanitize_text_field( $_POST[ 'mb_last_name' ] ) ) ),
		'email'      => is_email( strtolower( sanitize_email( wp_unslash( $_POST[ 'mb_email' ] ) ) ) ),
	);

	$mb_metadata = array(
		'mb_id'           => intval( $_POST[ 'mb_id' ] ),
		'phone'           => format_save_phone( $_POST[ 'mb_phone' ] ),
		'birthday'        => make_date_safe( $_POST[ 'mb_birthday' ] ),
		'occupation'      => ucwords( strtolower( sanitize_text_field( $_POST[ 'mb_occupation' ] ) ) ),
		'relationship_id' => ( isset( $_POST[ 'mb_relationship' ] ) ? intval( $_POST[ 'mb_relationship' ] ) : 1 ),
		'membership_type' => intval( $_POST[ 'memb_type' ] ),
		'addr1'           => sanitize_text_field( $_POST[ 'mb_addr1' ] ),
		'addr2'           => sanitize_text_field( $_POST[ 'mb_addr2' ] ),
		'city'            => sanitize_text_field( $_POST[ 'mb_city' ] ),
		'state'           => sanitize_text_field( $_POST[ 'mb_state' ] ),
		'zip'             => sanitize_text_field( $_POST[ 'mb_zip' ] ),
	);

	$cleaned_renewal_form_input[ 'reg_action' ]       = $reg_action;
	$cleaned_renewal_form_input[ 'userdata' ][ 'mb' ] = $mb_userdata;
	$cleaned_renewal_form_input[ 'metadata' ][ 'mb' ] = $mb_metadata;

	if ( isset( $_POST[ 'sp_first_name' ] ) && ! empty( $_POST[ 'sp_first_name' ] ) ) {

		if ( isset( $_POST[ 'sp_id' ] ) && ! empty( $_POST[ 'sp_id' ] ) ) {
			$cleaned_renewal_form_input[ 'metadata' ][ 'sp' ][ 'mb_id' ] = intval( $_POST[ 'mb_id' ] );
			$cleaned_renewal_form_input[ 'metadata' ][ 'sp' ][ 'sp_id' ] = intval( $_POST[ 'sp_id' ] );
			$cleaned_renewal_form_input[ 'metadata' ][ 'mb' ][ 'sp_id' ] = intval( $_POST[ 'sp_id' ] );
		}

		$sp_userdata = array(
			'first_name' => ucwords( strtolower( sanitize_text_field( $_POST[ 'sp_first_name' ] ) ) ),
			'last_name'  => ucwords( strtolower( sanitize_text_field( $_POST[ 'sp_last_name' ] ) ) ),
			'email'      => is_email( strtolower( sanitize_email( $_POST[ 'sp_email' ] ) ) ),
		);

		$sp_metadata = array(
			'phone'        => format_save_phone( $_POST[ 'sp_phone' ] ),
			'birthday'     => make_date_safe( $_POST[ 'sp_birthday' ] ),
			'relationship' => intval( $_POST[ 'sp_relationship' ] ),
		);

		$cleaned_renewal_form_input[ 'userdata' ][ 'sp' ] = $sp_userdata;
		$cleaned_renewal_form_input[ 'metadata' ][ 'sp' ] = $sp_metadata;

	}

	// Begin child data loop
	for ( $c = 1; $c <= $max_children; $c ++ ) {
		$ctag       = "c{$c}";
		$udata_name = $ctag . "_userdata";  #runtime array name
		$mdata_name = $ctag . "_metadata";  #runtime array name

		if ( isset( $_POST[ $ctag . '_first_name' ] ) && ! empty( $_POST[ $ctag . '_first_name' ] ) ) {

			if ( isset( $_POST[ $ctag . '_id' ] ) && ! empty( $_POST[ $ctag . '_id' ] ) ) {

				$cleaned_renewal_form_input[ 'metadata' ][ 'mb' ][ $ctag . '_id' ]  = intval( $_POST[ $ctag . '_id' ] );
				$cleaned_renewal_form_input[ 'metadata' ][ $ctag ][ 'mb_id' ]       = intval( $_POST[ 'mb_id' ] );
				$cleaned_renewal_form_input[ 'metadata' ][ $ctag ][ $ctag . '_id' ] = intval( $_POST[ $ctag . '_id' ] );
			}

			${$udata_name} = array(
				'first_name' => ucwords( strtolower( sanitize_text_field( $_POST[ $ctag . '_first_name' ] ) ) ),
				'last_name'  => ucwords( strtolower( sanitize_text_field( $_POST[ $ctag . '_last_name' ] ) ) ),
				'email'      => is_email( strtolower( sanitize_email( $_POST[ $ctag . '_email' ] ) ) ),
			);

			${$mdata_name} = array(
				'mb_id'        => intval( $_POST[ 'mb_id' ] ),
				'birthday'     => make_date_safe( $_POST[ $ctag . '_birthday' ] ),
				'relationship' => intval( $_POST[ $ctag . '_relationship' ] ),
			);

			$cleaned_renewal_form_input[ 'userdata' ][ $ctag ] = ${$udata_name};
			$cleaned_renewal_form_input[ 'metadata' ][ $ctag ] = ${$mdata_name};
		}
	} #end child_data loop

	return $cleaned_renewal_form_input;
}

function load_post_data( $fam_tag_key ) {

	foreach ( $_POST as $post_key => $post_value ) {

		if ( preg_match( "/" . $fam_tag_key . "/", $post_key, $matches ) ) {

			$fam_tag_key_data[] = $matches;
		}
	}

	return $fam_tag_key_data;
}

/**
 * @param $member
 * @param $user_id
 */
function add_members_metadata( $member, $user_id ) {
	global $memb_error;

	foreach ( $member as $meta_key => $meta_value ) {
		if ( ! isset( $user_id ) || empty( $user_id ) ) {
			continue;
		} else {
			update_user_meta( $user_id, $meta_key, $meta_value );

			// so check and make sure the stored value matches $new_value
			if ( get_user_meta( $user_id, $meta_key, true ) != $meta_value ) {
				$code      = "user_metadata_update_" . $user_id;
				$error_msg = "The update of the users metadata failed. : $user_id, $meta_key, $meta_value ";
				$memb_error->add( $code, $error_msg );
				error_log_message( $memb_error->get_error_message( $code ) );
				unset( $code, $error_msg );
			}
		}
	}
}

function update_members_metadata( $user_id, $meta_key, $meta_value, $prev_value = '' ) {
	global $memb_error;

	update_user_meta( $user_id, $meta_key, $meta_value, $prev_value );

	// so check and make sure the stored value matches $new_value
	if ( get_user_meta( $user_id, $meta_key, true ) != $meta_value ) {

		$code       = 'metadata_update_' . $meta_key;
		$error_data = array( $user_id, $meta_key, $form_value, $metadata_value );
		$error_msg  = "Metadata update failed : $user_id, $meta_key, $form_value, $metadata_value";
		$memb_error->add( $code, __( $error_msg, 'membership' ), $error_data );

		error_log_message( $memb_error->get_error_message( $code ) );
		$return_value = $memb_error->get_error_message( $code );
		unset( $code, $error_msg, $error_data );

	} else {

		$return_value = true;
	}

	return $return_value;
}


function compare_form_data_to_usermeta( $user_id, $meta_key, $form_value ) {
	global $memb_error;

	// Verify the stored value matches new or updated value
	$metadata_value = get_user_meta( $user_id, $meta_key, true );

	if ( $metadata_value != $form_value ) {

		$code       = 'compare_data_' . $meta_key;
		$error_data = array( $user_id, $meta_key, $form_value, $metadata_value );
		$message    = "Form input did not match existing metadata. Update needed for : $user_id, $meta_key, $form_value, $metadata_value";
		$memb_error->add( $code, __( $message, 'membership' ), $error_data );

		error_log_message( $memb_error->get_error_message( $code ) );

		$result = false;

	} else {

		$result = true;
	}

	return $result;
}

function verify_userdata_value( $user_id, $meta_key, $meta_value ) {
	global $memb_error;
	// Verify the stored value matches new or updated value
	$user_info = get_userdata( $user_id );
	if ( ! $user_info ) {
		$memb_error->add( 'get userdata failed', "There was a failure when getting userdata for: $user_id" );
		$result = $memb_error;
	}

	return $result;
}

/**
 * @param $member
 *
 * @return int|string
 * @internal param $membdata
 *
 */
function create_wordpress_user( $member ) {
	global $memb_error;

	$userdata = array(
		'first_name'      => $member[ 'first_name' ],
		'last_name'       => $member[ 'last_name' ],
		'user_email'      => $member[ 'email' ],
		'user_login'      => ( isset( $member[ 'username' ] ) ? $member[ 'username' ] : mb_strtolower( substr( $member[ 'first_name' ], 0, 3 ) . substr( $member[ 'last_name' ], 0, 4 ) ) ),
		'nickname'        => $member[ 'first_name' ] . ' ' . $member[ 'last_name' ],
		'display_name'    => $member[ 'first_name' ] . ' ' . $member[ 'last_name' ],
		'user_nicename'   => $member[ 'first_name' ] . '-' . $member[ 'last_name' ],
		'user_registered' => $member[ 'reg_date' ],
	);

	$memb_id = ( isset( $member[ 'ID' ] ) ) ? $member[ 'ID' ] : username_exists( $userdata[ 'user_login' ] );

	if ( $memb_id ) {

		$userdata[ 'ID' ] = $memb_id;

	} else {

		$userdata[ 'user_pass' ] = ( isset( $member->pass ) ? $member->pass : wp_generate_password( $length = 12, $include_standard_special_chars = false ) );
	}

	$memb_id = wp_insert_user( $userdata );


	return $memb_id;
}

function get_clean_usermeta_data( $user_id ) {

	$clean_user_metadata = array_map( function ( $a ) {

		return $a[ 0 ];

	}, get_user_meta( $user_id ) );

	return $clean_user_metadata;
}

/**
 * @param $message
 * @param $debug
 */
function debug_log_message( $message, $debug = false ) {
	if ( $debug ) {
		error_log( "!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!", 0 );
		error_log( "!!!!!!   $message   !!!!!!", 0 );
		error_log( "!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!", 0 );
	}
}

/**
 * @param $message
 * @param $debug
 */
function error_log_message( $message ) {
	error_log( "###############################################################", 0 );
	error_log( "######   $message   ######", 0 );
	error_log( "###############################################################", 0 );
}


//Shortcodes to be used in pages, posts, widgets etc.

// Add Shortcode to hide email addresses from bots.
function email_cloaking_shortcode( $atts ) {

	// Attributes
	extract( shortcode_atts( array(
		'email' => '',
	), $atts ) );

	// Code
	return antispambot( $email );
}

//To be added to a paypal canceled payment return page
function canceled_paypal_payment() {
	//todo: create code to do something with the user data if they cancel the registration or renewal at the paypal payment screen.
}


function new_member_paypal_welcome_processing() {
	//todo: create code needed to activate a new members registration. Including creating the wordpress user account an adding the registration data to the user_metadata
}

function register_membership_shortcodes() {
	add_shortcode( 'payment_canceled', 'canceled_paypal_payment' );
	add_shortcode( 'cloak', 'email_cloaking_shortcode' );
	add_shortcode( 'payment_completed', 'new_member_paypal_welcome_processing' );
}

add_action( 'init', 'register_membership_shortcodes' );

function list_active_members() {
	// WP_User_Query arguments
	$args = array(
		'role'       => 'Subscriber',
		'number'     => '25',
		'order'      => 'ASC',
		'orderby'    => 'user_login',
		'meta_query' => array(
			array(
				'key'     => 'hatch_date',
				'compare' => 'EXISTS',
				'type'    => 'DATETIME',
			),
		),
		'fields'     => array(
			'first_name',
			'last_name',
			'email',
			'phone',
			'addr1',
			'addr2',
			'city',
			'state',
			'zip',
		),
	);

	// The User Query
	$user_query = new WP_User_Query( $args );
}

add_shortcode( 'list_members', 'list_active_members' );

// Register User Contact Methods that are displayed in a users profile.
function member_contact_methods( $member_contact_method ) {

	$member_contact_method[ 'address' ]  = __( 'Address', 'text_domain' );
	$member_contact_method[ 'suite' ]    = __( 'Suite/Apt', 'text_domain' );
	$member_contact_method[ 'city' ]     = __( 'City', 'text_domain' );
	$member_contact_method[ 'state' ]    = __( 'State', 'text_domain' );
	$member_contact_method[ 'zip' ]      = __( 'Zip', 'text_domain' );
	$member_contact_method[ 'Phone' ]    = __( 'Phone', 'text_domain' );
	$member_contact_method[ 'twitter' ]  = __( 'Twitter Username', 'text_domain' );
	$member_contact_method[ 'facebook' ] = __( 'Facebook Username', 'text_domain' );
	$member_contact_method[ 'yahoo' ]    = __( 'YAHOO Groups Username', 'text_domain' );

	return $member_contact_method;
}

// Hook into the 'user_contactmethods' filter
add_filter( 'user_contactmethods', 'member_contact_methods' );


function load_states_array() {
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

/**
 * @param $table
 *
 * @return mixed
 */
function get_types( $table ) {
	global $wpdb;
	$table_data = $wpdb->get_results( "SELECT * FROM $table" );
	$types      = array();

	foreach ( $table_data as $type_obj ) {
		if ( isset( $type_obj->rel_type_long ) ) {
			$types[ $type_obj->ID ] = $type_obj->rel_type_long;
		} else {
			$types[ $type_obj->ID ] = $type_obj->memb_type;
		}
	}

	return $types;

}

function map_metadata_to_form_fields( $type ) {
	global $max_children;
	$splt_type = str_split($type);
	// metadata field => form field
	switch ( $type ) {
		case 'mb':
			$field_map = get_mb_field_map();
			break;
		case 'sp':
			$field_map = get_sp_field_map();
			break;
		default:
			$field_map = get_child_field_map( $type, $splt_type[1] );
			break;
	}

	return $field_map;
}

function get_mb_field_map() {

	$mapped_fields = array(
		"first_name" => "mb_first_name",
		"last_name"  => "mb_last_name",

		"mb_first_name" => "mb_first_name",
		"mb_last_name"  => "mb_last_name",

		"mb_mb_first_name" => "mb_first_name",
		"mb_mb_last_name"  => "mb_last_name",

		"phone"      => "mb_phone",
		"mb_phone"   => "mb_phone",
		"user_phone" => "mb_phone",

		"email"       => "mb_email",
		"mb_email"    => "mb_email",
		"mb_mb_email" => "mb_email",

		"birthday"    => "mb_birthday",
		"mb_birthday" => "mb_birthday",

		"occupation"    => "mb_occupation",
		"mb_occupation" => "mb_occupation",
		"user_occup"    => "mb_occupation",

		"addr1" => "mb_addr1",
		"addr2" => "mb_addr2",
		"city"  => "mb_city",
		"state" => "mb_state",
		"zip"   => "mb_zip",

		"mb_addr1" => "mb_addr1",
		"mb_addr2" => "mb_addr2",
		"mb_city"  => "mb_city",
		"mb_state" => "mb_state",
		"mb_zip"   => "mb_zip",

		"user_addr"  => "mb_addr1",
		"user_city"  => "mb_city",
		"user_state" => "mb_state",
		"user_zip"   => "mb_zip",

		"mb_relationship_id" => "mb_relationship",
		"relationship"       => "mb_relationship",
		"relationship_id"    => "mb_relationship",

		"mb_membership_id"   => "memb_type",
		'mb_membership_type' => "memb_type",
		"membership"         => "memb_type",
		"membership_id"      => "memb_type",
		"membership_type"    => "memb_type",
		"memb_type"          => "memb_type",
		"user_memb_type"     => "memb_type",

		"wp_user_id"    => "mb_id",
		"fam_memb_id_1" => "sp_id",

		"fam_1_first_name"      => "c1_first_name",
		"fam_1_last_name"       => "c1_last_name",
		"fam_1_email"           => "c1_email",
		"fam_1_birthday"        => "c1_birthday",
		"fam_1_relationship_id" => "c1_relationship",

		"fam_2_first_name"      => "c2_first_name",
		"fam_2_last_name"       => "c2_last_name",
		"fam_2_email"           => "c2_email",
		"fam_2_birthday"        => "c2_birthday",
		"fam_2_relationship_id" => "c2_relationship",

		"fam_3_first_name"      => "c3_first_name",
		"fam_3_last_name"       => "c3_last_name",
		"fam_3_email"           => "c3_email",
		"fam_3_birthday"        => "c3_birthday",
		"fam_3_relationship_id" => "c3_relationship",

		"fam_4_first_name"      => "c4_first_name",
		"fam_4_last_name"       => "c4_last_name",
		"fam_4_email"           => "c4_email",
		"fam_4_birthday"        => "c4_birthday",
		"fam_4_relationship_id" => "c4_relationship",

		"c1_first_name"      => "c1_first_name",
		"c1_last_name"       => "c1_last_name",
		"c1_email"           => "c1_email",
		"c1_birthday"        => "c1_birthday",
		"c1_relationship"    => "c1_relationship",
		"c1_relationship_id" => "c1_relationship",

		"c2_first_name"      => "c2_first_name",
		"c2_last_name"       => "c2_last_name",
		"c2_email"           => "c2_email",
		"c2_birthday"        => "c2_birthday",
		"c2_relationship"    => "c1_relationship",
		"c2_relationship_id" => "c2_relationship",

		"c3_first_name"      => "c3_first_name",
		"c3_last_name"       => "c3_last_name",
		"c3_email"           => "c3_email",
		"c3_birthday"        => "c3_birthday",
		"c3_relationship"    => "c1_relationship",
		"c3_relationship_id" => "c3_relationship",

		"c4_first_name"      => "c4_first_name",
		"c4_last_name"       => "c4_last_name",
		"c4_email"           => "c4_email",
		"c4_birthday"        => "c4_birthday",
		"c4_relationship"    => "c1_relationship",
		"c4_relationship_id" => "c4_relationship",

	);

	return $mapped_fields;
}

function get_sp_field_map() {

	$mapped_fields = array(
		"first_name"         => "sp_first_name",
		"sp_first_name"      => "sp_first_name",
		"last_name"          => "sp_last_name",
		"sp_last_name"       => "sp_last_name",
		"phone"              => "sp_phone",
		"user_phone"         => "sp_phone",
		"email"              => "sp_email",
		"birthday"           => "sp_birthday",
		"relationship"       => "sp_relationship",
		"relationship_id"    => "sp_relationship",
		"sp_relationship_id" => "sp_relationship",
		"sp_relationship"    => "sp_relationship",
		"sp_id"              => "sp_id",
		"fam_memb_id_1"      => "sp_id",
		"wp_user_id"         => "sp_id",
		"membUserID"         => 'mb_id',
	);

	return $mapped_fields;
}

function get_child_field_map( $ctype, $counter ) {

	$mapped_fields = array(
		"first_name"      => "{$ctype}_first_name",
		"last_name"       => "{$ctype}_last_name",
		"email"           => "{$ctype}_email",
		"birthday"        => "{$ctype}_birthday",
		"relationship"    => "{$ctype}_relationship",
		"relationship_id" => "{$ctype}_relationship",
		"c{$counter}_id"                 => "{$ctype}_id",

		"fam_{$counter}_first_name"      => "{$ctype}_first_name",
		"fam_{$counter}_last_name"       => "{$ctype}_last_name",
		"fam_{$counter}_email"           => "{$ctype}_email",
		"fam_{$counter}_birthday"        => "{$ctype}_birthday",
		"fam_{$counter}_relationship_id" => "{$ctype}_relationship",
		"fam_memb_id_{$counter}"         => "{$ctype}_id",
	);

	return $mapped_fields;
}

function get_relationship_keys() {
	$relationship_keys = array(
		"relationship",
		"relationship_id",
		"relationship_type",
		"mb_relationship",

		'sp_relationship',
		'sp_relationship_id',
		'c1_relationship',
		'c1_relationship_id',

		'c2_relationship',
		'c2_relationship_id',

		'c3_relationship',
		'c3_relationship_id',

		'c4_relationship',
		'c4_relationship_id',

		'fam_1_relationship_id',
		'fam_2_relationship_id',
		'fam_3_relationship_id',
		'fam_4_relationship_id',
	);

	return $relationship_keys;
}

function member_relationship_value_map() {
	$rel_val_map = array(
		'M' => 1,
		'S' => 2,
		'P' => 3,
		'C' => 4,
		'O' => 5,

		'Member'  => 1,
		'Spouse'  => 2,
		'Partner' => 3,
		'Child'   => 4,
		'Other'   => 5,
	);

	return $rel_val_map;
}

function map_rel_values_to_form_values( $user_rel_value ) {
	$rel_values = member_relationship_value_map();

	foreach ( $rel_values as $rel_key => $rel_value ) {

		if ( $user_rel_value == $rel_value ) {

			$return_rel_value = $rel_value;

		} else if ( $user_rel_value == $rel_key ) {

			$return_rel_value = $rel_value;
		}
	}

	return $return_rel_value;
}

function get_member_type_keys() {
	$memb_type_keys = array(
		"membership",
		"membership_id",
		"membership_type",
		"mb_membership_type",
		"memb_type",
		"user_memb_type",
	);

	return $memb_type_keys;
}

function get_membership_type_value_map() {
	$memb_val_map = array(
		'ID' => 1,
		'IC' => 2,
		'CO' => 3,
		'HH' => 4,

		'Single' => 1,
		'Couple' => 3,
		'Family' => 4,

		'S' => 1,
		'C' => 3,
		'F' => 4,
	);

	return $memb_val_map;
}

function validate_membership_type( $memb_data ) {

	$update_status = null;

	$memb_id = $memb_data->get( 'ID' );

	$membership_type_map = get_membership_type_value_map();

	foreach ( get_member_type_keys() as $mtype_key ) {

		if ( $memb_data->__isset( $mtype_key ) ) {

			$mtype_value = $memb_data->__get( $mtype_key );

			foreach ( $membership_type_map as $mmtype_key => $mmtype_value ) {

				if ( $mtype_key == 'membership_id' || $mtype_key == 'membership_type' ) { //Changing this to membership_type

					if ( $mmtype_value == $mtype_value || $mtype_value == $mmtype_key ) {

						update_user_meta( $memb_id, 'membership_type', $mmtype_value );
					}
				}

				if ( $mtype_value == $mmtype_key && $mtype_key <> 'membership_type' ) {

					delete_user_meta( $memb_id, $mtype_key );
				}
			}
		}
	}

	$updated_member_data = get_userdata( $memb_id );

	return $updated_member_data;
}

function validate_relationship_type( $memb_data ) {
	global $memb_error;

	$update_status = null;

	$memb_id = $memb_data->get( 'ID' );

	foreach ( get_relationship_keys() as $rel_key ) {

		if ( $memb_data->__isset( $rel_key ) ) {

			// get member's relationship meta_key value
			$prev_rel_value = $memb_data->__get( $rel_key );

			// Using map of relationship keys to values
			// Get the correct meta_key value for the relationship_id meta_key
			$new_rel_value = map_rel_values_to_form_values( $prev_rel_value );

			if ( ! $memb_data->__isset( 'relationship_id' ) ) {

				// Add/Update member's relationship_id meta_key and value
				$update = update_user_meta( $memb_id, 'relationship_id', $new_rel_value );

				if ( ! $update ) {

					// Failed update of the relationship_id meta_key
					$code      = 'failed_update_relationship_id';
					$error_msg = "Member {$memb_id} relationship_id meta_key update failed.";
					$memb_error->add( $code, $error_msg );
					error_log_message( $memb_error->get_error_message( $code ) );
					unset( $code, $error_msg );
				}
			}

			// If the relationship meta_key was not relationship_id
			// Delete it from the member's metadata
			if ( $rel_key <> 'relationship_id' ) {

				// Remove the invalid relationship meta_key and value
				if ( ! delete_user_meta( $memb_id, $rel_key ) ) {

					// Failed deletion of the invalid relationship metadata
					$code      = 'failed_delete_meta_key_' . $rel_key;
					$error_msg = "Failed to delete Member {$memb_id} metadata record {$rel_key}.";
					$memb_error->add( $code, $error_msg );
					error_log_message( $memb_error->get_error_message( $code ) );
					unset( $code, $error_msg );

				} else {

					// Successful deletion of the invalid relationship metadata
					$code      = 'success_delete_meta_key_' . $rel_key;
					$error_msg = "Successfully deleted member {$memb_id} metadata record {$rel_key}.";
					$memb_error->add( $code, $error_msg );
					error_log_message( $memb_error->get_error_message( $code ) );
					unset( $code, $error_msg );
				}
			}

		}
	}

	$updated_member_data = get_userdata( $memb_id );

	return $updated_member_data;
}

function validate_member_ids( $memb_data, $key_memb_id ) {
	global $max_children;

	$memb_id = $memb_data->get( 'ID' );

	$key_memb_data = get_userdata( $key_memb_id );

	if ( $key_memb_data->__isset( 'relationship_type' ) ) {

		$key_memb_relationship_value = $key_memb_data->__get( 'relationship_type' );

	} elseif ( $key_memb_data->__isset( 'relationship_id' ) ) {

		$key_memb_relationship_value = $key_memb_data->__get( 'relationship_id' );
	}

	// If the key member is a spouse/partner or other
	// set memb_data sp_id to key_memb_id.
	if ( $key_memb_relationship_value <> 1 && $key_memb_relationship_value <> 4 ) {

		update_user_meta( $memb_id, 'sp_id', $key_memb_id );

		if ( $memb_data->__isset( 'fam_memb_id_1' ) ) {
			delete_user_meta( $memb_id, 'fam_memb_id_1' );
		}

	} elseif ( $key_memb_relationship_value == 4 ) {

		for ( $c = 1; $c <= $max_children; $c ++ ) {
			$ctag  = "c{$c}";
			$c1    = $c - 1;
			$ctag1 = "c{$c1}";

			$child_key  = $ctag . '_id';
			$child1_key = $ctag1 . '_id';
			$fam_id_key = "fam_memb_id_{$c}";

			if ( $key_memb_data->__isset( $child_key ) || $key_memb_data->__isset( $fam_id_key ) ) {

				update_user_meta( $memb_id, $child_key, $key_memb_id );

				if ( $memb_data->__isset( $fam_id_key ) ) {
					delete_user_meta( $memb_id, $fam_id_key );
				}

			} elseif ( $memb_data->__isset( $child_key ) || $memb_data->__isset( $fam_id_key ) ) {

				//update_user_meta( $memb_id, $child1_key, $key_memb_id );
				//update_user_meta( $key_memb_id, $child1_key, $key_memb_id );

				if ( $memb_data->__isset( $fam_id_key ) ) {
					delete_user_meta( $memb_id, $fam_id_key );
				}
			}
		}

	} elseif ( ! $memb_data->__isset( 'mb_id' ) ) {

		update_user_meta( $memb_id, 'mb_id', $key_memb_id );
	}

	$updated_member_data = get_userdata( $memb_id );

	return $updated_member_data;
}

/**
 * @param $cur_memb_value
 *
 * @return int|mixed|string
 */
function map_memb_values_to_form_values( $cur_memb_value ) {
	$memb_values = get_membership_type_value_map();

	foreach ( $memb_values as $memb_key => $memb_value ) {
		if ( $cur_memb_value == $memb_value ) {
			$return_memb_value = $memb_value;
		} else if ( $cur_memb_value == $memb_key ) {
			$return_memb_value = $memb_value;
		}
	}

	return $return_memb_value;
}

function map_metadata_to_input_form_fields( $metadata_field_map, $usr_metadata ) {

	foreach ( $metadata_field_map as $meta_field_key => $form_field_key ) {

		foreach ( $usr_metadata as $usr_meta_key => $usr_meta_value ) {
			$meta_value = $usr_meta_value[ 0 ];

			if ( isset( $meta_value ) && ! empty( $meta_value ) ) {

				if ( $usr_meta_key == $meta_field_key ) {
					$prepared_meta_data = prepare_member_data( $meta_value, $meta_field_key );

					if ( $prepared_meta_data <> $metadata_fields[ $form_field_key ] ) {
						$metadata_fields[ $form_field_key ] = $prepared_meta_data;
					}
				}
			}
		}
	}

	return $metadata_fields;
}

function get_member_id_keys() {
	$member_id_keys = array(
		'mb_id',
		'prim_memb_id',
		'sp_id',
		'c1_id',
		'c2_id',
		'c3_id',
		'c4_id',

		'membUserID',
		'user_id',
		'wp_user_id',
		'sp_wp_user_id',
		'fam_memb_id_1',
		'fam_memb_id_2',
		'fam_memb_id_3',
		'fam_memb_id_4',
	);

	return $member_id_keys;
}

function get_user_relationship_data( $user_id ) {
	global $wpdb;

	$relationship_data = $wpdb->get_results( $wpdb->prepare( "SELECT  user_id, meta_key, meta_value 
FROM ctxphcco_wp_db . ctxphc_usermeta
WHERE user_id = %d
AND meta_key IN( 
    'relationship',
    'relationship_id',
    'mb_relationship'
    'sp_relationship',
    'sp_relationship_id',
    'c1_relationship',
    'c2_relationship',
    'c3_relationship',
    'c4_relationship',
    'c1_relationship_id',
    'c2_relationship_id',
    'c3_relationship_id',
    'c4_relationship_id',
    'fam_1_relationship_id',
    'fam_2_relationship_id',
    'fam_3_relationship_id',
    'fam_4_relationship_id'
     )  
ORDER BY user_id", $user_id ) );

	return $relationship_data;
}

function get_user_membership_data( $user_id ) {
	global $wpdb;

	$membership_data = $wpdb->get_results( $wpdb->prepare( "SELECT  user_id, meta_key, meta_value FROM ctxphcco_wp_db . ctxphc_usermeta
WHERE user_id = %d AND meta_key IN( 'membership',
		'membership_id',
		'membership_type',
		'mb_membership_type',
		'memb_type',
		'user_memb_type' ) 
ORDER BY user_id", $user_id ) );

	return $membership_data;
}

function get_associated_member_ids( $user_id ) {
	global $wpdb;
	$associated_ids = array();

	$associated_memb_records = $wpdb->get_results( $wpdb->prepare( "SELECT u.ID, m . user_id, m.meta_key, m.meta_value 
FROM ctxphcco_wp_db . ctxphc_users u JOIN ctxphcco_wp_db . ctxphc_usermeta m
WHERE u . ID = %d AND u.ID = m . meta_value AND m . meta_key IN( 
        'mb_id',
        'sp_id',
        'c1_id',
		'c2_id',
		'c4_id',
		'c4_id',
		'prim_memb_id',
		'membUserID',
		'user_id',
		'wp_user_id',
		'sp_wp_user_id',		
		'fam_memb_id_1',
		'fam_memb_id_2',
		'fam_memb_id_3',
		'fam_memb_id_4'
 ) AND u . ID > 999 AND m . user_id <> u . ID
ORDER BY m . user_id", $user_id ) );

	foreach ( $associated_memb_records as $associated_memb_record ) {
		if ( ! in_array( $associated_memb_record->user_id, $associated_ids ) ) {
			$associated_ids[] = $associated_memb_record->user_id;
		}
	}

	return $associated_ids;
}

function map_state_values() {
	$states_mapping = array(
		'AL' => 1,
		'AK' => 2,
		'AZ' => 3,
		'AR' => 4,
		'CA' => 5,
		'CO' => 6,
		'CT' => 7,
		'DE' => 8,
		'DC' => 51,
		'FL' => 9,
		'GA' => 10,
		'HI' => 11,
		'ID' => 12,
		'IL' => 13,
		'IN' => 14,
		'IA' => 15,
		'KS' => 16,
		'KY' => 17,
		'LA' => 18,
		'ME' => 19,
		'MD' => 20,
		'MA' => 21,
		'MI' => 22,
		'MN' => 23,
		'MS' => 24,
		'MO' => 25,
		'MT' => 26,
		'NE' => 27,
		'NV' => 29,
		'NH' => 29,
		'NJ' => 30,
		'NM' => 31,
		'NY' => 32,
		'NC' => 33,
		'ND' => 34,
		'OH' => 35,
		'OK' => 36,
		'OR' => 37,
		'PA' => 38,
		'RI' => 39,
		'SC' => 40,
		'SD' => 41,
		'TN' => 42,
		'TX' => 43,
		'UT' => 44,
		'VT' => 45,
		'VA' => 46,
		'WA' => 47,
		'WV' => 48,
		'WI' => 49,
		'WY' => 50,
	);

	return $states_mapping;
}

function map_state_values_to_form_values( $cur_memb_value ) {
	$state_values = map_state_values();

	foreach ( $state_values as $state_key => $state_value ) {
		if ( $cur_memb_value == $state_value ) {
			$return_memb_value = $state_key;
		} else if ( $cur_memb_value == $state_key ) {
			$return_memb_value = $state_key;
		}
	}

	return $return_memb_value;
}

add_filter( 'body_class', 'browser_body_class' );
function browser_body_class( $classes ) {
	global $is_lynx, $is_gecko, $is_IE, $is_opera, $is_NS4, $is_safari, $is_chrome, $is_iphone;

	if ( $is_lynx ) {
		$classes[] = 'lynx';
	} elseif ( $is_gecko ) {
		$classes[] = 'gecko';
	} elseif ( $is_opera ) {
		$classes[] = 'opera';
	} elseif ( $is_NS4 ) {
		$classes[] = 'ns4';
	} elseif ( $is_safari ) {
		$classes[] = 'safari';
	} elseif ( $is_chrome ) {
		$classes[] = 'chrome';
	} elseif ( $is_IE ) {
		$classes[] = 'ie';
	} else {
		$classes[] = 'unknown';
	}

	if ( $is_iphone ) {
		$classes[] = 'iphone';
	}

	return $classes;
}

add_filter( 'bulk_actions-load-users', 'register_my_bulk_actions' );

/**
 * @param $bulk_actions
 *
 * @return mixed
 */
function register_my_bulk_actions( $bulk_actions ) {
	$bulk_actions[ 'archive' ] = __( 'Archive', 'archive' );

	return $bulk_actions;
}


add_filter( 'handle_bulk_actions-users', 'my_bulk_action_handler', 10, 3 );

function my_bulk_action_handler( $redirect_to, $doaction, $user_ids ) {
	if ( $doaction !== 'archive' ) {
		return $redirect_to;
	}
	foreach ( $user_ids as $user_id ) {
		// Perform action for each post.
	}
	$redirect_to = add_query_arg( 'bulk_archived_users', count( $user_ids ), $redirect_to );

	return $redirect_to;
}

add_action( 'admin_notices', 'my_bulk_action_admin_notice' );

function my_bulk_action_admin_notice() {
	if ( ! empty( $_REQUEST[ 'bulk_archived_users' ] ) ) {
		$archived_count = intval( $_REQUEST[ 'bulk_archive_users' ] );
		printf( '<div id="message" class="updated fade">' . _n( 'Archived %s User.', 'Archived %s User.', $archived_count, 'archived' ) . '</div>', $archived_count );
	}
}

/**
 * Show custom user profile fields
 *
 * @param  obj $user The user object.
 *
 * @return void
 */
function ctxphc_custom_user_profile_fields( $user ) {
	?>
  <table class="form-table">
    <tr>
      <th>
        <label for="tc_location"><?php _e( 'Location' ); ?></label>
      </th>
      <td>
        <input type="text" name="tc_location" id="tc_location"
               value="<?php echo esc_attr( get_the_author_meta( 'tc_location', $user->ID ) ); ?>"
               class="regular-text"/>
        <br><span
            class="description"><?php _e( 'Your location.', 'travelcat' ); ?></span>
      </td>
    </tr>
    <tr>
      <th>
        <label for="tc_favorites"><?php _e( 'Favorites', 'travelcat' ); ?></label>
      </th>
      <td>
        <input type="text" name="tc_favorites" id="tc_favorites"
               value="<?php echo esc_attr( get_the_author_meta( 'tc_favorites', $user->ID ) ); ?>"
               class="regular-text"/>
        <br><span
            class="description"><?php _e( 'Can you share a few of your favorite places to be or to stay?', 'travelcat' ); ?></span>
        <br><span
            class="description"><?php _e( 'Separate by commas.', 'travelcat' ); ?></span>
      </td>
    </tr>
    <tr>
      <th>
        <label for="tc_travel_map"><?php _e( 'Travel map', 'travelcat' ); ?></label>
      </th>
      <td>
        <input type="text" name="tc_travel_map" id="tc_travel_map"
               value="<?php echo esc_attr( get_the_author_meta( 'tc_travel_map', $user->ID ) ); ?>"
               class="regular-text"/>
        <br><span
            class="description"><?php _e( 'Been there / Going there within a year / Wish list.', 'travelcat' ); ?></span>
        <br><span
            class="description"><?php _e( 'Separate by commas.', 'travelcat' ); ?></span>
      </td>
    </tr>
  </table>
	<?php
}

add_action( 'show_user_profile', 'ctxphc_custom_user_profile_fields' );
add_action( 'edit_user_profile', 'ctxphc_custom_user_profile_fields' );


function get_associated_members( $cur_user_id ) {
	global $wpdb;

	$assoc_memb_meta_query_args = array(
		'order'    => 'ASC',
		'order_by' => 'user_id',
		'relation' => 'OR', // Optional, defaults to "AND"

		array(
			'key'     => 'mb_id',
			'compare' => 'EXISTS',
		),
		array(
			'key'     => 'membUserID',
			'compare' => 'EXISTS',
		),
		array(
			'key'     => 'prim_memb_id',
			'compare' => 'EXISTS',
		),
		array(
			'key'     => 'user_id',
			'compare' => 'EXISTS',
		),
		array(
			'key'     => 'wp_user_id',
			'compare' => 'EXISTS',
		),
		array(
			'key'     => 'sp_id',
			'compare' => 'EXISTS',
		),
		array(
			'key'     => 'sp_wp_user_id',
			'compare' => 'EXISTS',
		),
		array(
			'key'     => 'c1_id',
			'compare' => 'EXISTS',
		),
		array(
			'key'     => 'c2_id',
			'compare' => 'EXISTS',
		),
		array(
			'key'     => 'c4_id',
			'compare' => 'EXISTS',
		),
		array(
			'key'     => 'c4_id',
			'compare' => 'EXISTS',
		),
		array(
			'key'     => 'fam_memb_id_1',
			'compare' => 'EXISTS',
		),
		array(
			'key'     => 'fam_memb_id_2',
			'compare' => 'EXISTS',
		),
		array(
			'key'     => 'fam_memb_id_3',
			'compare' => 'EXISTS',
		),
		array(
			'key'     => 'fam_memb_id_4',
			'compare' => 'EXISTS',
		),
		array(
			'key'     => 'membUserID',
			'compare' => 'EXISTS',
		),
		array(
			'key'     => 'prim_memb_id',
			'compare' => 'EXISTS',
		),
		array(
			'key'     => 'user_id',
			'compare' => 'EXISTS',
		),
		array(
			'key'     => 'wp_user_id',
			'compare' => 'EXISTS',
		),

	);

	$associated_ids = new WP_User_Query( $assoc_memb_meta_query_args );

	return $associated_ids;
}

function get_all_users_missing_membership_type() {

	$renewal_fields = array(
		'ID',
		'first_name',
		'last_name',
		'user_id',
		'meta_key',
		'meta_value',
	);

	$member_type_args = array(
		'order'    => 'ASC',
		'orderby'  => 'display_name',
		'relation' => 'OR', // Optional, defaults to "AND"
		array(
			'key'     => 'membership_type',
			'value'   => array( 1, 2, 3, 4, 'Single', 'Couple', 'Family', 'S', 'C', 'F', 'ID', 'IC', 'CO', 'HH' ),
			'compare' => 'NOT EXISTS',
			'fields'  => $renewal_fields,
		),

	);

	$memberships = new WP_User_Query( $member_type_args );

	return $memberships;
}