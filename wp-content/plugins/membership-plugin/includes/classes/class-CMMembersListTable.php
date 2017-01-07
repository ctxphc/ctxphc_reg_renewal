<?php
/**
 * Created by PhpStorm.
 * User: ken_kilgore1
 * Date: 5/3/2015
 * Time: 8:20 PM
 *
 * Members List Table class.
 *
 * @since 3.1.0
 * @access private
 *
 * @package WordPress
 * @subpackage List_Table
 */
namespace Membership\Classes;


use WP_List_Table;
use WP_User_Query;

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}


class CM_Members_List_Table extends WP_List_Table {

	/**
	 * Site ID to generate the Members list table for.
	 *
	 * @since 3.1.0
	 * @access public
	 * @var int
	 */
	public $site_id;

	/**
	 * Whether or not the current Members list table is for Multisite.
	 *
	 * @since 3.1.0
	 * @access public
	 * @var bool
	 */
	public $is_site_members;

	/**
	 * array of possible member statuses.
	 * Used to determine the correct member listing to display
	 *
	 * @since 1.0
	 * @access public
	 * @var array
	 */
	public $statuses;

	/**
	 * Member Status ID to generate the Members list table for.
	 *
	 * @since 1.1.0
	 * @access public
	 * @var int
	 */
	public $status_id;

	/**
	 * Member Status Name.
	 *
	 * @since 1.1.0
	 * @access public
	 * @var string
	 */
	public $memb_status;

	/**
	 * @var array
	 */
	private $args;


	/**
	 * Constructor will create the menu item
	 */
	public function __construct( $args = array() ) {
		if ( isset( $args[ 'memb_statuses' ] ) ) {
			$this->statuses = $args[ 'memb_statuses' ];
		}

		if ( isset( $args[ 'list_type' ] ) ) {
			$this->list_type = $args[ 'list_type' ];
		}

		if ( isset( $args[ 'screen' ] ) ) {
			$this->screen = convert_to_screen( $args[ 'screen' ] );
		} else {
			$this->screen = get_current_screen();
		}


		add_filter( "manage_{$this->screen->id}_columns", array( $this, 'get_columns' ), 0 );


		parent::__construct( array(
				'singular' => 'member',
				'plural'   => 'members',
				'screen'   => $this->screen,
			)
		);


		foreach ( $args[ 'memb_statuses' ] as $mid => $mstatus ) {
			if ( $mstatus->memb_status == $args[ 'list_type' ] ) {
				$this->status_id   = $mstatus->ID;
				$this->memb_status = $mstatus->memb_status;
			}
		}
	}


	/**
	 * Prepare the items for the table to process
	 *
	 * @param $args
	 */
	public function prepare_items( $args ) {
		global $membersearch, $_column_headers;

		$columns  = $this->get_columns();
		$hidden   = $this->get_hidden_columns();
		$sortable = $this->get_sortable_columns();

		$this->_column_headers = $_column_headers = array( $columns, $hidden, $sortable );

		$membersearch = isset( $_REQUEST[ 's' ] ) ? wp_unslash( trim( $_REQUEST[ 's' ] ) ) : '';

		/**
		 * Get page options
		 */
		$per_page         = ( $this->is_site_members ) ? 'site_members_network_per_page' : 'members_per_page';
		$members_per_page = $this->get_items_per_page( $per_page );
		$paged            = $this->get_pagenum();

		/* Preparing the query of the members for the type of report to be generated */
		$query_args = array(
			'number'     => $members_per_page,
			'offset'     => ( $paged - 1 ) * $members_per_page,
			'status_id'  => $this->status_id,
			'search'     => $membersearch,
			'fields'     => 'all_with_meta',
			'orderby'    => 'last_name',
			'meta_query' => array(
				'meta_key'   => 'status_id',
				'meta_value' => $this->status_id,
			),
		);

		if ( '' !== $query_args[ 'search' ] ) {
			$query_args[ 'search' ] = '*' . $query_args[ 'search' ] . '*';
		}

		if ( isset( $_REQUEST[ 'orderby' ] ) ) {
			$query_args[ 'orderby' ] = $_REQUEST[ 'orderby' ];
		}

		if ( isset( $_REQUEST[ 'order' ] ) ) {
			$query_args[ 'order' ] = $_REQUEST[ 'order' ];
		}

		// Fetch the member IDs for this page
		$cm_active_members = new WP_User_Query( $query_args );

		$this->items = $cm_active_members->get_results();

		$this->set_pagination_args( array(
				'total_items' => $cm_active_members->get_total(),
				'per_page'    => $members_per_page,
			)
		);
	}


	/**
	 * Override the parent columns method. Defines the columns to use in your listing table
	 *
	 * @return Array
	 */
	public function get_columns() {
		$columns = array(
			'cb'         => '<input type="checkbox" />',
			'first_name' => __( 'First' ),
			'last_name'  => __( 'Last' ),
			'email'      => __( 'E-mail' ),
			'phone'      => __( 'Phone' ),
			'hatch_date' => __( 'Hatch Date' ),
			'tag_date'   => __( 'Tag Date' ),
			'addr1'      => __( 'Address' ),
			'addr2'      => __( 'Suit/Apt' ),
			'city'       => __( 'City' ),
			'state'      => __( 'State' ),
			'zip'        => __( 'Zip' ),
		);

		return $columns;
	}


	/**
	 * Define which columns are hidden
	 *
	 * @return Array
	 */
	public function get_hidden_columns() {
		$hidden_columns = array(
			'addr1',
			'addr2',
			'city',
			'state',
			'zip',
		);

		return $hidden_columns;
	}


	/**
	 * Define the sortable columns
	 *
	 * @return Array
	 */
	public function get_sortable_columns() {
		$sortable_columns = array(
			'first_name' => array( 'first_name', false ),
			'last_name'  => array( 'last_name', true ),
			'email'      => array( 'email', false ),
			'hatch_date' => array( 'hatch_date', false ),
			'tag_date'   => array( 'tag_date', false ),
		);

		return $sortable_columns;
	}


	/**
	 * Output 'no members' message.
	 *
	 * @since 3.1.0
	 * @access public
	 */
	public function no_items() {
		_e( 'No matching members were found.' );
	}


	function column_first_name( $user_object, $style = '' ) {

		$column_name = 'first_name';

		if ( ! ( is_object( $user_object ) && is_a( $user_object, 'WP_User' ) ) ) {
			$user_object = get_userdata( (int) $user_object );
		}

		$edit_link    = esc_url( add_query_arg( 'wp_http_referer', urlencode( wp_unslash( $_SERVER[ 'REQUEST_URI' ] ) ), get_edit_member_link( $user_object->ID ) ) );
		$archive_link = esc_url( add_query_arg( 'wp_http_referer', urlencode( wp_unslash( $_SERVER[ 'REQUEST_URI' ] ) ), get_archive_member_link( $user_object->ID ) ) );

		$actions = array(
			'edit'    => '<a href="' . $edit_link . '">' . __( 'Edit' ) . '</a>',
			'archive' => '<a href="' . $archive_link . '">' . __( 'Archive' ) . '</a>',
		);

		$row_actions = "<strong><a href=\"$edit_link\">$user_object->first_name</a></strong><br/>";

		/**
		 * Filter the action links displayed under each user in the Members list table.
		 *
		 * @since 2.8.0
		 *
		 * @param array $actions An array of action links to be displayed.
		 * @param WP_User $user_object WP_User object for the currently-listed user.
		 */
		$actions = apply_filters( 'user_row_actions', $actions, $user_object );

		$row_actions .= $this->row_actions( $actions );

		//$class = "class=\"$column_name column-$column_name\"";

		//$style = '';

		//$attributes = "$class$style";
		$r = "$user_object->fist_name $row_actions";

		/**
		 * Filter the display output of custom columns in the Members list table.
		 *
		 * @since 2.8.0
		 *
		 * @param string $output Custom column output. Default empty.
		 * @param string $column_name Column name.
		 * @param int $user_id ID of the currently-listed user.
		 */
		//$r .= apply_filters( "manage_{$this->screen->id}_columns", '', $column_name );
		//$r .= apply_filters( $this->screen->id, '', $column_name, $user_object->ID );

		return $r;
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
			'archive' => __( 'Archive' ),
			'edit'    => __( 'Edit' ),
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
		if ( 'archive' === $this->current_action() ) {
			wp_die( 'Items deleted (or they would be if we had items to delete)!' );
		}

	}

	/**
	 * Generates the columns for a single row of the table
	 *
	 * @since 3.1.0
	 * @access protected
	 *
	 * @param object $item The current item
	 */
	protected function single_row_columns( $item ) {
		list( $columns, $hidden ) = $this->get_column_info();

		foreach ( $columns as $column_name => $column_display_name ) {
			$class = "class='$column_name column-$column_name'";

			$style = '';
			if ( in_array( $column_name, $hidden ) ) {
				$style = ' style="display:none;"';
			}

			$attributes = "$class$style";

			if ( 'cb' == $column_name ) {
				echo '<th scope="row" class="check-column">';
				echo $this->column_cb( $item );
				echo '</th>';
			} elseif ( method_exists( $this, 'column_' . $column_name ) ) {
				echo "<td $attributes>";
				echo call_user_func( array( $this, 'column_' . $column_name ), $item );
				echo "</td>";
			} else {
				echo "<td $attributes>";
				echo $this->column_default( $item, $column_name );
				echo "</td>";
			}
		}
	}

	function column_cb( $user_object ) {
		$userid = $user_object->ID;

		return sprintf( '<input type="checkbox" name="member[]" value="%s" />', $userid );
	}


	/**
	 * Define what data to show on each column of the table
	 *
	 * @param  Array $item Data
	 * @param  String $column_name - Current column name
	 *
	 * @return Mixed
	 */
	public function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'cb':
			case 'first_name':
			case 'last_name':
			case 'email':
			case 'phone':
			case 'hatch_date':
			case 'tag_date':
				return $item->$column_name;

			default:
				return print_r( $item, true );
		}
	}


	/**
	 * Return an associative array listing all the views that can be used
	 * with this table.
	 *
	 * Provides a list of roles and user count for that role for easy
	 * filtering of the user table.
	 *
	 * @since  3.1.0
	 * @access protected
	 *
	 * @return array An array of HTML links, one for each view.
	 */
	protected
	function get_views() {

		$cm_rpts = array(
			'Active'   => 'Active',
			'Archived' => 'Archived',
			'Pending'  => 'Pending',
			'All'      => 'All',
		);

		$cm_rpt_counts = array(
			'Active'   => count_memb_status( 'Active' ),
			'Archived' => count_memb_status( 'Archived' ),
			'Pending'  => count_memb_status( 'Pending' ),
			'All'      => count_memb_status( 'Active' ) +
			              count_memb_status( 'Archived' ) +
			              count_memb_status( 'Pending' ),
		);

		$cm_rpt_links = array();

		foreach ( $cm_rpts as $cm_rpt_id => $cm_rpt_name ) {
			$class = '';

			switch ( $cm_rpt_name ){
				case 'Active':
					$cm_page_value = 'active_members';
					break;
				case 'Pending':
					$cm_page_value = 'pending_members';
					break;
				case 'Archived':
					$cm_page_value = 'archived_members';
					break;
				case 'All':
					$cm_page_value = 'all_members';
					break;
			}

			$url = '/wp-admin/admin.php?page=' . $cm_page_value . '&cm_status=' . $cm_rpt_name;

			if ( $cm_rpt_name == $this->memb_status ) {
				$class = ' class="current"';
			}

			$cm_rpt_name = __( $cm_rpt_name );

			$cm_rpt_name_count = sprintf( __( '%1$s <span class="count">(%2$s)</span>' ), $cm_rpt_name, number_format_i18n( $cm_rpt_counts[ $cm_rpt_name ]
			) );

			$cm_rpt_links[ $cm_rpt_name ] = "<a href='" . esc_url( add_query_arg( 'cm_status', $cm_rpt_name, $url ) ) . "'$class>$cm_rpt_name_count</a>";
		}

		return $cm_rpt_links;
	}
}