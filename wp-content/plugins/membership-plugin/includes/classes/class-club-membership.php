<?php
/**
 * Created by PhpStorm.
 * User: ken_kilgore1
 * Date: 3/29/2015
 * Time: 9:00 AM
 */

class Club_Membership {

	public function __construct(){
		//run cm initialization
		add_action();

		//get all members
		add_action('get_all_members', array( $this, 'cm_get_all_members'));

		//Displays Membership Registration form
		add_shortcode('registration', array($this, 'cm_shortcode'));

	}

	public function cm_shortcode() {

	}

	public function cm_get_all_members() {
		if ( ! current_user_can( 'create_users' ) ){
			wp_die( __( 'Cheatin&#8217; uh?' ), 403 );
		}

	}

}

$cm_WPMembers = new Club_Membership;