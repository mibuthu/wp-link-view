<?php

// This class handles the shortcode [linkview]
class sc_linkview {

	// All available attributes
	public static $attr = array(

		'view_type'		=> array(	'val'		=> 'list<br />slider',
									'std_val'	=> 'list',
									'desc'		=> 'This attribute specifies how the links are displayed. The standard is to show the links in a list.<br />
													The second option is to show the links in a slider. This normally only make sense if you show the images, but it is also possible to show the link name with this option.' ),

		'cat_name' 		=> array(	'val'		=> 'Cat 1,Cat 2,...',
									'std_val'	=> '',
									'desc'		=> 'This attribute specifies what category should be shown. If you leave the attribute empty all categories are shown.<br />
													If the cat_name has spaces, simply wrap the name in quotes.<br />
													Example: <code>[linkview cat_name="Social Media"]</code><br />
													If you want to define multiple categories you can give them in a list splitted by the delimiter ","<br />
													Example: <code>[linkview cat_name="Blogroll,Social Media"]</code>' ),

		'show_img'		=> array(	'val'		=> '0 ... false<br />1 ... true',
									'std_val'	=> '0',
									'desc'		=> 'This attribute specifies if the image is displayed instead of the name. This attribute is only considered for links where an image was set.' ),

		'show_cat_name'	=> array(	'val'		=> '0 ... false<br />1 ... true',
									'std_val'	=> '1',
									'desc'		=> 'This attribute specifies if the category name is shown as a headline.' ),
		
		'vertical_align'=> array(	'val'		=> 'std<br />top<br />bottom<br />middle',
									'std_val'	=> 'std',
									'desc'		=> 'This attribute specifies the vertical alignment of the links. Changing this attribute normally only make sense if the link-images are displayed.<br />
													If you change this value you can for example modify the vertical alignment of the list symbol relativ to the image or the vertical alignment of images with different size in a slider.' ),

		'target'		=> array(	'val'		=> 'std<br />blank<br />top<br />none',
									'std_val'	=> 'std',
									'desc'		=> 'Set one of the given values to overwrite the standard value which was set for the link.<br />
													Set the attribute to "std" if you don´t want to overwrite the standard.' ),
													
		'list_symbol'	=> array(	'val'		=> 'std<br />none<br />circle<br />square<br />disc',
									'std_val'	=> 'std',
									'desc'		=> 'This attribute sets the style type of the list symbol.<br />
													The standard value is "std", this means the standard type which is set in your theme will be used. Set one of the other values to overwrite this standard.<br />
													A good example for the usage is to set the value to "none" for an image link list. The list symbols will be hidden which often looks better when images are used.' ),

		'slider_width'	=> array(	'val'		=> 'Number',
									'std_val'	=> '0',
									'desc'		=> 'This attribute sets the fixed width of the slider. If the attribute is set to 0 the width will be calculated automatically due to the given image sizes.<br />
													This attribute is only considered if the view type "slider" is selected.' ),

		'slider_height'	=> array(	'val'		=> 'Number',
									'std_val'	=> '0',
									'desc'		=> 'This attribute sets the fixed height of the slider. If the attribute is set to 0 the height will be calculated automatically due to the given image sizes.<br />
													This attribute is only considered if the view type "slider" is selected.' ),

		'slider_pause'	=> array(	'val'		=> 'Number',
									'std_val'	=> '6000',
									'desc'		=> 'This attribute sets the duration between the the slides in milliseconds. This is the time where you can see the link standing still before the next slide starts.<br />
													This attribute is only considered if the view type "slider" is selected.' ),

		'slider_speed'	=> array(	'val'		=> 'Number',
									'std_val'	=> '1000',
									'desc'		=> 'This attribute sets the animation speed of the slider in milliseconds. This is the time used to slide from one link to the next one.<br />
													This attribute is only considered if the view type "slider" is selected.' )
	);

	// main function to show the rendered HTML output
	public static function show_html( $atts ) {
		
		// check attributes
		$std_values = array();
		foreach( sc_linkview::$attr as $aname => $attribute ) {
			$std_values[$aname] = $attribute['std_val'];
		}
		$a = shortcode_atts( $std_values, $atts );

		// set categories
		$categories = sc_linkview::categories( $a );
		
		foreach( $categories as $cat ) {
			// get links
			$args = array(
				'orderby'        => 'name',
				'limit'          => -1,
				'category_name'  => $cat->name);
			$links = get_bookmarks( $args );

			// generate output
			if( !empty( $links ) ) {
				$out .= sc_linkview::html_category( $cat, $a );
				if( $a['view_type'] == 'slider' ) {
					$out .= sc_linkview::html_link_slider( $links, $a );
				}
				else {
					$out .= sc_linkview::html_link_list( $links, $a );
				}
			}
		}
		return $out;
	}

	public static function categories( $a ) {
		$catarray = array();
		if( empty( $a['cat_name'] ) ) {
			$catarray = get_terms( 'link_category', 'orderby=name' );
		}
		else {
			$catnames = array_map( 'trim', explode( ",", $a['cat_name'] ));
			foreach( $catnames as $catname ) {
				if( get_term_by( 'name', $catname, 'link_category') != false )
				{
					array_push( $catarray, get_term_by( 'name', $catname, 'link_category' ) );
				}
			}
		}
		return $catarray;
	}

	public static function slider_size( $a, $links ) {
		if(	$a['slider_width'] > 0 && $a['slider_height'] > 0 ) {
			$width = $a['slider_width'];
			$height = $a['slider_height'];
		}
		else {
			$width = 0;
			$height = 0;			
			foreach( $links as $link ) {
				if( $a['show_img'] > 0 && $link->link_image != null ) {
					list( $w, $h ) = getimagesize( $link->link_image );
					$width = max( $width, $w );
					$height = max( $height, $h );
				}
			}
			$ratio = 1;
			if( $a['slider_width'] > 0 ) {
				$ratio = $a['slider_width'] / $width;
			}
			else if( $a['slider_height'] > 0 ) {
				$ratio = $a['slider_height'] / $height;
			}
			$width = round( $width * $ratio );
			$height = round( $height * $ratio );
			// If no image was in all links, set manual size
			if( !$width )
				$width = 300;
			if( !$height )
				$height = 30; 
		}
		return array( $width, $height );
	}

	public static function html_category( $cat, $a ) {
		$out = '';
		if( $a['show_cat_name'] > 0 ) {
			$out .= '
					<h2>'.$cat->name.'</h2>';
		}
		return $out;
	}

	public static function html_link_list( $links, $a ) {
		if( $a['list_symbol'] == 'none' || $a['list_symbol'] == 'circle' || $a['list_symbol'] == 'square' || $a['list_symbol'] == 'disc' ) {
			$out = '
				<ul style="list-style-type:'.$a['list_symbol'].';">';
		}
		else {
			$out = '
				<ul>';
		}
		foreach( $links as $link ) {
			$out .= '
					<li><span';
			if( $a['vertical_align'] == 'top' || $a['vertical_align'] == 'middle' || $a['vertical_align'] == 'bottom' ) {
				$out .= ' style="display:inline-block; vertical-align:'.$a['vertical_align'].';"';
			}
			$out .= '>';
			$out .= sc_linkview::html_link( $link, $a );
			$out .= '</span></li>';
		}
		$out .= '
				</ul>';
		return $out;
	}

	public static function html_link_slider( $links, $a ) {
		$slider_id = sc_linkview::create_random_slider_id();
		list( $slider_width, $slider_height ) = sc_linkview::slider_size( $a, $links );
		// javascript
		$out = '
			<script type="text/javascript" src="wp-includes/js/jquery/jquery.js"></script>
			<script type="text/javascript" src="'.LV_URL.'js/easySlider.js"></script>
			<script type="text/javascript">
				jQuery(document).ready(function(){
					jQuery("#'.$slider_id.'").easySlider({
						auto: true,
						pause: '.$a['slider_pause'].',
						speed: '.$a['slider_speed'].',
						continuous: true,
						controlsShow: false
					});
				});	
			</script>';
		// styles
		$out .= '
			<style>
				#'.$slider_id.' ul, #'.$slider_id.' li {
					margin:0;
					padding:0;
					list-style:none;
				}
				#'.$slider_id.' li { 
					width: '.$slider_width.'px;
					height: '.$slider_height.'px;
					overflow: hidden;
					text-align: center;
				}
				#'.$slider_id.' img {
					max-width: 100%;
				}';
		if( $a['vertical_align'] == 'top' || $a['vertical_align'] == 'middle' || $a['vertical_align'] == 'bottom' ) {
			$out .= '
				#lvspan {
					display: table-cell;
					text-align: center;
					vertical-align: '.$a['vertical_align'].';
				}
				#lvspan * {
					vertical-align: '.$a['vertical_align'].';
				}
				#lvspan {
					width: '.$slider_width.'px;
					height: '.$slider_height.'px;
				}';
		}
		$out .= '
			</style>';
		// html
		$out .= '
			<div id="'.$slider_id.'">
				<ul>';
		// links
		foreach( $links as $link ) {
			$out .= '
					<li><span';
			if( $a['vertical_align'] == 'top' || $a['vertical_align'] == 'middle' || $a['vertical_align'] == 'bottom' ) {
				$out .= ' id="lvspan"';
			}
			$out .= '>';
			$out .= sc_linkview::html_link( $link, $a, $slider_width, $slider_height );
			$out .= '</span></li>';
		}
		$out .= '	
				</ul>
			</div>';
		return $out;
	}

	public static function html_link( $l, $a, $slider_width=0, $slider_height=0 ) {
		$out .= '<a href="'.$l->link_url;
		
		if( $a['target'] == 'blank' || $a['target'] == 'top' || $a['target'] == 'none' ) {
			$target = '_'.$a['target'];
		}
		else {
			$target = $l->link_target;
			// set target to _none if an empty string was returned
			if( $target == '' )
				$target = '_none';
		}
		$out .= '" target="'.$target.'" title="'.$l->link_name;
		if( $l->link_description != "" ) {
			$out .= ' ('.$l->link_description.')';
		}
		$out .= '">';

		if( $a['show_img'] > 0 && $l->link_image != null ) {
			$out .= '<img src="'.$l->link_image.'"'.sc_linkview::html_img_size( $l->link_image, $slider_width, $slider_height ).' alt="'.$l->link_name.'" />';
		}
		else {
			$out .= $l->link_name;
		}
		$out .= '</a>';
		return $out;
	}

	public static function html_img_size( $image, $slider_width=0, $slider_height=0 ) {
		if( $slider_width <= 0 || $slider_height <= 0 ) {
			return '';
		}
		else {
			$slider_ratio = $slider_width / $slider_height;
			list( $img_width, $img_height ) = getimagesize( $image );
			$img_ratio = $img_width / $img_height;
			if( $slider_ratio > $img_ratio ) {
				$scale = $slider_height / $img_height;
			}
			else {
				$scale = $slider_width / $img_width;
			}
			return ' width="'.round($img_width*$scale).'px" height="'.round($img_height*$scale).'px"';
		}
	}
	
	private static function create_random_slider_id() {
		$slider_id = mt_rand( 10000, 99999 );
		return 'slider'.$slider_id;
	}
}
?>
