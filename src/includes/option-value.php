<?php
/**
 * OptionValue Class
 *
 * @package link-view
 */

declare( strict_types=1 );

namespace WordPress\Plugins\mibuthu\LinkView;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly

require_once PLUGIN_PATH . 'includes/option-value-type.php';


/**
 * OptionValue Class
 *
 * This class handles an option value which can be used for shortcode, widget and plugin config options.
 */
class OptionValue {

	/**
	 * The type of the option value
	 */
	private OptionValueType $value_type;

	/**
	 * The current or the default value of the option
	 */
	private mixed $value;


	/**
	 * Class constructor which sets the required variables
	 *
	 * For an option with OptionType::Enum a value is required.
	 * For all other Option types the value is optional.
	 */
	public function __construct( OptionValueType $value_type, mixed $value = null ) {
		$this->value_type = $value_type;
		$this->value      = $this->value_type->value_from( $value );
	}


	/**
	 * Get the option value
	 */
	public function get(): mixed {
		return $this->value;
	}


	/**
	 * Get the option value as a string
	 */
	public function get_str(): string {
		return $this->value_type->as_str( $this->value );
	}


	/**
	 * Get the option value as an integer
	 */
	public function get_int(): int {
		return $this->value_type->as_int( $this->value );
	}


	/**
	 * Get the option value as a boolean
	 */
	public function get_bool(): bool {
		return $this->value_type->as_bool( $this->value );
	}


	/**
	 * Get the option value as an array
	 */
	public function get_array(): array {
		return $this->value_type->as_array( $this->value );
	}


	/**
	 * Set the option value
	 */
	public function set( mixed $value ): void {
		if ( OptionValueType::Enum === $this->value_type ) {
			assert( is_object( $value ), 'Invalid type (' . gettype( $value ) . ') for Option enum type provided' );
			assert(
				get_class( $value ) === get_class( $this->value ),
				'Invalid Option enum type provided ' .
					'(required: <' . esc_attr( get_class( $this->value ) ) . '>, provided: <' . esc_attr( get_class( $value ) ) . '>'
			);
			$this->value = $value;
		} else {
			$this->value = $this->value_type->value_from( $value );
		}
	}


	/**
	 * Set the option value from a string value
	 *
	 * Array is only allowed for option type StringArray
	 */
	public function set_from_str( string|array $value ): void {
		if ( OptionValueType::StringArray !== $this->value_type && is_array( $value ) ) {
			assert( false, 'Invalid value type <' . getType( $value ) . '> for option type ' . $this->value_type->value );
		}
		match ( $this->value_type ) {
			OptionValueType::String => $this->set( $value ),
			OptionValueType::Int => $this->set( (int) $value ),
			OptionValueType::Bool => $this->set( in_array( $value, [ null, 0, '', '0', 'false', 'False' ], true ) ? false : true ),
			OptionValueType::StringArray => $this->set( OptioNValueType::str_to_array( $value ) ),
			OptionValueType::Enum => $this->set_enum_from_str( $value ),
		};
	}


	/**
	 * Set the option of an enum from a string value
	 */
	private function set_enum_from_str( string $value ): void {
		$enum_class = get_class( $this->value );
		$enum_value = $enum_class::tryFrom( $value );
		if ( is_null( $enum_value ) ) {
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_trigger_error -- Trigger a warning is correct here
			trigger_error( 'Skip invalid enum value <' . esc_attr( $value ) . '>', E_USER_WARNING );
		} else {
			$this->set( $enum_value );
		}
	}


	/**
	 * Get the permitted values of the option
	 */
	public function permitted_values(): string|array {
		return $this->value_type->permitted_values( $this->value );
	}

}
