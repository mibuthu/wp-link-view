<?php

// This class handles all available options
class lv_options {
	private static $instance;
	public $group;
	public $options;

	public static function &get_instance() {
		// Create class instance if required
		if( !isset( self::$instance ) ) {
			self::$instance = new lv_options();
			self::$instance->init();
		}
		// Return class instance
		return self::$instance;
	}

	private function __construct() {
		$this->group = 'linkview';

		$this->options = array(

			'lv_css'      => array( 'section' => 'css',
			                        'type'    => 'textarea',
			                        'std_val' => '',
			                        'label'   => 'CSS for linkview',
			                        'desc'    => 'This option specifies the css code for the links displayed by the linkview shortcode.<br />
			                                      You can use the given classes for the container, image, name, url, ...' )
		);
	}

	public function init() {
		add_action( 'admin_init', array( &$this, 'register' ) );
	}

	public function register() {
		foreach( $this->options as $oname => $o ) {
			register_setting( 'lv_'.$o['section'], $oname );
		}
	}

	public function get( $name ) {
		if( isset( $this->options[$name] ) ) {
			return get_option( $name, $this->options[$name]['std_val'] );
		}
		else {
			return null;
		}
	}
}

