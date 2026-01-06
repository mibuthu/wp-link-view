<?php
/**
 * LinkViews Main Admin Class
 *
 * @package link-view
 */

// declare( strict_types=1 ); Remove for now due to warnings in php <7.0!

namespace WordPress\Plugins\mibuthu\LinkView\Admin;

use WordPress\Plugins\mibuthu\LinkView\Config;
use const WordPress\Plugins\mibuthu\LinkView\PLUGIN_URL;
use const WordPress\Plugins\mibuthu\LinkView\PLUGIN_PATH;

if ( ! defined( 'WP_ADMIN' ) ) {
	exit();
}

require_once PLUGIN_PATH . 'includes/config.php';


/**
 * LinkViews Main Admin Class
 *
 * This class handles all LinkView admin pages.
 */
class Admin {


	/**
	 * Class constructor which initializes required variables
	 */
	public function __construct(
		/**
		 * Config class instance reference
		 */
		private readonly Config $config
	) {}


	/**
	 * Initialize the admin page (register required admin actions)
	 */
	public function init(): void {
		add_action( 'admin_menu', $this->register_pages( ... ) );
		add_action( 'plugins_loaded', $this->config->version_upgrade( ... ) );
	}


	/**
	 * Add and register all pages in the admin menu
	 */
	public function register_pages(): void {
		$page = add_submenu_page(
			'link-manager.php',
			sprintf( __( 'About %1$s', 'link-view' ), 'LinkView' ),
			sprintf( __( 'About %1$s', 'link-view' ), 'LinkView' ),
			$this->config->req_capabilities,
			'lvw_admin_about',
			$this->show_about_page( ... )
		);
		add_action( 'admin_print_scripts-' . $page, $this->embed_about_styles( ... ) );
		$page = add_submenu_page(
			'options-general.php',
			sprintf( __( '%1$s Settings', 'link-view' ), 'LinkView' ),
			'LinkView',
			'manage_options',
			'lvw_admin_settings',
			$this->show_settings_page( ... )
		);
		add_action( 'admin_print_scripts-' . $page, $this->embed_settings_styles( ... ) );
	}


	/**
	 * Show the plugins about page
	 */
	public function show_about_page(): void {
		require_once PLUGIN_PATH . 'admin/about.php';
		$about = new About( $this->config );
		$about->show_page();
	}


	/**
	 * Show the plugins settings page
	 */
	public function show_settings_page(): void {
		require_once PLUGIN_PATH . 'admin/settings.php';
		$settings = new Settings( $this->config );
		$settings->show_page();
	}


	/**
	 * Embed the plugins about page styles
	 * TODO: move to admin about class
	 */
	public function embed_about_styles(): void {
		wp_enqueue_style( 'lvw_admin_about', PLUGIN_URL . 'admin/css/about.css', [], '1.0' );
	}


	/**
	 * Embed the plugins settings page styles
	 * TODO: move to admin settings class
	 */
	public function embed_settings_styles(): void {
		wp_enqueue_style( 'lvw_admin_settings', PLUGIN_URL . 'admin/css/settings.css', [], '1.0' );
	}

}
