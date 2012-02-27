<?php

// This class handles the shortcode [linkview]
class sc_linkview {

	public static $attr = array(
		'cat_name' 	=> array(	'val'		=> 'Name',
								'std_val'	=> '',
								'desc'		=> 'This attribute specifies what categories should be shown by name. If you leave the attribute empty all categories are shown.<br />
												For example <code>[linkview cat_name=Sponsors]</code>. If the cat_name has spaces, simply wrap the name in quotes.<br />
												Example: <code>[linkview cat_name="Social Media"]</code>' ),
		'show_img'	=> array(	'val'		=> '0 ... false<br />1 ... true',
								'std_val'	=> '0',
								'desc'		=> 'This attribute specifies if the image is displayed instead of the name. This attribute is only considered for links where an image was set.' )
		);

	// main function to show the rendered HTML output
	public static function show_html( $atts ) {
		
		// check attributes
		$std_values = array();
		foreach( sc_linkview::$attr as $aname => $attribute ) {
			$std_values[$aname] = $attribute['std_val'];
		}
		$a = shortcode_atts( $std_values, $atts );

		// generate output
		$out = "";
		//foreach( sc_linkview::$attr as $aname => $attribute ) {
		//	$out .= $aname.' {'.$a[$aname].'}<br />';
		//}
		ob_start();
		wp_list_bookmarks( 'category_name='.$a['cat_name'].'&show_images='.$a['show_img'] );
		$out .= ob_get_contents();
		ob_end_clean();
/*		$links = get_bookmarks('');
		foreach( $links as $link ) {
			$out .= $link->link_name.'<br />';
			$i++;
		}
*/
		return "$out";
	}
}
?>
