<?php
/**
 * Created by PhpStorm.
 * User: ken_kilgore1
 * Date: 2/23/2015
 * Time: 7:55 PM
 */

/**
 * Print out option html elements for member's relationship selectors.
 *
 * @since 2.1.0
 *
 * @param string $selected slug for the relationship type that should be already selected
 */
function wp_dropdown_roles( $selected = false ) {
	$p = '';
	$r = '';

	$relationship_types = array_reverse( get_relationship_types() );

	foreach ( $relationship_types as $type_id => $type_name ) {
		$name = translate($type_name['memb_type'] );
		if ( $selected == $type_id ) // preselect specified role
			$p = "\n\t<option selected='selected' value='" . esc_attr($type_id) . "'>$name</option>";
		else
			$r .= "\n\t<option value='" . esc_attr($type_id) . "'>$name</option>";
	}
	echo $p . $r;
}