<?php
if(!defined('WPINC')) {
	die;
}

require_once(LV_PATH.'includes/options.php');

// This class handles the shortcode [linkview]
class SC_Linkview {
	private static $instance;
	private $options;
	private $atts;
	private $num_ids;
	private $css_printed;
	private $slider_ids;
	private $slider_parameters;

	public static function &get_instance() {
		// singleton setup
		if(!isset(self::$instance)) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	private function __construct() {
		$this->options = &LV_Options::get_instance();

		// Define all available attributes
		$this->atts = array(
			'view_type'      => array('section' => 'general',
			                          'val'     => 'list<br />slider',
			                          'std_val' => 'list',
			                          'desc'    => 'This attribute specifies how the links are displayed. The standard is to show the links in a list.<br />
			                                        The second option is to show the links in a slider. This normally only make sense if you show the images, but it is also possible to show the link name with this option.'),

			'cat_filter'     => array('section' => 'general',
			                          'val'     => 'category slugs',
			                          'std_val' => 'all',
			                          'desc'    => 'This attribute specifies the link categories of which links are displayed. The standard is "all" or an empty string to show all links.<br />
			                                        Links defined in categories which doesn´t match cat_filter will not be displayed.<br />
			                                        The filter is specified via the given category slug. You can specify a single slug to only show links from this category.<br />
			                                        To show multiple categories you can use OR connection with the delimiter "<strong>&verbar;</strong>" or "<strong>&comma;</strong>".<br />
			                                        Examples:<br />
			                                        <code>[linkview cat_filter="social-media"]</code>&hellip; Show all links with category "social-media".<br />
			                                        <code>[linkview cat_filter="blogroll&comma;social-media"]</code>&hellip; Show all links with category "blogroll" or "social-media".'),

			'cat_name'       => array('section' => 'general',
			                          'val'     => 'Cat 1,Cat 2,...',
			                          'std_val' => '',
			                          'desc'    => 'DEPRECATED! Please do not use this attribute anymore, use "cat_filter" instead. This attribute will be removed in a future version!<br />
			                                        This attribute is only considered if the attribute "cat_filter" is not set.<br />
			                                        It specifies which categories should be shown. If you leave the attribute empty all categories are shown.<br />
			                                        If the cat_name has spaces, simply wrap the name in quotes.<br />
			                                        Example: <code>[linkview cat_name="Social Media"]</code><br />
			                                        If you want to define multiple categories you can give them in a list splitted by the delimiter ","<br />
			                                        Example: <code>[linkview cat_name="Blogroll,Social Media"]</code>'),

			'exclude_cat'    => array('section' => 'general',
			                          'val'     => 'Cat 1,Cat 2,...',
			                          'std_val' => '',
			                          'desc'    => 'This attribute specifies which categories should be excluded. This attribute is only considered if the attribute "cat_filter" and "cat_name" is not set.<br />
			                                        If the cat_name has spaces, simply wrap the name in quotes.<br />
			                                        If you want to define multiple categories you can give them in a list splitted by the delimiter ","<br />
			                                        Example: <code>[linkview exclude_cat="Blogroll,Social Media"]</code>'),

			'show_cat_name'  => array('section' => 'general',
			                          'val'     => '0 ... false<br />1 ... true',
			                          'std_val' => '1',
			                          'desc'    => 'This attribute specifies if the category name is shown as a headline.'),

			'link_orderby'   => array('section' => 'general',
			                          'val'     => 'link_id<br />url<br />name<br />owner<br />rating<br />visible<br />length<br />rand',
			                          'std_val' => 'name',
			                          'desc'    => 'This attribute specifies the value to sort the links on for the links in each category.<br />
			                                        The standard is to sort the links according the links name.<br />
			                                        You can also create a random order if you specify <code>rand</code>.<br />
			                                        If you required a more detailed description for the available options visit <a href="http://codex.wordpress.org/Function_Reference/get_bookmarks#Parameters" target="_blank">the wordpress codex</a>.<br />
			                                        You can also specify the order direction with the attribute "link_order".'),

			'link_order'     => array('section' => 'general',
			                          'val'     => 'asc<br />desc',
			                          'std_val' => 'asc',
			                          'desc'    => 'This attribute sets the order direction for the "link_orderby" attribute.<br />
			                                        The available options are ascending (standard) or descending.'),

			'num_links'      => array('section' => 'general',
			                          'val'     => 'number',
			                          'std_val' => '-1',
			                          'desc'    => 'This attribute sets the number of displayed links for each category.<br />
			                                        Specify a number smaller than 0 to view all links.'),

			'show_img'       => array('section' => 'general',
			                          'val'     => '0 ... false<br />1 ... true',
			                          'std_val' => '0',
			                          'desc'    => 'This attribute specifies if the image is displayed instead of the name. This attribute is only considered for links where an image was set.'),

			'link_items'     => array('section' => 'general',
			                          'val'     => 'name<br />address<br />description<br />image<br />rss<br />notes<br />rating',
			                          'std_val' => '',
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

			'vertical_align' => array('section' => 'general',
			                          'val'     => 'std<br />top<br />bottom<br />middle',
			                          'std_val' => 'std',
			                          'desc'    => 'This attribute specifies the vertical alignment of the links. Changing this attribute normally only make sense if the link-images are displayed.<br />
			                                        If you change this value you can for example modify the vertical alignment of the list symbol relativ to the image or the vertical alignment of images with different size in a slider.'),

			'target'         => array('section' => 'general',
			                          'val'     => 'std<br />blank<br />top<br />self',
			                          'std_val' => 'std',
			                          'desc'    => 'Set one of the given values to overwrite the standard value which was set for the link.<br />
			                                        Set the attribute to "std" if you don´t want to overwrite the standard.'),

			'class_suffix'   => array('section' => 'general',
			                          'val'     => 'string',
			                          'std_val' => '',
			                          'desc'    => 'This attribute sets the class suffix to allow different css settings for different link lists or sliders on the same site.<br />
			                                        The standard is an empty string which specifies that no specific suffix will be used.'),

			'list_symbol'    => array('section' => 'list',
			                          'val'     => 'std<br />none<br />circle<br />square<br />disc',
			                          'std_val' => 'std',
			                          'desc'    => 'This attribute sets the style type of the list symbol.<br />
			                                        The standard value is "std", this means the standard type which is set in your theme will be used. Set one of the other values to overwrite this standard.<br />
			                                        A good example for the usage is to set the value to "none" for an image link list. The list symbols will be hidden which often looks better when images are used.'),

			'cat_columns'    => array('section' => 'list',
			                          'val'     => 'Number',
			                          'std_val' => '1',
			                          'desc'    => __('This attribute sets the number of columns for the displayed categories in list view.<br />
			                                           The standard value is "1" to display 1 column only (a simple list).<br />
			                                           If you specify a number greater than 1 the categories will be displayed in multiple columns according to the given value.<br />
			                                           If you have multiple columns it is recommended to define a fixed with for the categories and links. This width must be set manually e.g. via the css entry: <code>.lv-multi-column { width: 32%; }')),

			'link_columns'   => array('section' => 'list',
			                          'val'     => 'Number',
			                          'std_val' => '1',
			                          'desc'    => __('This attribute sets the number of columns for the displayed links per category in list view.<br />
			                                           The standard value is "1" to display 1 column only (a simple list).<br />
			                                           If you specify a number greater than 1 the categories will be displayed in multiple columns according to the given value.
			                                           This feature will not work in Microsoft Internet Explorer < version 10.')),

			'slider_width'   => array('section' => 'slider',
			                          'val'     => 'Number',
			                          'std_val' => '0',
			                          'desc'    => 'This attribute sets the fixed width of the slider. If the attribute is set to 0 the width will be calculated automatically due to the given image sizes.<br />
			                                        This attribute is only considered if the view type "slider" is selected.'),

			'slider_height'  => array('section' => 'slider',
			                          'val'     => 'Number',
			                          'std_val' => '0',
			                          'desc'    => 'This attribute sets the fixed height of the slider. If the attribute is set to 0 the height will be calculated automatically due to the given image sizes.<br />
			                                        This attribute is only considered if the view type "slider" is selected.'),

			'slider_pause'   => array('section' => 'slider',
			                          'val'     => 'Number',
			                          'std_val' => '6000',
			                          'desc'    => 'This attribute sets the duration between the the slides in milliseconds. This is the time where you can see the link standing still before the next slide starts.<br />
			                                        This attribute is only considered if the view type "slider" is selected.'),

			'slider_speed'   => array('section' => 'slider',
			                          'val'     => 'Number',
			                          'std_val' => '1000',
			                          'desc'    => 'This attribute sets the animation speed of the slider in milliseconds. This is the time used to slide from one link to the next one.<br />
			                                        This attribute is only considered if the view type "slider" is selected.'),
		);
		$this->num_ids = 0;
		$this->css_printed = false;
		$this->slider_ids = null;
		$this->slider_parameters = null;
	}

	// main function to show the rendered HTML output
	public function show_html($atts, $content='') {
		// add leading "-" for css-suffix
		if(isset($atts['class_suffix'])) {
			$atts['class_suffix'] = '-'.$atts['class_suffix'];
		}

		// set attribute link_items to $content if an enclosing shortcode was used
		if('' !== $content && null !== $content) {
			// replace quotes html code with real quotes
			$content = str_replace('&#8220;', '"', $content);
			$content = str_replace('&#8221;', '"', $content);
			// set attribute
			$atts['link_items'] = $content;
		}

		// check attributes
		$std_values = array();
		foreach($this->atts as $aname => $attribute) {
			$std_values[$aname] = $attribute['std_val'];
		}
		$a = shortcode_atts($std_values, $atts);

		// set categories
		$categories = $this->categories($a);

		$out = '';
		// print custom css (only once, whe the shortcode is included the first time)
		if(!$this->css_printed) {
			$out .= '
				<style type="text/css">
					.linkview { overflow:auto; }
					.lv-slider ul, .lv-slider li { margin:0; padding:0; list-style-type:none; list-style-image:none; }
					.lv-slider li { overflow:hidden; text-align:center; }
					.lv-slider img { max-width:100%; }
					.lv-row { overflow:auto; }
					.lv-multi-column { float:left; }
					'.$this->options->get('lv_css').'
				</style>';
			$this->css_printed = true;
		}
		// wrapper div
		$out .= '
				<div class="linkview">';
		// prepare for category multi columns
		$cat_multicolumn = (is_int((int)$a['cat_columns']) && 1 < $a['cat_columns']) ? true : false;
		$class_cat_multicolumn = $cat_multicolumn ? ' lv-multi-column' : '';
		$cat_column = 0;
		// go through each category
		foreach($categories as $cat) {
			// cat multicolumn handling
			if($cat_multicolumn) {
				$cat_column++;
				if(1 == $cat_column) {   // first column
					$out .= '
					<div class="lv-row">';
				}
			}

			// set link order
			if('link_id' !== $a['link_orderby'] && 'url' !== $a['link_orderby'] && 'owner' !== $a['link_orderby'] && 'rating' !== $a['link_orderby']
					&& 'visible' !== $a['link_orderby'] && 'length' !== $a['link_orderby'] && 'rand' !== $a['link_orderby']) {
				$a['link_orderby'] = 'name';
			}
			if('desc' !== strtolower($a['link_order'])) {
				$a['link_order'] = 'asc';
			}
			// get links
			$args = array(
				'orderby'        => $a['link_orderby'],
				'order'          => $a['link_order'],
				'limit'          => $a['num_links'],
				'category_name'  => $cat->name);
			$links = get_bookmarks($args);
			// generate output
			if(!empty($links)) {
				$out .='
					<div class="lv-category'.$a['class_suffix'].$class_cat_multicolumn.'" style="overflow:hidden">';
				$out .= $this->html_category($cat, $a);
				$list_id = $this->get_new_list_id();
				$slider_size = array(0, 0);
				if('slider' === $a['view_type']) {
					$this->slider_ids[] = $list_id;
					$slider_size = $this->slider_size($a, $links);
					$out .= $this->html_slider_styles($links, $a, $list_id, $slider_size);
				}
				$out .= $this->html_link_list($links, $a, $list_id, $slider_size);
				$out .= '
					</div>';
			}
			// cat multicolumn handling
			if($cat_multicolumn && $cat_column == $a['cat_columns']) {   // last column
				$cat_column = 0;
				$out .= '
					</div>';
			}
		}
		// wrapper div
		$out .= '
				</div>';
		return $out;
	}

	public function get_atts($section=null) {
		if(null == $section) {
			return $this->atts;
		}
		else {
			$atts = NULL;
			foreach($this->atts as $aname => $attr) {
				if($attr['section'] === $section) {
					$atts[$aname] = $attr;
				}
			}
			return $atts;
		}
	}

	public function get_slider_ids() {
		return $this->slider_ids;
	}

	private function categories($a) {
		$catarray = array();
		if('all' != $a['cat_filter'] || '' == $a['cat_filter']) {
			str_replace(',', '|', $a['cat_filter']);
			$catslugs = array_map('trim', explode('|', $a['cat_filter']));
			foreach($catslugs as $catslug) {
				if(get_term_by('slug', $catslug, 'link_category')) {
					$catarray[] = get_term_by('slug', $catslug, 'link_category');
				}
			}
		}
		elseif(!empty($a['cat_name'])) {   // cat_name is deprecated! Will be removed in one of the next versions.
			$catnames = array_map('trim', explode(",", $a['cat_name']));
			foreach($catnames as $catname) {
				if(get_term_by('name', $catname, 'link_category'))
				{
					$catarray[] = get_term_by('name', $catname, 'link_category');
				}
			}
		}
		else {
			$catarray = get_terms('link_category', 'orderby=name');
			if($a['exclude_cat'] != '') {
				$excludecat = array_map('trim', explode(",", $a['exclude_cat']));
				$diff = Array();
				foreach($catarray as $cat) {
					if(array_search($cat->name, $excludecat) === false) {
						array_push($diff, $cat);
					}
				}
				$catarray = $diff;
				unset($diff);
			}
		}
		return $catarray;
	}

	private function slider_size($a, $links) {
		if(	$a['slider_width'] > 0 && $a['slider_height'] > 0) {
			$width = $a['slider_width'];
			$height = $a['slider_height'];
		}
		else {
			$width = 0;
			$height = 0;
			foreach($links as $link) {
				if($a['show_img'] > 0 && $link->link_image != null) {
					list($w, $h) = getimagesize($link->link_image);
					$width = max($width, $w);
					$height = max($height, $h);
				}
			}
			$ratio = 1;
			if($a['slider_width'] > 0) {
				$ratio = $a['slider_width'] / $width;
			}
			else if($a['slider_height'] > 0) {
				$ratio = $a['slider_height'] / $height;
			}
			$width = round($width * $ratio);
			$height = round($height * $ratio);
			// If no image was in all links, set manual size
			if(!$width)
				$width = 300;
			if(!$height)
				$height = 30;
		}
		return array($width, $height);
	}

	private function html_category($cat, $a) {
		$out = '';
		if($a['show_cat_name'] > 0) {
			$out .= '
					<h2 class="lv-cat-name'.$a['class_suffix'].'">'.$cat->name.'</h2>';
		}
		return $out;
	}

	private function html_link_list($links, $a, $list_id, $slider_size) {
		$num_columns = (is_int((int)$a['link_columns']) && 'slider' !== $a['view_type']) ? $a['link_columns'] : 1;
		$out = '
					<div id="'.$list_id.'" style="column-count:'.$num_columns.'; -moz-column-count:'.$num_columns.'; -webkit-column-count:'.$num_columns.';"';
		if('slider' === $a['view_type']) {
			$out .= ' class="lv-slider"';
		}
		$out .= '>
					<ul class="lv-link-list'.$a['class_suffix'].'"';
		if($a['list_symbol'] == 'none' || $a['list_symbol'] == 'circle' || $a['list_symbol'] == 'square' || $a['list_symbol'] == 'disc') {
			$out .= ' style="list-style-type:'.$a['list_symbol'].';"';
		}
		$out .= '>';
		foreach($links as $link) {
			$out .= '
						<li class="lv-list-item'.$a['class_suffix'].'"><div class="lv-link'.$a['class_suffix'].'"';
			if('slider' !== $a['view_type'] && ('top' === $a['vertical_align'] || 'middle' === $a['vertical_align'] || 'bottom' === $a['vertical_align'])) {
				$out .= ' style="display:inline-block; vertical-align:'.$a['vertical_align'].';"';
			}
			$out .= '>';
			$out .= $this->html_link($link, $a, $slider_size);
			$out .= '</div></li>';
		}
		$out .= '
					</ul>
					</div>';
		return $out;
	}

	private function html_slider_styles($links, $a, $list_id, $slider_size) {
		list($slider_width, $slider_height) = $slider_size;
		// prepare slider parameters which is used in footer script
		$this->slider_parameters[$list_id] = array('auto' => 'true',
		                                           'pause' => $a['slider_pause'],
		                                           'speed' => $a['slider_speed'],
		                                           'continuous' => 'true',
		                                           'controlsShow' => 'false');
		// styles
		$out = '
			<style type="text/css">
				#'.$list_id.' li { '.
					'width:'.$slider_width.'px; '.
					'height:'.$slider_height.'px; }';
		if($a['vertical_align'] == 'top' || $a['vertical_align'] == 'middle' || $a['vertical_align'] == 'bottom') {
			$out .= '
				#'.$list_id.' .lv-link'.$a['class_suffix'].' { '.
					'display:table-cell; '.
					'vertical-align:'.$a['vertical_align'].'; '.
					'width:'.$slider_width.'px; '.
					'height:'.$slider_height.'px; }';
		}
		$out .= '
			</style>';
		return $out;
	}

	private function html_link($l, $a, $slider_size) {
		$out ='';
		if('' === $a['link_items']) {
			// simple style (name or image)
			if($a['show_img'] > 0 && $l->link_image != null) {
				// image
				$out .= $this->html_link_item($l, 'image_l', $a, $slider_size);
			}
			else {
				// name
				$out .= $this->html_link_item($l, 'name_l', $a, $slider_size);
			}
		}
		else {
			// enhanced style (all items given in link_items attribute)
			$items = json_decode($a['link_items'], true);
			if(null !== $items) {
				$out .= $this->html_link_section($l, $items, $a, $slider_size);
			}
			else {
				$out .= 'ERROR while json decoding. There must be an error in your "link_items" json syntax.';
			}
		}
		return $out;
	}

	private function html_link_section($l, $section, $a, $slider_size) {
		$out = '';
		foreach($section as $iname => $item) {
			if(is_array($item)) {
				$out .= '<div class="lv-section-'.$iname.$a['class_suffix'].'">';
				$out .= $this->html_link_section($l, $item, $a, $slider_size);
				$out .= '</div>';
			}
			else {
				$out .= $this->html_link_item($l, $iname, $a, $slider_size, $item);
			}
		}
		return $out;
	}

	private function html_link_item($l, $item, $a, $slider_size, $caption='') {
		$is_link = ('_l' === substr($item, -2));
		if($is_link) {
			$item = substr($item, 0, -2);
		}
		$out = '<div class="lv-item-'.$item.$a['class_suffix'].'">';
		if('' !== $caption) {
			$out .= '<span class="lv-item-caption'.$a['class_suffix'].'">'.$caption.'</span>';
		}
		if($is_link) {
			// a link for this item should be created
			$out .= '<a class="lv-anchor'.$a['class_suffix'].'" href="'.$l->link_url;
			if('blank' === $a['target'] || 'top' === $a['target'] || 'self' === $a['target']) {
				$target = '_'.$a['target'];
			}
			else {
				$target = $l->link_target;
				// set target to _self if an empty string or _none was returned
				if('' === $target || '_none' === $target)
				{
					$target = '_self';
				}
			}
			$out .= '" target="'.$target.'" title="'.$l->link_name;
			if($l->link_description != "") {
				$out .= ' ('.$l->link_description.')';
			}
			$out .= '">';
		}
		switch($item) {
			case 'name':
				$out .= $l->link_name;
				break;
			case 'address':
				$out .= $l->link_url;
				break;
			case 'description':
				$out .= $l->link_description;
				break;
			case 'image':
				$out .= '<img src="'.$l->link_image.'"'.$this->html_img_size($l->link_image, $slider_size[0], $slider_size[1]).' alt="'.$l->link_name.'" />';
				break;
			case 'rss':
				$out .= $l->link_rss;
				break;
			case 'notes':
				$out .= $l->link_notes;
				break;
			case 'rating':
				$out .= $l->link_rating;
				break;
		}
		if($is_link) {
			$out .= '</a>';
		}
		$out .= '</div>';
		return $out;
	}

	private function html_img_size($image, $slider_width=0, $slider_height=0) {
		if($slider_width <= 0 || $slider_height <= 0) {
			return '';
		}
		else {
			$slider_ratio = $slider_width / $slider_height;
			list($img_width, $img_height) = getimagesize($image);
			$img_ratio = $img_width / $img_height;
			if($slider_ratio > $img_ratio) {
				$scale = $slider_height / $img_height;
			}
			else {
				$scale = $slider_width / $img_width;
			}
			return ' width='.round($img_width*$scale).' height='.round($img_height*$scale);
		}
	}

	private function get_new_list_id() {
		$this->num_ids++;
		return 'lv-id-'.$this->num_ids;
	}

	public function print_slider_script() {
		$out = '<script type="text/javascript">
			jQuery(document).ready(function(){';
		foreach($this->slider_ids as $id) {
			$out .= '
				jQuery("#'.$id.'").easySlider({';
			foreach($this->slider_parameters[$id] as $param => $value) {
				$out .= $param.': '.$value.',';
			}
			// remove the comma at the end of the output string
			$out = substr($out, 0, -1);
			$out .= '});';
		}
		$out .= '
			});
		</script>';
		echo $out;
	}
} // end of class SC_Linkview
?>
