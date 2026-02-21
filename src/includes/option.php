<?php
/**
 * Option Class
 *
 * @package link-view
 */

declare( strict_types=1 );

namespace WordPress\Plugins\mibuthu\LinkView;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly


/**
 * Option Class
 *
 * This class handles an option which can be used for shortcode, widget and plugin config options.
 */
class Option {

	/**
	 * Actual or default value
	 */
	public string $value;

	/**
	 * Permitted values
	 */
	public string|array $permitted_values = '';

	/**
	 * The boolean TRUE value option
	 */
	const string TRUE = 'true';

	/**
	 * The boolean FALSE value option
	 */
	const string FALSE = 'false';

	/**
	 * The boolean value options
	 *
	 * @var string[]
	 */
	const array BOOLEAN = [ self::TRUE, self::FALSE ];


	/**
	 * Class constructor which sets the required variables
	 */
	public function __construct( string $std_value, null|string|array $permitted_values = null ) {
		$this->value = $std_value;
		if ( ! is_null( $permitted_values ) ) {
			$this->permitted_values = $permitted_values;
		}
	}


	/**
	 * Modify several fields at once with the values given in an array
	 *
	 * @param array<string,string> $option_fields Fields with values to modify.
	 */
	public function modify( array $option_fields ): void {
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


	/**
	 * Return a if the option is a boolean value
	 */
	public function is_bool(): bool {
		return self::BOOLEAN === $this->permitted_values;
	}


	/**
	 * Return a boolean value if the option is a boolean, or the value string if not
	 */
	public function bool_value(): bool|string {
		if ( ! $this->is_bool() ) {
			return $this->value;
		}
		// Numbers > 0 are also accepted as true.
		if ( 0 < intval( $this->value ) ) {
			return true;
		}
		return self::TRUE === $this->value;
	}

}
