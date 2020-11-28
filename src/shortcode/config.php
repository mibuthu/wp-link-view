<?php
/**
 * LinkView Shortcode Attribute Class
 *
 * @package link-view
 */

// declare( strict_types=1 ); Remove for now due to warnings in php <7.0!

namespace WordPress\Plugins\mibuthu\LinkView\Shortcode;

if ( ! defined( 'WPINC' ) ) {
	exit();
}

require_once PLUGIN_PATH . 'includes/option.php';


use WordPress\Plugins\mibuthu\LinkView\Option;


/**
 * LinkView Shortcode Config Class
 *
 * This class handles the attributes for the shortcode [linkview].
 *
 * @property string $view_type
 * @property string $cat_filter
 * @property string $exclude_cat
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
	 * @var array<string,Option>
	 */
	private $atts;


	/**
	 * Class constructor which initializes required variables
	 *
	 * @return void
	 */
	public function __construct() {
		$this->atts = [
			'view_type'      => new Option( 'list', [ 'list', 'slider' ] ),
			'cat_filter'     => new Option( '' ),
			'exclude_cat'    => new Option( '' ),
			'show_cat_name'  => new Option( '1', Option::BOOLEAN ),
			'show_num_links' => new Option( '0', Option::BOOLEAN ),
			'link_orderby'   => new Option( 'name', [ 'link_id', 'url', 'name', 'owner', 'rating', 'visible', 'length', 'rand' ] ),
			'link_order'     => new Option( 'asc', [ 'asc', 'desc' ] ),
			'num_links'      => new Option( '-1' ),
			'show_img'       => new Option( '0', Option::BOOLEAN ),
			'link_items'     => new Option( '' ),
			'link_item_img'  => new Option( 'show_img_tag', [ 'show_img_tag', 'show_link_name', 'show_link_description', 'show_nothing' ] ),
			'link_target'    => new Option( 'std', [ 'std', 'blank', 'top', 'self' ] ),
			'link_rel'       => new Option( 'noopener', [ '', 'alternate', 'author', 'bookmark', 'external', 'help', 'license', 'next', 'nofollow', 'noreferrer', 'noopener', 'prev', 'search', 'tag' ] ),
			'class_suffix'   => new Option( '' ),
			'vertical_align' => new Option( 'std', [ 'std', 'top', 'bottom', 'middle' ] ),
			'list_symbol'    => new Option( 'std', [ 'std', 'none', 'circle', 'square', 'disc' ] ),
			'cat_columns'    => new Option( '1' ),
			'link_columns'   => new Option( '1' ),
			'slider_width'   => new Option( '0' ),
			'slider_height'  => new Option( '0' ),
			'slider_pause'   => new Option( '6000' ),
			'slider_speed'   => new Option( '1000' ),
		];
	}


	/**
	 * Set the values of multiple attributes
	 *
	 * @param array<string,string> $atts Attributes to set.
	 * @return void
	 */
	public function set_values( $atts ) {
		if ( ! is_array( $atts ) ) {
			return;
		}
		foreach ( $atts as $name => $value ) {
			if ( isset( $this->atts[ $name ] ) ) {
				// @phan-suppress-next-line PhanPartialTypeMismatchArgumentInternal
				if ( ! is_array( $this->atts [ $name ]->permitted_values ) || in_array( $value, $this->atts [ $name ]->permitted_values, true ) ) {
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
	 *
	 * @param string $name Attribute name.
	 * @return string|bool Attribute value.
	 */
	public function __get( $name ) {
		if ( isset( $this->atts[ $name ] ) ) {
			return $this->atts[ $name ]->bool_value();
		}
		// Trigger error is allowed in this case.
		// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_trigger_error
		trigger_error( 'Shortcode attribute "' . esc_attr( $name ) . '" does not exist!', E_USER_WARNING );
	}


	/**
	 * Set the value of the given attribute
	 *
	 * @param string $name Attribute name.
	 * @param string $value Attribute value.
	 * @return void
	 */
	public function __set( $name, $value ) {
		if ( isset( $this->atts[ $name ] ) ) {
			$this->atts[ $name ]->value = $value;
		}
		// Trigger error is allowed in this case.
		// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_trigger_error
		trigger_error( 'Shortcode attribute "' . esc_attr( $name ) . '" does not exist!', E_USER_WARNING );
	}


	/**
	 * Get a complete attribute
	 *
	 * @param string $name Attribute name.
	 * @return Option
	 */
	public function get( $name ) {
		if ( isset( $this->atts[ $name ] ) ) {
			return $this->atts[ $name ];
		}
		// Trigger error is allowed in this case.
		// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_trigger_error
		trigger_error( 'Shortcode attribute "' . esc_attr( $name ) . '" does not exist!', E_USER_WARNING );
	}


	/**
	 * Get all specified attributes
	 *
	 * @param string $section Optional, to only get the atts of the given section.
	 * @return array<string,Option>
	 */
	public function get_all( $section = null ) {
		if ( is_null( $section ) ) {
			return $this->atts;
		}
		$atts = [];
		foreach ( $this->atts as $name => $attr ) {
			if ( $attr->section === $section ) {
				$atts[ $name ] = $attr;
			}
		}
		return $atts;
	}


	/**
	 * Load the additional shortcode attribute data
	 *
	 * @return void
	 */
	public function load_admin_data() {
		require_once PLUGIN_PATH . 'shortcode/config-admin-data.php';
		$atts_admin_data = new ConfigAdminData();
		foreach ( array_keys( $this->atts ) as $attr_name ) {
			$this->atts[ $attr_name ]->modify( $atts_admin_data->$attr_name );
		}
	}

}
