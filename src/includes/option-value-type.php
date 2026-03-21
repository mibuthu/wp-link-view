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
			self::Enum => $value instanceof \BackedEnum,
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


	/**
	 * Convert the provided value to an integer
	 */
	public function as_int( mixed $value ): int {
		if ( is_null( $value ) ) {
			return 0;
		}
		return (int) match ( $this ) {
			self::String => $value,
			self::Int => $value,
			self::Bool => $value ? 1 : 0,
			self::StringArray => count( $value ),
			self::Enum => $value->value,
		};
	}


	/**
	 * Convert the provided value to a boolean
	 */
	public function as_bool( mixed $value ): bool {
		if ( is_null( $value ) ) {
			return false;
		}
		return match ( $this ) {
			self::String => ! in_array( $value, [ '', '0' ], true ),
			self::Int => 0 !== $value,
			self::Bool => (bool) $value,
			self::StringArray => count( $value ) > 0,
			self::Enum => true,
		};
	}


	/**
	 * Convert the provided value to an array
	 */
	public function as_array( mixed $value ): array {
		if ( is_null( $value ) ) {
			return [];
		}
		return match ( $this ) {
			self::String => $this->str_to_array( $value ),
			self::Int => [ $value ],
			self::Bool => [],
			self::StringArray => $value,
			self::Enum => [ $value->value ],
		};
	}


	/**
	 * Transform a string to an array
	 *
	 * Allowed separators are '|' and ','
	 */
	public static function str_to_array( string $str_value ): array {
		$str_value = str_replace( ',', '|', $str_value );
		return array_map( trim( ... ), array_map( strval( ... ), explode( '|', $str_value ) ) );
	}


	/**
	 * Get the permitted values of the option as a string or an array of strings
	 *
	 * If the option type is an enum, the value is required and all enum variants are returned.
	 * For all other types a static string with the type name will be returned.
	 */
	public function permitted_values( mixed $value = null ): string|array {
		return match ( $this ) {
			OptionValueType::String => 'String',
			OptionValueType::Int => 'Integer',
			OptionValueType::Bool => [ 'true', 'false' ],
			OptionValueType::StringArray => 'List of strings',
			OptionValueType::Enum => array_map( fn ( $c ) => $c->value, $value::cases() ),
		};
	}

}
