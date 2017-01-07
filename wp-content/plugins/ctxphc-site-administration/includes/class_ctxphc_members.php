<?php

/**
 * CTXPHC Member class.
 *
 * @since 2.0.0
 * @package CTXPHC Administration
 * @subpackage Members
 */
class CTXPHC_Member {
     /**
      * Member data container.
      *
      * @since 2.0.0
      * @access private
      * @var array
      */
     var $data;

     /**
      * The user's ID.
      *
      * @since 2.0.0
      * @access public
      * @var int
      */
     var $ID = 0;

     /**
      * The filter context applied to user data fields.
      *
      * @since 2.9.0
      * @access private
      * @var string
      */
     var $filter = null;

     /**
      * Constructor
      *
      * Retrieves the userdata and passes it to {@link CTXPHC_Member::init()}.
      *
      * @since 2.0.0
      * @access public
      *
      * @param int|string|stdClass|CTXPHC_Member $id Member's ID, a CTXPHC_Member object, or a user object from the DB.
      * @param string $name Optional. Member's username
      * @param int $blog_id Optional Blog ID, defaults to current blog.
      * @return CTXPHC_Member
      */
     function __construct( $id = 0, $name = '', $blog_id = '' ) {


               if ( is_a( $id, 'CTXPHC_Member' ) ) {
                         $this->init( $id->data, $blog_id );
                         return;
               } elseif ( is_object( $id ) ) {
                         $this->init( $id, $blog_id );
                         return;
               }

               if ( ! empty( $id ) && ! is_numeric( $id ) ) {
                         $name = $id;
                         $id = 0;
               }

               if ( $id )
                         $data = self::get_data_by( 'id', $id );
               else
                         $data = self::get_data_by( 'login', $name );

               if ( $data )
                         $this->init( $data, $blog_id );
     }

     /**
      * Sets up object properties, including capabilities.
      *
      * @param object $data Member DB row object
      * @param int $blog_id Optional. The blog id to initialize for
      */
     function init( $data, $blog_id = '' ) {
               $this->data = $data;
               $this->ID = (int) $data->id;

               $this->for_blog( $blog_id );
     }

     /**
      * Return only the unique Member fields
      *
      * @since 3.3.0
      *
      * @param string $field The fields to query against: 
      *        'id', 'email' or 'login'
      * @param string|int $value The field value
      * @return object Raw user object
      */
     static function get_data_by( $field, $value ) {
               global $wpdb;

               if ( 'id' == $field ) {
                         // Make sure the value is numeric to avoid casting objects, for example,
                         // to int 1.
                         if ( ! is_numeric( $value ) )
                                   return false;
                         $value = intval( $value );
                         if ( $value < 1 )
                                   return false;
               } else {
                         $value = trim( $value );
               }

               if ( !$value )
                         return false;

               switch ( $field ) {
                         case 'id':
                                   $memb_id = $value;
                                   $db_field = 'id';
                                   break;
                         case 'email':
                                   $memb_id = $value;
                                   $db_field = 'email';
                                   break;
                         case 'login':
                                   $value = sanitize_user( $value );
                                   $memb_id = $value;
                                   $db_field = 'login';
                                   break;
                         default:
                                   return false;
               }

               $member = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM " . $wpdb->prefix . "_members WHERE $db_field = %s", $value ) );

               if ( ! member ){ return false; }

               return $member;
     }



     /**
      * Determine whether the user exists in the database.
      *
      * @since 3.4.0
      * @access public
      *
      * @return bool True if user exists in the database, false if not.
      */
     function exists() {
               return ! empty( $this->ID );
     }

     /**
      * Retrieve the value of a property or meta key.
      *
      * Retrieves from the users and usermeta table.
      *
      * @since 3.3.0
      *
      * @param string $key Property
      */
     function get( $key ) {
               return $this->__get( $key );
     }

     /**
      * Determine whether a property or meta key is set
      *
      * Consults the users and usermeta tables.
      *
      * @since 3.3.0
      *
      * @param string $key Property
      */
     function has_prop( $key ) {
               return $this->__isset( $key );
     }

     /*
      * Return an array representation.
      *
      * @since 3.5.0
      *
      * @return array Array representation.
      */
     function to_array() {
               return get_object_vars( $this->data );
     }
}