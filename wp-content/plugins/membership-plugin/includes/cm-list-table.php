<?php
/**
 * Helper functions for displaying a list of items in an ajaxified HTML table.
 *
 * @package Club Membership Plugin
 * @subpackage CM_List_Table
 * @since 1.1.1
 */

global $debug_errors;
/**
 * Fetch an instance of a CM_List_Table class.
 *
 * @access private
 * @since 1.1.1
 *
 * @param string $class The type of the list table, which is the class name.
 * @param array $args Optional. Arguments to pass to the class. Accepts 'screen'.
 *
 * @return object|bool Object on success, false if the class does not exist.
 */
function _get_cm_list_table( $class, $args = array() ) {

	$core_classes = array(
		//Site Admin
		'Active'  => 'CM_Active_Members_List_Table',
		'Archive' => 'CM_Archived_Members_List_Table',
		'Pending' => 'CM_Pending_Members_List_Table',
		'All'     => 'CM_All_Members_List_Table',
	);

	if ( isset( $core_classes[ $class ] ) ) {
		foreach ( (array) $core_classes[ $class ] as $cm_list_table_class ) {
			require_once( CM_CLASSES . 'class-cm-' . $class . '-members-list-table.php' );
		}

		if ( isset( $args[ 'screen' ] ) ) {
			$args[ 'screen' ] = convert_to_screen( $args[ 'screen' ] );
		} elseif ( isset( $GLOBALS[ 'hook_suffix' ] ) ) {
			$args[ 'screen' ] = get_current_screen();
		} else {
			$args[ 'screen' ] = null;
		}

		return new $cm_list_table_class( $args );
	}

	return false;
}

/**
 * Register column headers for a particular screen.
 *
 * @since 2.7.0
 *
 * @param string $screen The handle for the screen to add help to. This is usually the hook name returned by the add_*_page() functions.
 * @param array $columns An array of columns with column IDs as the keys and translated column names as the values
 *
 * @see get_column_headers(), print_column_headers(), get_hidden_columns()
 */
function cm_register_column_headers( $screen, $columns ) {
	$cm_list_table = new _CM_List_Table_Compat( $screen, $columns );
}

/**
 * Prints column headers for a particular screen.
 *
 * @since 2.7.0
 */
function cm_print_column_headers( $screen, $id = true ) {
	$cm_list_table = new _CM_List_Table_Compat( $screen );

	$cm_list_table->print_column_headers( $id );
}

/**
 * Helper class to be used only by back compat functions
 *
 * @since 3.1.0
 */
class _CM_List_Table_Compat extends CM_List_Table {
	public $_screen;
	public $_columns;

	public function __construct( $screen, $columns = array() ) {
		if ( is_string( $screen ) ) {
			$screen = convert_to_screen( $screen );
		}

		$this->_screen = $screen;

		if ( ! empty( $columns ) ) {
			$this->_columns = $columns;
			add_filter( 'manage_' . $screen->id . '_columns', array( $this, 'get_columns', ), 0 );
		}
	}

	public function get_columns() {
		return $this->_columns;
	}

	protected function get_column_info() {
		$columns  = get_column_headers( $this->_screen );
		$hidden   = get_hidden_columns( $this->_screen );
		$sortable = array();

		return array( $columns, $hidden, $sortable, );
	}
}

//$debug_errors[] = wp_debug_backtrace_summary();
