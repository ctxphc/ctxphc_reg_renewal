<?php

/**
 * Active Members List Table class.
 *
 * @since 3.1.0
 * @access private
 *
 * @package WordPress
 * @subpackage List_Table
 */

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class CM_Active_Members_List_Table extends WP_List_Table {

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
	 * @var array
	 */
	private $args;

	/**
	 * Constructor.
	 *
	 * @since 3.1.0
	 * @access public
	 *
	 * @see WP_List_Table::__construct() for more information on default arguments.
	 *
	 * @param array $args An associative array of arguments.
	 */
	public function __construct( $args = array() ) {
		$this->args = $args;
		If ( isset( $args['screen'] ) && ! empty( $args['screen']) ){
			$this->screen = get_current_screen();
		}

		parent::__construct( array(
				'singular' => 'member',
				'plural'   => 'members',
				'screen'   => isset( $args[ 'screen' ] ) ? $args[ 'screen' ] : null,
			)
		);

		$this->is_site_members = 'site-members-network' == $this->screen->id;

		if ( $this->is_site_members ) {
			$this->site_id = isset( $_REQUEST[ 'id' ] ) ? intval( $_REQUEST[ 'id' ] ) : 0;
		}

	}


	/**
	 * Check the current user's permissions.
	 *
	 * @since 3.1.0
	 * @access public
	 */
	public function ajax_user_can() {
		if ( $this->is_site_members ) {
			return current_user_can( 'manage_sites' );
		} else {
			return current_user_can( 'list_users' );
		}
	}

	/**
	 * Add extra markup in the toolbars before or after the list
	 *
	 * @param string $which , helps you decide if you add the markup after (bottom) or before (top) the list
	 */
	function extra_tablenav( $which ) {
		if ( $which == "top" ) {
			//The code that goes before the table is here
			echo "Hello, I'm before the table";
		}
		if ( $which == "bottom" ) {
			//The code that goes after the table is there
			echo "Hi, I'm after the table";
		}
	}

	/**
	 * Prepare the members list for display.
	 *
	 * @since 3.1.0
	 * @access public
	 */
	public function prepare_items( $args ) {
		global $membersearch, $column_headers;

		$membersearch = isset( $_REQUEST[ 's' ] ) ? wp_unslash( trim( $_REQUEST[ 's' ] ) ) : '';

		/**
		 * Get page options
		 */
		$per_page         = ( $this->is_site_members ) ? 'site_members_network_per_page' : 'members_per_page';
		$members_per_page = $this->get_items_per_page( $per_page );
		$paged            = $this->get_pagenum();

		/**
		 * Preparing the query of the members for the type of report to be generated
		 */
		$query_args = array(
			'number'     => $members_per_page,
			'offset'     => ( $paged - 1 ) * $members_per_page,
			'status_id'  => 1, // TODO:  set this to a argument passed via $args
			'search'     => $membersearch,
			'fields'     => 'all_with_meta',
			'orderby'    => 'last_name',
			'meta_query' => array(
				'meta_key'   => 'memb_status',
				'meta_value' => $args[ 'status' ],
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

		//usort( $this->items, array( &$this, 'sort_data' ) );

		$this->set_pagination_args( array(
				'total_items' => $cm_active_members->get_total(),
				'per_page'    => $members_per_page,
			)
		);

		/* Register the Columns */
		$columns  = $this->get_columns();
		$hidden   = $this->get_hidden_columns();
		$sortable = $this->get_sortable_columns();

		$this->_column_headers = array( $columns, $hidden, $sortable );

		$column_headers[ $this->screen->id ] = array(
			'columns'  => $columns,
			'hidden'   => $hidden,
			'sortable' => $sortable,
		);
	}

	/**
	 * Get a list of columns for the list table.
	 *
	 * @since  1.1.1
	 * @access public
	 *
	 * @return array Array in which the key is the ID of the column,
	 *               and the value is the description.
	 */
	public function get_columns() {
		$c = array(
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

		return $c;
	}

	public function get_hidden_columns() {
		return array(
			'addr1',
			'addr2',
			'city',
			'state',
			'zip',
		);
	}

	/**
	 * Get a list of sortable columns for the list table.
	 *
	 * @since 1.1.1
	 * @access protected
	 *
	 * @return array Array of sortable columns.
	 */
	protected function get_sortable_columns() {
		$c = array(
			'first_name' => array( 'first_name', false ),
			'last_name'  => array( 'last_name', true ),
			'email'      => array( 'email', false ),
			'hatch_date' => array( 'hatch_date', false ),
			'state'      => array( 'state', false ),
			'zip'        => array( 'zip', false ),
		);

		return $c;
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

	/**
	 * Capture the bulk action required, and return it.
	 *
	 * Overridden from the base class implementation to capture
	 * the role change drop-down.
	 *
	 * @since  3.1.0
	 * @access public
	 *
	 * @return string The bulk action required.
	 */
	public function current_action() {
		if ( isset( $_REQUEST[ 'archive' ] ) && ! empty( $_REQUEST[ 'member' ] ) ) {
			return 'archive';
		}

		return parent::current_action();
	}

	/**
	 * Generate the list table rows.
	 *
	 * @since 3.1.0
	 * @access public
	 */
	public function display_rows() {
		// Format the date fields
		if ( ! $this->is_site_members ) {
			//todo: format date fields for display
			//$hatch_date = get_date_ready_for_display( array_keys( $this->items ) );
		}

		$style = '';

		foreach ( $this->items as $userid => $user_object ) {
			$style = ( ' class="alternate"' == $style ) ? '' : ' class="alternate"';
			//echo "\n\t" . $this->single_row( $user_object, $style, isset( $post_counts ) ? $post_counts[ $userid ]
			// : 0 );
			echo "\n\t" . $this->single_row( $user_object, $style );
		}
	}

	/**
	 * Generate HTML for a single row on the members list table.
	 *
	 * @since 3.1.0
	 * @access public
	 *
	 * @param object $user_object The current user object.
	 * @param string $style Optional. Style attributes added to the `<tr>` element.
	 *                            Must be sanitized. Default empty.
	 * @param string $role Optional. Key for the $wp_roles array. Default empty.
	 *
	 * @return string Output for a single row.
	 */
	function single_row( $user_object, $style = '' ) {
		global $column_headers;
		if ( ! ( is_object( $user_object ) && is_a( $user_object, 'WP_User' ) ) ) {
			$user_object = get_userdata( (int) $user_object );
		}

		$user_object->filter = 'display';

		$email = $user_object->user_email;

		$checkbox = '';

		// Check if the user for this row is editable
		if ( current_user_can( 'list_users' ) ) {
			// Set up the user editing link
			$edit_link    = esc_url( add_query_arg( 'wp_http_referer', urlencode( wp_unslash( $_SERVER[ 'REQUEST_URI' ] ) ), get_edit_member_link( $user_object->ID ) ) );
			$archive_link = esc_url( add_query_arg( 'wp_http_referer', urlencode( wp_unslash( $_SERVER[ 'REQUEST_URI' ] ) ), get_archive_member_link( $user_object->ID ) ) );

			// Set up the hover actions for this user
			$actions = array();

			if ( current_user_can( 'edit_user', $user_object->ID ) ) {
				$edit = "<strong><a href=\"$edit_link\">$user_object->first_name</a></strong><br/>";

				$actions[ 'edit' ] = '<a href="' . $edit_link . '">' . __( 'Edit' ) . '</a>';
				$actions[ 'archive' ] = '<a href="' . $archive_link . '">' . __( 'Archive' ) . '</a>';
			} else {
				$edit    = "<strong>$user_object->first_name</strong><br />";
			}

			/**
			 * Filter the action links displayed under each user in the Members list table.
			 *
			 * @since 2.8.0
			 *
			 * @param array $actions An array of action links to be displayed.
			 * @param WP_User $user_object WP_User object for the currently-listed user.
			 */
			$actions = apply_filters( 'user_row_actions', $actions, $user_object );

			$edit .= $this->row_actions( $actions );

			// Set up the checkbox ( because the user is editable, otherwise it's empty )
			$checkbox = '<label class="screen-reader-text" for="cb-select-' . $user_object->ID . '">' . sprintf( __(
					'Select %s' ), $user_object->first_name ) . '</label>'
			            . "<input type='checkbox' name='users[]' id='user_{$user_object->ID}' class='subscriber'
			            value='{$user_object->ID}' />";

		} else {
			$edit = '<strong>' . $user_object->first_name . '</strong>';
		}

		$r = "<tr id='user-$user_object->ID'$style>";

		foreach ( $columns as $column_name => $column_display_name ) {
			//do_action( 'add_debug_info', $column_display_name, 'column display name' );
			$class = "class=\"$column_name column-$column_name\"";

			$style = '';
			if ( in_array( $column_name, $column_headers[ $this->screen->id ][ 'hidden' ] ) ) {
				$style = ' style="display:none;"';
			}

			$attributes = "$class$style";

			switch ( $column_name ) {
				case 'cb':
					$r .= "<th scope='row' class='check-column'>$checkbox</th>";
					break;
				case 'first_name':
					$r .= "<td $attributes>$user_object->first_name $edit</td>";
					break;
				case 'last_name':
					$r .= "<td $attributes>$user_object->last_name $edit</td>";
					break;
				case 'email':
					$r .= "<td $attributes><a href='mailto:$email' title='" . esc_attr( sprintf( __( 'E-mail: %s' ), $email ) ) . "'>$email</a></td>";
					break;
				case 'phone':
					$r .= "<td $attributes>$user_object->phone</td>";
					break;
				case 'hatch_date':
					$r .= "<td $attributes>$user_object->hatch_date</td>";
					break;
				case 'tag_date':
					$r .= "<td $attributes>$user_object->tag_date</td>";
					break;
				case 'addr1':
					$r .= "<td $attributes>$user_object->addr1</td>";
					break;
				case 'addr2':
					$r .= "<td $attributes>$user_object->addr2</td>";
					break;
				case 'city':
					$r .= "<td $attributes>$user_object->city</td>";
					break;
				case 'state':
					$r .= "<td $attributes>$user_object->state</td>";
					break;
				case 'zip':
					$r .= "<td $attributes>$user_object->zip</td>";
					break;
				default:
					$r .= "<td $attributes>";

					/**
					 * Filter the display output of custom columns in the Members list table.
					 *
					 * @since 2.8.0
					 *
					 * @param string $output Custom column output. Default empty.
					 * @param string $column_name Column name.
					 * @param int $user_id ID of the currently-listed user.
					 */
					//$column_headers[ $screen->id ] = apply_filters( "manage_{$screen->id}_columns", array() );
					//$r .= apply_filters( "manage_{$this->screen->id}_columns", '', $column_name );
					//$r .= apply_filters( $this->screen->id, '', $column_name, $user_object->ID );
					$r .= "</td>";
			}
		}
		$r .= '</tr>';

		return $r;
	}

	/**
	 * Get a list of all, hidden and sortable columns, with filter applied
	 *
	 * @since 3.1.0
	 * @access protected
	 *
	 * @return array
	 */
	protected function get_column_info() {
		global $column_headers;
		if ( empty( $column_headers[ $this->screen->id ] ) ) {
			$columns  = $this->get_columns();
			$hidden   = $this->get_hidden_columns();
			$sortable = $this->get_sortable_columns();
		} else {
			$columns  = $column_headers[ $this->screen->id ][ 'columns' ];
			$hidden   = $column_headers[ $this->screen->id ][ 'hidden' ];
			$sortable = $column_headers[ $this->screen->id ][ 'sortable' ];
		}

		return array( $columns, $hidden, $sortable );
	}

	/**
	 * Return an associative array listing all the views that can be used
	 * with this table.
	 *
	 * Provides a list of status names and member count for each status type for easy
	 * filtering of the member table.
	 *
	 * @since  3.1.0
	 * @access protected
	 *
	 * @return array An array of HTML links, one for each view.
	 */
	protected function get_views() {
		global $memb_statuses;

		$status_links = array();
		foreach ( $memb_statuses as $status_type ) {
			$page_link = strtolower( $status_type->memb_status ) . '_members';

			$status_links[ strtolower( $status_type->memb_status ) ] = '<a href="?page=' . $page_link . '&status=' . $status_type->ID . '">' . $status_type->memb_status . '</a>';
		}

		return $status_links;
	}

	/**
	 * Retrieve an associative array of bulk actions available on this table.
	 *
	 * @since  3.1.0
	 * @access protected
	 *
	 * @return array Array of bulk actions.
	 */
	protected function get_bulk_actions() {
		$actions = array();

		if ( current_user_can( 'delete_users' ) ) {
			$actions[ 'activate' ] = __( 'Activate' );
			$actions[ 'archive' ]  = __( 'Archive' );
		}

		return $actions;
	}

	/**
	 * Allows you to sort the data by the variables set in the $_GET
	 *
	 * @return Mixed
	 */
	private function sort_data( $a, $b ) {
		// Set defaults
		$orderby = 'last_name';
		$order   = 'asc';

		// If orderby is set, use this as the sort column
		if ( ! empty( $_GET[ 'orderby' ] ) ) {
			$orderby = $_GET[ 'orderby' ];
		}

		// If order is set use this as the order
		if ( ! empty( $_GET[ 'order' ] ) ) {
			$order = $_GET[ 'order' ];
		}

		$result = strnatcmp( $a[ $orderby ], $b[ $orderby ] );

		if ( $order === 'asc' ) {
			return $result;
		}

		return - $result;
	}
}
