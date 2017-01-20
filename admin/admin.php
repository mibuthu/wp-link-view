<?php
if(!defined('WPINC')) {
	die;
}

require_once(LV_PATH.'includes/options.php');

// This class handles all available admin pages
class LV_Admin {
	private static $instance;
	private $options;

	private function __construct() {
		$this-> options = LV_Options::get_instance();
	}

	public static function &get_instance() {
		// singleton setup
		if(!isset(self::$instance)) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	public function init_admin_page() {
		// Register actions
		add_action('admin_menu', array(&$this, 'register_pages'));
	}

	/**
	 * Add and register all admin pages in the admin menu
	 */
	public function register_pages() {
		$page = add_submenu_page('link-manager.php', sprintf(__('About %1$s','link-view'), 'LinkView'), sprintf(__('About %1$s','link-view'), 'LinkView'), $this->options->get('lv_req_cap'), 'lv_admin_about', array(&$this, 'show_about_page'));
		add_action('admin_print_scripts-'.$page, array(&$this, 'embed_about_scripts'));
		$page = add_submenu_page('options-general.php', sprintf(__('%1$s Settings','link-view'), 'LinkView'), 'LinkView', 'manage_options', 'lv_admin_options', array(&$this, 'show_settings_page'));
		add_action('admin_print_scripts-'.$page, array(&$this, 'embed_settings_scripts'));
	}

	public function show_about_page() {
		require_once(LV_PATH.'admin/includes/admin-about.php');
		LV_Admin_About::get_instance()->show_page();
	}

	public function show_settings_page() {
		require_once(LV_PATH.'admin/includes/admin-settings.php');
		LV_Admin_Settings::get_instance()->show_page();
	}

	public function embed_about_scripts() {
		wp_enqueue_style('linkview_admin_about', LV_URL.'admin/css/admin_about.css');
	}

	public function embed_settings_scripts() {
		wp_enqueue_style('linkview_admin_settings', LV_URL.'admin/css/admin_settings.css');
	}
} // end class LV_Admin
?>
