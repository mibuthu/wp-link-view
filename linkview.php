<?php
/*
Plugin Name: Link View
Plugin URI: http://wordpress.org/extend/plugins/link-view/
Description: Display a link-list or link-slider in a post or page by using a shortcode.
Version: 0.4.3
Author: Michael Burtscher
Author URI: http://wordpress.org/extend/plugins/link-view/
License: GPLv2

A plugin for the blogging MySQL/PHP-based WordPress.
Copyright 2012-2013 Michael Burtscher

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

// GENERAL DEFINITIONS
define( 'LV_URL', plugin_dir_url( __FILE__ ) );
define( 'LV_PATH', plugin_dir_path( __FILE__ ) );


// MAIN PLUGIN CLASS
class linkview {
	private $shortcode;

	/**
	 * Constructor:
	 * Initializes the plugin.
	 */
	public function __construct() {
		$this->shortcode = NULL;

		// ALWAYS:
		// Register shortcodes
		add_shortcode( 'linkview', array( &$this, 'shortcode_linkview' ) );
		// Register widgets
		add_action( 'widgets_init', array( &$this, 'widget_init' ) );
		// Filters
		if ( !get_option( 'link_manager_enabled' ) ) {
			add_filter( 'pre_option_link_manager_enabled', '__return_true' ); // required for Wordpress 3.5
		}

		// ADMIN PAGE:
		if ( is_admin() ) {
			// Include required php-files and initialize required objects
			require_once( 'php/admin.php' );
			$admin = new lv_admin();
			// Register actions
			add_action( 'admin_menu', array( &$admin, 'register_pages' ) );
		}

		// FRONT PAGE:
		else {
			// Register actions
			add_action( 'init', array( &$this, 'frontpage_init' ) );
			add_action( 'wp_footer', array( &$this, 'frontpage_footer' ) );
		}
	} // end constructor

	public function shortcode_linkview( $atts, $content='' ) {
		if( NULL == $this->shortcode ) {
			require_once( 'php/sc_linkview.php' );
			$this->shortcode = sc_linkview::get_instance();
		}
		return $this->shortcode->show_html( $atts, $content );
	}

	public function widget_init() {
		// Widget "linkview"
		require_once( 'php/linkview_widget.php' );
		return register_widget( 'linkview_widget' );
	}

	public function frontpage_init() {
		wp_register_script( 'lv_easySlider', LV_URL.'js/easySlider.js', array( 'jquery' ), true );
	}

	public function frontpage_footer() {
		if( NULL != $this->shortcode && NULL != $this->shortcode->get_slider_ids() ) {
			wp_print_scripts( 'lv_easySlider' );
			$this->shortcode->print_slider_script();
		}
	}
} // end class linkview


// create a class instance
$lv = new linkview();
?>
