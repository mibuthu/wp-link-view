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

			'lv_req_cap'  => array('type'    => 'radio',
			                       'std_val' => 'manage_links',
			                       'label'   => __('Required capabilities to show LinkView About page'),
			                       'caption' => array('manage_links' => 'manage_links (Standard)', 'edit_pages' => 'edit_pages', 'edit_posts' => 'edit_posts'),
			                       'desc'    => __('With this option you can specify the required capabilities to show the LinkView About page.<br />
			                                       (see <a href="http://codex.wordpress.org/Roles_and_Capabilities">WordPress Codex</a> for more infos).')),

			'lv_ml_role'  => array('type'    => 'radio',
			                       'std_val' => 'editor',
			                       'label'   => __('Required role to manage links'),
			                       'caption' => array('editor' => 'Editor (Wordpress-Standard)', 'author' => 'Author', 'contributor' => 'Contributor', 'subscriber' => 'Subscriber'),
			                       'desc'    => __('With this option you can overwrite the wordpress default minimum required role to manage links (Capability: "manage_links").<br />
			                                       (see <a href="http://codex.wordpress.org/Roles_and_Capabilities">WordPress Codex</a> for more infos).<br />
			                                       Please not that this option also affects the viewing the LinkView About page if the required capabilities are set to "manage_links".<br />')),

			'lv_css'      => array('type'    => 'textarea',
			                       'std_val' => '',
			                       'label'   => 'CSS-code for linkview',
			                       'desc'    => 'With this option you can specify CSS-code for the links displayed by the linkview shortcode or widget.<br />
			                                     You can use the classes which are automatically created by the linkview shortcode or widget e.g. .lv-item-image, .lv-section-name, .lv-cat-name, ...<br />
			                                     You can find all available classes if you have a look at the sourcecode of your page where the shortcode or widget is included.<br />
			                                     If you use the shortcode several times you can specify different css styles if you set the attribute "class_suffix" and create CSS-code for these special classes
			                                     e.g. .lv-link-list-suffix, .lv-item-name-suffix.<br /><br />
			                                     Below you can find some working examples:<br />
			                                     <code>.lv-link {<br />
			                                     &nbsp;&nbsp;&nbsp;margin-bottom: 15px;<br />
			                                     }<br />
			                                     .lv-item-image img {<br />
			                                     &nbsp;&nbsp;&nbsp;-webkit-border-radius: 9px;<br />
			                                     &nbsp;&nbsp;&nbsp;-moz-border-radius: 9px;<br />
			                                     &nbsp;&nbsp;&nbsp;border-radius: 9px;<br />
			                                     }<br />
			                                     .lv-item-image-detail img {<br />
			                                     &nbsp;&nbsp;&nbsp;max-width: 250px;<br />
			                                     }<br />
			                                     .lv-section-left-detail {<br />
			                                     &nbsp;&nbsp;&nbsp;float: left;<br />
			                                     }<br />
			                                     .lv-section-right-detail {<br />
			                                     &nbsp;&nbsp;&nbsp;float: right;<br />
			                                     &nbsp;&nbsp;&nbsp;margin-left: 15px;<br />
			                                     }</code>')
		);
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

