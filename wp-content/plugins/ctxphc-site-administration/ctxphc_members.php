<?php
/**
 * CTXPHC Members administration panel.
 *
 */

/** WordPress Administration Bootstrap */
//require_once( dirname( __FILE__ ) . '/admin.php' );
require_once( ABSPATH . 'wp-admin/admin.php' );

//CTXPHC Membership Listing Requirements
require_once( 'includes/ctxphc-membership-functions.php');
include( 'includes/class-ctxphc-list-tables.php');

if ( ! current_user_can( 'manage_options' ) )
	wp_die( __( 'Cheatin&#8217; uh?' ) );

/******************************************************************************************************/
function ctxphc_donate_queue_stylesheet() {
	$styleurl = plugins_url('/css/styles.css', __FILE__);
	wp_register_style('ctxphc_donate_css', $styleurl);
	wp_enqueue_style('ctxphc_donate_css');
}
add_action('wp_enqueue_scripts', 'ctxphc_donate_queue_stylesheet');

/**********************************************************************************************************/
function ctxphc_donate_queue_admin_stylesheet() {
        $styleurl = plugins_url('/css/adminstyles.css', __FILE__);

        wp_register_style('ctxphc_donate_admin_css', $styleurl);
        wp_enqueue_style('ctxphc_donate_admin_css');
}
add_action('admin_print_styles', 'ctxphc_donate_queue_admin_stylesheet');

/******************************************************************************************************/
function ctxphc_donate_queue_scripts() {
	$load_in_footer = ( 'true' == get_option( 'ctxphc_donate_scripts_in_footer' ) );
	wp_enqueue_script( 'jquery' );
	$script_url = plugins_url( '/js/script.js', __FILE__ ); 
	wp_enqueue_script( 'ctxphc_donate_script', $script_url, array( 'jquery' ), false, $load_in_footer );

	$script_url = plugins_url( '/js/geo-selects.js', __FILE__ ); 
	wp_enqueue_script( 'ctxphc_donate_geo_selects_script', $script_url, array( 'jquery' ), false, $load_in_footer );

	// declare the URL to the file that handles the AJAX request (wp-admin/admin-ajax.php)
	wp_localize_script( 'ctxphc_donate_script', 'dgxDonateAjax',
		array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'nonce' => wp_create_nonce( 'dgx-donate-nonce' ),
			'postalCodeRequired' => ctxphc_donate_get_countries_requiring_postal_code()
		)
	);
}
add_action( 'wp_enqueue_scripts', 'ctxphc_donate_queue_scripts' );

/******************************************************************************************************/
function ctxphc_report_help(){
     add_screen_option( 'per_page', array('label' => _x( 'Members', 'members per page (screen options)' )) );
     add_screen_option( 'report_type', array('label' => __( 'Report', 'Select report type you wish to view (screen options)' )) );

     // contextual help - choose Help on the top right of admin panel to preview this.
     $screen_id = get_current_screen();
          $screen_id->add_help_tab( array(
          'id'      => 'ctxphc_reporting',
          'title'   => __('Reports'),
          'content' => '<p>' . __('This screen lists all the active CTXPHC members for your site.') . '</p>' .
                       '<p>' . __('To add a new member for your site, click the Add New button at the top of the screen or Add New in the Members menu section.') . '</p>' .
                       '<p>' . __('You can select the report type you wish to view by checking the correct box that matches the report you wish to view.') . '</p>'
     ) ) ;

     $screen_id->add_help_tab( array(
          'id'      => 'report-display',
          'title'   => __('Report Display'),
          'content' => '<p>' . __('You can customize the display of this screen in a number of ways:') . '</p>' .
                         '<ul>' .
                         '<li>' . __('You can select which type of report you wish to view and decide how many members to list per screen using the Screen Options tab.') . '</li>' .
                         '</ul>'
     ) );

     $help = '<p>' . __('Hovering over a row in the members list will display action links that allow you to manage members. You can perform the following actions:') . '</p>' .
          '<ul>' .
          '<li>' . __('Edit WILL SOON take you to the editable profile screen for that member.') . '</li>' .
          '<li>' . __( 'Archive allows you to mark a member as not active and removes them from the active members list. It does not archive their posts. You can also archive multiple members at once by using Bulk Actions.' ) . '</li>' .
          '<li>' . __( 'Renew marks the membmer as renewed. This is used when you receive a cash or check payment for renewal. You can also renew multiple members at once by using Bulk Actions.' ) . '</li>';
          '</ul>';

     $screen_id->add_help_tab( array(
          'id'      => 'asdfasdf',
          'title'   => __('Actions'),
          'content' => $help,
     ) );
     unset( $help );

     $screen_id->set_help_sidebar(
         '<p><strong>' . __('For more information:') . '</strong></p>' .
         '<p>' . __('<a href="http://codex.wordpress.org/Users_Screen" target="_blank">Documentation on Managing Users</a>') . '</p>' .
         '<p>' . __('<a href="http://codex.wordpress.org/Roles_and_Capabilities" target="_blank">Descriptions of Roles and Capabilities</a>') . '</p>' .
         '<p>' . __('<a href="http://wordpress.org/support/" target="_blank">Support Forums</a>') . '</p>'
     );
}
add_action('in_admin_header', 'ctxphc_report_help');


build_members_listing_table( $archived = 'N', $pending = 'N');

$active_members_table = new CTXPHC_List_Table();
$pagenum = $active_members_table->get_pagenum();
$title = __('CTXPHC Active Members');
$parent_file = 'ctxphc_members.php';


if ( empty($_REQUEST) ) {
	$referer = '<input type="hidden" name="wp_http_referer" value="'. esc_attr( wp_unslash( $_SERVER['REQUEST_URI'] ) ) . '" />';
} elseif ( isset($_REQUEST['wp_http_referer']) ) {
	$redirect = remove_query_arg(array('wp_http_referer', 'updated', 'archive_count'), wp_unslash( $_REQUEST['wp_http_referer'] ) );
	$referer = '<input type="hidden" name="wp_http_referer" value="' . esc_attr($redirect) . '" />';
} else {
	$redirect = 'ctxphc_members.php';
	$referer = '';
}

$update = '';


/******************************************************************************************************/
function archive_members_add_js() { ?>
<script>
jQuery(document).ready( function($) {
	var submit = $('#submit').prop('disabled', true);
	$('input[name=archive_option]').one('change', function() {
		submit.prop('disabled', false);
	});
	$('#reassign_member').focus( function() {
		$('#archive_option1').prop('checked', true).trigger('change');
	});
});
</script>
<?php
}

switch ( $active_members_table->current_action() ) {

     case 'archive':
          check_admin_referer('bulk-members');

          if ( $debug ){ error_log("!!!!  inside the archive case test!   !!!!!!", 0);}
          
          if ( ! current_user_can( 'archive_members' ) )
                    wp_die( __( 'You can&#8217;t archive that member.' ) );

          if ( empty($_REQUEST['members']) ) {
                    wp_redirect($redirect);
                    exit();
          }

          $memberids = $_REQUEST['members'];
          $update = 'archive';
          foreach ( $memberids as $id ) {
               $id = (int) $id;

               if ( ! current_user_can('archive_members') )
                         wp_die(__('You can&#8217;t archive that member.'));

               $member = get_memberdata( $id );
          }

          wp_nonce_field('renew-members');
          wp_redirect(add_query_arg('update', $update, $redirect));
          exit();

     break;

     case 'doarchive':
          if ( is_multisite() )
               wp_die( __('User archive is not allowed from this screen.') );

          check_admin_referer('archive-members');

          if ( empty($_REQUEST['members']) ) {
               wp_redirect($redirect);
               exit();
          }

          $memberids = array_map( 'intval', (array) $_REQUEST['members'] );

          if ( empty( $_REQUEST['archive_option'] ) ) {
               $url = self_admin_url( 'ctxphc_members.php?action=archive&members[]=' . implode( '&members[]=', $memberids ) . '&error=true' );
               $url = str_replace( '&amp;', '&', wp_nonce_url( $url, 'archive-members' ) );
               wp_redirect( $url );
               exit;
          }

          if ( ! current_user_can( 'archive_members' ) )
               wp_die(__('You can&#8217;t archive members.'));

          $update = 'archive';
          $archive_count = 0;

          foreach ( $memberids as $id ) {
               if ( ! current_user_can( 'archive_members') )
                         wp_die(__( 'You can&#8217;t archive that member.' ) );

               ctxphc_archive_user( $id );
               ++$archive_count;
          }

          $redirect = add_query_arg( array('archive_count' => $archive_count, 'update' => $update), $redirect);
          wp_redirect($redirect);
          exit();
     break;

     case 'renew':
               if ( is_multisite() )
                         wp_die( __('User renewal is not allowed from this screen.') );

               check_admin_referer('bulk-members');

               if ( empty($_REQUEST['members']) && empty($_REQUEST['member']) ) {
                         wp_redirect($redirect);
                         exit();
               }

               if ( ! current_user_can( 'renew_members' ) ){
                         $errors = new WP_Error( 'renew_members', __( 'You can&#8217;t renew members.' ) );
               }

               if ( empty($_REQUEST['members']) ){
                         $memberids = array( intval( $_REQUEST['member'] ) );
               } else {
                         $memberids = array_map( 'intval', (array) $_REQUEST['members'] );
               }
          ?>
          <form action="" method="post" name="updatemembers" id="updatemembers">
               <?php wp_nonce_field('renew-members') ?>
               <?php echo $referer; ?>

               <div class="wrap">
                    <?php screen_icon(); ?>
                    <h2><?php _e('Renew Members'); ?></h2>
                    <?php if ( isset( $_REQUEST['error'] ) ) : ?>
                    <div class="error">
                         <p><strong><?php _e( 'ERROR:' ); ?></strong> <?php _e( 'Please select an option.' ); ?></p>
                    </div>
                    <?php endif; ?>
                    <p><?php echo _n( 'You have specified this member for renewal:', 'You have specified the following members for renewal:', count( $memberids ) ); ?></p>
                    <ul>
                    <?php
                    $go_renew = 0;
                    foreach ( $memberids as $id ) {
                         echo "<li><input type=\"hidden\" name=\"members[]\" value=\"" . esc_attr($id) . "\" />" . sprintf(__('ID #%1$s: %2$s'), $id, $member->login) . "</li>\n";
                         $go_archive++;
                    }
                    ?>
                    </ul>
                    <?php if ( $go_renew ) : ?>
                         <input type="hidden" name="action" value="dorenewal" />
                         <?php submit_button( __('Confirm Renewal'), 'secondary' ); ?>
                    <?php else : ?>
                         <p><?php _e('There are no valid members selected for renewal.'); ?></p>
                    <?php endif; ?>
               </div>
          </form>
          <?php

     break;

case 'dorenewal':
	check_admin_referer('renew-members');

	if ( ! is_multisite() )
		wp_die( __( 'You can&#8217;t renew members.' ) );

	if ( empty($_REQUEST['members']) ) {
		wp_redirect($redirect);
		exit;
	}

	if ( ! current_user_can( 'renew_members' ) )
		wp_die( __( 'You can&#8217;t renew members.' ) );

	$memberids = $_REQUEST['members'];

	$update = 'renewal';

	$redirect = add_query_arg( array('update' => $update), $redirect);
	wp_redirect($redirect);
	exit;

     break;

     case 'edit':

          check_admin_referer('bulk-members');

          if ( ! is_multisite() )
               wp_die( __( 'You can&#8217;t ecit members.' ) );

          if ( empty($_REQUEST['members']) && empty($_REQUEST['member']) ) {
               wp_redirect($redirect);
               exit();
          }

          if ( !current_user_can('edit_users') )
               $error = new WP_Error('edit_members', __('You can&#8217;t edit members.'));

          if ( empty($_REQUEST['members']) )
               $memberids = array(intval($_REQUEST['member']));
          else
               $memberids = $_REQUEST['members'];

          //include( ABSPATH . 'wp-admin/admin-header.php' );
          ?>
          <form action="" method="post" name="updatemembers" id="updatemembers">
          <?php wp_nonce_field('edit-members') ?>
          <?php echo $referer; ?>

          <div class="wrap">
          <?php screen_icon(); ?>
          <h2><?php _e('Edit Members from Site'); ?></h2>
          <p><?php _e('You have specified these members for edit:'); ?></p>
          <ul>
          <?php 
          $go_edit = false;
          foreach ( $memberids as $id ) {
               $id = (int) $id;
               $userid = get_userdata( $current_user->ID );
               if ( !current_user_can('edit_users') ) {
                    echo "<li>" . sprintf(__('ID #%1$s: %2$s <strong>You don\'t have permission to edit this member.</strong>'), $id, $member->login) . "</li>\n";
               } else {
                    echo "<li><input type=\"hidden\" name=\"members[]\" value=\"{$id}\" />" . sprintf(__('ID #%1$s: %2$s'), $id, $member->login) . "</li>\n";
                    $go_edit = true;
               }
          } ?>
          <?php if ( $go_edit ) : ?>
               <input type="hidden" name="action" value="doedit" />
               <?php submit_button( __('Confirm Edit'), 'secondary' ); ?>
          <?php else : ?>
               <p><?php _e('There are no valid members selected for updating.'); ?></p>
          <?php endif; ?>
          </div>
          </form>
          <?php

     break;

default:
     if ( !empty($_GET['_wp_http_referer']) ) {
          wp_redirect( remove_query_arg( array( '_wp_http_referer', '_wpnonce'), wp_unslash( $_SERVER['REQUEST_URI'] ) ) );
          exit;
     }

     $report_type = get_ctxphc_member_list( $rp_type );
     error_log("report type is $report_type", 0);
     
     $active_members_table->prepare_items();
     $total_pages = $active_members_table->get_pagination_arg( 'total_pages' );
     if ( $pagenum > $total_pages && $total_pages > 0 ) {
          wp_redirect( add_query_arg( 'paged', $total_pages ) );
          exit;
     }

     //include( ABSPATH . 'wp-admin/admin-header.php' );

     $messages = array();
     if ( isset($_GET['update']) ) :
          switch($_GET['update']) {
          case 'del':
          case 'archive':
                    $archive_count = isset($_GET['archive_count']) ? (int) $_GET['archive_count'] : 0;
                    $messages[] = '<div id="message" class="updated"><p>' . sprintf( _n( 'User archived.', '%s members archived.', $archive_count ), number_format_i18n( $archive_count ) ) . '</p></div>';
                    break;
          case 'renew':
                    $messages[] = '<div id="message" class="updated fade"><p>' . __('User removed from this site.') . '</p></div>';
                    break;
          }
     endif; ?>

     <?php if ( isset($errors) && is_wp_error( $errors ) ) : ?>
               <div class="error">
                    <ul>
                    <?php
                         foreach ( $errors->get_error_messages() as $err )
                              echo "<li>$err</li>\n";
                    ?>
                    </ul>
               </div>
     <?php endif;

     if ( ! empty($messages) ) {
               foreach ( $messages as $msg )
                         echo $msg;
     } ?>

     <div class="wrap">
     <?php screen_icon(); ?>
     <h2>
     <?php
     echo esc_html( $title );
     if ( current_user_can( 'create_members' ) ) { ?>
          <a href="ctxphc_member_new.php" class="add-new-h2"><?php echo esc_html_x( 'Add New', 'member' ); ?></a>
     <?php } elseif ( is_multisite() && current_user_can( 'create_members' ) ) { ?>
               <a href="ctxphc_member_new.php" class="add-new-h2"><?php echo esc_html_x( 'Add New', 'member' ); ?></a>
     <?php }

     if ( $membersearch )
               printf( '<span class="subtitle">' . __('Search results for &#8220;%s&#8221;') . '</span>', esc_html( $membersearch ) ); ?>
     </h2>

     <?php $active_members_table->views(); ?>

     <form action="" method="get">

     <?php $active_members_table->search_box( __( 'Search Members' ), 'member' ); ?>

     <?php $active_members_table->display(); ?>
     </form>

     <br class="clear" />
     </div>
     <?php
     
     break;

} // end of the $doaction switch

include( ABSPATH . 'wp-admin/admin-footer.php' );


