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

	<h3><?php _e( 'Parrot Point Totals' ); ?></h3>

	<div class="wrap">
	    <div id="icon-users" class="icon32"><br/></div>
	    <h2>Current Members</h2>

	    <!--<div style="background:#ECECEC;border:1px solid #CCC;padding:0 10px;margin-top:5px;border-radius:5px;-moz-border-radius:5px;-webkit-border-radius:5px;">
		<p>This page demonstrates the use of the <tt><a href="http://codex.wordpress.org/Class_Reference/WP_List_Table" target="_blank" style="text-decoration:none;">WP_List_Table</a></tt> class in plugins.</p>
		<p>For a detailed explanation of using the <tt><a href="http://codex.wordpress.org/Class_Reference/WP_List_Table" target="_blank" style="text-decoration:none;">WP_List_Table</a></tt>
		class in your own plugins, you can view this file <a href="/wp-admin/plugin-editor.php?plugin=table-test/table-test.php" style="text-decoration:none;">in the Plugin Editor</a> or simply open <tt style="color:gray;"><?php echo __FILE__ ?></tt> in the PHP editor of your choice.</p>
		<p>Additional class details are available on the <a href="http://codex.wordpress.org/Class_Reference/WP_List_Table" target="_blank" style="text-decoration:none;">WordPress Codex</a>.</p>
	    </div>-->

	    <!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
	    <form id="members-filter" method="get">
		<!-- For plugins, we also need to ensure that the form posts back to our current page -->
		<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
		<!-- Now we can render the completed list table -->
		<?php $memb_list_table->display() ?>
	    </form>
	</div>
    </div>
</div>

<?php include( ABSPATH . 'wp-admin/admin-footer.php' );
?>
