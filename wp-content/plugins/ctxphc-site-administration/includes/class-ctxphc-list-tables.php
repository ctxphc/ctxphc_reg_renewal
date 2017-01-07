<?php

/*************************** LOAD THE BASE CLASS *******************************
 *******************************************************************************
 * The WP_List_Table class isn't automatically available to plugins, so we need
 * to check if it's available and load it if necessary.
 ******************************************************************************/
if (! class_exists('WP_List_Table')) {
    require_once (ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

// CTXPHC Membership Listing Requirements
require_once ('ctxphc-membership-functions.php');

/**
 * ********************************************************************************************
 * create my extention to the WP-List-Table class
 * *********************************************************************************************
 */
class CTXPHC_List_Table extends WP_List_Table
{

    /**
     * *************************************************************************
     * REQUIRED.
     * Set up a constructor that references the parent constructor. We
     * use the parent reference to set some default configs. We overfide the
     * parent to pass our own arguments. We will focus on three parameters:
     * Singular and plural labels, as well as whether the class supports AJAX.
     * *************************************************************************
     */
    function __construct()
    {
        global $status, $page;

        // Set parent defaults
        parent::__construct(array(
            'singular' => 'member', // singular name of the listed records
            'plural' => 'members', // plural name of the listed records as well as one of the table css classes
            'ajax' => false
        )) // does this table support ajax?
;
    }

    function extra_table_nav($which)
    {
        if ($which === "top") {
            // The code that goes before the table
            echo "Hi, I'm before the table";
        }
        if ($which === "bottom") {
            // The code that goes after the table
            echo "Hi, I'm after the table";
        }
    }

    function column_default($item, $column_name)
    {
        switch ($column_name) {
            case 'col_first_name':
                return $item->$column_name;
            case 'col_last_name':
                return $item->$column_name;
            case 'col_email':
                return $item->$column_name;
            case 'col_phone':
                return $item->$column_name;
            case 'col_bday':
                return $item->$column_name;
            case 'col_hatch_date':
                return $item->$column_name;
            case 'col_addr':
                return $item->$column_name;
            default:
                echo $column_name . " ";
                return print_r($item, true); // Show the whole array for troubleshooting purposes
        }
    }

    function column_col_first_name($item)
    {
        // Build row actions
        $actions = array(
            'edit' => sprintf('<a href="?page=%s&action=%s&member=%s">Edit</a>', $_REQUEST['page'], 'edit', $item->id),
            'archive' => sprintf('<a href="?page=%s&action=%s&member=%s">Archive</a>', $_REQUEST['page'], 'archive', $item->id),
            'renew' => sprintf('<a href="?page=%s&action=%s&member=%s">Renew</a>', $_REQUEST['page'], 'renew', $item->id)
        );

        // Return the title contents
        return sprintf('%1$s %2$s',
              /*$1%s*/ $item->first_name,
              /*$2%s*/ $this->row_actions($actions));
    }

    function column_col_last_name($item)
    {
        // Return the title contents
        return sprintf('%1$s',
               /*$1%s*/ $item->last_name);
    }

    function column_col_email($item)
    {
        // Return the title contents
        return sprintf('%1$s',
               /*$1%s*/ $item->email);
    }

    function column_col_bday($item)
    {
        // Return the title contents
        return sprintf('%1$s',
               /*$1%s*/ $item->bday);
    }

    function column_col_hatch_date($item)
    {
        // Return the title contents
        return sprintf('%1$s',
               /*$1%s*/ $item->hatch_date);
    }

    function column_col_addr($item)
    {
        // Return the title contents
        return sprintf('%1$s %2$s',
                /*$1%s*/ $item->address1,
                /*$2%s*/ $item->address2);
    }

    function column_col_phone($item)
    {
        // Return the title contents
        return sprintf('%1$s',
               /*$1%s*/ $item->phone);
    }

    /**
     * ************************************************************************
     * REQUIRED if displaying checkboxes or using bulk actions! The 'cb' column
     * is given special treatment when columns are processed.
     * It ALWAYS needs to
     * have it's own method.
     * ************************************************************************
     *
     * @see WP_List_Table::::single_row_columns()
     * @param array $item
     *            A singular item (one full row's worth of data)
     * @return string Text to be placed inside the column <td> (movie title only)
     *
     */
    function column_cb($item)
    {
        return sprintf('<input type="checkbox" name="%1$s[]" value="%2$s" />',
	   /*$1%s*/ $this->_args['singular'], // Let's simply repurpose the table's singular label ("member")
        /* $2%s */
        $item->id); // The value of the checkbox should be the record's id

    }

    /**
     * ************************************************************************
     * REQUIRED! This method dictates the table's columns and titles.
     * This should
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
     *
     *         Define the columns that are going to be used in the table
     * @return array $columns, the array of columns to use with the table
     *         ************************************************************************
     */
    function get_columns()
    {
        return $columns = array(
            'cb' => __('<input type="checkbox" />'), // Render a checkbox instead of text
            'col_first_name' => __('First Name'),
            'col_last_name' => __('Last Name'),
            'col_email' => __('Email'),
            'col_phone' => __('Phone'),
            'col_addr' => __('Address')
        );
    }

    /**
     * **************************************************************************
     * Decide which columns to activate the sorting functionality on
     *
     * @return array $sortable, the array of columns that can be sorted by the user
     *
     *         Optional: If you want one or more columns to be sortable (ASC/DESC toggle),
     *         you will need to register it here. This should return an array where the
     *         key is the column that needs to be sortable, and the value is db column to
     *         sort by. Often, the key and value will be the same, but this is not always
     *         the case (as the value is a column name from the database, not the list table).
     *
     *         This method merely defines which columns should be sortable and makes them
     *         clickable - it does not handle the actual sorting. You still need to detect
     *         the ORDERBY and ORDER querystring variables within prepare_items() and sort
     *         your data accordingly (usually by modifying your query).
     *
     * @return array An associative array containing all the columns that should be sortable: 'slugs'=>array('data_values',bool)
     *         ************************************************************************
     */
    public function get_sortable_columns()
    {
        $sortable_columns = array(
            'col_first_name' => array(
                'first_name',
                true
            ),
            'col_last_name' => array(
                'last_name',
                true
            ),
            'col_email' => array(
                'email',
                true
            )
        );
        return $sortable_columns;
    }

    /**
     * ************************************************************************
     * Optional.
     * If you need to include bulk actions in your list table, this is
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
     *         ************************************************************************
     */
    function get_bulk_actions()
    {
        $actions = array(
            'archive' => 'Archive',
            'renew' => 'Renew'
        );
        return $actions;
    }

    /**
     * ************************************************************************
     * Optional.
     * You can handle your bulk actions anywhere or anyhow you prefer.
     * For this example package, we will handle it in the class to keep things
     * clean and organized.
     *
     * @see $this->prepare_items() ************************************************************************
     */
    function process_bulk_action()
    {
        // Detect when a bulk action is being triggered...
        if ('archive' === $this->current_action()) {
            foreach ($_GET['member'] as $m_key => $m_value) {
                error_log("!!!!!!   m_key = $m_key and m_value = $m_value    !!!!!!", 0);
                archive_member($m_value);
            }
            // wp_die('Members archived (or they would be if we had items to archive)!');
        } elseif ('Renew' === $this->current_action()) {
            wp_die('Members Renewed (or they would be if we had items to renew)!');
        }
    }

    /**
     * ************************************************************************
     * REQUIRED! This is where you prepare your data for display.
     * This method will
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
     *       ************************************************************************
     */
    function prepare_items()
    {
        global $wpdb, $wp_column_headers;

        /* -- Preparing the query -- */
        $query = "SELECT * FROM " . $wpdb->prefix . "members_listing";

        /* If the value is not NULL, do a search for it. */
        if ($search != NULL) {

            // Trim Search Term
            $search = trim($search);

            /* Notice how you can search multiple columns for your search term easily, and return one data set */
            $data = $wpdb->get_results($wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "members_listing WHERE `first_name` LIKE '%%%s%%' OR `last_name` LIKE '%%%s%%'", $search, $search), ARRAY_A);
        }

        /* -- Register the Columns -- */
        $columns = $this->get_columns();
        $hidden_columns = array(); // Include any hidden columns from the table in here.
        $sortable_columns = $this->get_sortable_columns();

        $this->_column_headers = array(
            $columns,
            $hidden_columns,
            $sortable_columns
        );

        /* -- Process Bulk Actions -- */
        $this->process_bulk_action();

        /* -- Checks sort ordering -- */
        $orderby = ! empty($_REQUEST["orderby"]) ? $_REQUEST['orderby'] : 'id, first_name'; // If no sort, default to first name(fname)
        $order = ! empty($_REQUEST['order']) ? $_REQUEST['order'] : 'asc'; // If no order, default to asc
        if (! empty($orderby) & ! empty($order)) {
            $query .= ' ORDER BY ' . $orderby . ' ' . $order;
        }

        /* --Pagination paramerters -- */
        // Number of Members in table
        $totalmembers = $wpdb->query($query);
        // $wpdb->print_error();

        // How many to display per page
        $perpage = 25;

        // Which page is this?
        $paged = $this->get_pagenum();

        // Page Number
        if (empty($paged) || ! is_numeric($paged) || $paged <= 0) {
            $paged = 1;
        }

        // How many pages do we have in total?
        $totalpages = ceil($totalmembers / $perpage);

        // adjust the query to take pagination into account
        if (! empty($paged) && ! empty($perpage)) {
            $offset = ($paged - 1) * $perpage;
            $query .= ' LIMIT ' . (int) $offset . ',' . (int) $perpage;
        }

        /* -- Register Pagination -- */
        $this->set_pagination_args(array(
            "total_items" => $totalmembers,
            "total_pages" => $totalpages,
            "per_page" => $perpage
        ));

        /* -- Fetch the Members -- */
        $members = $wpdb->get_results($query);
        $this->items = $members;
        // $wpdb->print_error();
    }
}
/*-----------------------------------------------
 *
 * End of Class
 *
 *----------------------------------------------*/
