<?php
if(!defined('WPINC')) {
	die;
}

// This class handles all available options
class LV_Options {
	private static $instance;
	public $options;

	public static function &get_instance() {
		// singleton setup
		if(!isset(self::$instance)) {
			self::$instance = new self;
			self::$instance->init();
		}
		return self::$instance;
	}

	private function __construct() {
		$this->options = array(
			'lv_req_cap'  => array('std_val' => 'manage_links'),
			'lv_ml_role'  => array('std_val' => 'editor'),
			'lv_css'      => array('std_val' => ''),
		);
	}

	public function load_options_helptexts() {
		require_once(LV_PATH.'includes/options_helptexts.php');
		foreach($options_helptexts as $name => $values) {
			$this->options[$name] += $values;
		}
		unset($options_helptexts);
	}

	public function init() {
		add_action('admin_init', array(&$this, 'register'));
		add_filter('pre_update_option_lv_ml_role', array(&$this, 'update_manage_links_role'));
	}

	public function register() {
		foreach($this->options as $oname => $o) {
			register_setting('lv_options', $oname);
		}
	}

	public function get($name) {
		if(isset($this->options[$name])) {
			return get_option($name, $this->options[$name]['std_val']);
		}
		else {
			return null;
		}
	}

	public function update_manage_links_role($new_value, $old_value=null) {
		global $wp_roles;
		switch($new_value) {
			case 'subscriber':
				$wp_roles->add_cap('subscriber', 'manage_links');
			case 'contributor':
				$wp_roles->add_cap('contributor', 'manage_links');
			case 'author':
				$wp_roles->add_cap('author', 'manage_links');
				break;
		}
		switch($new_value) {
			case 'editor':
				$wp_roles->remove_cap('author', 'manage_links');
			case 'author':
				$wp_roles->remove_cap('contributor', 'manage_links');
			case 'contributor':
				$wp_roles->remove_cap('subscriber', 'manage_links');
				break;
		}
		return $new_value;
	}
} // end of class LV_Options

