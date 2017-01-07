<?php

/*
|--------------------------------------------------------------------------
| MAIN CLASS
|--------------------------------------------------------------------------
*/

class ctxphc_bod_dashboard {

    // *--------------------------------------------*
    // * Constructor
    // *--------------------------------------------*

    // *-------------------------
    // * Initializes the plugin by setting localization, filters, and administration functions.
    // *-------------------------
    function __construct() {
	    add_action('admin_menu', array( &$this,'ctxphc_bod_dashboard_register_menu') );
	    add_action('load-index.php', array( &$this,'ctxphc_bod_redirect_dashboard') );
    } // end constructor

    function ctxphc_bod_redirect_dashboard() {
	if(current_user_can( 'view_ctxphc_dashboard' )) {
	    include_once dirname(__FILE__) . '/ctxphc_create_temp_tables.php';
	    ctxphc_create_members_temp_table();

	    $screen = get_current_screen();
	    if( $screen->base == 'dashboard' ) {
		wp_redirect( admin_url( 'index.php?page=custom-dashboard' ) );
	    }
	}
    }

    function ctxphc_bod_dashboard_register_menu() {
	    add_dashboard_page( 'CTXPHC Dashboard', 'CTXPHC Dashboard', 'read', 'custom-dashboard', array( &$this,'ctxphc_bod_create_dashboard') );
    }

    function ctxphc_bod_create_dashboard() {
	    include_once( 'ctxphc_bod_render_dashboard.php'  );
    }
}

// instantiate plugin's class
$GLOBALS['sweet_custom_dashboard'] = new ctxphc_bod_dashboard();

?>
