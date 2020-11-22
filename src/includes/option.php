<?php
/**
 * Option Class
 *
 * @package link-view
 */

// declare( strict_types=1 ); Remove for now due to warnings in php <7.0!

namespace WordPress\Plugins\mibuthu\LinkView;

if ( ! defined( 'WPINC' ) ) {
	exit();
}


/**
 * Option Class
 *
 * This class handles an option which can be used for shortcode, widget and plugin config options.
 */
class Option {

	/**
	 * Actual or default value
	 *
	 * @var string
	 */
	public $value;

	/**
	 * Permitted values
	 *
	 * @var string|array
	 */
	public $permitted_values = '';

	/**
	 * Section
	 *
	 * @var string
	 */
	public $section = '';

	/**
	 * Type
	 *
	 * @var string
	 */
	public $type = '';

	/**
	 * Label
	 *
	 * @var string
	 */
	public $label = '';

	/**
	 * Caption
	 *
	 * @var string|array
	 */
	public $caption = '';

	/**
	 * Description
	 *
	 * @var string
	 */
	public $description = '';

	/**
	 * Tooltip
	 *
	 * @var string
	 */
	public $tooltip = '';


	/**
	 * Class constructor which sets the required variables
	 *
	 * @param string            $std_value Standard value for the option.
	 * @param null|string|array $permitted_values Available values for the option (optional).
	 * @return void
	 */
	public function __construct( $std_value, $permitted_values = null ) {
		$this->value = $std_value;
		if ( ! is_null( $permitted_values ) ) {
			$this->permitted_values = $permitted_values;
		}
	}


	/**
	 * Modify several fields at once with the values given in an array
	 *
	 * @param array<string,string> $option_fields Fields with values to modify.
	 * @return void
	 */
	public function modify( $option_fields ) {
		foreach ( $option_fields as $field_name => $field_value ) {
			if ( property_exists( $this, $field_name ) ) {
				$this->$field_name = $field_value;
			} else {
				// Trigger error is allowed in this case.
				// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_trigger_error
				trigger_error( 'The requested field name "' . esc_attr( $field_name ) . '" does not exist!', E_USER_WARNING );
			}
		}
	}

}
