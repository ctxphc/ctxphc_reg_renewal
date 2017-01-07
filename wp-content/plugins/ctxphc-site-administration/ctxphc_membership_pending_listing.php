<?php
/**
 *
 */

/** WordPress Administration Bootstrap */
require_once( './admin.php' );

$title = __( "CTXPHC Membership Administration" );

$curr_year = date('Y');

include( ABSPATH . 'wp-admin/admin-header.php' );

//CTXPHC Membership Listing Requirements
require_once( 'ctxphc_memb_tables.php' );
include( 'class-ctxphc-list-tables.php' );


//Create ctxphc members temp table
ctxphc_load_members_temp_table();

?>
<div class="wrap about-wrap">

    <h2><?php _e( 'CTXPHC Membership Administration Dashboard' ); ?></h2>

    <h2 class="nav-tab-wrapper">
	<a href="ctxphc_membership_listing.php" class="nav-tab nav-tab-active">
	    <?php _e( 'Listing' ); ?>
	</a>
	<a href="ctxphc_membership_pending_listing.php" class="nav-tab">
	    <?php _e( 'Pending' ); ?>
	</a>
	<a href="ctxphc_membership_parrot_points_admin.php" class="nav-tab">
	    <?php _e( "Parrot Points" ); ?>
	</a>
    </h2>

    <div class="changelog">

	<h3><?php _e( 'Pending Memberships' ); ?></h3>


    </div>
</div>

<?php include( ABSPATH . 'wp-admin/admin-footer.php' );
?>
