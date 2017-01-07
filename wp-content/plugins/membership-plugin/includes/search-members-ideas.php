<?php
/**
 * Created by PhpStorm.
 * User: ken_kilgore1
 * Date: 1/24/2015
 * Time: 10:13 AM
 */

//Search Parameters
//Search users.

      /* search (string) - Searches for possible string matches on columns. Use of the * wildcard before and/or after the string will match on columns starting with*, *ending with, or *containing* the string you enter.
                           search_columns (array) - List of database table columns to matches the search string across multiple columns.
                           'ID' - Search by user id.
                           'login' / 'user_login' - Search by user login.
                           'nicename' / 'user_nicename' - Search by user nicename.
                           'email' / 'user_email' - Search by user email.
                           'url' / 'user_url' - Search by user url.
                           We can use the user_search_columns filter to modify the search columns.
	*/

//Display users based on a keyword search
$user_query = new WP_User_Query( array( 'search' => 'Rami' ) );


//Display users based on a keyword search, only on login and email columns
$args = array(
	'search'         => 'Rami',
	'search_columns' => array( 'user_login', 'user_email' )
);
$user_query = new WP_User_Query( $args );


//Pagination Parameters
	//Limit retrieved Users.
	    //number (int) - The maximum returned number of results (needed in pagination).
		//offset (int) - Offset the returned results (needed in pagination).

		//Display 10 users
		$user_query = new WP_User_Query( array( 'number' => 10 ) );


		//Display 5 users starting from 25
		$user_query = new WP_User_Query( array( 'number' => 5, 'offset' => 25 ) );


//Order & Orderby Parameters
	//Sort retrieved Users.

	//orderby (string) - Sort retrieved users by parameter. Defaults to 'login'.
        //'ID' - Order by user id.
        //'display_name' - Order by user display name.
		//'name' / 'user_name' - Order by user name.
		//'login' / 'user_login' - Order by user login.
		//'nicename' / 'user_nicename' - Order by user nicename.
		//'email' / 'user_email' - Order by user email.
		//'url' / 'user_url' - Order by user url.
		//'registered' / 'user_registered' - Order by user registered date.
		//'post_count' - Order by user post count.
		//'meta_value' - Note that a 'meta_key=keyname' must also be present in the query (available with Version 3.7).


	//order (string) - Designates the ascending or descending order of the 'orderby' parameter. Defaults to 'ASC'.
		//'ASC' - ascending order from lowest to highest values (1, 2, 3; a, b, c).
		//'DESC' - descending order from highest to lowest values (3, 2, 1; c, b, a).



	//Display users sorted by Post Count, Descending order
	$user_query = new WP_User_Query( array ( 'orderby' => 'post_count', 'order' => 'DESC' ) );


	//Display users sorted by registered, Ascending order
	$user_query = new WP_User_Query( array ( 'orderby' => 'registered', 'order' => 'ASC' ) );



//Custom Field Parameters
	//Show users associated with a certain custom field.

	//The WP_Meta_Query class is used to parse this part of the query since 3.2.0, so check the docs for that class for the full, up to date list of arguments.

		meta_key (string) - Custom field key.
		meta_value (string) - Custom field value.
		meta_compare (string) - Operator to test the 'meta_value'. See 'compare' below.
		meta_query (array) - Custom field parameters (available with Version 3.5).
			key (string) - Custom field key.
			compare (string) - Operator to test. Possible values are '=', '!=', '>', '>=', '<', '<=', 'LIKE', 'NOT LIKE', 'IN', 'NOT IN', 'BETWEEN', 'NOT BETWEEN', 'EXISTS', and 'NOT EXISTS'. Default value is '='.
                                                                                                                                                                                                     Note: Currently 'NOT EXISTS' does not always work as intended if 'relation' is 'OR' when, (1) using the 'role' parameter on single site installs, or (2) for any query on multisite. See ticket #23849.
type (string) - Custom field type. Possible values are 'NUMERIC', 'BINARY', 'CHAR', 'DATE', 'DATETIME', 'DECIMAL', 'SIGNED', 'TIME', 'UNSIGNED'. Default value is 'CHAR'.
                                                                                                                                                                  Display users from Israel

$user_query = new WP_User_Query( array( 'meta_key' => 'country', 'meta_value' => 'Israel' ) );
Display users under 30 years old

$user_query = new WP_User_Query( array( 'meta_key' => 'age', 'meta_value' => '30', 'meta_compare' => '<' ) );
Multiple custom user fields handling

$args = array(
	'meta_query' => array(
		'relation' => 'OR',
		0 => array(
			'key'     => 'country',
			'value'   => 'Israel',
			'compare' => '='
		),
		1 => array(
			'key'     => 'age',
			'value'   => array( 20, 30 ),
			'type'    => 'numeric',
			'compare' => 'BETWEEN'
		)
	)
);
$user_query = new WP_User_Query( $args );