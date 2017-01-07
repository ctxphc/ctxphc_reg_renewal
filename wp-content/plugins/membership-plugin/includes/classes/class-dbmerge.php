<?php


/**
 * Class dbMerge
 * @property  string renewal_date
 * @property  array member_data
 * @property  string hatch_date
 * @property  string email
 * @property  string username
 * @property  string first_name
 * @property  string last_name
 * @property  string password
 * @property  string birthday
 * @property  string bday_month
 * @property  string bday_day
 * @property  int prim_memb_id
 * @property  mixed existing_member
 * @property  mixed family_rows
 * @property  bool address
 */
class dbMerge {
	public $address_id;
	public $address_table;
	public $family_table;
	public $spouse_table;
	public $ctxphc_member_array;
	public $ctxphc_member_address_array;
	public $ctxphc_member;
	public $member_table;
	public $user_id;
	public $membership_id;
	public $family_row;
	public $family_rows;
	public $prev_memb_id;
	public $fam_process;
	public $prev_wp_user_id;
	public $fam_count;
	public $prim_memb_wp_user_id;
	public $completed_count;
	public $completed;

	/**
	 * **********************************
	 * Initialize variables
	 * **********************************
	 *
	 * @param $args
	 */
	function __construct( $debug ) {
		$this->debug    = $debug;
		$this->messages = array();
		//$this->completed_count = 0;
		$this->family_table = 'ctxphc_ctxphc_family_members';
		$this->spouse_table = 'ctxphc_ctxphc_memb_spouses';
	}

	function verify_family_data() {
		//todo: create verification process for hatch date, renewal date, memb_id and wp_user_id if they have an email account
	}

	/**
	 * **********************************
	 * Make sure all Membership types
	 * are corrected for new membership
	 * types.
	 * **********************************
	 *
	 * @param $existing_memb_type
	 *
	 * @return string
	 */
	private function fix_membership_type( $existing_memb_type ) {
		switch ( $existing_memb_type ) {
			case 'Single':
			case 1:
				$new_memb_type = 1;
				break;
			case 'Couple':
			case 2:
				$new_memb_type = 3;
				break;
			case 'Family':
			case 3:
				$new_memb_type = 4;
				break;
		}

		/** @var INTEGER $new_memb_type */

		return $new_memb_type;
	}

	/**
	 * **********************************
	 * Make sure all Relationship types
	 * are corrected for new relationship
	 * types.
	 * **********************************
	 *
	 * @param $rel
	 *
	 * @return string
	 */
	private function fix_relationship_type( $rel ) {
		switch ( $rel ) {
			case 1:
				$rel_id = 2;
				break;
			case 2:
				$rel_id = 3;
				break;
			case 3:
				$rel_id = 4;
				break;
			case 4:
				$rel_id = 5;
				break;
			case 0:
				$rel_id = 1;
				break;
			case 'P':
				$rel_id = 3;
				break;
			case 'C':
				$rel_id = 4;
				break;
			case 'O':
				$rel_id = 5;
				break;
			default:
				$rel_id = 1;
				break;
		}

		/** @noinspection PhpUndefinedVariableInspection */

		return $rel_id;
	}

	/**
	 * **********************************
	 * Make sure all birthdays
	 * include leading zeros for single digit
	 * days and months.
	 * **********************************
	 *
	 * @param $bday_month
	 * @param $bday_day
	 *
	 * @return string
	 */
	private function fix_birthdays( $bday_month, $bday_day ) {
		if ( 0 == $bday_month || 0 == $bday_day ) {
			$bday = null;
		} else {
			$bday = sprintf( '%02s/%02s', $bday_month, $bday_day );
		}

		return $bday;
	}


	function fix_bday_values( $bday_val ) {
		if ( 0 === $bday_val ) {
			$bday_val = null;
		} else {
			$bday_val = sprintf( '%02s', $bday_val );
		}

		return $bday_val;
	}


	/**
	 * *********************************
	 * Determine Renewal Date based on Hatch Date
	 * if Hatch Date year is the same as current year
	 * and Hatch Date month is 9 or higher membership
	 * is good through to the next years renewal time.
	 * ********************************
	 */
	function get_renewal_date() {
		$curr_month   = date( 'm' );
		$curr_year    = date( 'Y' );
		$renewal_year = $curr_year + 1;
		$hatchdate    = new DateTime( $this->member_data[ 'hatch_date' ] );
		if ( $hatchdate ) {
			$hatch_month = date_format( $hatchdate, 'm' );
			$hatch_year  = date_format( $hatchdate, 'Y' );
		} else {
			$hatch_month = $curr_month;
			$hatch_year  = $curr_year;
		}

		if ( $hatch_month > 8 && $hatch_year == $curr_year ) {
			$this->renewal_date = sprintf( '%s-%02s-%02s', $renewal_year + 1, 01, 01 );
		} else {
			$this->renewal_date = sprintf( '%s-%02s-%02s', $renewal_year, 01, 01 );
		}

		return $this->renewal_date;
	}

	/**
	 * ******************************************
	 * * Prepare member data from previous membership tables
	 * * for inserting into the new membership table.
	 * ******************************************
	 *
	 * @param $memb_data
	 */
	function validate_existing_data( $memb_data ) {
		foreach ( $memb_data as $mkey => $mvalue ) {
			switch ( $mkey ) {
				case 'first_name':
					$clean_value                = sanitize_text_field( $mvalue );
					$this->member_data[ $mkey ] = $clean_value;
					break;
				case 'last_name':
					$clean_value                = sanitize_text_field( $mvalue );
					$this->member_data[ $mkey ] = $clean_value;
					break;
				case 'email':
					$safe_email                 = sanitize_email( $mvalue );
					$this->member_data[ $mkey ] = $safe_email;
					break;
				case 'phone':
					$this->member_data[ $mkey ] = $mvalue;
					break;
				case 'bday':
					$mkey                       = 'birthday';
					$this->member_data[ $mkey ] = $mvalue;
					break;
				case 'occupation':
					$mkey                       = 'occupation';
					$clean_value                = sanitize_text_field( $mvalue );
					$this->member_data[ $mkey ] = $clean_value;
					break;
				case 'addr1':
					$clean_value                = sanitize_text_field( $mvalue );
					$this->member_data[ $mkey ] = $clean_value;
					break;
				case 'addr2':
					$clean_value                = sanitize_text_field( $mvalue );
					$this->member_data[ $mkey ] = $clean_value;
					break;
				case 'city':
					$clean_value                = sanitize_text_field( $mvalue );
					$this->member_data[ $mkey ] = $clean_value;
					break;
				case 'state':
					$this->member_data[ $mkey ] = $mvalue;
					break;
				case 'zip':
					$clean_value                = sanitize_text_field( $mvalue );
					$this->member_data[ $mkey ] = $clean_value;
					break;
				case 'relationship_id':
					$mvalue                     = $this->fix_relationship_type( $mvalue );
					$mkey                       = 'relationship_id';
					$this->member_data[ $mkey ] = $mvalue;
					break;
				case 'tag_date':
					$mkey                       = 'tag_date';
					$this->member_data[ $mkey ] = $mvalue;
					break;
				case 'hatch_date':
					$mkey                       = 'hatch_date';
					$this->member_data[ $mkey ] = $mvalue;
					break;
				case 'initiated_date':
					$mkey                       = 'initiated_date';
					$this->member_data[ $mkey ] = $mvalue;
					break;
				case 'renewal_date':
					$mkey                       = 'renewal_date';
					$mvalue                     = $this->get_renewal_date( $this->member_data[ 'hatch_date' ] );
					$this->member_data[ $mkey ] = $mvalue;
					break;
			}
		}

		if ( empty( $this->username ) || ! $this->member_data[ 'username' ] ) {
			if ( $this->member_data[ 'email' ] ) {
				$this->username                  = mb_strtolower( substr( $this->member_data[ 'first_name' ], 0, 3 ) . substr( $this->member_data[ 'last_name' ], 0, 4 ) );
				$this->member_data[ 'username' ] = $this->username;

				$this->password = wp_generate_password( $length = 12, $include_standard_special_chars = false );
			}
		}
	} // end of member preparations


	/**
	 * ************************************
	 * If membership type is Couple or Family
	 * Retrieve record(s) to be inserted into
	 * users metadata.
	 * ************************************
	 *
	 * @param $f_table
	 *
	 * @return bool
	 * @internal param string $prim_memb_id
	 */
	function get_family_data( $f_table ) {
		/** @var wpdb $wpdb */
		global $wpdb;
		$this->family_rows = $wpdb->get_results( "SELECT * FROM $f_table WHERE memb_id = {$this->prev_memb_id}" );

		if ( null == $this->family_rows ) {

			$this->family_rows = $wpdb->get_results( "SELECT * FROM $f_table WHERE memb_id = {$this->prev_wp_user_id}" );

			if ( null == $this->family_rows ) {
				$this->fam_process = false;
			} else {
				$this->fam_process = true;
			}
		} else {
			$this->fam_process = true;
		}
	}

	/**
	 *  * ************************************
	 * Process the family member's data
	 * inserting it into the new
	 * membership table.
	 * ************************************
	 *
	 * @param $family_data
	 */
	function process_family_record( $family_data ) {
		$this->validate_existing_data( $family_data );
		//$this->member_data[ 'address_id' ] = $this->address_id;
		$this->create_wp_user_account();
		$this->insert_existing_members();
		$this->clear_processed_data();
	}

	/**
	 * **********************************
	 * Create Wordpress User for each
	 * member that has an email address.
	 * *********************************
	 *
	 * @param null $email
	 *
	 * @return int
	 */
	/** @noinspection PhpInconsistentReturnPointsInspection
	 * @param null $email
	 *
	 * @return int
	 */
	function create_wp_user_account( $email = null ) {
		if ( isset( $email ) ) {
			$user_id = get_user_by( 'email', $email );
			if ( ! $user_id and email_exists( $email ) == false ) {
				$username = mb_strtolower( substr( $this->member_data[ 'first_name' ], 0, 3 ) . substr( $this->member_data[ 'last_name' ], 0, 4 ) );
				$password = wp_generate_password( $length = 12, $include_standard_special_chars = false );
				$user_id  = wp_create_user( $username, $password, $email );
			}
		} elseif ( isset( $this->member_data[ 'username' ] ) ) {
			$user_id = username_exists( $this->member_data[ 'username' ] );
			if ( ! $user_id && email_exists( $this->member_data[ 'email' ] ) == false ) {
				$this->username = mb_strtolower( substr( $this->member_data[ 'first_name' ], 0, 3 ) . substr( $this->member_data[ 'last_name' ], 0, 4 ) );
				if ( isset( $this->password ) && ( $this->password == 'Password' || $this->password == 'password' || empty( $this->password ) ) ) {
					$this->password = wp_generate_password( $length = 12, $include_standard_special_chars = false );
					$user_id        = wp_create_user( $this->username, $this->password, $this->member_data[ 'email' ] );
				} else {
					$user_id = wp_create_user( $this->username, $this->password, $this->member_data[ 'email' ] );
				}
			}
		} elseif ( isset( $this->member_data[ 'email' ] ) ) {
			$user_id = get_user_by( 'email', $this->member_data[ 'email' ] );
			if ( ! $user_id && email_exists( $this->member_data[ 'email' ] ) == false ) {
				$this->username = mb_strtolower( substr( $this->member_data[ 'first_name' ], 0, 3 ) . substr( $this->member_data[ 'last_name' ], 0, 4 ) );
				if ( isset( $this->password ) && ( $this->password == 'Password' || $this->password == 'password' || empty( $this->password ) ) ) {
					$this->password = wp_generate_password( $length = 12, $include_standard_special_chars = false );
					$user_id        = wp_create_user( $this->username, $this->password, $this->member_data[ 'email' ] );
				}
			}
		} else {
			$user_id = $this->prim_memb_wp_user_id;
		}

		if ( is_wp_error( $user_id ) ) {
			$message = "There was an error when adding the member to Wordpress:  $user_id->get_error_message()";
			debug_log_message( $message, $this->debug );
		} else {
			$this->member_data[ 'wp_user_id' ] = $this->user_id = $user_id;
		}
	}


	/**
	 * **********************************
	 * Insert Member Record for each primary
	 * and family member.
	 * **********************************
	 *
	 * @internal param $wp_user_id
	 */
	function insert_existing_members() {
		if ( $this->member_data[ 'wp_user_id' ] ) {  //member had a wordpress username.
			$wp_user_id = $this->member_data[ 'wp_user_id' ];

			if ( 1 == $this->member_data[ 'relationship_id' ] ) { //this is the primary member's data.
				$this->prim_memb_wp_user_id = $wp_user_id = $this->member_data[ 'wp_user_id' ]; //store primary members id for later use.
			} else {
				$this->member_data[ 'fam_memb_ids' ] = $wp_user_id;
			}

			//$this->add_to_users_metadata( $wp_user_id );
			$this->add_member_metadata( $wp_user_id ); //add or update meta data.
		} else {  //member is a spouse, child or other and did not have an email address to create a wordpress user account with.
			//We will have to add this members data to the primary members metadata using the members
			$this->member_data[ 'fam_memb_ids' ] = $this->prim_memb_wp_user_id;
			$this->add_to_primary_members_metadata( $this->prim_memb_wp_user_id );
		}
	}

	/**
	 * @param $user_id
	 */
	private function add_member_metadata( $user_id ) {

		foreach ( $this->member_data as $mkey => $mvalue ) {

			if ( 'fam_memb_ids' == $mkey ) {  //meta key needs to except multiple values.
				add_user_meta( $this->prim_memb_wp_user_id, $mkey, $mvalue, false );
			} else {  //will create a unique key or update existing key
				update_user_meta( $user_id, $mkey, $mvalue, true );
			}
		}
	}

	private function add_to_primary_members_metadata( $user_id ) {

		if ( 2 == $this->member_data[ 'relationship_id' ] && $this->prim_memb_wp_user_id === $user_id ) {
			foreach ( $this->member_data as $memb_key => $memb_value ) {
				$meta_key   = 'sp_';
				$meta_key   = $meta_key . $memb_key;
				$meta_value = $memb_value;
				update_user_meta( $user_id, $meta_key, $meta_value );
			}
		} elseif ( 3 <= $this->member_data[ 'relationship_id' ] && $this->prim_memb_wp_user_id == $user_id ) {
			$this->fam_count ++;
			foreach ( $this->member_data as $memb_key => $memb_value ) {
				$meta_key   = 'fam_' . $this->fam_count . '_';
				$meta_key   = $meta_key . $memb_key;
				$meta_value = $memb_value;
				update_user_meta( $user_id, $meta_key, $meta_value );
			}
		}

		// Verify the stored value matches $new_value
		if ( get_user_meta( $user_id, $meta_key, true ) != $meta_value ) {
			$message = "There was a failure updating the user_metadata: $user_id, $meta_key, $meta_value";
			debug_log_message( $message, $this->debug );
		}
	}


	function clear_processed_data() {
		//$this->completed_counter ++;
		$this->completed[ ] = $this->member_data;
		unset( $this->member_data );
		unset( $this->username );
		unset( $this->password );
	}

	function reset_counters() {
		$this->fam_memb_id_counter = 0;
		//$this->completed_counter   = 0;
		$this->fam_count = 0;
	}

	/**
	 * **********************************
	 * This is the main control module
	 * for processing all previous
	 * data from the old table and
	 * inserting it into the new table.
	 * **********************************
	 *
	 * @param $member_record
	 */
	function prepare_members( $member_record ) {
		$this->reset_counters();
		unset( $this->address_id );

		$this->validate_existing_data( $member_record );
		if ( ! isset( $this->member_data[ 'relationship_id' ] ) ) {
			$this->member_data[ 'relationship_id' ] = 1;
		}

		$this->create_wp_user_account();
		$this->insert_existing_members();
		$this->clear_processed_data();

		$this->prev_memb_id    = $member_record->memb_id;
		$this->prev_wp_user_id = $this->user_id;

		if ( $this->membership_id >= 2 ) {
			$this->fam_count           = 0;
			$this->fam_memb_id_counter = 0;
			$this->get_family_data( $this->spouse_table );
			if ( $this->fam_process ) {

				foreach ( $this->family_rows as $family_row ) {
					$this->process_family_record( $family_row );
				}
			}

			if ( $this->membership_id == 4 ) {
				$this->get_family_data( $this->family_table );
				if ( $this->fam_process ) {
					foreach ( $this->family_rows as $family_row ) {
						$this->process_family_record( $family_row );
					}
				}
			}
		}
	}


	/**
	 * **********************************
	 * Display the results of the merged members data.
	 * **********************************
	 */
	function display() {
		echo "<h3> Completed:</h3><ul>";

		foreach ( $this->completed as $completed ) {
			echo "<li>processing record for: {$completed['first_name']}  {$completed['last_name']}, {$completed['email']} </li>";
		}
		echo '</ul>';
		//$this->completed_count = 0;
		unset( $this->completed );
	}

	function __destruct() {
	}
}