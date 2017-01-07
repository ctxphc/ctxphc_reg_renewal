<?php
/**
 *
 * cm_settings_page() displays the page content for the Club Membership settings sub-menu
 *
 * Created by PhpStorm.
 * User: ken_kilgore1
 * Date: 12/30/2014
 * Time: 11:00 AM
 */

function cm_settings_page() {
	//must check that the user has the required capability
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( __( 'Silly Man, You do not have sufficient permissions to access this page.' ) );
	}

	// Variables for the field and option names
	$opt_array         = array(
		'cm-manager-role-name' => 'Manage Members',
		'cm-membership-types'  => array(
			$type1_name => $type1_value,
			$type2_name => $type2_value,
			$type3_name => $type3_value,
			$type4_name => $type4_value,
			$type5_name => $type5_value,
		),
		'cm-membership-costs' => array(
			$type1_name => $cost1_value,
			$type2_name => $cost2_value,
			$type3_name => $cost3_value,
			$type4_name => $cost4_value,
			$type5_name => $cost5_value,
		),
	);


	// Read in existing option value from database
	$opt_val = get_option( $opt_name );

	// See if the user has posted us some information
	// If they did, this hidden field will be set to 'Y'
	if ( isset( $_POST[ $hidden_field_name ] ) && $_POST[ $hidden_field_name ] == 'Y' ) {
		// Read their posted value
		$opt_val = $_POST[ $data_field_name ];

		// Save the posted value in the database
		update_option( $opt_name, $opt_val );

		// Put a "Settings updated" message on the screen
		?>
		<div class="updated"></div>

		<div class="wrap">
			<?php
			// Header
			echo "<h2>" . __( 'Menu Test Settings', 'menu-test' ) . "</h2>";

			// Settings form
			?>
			<form action="" method="post" name="form1"></form>
			<?php _e( "Favorite Color:", 'menu-test' ); ?>

			<hr/>
		</div>
	<?php }
}