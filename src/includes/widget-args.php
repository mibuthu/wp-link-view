<?php
/**
 * Widget class
 *
 * @package link-view
 */

// declare( strict_types=1 ); Remove for now due to warnings in php <7.0!

namespace WordPress\Plugins\mibuthu\LinkView;

if ( ! defined( 'WPINC' ) ) {
	exit();
}

require_once PLUGIN_PATH . 'includes/attribute.php';


/**
 * LinkView Widget arguments class
 *
 * @property string $title
 * @property string $atts
 */
class WidgetArgs extends \WP_Widget {

	/**
	 * Widget Items
	 *
	 * @var array<string,Attribute>
	 */
	private $args;


	/**
	 * Class constructor which initializes required variables
	 */
	public function __construct() {
		$this->args = [
			'title' => new Attribute( __( 'Links', 'link-view' ) ),
			'atts'  => new Attribute( '' ),
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
	 * @return array<string,Attribute>
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
		require_once PLUGIN_PATH . 'includes/widget-args-admin-data.php';
		$args_admin_data = new WidgetArgsAdminData();
		foreach ( array_keys( $this->args ) as $arg_name ) {
			$this->args[ $arg_name ]->modify( $args_admin_data->$arg_name );
		}
	}

}
