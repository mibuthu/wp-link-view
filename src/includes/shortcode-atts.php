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
 */
class ShortcodeAtts {

	/**
	 * View Type
	 *
	 * @var Attribute
	 */
	public $view_type;

	/**
	 * Category Filter
	 *
	 * @var Attribute
	 */
	public $cat_filter;

	/**
	 * Excluded categories
	 *
	 * @var Attribute
	 */
	public $exclude_cat;

	/**
	 * Show category name
	 *
	 * @var Attribute
	 */
	public $show_cat_name;

	/**
	 * Show number of links
	 *
	 * @var Attribute
	 */
	public $show_num_links;

	/**
	 * Link order field
	 *
	 * @var Attribute
	 */
	public $link_orderby;

	/**
	 * Link order direction
	 *
	 * @var Attribute
	 */
	public $link_order;

	/**
	 * Number of links to show
	 *
	 * @var Attribute
	 */
	public $num_links;

	/**
	 * Show link image
	 *
	 * @var Attribute
	 */
	public $show_img;

	/**
	 * Link items to display
	 *
	 * @var Attribute
	 */
	public $link_items;

	/**
	 * Link item default image
	 *
	 * @var Attribute
	 */
	public $link_item_img;

	/**
	 * Link target
	 *
	 * @var Attribute
	 */
	public $link_target;

	/**
	 * Link rel attribute
	 *
	 * @var Attribute
	 */
	public $link_rel;

	/**
	 * HTML class suffix
	 *
	 * @var Attribute
	 */
	public $class_suffix;

	/**
	 * Used list symbol
	 *
	 * @var Attribute
	 */
	public $list_symbol;

	/**
	 * Vertical alignment
	 *
	 * @var Attribute
	 */
	public $vertical_align;

	/**
	 * Category columns settings
	 *
	 * @var Attribute
	 */
	public $cat_columns;

	/**
	 * Link columns settings
	 *
	 * @var Attribute
	 */
	public $link_columns;

	/**
	 * Slider width
	 *
	 * @var Attribute
	 */
	public $slider_width;

	/**
	 * Slider height
	 *
	 * @var Attribute
	 */
	public $slider_height;

	/**
	 * Slider pause duration
	 *
	 * @var Attribute
	 */
	public $slider_pause;

	/**
	 * Slider speed
	 *
	 * @var Attribute
	 */
	public $slider_speed;


	/**
	 * Class constructor which initializes required variables
	 *
	 * @return void
	 */
	public function __construct() {
		$this->view_type      = new Attribute( 'list', [ 'list', 'slider' ] );
		$this->cat_filter     = new Attribute( '' );
		$this->exclude_cat    = new Attribute( '' );
		$this->show_cat_name  = new Attribute( '1', [ '0', '1' ] );
		$this->show_num_links = new Attribute( '0', [ '0', '1' ] );
		$this->link_orderby   = new Attribute( 'name', [ 'link_id', 'url', 'name', 'owner', 'rating', 'visible', 'length', 'rand' ] );
		$this->link_order     = new Attribute( 'asc', [ 'asc', 'desc' ] );
		$this->num_links      = new Attribute( '-1' );
		$this->show_img       = new Attribute( '0', [ '0', '1' ] );
		$this->link_items     = new Attribute( '' );
		$this->link_item_img  = new Attribute( 'show_img_tag', [ 'show_img_tag', 'show_link_name', 'show_link_description', 'show_nothing' ] );
		$this->link_target    = new Attribute( 'std', [ 'std', 'blank', 'top', 'self' ] );
		$this->link_rel       = new Attribute( 'noopener', [ '', 'alternate', 'author', 'bookmark', 'external', 'help', 'license', 'next', 'nofollow', 'noreferrer', 'noopener', 'prev', 'search', 'tag' ] );
		$this->class_suffix   = new Attribute( '' );
		$this->vertical_align = new Attribute( 'std', [ 'std', 'top', 'bottom', 'middle' ] );
		$this->list_symbol    = new Attribute( 'std', [ 'std', 'none', 'circle', 'square', 'disc' ] );
		$this->cat_columns    = new Attribute( '1' );
		$this->link_columns   = new Attribute( '1' );
		$this->slider_width   = new Attribute( '0' );
		$this->slider_height  = new Attribute( '0' );
		$this->slider_pause   = new Attribute( '6000' );
		$this->slider_speed   = new Attribute( '1000' );
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
			if ( property_exists( $this, $name ) ) {
				if ( ! is_array( $this->$name->value_options ) || in_array( $value, $this->$name->value_options, true ) ) {
					$this->$name->value = $value;
				}
			} else {
				// Trigger error is allowed in this case.
				// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_trigger_error
				trigger_error( 'Shortcode attribute "' . esc_attr( $name ) . '" does not exist!', E_USER_WARNING );
			}
		}
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
			if ( isset( $this->$name ) ) {
				$this->$name->modify( $values );
			}
		}
		unset( $lv_shortcode_atts_helptexts );
	}

}
