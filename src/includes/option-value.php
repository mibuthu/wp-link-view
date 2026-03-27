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
	 * The enum type, if the value is an enum or enum array
	 */
	private ?string $enum_type = null;

	/**
	 * The current or the default value of the option
	 */
	private mixed $value;


	/**
	 * Class constructor which sets the required variables
	 *
	 * For an option of type enum a value is required.
	 * For an option of type enum array the enum_type attribute is required.
	 * For all other option types the value is optional.
	 */
	public function __construct( OptionValueType $value_type, mixed $value = null, ?string $enum_type = null ) {
		$this->value_type = $value_type;
		$this->value      = $this->value_type->value_from( $value, $enum_type );
		if ( in_array( $value_type, [ OptionValueType::Enum, OptionValueType::EnumArray ], true ) ) {
			if ( ! is_null( $enum_type ) && is_subclass_of( $enum_type, \BackedEnum::class ) ) {
				$this->enum_type = $enum_type;
			} elseif ( ! is_null( $value ) && $value instanceof \BackedEnum ) {
				$this->enum_type = $value::class;
			} else {
				// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_trigger_error -- Trigger a warning is correct here
				trigger_error( 'Failed to get the enum type (value: <' . esc_attr( $value ) . '>, enum_type: <' . esc_attr( $enum_type ) . '>!', E_USER_WARNING );
				$this->enum_type = null;
			}
		} else {
			$this->enum_type = null;
		}
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
	public function set( mixed $value ): bool {
		$value = $this->value_type->value_from( $value, $this->enum_type );
		if ( is_null( $value ) ) {
			return false;
		}
		$this->value = $value;
		return true;
	}


	/**
	 * Set the option value from a string value
	 *
	 * Array is only allowed for option type StringArray
	 */
	public function set_from_str( string $value ): void {
		if ( ! in_array( $this->value_type, [ OptionValueType::StringArray, OptionValueType::EnumArray ], true ) && is_array( $value ) ) {
			assert( false, 'Invalid value type <' . getType( $value ) . '> for option type ' . $this->value_type->value );
		}
		$this->set(
			match ( $this->value_type ) {
				OptionValueType::String => $value,
				OptionValueType::Int => (int) $value,
				OptionValueType::Bool => in_array( $value, [ null, 0, '', '0', 'false', 'False' ], true ) ? false : true,
				OptionValueType::StringArray => OptionValueType::str_to_array( $value ),
				OptionValueType::Enum => $this->enum_from_str( $value ),
				OptionValueType::EnumArray => $this->enum_array_from_str( $value ),
			}
		);
	}


	/**
	 * Set the option of an enum array from a string value
	 */
	private function enum_array_from_str( string $value ): ?array {
		$items       = OptionValueType::str_to_array( $value );
		$enum_values = [];
		foreach ( $items as $item ) {
			$enum_value = $this->enum_from_str( $item );
			if ( is_null( $enum_value ) ) {
				return null;
			}
			$enum_values[] = $enum_value;
		}
		return $enum_values;
	}


	/**
	 * Set the option of an enum from a string value
	 */
	private function enum_from_str( string $value ): mixed {
		$enum_value = $this->enum_type::tryFrom( $value );
		if ( is_null( $enum_value ) ) {
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_trigger_error -- Trigger a warning is correct here
			trigger_error( 'Skip invalid enum value <' . esc_attr( $value ) . '>', E_USER_WARNING );
		}
		return $enum_value;
	}


	/**
	 * Get the permitted values of the option
	 */
	public function permitted_values(): array {
		return $this->value_type->permitted_values( $this->enum_type );
	}

}
