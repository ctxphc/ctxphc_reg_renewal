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

    <h2><?php _e( 'CTXPHC Parrot Points Dashboard' ); ?></h2>

    <h2 class="nav-tab-wrapper">
	<a href="ctxphc-parrot-point-listing.php" class="nav-tab nav-tab-active">
	    <?php _e( 'Management' ); ?>
	</a>
	<a href="ctxphc_parrot-point-management.php" class="nav-tab">
	    <?php _e( 'Listing' ); ?>
	</a>
	<a href="#" class="nav-tab">
	    <?php _e( "??????????" ); ?>
	</a>
    </h2>

    <div class="changelog">
	<h3><?php _e( "Parrot Point's Listing" ); ?></h3>

    </div>
</div>

<?php include( ABSPATH . 'wp-admin/admin-footer.php' );
?>
