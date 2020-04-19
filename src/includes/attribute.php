<?php
/**
 * Attribute Class
 *
 * @package link-view
 */

// declare( strict_types=1 ); Remove for now due to warnings in php <7.0!
if ( ! defined( 'WPINC' ) ) {
	exit();
}


/**
 * Attribute Class
 *
 * This class handles the attributes for shortcode, widget options and plugin options.
 */
class LV_Attribute {

	/**
	 * Attribute (default) value
	 *
	 * @var string
	 */
	public $value;

	/**
	 * Attribute value options
	 *
	 * @var string|array
	 */
	public $value_options = '';

	/**
	 * Attribute section
	 *
	 * @var string
	 * @phan-suppress PhanUnreferencedPublicProperty
	 */
	public $section = '';

	/**
	 * Attribute type
	 *
	 * @var string
	 * @phan-suppress PhanReadOnlyPublicProperty
	 */
	public $type = '';

	/**
	 * Attribute label
	 *
	 * @var string
	 * @phan-suppress PhanReadOnlyPublicProperty
	 */
	public $label = '';

	/**
	 * Attribute caption
	 *
	 * @var string|array
	 * @phan-suppress PhanReadOnlyPublicProperty
	 */
	public $caption = '';

	/**
	 * Attribute description
	 *
	 * @var string
	 * @phan-suppress PhanReadOnlyPublicProperty
	 */
	public $description = '';

	/**
	 * Attribute tooltip
	 *
	 * @var string
	 * @phan-suppress PhanReadOnlyPublicProperty
	 */
	public $tooltip = '';


	/**
	 * Class constructor which sets the required variables
	 *
	 * @param string            $std_value Standard attribute value.
	 * @param null|string|array $value_options Attribute value (optional).
	 * @return void
	 */
	public function __construct( $std_value, $value_options = null ) {
		$this->value = $std_value;
		if ( ! is_null( $value_options ) ) {
			$this->value_options = $value_options;
		}
	}


	/**
	 * Modify several fields at once with the values given in an array
	 *
	 * @param array<string,string> $attributes Fields with values to modify.
	 * @return void
	 */
	public function modify( $attributes ) {
		foreach ( $attributes as $name => $value ) {
			if ( property_exists( $this, $name ) ) {
				$this->$name = $value;
			} else {
				// Trigger error is allowed in this case.
				// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_trigger_error
				trigger_error( 'The requested attribute "' . esc_attr( $name ) . '" does not exist!', E_USER_WARNING );
			}
		}
	}

}
