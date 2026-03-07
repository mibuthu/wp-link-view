<?php
/**
 * LinkView Shortcode Attribute Class
 *
 * @package link-view
 */

declare( strict_types=1 );

namespace WordPress\Plugins\mibuthu\LinkView\Shortcode;

use const WordPress\Plugins\mibuthu\LinkView\PLUGIN_PATH;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly

require_once PLUGIN_PATH . 'includes/option-value.php';


use WordPress\Plugins\mibuthu\LinkView\OptionValue;


/**
 * LinkView Shortcode Config Class
 *
 * This class handles the attributes for the shortcode [linkview].
 *
 * @property string $view_type
 * @property string $cat_filter
 * @property string $exclude_cat
 * @property string $cat_grouping
 * @property string $show_cat_name
 * @property string $show_num_links
 * @property string $link_orderby
 * @property string $link_order
 * @property string $num_links
 * @property string $show_img
 * @property string $link_items
 * @property string $link_item_img
 * @property string $link_target
 * @property string $link_rel
 * @property string $custom_class
 * @property string $class_suffix
 * @property string $vertical_align
 * @property string $list_symbol
 * @property string $cat_columns
 * @property string $link_columns
 * @property string $slider_width
 * @property string $slider_height
 * @property string $slider_pause
 * @property string $slider_speed
 */
class Config {

	/**
	 * Shortcode attributes
	 *
	 * @var array<string,OptionValue>
	 */
	private array $atts;


	/**
	 * Class constructor which initializes required variables
	 */
	public function __construct() {
		$this->atts = [
			'view_type'      => new OptionValue( 'list', [ 'list', 'slider' ] ),
			'cat_filter'     => new OptionValue( '' ),
			'exclude_cat'    => new OptionValue( '' ),
			'cat_grouping'   => new OptionValue( OptionValue::TRUE, OptionValue::BOOLEAN ),
			'show_cat_name'  => new OptionValue( OptionValue::TRUE, OptionValue::BOOLEAN ),
			'show_num_links' => new OptionValue( OptionValue::FALSE, OptionValue::BOOLEAN ),
			'link_orderby'   => new OptionValue( 'name', [ 'link_id', 'url', 'name', 'owner', 'rating', 'visible', 'length', 'rand' ] ),
			'link_order'     => new OptionValue( 'asc', [ 'asc', 'desc' ] ),
			'num_links'      => new OptionValue( '-1' ),
			'show_img'       => new OptionValue( OptionValue::FALSE, OptionValue::BOOLEAN ),
			'link_items'     => new OptionValue( '' ),
			'link_item_img'  => new OptionValue( 'show_img_tag', [ 'show_img_tag', 'show_link_name', 'show_link_description', 'show_nothing' ] ),
			'link_target'    => new OptionValue( 'std', [ 'std', 'blank', 'top', 'self' ] ),
			'link_rel'       => new OptionValue( 'noopener', [ '', 'alternate', 'author', 'bookmark', 'external', 'help', 'license', 'next', 'nofollow', 'noreferrer', 'noopener', 'prev', 'search', 'tag' ] ),
			'custom_class'   => new OptionValue( '' ),
			'class_suffix'   => new OptionValue( '' ),
			'vertical_align' => new OptionValue( 'std', [ 'std', 'top', 'bottom', 'middle' ] ),
			'list_symbol'    => new OptionValue( 'std', [ 'std', 'none', 'circle', 'square', 'disc' ] ),
			'cat_columns'    => new OptionValue( '1' ),
			'link_columns'   => new OptionValue( '1' ),
			'slider_width'   => new OptionValue( '0' ),
			'slider_height'  => new OptionValue( '0' ),
			'slider_pause'   => new OptionValue( '6000' ),
			'slider_speed'   => new OptionValue( '1000' ),
		];
	}


	/**
	 * Set the values of multiple attributes
	 *
	 * @param array<string,string> $atts Attributes to set.
	 */
	public function set_values( array $atts ): void {
		if ( ! is_array( $atts ) ) {
			return;
		}
		foreach ( $atts as $name => $value ) {
			if ( isset( $this->atts[ $name ] ) ) {
				// @phan-suppress-next-line PhanPartialTypeMismatchArgumentInternal
				if ( ! is_array( $this->atts [ $name ]->permitted_values ) || in_array( $value, $this->atts [ $name ]->permitted_values, true ) || $this->atts[ $name ]->is_bool() ) {
					$this->atts[ $name ]->value = $value;
				}
			} else {
				// Trigger error is allowed in this case.
				// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_trigger_error
				trigger_error( 'Shortcode attribute "' . esc_attr( $name ) . '" does not exist!', E_USER_WARNING );
			}
		}
	}


	/**
	 * Get the value of the given attribute
	 *
	 * If the option is a boolean value, a bool is returned.
	 */
	public function __get( string $name ): string|bool {
		if ( isset( $this->atts[ $name ] ) ) {
			return $this->atts[ $name ]->bool_value();
		}
		// Trigger error is allowed in this case.
		// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_trigger_error
		trigger_error( 'Shortcode attribute "' . esc_attr( $name ) . '" does not exist!', E_USER_WARNING );
		return '';
	}


	/**
	 * Set the value of the given attribute
	 */
	public function __set( string $name, string $value ): void {
		if ( isset( $this->atts[ $name ] ) ) {
			$this->atts[ $name ]->value = $value;
		}
		// Trigger error is allowed in this case.
		// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_trigger_error
		trigger_error( 'Shortcode attribute "' . esc_attr( $name ) . '" does not exist!', E_USER_WARNING );
	}


	/**
	 * Get a complete attribute
	 */
	public function get( string $name ): ?OptionValue {
		if ( isset( $this->atts[ $name ] ) ) {
			return $this->atts[ $name ];
		}
		// Trigger error is allowed in this case.
		// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_trigger_error
		trigger_error( 'Shortcode attribute "' . esc_attr( $name ) . '" does not exist!', E_USER_WARNING );
		return null;
	}

}
