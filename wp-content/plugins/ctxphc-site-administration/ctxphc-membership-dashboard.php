<?php
/** WordPress Administration Bootstrap */
require_once( ABSPATH . 'wp-load.php' );
require_once( ABSPATH . 'wp-admin/admin.php' );
require_once( ABSPATH . 'wp-admin/admin-header.php' );

//CTXPHC Membership Listing Requirements
require_once( 'includes/ctxphc-membership-functions.php');


?>
<div class="wrap about-wrap">

    <h2><?php _e( 'Central Texas Parrot Head Club - Membership Dashboard' ); ?></h2>

    <h2 class="nav-tab-wrapper">
         <a href="<?php plugins_url( 'ctxphc-membership-dashboard.php' , __FILE__ ) ?>" class="nav-tab nav-tab-active" onclick="rtp_type=active">
	    <?php _e( 'Active Members' ); ?>
	</a>
	<a href="<?php plugins_url( 'ctxphc-membership-dashboard.php' , __FILE__ ) ?>" class="nav-tab" onclick="rtp_type=archived">
	    <?php _e( 'Archived Members<br >coming soon' ); ?>
	</a>
	<a href="<?php plugins_url( 'ctxphc-membership-dashboard.php' , __FILE__ ) ?>" class="nav-tab" onclick="rtp_type=pending">
	    <?php _e( "Pending Payments<br >coming soon" ); ?>
	</a>
    </h2>

    <div class="changelog">
	<h3><?php _e( 'Active Members' ); ?></h3>

	<?php get_ctxphc_member_list( $rp_type ); ?>

    </div>
</div>