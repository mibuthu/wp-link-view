<?php
/**
 * Plugin Name: Link View
 * Plugin URI: https://wordpress.org/plugins/link-view/
 * Description: Display a link-list or link-slider in a post or page by using a shortcode.
 * Version: 0.7.3
 * Author: mibuthu
 * Author URI: https://wordpress.org/plugins/link-view/
 * Text Domain: link-view
 * License: GPLv2
 *
 * A plugin for the blogging MySQL/PHP-based WordPress.
 * Copyright 2012-2020 mibuthu
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNUs General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You can view a copy of the HTML version of the GNU General Public
 * License at https://www.gnu.org/copyleft/gpl.html
 *
 * @package link-view
 */

// declare( strict_types=1 ); Remove for now due to warnings in php <7.0!

namespace WordPress\Plugins\mibuthu\LinkView;

if ( ! defined( 'WPINC' ) ) {
	exit();
}

// General definitions.
define( 'PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'PLUGIN_PATH', plugin_dir_path( __FILE__ ) );


/**
 * Main plugin class
 *
 * This is the initial class for loading the plugin.
 */
class LinkView {


	/**
	 * Class Constructor
	 * Initializes the plugin.
	 *
	 * @return void
	 */
	public function __construct() {
		// Always!
		add_action( 'plugins_loaded', [ &$this, 'load_textdomain' ] );
		add_shortcode( 'linkview', [ &$this, 'shortcode_linkview' ] );
		add_action( 'widgets_init', [ &$this, 'widget_init' ] );
		// Enable WordPress link manager (disabled by default since version 3.5).
		if ( false !== get_option( 'link_manager_enabled' ) ) {
			add_filter( 'pre_option_link_manager_enabled', '__return_true' );
		}

		// Depending on Page Type!
		if ( is_admin() ) { // Admin page.
			require_once PLUGIN_PATH . 'admin/admin.php';
			// @phan-suppress-next-line PhanUndeclaredMethod
			Admin\Admin::get_instance()->init();
		} else { // Front page.
			add_action( 'wp_enqueue_scripts', [ &$this, 'register_scripts' ] );
		}
	}


	/**
	 * Load link-view textdomain for translations
	 *
	 * @return void
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'link-view', false, basename( PLUGIN_PATH ) . '/languages' );
	}


	/**
	 * Initialize link-view shortcode
	 *
	 * @param array<string,string> $atts Shortcode attributes.
	 * @param string               $content Shortcode content.
	 * @return string HTML to display
	 */
	public function shortcode_linkview( $atts, $content = '' ) {
		static $shortcodes;
		if ( ! $shortcodes instanceof Shortcodes ) {
			require_once PLUGIN_PATH . 'includes/shortcodes.php';
			$shortcodes = Shortcodes::get_instance();
		}
		// @phan-suppress-next-line PhanPossiblyUndeclaredMethod
		return $shortcodes->add( $atts, $content );
	}


	/**
	 * Initialize link-view widget
	 *
	 * @return void
	 */
	public function widget_init() {
		require_once PLUGIN_PATH . 'includes/widget.php';
		register_widget( __NAMESPACE__ . '\Widget' );
	}


	/**
	 * Function to register the javascript files
	 *
	 * @return void
	 */
	public function register_scripts() {
		wp_register_script( 'lvw_easySlider', PLUGIN_URL . 'includes/js/easySlider.min.js', [ 'jquery' ], '1.7', true );
		wp_register_script( 'lvw_masonry', PLUGIN_URL . 'includes/js/masonry.pkgd.min.js', [ 'jquery' ], '4.2.2', true );
	}

}


/**
 * LinkView Class instance
 *
 * @var LinkView
 */
$linkview = new LinkView();
