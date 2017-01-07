<?php
/**
 * Created by PhpStorm.
 * User: ken_kilgore1
 * Date: 1/5/2015
 * Time: 6:18 PM
 *
 * file: functions.php
 *
 * @param $renewing_member_id
 *
 * @return array
 */

function get_renewing_member_ids( $renewing_member_id ) {
	global $wpdb;

	$prim_member_id = get_user_meta( $renewing_member_id, 'prim_memb_id', true );
	if ( empty( $prim_member_id ) ) {
		$prim_member_id = $renewing_member_id;
	}
	$member_ids[ ] = $prim_member_id;
	$fam_memb_ids  = $wpdb->get_results( "SELECT meta_key, meta_value FROM ctxphcco_wp_db.ctxphc_usermeta WHERE meta_key LIKE 'fam_memb_id%' AND user_id = $prim_member_id" );
	foreach ( $fam_memb_ids as $fam_memb_id ) {
		$member_ids[ ] = intval( $fam_memb_id->meta_value );
	}

	return $member_ids;
}

function register_user_dashboard_styles() {
	wp_register_style( 'user-dashboard-style', plugins_url( 'user-dashboard-styles.css', __FILE__ ) );
	wp_enqueue_style( 'user-dashboard-style', plugins_url( 'user-dashboard-styles.css', __FILE__ ) );
}

// Hook into the 'wp_enqueue_scripts' action
add_action( 'wp_enqueue_scripts', 'register_user_dashboard_styles' );

//hook to use when enqueuing items that are meant to appear on the login page.
add_action( 'login_enqueue_scripts', 'register_user_dashboard_styles', 10 );


function create_memb_object( $objKey, $objVal ) {
	$memb          = new stdClass();
	$memb->$objKey = $objVal;
}

function renewal_update_processing( $new_query ) {
	settype( $mb_data, 'array' );
	settype( $sp_data, 'array' );
	settype( $c1_data, 'array' );
	settype( $c2_data, 'array' );
	settype( $c3_data, 'array' );
	settype( $c4_data, 'array' );

	$clean_post_data = ud_get_clean_post_data();

	foreach ( $clean_post_data as $key => $value ) {

		if ( preg_match( '/mb_|memb_/', $key ) == true ) {
			$mb_data[ $key ] = $value;
		}
		if ( preg_match( '/sp_/', $key ) == true ) {
			$sp_data[ $key ] = $value;
		}
		if ( preg_match( '/c1_/', $key ) == true ) {
			$c1_data[ $key ] = $value;
		}
		if ( preg_match( '/c2_/', $key ) == true ) {
			$c2_data[ $key ] = $value;
		}
		if ( preg_match( '/c3_/', $key ) == true ) {
			$c3_data[ $key ] = $value;
		}
		if ( preg_match( '/c4_/', $key ) == true ) {
			$c4_data[ $key ] = $value;
		}
	}


	foreach ( $mb_data as $meta_key => $meta_value ) {
		$prev_meta_value = get_user_meta( $mb_data[ 'mb_id' ], $meta_key, true );
		if ( ! empty( $meta_value ) && ! empty( $mb_data[ 'mb_id' ] ) ) {
			if ( 'memb_type' !== $meta_key ) {
				if ( 'mb_id' !== $meta_key ) {
					$meta_key = str_replace( 'mb_', '', $meta_key );
				}
			}
			$result = update_user_meta( $mb_data[ 'mb_id' ], $meta_key, $meta_value, $prev_meta_value );
		}
	}

	if ( isset( $sp_data ) ) {
		foreach ( $sp_data as $meta_key => $meta_value ) {
			if ( ! empty( $meta_value ) && ! empty( $sp_data[ 'sp_id' ] ) ) {
				if ( 'sp_id' !== $meta_key ) {
					$meta_key = str_replace( 'sp_', '', $meta_key );
				}
				$result   = update_user_meta( $sp_data[ 'sp_id' ], $meta_key, $meta_value );
			}
		}
	}

	if ( isset( $c1_data ) ) {
		foreach ( $c1_data as $meta_key => $meta_value ) {
			if ( ! empty( $meta_value ) && ! empty( $c1_data[ 'c1_id ' ] ) ) {
				if ( 'c1_id' !== $meta_key ) {
					$meta_key = str_replace( 'c1_', '', $meta_key );
				}
				$result   = update_user_meta( $c1_data[ 'c1_id' ], $meta_key, $meta_value );
			}
		}
	}

	if ( isset( $c2_data ) ) {
		foreach ( $c2_data as $meta_key => $meta_value ) {
			if ( ! empty( $meta_value ) && ! empty( $c2_data[ 'c2_id' ] ) ) {
				if ( 'c1_id' !== $meta_key ) {
					$meta_key = str_replace( 'c2_', '', $meta_key );
				}
				$result   = update_user_meta( $c2_data[ 'c2_id' ], $meta_key, $meta_value );
			}
		}
	}

	if ( isset( $c3_data ) ) {
		foreach ( $c3_data as $meta_key => $meta_value ) {
			if ( ! empty( $meta_value ) && ! empty( $c3_data[ 'c3_id' ] ) ) {
				if ( 'c1_id' !== $meta_key ) {
					$meta_key = str_replace( 'c3_', '', $meta_key );
				}
				$result   = update_user_meta( $c3_data[ 'c3_id' ], $meta_key, $meta_value );
			}
		}
	}

	if ( isset( $c4_data ) ) {
		foreach ( $c4_data as $meta_key => $meta_value ) {
			if ( ! empty( $meta_value ) && ! empty( $c4_data[ 'c4_id' ] ) ) {
				if ( 'c1_id' !== $meta_key ) {
					$meta_key = str_replace( 'c4_', '', $meta_key );
				}
				$result   = update_user_meta( $c4_data[ 'c4_id' ], $meta_key, $meta_value );
			}
		}
	}

}

function renewal_complete_processing() {
	$email      = esc_sql( $_REQUEST[ 'email' ] );
	$first_name = esc_sql( $_REQUEST[ 'first_name' ] );
	$last_name  = esc_sql( $_REQUEST[ 'last_name' ] );

	$from    = get_option( 'admin_email' );
	$headers = 'From: ' . $from . "\r\n";
	$subject = "Successful Membership Renewal";
	$msg     = "<h3>Hello $first_name $last_name</h3>";
	$msg .= "<p>Your membership renewal was successful!</p>";
	$msg .= "<p>Thank you for renewing you <strong>Central Texas Parrothead Club Membership</strong>!";
	$msg .= "You can check out our upcomming events on our calendar.";
	$msg .= "<br><br>Thank you,<br>CTXPHC Membership Director";

	wp_mail( $email, $subject, $msg, $headers );
	echo "A renewal verification email has been sent to .";
}

function ud_get_clean_post_data() {
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
						$clean_numb_grps[ ] = intval( $numbers );
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