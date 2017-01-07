<?php
function create_ctxphc_members_temp_table() {
    global $wpdb;
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

    $table_name = $wpdb->prefix . "members_temp";

    //Drop the members temp table if it still exists
    $query = "DROP TABLE IF EXISTS $table_name";
    $wpdb->query( $query );
    //$wpdb->print_error();

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
	    ID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	    reg_user mediumint(9) NOT NULL,
	    memb_id mediumint(9) NOT NULL,
	    first_name varchar(55) NOT NULL,
	    last_name varchar(75) NOT NULL,
	    bday varchar(10),
	    email varchar(255),
	    hatch_date datetime
	)";

    dbDelta( $sql );
}

function ctxphc_drop_members_temp_table() {
    global $wpdb;
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

    $table_name = $wpdb->prefix . "members_temp";

    $sql = "DROP TABLE IF EXISTS $table_name";
    dbDelta( $sql );
}

function ctxphc_load_members_temp_table() {
    global $wpdb;
    $wpdb->show_errors();

    $table_name = $wpdb->prefix . "members_temp";

    create_ctxphc_members_temp_table();

    $memb_count = 0;
    $prim_memb_count = 0;
    $sp_memb_count = 0;
    $fam_memb_count = 0;

    $primMembers = $wpdb->get_results("SELECT memb_id, first_name, last_name, bday_month, bday_day, email, hatch_date FROM ctxphc_members");
    //$wpdb->print_error();

    if ($primMembers) {
	foreach ( $primMembers as $primMember ) {
	    $memb_count++;
	    $prim_memb_count++;

	    $primMemberID = $primMember->memb_id;

	    /*  if ( $primMember->bday_month <= 9 ) {
		$bday_month = 0 . $primMember->bday_month;
	    }

	    if ( $primMember->bday_day <= 9 ) {
		$bday_day = 0 . $primMember->bday_day;
	    }
	    $primMembBDay = $bday_month . '/' . $bday_day; */

	    //echo "<div>primMembBDay = {$primMembBDay}</div>";s
	    //Insert primary members data into temp members table
	    $wpdb->insert($table_name,
		array(
		    'reg_user'	    => $primMember->memb_id,
		    'memb_id'	    => $primMember->memb_id,
		    'first_name'    => $primMember->first_name,
		    'last_name'	    => $primMember->last_name,
		    'bday'	    => $primMember->bday_month . '/' . $primMember->bday_day,
		    'email'	    => $primMember->email,
		    'hatch_date'  => $primMember->hatch_date

		)
	    );
	    //$wpdb->print_error();

	    //Select from ctxphc_ctxphc_memb_spouses table where spouses reg_user  = primary members memb_id
	    //If it finds a record add it to the temp table for displaying later.
	    $spMember = $wpdb->get_row("SELECT first_name, last_name, bday_month, bday_day, email, phone, memb_id, hatch_date
			    FROM ctxphc_members_spouse WHERE memb_id = {$primMemberID}");


	    if ( $spMember ) {
		$memb_count++;
		$sp_memb_count++;

		/* if ( $spMember->bday_month <= 9 ) {
		    $bday_month = 0 . $spMember->bday_month;
		}

		if ( $spMember->bday_day <= 9 ) {
		    $bday_day = 0 . $spMember->bday_day;
		}

		$spMembBDay = $bday_month . '/' . $bday_day;
		 *
		 */

		//echo "<div>spMembBDay = {$spMembBDay}</div>";
		$wpdb->insert($table_name,
		    array(
			'reg_user'	=> $spMember->memb_id,
			'memb_id'	=> $spMember->memb_id,
			'first_name'	=> $spMember->first_name,
			'last_name'	=> $spMember->last_name,
			'bday'		=> $spMember->bday_month . '/' . $spMember->bday_day,
			'email'		=> $spMember->email,
			'hatch_date'  => $primMember->hatch_date
		    )
		);
		//$wpdb->print_error();
	    }

	    //Select from ctxphc_ctxphc_family_members table where family members memb_id  = primary members memb_id
	    //If it finds a record add it to the temp table for displaying later.
	    $famListing = $wpdb->get_results("SELECT first_name, last_name, bday_month, bday_day, email, memb_id
	    FROM ctxphc_members_family WHERE memb_id = {$primMemberID} ORDER BY first_name");

	    If ($famListing) {
		foreach ( $famListing as $famMember ) {
		    $memb_count++;
		    $fam_memb_count++;

		    $wpdb->insert($table_name,
			array(
			    'reg_user'	    => $famMember->memb_id,
			    'memb_id'	    => $famMember->memb_id,
			    'first_name'    => $famMember->first_name,
			    'last_name'	    => $famMember->last_name,
			    'bday'	    => $famMember->bday_month . '/' . $famMember->bday_day,
			    'email'	    => $famMember->email,
			    'hatch_date'  => $primMember->hatch_date
			)
		    );
		//$wpdb->print_error();
		}
	    }
	}
    }
}

?>