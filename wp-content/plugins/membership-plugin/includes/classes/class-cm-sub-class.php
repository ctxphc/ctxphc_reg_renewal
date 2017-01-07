<?php
/**
 * Created by PhpStorm.
 * User: ken_kilgore1
 * Date: 2/23/2015
 * Time: 9:38 AM
 */

namespace Membership\includes\classes;


class CM_Sub_Class extends CM_Base_Class {
	function __construct() {
		add_action( 'cm_membership');
		//add_action( 'cm_base_class_display', array( &$this, 'notify_me' ), 1, 1);
		//add_action( 'cm_base_class_archive', array( &$this, 'archive' ), 1, 1 );
		//add_action( 'cm_base_class_register', array( &$this, 'register' ), 1, 1 );
		//add_action( 'cm_base_class_delete', array( &$this, 'delete' ), 1, 1 );
		//register_activation_hook( __FILE__, 'cm_activation' );
		//register_deactivation_hook( __FILE__, 'cm_deactivation' );
	}

	protected function archive( $obj ) {
		do_action( 'cm_base_class_archive', $obj );
		cm_archive_member( $obj );
	}

	protected function activate( $obj ){
		do_action('cm_base_class_archive', $obj );
		cm_activate_member( $obj );
	}

	protected function delete( $obj ){
		// Method must be defined
		cm_delete_member( $obj );
	}

	protected function register( $obj ){
		// Method must be defined
		cm_regiter_member( $obj );
	}

	protected function notify_me( $obj ){
		wp_mail(
			'kaptkaos@gmail.com',
			'Registration Testing of New class objects',
			print_r( $obj, true )
		);
	}
}

$sub = new CM_Sub_Class;

$new_reg = array(
	'form_title' => 'Membership Registration',
	'form_content' => 'reg_template.php',
);
$sub->display( $new_reg );
