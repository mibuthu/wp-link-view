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

			'lv_css'      => array('section' => 'css',
			                       'type'    => 'textarea',
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
	}

	public function register() {
		foreach($this->options as $oname => $o) {
			register_setting('lv_'.$o['section'], $oname);
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
} // end of class LV_Options

