<?php
/**
 * Created by PhpStorm.
 * User: ken_kilgore1
 * Date: 1/20/2015
 * Time: 8:44 PM
 */

namespace Membership\includes\classes;


class classPayPalIPNHandler {

	public function constuc() {


	}

	/**
	 * @param $data
	 */
	function paypal_ipn_handler( $data ) {
		global $wpdb;
		switch ( $data->item_name ) {
			case 'Registration':
				$new_memb_ids = activate_new_member( $data->custom );

				if ( is_array( $new_memb_ids ) ) {
					$memb_data = $wpdb->get_row( " SELECT * FROM ctxphc_members WHERE id = {$new_memb_ids['memb']}" );

					if ( $new_memb_ids[ 'sp' ] ) {
						$fam_memb_data[ 'sp' ] = $wpdb->get_row( "SELECT * FROM ctxphc_members_family WHERE id = {$new_memb_ids['sp']}" );
						$wpdb->print_error();
					}

					if ( $new_memb_ids[ 'c1' ] ) {
						$fam_memb_data[ 'c1' ] = $wpdb->get_row( "SELECT * FROM ctxphc_members_family WHERE id = {$new_memb_ids['c1']}" );
					}

					if ( $new_memb_ids[ 'c2' ] ) {
						$fam_memb_data[ 'c2' ] = $wpdb->get_row( "SELECT * FROM ctxphc_members_family WHERE id = {$new_memb_ids['c2']}" );
					}

					if ( $new_memb_ids[ 'c3' ] ) {
						$fam_memb_data[ 'c3' ] = $wpdb->get_row( "SELECT * FROM ctxphc_members_family WHERE id = {$new_memb_ids['c3']}" );
					}

					if ( $new_memb_ids[ 'c4' ] ) {
						$fam_memb_data[ 'c4' ] = $wpdb->get_row( "SELECT * FROM ctxphc_members_family WHERE id = {$new_memb_ids['c4']}" );
					}

					//  TODO: add_memb_to_wp_users( $new_memb_ids );

					/*  TODO: Create function:  remove_reg_rec( $data->custom ) */

					/*  TODO: Create function:  send_new_member_success_email( $memb_data )  */

					//email to support and membership
					// TODO: Break out to its own function send_registration_completed_emails( $reg_type, $to = 'default' )
					$to         = "Kapt Kaos <kaptkaos@gmail.com>";
					$subject    = "New Member Registration Completed";
					$headers[ ] = "From: CTxPHC Support <support@ctxphc.com>";
					$headers[ ] = "Cc: CTXPHC Support <support@ctxphc.com>";
					$headers[ ] = "MIME-Version: 1.0\r\n";
					$headers[ ] = "Content-Type: text/html; charset=ISO-8859-1\r\n";

					$body = "<html><body>";
					$body .= "<p>Automated email after recieving IPN data from Paypal. <br />";
					$body .= "This is generated in the functions.php file. <br />";
					$body .= "Nothing else is needed at this point as this is only a test message.";
					$body .= "<p>";
					$body .= "New Member Info:<br />";
					$body .= "Name: {$memb_data->first_name} {$memb_data->last_name}<br />";
					$body .= "Email: {$memb_data->email}<br />";
					$body .= "Phone: {$memb_data->phone}<br />";
					$body .= "Hatch Date: {$memb_data->hatch_date}</p>";

					// TODO: create spouse and child data display

					$body .= "<p>Thank you,<br />CTxPHC Support<br /><a href='http://ctxphc.com'>www.ctxphc.com</a>";
					$body .= "</body></html>";

					add_filter( 'wp_mail_content_type', 'set_html_content_type' );
					wp_mail( $to, $subject, $body, $headers );
					remove_filter( 'wp_mail_content_type', 'set_html_content_type' );
				}
				Break;
			case 'Membership Renewal':
				$renewing_memb_ids = activate_renewing_member( $data->custom );

				if ( is_array( $renewing_memb_ids ) ) {
					$renewing_memb_data = $wpdb->get_row( " SELECT * FROM ctxphc_members WHERE id = {$renewing_memb_ids['memb']}" );
					//  add_memb_to_wp_users( $renewing_memb_ids );

					//  remove_renewing_memb_rec( $data->custom );

					//email to support and membership
					// TODO: Break out to its own function send_registration_completed_emails( $reg_type, $to = 'default' )
					$to         = "Kapt Kaos <kaptkaos@gmail.com>";
					$subject    = "Membership Renewal Completed";
					$headers[ ] = "From: CTxPHC Support <support@ctxphc.com>";
					$headers[ ] = "Cc: CTXPHC Support <support@ctxphc.com>";
					$headers[ ] = "MIME-Version: 1.0\r\n";
					$headers[ ] = "Content-Type: text/html; charset=ISO-8859-1\r\n";

					$body = "<html><body>";
					$body .= "<p>Automated email after recieving IPN data from Paypal. <br />";
					$body .= "This is generated in the functions.php file. <br />";
					$body .= "Nothing else is needed at this point as this is only a test message.";
					$body .= "<p>";
					$body .= "Renewing Member Info:<br />";
					$body .= "Name: {$renewing_memb_data->first_name} {$renewing_memb_data->last_name}<br />";
					$body .= "Email: {$renewing_memb_data->email}<br />";
					$body .= "Phone: {$renewing_memb_data->phone}<br />";
					$body .= "Hatch Date: {$renewing_memb_data->hatch_date}</p>";

					// TODO: create spouse and child data display

					$body .= "<p>Thank you,<br />CTxPHC Support<br /><a href='http://ctxphc.com'>www.ctxphc.com</a>";
					$body .= "</body></html>";

					add_filter( 'wp_mail_content_type', 'set_html_content_type' );
					wp_mail( $to, $subject, $body, $headers );
					remove_filter( 'wp_mail_content_type', 'set_html_content_type' );
				}

				Break;
			case 'Pirates Ball Early Registration':
				$pbRegID   = $data->custom;
				$pbRegData = $wpdb->get_row( "SELECT * FROM ctxphc_pb_reg WHERE pbRegId = $pbRegID" );
				$wpdb->print_error();

				$pb_paid_reg = pb_paid_reg( $pbRegID );

				if ( $pb_paid_reg ) {

					//email to support and membership PB Registration payment is complete!
					// TODO: Break out to its own function send_registration_completed_emails( $reg_type, $to = 'default' )
					$to[ ] = "support@ctxphc.com";
					$to[ ] = "membership@ctxphc.com";

					$subject = "Pirates Ball Registration Payment is Complete!!!!";

					$headers[ ] = "From: Central Texas Parrothead Club<ctxphc@ctxphc.com>";
					$headers[ ] = "Reply-To: support@ctxphc.com";
					$headers[ ] = "MIME-Version: 1.0\r\n";
					$headers[ ] = "Content-Type: text/html; charset=ISO-8859-1\r\n";

					$body = "<html><body>";
					$body .= "<h2>Pirat's Ball Registration Payment Complete!</h2>";
					$body .= "<p><div>The PB Registration payment is complete for " . $pbRegData->first_name . ' ' . $pbRegData->last_name . "</div>";
					$body .= "<div class='spacer'></div>";
					$body .= "<p><h3>The Registration Info:</h3>";
					$body .= "<div>" . $pbRegData->first_name . " " . $pbRegData->last_name . "</div>";
					$body .= "<div>" . $pbRegData->addr1 . "</div>";
					if ( isset( $pbRegData->addr2 ) ) {
						$body .= "<div>" . $pbRegData->addr2 . "</div>";
					}
					$body .= "<div>" . $pbRegData->city . ", " . $pbRegData->state . " " . $pbRegData->zip . "</div>";
					$body .= "<p><ul><li>Date Registered:  " . $pbRegData->reg_date . "</li>";
					$body .= "<li>Email:  " . $pbRegData->email . "</li>";
					$body .= "<li>Club Affiliation:  " . $pbRegData->club_aff . "</li>";
					$body .= "<li>Number Attending:  " . $pbRegData->quantity . "</li></ul>";
					$body .= "<p><h3>Attendees:</h3>";
					$body .= "<ul><li>Attendee 1: " . $pbRegData->attendee_1 . "</li>";
					if ( isset( $pbRegData->attendee_2 ) ) {
						$body .= "<li>Attendee 2:  " . $pbRegData->attendee_2 . "</li>";
					}
					if ( isset( $pbRegData->attendee_3 ) ) {
						$body .= "<li>Attendee 3:  " . $pbRegData->attendee_3 . "</li>";
					}
					if ( isset( $pbRegData->attendee_4 ) ) {
						$body .= "<li>Attendee 4:  " . $pbRegData->attendee_4 . "</li>";
					}
					$body .= "</ul>";
					$body .= "<p>Total Paid:  $" . $pbRegData->amount . "</div>";
					$body .= "<p>FinsUp! ";
					$body .= "<p>CTxPHC Support<br />";
					$body .= "Central Texas Parrot Head Club</div>";
					$body .= "</body></html>";

					add_filter( 'wp_mail_content_type', 'set_html_content_type' );
					wp_mail( $to, $subject, $body, $headers );
					remove_filter( 'wp_mail_content_type', 'set_html_content_type' );
				} else {
					//email to support that PB Reg payment processing failed.
					// TODO: Break out to its own function send_registration_completed_emails( $reg_type, $to = 'default' )
					$to[ ] = "support@ctxphc.com";

					$subject = "Pirates Ball Registration Payment Processing FAILED";

					$headers[ ] = "From: Central Texas Parrothead Club<ctxphc@ctxphc.com>";
					$headers[ ] = "Reply-To: support@ctxphc.com";
					$headers[ ] = "MIME-Version: 1.0\r\n";
					$headers[ ] = "Content-Type: text/html; charset=ISO-8859-1\r\n";

					$body = "<html><body>";
					$body .= "<h2>Pirat's Ball Registration Payment Processing FAILED!</h2>";
					$body .= "<p><div>The PB Registration payment processing failed for " . $pbRegData->first_name . ' ' . $pbRegData->last_name . "</div>";
					$body .= "<div class='spacer'></div>";
					$body .= "<p><h3>The Registration Info:</h3>";
					$body .= "<div>" . $pbRegData->first_name . " " . $pbRegData->last_name . "</div>";
					$body .= "<div>" . $pbRegData->addr1 . "</div>";
					if ( isset( $pbRegData->addr2 ) ) {
						$body .= "<div>" . $pbRegData->addr2 . "</div>";
					}
					$body .= "<div>" . $pbRegData->city . ", " . $pbRegData->state . " " . $pbRegData->zip . "</div>";
					$body .= "<p><ul><li>Date Registered:  " . $pbRegData->reg_date . "</li>";
					$body .= "<li>Email:  " . $pbRegData->email . "</li>";
					$body .= "<li>Club Affiliation:  " . $pbRegData->club_aff . "</li>";
					$body .= "<li>Number Attending:  " . $pbRegData->quantity . "</li></ul>";
					$body .= "<p><h3>Attendees:</h3>";
					$body .= "<ul><li>Attendee 1: " . $pbRegData->attendee_1 . "</li>";
					if ( isset( $pbRegData->attendee_2 ) ) {
						$body .= "<li>Attendee 2:  " . $pbRegData->attendee_2 . "</li>";
					}
					if ( isset( $pbRegData->attendee_3 ) ) {
						$body .= "<li>Attendee 3:  " . $pbRegData->attendee_3 . "</li>";
					}
					if ( isset( $pbRegData->attendee_4 ) ) {
						$body .= "<li>Attendee 4:  " . $pbRegData->attendee_4 . "</li>";
					}
					$body .= "</ul>";
					$body .= "<p>Total Paid:  $" . $pbRegData->amount . "</div>";
					$body .= "<p>FinsUp! ";
					$body .= "<p>CTxPHC Support<br />";
					$body .= "Central Texas Parrot Head Club</div>";
					$body .= "</body></html>";

					add_filter( 'wp_mail_content_type', 'set_html_content_type' );
					wp_mail( $to, $subject, $body, $headers );
					remove_filter( 'wp_mail_content_type', 'set_html_content_type' );

				}
				Break;
		}
	}
}