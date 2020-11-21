<?php
/**
 * LinkView Shortcode Attribute Class
 *
 * @package link-view
 */

// declare( strict_types=1 ); Remove for now due to warnings in php <7.0!

namespace WordPress\Plugins\mibuthu\LinkView;

if ( ! defined( 'WPINC' ) ) {
	exit();
}

require_once PLUGIN_PATH . 'includes/attribute.php';


/**
 * LinkView Shortcode Attribute Class
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
class ShortcodeAtts {

	/**
	 * View Type
	 *
	 * @var array<string,Attribute>
	 */
	private $shortcode_atts;


	/**
	 * Class constructor which initializes required variables
	 *
	 * @return void
	 */
	public function __construct() {
		$this->shortcode_atts = [
			'view_type'      => new Attribute( 'list', [ 'list', 'slider' ] ),
			'cat_filter'     => new Attribute( '' ),
			'exclude_cat'    => new Attribute( '' ),
			'show_cat_name'  => new Attribute( '1', [ '0', '1' ] ),
			'show_num_links' => new Attribute( '0', [ '0', '1' ] ),
			'link_orderby'   => new Attribute( 'name', [ 'link_id', 'url', 'name', 'owner', 'rating', 'visible', 'length', 'rand' ] ),
			'link_order'     => new Attribute( 'asc', [ 'asc', 'desc' ] ),
			'num_links'      => new Attribute( '-1' ),
			'show_img'       => new Attribute( '0', [ '0', '1' ] ),
			'link_items'     => new Attribute( '' ),
			'link_item_img'  => new Attribute( 'show_img_tag', [ 'show_img_tag', 'show_link_name', 'show_link_description', 'show_nothing' ] ),
			'link_target'    => new Attribute( 'std', [ 'std', 'blank', 'top', 'self' ] ),
			'link_rel'       => new Attribute( 'noopener', [ '', 'alternate', 'author', 'bookmark', 'external', 'help', 'license', 'next', 'nofollow', 'noreferrer', 'noopener', 'prev', 'search', 'tag' ] ),
			'class_suffix'   => new Attribute( '' ),
			'vertical_align' => new Attribute( 'std', [ 'std', 'top', 'bottom', 'middle' ] ),
			'list_symbol'    => new Attribute( 'std', [ 'std', 'none', 'circle', 'square', 'disc' ] ),
			'cat_columns'    => new Attribute( '1' ),
			'link_columns'   => new Attribute( '1' ),
			'slider_width'   => new Attribute( '0' ),
			'slider_height'  => new Attribute( '0' ),
			'slider_pause'   => new Attribute( '6000' ),
			'slider_speed'   => new Attribute( '1000' ),
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
			if ( isset( $this->shortcode_atts[ $name ] ) ) {
				// @phan-suppress-next-line PhanPartialTypeMismatchArgumentInternal
				if ( ! is_array( $this->shortcode_atts [ $name ]->value_options ) || in_array( $value, $this->shortcode_atts [ $name ]->value_options, true ) ) {
					$this->shortcode_atts[ $name ]->value = $value;
				}
			} else {
				// Trigger error is allowed in this case.
				// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_trigger_error
				trigger_error( 'Shortcode attribute "' . esc_attr( $name ) . '" does not exist!', E_USER_WARNING );
			}
		}
	}


	/**
	 * Get the value of the given attribute.
	 *
	 * @param string $name Attribute name.
	 * @return string Attribute value.
	 */
	public function __get( $name ) {
		if ( isset( $this->shortcode_atts[ $name ] ) ) {
			return $this->shortcode_atts[ $name ]->value;
		}
		// Trigger error is allowed in this case.
		// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_trigger_error
		trigger_error( 'Shortcode attribute "' . esc_attr( $name ) . '" does not exist!', E_USER_WARNING );
	}


	/**
	 * Set the value of the given attribute.
	 *
	 * @param string $name Attribute name.
	 * @param string $value Attribute value.
	 * @return void
	 */
	public function __set( $name, $value ) {
		if ( isset( $this->shortcode_atts[ $name ] ) ) {
			$this->shortcode_atts[ $name ]->value = $value;
		}
		// Trigger error is allowed in this case.
		// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_trigger_error
		trigger_error( 'Shortcode attribute "' . esc_attr( $name ) . '" does not exist!', E_USER_WARNING );
	}


	/**
	 * Get a complete attribute.
	 *
	 * @param string $name Attribute name.
	 * @return Attribute
	 */
	public function get( $name ) {
		if ( isset( $this->shortcode_atts[ $name ] ) ) {
			return $this->shortcode_atts[ $name ];
		}
		// Trigger error is allowed in this case.
		// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_trigger_error
		trigger_error( 'Shortcode attribute "' . esc_attr( $name ) . '" does not exist!', E_USER_WARNING );
	}


	/**
	 * Get all specified options
	 *
	 * @return array<string,Attribute>
	 */
	public function get_all() {
		return $this->shortcode_atts;
	}


	/**
	 * Load shortcode helptexts (required for admin page only)
	 *
	 * @return void
	 */
	public function load_helptexts() {
		global $lv_shortcode_atts_helptexts;
		require_once PLUGIN_PATH . 'includes/shortcode-atts-helptexts.php';
		foreach ( $lv_shortcode_atts_helptexts as $name => $values ) {
			if ( isset( $this->shortcode_atts[ $name ] ) ) {
				$this->shortcode_atts[ $name ]->modify( $values );
			}
		}
		unset( $lv_shortcode_atts_helptexts );
	}

}
