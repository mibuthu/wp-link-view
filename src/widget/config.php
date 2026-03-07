<?php
/**
 * Widget class
 *
 * @package link-view
 */

declare( strict_types=1 );

namespace WordPress\Plugins\mibuthu\LinkView\Widget;

use const WordPress\Plugins\mibuthu\LinkView\PLUGIN_PATH;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly

require_once PLUGIN_PATH . 'includes/option-value.php';

use WordPress\Plugins\mibuthu\LinkView\OptionValue;


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
	 * @var array<string,OptionValue>
	 */
	private array $args;


	/**
	 * Class constructor which initializes required variables
	 */
	public function __construct() {
		$this->args = [
			'title' => new OptionValue( __( 'Links', 'link-view' ) ),
			'atts'  => new OptionValue( '' ),
		];
	}


	/**
	 * Get the value of the given arguments
	 */
	public function __get( string $name ): string {
		if ( isset( $this->args[ $name ] ) ) {
			return $this->args[ $name ]->value;
		}
		// Trigger error is allowed in this case.
		// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_trigger_error
		trigger_error( 'Widget argument "' . esc_attr( $name ) . '" does not exist!', E_USER_WARNING );
		return '';
	}


	/**
	 * Get all specified arguments
	 *
	 * @return array<string,OptionValue>
	 */
	public function get_all(): array {
		return $this->args;
	}


	/**
	 * Load help-texts of widget args
	 */
	public function load_args_admin_data(): void {
		require_once PLUGIN_PATH . 'widget/config-admin-data.php';
		$args_admin_data = new ConfigAdminData();
		foreach ( array_keys( $this->args ) as $arg_name ) {
			$this->args[ $arg_name ]->modify( $args_admin_data->$arg_name );
		}
	}

}
