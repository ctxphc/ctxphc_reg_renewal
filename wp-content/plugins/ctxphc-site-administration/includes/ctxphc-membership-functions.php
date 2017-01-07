<?php

/**
 * Changes the current member by ID or name.
 *
 *
 * Set $id to null and specify a name if you do not know a member's ID.
 *
 * Some WordPress functionality is based on the current member and not based on
 * the signed in member. Therefore, it opens the ability to edit and perform
 * actions on members who aren't signed in.
 *
 * @since 2.0.3
 * @global object $current_member The current member object which holds the member data.
 * @uses do_action() Calls 'set_current_member' hook after setting the current member.
 *
 * @param int $id User ID
 * @param string $name User's username
 * @return CTXPHC_Member Current member User object
 */


/**
 * Abbreviations for U.S. States
 * Based on list at https://developer.paypal.com/docs/classic/api/StateandProvinceCodes/
 */
function ctxphc_get_states() {
	$states = array(
		'AL' => 'Alabama',
		'AK' => 'Alaska',
		'AS' => 'American Samoa',
		'AZ' => 'Arizona',
		'AR' => 'Arkansas',
		'CA' => 'California',
		'CO' => 'Colorado',
		'CT' => 'Connecticut',
		'DE' => 'Delaware',
		'DC' => 'District of Columbia',
		'FM' => 'Federated States of Micronesia',
		'FL' => 'Florida',
		'GA' => 'Georgia',
		'GU' => 'Guam',
		'HI' => 'Hawaii',
		'ID' => 'Idaho',
		'IL' => 'Illinois',
		'IN' => 'Indiana',
		'IA' => 'Iowa',
		'KS' => 'Kansas',
		'KY' => 'Kentucky',
		'LA' => 'Louisiana',
		'ME' => 'Maine',
		'MH' => 'Marshall Islands',
		'MD' => 'Maryland',
		'MA' => 'Massachusetts',
		'MI' => 'Michigan',
		'MN' => 'Minnesota',
		'MS' => 'Mississippi',
		'MO' => 'Missouri',
		'MT' => 'Montana',
		'NE' => 'Nebraska',
		'NV' => 'Nevada',
		'NH' => 'New Hampshire',
		'NJ' => 'New Jersey',
		'NM' => 'New Mexico',
		'NY' => 'New York',
		'NC' => 'North Carolina',
		'ND' => 'North Dakota',
		'MP' => 'Northern Mariana Islands',
		'OH' => 'Ohio',
		'OK' => 'Oklahoma',
		'OR' => 'Oregon',
		'PW' => 'Palau',
		'PA' => 'Pennsylvania',
		'PR' => 'Puerto Rico',
		'RI' => 'Rhode Island',
		'SC' => 'South Carolina',
		'SD' => 'South Dakota',
		'TN' => 'Tennessee',
		'TX' => 'Texas',
		'UT' => 'Utah',
		'VT' => 'Vermont',
		'VI' => 'Virgin Islands',
		'VA' => 'Virginia',
		'WA' => 'Washington',
		'WV' => 'West Virginia',
		'WI' => 'Wisconsin',
		'WY' => 'Wyoming',
		'AA' => 'Armed Forces Americas',
		'AE' => 'Armed Forces',
		'AP' => 'Armed Forces Pacific',
	);

	return $states;
}

/******************************************************************************************************/
/**
 * @param        $id
 * @param string $name
 *
 * @return \CTXPHC_Member
 */
function ctxphc_set_current_member( $id, $name = '' ) {
	global $current_member;

	if ( isset( $current_member ) && ( $current_member instanceof
		CTXPHC_Member ) && ( $id == $current_member->id ) ){
		return $current_member;
	}


	$current_member = new CTXPHC_Member( $id, $name );

	setup_memberdata( $current_member->id );

	do_action( 'set_current_member' );

	return $current_member;
}

/**
 * Retrieve the current member object.
 *
 * @since 2.0.3
 *
 * @return CTXPHC_Member Current member CTXPHC_Member object
 */
function ctxphc_get_current_member() {
	global $current_member;

	get_currentmemberinfo();

	return $current_member;
}

/**
 * Populate global variables with information about the currently logged in member.
 *
 * Will set the current member, if the current member is not set. The current member
 * will be set to the logged in person. If no member is logged in, then it will
 * set the current member to 0, which is invalid and won't have any permissions.
 *
 * @since 0.71
 * @uses $current_member Checks if the current member is set
 * @uses wp_validate_auth_cookie() Retrieves current logged in member.
 *
 * @return bool|null False on XMLRPC Request and invalid auth cookie. Null when current member set
 */
function get_currentmemberinfo() {
	global $current_member;

	if ( ! empty( $current_member ) ) {
		if ( $current_member instanceof CTXPHC_Member )
			return $current_member;

		// Upgrade stdClass to CTXPHC_Member
		if ( is_object( $current_member ) && isset( $current_member->ID ) ) {
			$cur_id = $current_member->ID;
			$current_member = null;
			$current_member = ctxphc_set_current_member( $cur_id );
			return $current_member;
		}

		// $current_member has a junk value. Force to CTXPHC_Member with ID 0.
		$current_member = null;
		ctxphc_set_current_member( 0 );
		return false;
	}

	if ( defined( 'XMLRPC_REQUEST' ) && XMLRPC_REQUEST ) {
		wp_set_current_member( 0 );
		return false;
	}

	if ( ! $member = wp_validate_auth_cookie() ) {
		if ( is_blog_admin() || is_network_admin() || empty( $_COOKIE[ LOGGED_IN_COOKIE ] ) || ! $member = wp_validate_auth_cookie(
			$_COOKIE[ LOGGED_IN_COOKIE ], 'logged_in' ) ){
			ctxphc_set_current_member( 0 );
			return false;
		}
	}
	ctxphc_set_current_member( $member );
}

/**
 * Retrieve member info by member ID.
 *
 * @since 0.71
 *
 * @param int $member_id User ID
 * @return CTXPHC_Member|bool CTXPHC_Member object on success, false on failure.
 */
function get_memberdata( $member_id ) {
	return get_member_by( 'id', $member_id );
}

/**
 * Retrieve member info by a given field
 *
 * @since 2.8.0
 *
 * @param string $field The field to retrieve the member with. id | slug | email | login
 * @param int|string $value A value for $field. A member ID, slug, email address, or login name.
 * @return CTXPHC_Member|bool CTXPHC_Member object on success, false on failure.
 */
function get_member_by( $field, $value ) {
	$memberdata = CTXPHC_Member::get_data_by( $field, $value );

	if ( ! $memberdata )
		return false;

	$member = new CTXPHC_Member;
	$member->init( $memberdata );

	return $member;
}

/**
 * Retrieve info for member lists to prevent multiple queries by get_memberdata()
 *
 * @since 3.0.0
 *
 * @param array $member_ids User ID numbers list
 */
function cache_members( $member_ids ) {
	global $wpdb;

	$clean = _get_non_cached_ids( $member_ids, 'members' );

	if ( empty( $clean ) )
		return;

	$list = implode( ',', $clean );

	$members = $wpdb->get_results( "SELECT * FROM $wpdb->members WHERE ID IN ($list)" );

	$ids = array();
	foreach ( $members as $member ) {
		update_member_caches( $member );
		$ids[] = $member->ID;
	}
	update_meta_cache( 'member', $ids );
}

/**
 * Notify the blog admin of a new member, normally via email.
 *
 * @since 2.0
 *
 * @param int $member_id User ID
 * @param string $plaintext_pass Optional. The member's plaintext password
 */
/******************************************************************************************************/
function ctxphc_new_member_notification( $member_id, $plaintext_pass = '' ) {
	$member = get_memberdata( $member_id );

	// The blogname option is escaped with esc_html on the way into the database in sanitize_option
	// we want to reverse this for the plain text arena of emails.
	$blogname = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );

	$message  = sprintf( __( 'New member registration on your site %s:' ),
						$blogname ) . "\r\n\r\n";
	$message .= sprintf( __( 'Username: %s' ), $member->member_login ) .
				"\r\n\r\n";
	$message .= sprintf( __( 'E-mail: %s' ), $member->member_email ) . "\r\n";
	wp_mail( get_option( 'admin_email' ), sprintf(__('[%s] New User
	Registration'), $blogname), $message);

	if ( empty($plaintext_pass) )
		return;

	$message  = sprintf( __( 'Username: %s' ), $member->member_login ) . "\r\n";
	$message .= sprintf( __( 'Password: %s' ), $plaintext_pass ) . "\r\n";
	$message .= wp_login_url() . "\r\n";

	wp_mail( $member->member_email, sprintf(__('[%s] Your username and
	password'), $blogname), $message);
}

function count_member( $membership_type, $status_id ) {
	global $wpdb;
	$debug = true;
	$where = "membership_type = $membership_type AND status_id = $status_id";

	$message = "membership_type = $membership_type AND status_id = $status_id";
	error_log_debug_message( $message, $debug );

	$member_count = $wpdb->get_var(
		"SELECT COUNT(DISTINCT ID) FROM ctxphc_members WHERE {$where}"
	);

	$message = "The number of members are $member_count ";
	error_log_debug_message( $message, $debug );

	return $member_count;
}

/**
 * @param $months_from_now
 *
 * @return array
 */
function get_members_birthdays( $months_from_now ) {
	Global $wpdb;
	$the_members_birthdays = array();
	$members_birthday_month = array();
	$month_to_get = date( m ) + $months_from_now;

	$members_birthdays = $wpdb->get_results(
		'SELECT first_name, last_name, bday
		FROM ctxphc_members
		WHERE status_id = 1
		ORDER BY bday'
	);

	foreach ( $members_birthdays as $members_bday ) {
		$members_birthday_month[ $members_bday->ID ] = str_split( $members_bday->bday, 2 );
	}

	foreach ( $members_birthday_month as $the_members_birthday_month ) {
		if ( $the_members_birthday_month == $month_to_get ) {
			$the_members_birthdays[] = $wpdb->get_row(
				"SELECT first_name, last_name, bday
				FROM ctxphc_members
				WHERE ID = {$the_members_birthday_month->ID}"
			);
		}
	}
	return $the_members_birthdays;
}

function display_birthdays( $birthdays, $month_name ){

	echo '<div class="table table_bdays2" >';
	echo '<p class="sub" >' . sanitize_text_field( $month_name ) . '</p>';
	echo '<table ><tbody >';
	$rec_count = 0;
	foreach ( $birthdays as $birthday ) {
		$rec_count ++;
		if ( $rec_count <> 1 ) {
			echo '<tr><td class="b" >';
			echo sanitize_text_field( "{$birthday->first_name} {$birthday->last_name}" );
			echo '</td ><td class="last t" >';
			echo sanitize_text_field( "{$birthday->bday}" );
			echo '</td ></tr >';
		} else {
			echo '<tr class="first" ><td class="b" >';
			echo sanitize_text_field( "{$birthday->first_name } {$birthday->last_name}" );
			echo '</td ><td class="last t" >';
			echo sanitize_text_field( "{$birthday->bday_month}/{$birthday->bday_day}" );
			echo '</td ></tr >';
		}
	}
	echo '</tbody ></table ></div>';
}

function display_birthday_error( $month_name ){
	echo '<div class="table table_bdays2" >';
	echo '<p class="sub" >' . sanitize_text_field( $month_name ) . '</p>';
	echo '<table ><tbody >';
	echo '<tr ><td class="first b " >';
	echo sanitize_text_field( 'There was a problem getting the birthdays. Contact <a href="mailto:' . antispambot( 'support@ctxphc.com' ) . '" >CTXPHC Support</a >.' );
	echo '</td ><td class="t posts" >ERROR!</td ></tr >';
	echo '</tbody></table></div>';
}

/**
 * Display member counts for each membership type and total member count
 *
 * @param $act_indiv
 * @param $act_indiv_child
 * @param $act_couple
 * @param $act_family
 * @param $act_members
 */
function display_member_counts( $act_indiv, $act_indiv_child, $act_couple, $act_family, $act_members ){?>
	<div class="table table_members" >
		<p class="sub" ><?php echo 'Current Membership'; ?></p>
	<table >
		<tr class="first" >
			<td class="first b individual" ><?php echo absint( $act_indiv ); ?></td >
			<td class="t posts" >Individuals</td >
		</tr >
		<tr >
			<td class="first b pluschild" ><?php echo absint( $act_indiv_child );
				?></td >
			<td class="t posts" >Individual & Children</td >
		</tr >
		<tr >
			<td class="first b couple" ><?php echo absint( $act_couple ); ?></td >
			<td class="t posts" >Couples</td >
		</tr >
		<tr >
			<td class="first b household" ><?php echo absint( $act_family ); ?></td >
			<td class="t posts" >Households</td >
		</tr >
		<tr >
			<td class="first b tot_members" ><?php echo absint( $act_members );
				?></td >
			<td class="t posts" >Total Members</td >
		</tr >
	</table >
	</div>
	<div class="table table_parrot_points" >
		<p class="sub" ><?php echo 'Parrot Point Top 10'; ?></p >
		<table >
			<tr class="first" >
				<td class='b ' ><span class='total-count' >23</span ></td >
				<td class="last t " >Members Name</td >
			</tr >
			<tr >
				<td class='b ' ><span class='total-count' >20</span ></td >
				<td class="last t" >Members Name</td >
			</tr >
			<tr >
				<td class='b ' ><span class='total-count' >19</span ></td >
				<td class="last t" >Members Name</td >
			</tr >
			<tr >
				<td class='b ' ><span class='total-count' >15</span ></td >
				<td class="last t" >Members Name</td >
			</tr >
		</table >
	</div >
	<br class="clear" />
	<?php
}
/******************************************************************************************************/
function process_members_family_records( $member_record ){
	global $wpdb;

	$family_table = 'ctxphc_members_family';
	$memb_id = $member_record->memb_id;
	$family_query = "SELECT id, first_name, last_name, memb_id, email FROM ctxphc_members_family WHERE memb_id = $memb_id";
	$output_type = 'ARRAY_A';

	$family_records = $wpdb->get_results( $family_query );
	$num_rows = $wpdb->num_rows;
	echo sanitize_text_field( "<div>number of rows in family records is  $num_rows</div>" );
	foreach ( $family_records as $family_record ){
		echo sanitize_text_field( "Updating memb_id for family member: $family_record->first_name $family_record->last_name<br />" );
		echo sanitize_text_field( "Current family's memb_id is $family_record->memb_id. Changing it to actual member's id of $member_record->id" );
		echo "<div class='spacer'></div>";

		$data = array( 'memb_id' => $member_record->id );
		$where = array( 'id' => $family_record->id );

		$memb_id_update = $wpdb->update( $family_table, $data, $where );

		if ( $memb_id_update ){
			echo sanitize_text_field( "Updated $memb_id_update record(s)<br />" );
			echo "<div class='spacer'></div>";

			//if family member has an email address check to see if they have wordpress user account.
			//if not create one, then update wp_user_id field in family members table.
			$user_id = $family_record->id;
			$email_addr = $family_record->email;
			if ( ! empty( $email_addr ) ){

				$wp_user_id = update_wp_user_id( $family_table, $email_addr, $user_id );

				if ( $wp_user_id ){
					echo sanitize_text_field( "Family member $family_record->first_name $family_record->last_name  has an existing wordpress login and password." );
				} else {
					echo sanitize_text_field( "**** Family member $family_record->first_name  $family_record->last_name  failed to get or update the wordpress login and password. ****" );
				}
			}
		}
	}
}

/******************************************************************************************************/
function update_wp_user_id( $table, $email_addr, $user_id ){
	global $wpdb;
	$wp_user_id = email_exists( $email_addr ); //Returns wordpress user id or FALSE

	if ( $wp_user_id ){
		$data = array( 'wp_user_id' => $wp_user_id );
		$where = array( 'id' => $user_id );

		$update_result = $wpdb->update( $table, $data, $where );

		if ( ! $update_result ){
			return FALSE;
		}
	} else { //as the user has an email address, they will get a wordpress login

		$wp_user_id = create_new_wp_user( $username, $email_addr );

		if ( $wp_user_id ){
			echo '<div>Creeated a new wordpress username and password for $username, $email </div>';
		}
	}
	return $wp_user_id;
}

/******************************************************************************************************/
function create_new_wp_user( $username, $email ){
	$random_password = wp_generate_password( $length = 12, $include_standard_special_chars = true );
	$wp_user_id = wp_create_user( $username, $random_password, $email );

	if ( $wp_user_id ){
		return $wp_user_id;
	} else {
		return FALSE;
	}
}

/******************************************************************************************************/
function create_ctxphc_members_listing_table() {
	global $wpdb;
	$wpdb->show_errors();
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

	$table_name = $wpdb->prefix . "members_listing";

	//Drop the members temp table if it still exists
	$query = "DROP TABLE IF EXISTS $table_name";
	$wpdb->query( $query );

	$sql = "CREATE TABLE IF NOT EXISTS $table_name (
		id SMALLINT NOT NULL AUTO_INCREMENT,
		first_name varchar(55) NOT NULL,
		last_name varchar(75) NOT NULL,
		bday varchar(10),
		email varchar(255),
			  phone varchar(12),
		hatch_date datetime,
			  address1 varchar(175),
			  address2 varchar(175),
			  memb_id SMALLINT(9),
			  p_memb enum('Y','N'),
			  PRIMARY KEY  (id)
	);";

	dbDelta( $sql );
}

/******************************************************************************************************/
function get_ctxphc_record( $id, $table ){

	 global $wpdb;
	 $wpdb->show_errors();
	 $result = $wpdb->get_row( "SELECT * FROM $table WHERE id = $id");

	 return $result;
}

/******************************************************************************************************/
function load_data( $data ){
	global $wpdb;
	$wpdb->show_errors();
	if ( $data->addr1 ){
		$insert_data = array(
			'first_name'   => $data->first_name,
			'last_name'    => $data->last_name,
			'email'        => $data->email,
			'phone'        => $data->phone,
			'bday'         => $data->bday_month . '/' . $data->bday_day . '/' . $data->bday_year,
			'hatch_date'   => $data->hatch_date,
			'address1'     => $data->addr1 . ' ' . $data->addr2,
			'address2'     => $data->city . ', ' . $data->state . ' ' . $data->zip,
			'memb_id'      => $data->id,
			'p_memb'       => 'Y',
		);
	} else {
		$insert_data = array(
			'first_name'   => $data->first_name,
			'last_name'    => $data->last_name,
			'email'        => $data->email,
			'phone'        => $data->phone,
			'bday'         => $data->bday_month . '/' . $data->bday_day . '/' . $data->bday_year,
			'hatch_date'   => $data->hatch_date,
			'address1'     => NULL,
			'address2'     => NULL,
			'memb_id'      => $data->id,
			'p_memb'       => 'N',
		);
	}
	$insert_result = $wpdb->insert(  'ctxphc_members_listing', $insert_data );

	if ( $insert_result ){
		//something
	} else {
		error_log( "data insert FAILED with mysql error $wpdb->print_error()", 0 );
	}
}

/******************************************************************************************************/
function ctxphc_get_all_active_members(){
	 GLOBAL $wpdb;
	 $all_members = $wpdb->get_results( "SELECT ID from ctxphc_members WHERE status_id = 1" );

	 return $all_members;
}


/******************************************************************************************************/
function get_ctxphc_member_list( $rpt_type ){
	 switch ($rpt_type) {
		  case 'active':
			   ctxphc_render_active_member_listing();

			   break;

		  case 'arcived':
			   ctxphc_render_archived_members_listing();

			   break;

		  case 'renewals':


			   break;

		  case 'pending':
			   ctxphc_pending_payments_listing();

			   break;

		  case 'phip':


			   break;

		  default:
			   break;
	 }
}

//Functions to display the membership listing
/******************************************************************************************************/
function ctxphc_render_active_member_listing() {
	include( 'class-ctxphc-list-tables.php');
	build_members_listing_table( $archived = 'N', $pending = 'N');

	$active_members_table = new CTXPHC_List_Table();
	$active_members_table->prepare_items();
	?>

	<!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
	<form id="members-filter" method="get">
	<!-- For plugins, we also need to ensure that the form posts back to our current page -->
	<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
	<!-- Now we can render the completed list table -->
		  <?php $active_members_table->search_box( __( 'Search Users' ), 'user' ); ?>
	<?php $active_members_table->display() ?>
	</form>
<?php }

/******************************************************************************************************/
function ctxphc_render_archived_members_listing(){
	 include( 'class-ctxphc-list-tables.php');
	 build_members_listing_table( $archived = 'Y', $pending = 'N');
	 $archived_members_table = new CTXPHC_List_Table();
	 $archived_members_table->prepare_items();
	?>

	<!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
	<form id="members-filter" method="get">
	<!-- For plugins, we also need to ensure that the form posts back to our current page -->
	<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
	<!-- Now we can render the completed list table -->
	<?php $archived_members_table->display() ?>
	</form>
<?php }

/******************************************************************************************************/
function ctxphc_pending_payments_listing() {
	include( 'class-ctxphc-list-tables.php');

	build_members_listing_table( $archived = 'N', $pending = 'Y' );
	$pending_payment_table = new CTXPHC_List_Table();
	$pending_payment_table->prepare_items();
	?>

	<!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
	<form id="members-filter" method="get">
	<!-- For plugins, we also need to ensure that the form posts back to our current page -->
	<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
	<!-- Now we can render the completed list table -->
	<?php $pending_payment_table->display() ?>
	</form>
<?php }

/******************************************************************************************************/
function ctxphc_archive_user( $memb ){
		  GLOBAL $wpdb;
		  $member_data = $wpdb->get_row( "SELECT memb_id, p_memb FROM ctxphc_members_listing WHERE id = $memb");

		  if ( $member_data->p_memb === 'Y'){
			   $table = 'ctxphc_members';
		  } else {
			   $table = 'ctxphc_members_family';
		  }

		  $data = array( 'archived' => 'Y' );
		  $where = array( 'id' => $memb );
		  error_log("!!!!!!  The table value is $table.  !!!!!!", 0);
		  error_log("!!!!!! The archvied value is {$data['archived']} !!!!!!", 0);
		  error_log("!!!!!! The where clause is {$where['id']} !!!!!!", 0);

		  $update_result = $wpdb->update( $table, $data, $where );

		  return $update_result;
	}