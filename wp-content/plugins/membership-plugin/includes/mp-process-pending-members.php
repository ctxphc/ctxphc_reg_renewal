<?php
/**
 * Created by PhpStorm.
 * User: ken_kilgore1
 * Date: 2/21/2015
 * Time: 3:55 PM
 */

global $wpdb, $memb_error, $primary_memb_id;

$memb_error = new WP_Error();

$pending_members = $wpdb->get_results( 'SELECT * FROM ctxphc_pending_registrations' );

foreach ( $pending_members as $pending_member ) {

	$wp_user_query_args = array(
		'search'         => $pending_member->email,
		'search_columns' => array( 'user_email' ),
		'fields'         => 'all_with_meta',
	);

	$user_query = new WP_User_Query( $wp_user_query_args );

	if ( 1 > $user_query->total_users ) {
		foreach ( $pending_member as $pending_key => $pending_value ) {
			switch ( $pending_key ) {
				case strpos( $pending_key, 'sp_' ):
					if ( ! empty( $pending_value ) ) {
						$spouse_data[ $pending_key ] = $pending_value;
					};
					break;
				case strpos( $pending_key, 'c1_' ):
					if ( ! empty( $pending_value ) ) {
						$child1_data[ $pending_key ] = $pending_value;
					};
					break;
				case strpos( $pending_key, 'c2_' ):
					if ( ! empty( $pending_value ) ) {
						$child2_data[ $pending_key ] = $pending_value;
					};
					break;
				case strpos( $pending_key, 'c3_' ):
					if ( ! empty( $pending_value ) ) {
						$child3_data[ $pending_key ] = $pending_value;
					};
					break;
				case strpos( $pending_key, 'c4_' ):
					if ( ! empty( $pending_value ) ) {
						$child4_data[ $pending_key ] = $pending_value;
					};
					break;
				default:
					if ( ! empty( $pending_value ) ) {
						$member_data[ $pending_key ] = $pending_value;
					}
					break;
			}
		}

		if ( is_array( $member_data ) && ! empty( $member_data ) ) {
			$memb_id         = add_pending_member( $member_data );
			$primary_memb_id = $memb_id;
		}

		if ( is_array( $spouse_data ) && ! empty( $spouse_data ) ) {
			$memb_id = add_pending_member( $spouse_data );
			if ( ! is_wp_error( $memb_id ) ) {
				unset( $spouse_data );
			}
		}

		if ( is_array( $child1_data ) && ! empty( $child1_data ) ) {
			$memb_id = add_pending_member( $child1_data );
			if ( ! is_wp_error( $memb_id ) ) {
				unset( $child1_data );
			}
		}

		if ( is_array( $child2_data ) && ! empty( $child2_data ) ) {
			$memb_id = add_pending_member( $child2_data );
			if ( ! is_wp_error( $memb_id ) ) {
				unset( $child2_data );
			}
		}

		if ( is_array( $child3_data ) && ! empty( $child3_data ) ) {
			$memb_id = add_pending_member( $child3_data );
			if ( ! is_wp_error( $memb_id ) ) {
				unset( $child3_data );
			}
		}

		if ( is_array( $child4_data ) && ! empty( $child4_data ) ) {

			$memb_id = add_pending_member( $child4_data );
			if ( ! is_wp_error( $memb_id ) ) {
				unset( $child4_data );
			}
		}

		if ( ! is_wp_error( $memb_id ) ) {
			$users[ $memb_id ] = get_userdata( $memb_id );
		}
	}
}

function fix_relationship_status( $pending_value ) {
	switch ( $pending_value ) {
		case 'S':
			$new_pending_value = 3;
			break;
		case 'C':
			$new_pending_value = 4;
			break;
		case 'O':
			$new_pending_value = 5;
			break;
	}

	return $new_pending_value;
}

/**
 * @param $membdata
 *
 * @return int|string
 */
function add_wp_user( $member ) {
	global $memb_error;

	foreach ( $member as $mkey => $mvalue ) {
		$mkey            = substr( $mkey, 3 );
		$member[ $mkey ] = $mvalue;
	}

	if ( empty( $member[ 'email' ] ) ) { //Member does not have an email address and will not get a Wordpress User account.  There data will be combined with the Member's record that filled out the registration form.
		$memb_error->add( 'no_email', 'Without an email address we cannot create a wordpress user account.' );
		$memb_id = $memb_error;
	} else { //Member will get a Wordpress User account.
		$userdata = array(
			'first_name'      => $member[ 'first_name' ],
			'last_name'       => $member[ 'last_name' ],
			'user_email'      => $member[ 'email' ],
			'user_login'      => mb_strtolower( substr( $member[ 'first_name' ], 0, 3 ) . substr( $member[ 'last_name' ], 0, 4 ) ),
			'user_pass'       => wp_generate_password( $length = 12, $include_standard_special_chars = false ),
			'nickname'        => $member[ 'first_name' ] . ' ' . $member[ 'last_name' ],
			'display_name'    => $member[ 'first_name' ] . ' ' . $member[ 'last_name' ],
			'user_nicename'   => $member[ 'first_name' ] . ' ' . $member[ 'last_name' ],
			'user_registered' => $member[ 'reg_date' ],
		);

		$memb_id = wp_insert_user( $userdata );
	}

	return $memb_id;
}


/**
 * @param $data
 */
function add_pending_member( $data ) {
	global $primary_memb_id;


	$memb_id = add_wp_user( $data );

	if ( is_wp_error( $memb_id ) ) {
		$memb_id = $primary_memb_id;
	} else {
		foreach ( $data as $dkey => $dvalue ) {
			$old_dkey = $dkey;
			$new_dkey = preg_replace( "/(sp_|c1_|c2_|c3_|c4_)?(.*)/", "$2", $dkey );
			( 'relationship' == $new_dkey ? $dvalue = fix_relationship_status( $dvalue ) : $dvalue );
			$dkey = $new_dkey;
			$data[ $dkey ] = $dvalue;

			if ( $old_dkey != $new_dkey ) {
				unset( $data[ $old_dkey ] );
			}
		}

	}

	foreach ( $data as $key => $value ) {
		$result = update_user_meta( $memb_id, $key, $value );
	}

	return $memb_id;
}

/**
 * **********************************
 * Display the results of inserting each pending member.
 * **********************************
 */
function display_insert_pending_members( $user_recs ) {
	foreach ( $user_recs as $user ) {
		echo "<li>Inserted: {$user->first_name}  {$user->last_name}, {$user->email} </li>";
	}
}

?>
<div class="wrap">
	<h2>
		<?php
		echo 'Inserted Pending Members';
		?>
	</h2>

	<form method="post">
		<input type="hidden" name="page" value="insert_pending_members">

		<div>
			<h3>Inserted Pending Members</h3>
			<ul>
				<?php
				display_insert_pending_members( $users );
				?>
			</ul>
		</div>
	</form>
</div>