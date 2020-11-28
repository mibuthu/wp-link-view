<?php
/**
 * Additional data for the shortcode attributes which are required for the shortcode help page.
 *
 * @package link-view
 */

// declare( strict_types=1 ); Remove for now due to warnings in php <7.0!

namespace WordPress\Plugins\mibuthu\LinkView\Shortcode;

if ( ! defined( 'WPINC' ) ) {
	exit();
}

require_once PLUGIN_PATH . 'includes/option.php';


/**
 * LinkView Shortcode Config Attributes Admin Data Class
 *
 * This class provides all additional data for the attributes which is only required in the admin page.
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
class ConfigAdminData {

	/**
	 * Additional data for the attributes
	 *
	 * @var array<string,array<string,string|array>>
	 */
	private $atts_data;


	/**
	 * Constructor: Initialize the data
	 */
	public function __construct() {
		$this->atts_data = [
			'view_type'      => [
				'section'     => 'general',
				'description' => __( 'This attribute specifies how the links are displayed.', 'link-view' ) . '<br />
			' . __( 'Showing the links in a list is the default, alternatively the links can be displayed in a slider.', 'link-view' ),
			],

			'cat_filter'     => [
				'section'          => 'general',
				'permitted_values' => __( 'category slugs', 'link-view' ),
				'description'      => __( 'This attribute specifies the displayed link categories. Default is an empty string to show all categories.', 'link-view' ) . '<br />
			' . __( 'Links with categories that doesn´t match the filter will not be displayed.', 'link-view' ) . '<br />
			' . __( 'The filter is specified via the given category slug. The simplest version is a single slug to only show links from this category.', 'link-view' ) . '<br />
			' . sprintf( __( 'To show multiple categories, multiple slugs can be provided seperated by %1$s or %2$s.', 'link-view' ), '<code>|</code>', '<code>,</code>' ) . '<br />
			' . __( 'Examples', 'link-view' ) . ':<br />
			<code>[linkview cat_filter="social-media"]</code> &hellip; ' . sprintf( __( 'Show all links with category %1$s.', 'link-view' ), '"social-media"' ) . '<br />
			<code>[linkview cat_filter="blogroll&comma;social-media"]</code> &hellip; ' . sprintf( __( 'Show all links with category %1$s or %2$s.', 'link-view' ), '"blogroll"', '"social-media"' ),
			],

			'exclude_cat'    => [
				'section'          => 'general',
				'permitted_values' => 'Cat 1,Cat 2,&hellip;',
				'description'      => __( 'This attribute specifies which categories should be excluded.', 'link-view' )
					. sprintf( __( 'This attribute is only considered if the attribute %1$s is not set.', 'link-view' ), '<code>cat_filter</code>' ) . '<br />
			' . __( 'If the category name has spaces, the name must be surrounded by quotes.', 'link-view' ) . '<br />
			' . sprintf( __( 'To exclude multiple categories, multiple names can be provided seperated by %1$s.', 'link-view' ), '<code>,</code>' ) . '<br />
			' . __( 'Example', 'link-view' ) . ': <code>[linkview exclude_cat="Blogroll,Social Media"]</code>',
			],

			'show_cat_name'  => [
				'section'          => 'general',
				'permitted_values' => [ '0 &hellip; false', '1 &hellip; true' ],
				'description'      => __( 'This attribute specifies if the category name is shown as a headline.', 'link-view' ),
			],

			'show_num_links' => [
				'section'          => 'general',
				'permitted_values' => [ '0 &hellip; false', '1 &hellip; true' ],
				'description'      => __( 'This attribute specifies if the number of links shall be displayed in brackets next to the category name in the headline.', 'link-view' ) . '<br />
			' . sprintf( __( 'The headline with the category name must be displayed (%1$s) to show the number of links.', 'link-view' ), '<code>show_cat_name=true</code>' ),
			],

			'link_orderby'   => [
				'section'     => 'general',
				'description' => __( 'This attribute specifies the sort parameter of the links for each category.', 'link-view' ) . '<br />
			' . __( 'By default the links are sorted according the link name.', 'link-view' ) . '<br />
			' . sprintf( __( 'A random order can be specify by %1$s.', 'link-view' ), '<code>rand</code>' ) . '<br />
			' . sprintf(
				__( 'A detailed description of all available options is available in the %1$sWordPress codex%2$s.', 'link-view' ),
				'<a href="https://codex.wordpress.org/Function_Reference/get_bookmarks#Parameters" target="_blank" rel="noopener">',
				'</a>'
			) . '<br />
			' . sprintf( __( 'See also the attribute %1$s to specify the order direction.', 'link-view' ), '<code>link_order</code>' ),
			],

			'link_order'     => [
				'section'     => 'general',
				'description' => sprintf( __( 'This attribute sets the order direction for the %1$s attribute.', 'link-view' ), '"link_orderby"' ) . '<br />
			' . sprintf( __( 'The available options are %1$s (default) and %2$s.', 'link-view' ), '<code>ascending</code>', '<code>descending</code>' ),
			],

			'num_links'      => [
				'section'          => 'general',
				'permitted_values' => __( 'Number', 'link-view' ),
				'description'      => __( 'This attribute sets the number of displayed links for each category.', 'link-view' ) . '<br />
			' . __( 'A number smaller than 0 displays all links.', 'link-view' ),
			],

			'show_img'       => [
				'section'          => 'general',
				'permitted_values' => [ '0 &hellip; false', '1 &hellip; true' ],
				'description'      => __( 'This attribute specifies if the image shall be displayed instead of the name.', 'link-view' ) .
				__( 'This attribute is only considered for links where an image is set.', 'link-view' ),
			],

			'link_items'     => [
				'section'          => 'general',
				'permitted_values' => [ 'name', 'address', 'description', 'image', 'rss', 'notes', 'rating' ],
				'description'      => __( 'With this attribute more complex display options can be defined.', 'link-view' ) . '<br />
			' . sprintf( __( 'By default (empty string) only the link name or the link image (see attribute %1$s) is shown.', 'link-view' ), '<code>show_img</code>' ) . '<br />
			' . __( 'By specifying the below described JSON structure complex display options can be defined.', 'link-view' ) . '<br />
			' . __( 'Please use single quotes for defining this attribute because the double quotes are required to define the JSON code.', 'link-view' ) . '<br />
			' . sprintf(
				__( 'This attribute can also be defined as the content of an enclosed shortcode e.g. %1$s.', 'link-view' ),
				'<code>[linkview]' . __( 'JSON data', 'link-view' ) . '[/linkview]</code>'
			) . '<br />
			<p>' . __( 'Examples with all possible options', 'link-view' ) . ':</p>
			<code>{ "name": "", "address": "URL :" }</code><br />
			' . sprintf( __( 'Defining a list of JSON Objects (%1$s pairs) is the simplest version of usage.', 'link-view' ), '<code>"key": "value"</code>"' )
					. sprintf( __( 'The key defines one of the available items (see "%1$s"), the value defines an optional heading for the item.', 'link-view' ), __( 'Value options', 'link-view' ) )
					. sprintf( __( 'If no heading is required leave the value empty (%1$s).', 'link-view' ), '<code>""</code>' ) . '<br />
			' . sprintf( __( 'The list must be enclosed in curly braces (%1$s) to have valid JSON data. Double quotes must be added around the key and the value.', 'link-view' ), '<code>{}</code>' )
					. sprintf( __( 'The %1$s character separats the key and the value, multiple objects are separated via comma (%2$s).', 'link-view' ), '<code>:</code>', '<code>,</code>' ) . '<br />
			<p><code>{ "name": "", "image_l": "", "address_l": "URL :" }</code><br />
			' . sprintf( __( 'Add a %1$s at the end of the item name to include a link to the link target.', 'link-view' ), '<code>_l</code>' ) . '</p>
			<code>{ "name": "", "left": { image_l": "", "address_l": "URL :" }, "right": { "description": "Description :", "notes": "Notes: " } }</code><br />
			' . sprintf(
				__( 'Multiple items can be grouped by using sub-object. The key of the sub-object defines the name of the group which also will be added as a css-class (e.g. %1$s).', 'link-view' ),
				'<code>.lvw-section-left</code>'
			),
			],

			'link_item_img'  => [
				'section'     => 'general',
				'description' => __( 'With this attribute the display option for link images can be set, if no link image is available.', 'link-view' ) . '<br />
			' . sprintf( __( 'This option is only considered if the %1$s item is used in %2$s.', 'link-view' ), '<coee>link_image</code>', '<code>link_items</code>' ) . '<br />
			' . sprintf( __( 'With %1$s an %2$s tag is still added.', 'link-view' ), '<code>show_img_tag</code>', '<code>&lt;img&gt;</code>' ) . ' '
					. sprintf( __( 'Due to the empty link address of the image the %1$s attribute will be displayed.', 'link-view' ), '<code>alt</code>' ) . '<br />
			' . sprintf( __( 'With %1$s the complete link item will be removed.', 'link-view' ), '<code>show_nothing</code>' ) . '<br />
			' . sprintf( __( 'With the other options only the %1$s tag will be removed and an alternative text (link name or description) will be displayed.', 'link-view' ), '<code>&lt;img&gt;</code>' ),
			],

			'link_target'    => [
				'section'     => 'general',
				'description' => __( 'Set one of the available options to override the default value defined for the link.', 'link-view' ),
			],

			'link_rel'       => [
				'section'     => 'general',
				'description' => sprintf( __( 'With this attribute the %1$s attribute for the HTML-links can be set.', 'link-view' ), '<code>rel</code>' )
					. ' (' . sprintf( __( 'see %1$sthis link%2$s for details', 'link-view' ), '<a href="https://www.w3schools.com/tags/att_a_rel.asp" target="_blank" rel="noopener">', '</a> for details).' ) . ').',
			],

			'class_suffix'   => [
				'section'          => 'general',
				'permitted_values' => __( 'String', 'link-view' ),
				'description'      => __( 'With this attribute a css class suffix can be specified. This allows using different css styles for different link lists or sliders on the same site.', 'link-view' ),
			],

			'vertical_align' => [
				'section'     => 'general',
				'description' => __( 'This attribute specifies the vertical alignment of the links. Changing this attribute normally only make sense if the link-images are displayed.', 'link-view' ) . '<br />
			' . __( 'With this option e.g. the vertical alignment of the list symbol relativ to the image or the vertical alignment of images with different height in a slider can be changed.', 'link-view' ),
			],

			'list_symbol'    => [
				'section'     => 'list',
				'description' => __( 'This attribute sets the style type of the list symbol.', 'link-view' ) . '<br />
			' . sprintf( __( 'With the default value %1$s the standard type which is set in your theme will be used.', 'link-view' ), '<code>std</code>' )
					. __( 'All other available options overide this standard.', 'link-view' ) . '<br />
			' . sprintf( __( 'For example setting the value to %1$s will hide the list symbols.', 'link-view' ), '<code>none</code>' ),
			],

			'cat_columns'    => [
				'section'          => 'list',
				'permitted_values' => [ 'Number', 'static', 'css', 'masonry' ],
				'description'      => __( 'This attribute specifies column layout for the categories in list view.', 'link-view' ) . '<br />
			' . __( 'There are 3 different types of multiple column layouts available.', 'link-view' )
					. sprintf(
						__( 'Find more information regarding the types and options in the chapter %1$s.', 'link-view' ),
						'<a href="#multicol">' . __( 'Multi-column layout types and options', 'link-view' ) . '</a>.'
					),
			],

			'link_columns'   => [
				'section'          => 'list',
				'permitted_values' => [ 'Number', 'static', 'css', 'masonry' ],
				'description'      => __( 'This attribute specifies column layout for the links in list view.', 'link-view' ) . '<br />
			' . __( 'There are 3 different types of multiple column layouts available.', 'link-view' )
					. sprintf(
						__( 'Find more information regarding the types and options in the chapter %1$s.', 'link-view' ),
						'<a href="#multicol">' . __( 'Multi-column layout types and options', 'link-view' ) . '</a>.'
					),
			],

			'slider_width'   => [
				'section'          => 'slider',
				'permitted_values' => 'Number',
				'description'      => __( 'This attribute sets the fix width of the slider.', 'link-view' )
					. sprintf( __( 'If the attribute is set to %1$s the width will be calculated automatically due to the given image sizes.', 'link-view' ), '<code>0</code>' ) . '<br />
			' . sprintf( __( 'This attribute is only considered if the view type %1$s is selected.', 'link-view' ), '<code>slider</code>' ),
			],

			'slider_height'  => [
				'section'          => 'slider',
				'permitted_values' => 'Number',
				'description'      => __( 'This attribute sets the fix height of the slider.', 'link-view' )
					. sprintf( __( 'If the attribute is set to %1$s the height will be calculated automatically due to the given image sizes.', 'link-view' ), '<code>0</code>' ) . '<br />
			' . sprintf( __( 'This attribute is only considered if the view type %1$s is selected.', 'link-view' ), '<code>slider</code>' ),
			],

			'slider_pause'   => [
				'section'          => 'slider',
				'permitted_values' => 'Number',
				'description'      => __( 'This attribute sets the duration between the the slides in milliseconds.', 'link-view' )
					. __( 'The link stands still for this time and afterwards the sliding animation to the next link starts.', 'link-view' ) . '<br />
			' . sprintf( __( 'This attribute is only considered if the view type %1$s is selected.', 'link-view' ), '<code>slider</code>' ),
			],

			'slider_speed'   => [
				'section'          => 'slider',
				'permitted_values' => 'Number',
				'description'      => __( 'This attribute sets the duration of the animation for switching from one link to the next in milliseconds.', 'link-view' ) . '<br />
			' . sprintf( __( 'This attribute is only considered if the view type %1$s is selected.', 'link-view' ), '<code>slider</code>' ),
			],
		];
	}


	/**
	 * Get the data for a given attribute
	 *
	 * @param string $attribute_name The name of the option.
	 * @return array<string,string|array>
	 */
	public function __get( $attribute_name ) {
		if ( isset( $this->atts_data[ $attribute_name ] ) ) {
			return $this->atts_data[ $attribute_name ];
		}
	}

}
