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
	private $sc_ids;
	private $css_printed;
	private $css_multicol_printed;
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
			'view_type'      => array('std_val' => 'list'),
			'cat_filter'     => array('std_val' => 'all'),
			'exclude_cat'    => array('std_val' => ''),
			'show_cat_name'  => array('std_val' => '1'),
			'link_orderby'   => array('std_val' => 'name'),
			'link_order'     => array('std_val' => 'asc'),
			'num_links'      => array('std_val' => '-1'),
			'show_img'       => array('std_val' => '0'),
			'link_items'     => array('std_val' => ''),
			'link_item_img'  => array('std_val' => 'show_img_tag'),
			'link_target'    => array('std_val' => 'std'),
			'link_rel'       => array('std_val' => 'noopener', 'val' => array('alternate','author','bookmark','external','help','license','next','nofollow','noreferrer','noopener','prev','search','tag')),
			'class_suffix'   => array('std_val' => ''),
			'list_symbol'    => array('std_val' => 'std'),
			'vertical_align' => array('std_val' => 'std'),
			'cat_columns'    => array('std_val' => '1'),
			'link_columns'   => array('std_val' => '1'),
			'slider_width'   => array('std_val' => '0'),
			'slider_height'  => array('std_val' => '0'),
			'slider_pause'   => array('std_val' => '6000'),
			'slider_speed'   => array('std_val' => '1000'),
		);
		$this->num_ids = 0;
		$this->sc_ids = 0;
		$this->css_printed = false;
		$this->css_multicol_printed = false;
		$this->slider_ids = null;
		$this->slider_parameters = null;
	}


	public function load_sc_linkview_helptexts() {
		require_once(LV_PATH.'includes/sc_linkview_helptexts.php');
		foreach($sc_linkview_helptexts as $name => $values) {
			$this->atts[$name] = array_merge($this->atts[$name], $values);
		}
		unset($sc_linkview_helptexts);
	}

	// main function to show the rendered HTML output
	public function show_html($atts, $content='') {
		$this->sc_ids ++;
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

		// prepare for category multi columns
		$cat_multicol = $this->get_multicol_settings($a['cat_columns']);
		$cat_classes = $this->get_multicol_classes($cat_multicol, 'lv-category'.$a['class_suffix']);
		$cat_wrapper_styles = $this->get_multicol_wrapper_styles($cat_multicol);
		$cat_col = 0;
		// print styles and scripts for multi-column support
		$out .= $this->print_mansonry_script($cat_multicol, '.linkview#lv-sc-id-'.$this->sc_ids, '.lv-category-column');
		$out .= $this->print_css_styles($cat_multicol);
		// wrapper div
		$out .= '
				<div class="linkview" id="lv-sc-id-'.$this->sc_ids.'"'.$cat_wrapper_styles.'>';
		// go through each category
		foreach($categories as $cat) {
			// cat multi-column handling
			$out .= $this->html_multicol_before($cat_multicol['type'], $cat_col);
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
					<div'.$cat_classes.'>';
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
			// cat multi-column handling
			$out .= $this->html_multicol_after($cat_multicol['type'], $cat_col, $cat_multicol['opt']['num_columns']);
		}
		// close last column div if required
		if(0 != $cat_col) {
			$out .= '
					</div>';
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
		$out = '';
		// prepare for linklist multi columns
		$link_multicol = $this->get_multicol_settings($a['link_columns']);
		$link_classes = $this->get_multicol_classes($link_multicol, 'lv-list-item'.$a['class_suffix']);
		$link_wrapper_styles = $this->get_multicol_wrapper_styles($link_multicol, ('none'==$a['list_symbol'] || 'circle'==$a['list_symbol'] || 'square'==$a['list_symbol'] || 'disc'==$a['list_symbol']) ? 'list-style-type:'.$a['list_symbol'].';' : '');
		$link_col = 0;
		// print styles and scripts for multi-column support
		$out .= $this->print_mansonry_script($link_multicol, '.linkview #'.$list_id, '.lv-list-item-column');
		$out .= $this->print_css_styles($link_multicol);
 		// wrapper div and list tag
		$out .= '
					<div id="'.$list_id.'"';
		if('slider' === $a['view_type']) {
			$out .= ' class="lv-slider"';
		}
		$out .= '>
					<ul class="lv-link-list'.$a['class_suffix'].'"'.$link_wrapper_styles.'>';
		// go through each link
		foreach($links as $link) {
			// link multi-column handling
			$out .= $this->html_multicol_before($link_multicol['type'], $link_col);
			// actual link
			$out .= '
						<li'.$link_classes.'><div class="lv-link'.$a['class_suffix'].'"';
			if('slider' !== $a['view_type'] && ('top' === $a['vertical_align'] || 'middle' === $a['vertical_align'] || 'bottom' === $a['vertical_align'])) {
				$out .= ' style="display:inline-block; vertical-align:'.$a['vertical_align'].';"';
			}
			$out .= '>';
			$out .= $this->html_link($link, $a, $slider_size);
			$out .= '</div></li>';
			// link multi-column-handling
			$out .= $this->html_multicol_after($link_multicol['type'], $link_col, $link_multicol['opt']['num_columns']);
		}
		// close last column div if required
		if(0 != $link_col) {
			$out .= '
					</div>';
		}
		// close list and wrapper div
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
		// check if a hyperlink shall be added
		$is_link = ('_l' === substr($item, -2));
		if($is_link) {
			$item = substr($item, 0, -2);
		}
		// handle link_item_img="nothing"
		if('image' == $item && '' == $l->link_image && 'show_nothing' == $a['link_item_img']) {
			return '';
		}
		// prepare output
		$out = '<div class="lv-item-'.$item.$a['class_suffix'].'">';
		if('' !== $caption) {
			$out .= '<span class="lv-item-caption'.$a['class_suffix'].'">'.$caption.'</span>';
		}
		// if a link for this item should be created
		if($is_link) {
			// prepare link
			// check target
			if('blank' === $a['link_target'] || 'top' === $a['link_target'] || 'self' === $a['link_target']) {
				$target = '_'.$a['link_target'];
			}
			else {
				$target = $l->link_target;
				// set target to _self if an empty string or _none was returned
				if('' === $target || '_none' === $target) {
					$target = '_self';
				}
			}
			// check description
			$description = '';
			if($l->link_description != "") {
				$description = ' ('.$l->link_description.')';
			}
			// check rel attribute
			$rel = '';
			if('' != $a['link_rel']) {
				// check value according to allowed values for HTML5 (see http://www.w3schools.com/tags/att_a_rel.asp)
				if(in_array($a['link_rel'], $this->atts['link_rel']['val'])) {
					$rel = ' rel="'.$a['link_rel'].'"';
				}
			}
			$out .= '<a class="lv-anchor'.$a['class_suffix'].'" href="'.$l->link_url.'" target="'.$target.'" title="'.$l->link_name.$description.'"'.$rel.'>';
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
				$out .= $this->html_img_tag($l, $a, $slider_size);
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

	private function html_img_tag($l, $a, $slider_size) {
		// handle links without an image
		if('' == $l->link_image) {
			switch($a['link_item_img']) {
				case 'show_link_name':
					return $l->link_name;
				case 'show_link_description':
					return $l->link_description;
				// 'show_nothing': is already handled in html_link_item
				// 'show_img_tag': proceed as normal with the image tag
			}
		}
		// handle image size
		$slider_width = $slider_size[0];
		$slider_height = $slider_size[1];
		if($slider_width <= 0 || $slider_height <= 0) {
			$size_text = '';
		}
		else {
			$slider_ratio = $slider_width / $slider_height;
			list($img_width, $img_height) = getimagesize($l->link_image);
			$img_ratio = $img_width / $img_height;
			if($slider_ratio > $img_ratio) {
				$scale = $slider_height / $img_height;
			}
			else {
				$scale = $slider_width / $img_width;
			}
			$size_text = ' width='.round($img_width*$scale).' height='.round($img_height*$scale);
		}
		// return img tag
		return '<img src="'.$l->link_image.'"'.$size_text.' alt="'.$l->link_name.'" />';
	}

	private function html_multicol_before($type, &$column) {
		if('static' == $type) {
			$column++;
			if(1 == $column) {   // first column
				return '
				<div class="lv-row">';
			}
		}
		return '';
	}

	private function html_multicol_after($type, &$column, $num_columns) {
		if('static' == $type && $column == $num_columns) {   // last column
			$column = 0;
			return '
				</div>';
		}
		return '';
	}

	private function get_multicol_settings($otext) {
		// Check if multicolumn is enabled
		if(1 == $otext) {
			$ret['type'] = false;
			$ret['opt']['num_columns'] = 1;
			return $ret;
		}
		// Handle special case of giving a number only (short form of static type)
		if(ctype_digit(strval($otext))) {
			$ret['type'] = 'static';
			$ret['opt']['num_columns'] = (int)$otext;
			return $ret;
		}
		// Exctract type and options
		$ret['opt'] = array();
		$oarray = explode("(", $otext);
		$ret['type'] = $oarray[0];
		if('static' != $ret['type'] && 'css' != $ret['type'] && 'masonry' != $ret['type']) {
			$ret['type'] = 'static';
		}
		if(isset($oarray[1])) {
			$option_array = explode("|", substr($oarray[1],0,-1));
			foreach($option_array as $option_text) {
				$o = explode("=", $option_text);
				$ret['opt'][$o[0]] = $o[1];
			}
		}
		// validate required options and set them if not available
		switch ($ret['type']) {
			case 'static':
				if(!isset($ret['opt']['num_columns']) || !ctype_digit(strval($ret['opt']['num_columns'])) || 0 >= (int)$ret['opt']['num_columns']) {
					$ret['opt']['num_columns'] = 3;
					// disable multicolumn if num_columns = 1
					if(1 == (int)$ret['opt']['num_columns']) {
						$ret['type'] = false;
					}
				}
				break;
			case 'css':
				// no requirements
				break;
			case 'masonry':
				// no requirements
				break;
		}
		if(!isset($ret['opt']['num_columns'])) {
			$ret['opt']['num_columns'] = 0;
		}
		return $ret;
	}

	private function get_multicol_classes($multicol, $additional_classes='') {
		$classes = $additional_classes;
		if($multicol['type']) {
			$classes .= ' lv-multi-column lv-'.$multicol['type'].'-column';
		}
		if('' == $classes) {
			return '';
		}
		else {
			return ' class="'.$classes.'"';
		}
	}

	private function get_multicol_wrapper_styles($multicol, $additional_styles='') {
		$styles = $additional_styles;
		// prepare multi-column css options
		if('css' == $multicol['type']) {
			foreach($multicol['opt'] as $oname => $ovalue) {
				// do not add internal options
				if('num_columns' == $oname) { continue; }
				// add attribute
				$styles .= $oname.':'.$ovalue.';';
				// add prefixed browser specific attributes
				if('column' == substr($oname, 0, 6)) {
					$styles .='-moz-'.$oname.':'.$ovalue.';-webkit-'.$oname.':'.$ovalue.';';
				}
			}
		}
		if('' == $styles) {
			return '';
		}
		else {
			return ' style="'.$styles.'"';
		}
	}

	private function get_new_list_id() {
		$this->num_ids++;
		return 'lv-id-'.$this->num_ids;
	}

	private function print_css_styles($multicolumn) {
		// print custom css (only once, whe the shortcode is included the first time)
		$css = '';
		if($multicolumn && !$this->css_multicol_printed) {
			// some default styles for multi-column layouts
			$css .= '
					.lv-multi-column { float:left; }
					.lv-multi-column li { page-break-inside: avoid; }
					.lv-row { overflow:auto; }
					.lv-css-column { break-inside:avoid-column; column-break-inside:avoid; -webkit-column-break-inside:avoid; overflow:hidden; }';
			$this->css_multicol_printed = true;
		}
		if(!$this->css_printed) {
			$css .= '
					.linkview { overflow:auto; }
					.linkview > div { overflow:hidden; }
					.lv-slider ul, .lv-slider li { margin:0; padding:0; list-style-type:none; list-style-image:none; }
					.lv-slider li { overflow:hidden; text-align:center; }
					.lv-slider img { max-width:100%; }
					'.$this->options->get('lv_css');
			$this->css_printed = true;
		}

		if('' == $css) {
			return '';
		}
		else {
			return '<style type="text/css">'.$css.'
				</style>';
		}
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

	public function print_mansonry_script($multicol, $parent_selector, $item_selector) {
		// check for masonry type, else do nothing
		if('masonry' != $multicol['type']) {
			return '';
		}
		// prepare options
		$option_text = 'itemSelector:"'.$item_selector.'"';
		foreach($multicol['opt'] as $oname => $ovalue) {
			$option_text .= ','.$oname.':'.$ovalue;
		}
		return '
				<script type="text/javascript" src="'.LV_URL.'includes/js/masonry.pkgd.min.js"></script>
				<script type="text/javascript">
					jQuery(document).ready( function() {
						jQuery("'.$parent_selector.'").masonry({'.$option_text.'});
					});
				</script>';
	}
} // end of class SC_Linkview
?>
