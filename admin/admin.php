<?php
if(!defined('WPINC')) {
	die;
}

require_once(LV_PATH.'includes/sc_linkview.php');
require_once(LV_PATH.'includes/options.php');

// This class handles all available admin pages
class LV_Admin {
	private static $instance;

	private function __construct() {
		// nothing to do
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
		$page = add_submenu_page('link-manager.php', 'About LinkView', 'About LinkView', 'manage_links', 'lv_admin_main', array(&$this, 'show_about_page'));
		add_action('admin_print_scripts-'.$page, array(&$this, 'embed_about_scripts'));
	}

	public function show_about_page() {
		require_once(LV_PATH.'admin/includes/admin-about.php');
		LV_Admin_About::get_instance()->show_about();
	}

	public function embed_about_scripts() {
		wp_enqueue_style('linkview_admin_about', LV_URL.'admin/css/admin_about.css');
	}
} // end class LV_Admin
?>
