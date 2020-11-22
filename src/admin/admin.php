<?php
/**
 * LinkViews Main Admin Class
 *
 * @package link-view
 */

// declare( strict_types=1 ); Remove for now due to warnings in php <7.0!

namespace WordPress\Plugins\mibuthu\LinkView\Admin;

use WordPress\Plugins\mibuthu\LinkView\Options;

if ( ! defined( 'WP_ADMIN' ) ) {
	exit();
}

require_once PLUGIN_PATH . 'includes/options.php';


/**
 * LinkViews Main Admin Class
 *
 * This class handles all LinkView admin pages.
 */
class Admin {

	/**
	 * Options class instance reference
	 *
	 * @var Options
	 */
	private $options;


	/**
	 * Class constructor which initializes required variables
	 *
	 * @param Options $options_instance The Options instance as a reference.
	 */
	public function __construct( &$options_instance ) {
		$this->options = $options_instance;
	}


	/**
	 * Initialize the admin page (register required admin actions)
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'admin_menu', [ $this, 'register_pages' ] );
		add_action( 'plugins_loaded', [ $this->options, 'version_upgrade' ] );
	}


	/**
	 * Add and register all pages in the admin menu
	 *
	 * @return void
	 */
	public function register_pages() {
		$page = add_submenu_page(
			'link-manager.php',
			sprintf( __( 'About %1$s', 'link-view' ), 'LinkView' ),
			sprintf( __( 'About %1$s', 'link-view' ), 'LinkView' ),
			$this->options->lvw_req_capabilities,
			'lvw_admin_about',
			[ $this, 'show_about_page' ]
		);
		add_action( 'admin_print_scripts-' . $page, [ $this, 'embed_about_styles' ] );
		$page = add_submenu_page(
			'options-general.php',
			sprintf( __( '%1$s Settings', 'link-view' ), 'LinkView' ),
			'LinkView',
			'manage_options',
			'lvw_admin_options',
			[ &$this, 'show_settings_page' ]
		);
		add_action( 'admin_print_scripts-' . $page, [ &$this, 'embed_settings_styles' ] );
	}


	/**
	 * Show the plugins about page
	 *
	 * @return void
	 */
	public function show_about_page() {
		require_once PLUGIN_PATH . 'admin/includes/about.php';
		$about = new About( $this->options );
		$about->show_page();
	}


	/**
	 * Show the plugins settings page
	 *
	 * @return void
	 */
	public function show_settings_page() {
		require_once PLUGIN_PATH . 'admin/includes/settings.php';
		$settings = new Settings( $this->options );
		$settings->show_page();
	}


	/**
	 * Embed the plugins about page styles
	 * TODO: move to admin about class
	 *
	 * @return void
	 */
	public function embed_about_styles() {
		wp_enqueue_style( 'lvw_admin_about', PLUGIN_URL . 'admin/css/admin_about.css', [], '1.0' );
	}


	/**
	 * Embed the plugins settings page styles
	 * TODO: move to admin settings class
	 *
	 * @return void
	 */
	public function embed_settings_styles() {
		wp_enqueue_style( 'lvw_admin_settings', PLUGIN_URL . 'admin/css/admin_settings.css', [], '1.0' );
	}

}

