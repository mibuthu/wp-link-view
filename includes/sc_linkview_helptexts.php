<?php
if(!defined('WPINC')) {
	exit;
}

$sc_linkview_helptexts = array(
	'view_type'      => array('section' => 'general',
	                          'val'     => array('list','slider'),
	                          'desc'    => 'This attribute specifies how the links are displayed. The standard is to show the links in a list.<br />
	                                        The second option is to show the links in a slider. This normally only make sense if you show the images, but it is also possible to show the link name with this option.'),

	'cat_filter'     => array('section' => 'general',
	                          'val'     => 'category slugs',
	                          'desc'    => 'This attribute specifies the link categories of which links are displayed. The standard is "all" or an empty string to show all links.<br />
	                                        Links defined in categories which doesn´t match cat_filter will not be displayed.<br />
	                                        The filter is specified via the given category slug. You can specify a single slug to only show links from this category.<br />
	                                        To show multiple categories you can use OR connection with the delimiter "<strong>&verbar;</strong>" or "<strong>&comma;</strong>".<br />
	                                        Examples:<br />
	                                        <code>[linkview cat_filter="social-media"]</code>&hellip; Show all links with category "social-media".<br />
	                                        <code>[linkview cat_filter="blogroll&comma;social-media"]</code>&hellip; Show all links with category "blogroll" or "social-media".'),

	'exclude_cat'    => array('section' => 'general',
	                          'val'     => 'Cat 1,Cat 2,...',
	                          'desc'    => 'This attribute specifies which categories should be excluded. This attribute is only considered if the attribute "cat_filter" is not set.<br />
	                                        If the category name has spaces, simply wrap the name in quotes.<br />
	                                        If you want to define multiple categories you can give them in a list splitted by the delimiter ","<br />
	                                        Example: <code>[linkview exclude_cat="Blogroll,Social Media"]</code>'),

	'show_cat_name'  => array('section' => 'general',
	                          'val'     => array('0 ... false','1 ... true'),
	                          'desc'    => 'This attribute specifies if the category name is shown as a headline.'),

	'link_orderby'   => array('section' => 'general',
	                          'val'     => array('link_id','url','name','owner','rating','visible','length','rand'),
	                          'std_val' => 'name',
	                          'desc'    => 'This attribute specifies the value to sort the links on for the links in each category.<br />
	                                        The standard is to sort the links according the links name.<br />
	                                        You can also create a random order if you specify <code>rand</code>.<br />
	                                        If you required a more detailed description for the available options visit <a href="http://codex.wordpress.org/Function_Reference/get_bookmarks#Parameters" target="_blank" rel="noopener">the wordpress codex</a>.<br />
	                                        You can also specify the order direction with the attribute "link_order".'),

	'link_order'     => array('section' => 'general',
	                          'val'     => array('asc','desc'),
	                          'desc'    => 'This attribute sets the order direction for the "link_orderby" attribute.<br />
	                                        The available options are ascending (standard) or descending.'),

	'num_links'      => array('section' => 'general',
	                          'val'     => 'Number',
	                          'desc'    => 'This attribute sets the number of displayed links for each category.<br />
	                                        Specify a number smaller than 0 to view all links.'),

	'show_img'       => array('section' => 'general',
	                          'val'     => array('0 ... false','1 ... true'),
	                          'desc'    => 'This attribute specifies if the image is displayed instead of the name. This attribute is only considered for links where an image was set.'),

	'link_items'     => array('section' => 'general',
	                          'val'     => array('name','address','description','image','rss','notes','rating'),
	                          'desc'    => 'This is a more complex but very powerful attribute.
	                                        The standard is to leave it emtpy. Then only the link name or the link image (see attribute "show_img") is shown.<br />
	                                        If you use this attribute you can overwrite these settings and you can customize the displayed link items and their arrangement to your special requirements.<br />
	                                        This attribute is set via a specific JSON data structure.<br />
	                                        Please use single quotes for defining this attribute because you require the double quotes to define the JSON code.<br />
	                                        This attribute can also be defined as the content of an enclosed shortcode e.g. <code>[linkview]JSON data[/linkview]</code>.<br />
	                                        <p>Below you can find some examples with all possible options:</p>
	                                        <code>{ "name": "", "address": "URL :" }</code><br />
	                                        Defining a list of JSON Objects ("key": "value" pairs) is the simplest version of usage. The key defines one of the available items (see Value options),
	                                        the value defines an optional heading for the item. If no heading is required leave the value empty ("").<br />
	                                        To have valid JSON data the list must be enclosed in curly braces {}. Double quotes must be added around the key and the value.
	                                        The ":" character separats the key and the value, multiple objects are separated via comma.<br />
	                                        <p><code>{ "name": "", "image_l": "", "address_l": "URL :" }</code><br />
	                                        If you want to create an anchor (link) onto the item you have to add a "_l" at the end of the item name.</p>
	                                        <code>{ "name": "", "left": { image_l": "", "address_l": "URL :" }, "right": { "description": "Description :", "notes": "Notes: " } }</code><br />
	                                        You can group multiple items by using sub-object. The key of the sub-object defines the name of the group which also will be added as a css-class (e.g. .lv-section-left).'),

	'link_item_img'  => array('section' => 'general',
	                          'val'     => array('show_img_tag','show_link_name','show_link_description','show_nothing'),
	                          'desc'    => 'With this attribute the display option for link images can be set, if no link image for a specific link is available.<br />
	                                        This option is only considered if the "link_image" item is used in "link_items".<br />
	                                        With "show_img_tag" an <code>&lt;img&gt;</code> tag is still added. Due to the empty link address the image name of the alt attribute is displayed then.<br />
	                                        With "nothing" the complete link item will be removed.<br />
	                                        With the other options only the <code>&lt;img&gt;</code> tag will be removed and an alternative text (link name or description) will be displayed.'),

	'link_target'    => array('section' => 'general',
	                          'val'     => array('std','blank','top','self'),
	                          'desc'    => 'Set one of the given values to overwrite the standard value which was set for the link.<br />
	                                        Set the attribute to "std" if you don´t want to overwrite the standard.'),

	'link_rel'       => array('section' => 'general',
	                       // 'val'     => val is already set in sc_linkview, because the array is required for checking for a valid rel attribute
	                          'desc'    => 'With this attribute you can set the "rel" attribute for the HTML-links (see <a href="http://www.w3schools.com/tags/att_a_rel.asp" target="_blank" rel="noopener">this link</a> for details).'),

	'class_suffix'   => array('section' => 'general',
	                          'val'     => 'string',
	                          'desc'    => 'This attribute sets the class suffix to allow different css settings for different link lists or sliders on the same site.<br />
	                                        The standard is an empty string which specifies that no specific suffix will be used.'),

	'list_symbol'    => array('section' => 'list',
	                          'val'     => array('std','none','circle','square','disc'),
	                          'desc'    => 'This attribute sets the style type of the list symbol.<br />
	                                        The standard value is "std", this means the standard type which is set in your theme will be used. Set one of the other values to overwrite this standard.<br />
	                                        A good example for the usage is to set the value to "none" for an image link list. The list symbols will be hidden which often looks better when images are used.'),

	'vertical_align' => array('section' => 'general',
	                          'val'     => array('std','top','bottom','middle'),
	                          'desc'    => 'This attribute specifies the vertical alignment of the links. Changing this attribute normally only make sense if the link-images are displayed.<br />
	                                        If you change this value you can for example modify the vertical alignment of the list symbol relativ to the image or the vertical alignment of images with different size in a slider.'),

	'cat_columns'    => array('section' => 'list',
	                          'val'     => array('Number','static','css','masonry'),
	                          'desc'    => 'This attribute specifies if and how the categories shall be displayed in multiple columns in list view.<br />
	                                        There are 3 different types of multiple column layouts available. Find more information regarding the types and options in the chapter <a href="#multicol">Multi-column layout types and options</a>.'),

	'link_columns'   => array('section' => 'list',
	                          'val'     => array('Number','static','css','masonry'),
	                          'desc'    => 'This attribute specifies if and how the links shall be displayed in multiple columns in list view.<br />
	                                        There are 3 different types of multiple column layouts available. Find more information regarding the types and options in the chapter <a href="#multicol">Multi-column layout types and options</a>.'),

	'slider_width'   => array('section' => 'slider',
	                          'val'     => 'Number',
	                          'desc'    => 'This attribute sets the fixed width of the slider. If the attribute is set to 0 the width will be calculated automatically due to the given image sizes.<br />
	                                        This attribute is only considered if the view type "slider" is selected.'),

	'slider_height'  => array('section' => 'slider',
	                          'val'     => 'Number',
	                          'desc'    => 'This attribute sets the fixed height of the slider. If the attribute is set to 0 the height will be calculated automatically due to the given image sizes.<br />
	                                        This attribute is only considered if the view type "slider" is selected.'),

	'slider_pause'   => array('section' => 'slider',
	                          'val'     => 'Number',
	                          'desc'    => 'This attribute sets the duration between the the slides in milliseconds. This is the time where you can see the link standing still before the next slide starts.<br />
	                                        This attribute is only considered if the view type "slider" is selected.'),

	'slider_speed'   => array('section' => 'slider',
	                          'val'     => 'Number',
	                          'desc'    => 'This attribute sets the animation speed of the slider in milliseconds. This is the time used to slide from one link to the next one.<br />
	                                        This attribute is only considered if the view type "slider" is selected.'),
);
?>
