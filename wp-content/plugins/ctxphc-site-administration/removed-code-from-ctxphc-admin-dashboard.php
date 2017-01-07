<?php

/*************************** LOAD THE BASE CLASS *******************************
 *******************************************************************************
 * The WP_List_Table class isn't automatically available to plugins, so we need
 * to check if it's available and load it if necessary.
 ******************************************************************************/
if(!class_exists('WP_List_Table')){
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

/**********************************************************************************************
* create my extention the WP-List-Table class
**********************************************************************************************/
class Memb_List_Table extends WP_List_Table {
   /***************************************************************************
     * REQUIRED. Set up a constructor that references the parent constructor. We
     * use the parent reference to set some default configs.  We overfide the
	 * parent to pass our own arguments.  We will focus on three parameters:
	 * Singular and plural labels, as well as whether the class supports AJAX.
     ***************************************************************************/
    function __construct(){
        global $status, $page;

        //Set parent defaults
        parent::__construct( array(
            'singular'  => 'list_member',     //singular name of the listed records
            'plural'    => 'list_members',    //plural name of the listed records as well as one of the table css classes
            'ajax'      => false        //does this table support ajax?
        ) );
    }


	function extra_table_nav($which) {
		if ( $which == "top" ) {
			//The code that goes before the table
			echo "Hi, I'm before the table";
		}
		if ( $which == "bottom" ) {
			//The code that goes after the table
			echo "Hi, I'm after the table";
		}
	}

	function column_default($item, $column_name){
        switch($column_name){
            case 'col_memb_fname':
				return $item->$column_name;
            case 'col_memb_lname':
                return $item->$column_name;
			case 'col_memb_email':
				return $item->$column_name;
            case 'col_memb_bday':
                return $item->$column_name;
			case 'col_memb_renew_date':
                return $item->$column_name;
            default:
				echo $column_name . " ";
                return print_r($item,true); //Show the whole array for troubleshooting purposes
        }
    }

	function column_col_memb_fname($item){

        //Build row actions
        $actions = array(
            'edit'      => sprintf('<a href="?page=%s&action=%s&member=%s">Edit</a>',$_REQUEST['page'],'edit',$item->memb_id),
            'archive'    => sprintf('<a href="?page=%s&action=%s&member=%s">Archive</a>',$_REQUEST['page'],'archive',$item->id),
			'renew'    => sprintf('<a href="?page=%s&action=%s&member=%s">Renew</a>',$_REQUEST['page'],'renew',$item->id),
        );

		//Return the title contents
        return sprintf('%1$s %2$s',
            /*$1%s*/ $item->memb_fname,
            /*$2%s*/ $this->row_actions($actions)
        );
    }

	function column_col_memb_lname($item){

        //Return the title contents
        return sprintf('%1$s',
            /*$1%s*/ $item->memb_lname
        );
    }

	function column_col_memb_email($item){

        //Return the title contents
        return sprintf('%1$s',
            /*$1%s*/ $item->memb_email
        );
    }

	function column_col_memb_bday($item){

        //Return the title contents
        return sprintf('%1$s',
            /*$1%s*/ $item->memb_bday
        );
    }

	function column_col_memb_renew_date($item){

        //Return the title contents
        return sprintf('%1$s',
            /*$1%s*/ $item->memb_renewal_date
        );
    }


	/** ************************************************************************
     * REQUIRED if displaying checkboxes or using bulk actions! The 'cb' column
     * is given special treatment when columns are processed. It ALWAYS needs to
     * have it's own method.
     *
     * @see WP_List_Table::::single_row_columns()
     * @param array $item A singular item (one full row's worth of data)
     * @return string Text to be placed inside the column <td> (movie title only)
     **************************************************************************/
    function column_cb($item){
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            /*$1%s*/ $this->_args['singular'],  //Let's simply repurpose the table's singular label ("movie")
            /*$2%s*/ $item->id                //The value of the checkbox should be the record's id
        );
    }

	/** ************************************************************************
     * REQUIRED! This method dictates the table's columns and titles. This should
     * return an array where the key is the column slug (and class) and the value
     * is the column's title text. If you need a checkbox for bulk actions, refer
     * to the $columns array below.
     *
     * The 'cb' column is treated differently than the rest. If including a checkbox
     * column in your table you must create a column_cb() method. If you don't need
     * bulk actions or checkboxes, simply leave the 'cb' entry out of your array.
     *
     * @see WP_List_Table::::single_row_columns()
     * @return array An associative array containing column information: 'slugs'=>'Visible Titles'

	 * Define the columns that are going to be used in the table
	 * @return array $columns, the array of columns to use with the table
     **************************************************************************/
	function get_columns() {
	    return $columns= array(
		'cb'        			=>__('<input type="checkbox" />'), //Render a checkbox instead of text
		'col_memb_fname'		=>__('First Name'),
		'col_memb_lname'		=>__('Last Name'),
		'col_memb_email'		=>__('Email'),
		'col_memb_bday'			=>__('Birthday'),
		'col_memb_renew_date'	=>__('Renewal')
	    );
	}

	/****************************************************************************
	* Decide which columns to activate the sorting functionality on
	* @return array $sortable, the array of columns that can be sorted by the user
	*

    * Optional. If you want one or more columns to be sortable (ASC/DESC toggle),
    * you will need to register it here. This should return an array where the
    * key is the column that needs to be sortable, and the value is db column to
    * sort by. Often, the key and value will be the same, but this is not always
    * the case (as the value is a column name from the database, not the list table).
    *
    * This method merely defines which columns should be sortable and makes them
    * clickable - it does not handle the actual sorting. You still need to detect
    * the ORDERBY and ORDER querystring variables within prepare_items() and sort
    * your data accordingly (usually by modifying your query).
    *
    * @return array An associative array containing all the columns that should be sortable: 'slugs'=>array('data_values',bool)
    **************************************************************************/
    public function  get_sortable_columns() {
	$sortable_columns = array(
	    'col_memb_fname'	    => array('memb_fname', true),
	    'col_memb_lname'	    => array('memb_lname', true),
	    'col_memb_email'	    => array('memb_email', true),
	    'col_memb_bday'	    => array('memb_bday', true),
	    'col_memb_renew_date'   => array('memb_renewal_date', true)
        );
	return $sortable_columns;
    }

    /** ************************************************************************
     * Optional. If you need to include bulk actions in your list table, this is
     * the place to define them. Bulk actions are an associative array in the format
     * 'slug'=>'Visible Title'
     *
     * If this method returns an empty value, no bulk action will be rendered. If
     * you specify any bulk actions, the bulk actions box will be rendered with
     * the table automatically on display().
     *
     * Also note that list tables are not automatically wrapped in <form> elements,
     * so you will need to create those manually in order for bulk actions to function.
     *
     * @return array An associative array containing all the bulk actions: 'slugs'=>'Visible Titles'
     **************************************************************************/
    function get_bulk_actions() {
        $actions = array(
	    'archive'   => 'Archive',
	    'renew'	=> 'Renew'
        );
        return $actions;
    }


    /** ************************************************************************
     * Optional. You can handle your bulk actions anywhere or anyhow you prefer.
     * For this example package, we will handle it in the class to keep things
     * clean and organized.
     *
     * @see $this->prepare_items()
     **************************************************************************/
    function process_bulk_action() {

        //Detect when a bulk action is being triggered...
        if( 'archive'===$this->current_action() ) {
            wp_die('Members archived (or they would be if we had items to archive)!');
        }
    }


    /** ************************************************************************
     * REQUIRED! This is where you prepare your data for display. This method will
     * usually be used to query the database, sort and filter the data, and generally
     * get it ready to be displayed. At a minimum, we should set $this->items and
     * $this->set_pagination_args(), although the following properties and methods
     * are frequently interacted with here...
     *
     * @uses $this->_column_headers
     * @uses $this->items
     * @uses $this->get_columns()
     * @uses $this->get_sortable_columns()
     * @uses $this->get_pagenum()
     * @uses $this->set_pagination_args()
     **************************************************************************/
    function prepare_items() {
        global $wpdb, $_wp_column_headers;
		$screen = get_current_screen();

		/* -- Preparing the query -- */
			$query = "SELECT * FROM ctxphc_ctxphc_members_temp";

		/* -- Register the Columns -- */
			$columns = $this->get_columns();
			$hidden_columns = array(); //Include any hidden columns from the table in here.
			$sortable_columns = $this->get_sortable_columns();

			$this->_column_headers = array($columns, $hidden_columns, $sortable_columns);

		/* -- Process Bulk Actions -- */
			$this->process_bulk_action();

		/* -- Checks sort ordering -- */
			$orderby = !empty($_REQUEST["orderby"]) ? $_REQUEST['orderby'] : 'memb_fname';  //If no sort, default to first name(fname)
			$order = !empty($_REQUEST['order']) ? $_REQUEST['order'] : 'asc'; //If no order, default to asc
			if(!empty($orderby) & !empty($order)){ $query.=' ORDER BY '.$orderby.' '.$order; }

		/* --Pagination paramerters -- */
			// Number of Members in table
			$totalmembers = $wpdb->query($query);
			//$wpdb->print_error();

			//How many to display per page
			$perpage = 25;

			//Which page is this?
			$paged = $this->get_pagenum();

			//Page Number
			if(empty($paged) || !is_numeric($paged) || $paged<=0 ){ $paged=1; }

			//How many pages do we have in total?
			$totalpages = ceil($totalmembers/$perpage);

			//adjust the query to take pagination into account
			if(!empty($paged) && !empty($perpage)){
				$offset=($paged-1)*$perpage;
				$query.=' LIMIT '.(int)$offset.','.(int)$perpage;
			}

		/* -- Register Pagination -- */
			$this->set_pagination_args( array(
				"total_items"	=> $totalmembers,
				"total_pages"	=> $totalpages,
				"per_page"		=> $perpage,
			));

		/* -- Fetch the Members -- */
			$members = $wpdb->get_results($query);
			$this->items = $members;
			//$wpdb->print_error();
	}
}
/*-----------------------------------------------
 *
 * End of Class
 *
 *----------------------------------------------*/


function create_ctxphc_members_temp_table() {
    global $wpdb;
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

    $table_name = $wpdb->prefix . "members_temp";

    //Drop the members temp table if it still exists
    $query = "DROP TABLE IF EXISTS $table_name";
    dbDelta( $query );

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
	    ID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	    memb_id mediumint(9) NOT NULL,
	    memb_fname varchar(55) NOT NULL,
	    memb_lname varchar(75) NOT NULL,
	    memb_bday varchar(10),
	    memb_email varchar(255),
	    memb_renewal_date datetime
	)";

    dbDelta( $sql );

	/* $connection = mysql_connect('localhost', 'ctxphcco_admin', 'P@rr0theads') or die('Could not connect to the database: ' . mysql_error());
	mysql_select_db('ctxphcco_wp_db') or die("Can not connect.");

	//Drop the members temp table if it still exists
	$query = "DROP TABLE IF EXISTS ctxphc_ctxphc_members_temp";
	$results = mysql_query($query, $connection);
	$tempresult = mysql_query($results);

	//Create the members temp table if it hasn't been created already(shouldn't be created at this point)
	$query = "CREATE TABLE IF NOT EXISTS ctxphc_ctxphc_members_temp (
		ID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
		memb_id mediumint(9) NOT NULL,
		memb_fname varchar(55) NOT NULL,
		memb_lname varchar(75) NOT NULL,
		memb_bday varchar(10),
		memb_email varchar(255),
		memb_renewal_date datetime
	)";
	$results = mysql_query($query, $connection);
	$tempresult = mysql_query($results); */
}

function build_ctxphc_members_temp_table() {
	global $wpdb;
	$wpdb->show_errors();

	create_ctxphc_members_temp_table();

	$membCount = 0;

	$primMembers = $wpdb->get_results("SELECT memb_id, memb_fname, memb_lname, memb_bday_month, memb_bday_day, memb_email, memb_renewal_date FROM ctxphc_ctxphc_members");
	//$wpdb->print_error();

	if ($primMembers) {
		foreach ( $primMembers as $primMember ) {
			$membCount = $membCount + 1;
			$pMembCount = $pMembCount + 1;
			$primMemberID = $primMember->memb_id;
			$primMembBDay = $primMember->memb_bday_month . '/' . $primMember->memb_bday_day;

			//echo "<div>primMembBDay = {$primMembBDay}</div>";
			//Insert primary members data into temp members table
			$wpdb->insert('ctxphc_ctxphc_members_temp',
				array(
					'memb_id' 			=> $primMember->memb_id,
					'memb_fname'		=> $primMember->memb_fname,
					'memb_lname'		=> $primMember->memb_lname,
					'memb_bday'			=> $primMembBDay,
					'memb_email'		=> $primMember->memb_email,
					'memb_renewal_date'	=> $primMember->memb_renewal_date
				)
			);
			//$wpdb->print_error();

			//Select from ctxphc_ctxphc_memb_spouses table where spouses memb_id  = primary members memb_id
			//If it finds a record add it to the temp table for displaying later.
			$spMember = $wpdb->get_row("SELECT sp_fname, sp_lname, sp_bday_month, sp_bday_day, sp_email, sp_phone, memb_id
					FROM ctxphc_ctxphc_memb_spouses WHERE memb_id = {$primMemberID}");

			if ( $spMember ) {
				$membCount++;
				$spMembBDay = $spMember->sp_bday_month . '/' . $spMember->sp_bday_day;
				//echo "<div>spMembBDay = {$spMembBDay}</div>";
				$wpdb->insert('ctxphc_ctxphc_members_temp',
					array(
						'memb_id' 			=> $spMember->memb_id,
						'memb_fname'		=> $spMember->sp_fname,
						'memb_lname'		=> $spMember->sp_lname,
						'memb_bday'			=> $spMembBDay,
						'memb_email'		=> $spMember->sp_email,
						'memb_renewal_date'	=> $spMember->sp_renewal_date
					)
				);
				//$wpdb->print_error();
			}

			//Select from ctxphc_ctxphc_family_members table where family members memb_id  = primary members memb_id
			//If it finds a record add it to the temp table for displaying later.
			$famListing = $wpdb->get_results("SELECT fam_fname, fam_lname, fam_bday_month, fam_bday_day, fam_email, memb_id
				FROM ctxphc_ctxphc_family_members WHERE memb_id = {$primMemberID} ORDER BY fam_fname");

			If ($famListing) {
				foreach ( $famListing as $famMember ) {
					$membCount++;
					$famMembBDay = $famMember->fam_bday_month . '/' . $famMember->fam_bday_day;

					$wpdb->insert('ctxphc_ctxphc_members_temp',
						array(
							'memb_id' 			=> $famMember->memb_id,
							'memb_fname'		=> $famMember->fam_fname,
							'memb_lname'		=> $famMember->fam_lname,
							'memb_bday'			=> $famMembBDay,
							'memb_email'		=> $famMember->fam_email,
							'memb_renewal_date'	=> $famMember->fam_renewal_date
						)
					);
				//$wpdb->print_error();
				}
			}
		}
	}
}







function drop_ctxphc_members_temp_table() {
    mysql_query("DROP TABLE IF EXISTS 'ctxphc_wp_db'.'ctxphc_ctxphc_members_temp'") or die(mysql_error());
}

/* function ctxphc_admin_menus() {
    // Main CTXPHC Site Administation Dashboard Menu
    add_menu_page('CTXPHC Site Administation Dashboard', 'CTXPHC Dashboard', 'manage_options', 'ctxphc_admin_dashboard', 'render_ctxphc_admin_dashboard', '', 3);
    add_submenu_page('ctxphc_admin_dashboard','Member List','Member List','manage_options','ctxphc_membership_listing', 'render_membership_listing');
    add_submenu_page('ctxphc_admin_dashboard','Parrot Points', 'Parrot Points','manage_options','ctxphc_parrot_points', 'render_ctxph_parrot_points');
    add_submenu_page('ctxphc_admin_dashboard','Pirates Ball', 'Pirates Ball','manage_options','ctxphc_pirates_ball_attendees_listing', 'render_pb_attendees_listing');
}  */

// add_action('admin_menu','ctxphc_admin_menus');

function count_active_singles( ) {
	global $wpdb;
	$ActSingles = $wpdb->get_var( $wpdb->prepare( "select count(memb_id) from ctxphc_ctxphc_members where memb_type=%s", 1));
	return $ActSingles;
}

function count_active_couples( ) {
	global $wpdb;
	$tSpouses = $wpdb->get_var( $wpdb->prepare("SELECT COUNT( DISTINCT b.sp_id ) FROM ctxphc_ctxphc_members a JOIN ctxphc_ctxphc_memb_spouses b ON a.memb_id = b.memb_id WHERE a.memb_type =%s", 2));
	$tCoupMembers = $wpdb->get_var( $wpdb->prepare( "select count(distinct memb_id) from ctxphc_ctxphc_members where memb_type = %s",2));
	$ActCouples = $tSpouses + $tCoupMembers;
	return $ActCouples;
}

function count_active_families() {
	global $wpdb;
	$tFamily = $wpdb->get_var( $wpdb->prepare("select count(distinct fam_id) from ctxphc_ctxphc_family_members a JOIN ctxphc_ctxphc_members b on a.memb_id = b.memb_id WHERE b.memb_type=%s",3));
	$tFamSpouses = $wpdb->get_var( $wpdb->prepare("select count(distinct a.sp_id) from ctxphc_ctxphc_memb_spouses a JOIN ctxphc_ctxphc_members b on a.memb_id = b.memb_id WHERE b.memb_type=%s",3));
	$tFamMembers = $wpdb->get_var( $wpdb->prepare( "select count(distinct a.memb_id) from ctxphc_ctxphc_members a JOIN ctxphc_ctxphc_family_members b on a.memb_id = b.memb_id where a.memb_type=%s",3));
	$ActFamily = $tFamily + $tFamSpouses + $tFamMembers;
	return $ActFamily;
}

//Function to display the CTXPHC Site Administation Dashboard
function render_ctxphc_admin_dashboard() {
    include ('render_ctxphc_admin_dashboard.php');
}

//Function to display the membership listing
function render_membership_listing() {
    include ('render_membership_listing.php');
}

function ctxph_edit_membership($membid) {
    include ('render_edit_membership.php');
}

function render_ctxph_parrot_points() {
    include ('render_ctxphc_parrot_points.php');
}

function render_ctxph_pb_attendees() {
    include ('render_ctxph_pb_attendees.php');
}

function list_members() {
	global $wpdb;
	$members = $wpdb->get_row("select * from $wpdb->ctxphc_ctxphc_members");
}?>
