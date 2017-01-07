<?php

/**
 * Plugin Name: Anti SpamBot Email Links
 * Description: Adds a shortcode button to the WordPress TinyMCE editor for easy anti spambot email links.
 * Version: 1.0
 * Author: Tibor Paulsch
 * Author URI: http://tiborpaulsch.nl/
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU 
 * General Public License version 2, as published by the Free Software Foundation.  You may NOT assume 
 * that you can use any other version of the GPL.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 */

// Add shortcode [email] for email antispam links
add_shortcode( 'email', 'tibs_sc8_email' );
function tibs_sc8_email( $attr, $content ) {
	if( is_email( $content ) ) {
		$content = antispambot( $content );
		return sprintf( '<a href="mailto:%s">%s</a>', $content, $content );
	}
	else {
		return '';
	}
}

// Add a button to the post editor for the email shortcode
add_action( 'init', 'tibs_add_button' );
function tibs_add_button() {
	if ( current_user_can( 'edit_posts') &&  current_user_can('edit_pages' ) ) {
		add_filter( 'mce_external_plugins', 'tibs_add_plugin' );
		add_filter( 'mce_buttons', 'tibs_register_button' );
	}
}

function tibs_add_plugin( $plugin_array ) {
	$plugin_array['emaillink'] = plugin_dir_url(__FILE__).'lib/customcodes.js';
	
	return $plugin_array;
}

function tibs_register_button($buttons) {
	array_push( $buttons, "emaillink" );
	
	return $buttons;
}



