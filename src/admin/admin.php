<?php
/**
 * LinkViews Main Admin Class
 *
 * @package link-view
 */

declare( strict_types=1 );
if ( ! defined( 'WP_ADMIN' ) ) {
	exit();
}

require_once LV_PATH . 'includes/options.php';


/**
 * LinkViews Main Admin Class
 *
 * This class handles all LinkView admin pages.
 */
class LV_Admin {

	/**
	 * Class singleton instance reference
	 *
	 * @var self
	 */
	private static $instance;

	/**
	 * Options class instance reference
	 *
	 * @var LV_Options
	 */
	private $options;


	/**
	 * Singleton provider and setup
	 *
	 * @return self
	 */
	public static function &get_instance() {
		// There seems to be an issue with the self variable in phan.
		// @phan-suppress-next-line PhanPluginUndeclaredVariableIsset.
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}


	/**
	 * Class constructor which initializes required variables
	 */
	private function __construct() {
		$this->options = LV_Options::get_instance();
	}


	/**
	 * Initialize the admin page (register required admin actions)
	 *
	 * @return void
	 */
	public function init_admin_page() {
		add_action( 'admin_menu', array( &$this, 'register_pages' ) );
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
			$this->options->get( 'lv_req_cap' ),
			'lv_admin_about',
			array( &$this, 'show_about_page' )
		);
		add_action( 'admin_print_scripts-' . $page, array( &$this, 'embed_about_styles' ) );
		$page = add_submenu_page(
			'options-general.php',
			sprintf( __( '%1$s Settings', 'link-view' ), 'LinkView' ),
			'LinkView',
			'manage_options',
			'lv_admin_options',
			array( &$this, 'show_settings_page' )
		);
		add_action( 'admin_print_scripts-' . $page, array( &$this, 'embed_settings_styles' ) );
	}


	/**
	 * Show the plugins about page
	 *
	 * @return void
	 */
	public function show_about_page() {
		require_once LV_PATH . 'admin/includes/admin-about.php';
		LV_Admin_About::get_instance()->show_page();
	}


	/**
	 * Show the plugins settings page
	 *
	 * @return void
	 */
	public function show_settings_page() {
		require_once LV_PATH . 'admin/includes/admin-settings.php';
		LV_Admin_Settings::get_instance()->show_page();
	}


	/**
	 * Embed the plugins about page styles
	 * TODO: move to admin about class
	 *
	 * @return void
	 */
	public function embed_about_styles() {
		wp_enqueue_style( 'lv_admin_about', LV_URL . 'admin/css/admin_about.css', array(), '1.0' );
	}


	/**
	 * Embed the plugins settings page styles
	 * TODO: move to admin settings class
	 *
	 * @return void
	 */
	public function embed_settings_styles() {
		wp_enqueue_style( 'lv_admin_settings', LV_URL . 'admin/css/admin_settings.css', array(), '1.0' );
	}

}

