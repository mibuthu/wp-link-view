<?php
/**
 * OptionValueType Enum
 *
 * @package link-view
 */

declare( strict_types=1 );

namespace WordPress\Plugins\mibuthu\LinkView;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly


/**
 * Option Type Enum
 *
 * An Enum that defines all allowed types of an option.
 */
enum OptionValueType {
	case String;
	case Int;
	case Bool;
	case StringArray;
	case Enum;


	/**
	 * Check and get the value from a provided value
	 *
	 * If the value is $null, the default value will be returned.
	 */
	public function value_from( mixed $value ): mixed {
		if ( is_null( $value ) ) {
			// return the default value
			return $this->default_value();
		}
		if ( $this->is_correct_type( $value ) ) {
			return $value;
		}
		assert( true, 'Invalid type provided for an option with the type ' . esc_attr( $this->name ) );
		return null;
	}


	/**
	 * Returns the default value of the type
	 */
	public function default_value(): mixed {
		return match ( $this ) {
			self::String => (string) '',
			self::Int => (int) 0,
			self::Bool => (bool) false,
			self::StringArray => [],
			self::Enum => assert( true, 'No default value for option type = Enum available', E_USER_WARNING ),
		};
	}


	/**
	 * Checks if the provided value has the correct type
	 */
	private function is_correct_type( mixed $value ): bool {
		return match ( $this ) {
			self::String => is_string( $value ),
			self::Int => is_int( $value ),
			self::Bool => is_bool( $value ),
			self::StringArray => is_array( $value ) && array_reduce( $value, fn( bool $carry, $item ) => $carry && is_string( $item ), true ),
			self::Enum => false,
		};
	}


	/**
	 * Convert the provided value to a string
	 */
	public function as_str( mixed $value ): string {
		if ( is_null( $value ) ) {
			return '';
		}
		return (string) match ( $this ) {
			self::String => $value,
			self::Int => $value,
			self::Bool => $value ? 'true' : 'false',
			self::StringArray => implode( ', ', $value ),
			self::Enum => $value->value,
		};
	}

}
