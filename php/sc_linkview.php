<?php

// This class handles the shortcode [linkview]
class sc_linkview {

	// All available attributes
	public static $attr = array(

		'view_type'		=> array(	'val'		=> 'list<br />slider',
									'std_val'	=> 'list',
									'desc'		=> 'This attribute specifies how the links are displayed. The standard is to show the links in a list.<br />
													The second option is to show the links in a slider. This normally only make sense if you show the images, but it is also possible to show the link name with this option.'),

		'cat_name' 		=> array(	'val'		=> 'Name',
									'std_val'	=> '',
									'desc'		=> 'This attribute specifies what category should be shown. If you leave the attribute empty all categories are shown.<br />
													If the cat_name has spaces, simply wrap the name in quotes.<br />
													Example: <code>[linkview cat_name="Social Media"]</code>' ),

		'show_img'		=> array(	'val'		=> '0 ... false<br />1 ... true',
									'std_val'	=> '0',
									'desc'		=> 'This attribute specifies if the image is displayed instead of the name. This attribute is only considered for links where an image was set.' ),

		'show_cat_name'	=> array(	'val'		=> '0 ... false<br />1 ... true',
									'std_val'	=> '1',
									'desc'		=> 'This attribute specifies if the category name is shown as a headline.'),

		'target'		=> array(	'val'		=> 'blank<br />top<br />none',
									'std_val'	=> '',
									'desc'		=> 'Enter "blank", "top" or "none" to overwrite the standard value which was set for the link.<br />
													Leave this field empty if you don´t want to overwrite the standard.'),

		'slider_width'	=> array(	'val'		=> 'number',
									'std_val'	=> '0',
									'desc'		=> 'This attribute sets the fixed width of the slider. If the attribute is set to 0 the width will be calculated automatically due to the given image sizes.<br />
													This attribute is only considered if the view type "slider" is selected.'),

		'slider_height'	=> array(	'val'		=> 'number',
									'std_val'	=> '0',
									'desc'		=> 'This attribute sets the fixed height of the slider. If the attribute is set to 0 the height will be calculated automatically due to the given image sizes.<br />
													This attribute is only considered if the view type "slider" is selected.')
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
		if( empty( $a['cat_name'] ) ) {
			return get_terms('link_category', 'orderby=count&hide_empty=0');
		}
		else {
			return array( get_term_by( 'name', $a['cat_name'], 'link_category', 'orderby=count&hide_empty=0' ) );
		}
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
		$out .= '
			<ul>';
		foreach( $links as $link ) {
			$out .= '
				<li>'.sc_linkview::html_link( $link, $a ).'</li>';
		}
		$out .= '
			</ul>';
		return $out;
	}

	public static function html_link_slider( $links, $a ) {
		list( $slider_width, $slider_height ) = sc_linkview::slider_size( $a, $links );
		// javascript
		$out = '
			<script type="text/javascript" src="'.LV_URL.'js/jquery.js"></script>
			<script type="text/javascript" src="'.LV_URL.'js/easySlider.js"></script>
			<script type="text/javascript">
				$(document).ready(function(){	
					$("#slider").easySlider({
						auto: true,
						speed: 1000,
						pause: 6000,
						continuous: true,
						controlsShow: false
					});
				});	
			</script>';
		// styles
		$out .= '
			<style>
				#slider ul, #slider li {
					margin:0;
					padding:0;
					list-style:none;
				}
				#slider li { 
					width: '.$slider_width.'px;
					height: '.$slider_height.'px;
					overflow: hidden; 
					text-align: center;
					vertical-align: middle;
				}
				#slider img {
					max-width: 100%;
				}
			</style>';
		// html
		$out .= '
			<div id="slider">
				<ul>';
		// links
		foreach( $links as $link ) {
			$out .= '
					<li>'.sc_linkview::html_link( $link, $a, $slider_width, $slider_height ).'</li>';
		}
		$out .= '	
				</ul>
			</div>';
		return $out;
	}

	public static function html_link( $l, $a, $slider_width=0, $slider_height=0 ) {
		$out = '<a href="'.$l->link_url;
		
		switch( $a['target'] ) {
			case 'blank':
				$target = '_blank';
				break;
			case 'top':
				$target = '_top';
				break;
			case 'none':
				$target = '_none';
				break;
			default:
				$target = $l->link_target;
		}
		$out .= '" target="'.$target.'">';

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
}
?>
