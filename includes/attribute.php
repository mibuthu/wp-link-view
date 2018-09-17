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
	 * Attribute default value
	 *
	 * @var string
	 */
	public $std_val;

	/**
	 * Attribute value
	 *
	 * @var null|string
	 */
	public $val = null;

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
	 * @param string      $std_val Standard attribute value.
	 * @param string|null $val     Attribute value (optional).
	 * @return void
	 */
	public function __construct( $std_val, $val = null ) {
		$this->std_val = $std_val;
		if ( ! is_null( $val ) ) {
			$this->val = $val;
		}
	}


	/**
	 * Modify several fields at once with the values given in an array
	 *
	 * @param array $items Fields with values to modify.
	 * @return void
	 */
	public function modify( $items ) {
		foreach ( $items as $itemname => $itemvalue ) {
			if ( property_exists( $this, $itemname ) ) {
				$this->$itemname = $itemvalue;
			}
		}
	}

}
