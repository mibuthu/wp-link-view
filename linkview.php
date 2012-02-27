<?php
/*
Plugin Name: Link View 
Plugin URI: http://wordpress.org/extend/plugins/linkview/
Description: Display a link-list in a post or page by using a shortcode.
Version: 0.1.0
Author: Michael Burtscher

A plugin for the blogging MySQL/PHP-based WordPress.
Copyright 2012 Michael Burtscher

This program is free software; you can redistribute it and/or
modify it under the terms of the GNUs General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You can view a copy of the HTML version of the GNU General Public
License at http://www.gnu.org/copyleft/gpl.html
*/


// add admin pages in admin menu
add_action('admin_menu', 'on_lv_admin');

function on_lv_admin() {
	require_once( 'php/admin.php' );
	add_submenu_page( 'link-manager.php', 'Link View', 'Link View', 'edit_posts', 'lv_admin_main', array( admin, 'show_main' ) );
}


// add shortcode [linkview]
add_shortcode('linkview', 'on_lv_sc_linkview');

function on_lv_sc_linkview( $atts ) {
	require_once( 'php/sc_linkview.php' );
	return sc_linkview::show_html( $atts );
}
?>
