<?php
/** WordPress Administration Bootstrap */
require_once( ABSPATH . 'wp-load.php' );
require_once( ABSPATH . 'wp-admin/admin.php' );
require_once( ABSPATH . 'wp-admin/admin-header.php' );

//CTXPHC Membership Listing Requirements
require_once 'ctxphc_memb_tables.php';
require_once 'class-ctxphc-list-tables.php';


//Create ctxphc members temp table
//ctxphc_load_members_temp_table();

?>
<div class="wrap about-wrap">

    <h2><?php _e( "CTXPHC Pirates's Ball Dashboard" ); ?></h2>

    <h2 class="nav-tab-wrapper">
	<a href="ctxphc-pirate's-ball-listing.php" class="nav-tab nav-tab-active">
	    <?php _e( 'All Attendees' ); ?>
	</a>
	<a href="ctxphc_parrot-point-management.php" class="nav-tab">
	    <?php _e( 'Paid Attendees' ); ?>
	</a>
	<a href="#" class="nav-tab">
	    <?php _e( "Pending Attendees" ); ?>
	</a>
    </h2>

    <div class="changelog">
	<h3><?php _e( "Pirate's Ball Attendees Listing" ); ?></h3>

    </div>
</div>

<?php include( ABSPATH . 'wp-admin/admin-footer.php' );
?>