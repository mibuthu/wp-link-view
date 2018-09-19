<?php
/**
 * Attribute Class
 *
 * @package link-view
 */

declare(strict_types=1);
if ( ! defined( 'WPINC' ) ) {
	die;
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
	 * @var null|string|array
	 */
	public $value_options = null;

	/**
	 * Attribute section
	 *
	 * @var null|string
	 */
	public $section = null;

	/**
	 * Attribute type
	 *
	 * @var null|string
	 */
	public $type = null;

	/**
	 * Attribute label
	 *
	 * @var null|string
	 */
	public $label = null;

	/**
	 * Attribute caption
	 *
	 * @var null|string
	 */
	public $caption = null;

	/**
	 * Attribute description
	 *
	 * @var string|null
	 */
	public $description = null;

	/**
	 * Attribute tooltip
	 *
	 * @var string|null
	 */
	public $tooltip = null;


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
	 * @param array $attributes Fields with values to modify.
	 * @return void
	 */
	public function modify( $attributes ) {
		foreach ( $attributes as $name => $value ) {
			if ( property_exists( $this, $name ) ) {
				$this->$name = $value;
			} else {
				// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_trigger_error
				trigger_error( 'Attribute "' . esc_attr( $name ) . '" does not exist!', E_USER_WARNING );
			}
		}
	}

}
