<?php
/**
 * LinkView Shortcode Attribute Class
 *
 * @package link-view
 */

declare(strict_types=1);
if ( ! defined( 'WPINC' ) ) {
	die;
}

require_once LV_PATH . 'includes/attribute.php';


/**
 * LinkView Shortcode Attribute Class
 *
 * This class handles the attributes for the shortcode [linkview].
 */
class LV_ShortcodeAtts {

	/**
	 * View Type
	 *
	 * @var LV_Attribute
	 */
	public $view_type;

	/**
	 * Category Filter
	 *
	 * @var LV_Attribute
	 */
	public $cat_filter;

	/**
	 * Excluded categories
	 *
	 * @var LV_Attribute
	 */
	public $exclude_cat;

	/**
	 * Show category name
	 *
	 * @var LV_Attribute
	 */
	public $show_cat_name;

	/**
	 * Show number of links
	 *
	 * @var LV_Attribute
	 */
	public $show_num_links;

	/**
	 * Link order field
	 *
	 * @var LV_Attribute
	 */
	public $link_orderby;

	/**
	 * Link order direction
	 *
	 * @var LV_Attribute
	 */
	public $link_order;

	/**
	 * Number of links to show
	 *
	 * @var LV_Attribute
	 */
	public $num_links;

	/**
	 * Show link image
	 *
	 * @var LV_Attribute
	 */
	public $show_img;

	/**
	 * Link items to display
	 *
	 * @var LV_Attribute
	 */
	public $link_items;

	/**
	 * Link item default image
	 *
	 * @var LV_Attribute
	 */
	public $link_item_img;

	/**
	 * Link target
	 *
	 * @var LV_Attribute
	 */
	public $link_target;

	/**
	 * Link rel attribute
	 *
	 * @var LV_Attribute
	 */
	public $link_rel;

	/**
	 * HTML class suffix
	 *
	 * @var LV_Attribute
	 */
	public $class_suffix;

	/**
	 * Used list symbol
	 *
	 * @var LV_Attribute
	 */
	public $list_symbol;

	/**
	 * Vertical alignment
	 *
	 * @var LV_Attribute
	 */
	public $vertical_align;

	/**
	 * Category columns settings
	 *
	 * @var LV_Attribute
	 */
	public $cat_columns;

	/**
	 * Link columns settings
	 *
	 * @var LV_Attribute
	 */
	public $link_columns;

	/**
	 * Slider width
	 *
	 * @var LV_Attribute
	 */
	public $slider_width;

	/**
	 * Slider height
	 *
	 * @var LV_Attribute
	 */
	public $slider_height;

	/**
	 * Slider pause duration
	 *
	 * @var LV_Attribute
	 */
	public $slider_pause;

	/**
	 * Slider speed
	 *
	 * @var LV_Attribute
	 */
	public $slider_speed;


	/**
	 * Class constructor which initializes required variables
	 *
	 * @return void
	 */
	public function __construct() {
		$this->view_type      = new LV_Attribute( 'list', array( 'list', 'slider' ) );
		$this->cat_filter     = new LV_Attribute( '' );
		$this->exclude_cat    = new LV_Attribute( '' );
		$this->show_cat_name  = new LV_Attribute( '1', array( '0', '1' ) );
		$this->show_num_links = new LV_Attribute( '0', array( '0', '1' ) );
		$this->link_orderby   = new LV_Attribute( 'name', array( 'link_id', 'url', 'name', 'owner', 'rating', 'visible', 'length', 'rand' ) );
		$this->link_order     = new LV_Attribute( 'asc', array( 'asc', 'desc' ) );
		$this->num_links      = new LV_Attribute( '-1' );
		$this->show_img       = new LV_Attribute( '0', array( '0', '1' ) );
		$this->link_items     = new LV_Attribute( '' );
		$this->link_item_img  = new LV_Attribute( 'show_img_tag', array( 'show_img_tag', 'show_link_name', 'show_link_description', 'show_nothing' ) );
		$this->link_target    = new LV_Attribute( 'std', array( 'std', 'blank', 'top', 'self' ) );
		$this->link_rel       = new LV_Attribute( 'noopener', array( '', 'alternate', 'author', 'bookmark', 'external', 'help', 'license', 'next', 'nofollow', 'noreferrer', 'noopener', 'prev', 'search', 'tag' ) );
		$this->class_suffix   = new LV_Attribute( '' );
		$this->list_symbol    = new LV_Attribute( 'std', array( 'std', 'none', 'circle', 'square', 'disc' ) );
		$this->vertical_align = new LV_Attribute( 'std', array( 'std', 'top', 'bottom', 'middle' ) );
		$this->cat_columns    = new LV_Attribute( '1' );
		$this->link_columns   = new LV_Attribute( '1' );
		$this->slider_width   = new LV_Attribute( '0' );
		$this->slider_height  = new LV_Attribute( '0' );
		$this->slider_pause   = new LV_Attribute( '6000' );
		$this->slider_speed   = new LV_Attribute( '1000' );
	}


	/**
	 * Set the values of multiple attributes
	 *
	 * @param array $atts Attributes to set.
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
		require_once LV_PATH . 'includes/shortcode-atts-helptexts.php';
		foreach ( $lv_shortcode_atts_helptexts as $name => $values ) {
			if ( isset( $this->$name ) ) {
				$this->$name->modify( $values );
			}
		}
		unset( $lv_shortcode_atts_helptexts );
	}

}
