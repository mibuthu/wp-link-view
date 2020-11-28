<?php
/**
 * Widget class
 *
 * @package link-view
 */

// declare( strict_types=1 ); Remove for now due to warnings in php <7.0!

namespace WordPress\Plugins\mibuthu\LinkView\Widget;

if ( ! defined( 'WPINC' ) ) {
	exit();
}

require_once PLUGIN_PATH . 'includes/option.php';

use WordPress\Plugins\mibuthu\LinkView\Option;


/**
 * LinkView Widget arguments config class
 *
 * @property string $title
 * @property string $atts
 */
class Config {

	/**
	 * Widget Items
	 *
	 * @var array<string,Option>
	 */
	private $args;


	/**
	 * Class constructor which initializes required variables
	 */
	public function __construct() {
		$this->args = [
			'title' => new Option( __( 'Links', 'link-view' ) ),
			'atts'  => new Option( '' ),
		];
	}


	/**
	 * Get the value of the given arguments
	 *
	 * @param string $name Argument name.
	 * @return string Argument value.
	 */
	public function __get( $name ) {
		if ( isset( $this->args[ $name ] ) ) {
			return $this->args[ $name ]->value;
		}
		// Trigger error is allowed in this case.
		// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_trigger_error
		trigger_error( 'Widget argument "' . esc_attr( $name ) . '" does not exist!', E_USER_WARNING );
	}


	/**
	 * Get all specified arguments
	 *
	 * @return array<string,Option>
	 */
	public function get_all() {
		return $this->args;
	}


	/**
	 * Load helptexts of widget args
	 *
	 * @return void
	 */
	public function load_args_admin_data() {
		require_once PLUGIN_PATH . 'widget/config-admin-data.php';
		$args_admin_data = new ConfigAdminData();
		foreach ( array_keys( $this->args ) as $arg_name ) {
			$this->args[ $arg_name ]->modify( $args_admin_data->$arg_name );
		}
	}

}
