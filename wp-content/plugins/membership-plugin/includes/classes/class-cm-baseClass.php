<?php
/**
 * Created by PhpStorm.
 * User: ken_kilgore1
 * Date: 2/23/2015
 * Time: 9:29 AM
 */

namespace Membership\includes\classes;


abstract class CM_Base_Class {

	abstract protected function archive( $obj );
	abstract protected function activate( $obj );
	abstract protected function delete( $obj );
	abstract protected function register( $obj );

	public function get_reg_form( $args ){
		$display_reg_form = get_form( $args->display_type );
		return apply_filters( 'cm_base_class_get_form', $display_reg_form );
	}
}