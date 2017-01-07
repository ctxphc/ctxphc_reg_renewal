<?php
/**
 * Created by PhpStorm.
 * User: ken_kilgore1
 * Date: 3/29/2015
 * Time: 1:23 PM
 */

class cm_membership_renewals {


	/**
	 * @param $renewing_member_id
	 *
	 * @return mixed
	 */
	function get_renewing_member_data( $renewing_member_id ) {

		// get primary members info if renewing member is not the primary member.
		$prim_memb_data          = get_primary_member_data( $renewing_member_id );
		$meta_data_array[ 'mb' ] = $prim_memb_data;
		$family_member_ids       = get_family_member_ids( $prim_memb_data[ 'member_wp_user_id' ] );

		foreach ( $family_member_ids as $memb_id ) {
			$fam_memb_id_key                      = $memb_id->meta_key;
			$fam_memb_id                          = $memb_id->meta_value;
			$fam_members_data[ $fam_memb_id_key ] = get_clean_usermeta_data( $fam_memb_id );
		}

		$child_count = 0;
		foreach ( $fam_members_data as $fam_meta_data ) {
			if ( 2 == $fam_meta_data[ 'member_relationship_id' ] || 3 == $fam_meta_data[ 'member_relationship_id' ] ) {
				$meta_data_array[ 'sp' ] = $fam_meta_data;
			} else {
				$child_count ++;
				$meta_data_array[ 'c' . $child_count ] = $fam_meta_data;
			}
		}

		return $meta_data_array;
	}

	/**
	 * @param $renew_memb_id
	 *
	 * @return mixed
	 */
	function get_renewing_member_relationship( $renew_memb_id ) {
		return get_user_meta( $renew_memb_id, 'relationship_id', true );
	}

	/**
	 * @param $renewing_id
	 *
	 * @return array
	 */
	function get_primary_member_data( $renewing_id ) {
		$prim_member_id = get_user_meta( $renewing_id, 'member_prim_memb_id', true );
		if ( $prim_member_id ) {
			$prim_member_metadata = array_map( function ( $a ) {
				return $a[ 0 ];
			}, get_user_meta( $prim_member_id ) );
		} else {
			$prim_member_metadata = array_map( function ( $a ) {
				return $a[ 0 ];
			}, get_user_meta( $renewing_id ) );
		}

		return $prim_member_metadata;
	}

	function register_user_dashboard_styles() {
		wp_register_style( 'user-dashboard-style', plugins_url( 'user-dashboard-styles.css', __FILE__ ), false );
		wp_enqueue_style( 'user-dashboard-style' );
	}

// Hook into the 'wp_enqueue_scripts' action
//add_action( 'wp_enqueue_scripts', 'register_user_dashboard_styles' );

//hook to use when enqueuing items that are meant to appear on the login page.
//add_action( 'login_enqueue_scripts', 'register_user_dashboard_styles', 10 );


	function create_memb_object( $objKey, $objVal ) {
		$memb          = new stdClass();
		$memb->$objKey = $objVal;
	}


}