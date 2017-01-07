<?php

/**
 * Retrieve member info by member ID.
 *
 * @since 1.1.1
 *
 * @param int $member_id Member ID
 *
 * @return MP_Member|bool MP_Member object on success, false on failure.
 */
function get_memberdata( $user_id ) {
	return get_member_by( 'id', $user_id );
}


/**
 * Retrieve member info by a given field
 *
 * @since 1.1.1
 *
 * @param string $field The field to retrieve the member with. id | slug | email | login
 * @param int|string $value A value for $field. A member ID, slug, email address, or login name.
 *
 * @return MP_Member|bool MP_Member object on success, false on failure.
 */
function get_member_by( $field, $value ) {
	$memberdata = MP_Member::get_mp_data_by( $field, $value );

	if ( ! $memberdata ) {
		return false;
	}

	$member = new MP_Member;
	$member->init( $memberdata );

	return $member;
}


/**
 * MembershipPlugin Member class.
 *
 * @since 1.1.1
 * @package MembershipPlugin
 * @subpackage Members
 *
 */

use stdClass;

class MP_Member {
	/**
	 * Member data container.
	 *
	 * @since 1.1.1
	 * @access private
	 * @var array
	 */
	var $data;

	/**
	 * The member's ID.
	 *
	 * @since 1.1.1
	 * @access public
	 * @var int
	 */
	public $ID = 0;

	/**
	 * The available member status options.
	 *
	 * @since 1.1.1
	 * @access public
	 * @var array
	 */
	public $status = array();

	/**
	 * User metadata status_id.
	 *
	 * @since 1.1.1
	 * @access public
	 * @var string
	 */
	public $status_key;


	/**
	 * The filter context applied to member data fields.
	 *
	 * @since 1.1.1
	 * @access private
	 * @var string
	 */
	var $filter = null;

	private static $back_compat_keys;

	/**
	 * Constructor
	 *
	 * Retrieves the memberdata and passes it to {@link MP_Member::init()}.
	 *
	 * @since 1.1.1
	 * @access public
	 *
	 * @param int|string|stdClass|MP_Member $id Member's ID, a MP_Member object, or a member object from the DB.
	 * @param string $name Optional. Member's username
	 *
	 * @return MP_Member
	 */
	public function __construct( $id = 0, $name = '' ) {
		if ( ! isset( self::$back_compat_keys ) ) {
			self::$back_compat_keys = array(
				'user_firstname'   => 'first_name',
				'user_lastname'    => 'last_name',
				'user_description' => 'description',
			);
		}

		if ( is_a( $id, 'MP_Member' ) ) {
			$this->init( $id->data );

			return;
		} elseif ( is_object( $id ) ) {
			$this->init( $id );

			return;
		}

		if ( ! empty( $id ) && ! is_numeric( $id ) ) {
			$name = $id;
			$id   = 0;
		}

		if ( $id ) {
			$data = self::get_mp_data_by( 'id', $id );
		} else {
			$data = self::get_mp_data_by( 'login', $name );
		}

		if ( $data ) {
			$this->init( $data );
		}
	}

	/**
	 * Sets up object properties, including status.
	 *
	 * @param object $data Member DB row object
	 */
	public function init( $data ) {
		$this->data = $data;
		$this->ID   = (int) $data->ID;
	}

	/**
	 * Return only the main member fields
	 *
	 * @since 1.1.1
	 *
	 * @param string $field The field to query against: 'id', 'slug', 'email' or 'login'
	 * @param string|int $value The field value
	 *
	 * @return object Raw member object
	 */
	public static function get_mp_data_by( $field, $value ) {
		global $wpdb;

		if ( 'id' == $field ) {
			// Make sure the value is numeric to avoid casting objects, for example,
			// to int 1.
			if ( ! is_numeric( $value ) ) {
				return false;
			}
			$value = intval( $value );
			if ( $value < 1 ) {
				return false;
			}
		} else {
			$value = trim( $value );
		}

		if ( ! $value ) {
			return false;
		}

		switch ( $field ) {
			case 'id':
				$user_id  = $value;
				$db_field = 'ID';
				break;
			case 'slug':
				$user_id  = wp_cache_get( $value, 'userslugs' );
				$db_field = 'user_nicename';
				break;
			case 'email':
				$user_id  = wp_cache_get( $value, 'useremail' );
				$db_field = 'user_email';
				break;
			case 'login':
				$value    = sanitize_user( $value );
				$user_id  = wp_cache_get( $value, 'userlogins' );
				$db_field = 'user_login';
				break;
			default:
				return false;
		}

		if ( false !== $user_id ) {
			if ( $member = wp_cache_get( $user_id, 'users' ) ) {
				return $member;
			}
		}

		if ( ! $member = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $wpdb->users WHERE $db_field = %s", $value ) ) ) {
			return false;
		}

		update_user_caches( $member );

		return $member;
	}

	/**
	 * Magic method for checking the existence of a certain custom field
	 *
	 * @since 1.1.1
	 *
	 * @param string $key
	 *
	 * @return bool
	 */
	public function __isset( $key ) {
		if ( isset( $this->data->$key ) ) {
			return true;
		}

		if ( isset( self::$back_compat_keys[ $key ] ) ) {
			$key = self::$back_compat_keys[ $key ];
		}

		return metadata_exists( 'user', $this->ID, $key );
	}

	/**
	 * Magic method for accessing custom fields
	 *
	 * @since 1.1.1
	 *
	 * @param string $key
	 *
	 * @return mixed
	 */
	public function __get( $key ) {
		if ( isset( $this->data->$key ) ) {
			$value = $this->data->$key;
		} else {
			if ( isset( self::$back_compat_keys[ $key ] ) ) {
				$key = self::$back_compat_keys[ $key ];
			}
			$value = get_user_meta( $this->ID, $key, true );
		}

		if ( $this->filter ) {
			$value = sanitize_user_field( $key, $value, $this->ID, $this->filter );
		}

		return $value;
	}

	/**
	 * Magic method for setting custom fields
	 *
	 * @since 1.1.1
	 *
	 * @param $key
	 * @param $value
	 */
	public function __set( $key, $value ) {
		$this->data->$key = $value;
	}

	/**
	 * Determine whether the member exists in the database.
	 *
	 * @since 1.1.1
	 * @access public
	 *
	 * @return bool True if member exists in the database, false if not.
	 */
	public function exists() {
		return ! empty( $this->ID );
	}

	/**
	 * Retrieve the value of a property or meta key.
	 *
	 * Retrieves from the users and usermeta table.
	 *
	 * @since 1.1.1
	 *
	 * @param string $key Property
	 *
	 * @return mixed
	 */
	public function get( $key ) {
		return $this->__get( $key );
	}

	/**
	 * Determine whether a property or meta key is set
	 *
	 * Consults the users and usermeta tables.
	 *
	 * @since 1.1.1
	 *
	 * @param string $key Property
	 *
	 * @return bool
	 */
	public function has_prop( $key ) {
		return $this->__isset( $key );
	}

	/**
	 * Return an array representation.
	 *
	 * @since 1.1.1
	 *
	 * @return array Array representation.
	 */
	public function to_array() {
		return get_object_vars( $this->data );
	}


	/**
	 * Set the status of the member.
	 *
	 * This will update the previous status of the member and assign the member the
	 * new one.
	 *
	 * @since 1.1.1
	 * @access public
	 *
	 * @param string $status Role name.
	 */
	public function set_status( $status ) {

		$old_status = $this->status_id;
		if ( ! empty( $status ) ) {
			$this->status_id = $status;
		} else {
			$this->status_id = $old_status;
		}
		update_user_meta( $this->ID, $this->status_key, $this->status );
	}
}