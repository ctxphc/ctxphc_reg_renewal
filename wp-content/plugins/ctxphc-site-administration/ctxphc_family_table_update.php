<?php
/* 
Copyright (C) 2014 ken_kilgore1

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
*/


/** WordPress Administration Bootstrap */
require_once( ABSPATH . 'wp-load.php' );
require_once( ABSPATH . 'wp-admin/admin.php' );
require_once( ABSPATH . 'wp-admin/admin-header.php' );

function ctxphc_family_table_update_init() {
       /* Register our stylesheet. */
       wp_register_style( 'ctxphc_admin_stylesheet', plugins_url('includes/css/ctxphc-admin-styles.css', __FILE__) );
       wp_enqueue_style( 'ctxphc_admin_stylesheet' );
}
add_action( 'admin_init', 'ctxphc_family_table_update_init' );


function ctxphc_members_family_updates(){
   // include_once( 'includes/ctxphc-membership-functions.php' );
    global $wpdb;
    $family_table = 'ctxphc_members_family';
    $member_table = 'ctxphc_members';

    $member_records = $wpdb->get_results( "SELECT id, first_name, last_name, memb_id, email, membership_type FROM ctxphc_members");
    ?>
    <h2><?php _e( "Primary and Family member database clean up step 1" ); ?></h2>

    <div class="changelog">
        <div>
            <h3><?php _e( "Primary and Family member database clean up step 1" ); ?></h3>		    
        </div>

        <div class="spacer"></div> 

        <?php 
        foreach ( $member_records as $member_record ){
            If ( $member_record->memb_id != $member_record->id ){
                echo "<div>";
                echo "<div class=spacer></div>Name: $member_record->first_name $member_record->last_name <br/>";
                echo "memb_id:  $member_record->memb_id  - id:  $member_record->id <div class=spacer></div>";

                if ( $member_record->membership_type === 'IC' || $member_record->membership_type=== 'CO' || $member_record->membership_type === 'HH' ){
                    process_members_family_records( $member_record );
                }

                echo "<div class='spacer'></div>";
                
                $user_id = $member_record->id;
                $email_addr = $member_record->email;
                if ( !empty( $email_addr ) ){
                    $wp_user_id = update_wp_user_id( $member_table, $email_addr, $user_id );

                    if ( $wp_user_id ){
                    echo "Member $member_record->first_name $member_record->last_name has an existing or updated wordpress login and password.";
                    } else {
                        echo "**** Member $member_record->first_name $member_record->last_name failed to get or update the wordpress login and password. ****";
                    }
                }
            }
            echo "</div>";
        }
}?>
    
    <div class="wrap">
    <?php screen_icon(); ?>
    <h2><?php echo esc_html( $title ); ?></h2>
    <div id="dashbaord-widgets-wrap">
	<?php ctxphc_members_family_updates(); ?>
	<div class="clear"></div>
    </div><!-- dashboard-widgets-wrap -->
</div><!-- wrap -->
