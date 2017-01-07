<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/** WordPress Administration Bootstrap */
require_once( ABSPATH . 'wp-load.php' );
require_once( ABSPATH . 'wp-admin/admin.php' );
require_once( ABSPATH . 'wp-admin/admin-header.php' );

//CTXPHC Membership Listing Requirements
require_once( 'includes/ctxphc-membership-functions.php');


//Create ctxphc members temp table
//ctxphc_load_members_temp_table();

?>
<div class="wrap about-wrap">

    <h2><?php _e( 'Central Texas Parrot Head Club - Membership Dashboard' ); ?></h2>

    <h2 class="nav-tab-wrapper">
	<a href="<?php plugins_url( 'ctxphc-membership-dashboard.php' , __FILE__ ) ?>" class="nav-tab nav-tab-active">
	    <?php _e( 'Active Members' ); ?>
	</a>
	<a href="<?php plugins_url( 'ctxphc-archived-members.php' , __FILE__ ) ?>" class="nav-tab">
	    <?php _e( 'Archived Members<br >coming soon' ); ?>
	</a>
	<a href="<?php plugins_url( 'ctxphc-pending-payments.php' , __FILE__ ) ?>" class="nav-tab">
	    <?php _e( "Pending Payments<br >coming soon" ); ?>
	</a>
    </h2>

    <div class="changelog">
	<h3><?php _e( 'Active Members' ); ?></h3>

	<?php ctxphc_render_archived_members_listing(); ?>

    </div>
</div>

<?php //include( ABSPATH . 'wp-admin/admin-footer.php' );
?>
