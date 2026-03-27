<?php
/**
 * Additional data for the shortcode attributes which are required for the shortcode help page.
 *
 * @package link-view
 *
 * phpcs:disable Generic.Files.OneObjectStructurePerFile.MultipleFound -- value class is in the same file
 */

// cspell:ignore sthis

declare( strict_types=1 );

namespace WordPress\Plugins\mibuthu\LinkView\Shortcode;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly


/**
 * Enum of available shortcode sections
 */
enum Section {
	case General;
	case LinkList;
	case LinkSlider;
}


/**
 * ShortcodeAdminDataValue class
 *
 * This class represents the shortcode admin data for one config value.
 */
final class ConfigAdminDataValue {


	/**
	 * Constructor: Initialize the readonly data
	 */
	public function __construct(
		public readonly Section $section,
		public readonly string $description,
		public readonly string|array|null $permitted_values = null,
	) {}

}


/**
 * LinkView Shortcode Config Attributes Admin Data Class
 *
 * This class provides all additional data for the attributes which is only required in the admin page.
 */
class ConfigAdminData {

	// phpcs:disable Squiz.Commenting.VariableComment.Missing, Squiz.WhiteSpace.MemberVarSpacing.Incorrect -- not required here
	public readonly ConfigAdminDataValue $view_type;
	public readonly ConfigAdminDataValue $cat_filter;
	public readonly ConfigAdminDataValue $exclude_cat;
	public readonly ConfigAdminDataValue $cat_grouping;
	public readonly ConfigAdminDataValue $show_cat_name;
	public readonly ConfigAdminDataValue $show_num_links;
	public readonly ConfigAdminDataValue $link_orderby;
	public readonly ConfigAdminDataValue $link_order;
	public readonly ConfigAdminDataValue $num_links;
	public readonly ConfigAdminDataValue $show_img;
	public readonly ConfigAdminDataValue $link_items;
	public readonly ConfigAdminDataValue $link_item_img;
	public readonly ConfigAdminDataValue $link_target;
	public readonly ConfigAdminDataValue $link_rel;
	public readonly ConfigAdminDataValue $custom_class;
	public readonly ConfigAdminDataValue $class_suffix;
	public readonly ConfigAdminDataValue $vertical_align;
	public readonly ConfigAdminDataValue $list_symbol;
	public readonly ConfigAdminDataValue $cat_columns;
	public readonly ConfigAdminDataValue $link_columns;
	public readonly ConfigAdminDataValue $slider_width;
	public readonly ConfigAdminDataValue $slider_height;
	public readonly ConfigAdminDataValue $slider_pause;
	public readonly ConfigAdminDataValue $slider_speed;
	// phpcs:enable Squiz.Commenting.VariableComment.Missing, Squiz.WhiteSpace.MemberVarSpacing.Incorrect


	/**
	 * Constructor: Initialize the data
	 */
	public function __construct() {
		$this->view_type = new ConfigAdminDataValue(
			section: Section::General,
			description:
				__( 'This attribute specifies how the links are displayed.', 'link-view' ) . '<br />
				' . __( 'Showing the links in a list is the default, alternatively the links can be displayed in a slider.', 'link-view' ),
		);

		$this->cat_filter = new ConfigAdminDataValue(
			section: Section::General,
			description:
				__( 'This attribute specifies the displayed link categories. Default is an empty string to show all categories.', 'link-view' ) . '<br />
				' . __( 'Links with categories that do not match the filter will be hidden.', 'link-view' ) . '<br />
				' . __( 'The filter is specified via the given category slug. The simplest version is a single slug to only show links from this category.', 'link-view' ) . '<br />
				' . sprintf(
					// translators: Placeholders are code tags including the allowed separators
					__( 'To show multiple categories, multiple slugs can be provided separated by %1$s or %2$s.', 'link-view' ),
					'<code>|</code>',
					'<code>,</code>'
				) . '<br />
				' . __( 'Examples', 'link-view' ) . ':<br />
				<code>[linkview cat_filter="social-media"]</code> &hellip; ' . sprintf(
					// translators: Placeholder is: '"social-media"'
					__( 'Show all links with category %1$s.', 'link-view' ),
					'"social-media"'
				) . '<br />
				<code>[linkview cat_filter="blogroll&comma;social-media"]</code> &hellip; ' . sprintf(
					// translators: Placeholder is: '"blogroll"' and '"social-media"'
					__( 'Show all links with category %1$s or %2$s.', 'link-view' ),
					'"blogroll"',
					'"social-media"'
				),
			permitted_values: __( 'category slugs', 'link-view' ),
		);

		$this->exclude_cat = new ConfigAdminDataValue(
			section: Section::General,
			description:
				__( 'This attribute specifies which categories should be excluded.', 'link-view' ) . '<br>
				' . sprintf(
					// translators: Placeholder is a code tag including the attribute name 'cat_filter'
					__( 'This attribute is only considered if the attribute %1$s is not set.', 'link-view' ),
					'<code>cat_filter</code>'
				) . '<br />
				' . __( 'If the category name has spaces, the name must be surrounded by quotes.', 'link-view' ) . '<br />' .
				sprintf(
					// translators: Placeholder is a code tag including the separator
					__( 'To exclude multiple categories, multiple names can be provided separated by %1$s.', 'link-view' ),
					'<code>,</code>'
				) . '<br />
				' . __( 'Example', 'link-view' ) . ': <code>[linkview exclude_cat="Blogroll,Social Media"]</code>',
			permitted_values: __( 'Cat 1,Cat 2,&hellip;', 'link-view' )
		);

		$this->cat_grouping = new ConfigAdminDataValue(
			section: Section::General,
			description:
				__( 'By default the links are grouped by category.', 'link-view' ) . '<br />
				' . __( 'To show all links in one list this option can be disabled.', 'link-view' )
		);

		$this->show_cat_name = new ConfigAdminDataValue(
			section: Section::General,
			description: __( 'This attribute specifies if the category name is shown as a headline when category grouping is enabled.', 'link-view' )
		);

		$this->show_num_links = new ConfigAdminDataValue(
			section: Section::General,
			description:
				__( 'If enabled the number of links is displayed in brackets next to the category name in the headline.', 'link-view' ) . '<br />
				' . sprintf(
					// translators: Placeholders are code tag including the attribute names 'cat_grouping' and 'show_cat_name'
					__( 'The shortcode options %1$s and %2$s must be enabled to display the number.', 'link-view' ),
					'<code>cat_grouping</code>',
					'<code>show_cat_name</code>'
				)
		);

		$this->link_orderby = new ConfigAdminDataValue(
			section: Section::General,
			description:
				__( 'This attribute specifies the sort parameter of the links for each category.', 'link-view' ) . '<br />
				' . __( 'By default the links are sorted according the link name.', 'link-view' ) . '<br />
				' . sprintf(
					// translators: Placeholder is a code tag including the separator
					__( 'A random order can be specify by %1$s.', 'link-view' ),
					'<code>rand</code>'
				) . '<br />
				' . sprintf(
					// translators: Placeholder is a link to the function reference in the WordPress codex
					__( 'A detailed description of all available options is available in the %1$s.', 'link-view' ),
					'<a href="https://codex.wordpress.org/Function_Reference/get_bookmarks#Parameters" target="_blank" rel="noopener">WordPress codex</a>'
				) . '<br />
				' . sprintf(
					// translators: Placeholder is a code tag including the attribute name 'link_order'
					__( 'See also the attribute %1$s to specify the order direction.', 'link-view' ),
					'<code>link_order</code>'
				)
		);

		$this->link_order = new ConfigAdminDataValue(
			section: Section::General,
			description:
				sprintf(
					// translators: Placeholder is a code tag including the attribute name 'link_orderby'
					__( 'This attribute sets the order direction for the %1$s attribute.', 'link-view' ),
					'"link_orderby"'
				) . '<br />
				' . sprintf(
					// translators: Placeholders are a code tags including the attribute values 'ascending' and 'descending'
					__( 'The available options are %1$s (default) and %2$s.', 'link-view' ),
					'<code>ascending</code>',
					'<code>descending</code>'
				)
		);

		$this->num_links = new ConfigAdminDataValue(
			section: Section::General,
			description:
				__( 'This attribute sets the number of displayed links for each category.', 'link-view' ) . '<br />
				' . __( 'A number smaller than 0 displays all links.', 'link-view' ),
			permitted_values: __( 'Number', 'link-view' )
		);

		$this->show_img = new ConfigAdminDataValue(
			section: Section::General,
			description:
				__( 'This attribute specifies if the image shall be displayed instead of the name.', 'link-view' ) . '<br />
				' . __( 'This attribute is only considered for links where an image is set.', 'link-view' )
		);

		$this->link_items = new ConfigAdminDataValue(
			section: Section::General,
			description:
				__( 'With this attribute more complex display options can be defined.', 'link-view' ) . '<br />
				' . sprintf(
					// translators: Placeholder is a code tag including the attribute name 'show_img'
					__( 'By default (empty string) only the link name or the link image (see attribute %1$s) is shown.', 'link-view' ),
					'<code>show_img</code>'
				) . '<br />
				' . __( 'By specifying the below described JSON structure complex display options can be defined.', 'link-view' ) . '<br />
				' . __( 'Please use single quotes for defining this attribute because the double quotes are required to define the JSON code.', 'link-view' ) . '<br />
				' . sprintf(
					// translators: Placeholder is a code tag including an example where the data is the content of the enclosed shortcode
					__( 'This attribute can also be defined as the content of an enclosed shortcode e.g. %1$s.', 'link-view' ),
					'<code>[linkview]' . __( 'JSON data', 'link-view' ) . '[/linkview]</code>'
				) . '<br />
				<p>' . __( 'Examples with all possible options', 'link-view' ) . ':</p>
				<code>{ "name": "", "address": "URL :" }</code><br />
				' . sprintf(
					// translators: Placeholder is a code tag including an example
					__( 'Defining a list of JSON Objects (%1$s pairs) is the simplest version of usage.', 'link-view' ),
					'<code>"key": "value"</code>"'
				)
				. sprintf(
					// translators: Placeholder is: Value options (a separate translation text)
					__( 'The key defines one of the available items (see "%1$s"), the value defines an optional heading for the item.', 'link-view' ),
					__( 'Value options', 'link-view' )
				)
				. sprintf(
					// translators: Placeholder is a code tag including the empty heading example
					__( 'If no heading is required leave the value empty (%1$s).', 'link-view' ),
					'<code>""</code>'
				) . '<br />
				' . sprintf(
					// translators: Placeholder is a code tag including the curly brackets only example
					__( 'The list must be enclosed in curly braces (%1$s) to have valid JSON data. Double quotes must be added around the key and the value.', 'link-view' ),
					'<code>{}</code>'
				) . sprintf(
					// translators: Placeholders are a code tags including the allowed separators
					__( 'The %1$s character separates the key and the value, multiple objects are separated via comma (%2$s).', 'link-view' ),
					'<code>:</code>',
					'<code>,</code>'
				) . '<br />
				<p><code>{ "name": "", "image_l": "", "address_l": "URL :" }</code><br />
				' . sprintf(
					// translators: Placeholder is a code tag including the '_l' text
					__( 'Add a %1$s at the end of the item name to include a link to the link target.', 'link-view' ),
					'<code>_l</code>'
				) . '</p>
				<code>{ "name": "", "left": { "image_l": "", "address_l": "URL :" }, "right": { "description": "Description :", "notes": "Notes: " } }</code><br />
				' . sprintf(
					// translators: Placeholder is a code tag including an example CSS class
					__( 'Multiple items can be grouped by using sub-object. The key of the sub-object defines the name of the group which also will be added as a CSS class (e.g. %1$s).', 'link-view' ),
					'<code>.lvw-section-left</code>'
				),
			permitted_values: [ 'name', 'address', 'description', 'image', 'rss', 'notes', 'rating' ]
		);

		$this->link_item_img = new ConfigAdminDataValue(
			section: Section::General,
			description:
				__( 'With this attribute the display option for link images can be set, if no link image is available.', 'link-view' ) . '<br />
				' . sprintf(
					// translators: Placeholder is a code tag including the attribute name 'link_items'
					__( 'This option is only considered if the %1$s item is used in %2$s.', 'link-view' ),
					'<code>link_image</code>',
					'<code>link_items</code>'
				) . '<br />
				' . sprintf(
					// translators: 1st placeholder: code tag including the attribute name 'show_img_tag', 2nd placeholder: code tag including the '<img>' tag name
					__( 'With %1$s an %2$s tag is still added.', 'link-view' ),
					'<code>show_img_tag</code>',
					'<code>&lt;img&gt;</code>'
				) . ' '
				. sprintf(
					// translators: Placeholder is a code tag including the HTML image attribute 'alt'
					__( 'Due to the empty link address of the image the %1$s attribute will be displayed.', 'link-view' ),
					'<code>alt</code>'
				) . '<br />
				' . sprintf(
					// translators: Placeholder is a code tag including the attribute value 'show_nothing'
					__( 'With %1$s the complete link item will be removed.', 'link-view' ),
					'<code>show_nothing</code>'
				) . '<br />
				' . sprintf(
					// translators: Placeholder is a code tag including the '<img>' tag name
					__( 'With the other options only the %1$s tag will be removed and an alternative text (link name or description) will be displayed.', 'link-view' ),
					'<code>&lt;img&gt;</code>'
				)
		);

		$this->link_target = new ConfigAdminDataValue(
			section: Section::General,
			description: __( 'Set one of the available options to override the default value defined for the link.', 'link-view' ),
		);

		$this->link_rel = new ConfigAdminDataValue(
			section: Section::General,
			description:
				sprintf(
					// translators: Placeholder is a code tag including the 'rel' HTML attribute
					__( 'With this attribute the %1$s attribute for the HTML-links can be set', 'link-view' ),
					'<code>rel</code>'
				) .
				' (' . sprintf(
					// translators: Placeholder is a link to the rel page on w3schools.com
					__( 'see %1$s for details', 'link-view' ),
					'<a href="https://www.w3schools.com/tags/att_a_rel.asp" target="_blank" rel="noopener">' . __( 'this link', 'link-view' ) . '</a>'
				) . ').<br />
				' . sprintf(
					// translators: Placeholder is a code tag including the separator
					__( 'Use a comma (%1$s) to separate multiple rel attributes.', 'link-view' ),
					'<code>,</code>'
				) . '<br />
				' . __( 'These rel attributes will be combined with the rel attributes set for each link.', 'link-view' ),
		);

		$this->custom_class = new ConfigAdminDataValue(
			section: Section::General,
			description:
				__( 'With this attribute additional CSS classes can be specified. The classes are added to the link-view wrapper div.', 'link-view' ) . '<br />
				' . sprintf(
					// translators: Placeholder is a code tag including the separator
					__( 'Use a comma (%1$s) to separate multiple classes.', 'link-view' ),
					'<code>,</code>'
				),
			permitted_values: __( 'String', 'link-view' )
		);

		$this->class_suffix = new ConfigAdminDataValue(
			section: Section::General,
			description: __( 'With this attribute a css class suffix can be specified. This allows using different css styles for different link lists or sliders on the same site.', 'link-view' ),
			permitted_values: __( 'String', 'link-view' )
		);

		$this->vertical_align = new ConfigAdminDataValue(
			section: Section::General,
			description:
				__( 'This attribute specifies the vertical alignment of the links. Changing this attribute normally only make sense if the link-images are displayed.', 'link-view' ) . '<br />
				' . __( 'With this option e.g. the vertical alignment of the list symbol relatively to the image or the vertical alignment of images with different height in a slider can be changed.', 'link-view' )
		);

		$this->list_symbol = new ConfigAdminDataValue(
			section: Section::LinkList,
			description:
				__( 'This attribute sets the style type of the list symbol.', 'link-view' ) . '<br />
				' . sprintf(
					// translators: Placeholder is a code tag including the value 'std'
					__( 'With the default value %1$s the standard type which is set in your theme will be used.', 'link-view' ),
					'<code>std</code>'
				) .
				__( 'All other available options override this standard.', 'link-view' ) . '<br />
				' . sprintf(
					// translators: Placeholder is a code tag including the value 'none'
					__( 'For example setting the value to %1$s will hide the list symbols.', 'link-view' ),
					'<code>none</code>'
				),
		);

		$this->cat_columns = new ConfigAdminDataValue(
			section: Section::LinkList,
			description:
				__( 'This attribute specifies column layout for the categories in list view.', 'link-view' ) . '<br />
				' . __( 'There are 3 different types of multiple column layouts available.', 'link-view' ) .
				sprintf(
					// translators: Placeholder is an internal link to the multi-column section
					__( 'Find more information regarding the types and options in the chapter %1$s.', 'link-view' ),
					'<a href="#multicol">' . __( 'Multi-column layout types and options', 'link-view' ) . '</a>.'
				),
			permitted_values: [ 'Number', 'static', 'css', 'masonry' ]
		);

		$this->link_columns = new ConfigAdminDataValue(
			section: Section::LinkList,
			description:
				__( 'This attribute specifies column layout for the links in list view.', 'link-view' ) . '<br />
				' . __( 'There are 3 different types of multiple column layouts available.', 'link-view' ) .
				sprintf(
					// translators: Placeholder is an internal link to the multi-column section
					__( 'Find more information regarding the types and options in the chapter %1$s.', 'link-view' ),
					'<a href="#multicol">' . __( 'Multi-column layout types and options', 'link-view' ) . '</a>.'
				),
			permitted_values: [ 'Number', 'static', 'css', 'masonry' ]
		);

		$this->slider_width = new ConfigAdminDataValue(
			section: Section::LinkSlider,
			description:
				__( 'This attribute sets the fix width of the slider.', 'link-view' ) .
				sprintf(
					// translators: Placeholder is a code tag including the value '0'
					__( 'If the attribute is set to %1$s the width will be calculated automatically due to the given image sizes.', 'link-view' ),
					'<code>0</code>'
				) . '<br />
				' . sprintf(
					// translators: Placeholder is a code tag including the value 'slider'
					__( 'This attribute is only considered if the view type %1$s is selected.', 'link-view' ),
					'<code>slider</code>'
				),
			permitted_values: 'Number'
		);

		$this->slider_height = new ConfigAdminDataValue(
			section: Section::LinkSlider,
			description:
				__( 'This attribute sets the fix height of the slider.', 'link-view' ) .
				sprintf(
					// translators: Placeholder is a code tag including the value '0'
					__( 'If the attribute is set to %1$s the height will be calculated automatically due to the given image sizes.', 'link-view' ),
					'<code>0</code>'
				) . '<br />
				' . sprintf(
					// translators: Placeholder is a code tag including the value 'slider'
					__( 'This attribute is only considered if the view type %1$s is selected.', 'link-view' ),
					'<code>slider</code>'
				),
			permitted_values: 'Number'
		);

		$this->slider_pause = new ConfigAdminDataValue(
			section: Section::LinkSlider,
			description:
				__( 'This attribute sets the duration between the the slides in milliseconds.', 'link-view' ) . ' ' .
				__( 'The link stands still for this time and afterwards the sliding animation to the next link starts.', 'link-view' ) . '<br />
				' . sprintf(
					// translators: Placeholder is a code tag including the value 'slider'
					__( 'This attribute is only considered if the view type %1$s is selected.', 'link-view' ),
					'<code>slider</code>'
				),
			permitted_values: 'Number'
		);

		$this->slider_speed = new ConfigAdminDataValue(
			section: Section::LinkSlider,
			description:
				__( 'This attribute sets the duration of the animation for switching from one link to the next in milliseconds.', 'link-view' ) . '<br />
				' . sprintf(
					// translators: Placeholder is a code tag including the value 'slider'
					__( 'This attribute is only considered if the view type %1$s is selected.', 'link-view' ),
					'<code>slider</code>'
				),
			permitted_values: 'Number'
		);
	}


	/**
	 * Get all shortcode options
	 */
	public function get_all( ?Section $section ): array {
		$result     = [];
		$reflection = new \ReflectionClass( $this );
		$properties = $reflection->getProperties( \ReflectionProperty::IS_PUBLIC );
		foreach ( $properties as $property ) {
			$name = $property->getName();
			if ( $this->$name instanceof ConfigAdmiNDataValue ) {
				if ( is_null( $section ) || $section === $this->$name->section ) {
					$result[ $name ] = $this->$name;
				}
			}
		}
		return $result;
	}

}
