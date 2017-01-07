<?php
/**
 * Our custom dashboard page
 */

/** WordPress Administration Bootstrap */
require_once( ABSPATH . 'wp-load.php' );
require_once( ABSPATH . 'wp-admin/admin.php' );
require_once( ABSPATH . 'wp-admin/admin-header.php' );
?>
<div class="wrap about-wrap">

    <h1><?php _e( 'CTXPHC Admin Dashboard Page' ); ?></h1>

    <h2 class="nav-tab-wrapper">
	<a href="#" class="nav-tab nav-tab-active">
	    <?php _e( 'Dashboard' ); ?>
	</a>
	<a href="#" class="nav-tab">
	    <?php _e( 'Membership' ); ?>
	</a>
	<a href="#" class="nav-tab">
	    <?php _e( "Pirate's Ball" ); ?>
	</a>
    </h2>

    <div class="changelog">
	<?php
	add_action( 'wp_dashboard_setup', 'ctxphc_admin_dashboard_setup' );
	function ctxphc_dashboard_admin_setup() {
	    add_meta_box( 'ctxphc_membership_count_wigdet', 'CTXPHC Membership Counts', 'ctxphc_membership_count_widget', 'ctxphc_admin_dashboard', 'side', 'high' );
	}
	function ctxphc_membership_count_widget() {
	    // widget content goes here
	    Global $wpdb;
	    $bdayData = array();
	    $ActSingles = count_active_singles();
	    $ActCouples = count_active_couples();
	    $ActFamilies = count_active_families();
	    $ActMembers = $ActSingles + $ActCouples + $ActFamilies;

	?>
	<div>
	    Singles: <?php echo $ActSingles; ?>
	</div>
	<div>
	    Couples: <?php echo $ActCouples; ?>
	</div>
	<div>
	    Families: <?php echo $ActFamilies; ?>
	</div>
	<?php } ?>
	
	<h3><?php _e( 'Morbi leo risus, porta ac consectetur' ); ?></h3>

	<div class="feature-section images-stagger-right">
	    <img src="<?php echo esc_url( admin_url( 'images/screenshots/theme-customizer.png' ) ); ?>" class="image-50" />
	    <h4><?php _e( 'Risus Consectetur Elit Sollicitudin' ); ?></h4>
	    <p><?php _e( 'Cras mattis consectetur purus sit amet fermentum. Vivamus sagittis lacus vel augue laoreet rutrum faucibus dolor auctor. Vestibulum id ligula porta felis euismod semper. Cras justo odio, dapibus ac facilisis in, egestas eget quam. Nulla vitae elit libero, a pharetra augue. Donec sed odio dui.' ); ?></p>

	    <h4><?php _e( 'Mattis Justo Purus' ); ?></h4>
	    <p><?php _e( 'Aenean lacinia bibendum nulla sed consectetur. Donec id elit non mi porta gravida at eget metus. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum id ligula porta felis euismod semper. Integer posuere erat a ante venenatis dapibus posuere velit aliquet. Cras mattis consectetur purus sit amet fermentum. Maecenas faucibus mollis interdum. Etiam porta sem malesuada magna mollis euismod. Maecenas faucibus mollis interdum. Curabitur blandit tempus porttitor. Cras justo odio, dapibus ac facilisis in, egestas eget quam.' ); ?></p>
	</div>
    </div>
</div>
<?php include( ABSPATH . 'wp-admin/admin-footer.php' );