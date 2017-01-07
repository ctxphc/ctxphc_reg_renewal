<?php
/** WordPress Administration Bootstrap */
require_once( ABSPATH . 'wp-load.php' );
require_once( ABSPATH . 'wp-admin/admin.php' );
require_once( ABSPATH . 'wp-admin/admin-header.php' );

//CTXPHC Membership Listing Requirements
require_once 'ctxphc_memb_tables.php';
require_once 'class-ctxphc-list-tables.php';


//Create ctxphc members temp table
ctxphc_load_members_temp_table();

?>
<div class="wrap about-wrap">

    <h2><?php _e( 'CTXPHC Membership Renewals Dashboard' ); ?></h2>

    <h2 class="nav-tab-wrapper">
	<a href="<?php plugins_url( 'ctxphc-renewal-dashboard.php', __FILE__ ) ?>" class="nav-tab">
	    <?php _e( 'Waiting' ); ?>
	</a>
	<a href="<?php plugins_url( 'ctxphc-renewals-pending.php', __FILE__ ) ?>" class="nav-tab nav-tab-active">
	    <?php _e( 'Pending' ); ?>
	</a>
	<a href="<?php plugins_url( 'ctxphc-renewals-paid.php', __FILE__ ) ?>" class="nav-tab">
	    <?php _e( "Completed" ); ?>
	</a>
    </h2>

    <div class="changelog">
	<h3><?php _e( 'Members Renewal Pending' ); ?></h3>

    </div>
</div>

<?php include( ABSPATH . 'wp-admin/admin-footer.php' );
?>
