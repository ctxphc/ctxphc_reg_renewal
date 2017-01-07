<?php
/**
 * About This Version administration panel.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** WordPress Administration Bootstrap */
require_once( './admin.php' );

$title = __( "CTXPHC Pirate's Ball Admin Console" );

$curr_year = date('Y');

include( ABSPATH . 'wp-admin/admin-header.php' );
?>

<div class="wrap about-wrap">
    <h1><?php printf( __( "CTXPHC Pirate's Ball Administration Dashboard" ) ); ?></h1>
    <div class="about-text"><?php _e( "Displays a list of all PAID attendees for the $curr_year Pirate's Ball." ); ?></div>
    <div class="wp-badge"><?php printf( __( '%s' ), $curr_year ); ?></div>

	<h2><?php _e( "CTXPHC Pirate's Ball Administration Dashboard" ); ?></h2>

	<h2 class="nav-tab-wrapper">
	    <a href="ctxphc_pirates_ball_attendees_listing.php" class="nav-tab nav-tab-active">
		<?php _e( 'Listing' ); ?>
	    </a>
	    <a href="ctxphc_pirates_ball_pending_attendees_listing.php" class="nav-tab">
		<?php _e( 'Pending' ); ?>
	    </a>
	    <a href="ctxphc_pirates_ball_paid_attendees_listing.php" class="nav-tab">
		<?php _e( "Paid" ); ?>
	    </a>
	</h2>

	<div class="changelog">

	    <h3><?php _e( "$curr_year Pirate's Ball Paid Attendees" ); ?></h3>

	    <div class="feature-section images-stagger-right">
		<img src="<?php echo esc_url(plugins_url( 'images/pb-badge.jpg' ) ); ?>" class="image-50" />
		<h4><?php _e( 'Risus Consectetur Elit Sollicitudin' ); ?></h4>
		<p><?php _e( 'Cras mattis consectetur purus sit amet fermentum. Vivamus sagittis lacus vel augue laoreet rutrum faucibus dolor auctor. Vestibulum id ligula porta felis euismod semper. Cras justo odio, dapibus ac facilisis in, egestas eget quam. Nulla vitae elit libero, a pharetra augue. Donec sed odio dui.' ); ?></p>

		<h4><?php _e( 'Mattis Justo Purus' ); ?></h4>
		<p><?php _e( 'Aenean lacinia bibendum nulla sed consectetur. Donec id elit non mi porta gravida at eget metus. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum id ligula porta felis euismod semper. Integer posuere erat a ante venenatis dapibus posuere velit aliquet. Cras mattis consectetur purus sit amet fermentum. Maecenas faucibus mollis interdum. Etiam porta sem malesuada magna mollis euismod. Maecenas faucibus mollis interdum. Curabitur blandit tempus porttitor. Cras justo odio, dapibus ac facilisis in, egestas eget quam.' ); ?></p>
	    </div>
	</div>
    </div>

    <?php include( ABSPATH . 'wp-admin/admin-footer.php' );
?>