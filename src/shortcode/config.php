<?php
/**
 * LinkView Shortcode Attribute Class
 *
 * @package link-view
 *
 * phpcs:disable Generic.Files.OneObjectStructurePerFile.MultipleFound -- enums for the options are in the same file
 */

declare( strict_types=1 );

namespace WordPress\Plugins\mibuthu\LinkView\Shortcode;

use const WordPress\Plugins\mibuthu\LinkView\PLUGIN_PATH;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly

require_once PLUGIN_PATH . 'includes/option-value.php';


use WordPress\Plugins\mibuthu\LinkView\OptionValue;
use WordPress\Plugins\mibuthu\LinkView\OptionValueType;

enum ViewType: string {
	case List   = 'list';
	case Slider = 'slider';
}

enum LinkOrderBy: string {
	case LinkId  = 'link_id';
	case Url     = 'url';
	case Name    = 'name';
	case Owner   = 'owner';
	case Rating  = 'rating';
	case Visible = 'visible';
	case Length  = 'length';
	case Rand    = 'rand';
}


enum LinkOrder: string {
	case Asc  = 'asc';
	case Desc = 'desc';
}


enum LinkItemImg: string {
	case ShowImgTag          = 'show_img_tag';
	case ShowLinkName        = 'show_link_name';
	case ShowLinkDescription = 'show_link_description';
	case ShowNothing         = 'show_nothing';
}


enum LinkTarget: string {
	case Std   = 'std';
	case Blank = 'blank';
	case Top   = 'top';
	case Self  = 'self';
}


enum LinkRel: string {
	case Empty      = '';
	case Alternate  = 'alternate';
	case Author     = 'author';
	case Bookmark   = 'bookmark';
	case External   = 'external';
	case Help       = 'help';
	case License    = 'license';
	case Next       = 'next';
	case NoFollow   = 'nofollow';
	case NoReferrer = 'noreferrer';
	case NoOpener   = 'noopener';
	case Prev       = 'prev';
	case Search     = 'search';
	case Tag        = 'tag';
}


enum VerticalAlign: string {
	case Std    = 'std';
	case Top    = 'top';
	case Bottom = 'bottom';
	case Middle = 'middle';
}


enum ListSymbol: string {
	case Std    = 'std';
	case None   = 'none';
	case Circle = 'circle';
	case Square = 'square';
	case Disc   = 'disc';
}


/**
 * LinkView Shortcode Config Class
 *
 * This class handles the attributes for the shortcode [linkview].
 *
 * @property OptionValue $view_type
 * @property OptionValue $cat_filter
 * @property OptionValue $exclude_cat
 * @property OptionValue $cat_grouping
 * @property OptionValue $show_cat_name
 * @property OptionValue $show_num_links
 * @property OptionValue $link_orderby
 * @property OptionValue $link_order
 * @property OptionValue $num_links
 * @property OptionValue $show_img
 * @property OptionValue $link_items
 * @property OptionValue $link_item_img
 * @property OptionValue $link_target
 * @property OptionValue $link_rel
 * @property OptionValue $custom_class
 * @property OptionValue $class_suffix
 * @property OptionValue $vertical_align
 * @property OptionValue $list_symbol
 * @property OptionValue $cat_columns
 * @property OptionValue $link_columns
 * @property OptionValue $slider_width
 * @property OptionValue $slider_height
 * @property OptionValue $slider_pause
 * @property OptionValue $slider_speed
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
			// General
			'view_type'      => new OptionValue( OptionValueType::Enum, ViewType::List ),
			'cat_filter'     => new OptionValue( OptionValueType::StringArray ),
			'exclude_cat'    => new OptionValue( OptionValueType::StringArray ),
			'cat_grouping'   => new OptionValue( OptionValueType::Bool, true ),
			'show_cat_name'  => new OptionValue( OptionValueType::Bool, true ),
			'show_num_links' => new OptionValue( OptionValueType::Bool, false ),
			'link_orderby'   => new OptionValue( OptionValueType::Enum, LinkOrderBy::Name ),
			'link_order'     => new OptionValue( OptionValueType::Enum, LinkOrder::Asc ),
			// TODO: num_links should be renamed to link_limit
			'num_links'      => new OptionValue( OptionValueType::Int, -1 ),
			'show_img'       => new OptionValue( OptionValueType::Bool, false ),
			'link_items'     => new OptionValue( OptionValueType::String ),
			'link_item_img'  => new OptionValue( OptionValueType::Enum, LinkItemImg::ShowImgTag ), // TODO: Problems with some value options
			'link_target'    => new OptionValue( OptionValueType::Enum, LinkTarget::Std ),
			// TODO: Check how to handle the link_rel option
			'link_rel'       => new OptionValue( OptionValueType::Enum, LinkRel::NoOpener ), // TODO: Code adoption required
			'custom_class'   => new OptionValue( OptionValueType::String ),
			'class_suffix'   => new OptionValue( OptionValueType::String ),
			'vertical_align' => new OptionValue( OptionValueType::Enum, VerticalAlign::Std ),

			// Link List
			'list_symbol'    => new OptionValue( OptionValueType::Enum, ListSymbol::Std ),
			'cat_columns'    => new OptionValue( OptionValueType::String, '1' ),
			'link_columns'   => new OptionValue( OptionValueType::String, '1' ),

			// Link Slider
			'slider_width'   => new OptionValue( OptionValueType::Int ),
			'slider_height'  => new OptionValue( OptionValueType::Int ),
			'slider_pause'   => new OptionValue( OptionValueType::Int, 6000 ),
			'slider_speed'   => new OptionValue( OptionValueType::Int, 1000 ),
		];
	}


	/**
	 * Set the values of multiple attributes
	 *
	 * @param array<string,mixed> $atts Attributes to set.
	 */
	public function set_values( ?array $atts ): void {
		if ( ! is_array( $atts ) ) {
			return;
		}
		foreach ( $atts as $name => $value ) {
			if ( ! $this->attr_exists( $name ) ) {
				return;
			}
			$this->atts[ $name ]->set_from_str( $value );
		}
	}


	/**
	 * Get the option value class instance of the given attribute
	 */
	public function __get( string $name ): ?OptionValue {
		if ( ! $this->attr_exists( $name ) ) {
			return null;
		}
		return $this->atts[ $name ];
	}


	/**
	 * Set the value of the given attribute
	 */
	public function __set( string $name, mixed $value ): void {
		if ( ! $this->attr_exists( $name ) ) {
			return;
		}
		$this->atts[ $name ]->set( $value );
	}


	/**
	 * Check if the attribute exists
	 */
	private function attr_exists( mixed $name ): bool {
		if ( isset( $this->atts[ $name ] ) ) {
			return true;
		}
		// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_trigger_error -- Trigger a warning is correct here
		trigger_error( 'Shortcode attribute "' . esc_attr( $name ) . '" does not exist!', E_USER_WARNING );
		return false;
	}

}
