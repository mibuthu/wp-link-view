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
		if ( OptionValueType::Enum === $value_type ) {
			assert( $value instanceof \BackedEnum, 'Missing required backed enum value for an option with type OptionType::Enum' );
		}
		$this->value = $this->value_type->value_from( $value );
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

}
